<?php
const MBL_AUTH_PAGE = 'auth';
const MBL_AUTH_SALT = 4213;
function wpm_generate_auth_secret( int $user_id ): string
{
    $chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
    $random_string = '';
    for ( $i = 0; $i < 8; $i++ ) {
        $random_string .= substr( $chars, wp_rand( 0, strlen( $chars ) - 1 ), 1 );
    }

    return $user_id . time() . $random_string;
}

function wpm_user_auth_link( int $user_id ): string
{
    $cache_key = 'wpm_auth_link_' . $user_id;
    $auth_link = wp_cache_get( $cache_key, 'wpm_users_auth_links' );
    if ( $auth_link === false ) {
        global $wpdb;
        $table = $wpdb->prefix . "memberlux_auto_auth";
        $secret = $wpdb->get_var( $wpdb->prepare( "SELECT secret FROM $table WHERE user_id = %d", $user_id ) );

        if ( !$secret ) {
            $secret = wpm_generate_auth_secret( $user_id );
            $wpdb->insert( $table, [ 'user_id' => $user_id, 'secret' => $secret ] );
        }

        $auth_link = wpm_user_auth_link_by_secret( $user_id, $secret );
    }

    return $auth_link;
}

function wpm_user_auth_link_by_secret( $user_id, $secret ): string
{
    $cache_key = 'wpm_auth_link_' . $user_id;
    $crypt = '-' . base_convert( $user_id + MBL_AUTH_SALT, 10, 35 );
    $auth_link = get_bloginfo( 'url' ) . '/auth/' . hash( 'sha256', $secret ) . $crypt;
    wp_cache_set( $cache_key, $auth_link, 'wpm_users_auth_links' );

    return $auth_link;
}

function wpm_users_auth_links_by_emails( array $emails )
{
    global $wpdb;
    $table = $wpdb->prefix . "memberlux_auto_auth";
    $user_emails_in = implode( ',', array_map( fn( $e ) => "'$e'", $emails ) );
    $data = $wpdb->get_results(
        "SELECT u.ID, u.user_email, a.secret
              FROM $wpdb->users AS u
              LEFT JOIN $table AS a ON u.ID = a.user_id
              WHERE u.user_email IN($user_emails_in)",
        ARRAY_A
    );

    return array_reduce( $data, function ( $carry, $r ) {
        $user_id = (int)$r['ID'];
        $auth_link = $r['secret'] ? wpm_user_auth_link_by_secret( $user_id, $r['secret'] ) : wpm_user_auth_link( $user_id );
        $carry[$r['user_email']] = [ 'user_id' => $r['ID'], 'auth_link' => $auth_link ];

        return $carry;
    }, [] );
}

function wpm_verify_auth_secret( int $user_id, string $secret_hash ): bool
{
    global $wpdb;
    $table = $wpdb->prefix . "memberlux_auto_auth";
    $secret = $wpdb->get_var( $wpdb->prepare( "SELECT secret FROM $table WHERE user_id = %d", $user_id ) );

    return !empty( $secret ) && hash( 'sha256', $secret ) === $secret_hash;
}

add_action( 'init', 'wpm_auto_login_rewrite' );
function wpm_auto_login_rewrite()
{
    global $wp_rewrite;
    $query = 'index.php?pagename=auth&credentials=$matches[2]';
    $regexp = '^(' . MBL_AUTH_PAGE . ')/([^/]*)/?';
    add_rewrite_rule( $regexp, $query, 'top' );
    add_filter( 'query_vars', 'wpm_auto_login_query_vars' );
    $rewrite = $wp_rewrite->wp_rewrite_rules();

    if(!isset($rewrite[$regexp])) {
        flush_rewrite_rules();
    }
}

function wpm_auto_login_query_vars( $vars )
{
    $vars[] = 'credentials';

    return $vars;
}

add_action( 'set_404', 'wpm_auto_login_page_not404', 1, 10 );
function wpm_auto_login_page_not404( WP_Query $wp_query )
{
    if ( $wp_query->get( 'pagename' ) === MBL_AUTH_PAGE ) {
        $wp_query->is_404 = false;
        status_header( 200 );
    }
}

