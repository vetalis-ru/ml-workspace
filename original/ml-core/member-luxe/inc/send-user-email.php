<?php
function wpm_send_user_email( $user_id, $key, $email, $params, $files = array() ): ?bool
{
    if ( is_array( $key ) ) {
        $options = $key;
    } else {
        $options = wpm_get_option( "letters.$key" );
    }

    if ( wpm_array_get( $options, "enabled", 'on' ) != 'on' ) {
        return true;
    }

    $raw_title = wpm_array_get( $options, "title" );
    $raw_content = stripslashes( wpm_array_get( $options, "content" ) );
    $title = apply_filters( 'wpm_user_mail_shortcode_replacement', $raw_title, $user_id, $params );
    $message = apply_filters( 'wpm_user_mail_shortcode_replacement', $raw_content, $user_id, $params );
    $message = wpautop( mbl_auto_link_text( $message, 'urls' ) );
    $text = wpm_get_partial( __DIR__ . '/mailer/templates/from_db.php', compact( 'message' ) );

    return MBLMailer::create()
        ->addRecipient( $email )
        ->setSubject( $title )
        ->setMessageRaw( $text )
        ->setAttachments( $files )
        ->send();
}

add_filter( 'wpm_user_mail_shortcode_replacement', 'wpm_user_mail_shortcode_replacement', 10, 3 );
function wpm_user_mail_shortcode_replacement( $message, $user_id, $params )
{
    $user = get_user_by( 'id', $user_id );
    if ( !in_array( 'customer', $user->roles ) ) {
        return $message;
    }

    $user_auth_link = wpm_user_auth_link( $user_id );
    $all_params = array_merge( $params, [
        'auth_link' => '<a href="' . $user_auth_link . '">' . urldecode( $user_auth_link ) . '</a>',
    ] );

    if ( wpm_get_option( 'material_url_is_auth', 'off' ) === 'on' && isset( $params['material_url'] ) ) {
        $material_url_with_auth = $user_auth_link . '?' . http_build_query( [ 'redirect_to' => $params['material_url'] ] );
        $link = '<a href="' . $material_url_with_auth . '">' . urldecode( $material_url_with_auth ) . '</a>';
        $message = str_replace( '[material_url]', $link, $message );
    }

    if ( wpm_get_option( 'page_link_is_auth', 'off' ) === 'on' && isset( $params['page_link'] ) ) {
        $page_link_with_auth = $user_auth_link . '?' . http_build_query( [ 'redirect_to' => $params['page_link'] ] );
        $link = '<a href="' . $page_link_with_auth . '>' . urldecode( $page_link_with_auth ) . '</a>';
        $message = str_replace( '[page_link]', $link, $message );
    }

    if ( wpm_get_option( 'user_key_link_is_auth', 'off' ) === 'on' && isset( $params['user_key_link'] ) ) {
        $user_key_link_with_auth = $user_auth_link . '?' . http_build_query( [ 'redirect_to' => $params['user_key_link'] ] );
        $link = '<a href="' . $user_key_link_with_auth . '">' . urldecode( $user_key_link_with_auth ) . '</a>';
        $message = str_replace( '[user_key_link]', $link, $message );
    }

    if ( wpm_get_option( 'start_page_is_auth', 'off' ) === 'on' && strpos( $message, '[start_page]' ) !== false ) {
        $start_page_url = wpm_get_start_url();

        $auth_link = $user_auth_link . '?' . http_build_query( [ 'redirect_to' => $start_page_url ] );
        $link = '<a href="' . $auth_link . '">' . urldecode( $auth_link ) . '</a>';
        $message = str_replace( '[start_page]', $link, $message );
    }

    preg_match_all( '/\[auth_link redirect="([^"]+)"]/', $message, $matches, PREG_SET_ORDER );
    foreach ( $matches as $match ) {
        $redirect_to = $match[1] ?? '';
        $auth_link = $user_auth_link . '?' . http_build_query( [ 'redirect_to' => $redirect_to ] );
        $auth_link_view = $user_auth_link . '?redirect_to=' . $redirect_to;
        $link = "<a href='$auth_link'>$auth_link_view</a>";
        $message = str_replace( $match[0], $link, $message );
    }

    foreach ( $all_params as $param => $value ) {
        $message = str_replace( '[' . $param . ']', $value, $message );
    }

    return $message;
}