add_filter( 'template_redirect', 'wpm_auto_login_page_template', 1, 999 );
function wpm_auto_login_page_template()
{
    if ( get_query_var( 'pagename' ) !== MBL_AUTH_PAGE ) {
        return;
    }

    $credentials = get_query_var( 'credentials' );
    $credential_data = explode( '-', $credentials );

    if ( count( $credential_data ) !== 2 ) {
        die( 'Incorrect link' );
    }

    $secret_hash = $credential_data[0];
    $user_id = base_convert( $credential_data[1], 35, 10 ) - MBL_AUTH_SALT;
    $user = get_user_by( 'id', $user_id );

    if ( !$user ) {
        die( 'Incorrect link' );
    }

    $redirect_to = $_GET['redirect_to'] ?? get_permalink( wpm_get_option( 'home_id' ) );

    if (!in_array( 'customer', $user->roles )) {
        wp_redirect( $redirect_to );
        exit();
    }

    if ( !wpm_verify_auth_secret( $user_id, $secret_hash ) ) {
        die( 'Incorrect link' );
    }

    nocache_headers();
    wp_clear_auth_cookie();
    wp_set_auth_cookie( $user_id );
    do_action( 'wp_login', $user->user_login, $user );
    wp_redirect( $redirect_to );
    exit();
}

function wpm_auto_login_shortcodes_tips()
{
    ?>
    <span class="code-string">[auth_link]</span> - <?php _e( 'ссылка для входа', 'mbl_admin' ) ?> <br>
    <span class="code-string">[auth_link redirect="<?= site_url() ?>"]</span> - <?php
    _e( 'ссылка для входа с перенаправлением', 'mbl_admin' ) ?> <br>
    <?php
}

function wpm_user_table_clipboard_column( $columns ) {
    $columns['auth_link'] = 'Вход';
    return $columns;
}
add_filter( 'manage_users_columns', 'wpm_user_table_clipboard_column' );

function wpm_user_table_clipboard_column_content( $value, $column_name, $user_id )
{
    $user = get_user_by( 'id', $user_id );
    if ( 'auth_link' === $column_name && current_user_can( 'administrator' ) && in_array( 'customer', $user->roles ) ) {
        return '<button type="button" class="user-auth-link"
         aria-label="Скопировать"
         data-clipboard-text="' . wpm_user_auth_link( $user_id ) . '"
         data-user-id="' . $user_id . '">
            <svg class="auth-link" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M384 336H192c-8.8 0-16-7.2-16-16V64c0-8.8 7.2-16 16-16l140.1 0L400 115.9V320c0 8.8-7.2 16-16 16zM192 384H384c35.3 0 64-28.7 64-64V115.9c0-12.7-5.1-24.9-14.1-33.9L366.1 14.1c-9-9-21.2-14.1-33.9-14.1H192c-35.3 0-64 28.7-64 64V320c0 35.3 28.7 64 64 64zM64 128c-35.3 0-64 28.7-64 64V448c0 35.3 28.7 64 64 64H256c35.3 0 64-28.7 64-64V416H272v32c0 8.8-7.2 16-16 16H64c-8.8 0-16-7.2-16-16V192c0-8.8 7.2-16 16-16H96V128H64z"/></svg>
            <span style="display: none" class="dashicons dashicons-yes"></span>
         </button>';
    }
    return $value;
}
add_filter( 'manage_users_custom_column', 'wpm_user_table_clipboard_column_content', 10, 3 );

function wpm_user_table_clipboard_styles() {
    global $pagenow;
    if ( 'users.php' === $pagenow && current_user_can( 'administrator' ) ) {
        ?>
        <style>
            .column-auth_link {
                width: 40px;
            }
            .auth-link {
                display: inline-block;
                height: 20px;
                width: 20px;
            }
            .user-auth-link {
                background: none;
                border: 0;
                cursor: pointer;
            }
            .user-auth-link:hover {
                color: #0056b3;
            }
            .user-auth-link:active,
            .user-auth-link:focus {
                color: #0056b3;
                background-color: #e9ecef;
                border-color: #dee2e6;
            }
        </style>
        <?php
    }
}
function wpm_user_table_clipboard_script()
{
    global $pagenow;
    if ( 'users.php' === $pagenow && current_user_can( 'administrator' ) ) {
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                var clipboard = new ClipboardJS('.user-auth-link');
                clipboard.on('success', function (e) {
                    $(e.trigger).css('color', '#008000');
                    $(e.trigger).find('span').show();
                });
            });
        </script>
        <?php
    }
}

add_action( 'admin_footer', 'wpm_user_table_clipboard_script' );

add_action( 'admin_head', 'wpm_user_table_clipboard_styles' );
