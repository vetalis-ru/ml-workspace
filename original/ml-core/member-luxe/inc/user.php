<?php

use http\Cookie;

/**
 * WPM register form
 */
function wpm_register_form()
{
    wpm_render_partial('register-form');
}


/**
 * Ajax create new User
 */

function wpm_ajax_register_user()
{
    $error = false;
    $registered = false;
    $message = '';
    $form = array();


    foreach ($_POST['fields'] as $item) {
        $form[$item['name']] = trim($item['value']);
    }

    $form = apply_filters('wpm_ajax_register_user_form_filter', $form);

    if(!MBLReCaptcha::check('registration', $form)) {
        $message = __('Необходимо пройти проверку reCAPTCHA', 'mbl');
        $error = true;
        wpm_registration_result($message, $registered);
    }

    if (!array_key_exists('name', $form)) {
        $form['name'] = '';
    }

    if (!array_key_exists('surname', $form)) {
        $form['surname'] = '';
    }

    if (!array_key_exists('custom1', $form)) {
        $form['custom1'] = '';
    }
    if (!array_key_exists('custom2', $form)) {
        $form['custom2'] = '';
    }
    if (!array_key_exists('custom3', $form)) {
        $form['custom3'] = '';
    }
    if (!array_key_exists('custom4', $form)) {
        $form['custom4'] = '';
    }
    if (!array_key_exists('custom5', $form)) {
        $form['custom5'] = '';
    }
    if (!array_key_exists('custom6', $form)) {
        $form['custom6'] = '';
    }
    if (!array_key_exists('custom7', $form)) {
        $form['custom7'] = '';
    }
    if (!array_key_exists('custom8', $form)) {
        $form['custom8'] = '';
    }
    if (!array_key_exists('custom9', $form)) {
        $form['custom9'] = '';
    }
    if (!array_key_exists('custom10', $form)) {
        $form['custom10'] = '';
    }
    if (!array_key_exists('custom1textarea', $form)) {
        $form['custom1textarea'] = '';
    }

    if (!array_key_exists('patronymic', $form)) {
        $form['patronymic'] = '';
    }

    if (!array_key_exists('phone', $form)) {
        $form['phone'] = '';
    }


    if (wpm_is_users_overflow()) {
        $message = __('Регистрация временно недоступна', 'mbl');
        $error = true;
        wpm_registration_result($message, $registered);
    }

    $index = wpm_search_key_id(wpm_array_get($form, 'code'));

    // check if user key exist
    if (apply_filters('wpm_ajax_register_empty_index_filter', $index == null)) {
        // if not exist
        $error = true;
        $message .= (__('Неверный ключ', 'mbl') . '<br>');

        wpm_registration_result($message, $registered);

    } elseif(wpm_array_get($index, 'key_info.status') == 'used') {
        $message .= __('Этот код доступа уже используется', 'mbl');
        $error = true;
        wpm_registration_result($message, $registered);
    }

    if (!validate_username($form['login'])) {
        $message .= __('Некорректный логин. Для логина разрешены только буквы латинского алфавита и цифры', 'mbl');
        wpm_registration_result($message, $registered);
    }


    // check if username exist
    if (username_exists($form['login'])) {
        $message .= __('Этот логин уже используется', 'mbl');
        wpm_registration_result($message, $registered);
    }
    // check if email exist
    if (email_exists($form['email'])) {
        $message .= __('Этот email уже используется', 'mbl');
        wpm_registration_result($message, $registered);
    }

    if ($error) {
        wpm_registration_result($message, $registered);
    } else {

        $user_id = wp_insert_user(
            array(
                'user_login' => $form['login'],
                'user_pass'  => $form['pass'],
                'user_email' => $form['email'],
                'first_name' => wpm_array_get($form, 'first_name'),
                'last_name'  => wpm_array_get($form, 'last_name'),
                'role'       => 'customer',
                'user_url'   => 'ajax_user_registration',
            )
        );

        $registered = wpm_register_user(array(
            'user_id' => $user_id,
            'user_data' => $form,
            'index' => $index,
            'source' => 'free_registration_form',
            'send_email' => true
        ));

        if ($registered) {
            $message = __('Спасибо за регистрацию!', 'mbl') . '<br>' . __('Сообщение с подтверждением отправлено на указанный адрес', 'mbl');
	        do_action('wpm_ajax_register_user_registered', $user_id);
            wpm_registration_result($message, $registered);
        }
    }
    //On success
}

function wpm_register_user_hook($user_id)
{
    $form = array(
        'login' => $_POST['user_login'],
        'pass' => $_POST['pass'],
        'email' => $_POST['email'],
        'first_name' => $_POST['first_name'],
        'surname' => $_POST['last_name'],
        'custom1' => $_POST['custom1'],
        'custom2' => $_POST['custom2'],
        'custom3' => $_POST['custom3'],
        'custom4' => $_POST['custom4'],
        'custom5' => $_POST['custom5'],
        'custom6' => $_POST['custom6'],
        'custom7' => $_POST['custom7'],
        'custom8' => $_POST['custom8'],
        'custom9' => $_POST['custom9'],
        'custom10' => $_POST['custom10'],
        'custom1textarea' => $_POST['custom1textarea'],
        'phone' => '',
        'name' => '',
        'patronymic' => '',
    );

    wpm_register_user(array(
        'user_id' => $user_id,
        'user_data' => $form,
        'index' => '',
        'send_email' => true
    ));
}

function wpm_register_user($args)
{
    $registered = false;

    if(wpm_is_users_overflow()) {
        return false;
    }

    if (!is_wp_error($args['user_id'])) {

        if($args['send_email']){
            wpm_send_registration_email('admin', $args['user_data'], $args['user_id']);
            wpm_send_registration_email('user', $args['user_data'], $args['user_id']);
        }

        // update user meta
        if(isset($args['user_data']['surname'])) {
            update_user_meta( $args['user_id'], 'surname', $args['user_data']['surname'] );
        }
        if(isset($args['user_data']['custom1'])){
            update_user_meta($args['user_id'], 'custom1', $args['user_data']['custom1']);
        }
        if(isset($args['user_data']['custom2'])){
            update_user_meta($args['user_id'], 'custom2', $args['user_data']['custom2']);
        }
        if(isset($args['user_data']['custom3'])){
            update_user_meta($args['user_id'], 'custom3', $args['user_data']['custom3']);
        }
        if(isset($args['user_data']['custom4'])){
            update_user_meta($args['user_id'], 'custom4', $args['user_data']['custom4']);
        }
        if(isset($args['user_data']['custom5'])){
            update_user_meta($args['user_id'], 'custom5', $args['user_data']['custom5']);
        }
        if(isset($args['user_data']['custom6'])){
            update_user_meta($args['user_id'], 'custom6', $args['user_data']['custom6']);
        }
        if(isset($args['user_data']['custom7'])){
            update_user_meta($args['user_id'], 'custom7', $args['user_data']['custom7']);
        }
        if(isset($args['user_data']['custom8'])){
            update_user_meta($args['user_id'], 'custom8', $args['user_data']['custom8']);
        }
        if(isset($args['user_data']['custom9'])){
            update_user_meta($args['user_id'], 'custom9', $args['user_data']['custom9']);
        }
        if(isset($args['user_data']['custom10'])){
            update_user_meta($args['user_id'], 'custom10', $args['user_data']['custom10']);
        }
        if(isset($args['user_data']['custom1textarea'])){
            update_user_meta($args['user_id'], 'custom1textarea', $args['user_data']['custom1textarea']);
        }

        if(isset($args['user_data']['phone'])){
            update_user_meta($args['user_id'], 'phone', $args['user_data']['phone']);
        }

        if (!empty($args['index'])) {
            $duration = $args['index']['key_info']['duration'] ?: 0;
            $units = array_key_exists('units', $args['index']['key_info']) ? $args['index']['key_info']['units'] : 'months';
            $date_start = date('d-m-Y', time());
            $date_end = date('d-m-Y', strtotime("+$duration " . $units));

            if(isset($args['user_data']['code'])) {
                $termKeyData = array(
                    'user_id' => $args['user_id'],
                    'status'     => 'used',
                    'date_start' => $date_start,
                    'date_end'   => $date_end,
                    'key_type'   => ($args['index']['is_deleted'] ? 'wpm_keys_basket' : 'wpm_term_keys')
                );
                MBLTermKeysQuery::update($termKeyData, array('key' => $args['user_data']['code']));
                /**
                 * Fires immediately after the key is activated to the user
                 *
                 * @since 2.54
                 * @param int $user_id
                 * @param int $term_id
                 * @param string $code
                 * @param array $key wpm_search_key_id()
                 * @param string $source
                 *    Accepts: purchase, free_item, free_registration_form, bulk_operations_reg, bulk_operations_add,
                 *             auto_registration, activation_page, profile_page_self, profile_page_admin,
                 *             after_auto_training_passed
                 */
                $user_id = $args['user_id'];
                $key = $args['index'];
                $code = $key['key_info']['key'];
                $term_id = $key['term_id'];
                $source = $args['source'] ?? '';
                do_action('wpm_update_user_key_dates', $user_id, $term_id, $code, $key, $source);
            }
        }

        mbl_subscription_user_register($args['user_id']);

        $registered = true;
    }

    return $registered;
}

//add_action('wp_ajax_wpm_ajax_register_user_action', 'wpm_ajax_register_user'); // ajax for logged in users
add_action('wp_ajax_nopriv_wpm_ajax_register_user_action', 'wpm_ajax_register_user'); // ajax for not logged in users


/**/

function wpm_send_registration_email($receiver, $form, $user_id)
{
    $user_name = (isset($form['first_name'])) ? $form['first_name'] : '';
    $user_login = $form['login'];
    $user_email = $form['email'];
    $user_pass = $form['pass'];

    add_filter('wp_mail_content_type', 'wpm_register_set_content_type');
    $main_options = get_option('wpm_main_options');

    if ($receiver == 'user') {
        $start_page = '<a href="' . get_permalink($main_options['home_id']) . '">' . get_permalink($main_options['home_id']) . '</a>';
        $params = compact('user_name', 'user_login', 'user_pass', 'start_page');
        wpm_send_user_email($user_id, 'registration', $user_email, $params);
    } elseif ($receiver == 'admin') {
        MBLMail::registration(get_option('admin_email'), compact('user_login', 'user_email'));
    }
}

/**/


function wpm_registration_result($message, $registered)
{
    echo json_encode(array(
        'message' => $message,
        'registered' => $registered,
        'clear_utm' => apply_filters('wpm_clear_utm', false)
    ));
    die();
}


/**
 *
 */
function wpm_ajax_login_form()
{
    wpm_render_partial('login-form');
}

function wpm_ajax_login_init()
{
    // Enable the user with no privileges to run ajax_login() in AJAX
    add_action('wp_ajax_nopriv_wpm_ajaxlogin', 'wpm_ajax_login');
}

// Execute the action only if the user isn't logged in
add_action('init', 'wpm_ajax_login_init');

function wpm_ajax_login()
{
    // First check the nonce, if it fails the function will break
    check_ajax_referer('wpm-ajax-login-nonce', 'security');

    // Nonce is checked, get the POST data and sign user on
    $info = array();
    $info['user_login'] = $_POST['username'];
    $info['user_password'] = $_POST['password'];
    $info['remember'] = 'forever';
    if (wpm_array_get($_POST, 'remember')) {
        $info['remember'] = 'forever';
    }

    if(!MBLReCaptcha::check('login')) {
        echo json_encode(array('loggedin' => false, 'message' => __('Необходимо пройти проверку reCAPTCHA', 'mbl')));
        die();
    }

    $user_signon = wp_signon($info, false);

    if (is_wp_error($user_signon)) {
        $message = wpm_array_get($user_signon->errors, 'wpm_inactive.0', __('Логин или пароль неправильные. Попробуйте ввести еще раз, возможно включен CapsLock.', 'mbl'));
        echo json_encode(array('loggedin' => false, 'message' => $message));
    } else {
	    echo json_encode(array('loggedin' => true, 'message' => __('Вход выполнен, переадресация...', 'mbl')));
    }
    die();
}

add_filter('auth_cookie_expiration', 'wpm_auth_cookie_expiration', 10, 3);

function wpm_auth_cookie_expiration($duration, $user_id, $remember)
{
    return $remember ? 31556926 : $duration;
}

function wpm_disable_default_avatars( $value ) {
    return wpm_is_admin_wpm_page() ? false : $value ;
}
add_filter( 'option_show_avatars', 'wpm_disable_default_avatars' );

add_action('admin_enqueue_scripts', 'wpm_profile_styles');
function wpm_profile_styles()
{
    $current_screen = get_current_screen();
    if ($current_screen->id === 'user-edit' || $current_screen->id === 'profile') {
        global $profile_user;
        wp_enqueue_style('wpm-profile', plugins_url('/member-luxe/css/profile.css'), [], WP_MEMBERSHIP_VERSION);
        if (wpm_is_admin()) {
            wp_enqueue_script('wpm-profile', plugins_url('/member-luxe/js/admin/profile.js'), ['jquery'], WP_MEMBERSHIP_VERSION);
        } else {
            wp_enqueue_script('wpm-profile', plugins_url('/member-luxe/js/admin/profile-user.js'), ['jquery'], WP_MEMBERSHIP_VERSION);
        }
        wp_localize_script('wpm-profile', 'wpm', ['user_id' => $profile_user->ID]);
    }
}
/**
 *
 */
add_action('show_user_profile', 'wpm_show_extra_profile_fields');
add_action('edit_user_profile', 'wpm_show_extra_profile_fields');

function wpm_show_extra_profile_fields($user)
{
    global $wpdb;
    //if (!in_array('customer', $user->roles)) return;
    wp_enqueue_media(); // Include Wordpress Media Library

    $main_options = get_option('wpm_main_options');

    $terms_table = $wpdb->prefix . "terms";
    $term_taxonomy_table = $wpdb->prefix . "term_taxonomy";
    $autotraining_terms = $wpdb->get_results("SELECT a.*, b.count, b.parent
                                         FROM " . $terms_table . " AS a
                                         JOIN " . $term_taxonomy_table . " AS b ON a.term_id = b.term_id
                                         WHERE b.taxonomy='wpm-category';", OBJECT);

    $autotrainings = array();

    if (count($autotraining_terms)) {
        foreach ($autotraining_terms as $autotraining) {
            if (wpm_is_autotraining($autotraining->term_id)) {
                $autotrainings[$autotraining->term_id] = $autotraining;
            }
        }
    }

    do_action('mbl_extra_profile_fields', $autotrainings, $user);
    $current_user = wp_get_current_user();
    ?>
    <script>
        jQuery(function ($) {
            $('.add-new-key').click(function () {
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: ajaxurl,
                    data: {
                        action: "wpm_add_key_to_user_action",
                        key: $('#user_key').val(),
                        user_id: "<?php echo $user->ID; ?>",
                        source: "<?= $user->ID === $current_user->ID ? 'profile_page_self' : 'profile_page_admin' ?>"
                    },
                    success: function (data) {

                        $('.result').html(data.message);
                        if (!data.error) setTimeout(function () {
                            location.reload();
                        }, 1000);

                    },
                    error: function (errorThrown) {
                        //console.log(errorThrown);
                    }
                });
            });
            $('.add-new-auto-training-access').click(function () {
                var tr = $(this).closest('tr'),
                    term_id = tr.find('[name="auto_training_access"]').val(),
                    level = tr.find('[name="auto_training_access_number"]').val();

                tr.closest('table').css({opacity:'0.5'});

                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: ajaxurl,
                    data: {
                        action: "wpm_add_auto_training_access",
                        term_id: term_id,
                        level: level,
                        user_id: "<?php echo $user->ID; ?>"
                    },
                    success: function (data) {
                        if (!data.error) {
                            tr.find('.access-result').html(data.result);
                            tr.closest('table').css({opacity:'1'});
                        }
                    },
                    error: function (errorThrown) {
                    }
                });
            });

            $(document).on('click', '#wpm_stats_block_exclude', function () {
                $.post(
                    ajaxurl,
                    {
                        action  : 'wpm_stats_exclude',
                        user_id : <?php echo $user->ID; ?>
                    },
                    function (result) {
                        if (result.success) {
                            window.location.reload();
                        }
                    },
                    "json"
                );

                return false;
            });

            // Upload media file ====================================

            var wpm_file_frame;
            var image_id = '';
            $(document).on('click', '.wpm-media-upload-button', function (event) {
                image_id = $(this).attr('data-id');

                event.preventDefault();

                // If the media frame already exists, reopen it.
                if (wpm_file_frame) {
                    wpm_file_frame.open();
                    return;
                }

                // Create the media frame.
                wpm_file_frame = wp.media.frames.downloadable_file = wp.media({
                    title: '<?php _e('Выберите файл', 'mbl_admin'); ?>',
                    button: {
                        text: '<?php _e('Использовать изображение', 'mbl_admin'); ?>'
                    },
                    multiple: false
                });

                // When an image is selected, run a callback.
                wpm_file_frame.on('select', function () {
                    var attachment = wpm_file_frame.state().get('selection').first().toJSON();

                    $('#' + image_id).val(attachment.id);
                    $('input#wpm_' + image_id).val(attachment.sizes.thumbnail.url);
                    $('#wpm-' + image_id + '-preview').attr('src', attachment.sizes.thumbnail.url).show();
                    $('#delete-wpm-' + image_id).show();
                    $('.wpm-crop-media-button[data-id="' + image_id +'"]').show();
                });
                // Finally, open the modal.
                wpm_file_frame.open();
            });
            $(document).on('click', '.wpm-delete-media-button', function () {
                image_id = $(this).attr('data-id');

                $('#avatar').val('');
                $('input#wpm_' + image_id).val('');
                $('#delete-wpm-' + image_id).hide();
                $('#wpm-' + image_id + '-preview').hide();
                $('.wpm-crop-media-button[data-id="' + image_id +'"]').hide();
            });

            // End upload media file ====================================
        });
    </script>

    <?php $blockedFilter = get_user_meta($user->ID, 'wpm_block_filter', true) ?>

    <?php if ($blockedFilter) : ?>
        <div id="wpm-blocked-reason">
            <?php _e('Заблокирован: фильтр', 'mbl_admin'); ?> "<?php echo $blockedFilter; ?>" (x<?php echo get_user_meta($user->ID, 'wpm_blocks_count', true) ?: 1; ?>)
        </div>
    <?php endif; ?>

    <?php if (wpm_is_admin()) : ?>
    <table class="form-table">
        <tr>
            <th><label><?php _e('Автоблокировка', 'mbl_admin'); ?></label></th>
            <td>
                <div class="mbl-settings-color">
                    <?php if (!wpm_is_excluded_from_block($user->ID)) : ?>
                        <a href="#" id="wpm_stats_block_exclude"><?php _e('Добавить в список исключений автоблокировки', 'mbl_admin'); ?></a>
                    <?php else : ?>
                        <a href="#" id="wpm_stats_block_exclude"><?php _e('Удалить из списка исключений автоблокировки', 'mbl_admin'); ?></a>
                    <?php endif; ?>
                </div>
            </td>
        </tr>
    </table>
    <?php endif; ?>

    <h3><?php _e('Дополнительная информация', 'mbl_admin'); ?></h3>

    <?php wpm_render_partial('profile-avatar', 'admin', compact('user')) ?>

    <table class="form-table">
        <tr>
            <th><label for="surname"><?php _e('Отчество', 'mbl_admin'); ?></label></th>
            <td>
                <input type="text" name="surname" id="surname"
                       value="<?php echo esc_attr(get_the_author_meta('surname', $user->ID)); ?>" class="regular-text"/><br/>
            </td>
        </tr>
	    <?php if(!empty($main_options['registration_form']['custom1_label'])): ?>
            <tr>
                <th><label for="custom1"><?php echo $main_options['registration_form']['custom1_label'] ?></label></th>
                <td>
                    <input type="text" name="custom1" id="custom1"
                           value="<?php echo esc_attr(get_the_author_meta('custom1', $user->ID)); ?>" class="regular-text"/><br/>
                </td>
            </tr>
        <?php endif; ?>
	    <?php if(!empty($main_options['registration_form']['custom2_label'])): ?>
            <tr>
                <th><label for="custom2"><?php echo $main_options['registration_form']['custom2_label'] ?></label></th>
                <td>
                    <input type="text" name="custom2" id="custom2"
                           value="<?php echo esc_attr(get_the_author_meta('custom2', $user->ID)); ?>" class="regular-text"/><br/>
                </td>
            </tr>
	    <?php endif; ?>
	    <?php if(!empty($main_options['registration_form']['custom3_label'])): ?>
            <tr>
                <th><label for="custom3"><?php echo $main_options['registration_form']['custom3_label'] ?></label></th>
                <td>
                    <input type="text" name="custom3" id="custom3"
                           value="<?php echo esc_attr(get_the_author_meta('custom3', $user->ID)); ?>" class="regular-text"/><br/>
                </td>
            </tr>
	    <?php endif; ?>
	    <?php if(!empty($main_options['registration_form']['custom4_label'])): ?>
            <tr>
                <th><label for="custom4"><?php echo $main_options['registration_form']['custom4_label'] ?></label></th>
                <td>
                    <input type="text" name="custom4" id="custom4"
                           value="<?php echo esc_attr(get_the_author_meta('custom4', $user->ID)); ?>" class="regular-text"/><br/>
                </td>
            </tr>
	    <?php endif; ?>
	    <?php if(!empty($main_options['registration_form']['custom5_label'])): ?>
            <tr>
                <th><label for="custom5"><?php echo $main_options['registration_form']['custom5_label'] ?></label></th>
                <td>
                    <input type="text" name="custom5" id="custom5"
                           value="<?php echo esc_attr(get_the_author_meta('custom5', $user->ID)); ?>" class="regular-text"/><br/>
                </td>
            </tr>
        <?php endif; ?>
	    <?php if(!empty($main_options['registration_form']['custom6_label'])): ?>
            <tr>
                <th><label for="custom6"><?php echo $main_options['registration_form']['custom6_label'] ?></label></th>
                <td>
                    <input type="text" name="custom6" id="custom6"
                           value="<?php echo esc_attr(get_the_author_meta('custom6', $user->ID)); ?>" class="regular-text"/><br/>
                </td>
            </tr>
	    <?php endif; ?>
	    <?php if(!empty($main_options['registration_form']['custom7_label'])): ?>
            <tr>
                <th><label for="custom7"><?php echo $main_options['registration_form']['custom7_label'] ?></label></th>
                <td>
                    <input type="text" name="custom7" id="custom7"
                           value="<?php echo esc_attr(get_the_author_meta('custom7', $user->ID)); ?>" class="regular-text"/><br/>
                </td>
            </tr>
	    <?php endif; ?>
	    <?php if(!empty($main_options['registration_form']['custom8_label'])): ?>
            <tr>
                <th><label for="custom8"><?php echo $main_options['registration_form']['custom8_label'] ?></label></th>
                <td>
                    <input type="text" name="custom8" id="custom8"
                           value="<?php echo esc_attr(get_the_author_meta('custom8', $user->ID)); ?>" class="regular-text"/><br/>
                </td>
            </tr>
	    <?php endif; ?>
	    <?php if(!empty($main_options['registration_form']['custom9_label'])): ?>
            <tr>
                <th><label for="custom9"><?php echo $main_options['registration_form']['custom9_label'] ?></label></th>
                <td>
                    <input type="text" name="custom9" id="custom9"
                           value="<?php echo esc_attr(get_the_author_meta('custom9', $user->ID)); ?>" class="regular-text"/><br/>
                </td>
            </tr>
	    <?php endif; ?>
	    <?php if(!empty($main_options['registration_form']['custom10_label'])): ?>
            <tr>
                <th><label for="custom10"><?php echo $main_options['registration_form']['custom10_label'] ?></label></th>
                <td>
                    <input type="text" name="custom10" id="custom10"
                           value="<?php echo esc_attr(get_the_author_meta('custom10', $user->ID)); ?>" class="regular-text"/><br/>
                </td>
            </tr>
	    <?php endif; ?>
	    <?php if(!empty($main_options['registration_form']['custom1textarea_label'])): ?>
            <tr>
                <th><label for="custom1textarea"><?php echo $main_options['registration_form']['custom1textarea_label'] ?></label></th>
                <td>
                    <textarea name="custom1textarea" id="custom1textarea"><?php echo esc_attr(get_the_author_meta('custom1textarea', $user->ID)); ?></textarea><br/>
                </td>
            </tr>
	    <?php endif; ?>
    </table>
    <?php /*
    <h3><?php _e('Код доступа', 'mbl_admin'); ?><a id="activation"></a></h3>
    <table class="form-table">
        <tr>
            <th><label><?php _e('Коды доступа', 'mbl_admin'); ?></label></th>
            <td>
                <div  class="mbl-settings-color">
                    <p>
                        <span class="description"><?php _e('Добавить новый ключ', 'mbl_admin'); ?></span><br>
                    </p>

                    <p>
                        <input type="text" id="user_key" value="" placeholder="<?php _e('Ваш код доступа', 'mbl_admin'); ?>" class="regular-text">
                        <button type="button" class="button add-new-key"><?php _e('Добавить', 'mbl_admin'); ?></button>
                        <span class="result"></span><br>
                    </p>
                    <p>
                        <span class="description"><?php _e('Ваши коды доступа', 'mbl_admin'); ?></span><br>
                    </p>
                    <?php

                    $html = wpm_user_keys($user, false, true);

                    if (!empty($html)) echo '<ul class="user-keys-list">' . $html . '</ul>';

                    ?>

                    <script>
                        jQuery(document).ready(function () {
                            jQuery(document).on('click', '.remove-key', function () {
                                var elem = jQuery(this);
                                var key = elem.attr('data-key');

                                jQuery.ajax({
                                    type: 'GET',
                                    dataType: 'json',
                                    url: ajaxurl,
                                    data: {
                                        action: "wpm_move_key_to_ban_action",
                                        key: key,
                                        user: <?php echo $user->ID;?>
                                    },
                                    success: function (data) {
                                        if(!data.error){
                                            elem.parent('li').addClass('banned_key');
                                            location.reload();
                                        }
                                    },
                                    error: function (errorThrown) {
                                        //console.log(errorThrown);
                                    }
                                });
                            });
                        });
                    </script>
                </div>
            </td>
        </tr>
    </table>
    */ ?>
    <?php
    $keys = wpm_user_all_keys($user);
    ?>
    <svg width="0" height="0" class="hidden">
        <symbol xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" id="Infinity">
            <path fill="currentColor" d="M0 241.1C0 161 65 96 145.1 96c38.5 0 75.4 15.3 102.6 42.5L320 210.7l72.2-72.2C419.5 111.3 456.4 96 494.9 96C575 96 640 161 640 241.1v29.7C640 351 575 416 494.9 416c-38.5 0-75.4-15.3-102.6-42.5L320 301.3l-72.2 72.2C220.5 400.7 183.6 416 145.1 416C65 416 0 351 0 270.9V241.1zM274.7 256l-72.2-72.2c-15.2-15.2-35.9-23.8-57.4-23.8C100.3 160 64 196.3 64 241.1v29.7c0 44.8 36.3 81.1 81.1 81.1c21.5 0 42.2-8.5 57.4-23.8L274.7 256zm90.5 0l72.2 72.2c15.2 15.2 35.9 23.8 57.4 23.8c44.8 0 81.1-36.3 81.1-81.1V241.1c0-44.8-36.3-81.1-81.1-81.1c-21.5 0-42.2 8.5-57.4 23.8L365.3 256z"></path>
        </symbol>
    </svg>
    <table class="form-table">
        <tr>
            <th><label>Коды доступа</label></th>
            <td>
                <div class="mbl-table-wrap mbl-settings-color">
                    <div id="mlbResult" class="mbl-notice" style="display:none;"></div>
                    <div class="mbl-key-forms">
                        <?php if (wpm_is_admin()): ?>
                            <div class="mbl-form-key">
                                <div>
                                    <label for="userWpmLvl"><?php _e('Выберите уровень доступа', 'mbl_admin') ?></label>
                                    <select id="userWpmLvl"
                                            class="mbl-users-level"><?php echo wpm_get_levels_select(); ?></select>
                                </div>
                                <div>
                                    <label>Время действия <input id="userWpmDuration" type="number" value="12" min="1"
                                                                 max="99"/></label>
                                    <label>
                                        <select id="userWpmUnits">
                                            <option value="months">месяцев</option>
                                            <option value="days">дней</option>
                                        </select>
                                    </label>
                                </div>
                                <div>
                                    <label>Неограниченный доступ <input id="userWpmUnlimited" type="checkbox"></label>
                                </div>
                                <div>
                                    <button id="addKeyFromData" class="button button-primary" type="button">Добавить
                                    </button>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div>
                            <label>Добавить новый ключ <input id="userWpmKey" type="text" placeholder="Ваш код доступа" class="mbl-level-key"></label>
                            <button id="addNewKey" class="button button-primary" type="button">Добавить</button>
                        </div>
                    </div>
                    <div class="wpm-ajax-overlay">
                        <div class="wpm-spinner">
                            <div class="rect1"></div>
                            <div class="rect2"></div>
                            <div class="rect3"></div>
                            <div class="rect4"></div>
                            <div class="rect5"></div>
                        </div>
                    </div>
                    <table id="mblKeysTable" class="mbl-table"<?php if (empty($keys)): ?> style="display: none"<?php endif; ?>>
                        <thead>
                        <tr>
                            <th>Уровень доступа</th>
                            <th>Код доступа</th>
                            <th class="mbl-status"></th>
                            <th class="mbl-duration">Время действия</th>
                            <th class="mbl-date">Начало</th>
                            <th class="mbl-date">Конец</th>
                            <th class="mbl-left">Осталось</th>
                            <?php if (wpm_is_admin()): ?>
                                <th class="mbl-remove">Удалить</th>
                            <?php endif; ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($keys as $key): ?>
                            <tr>
                                <td><?= $key['term'] ? $key['term']->name : 'Уровень доступа удалён' ?></td>
                                <td class="mbl-key-col"><span class="mbl-key"><?= $key['key'] ?></span></td>
                                <td class="mbl-key-status">
                                    <?php if ($key['status'] === 'banned'): ?>
                                        <span title="Удален" style="color: red" class="dashicons dashicons-no-alt"></span>
                                    <?php elseif ($key['status'] === 'expired'): ?>
                                        <span title="Закончился" style="color: #ff9f09" class="dashicons dashicons-backup"></span>
                                    <?php else: ?>
                                        <span title="Активен" style="color: #00AD47" class="dashicons dashicons-post-status"></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($key['is_unlimited'] === 1): ?>
                                        Не ограничено
                                    <?php elseif ($key['units'] === 'months'): ?>
                                        <?= $key['duration'] ?> мес.
                                    <?php elseif ($key['units'] === 'days'): ?>
                                        <?= $key['duration'] ?> дн.
                                    <?php endif; ?>
                                </td>
                                <td><?= $key['date_start_format'] ?></td>
                                <td>
                                    <?php if ($key['is_unlimited'] === 1): ?>
                                        <svg class="mbl-icon">
                                            <use href="#Infinity"></use>
                                        </svg>
                                    <?php else: ?>
                                        <?= $key['date_end_format'] ?>
                                    <?php endif; ?>
                                </td>
                                <td class="mbl-key-left">
                                    <?php
                                    if ($key['is_unlimited'] === 1) {
                                        echo '<svg class="mbl-icon"><use href="#Infinity"></use></svg>';
                                    } else {
                                        echo $key['left'] === '—' ? $key['left'] : "{$key['left']} дн.";
                                    }
                                    ?>
                                </td>
                                <?php if (wpm_is_admin()): ?>
                                    <td class="mbl-key-action">
                                        <?php if ($key['is_banned'] !== 1): ?>
                                            <button class="mbl-remove-key" data-key="<?= $key['key'] ?>">
                                                <span class="dashicons dashicons-trash"></span>
                                            </button>
                                        <?php else: ?>
                                            —
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </td>
        </tr>
    </table>
    <?php
    $user_ID = get_current_user_id();
    $user_data = get_userdata($user_ID);
    if (count(array_intersect(array('administrator', 'teacher', 'editor', 'manageruser'), $user_data->roles))) : ?>
    <table class="form-table">
        <tr>
            <th><label><?php _e('Настройки автотренингов', 'mbl_admin'); ?></label></th>
            <td>
                <div class="mbl-settings-color" style="display: flex">
                    <div style="min-width: 160px; margin-right: 15px">
                        <p>
                            <span class="description"><?php _e('Выбор автотренинга', 'mbl_admin'); ?></span><br>
                        </p>
                        <p>
                            <select name="auto_training_access">
                            <?php foreach ($autotrainings AS $autotraining) : ?>
                                  <option value="<?php echo $autotraining->term_id; ?>"><?php echo $autotraining->name; ?></option>
                            <?php endforeach; ?>
                            </select>
                        </p>
                    </div>
                    <div>
                        <p>
                            <span class="description"><?php _e('Введите порядковый номер урока', 'mbl_admin'); ?></span><br>
                        </p>
                        <p>
                            <input type="text" style="width: 40px" name="auto_training_access_number">
                            <button type="button" class="button add-new-auto-training-access"><?php _e('Сохранить', 'mbl_admin'); ?></button>
                            <span class="access-result"></span><br>
                        </p>
                    </div>
                </div>
            </td>
        </tr>
    </table>
    <?php endif; ?>
<?php
}

add_action('personal_options_update', 'wpm_save_extra_profile_fields');
add_action('edit_user_profile_update', 'wpm_save_extra_profile_fields');

function wpm_save_extra_profile_fields($user_id)
{
    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }

    update_user_meta($user_id, 'surname', $_POST['surname'] ?? '');
    update_user_meta($user_id, 'custom1', $_POST['custom1'] ?? '');
    update_user_meta($user_id, 'custom2', $_POST['custom2'] ?? '');
    update_user_meta($user_id, 'custom3', $_POST['custom3'] ?? '');
    update_user_meta($user_id, 'custom4', $_POST['custom4'] ?? '');
    update_user_meta($user_id, 'custom5', $_POST['custom5'] ?? '');
    update_user_meta($user_id, 'custom6', $_POST['custom6'] ?? '');
    update_user_meta($user_id, 'custom7', $_POST['custom7'] ?? '');
    update_user_meta($user_id, 'custom8', $_POST['custom8'] ?? '');
    update_user_meta($user_id, 'custom9', $_POST['custom9'] ?? '');
    update_user_meta($user_id, 'custom10', $_POST['custom10'] ?? '');
    update_user_meta($user_id, 'custom1textarea', $_POST['custom1textarea'] ?? '');

    update_user_meta($user_id, 'avatar', $_POST['avatar'] ?? '');

}

/**
 *
 */
function wpm_add_contact_methods($profile_fields)
{

    /* $user = wp_get_current_user();
     if (!in_array('customer', $user->roles)) return;*/
    // Add new fields
    $profile_fields['phone'] = __('Телефон', 'mbl_admin');
    return $profile_fields;
}

add_filter('user_contactmethods', 'wpm_add_contact_methods');

/**
 *
 */
add_action('admin_init', 'wpm_user_profile');
function wpm_user_profile()
{
    $user = wp_get_current_user();
    if (!in_array('customer', $user->roles)) return;

    //removes the `profile.php` admin color scheme options
    remove_action('admin_color_scheme_picker', 'admin_color_scheme_picker');
    add_action('admin_head', 'wpm_hide_personal_options');
}

function wpm_hide_personal_options()
{
    echo "\n" . '<script type="text/javascript">jQuery(document).ready(function($) { $(\'form#your-profile > h3:first\').hide(); $(\'form#your-profile > table:first\').hide(); $(\'form#your-profile\').show(); });</script>' . "\n";
}


function wpm_add_key_to_user($user_id = null, $code = null, $return = false, $source = '')
{
    if (!$user_id) {
        $user_id = $_POST['user_id'];
    }
    if (!$user_id) {
        $user_id = get_current_user_id();
    }
    if ($code === null) {
        $code = trim($_POST['key']);
    }
    if (isset($_POST['source'])) {
        $source = $_POST['source'];
    }

    $user = get_userdata($user_id);

    $result = ['error' => false];

    $index = wpm_search_key_id($code);

    // check if user key exist
    if (empty($index)) {
        // if not exist
        $result['error'] = true;
        $result['message'] = __('Неверный ключ', 'mbl');
    } else {
        // if exist
        if ($index['key_info']['status'] == 'new') {
            wpm_update_user_key_dates($user_id, $code, false, $source);
            $term_id = wpm_array_get($index, 'key_info.term_id');
            $result['message'] = __('Ключ добавлен', 'mbl');

            MBLSubscription::add($user_id, $term_id);
        } else {
            // if key is used
            $result['message'] = __('Этот ключ уже используется', 'mbl');
            $result['error'] = true;
        }
    }

    $result['keys'] = wpm_user_keys($user, $is_table = true, false);

    if ($return) {
        return $result;
    } else {
        if (isset($_POST['include_keys'])) {
            $result['_keys'] = wpm_user_all_keys((int)$user_id);
        }
        echo json_encode($result);
        die();
    }
}

add_action('wpm_update_user_key_dates', 'wpm_key_activated_to_user', 10, 5);
function wpm_key_activated_to_user($user_id, $term_id, $code, $key, $source) {
    global $wpdb;
    $key_id = $key['item_id'];
    $table = "{$wpdb->prefix}memberlux_keys_meta";
    $key_meta = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE key_id = %d", [$key_id]));
    $data = [
        'activation_datetime' => date('Y-m-d H:i:s', time()),
        'source' => $source
    ];
    if (wpm_has_utm()) {
        $data = array_merge($data, wpm_get_utm());
        wpm_clean_utm();
    }
    if (!is_null($key_meta)) {
        $wpdb->update($table, $data, ['key_id' => $key_id]);
    } else {
        $wpdb->insert($table, array_merge(['key_id' => $key_id], $data));
    }
}

add_action('wp_ajax_wpm_add_key_to_user_profile_action', 'wpm_add_key_to_user_in_profile', 10, 0);
function wpm_add_key_to_user_in_profile()
{
    if (!wpm_is_admin()) {
        die(json_encode([
            'error' => true,
            'message' => 'У вас нет прав на совершение этого действия',
        ]));
    }
    $user_id = intval($_POST['user']);
    $term_id = intval($_POST['term_id']);
    $duration = (int)$_POST['duration'];
    $units = $_POST['units'];
    $is_unlimited = intval($_POST['is_unlimited']);
    if ($duration > 0) {
        $user = get_user_by('ID', $user_id);
        $code = wpm_insert_one_user_key($term_id, $duration, $units, $is_unlimited);
        wpm_add_code_to_user($user->ID, $code, 'profile_page_admin');
        wpm_send_email_about_new_key($user, $code, $term_id);
        $result = [
            'error' => false,
            'message' => 'Код доступа успешно добавлен!',
            '_keys' => wpm_user_all_keys($user)
        ];
    } else {
        $result = [
            'error' => true,
            'message' => 'Укажите корректное значение поля Время действия',
        ];
    }
    echo json_encode($result);
    die();
}

add_action('wp_ajax_wpm_add_key_to_user_action', 'wpm_add_key_to_user', 10, 0);

function wpm_add_auto_training_access() {
    $user_id = intval(trim($_POST['user_id']));
    $term_id = intval(trim($_POST['term_id']));
    $level = intval(trim($_POST['level']));

    $training_access = get_user_meta($user_id, 'training_access', true);
    $training_access = (empty($training_access) && !is_array($training_access)) ? array() : $training_access;

    $new_training_access = array();

    foreach ($training_access AS $training_access_item) {
        if($training_access_item['term_id'] != $term_id) {
            $new_training_access[] = $training_access_item;
        }
    }

    array_push($new_training_access, array('level' => $level, 'term_id' => $term_id));
    update_user_meta($user_id, 'training_access', $new_training_access);

    echo json_encode(array('result' =>  __('Уровень автотренинга обновлен', 'mbl_admin')));
    die();
}


add_action('wp_ajax_wpm_add_auto_training_access', 'wpm_add_auto_training_access');

function wpm_get_user_keys_info($user_id)
{
    $userKeys = MBLTermKeysQuery::findByUserId($user_id);

    return MBLTermKeysQuery::transformKeysToInfo($userKeys);
}

function wpm_update_user_key_dates($user_id, $code, $isBanned = false, $source = '')
{
    $key = wpm_search_key_id($code);
    $term_id = $key['term_id'];
    $duration = $key['key_info']['duration'] ?: 0;
    $units = wpm_array_get($key, 'key_info.units', 'months');

    if ($key['is_deleted']) {
        $key['key_info']['date_start'] = date('d-m-Y', time());
        $key['key_info']['date_end'] = date('d-m-Y', strtotime("+$duration " . $units));
        $key['key_info']['status'] = 'used';

        MBLTermKeysQuery::updateKey($key['key_info']);
    } elseif($isBanned) {
        $date_start = strtotime($key['key_info']['date_start']);

        foreach (wpm_get_user_keys_info($user_id) AS $user_key) {
            $start = strtotime($user_key['key_info']['date_start']);
            if ($user_key['term_id'] == $term_id && $start > $date_start) {
                $user_key['key_info']['date_start'] = date('d-m-Y', strtotime("-$duration " . $units, $start));
                $user_key['key_info']['date_end'] = date('d-m-Y', strtotime("-$duration " . $units, strtotime($user_key['key_info']['date_end'])));
                MBLTermKeysQuery::updateKey($user_key['key_info']);
            }
        }
    } else {
        $date_start = time();
        $is_unlimited = false;

        /*
        foreach (wpm_get_user_keys_info($user_id) AS $user_key) {
            if(wpm_array_get($user_key, 'key_info.is_unlimited')) {
                $is_unlimited = true;
                continue;
            }

            if ($user_key['term_id'] == $term_id && wpm_array_get($user_key, 'key_info.key_type') == 'wpm_term_keys' && !$user_key['is_deleted']) {
                $date_start = max($date_start, strtotime($user_key['key_info']['date_end']));
            }
        }
        */
        foreach (wpm_get_user_keys_info($user_id) AS $user_key) {
            if ($user_key['term_id'] == $term_id && wpm_array_get($user_key, 'key_info.key_type') == 'wpm_term_keys' && !$user_key['is_deleted']) {
                if(wpm_array_get($user_key, 'key_info.is_unlimited')) {
                    $is_unlimited = true;
                    continue;
                }
                $date_start = max($date_start, strtotime($user_key['key_info']['date_end']));
            }
        }

        if($is_unlimited) {
            $date_start = time();
        }

        $key['key_info']['date_registered'] = date('d-m-Y', time());
        $key['key_info']['date_start'] = date('d-m-Y', $date_start);
        $key['key_info']['date_end'] = date('d-m-Y', strtotime("+$duration " . $units, $date_start));
        $key['key_info']['status'] = 'used';
        $key['key_info']['user_id'] = $user_id;

        MBLTermKeysQuery::updateKey($key['key_info']);
    }
    /**
     * Fires immediately after the key is activated to the user
     *
     * @since 2.54
     * @param int $user_id
     * @param int $term_id
     * @param string $code
     * @param array $key wpm_search_key_id()
     * @param string $source
     *    Accepts: purchase, free_item, free_registration_form, bulk_operations_reg, bulk_operations_add,
     *             auto_registration, activation_page, profile_page_self, profile_page_admin, after_auto_training_passed
     */
    do_action('wpm_update_user_key_dates', $user_id, $term_id, $code, $key, $source);
}

function wpm_user_all_keys($user) {
    if (is_int($user)) {
        $user = get_user_by('ID', $user);
    }
    $keys = array_merge(
        MBLTermKeysQuery::find(array('user_id' => $user->ID)),
        MBLTermKeysQuery::find(array('user_id' => $user->ID, 'is_banned' => 1, 'key_type' => 'wpm_term_keys')),
    );
    get_terms(['taxonomy' => 'wpm-levels', 'hide_empty' => false,
        'include' => array_map(fn($key) => $key['term_id'], $keys)
    ]);
    return array_map(function ($key) {
        $is_unlimited = (int)$key['is_unlimited'];
        if ((int)$key['is_banned'] === 1) {
            $status = 'banned';
        } elseif ($is_unlimited === 0 && strtotime($key['date_end']) < strtotime(date('Y-m-d 23:59:59'))) {
            $status = 'expired';
        } else {
            $status = 'active';
        }
        if (strtotime($key['date_start']) > strtotime(date('Y-m-d 00:00:00'))) {
            $date1 = new DateTime($key['date_start']);
        } else {
            $date1 = new DateTime();
        }
        $date2 = new DateTime($key['date_end']);
        $interval = $date2->diff($date1);
        if ((int)$key['is_unlimited'] === 1) {
            $left = 'infinity';
        } else {
            $left = (int)$key['is_banned'] !== 1 && $date2 > $date1 ? $interval->days : '—';
        }
        return [
            'key' => $key['key'],
            'is_unlimited' => $is_unlimited,
            'duration' => $key['duration'],
            'units' => $key['units'],
            'is_banned' => (int)$key['is_banned'],
            'date_start' => $key['date_start'],
            'date_end' => $key['date_end'],
            'date_start_format' => date('d.m.Y', strtotime($key['date_start'])),
            'date_end_format' => date('d.m.Y', strtotime($key['date_end'])),
            'term' => get_term((int)$key['term_id'], 'wpm-levels'),
            'status' => $status,
            'left' => $left
        ];
    }, $keys);
}

function wpm_move_key_to_ban ()
{
    $user_id = intval($_GET['user']);
    $result = array(
        'message' => '',
        'error' => false,
    );

    $key = MBLTermKeysQuery::findOne(array(
        'user_id' => $user_id,
        'key' => trim($_GET['key'])
    ));

    if($key) {
        $key['is_banned'] = 1;

        MBLTermKeysQuery::updateKey($key);
    }
    $result['_keys'] = wpm_user_all_keys($user_id);
    echo json_encode($result);
    die();
}
add_action('wp_ajax_wpm_move_key_to_ban_action', 'wpm_move_key_to_ban');

function wpm_search_key_id($key)
{
    $data = MBLTermKeysQuery::findOne(array('key' => trim($key)));

    return MBLTermKeysQuery::transformKeyToInfo($data);
}

/*
* On login attempt check if user account is active
*/
function wpm_check_login($user, $username, $password)
{
    if(is_wp_error($user)) {
        return $user;
    }

    $meta = wpm_get_user_status($user->ID);

    if ($meta == 'inactive' && !in_array('administrator', $user->roles)) {
        return new WP_Error('wpm_inactive', __('Ваш аккаунт временно заблокирован.', 'mbl_admin'));
    } else {
        return $user;
    }
}
add_filter('authenticate', 'wpm_check_login', 100, 3);

/*
* Insert new items to the bulk actions dropdown on users.php
*/
function wpm_bulk_admin_footer()
{
    if(!current_user_can('activate_plugins')) {
        return;
    }
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('<option>').val('wpm_deactivate_account').text('<?php _e('Заблокировать', 'mbl_admin'); ?>').appendTo("select[name='action']");
            $('<option>').val('wpm_activate_account').text('<?php _e('Разблокировать', 'mbl_admin'); ?>').appendTo("select[name='action']");
        });
    </script>
<?php
}
add_action('admin_footer-users.php', 'wpm_bulk_admin_footer');

/*
* Perform bulk actions on form submit
*/
function wpm_users_bulk_action()
{
    if(!current_user_can('activate_plugins')) {
        return;
    }

    $wp_list_table = _get_list_table('WP_Users_List_Table');

    $action = $wp_list_table->current_action();
    $ret = wpm_array_get($_GET, 'ret', 'users');

    switch($action) {
        case 'wpm_deactivate_account':
            $user_ids = $_GET['users'];
            $deactivated = 0;
            foreach( $user_ids as $user_id ) {
                if(get_current_user_id() != $user_id){
                    update_user_meta($user_id, 'wpm_status', 'inactive');
                    $deactivated++;
                }
            }
            $sendback = add_query_arg( array('deactivated' => $deactivated ), $sendback ?? '' );
            break;
        case 'wpm_activate_account':
            $user_ids = $_GET['users'];
            $activated = 0;
            foreach( $user_ids as $user_id ) {
                update_user_meta($user_id, 'wpm_status', 'active');
                delete_user_meta($user_id, 'wpm_block_filter');
                MBLStatsBlocker::clearUserBlocks($user_id);
                $activated++;
            }
            $sendback = add_query_arg( array('activated' => $activated ), $sendback ?? '' );
            break;
        case 'wpm_activate_single_account':
            update_user_meta($_GET['user'], 'wpm_status', 'active');
            delete_user_meta($_GET['user'], 'wpm_block_filter');
            if($ret == 'users') {
                $sendback = add_query_arg( array('activated' => 1 ), $sendback ?? '' );
            } else {
                $sendback = urldecode($ret);
            }
            $filterId = wpm_array_get($_GET, 'filter_id');
            if($filterId) {
                MBLStatsBlocker::unblockUser(intval($_GET['user']), $filterId);
            } else {
                MBLStatsBlocker::clearUserBlocks(intval($_GET['user']));
            }
            break;
        case 'wpm_deactivate_single_account':
            update_user_meta($_GET['user'], 'wpm_status', 'inactive');

            if($ret == 'users') {
                $sendback = add_query_arg( array('activated' => 1 ), $sendback ?? '' );
            } else {
                $sendback = urldecode($ret);
            }
            break;
        default: return;
    }
    wp_redirect($sendback);
    exit();
}
add_action('load-users.php', 'wpm_users_bulk_action');

/*
* Display admin notice on activation and deactivation of accounts
*/
function custom_bulk_admin_notices()
{
    global $pagenow;

    if ($pagenow == 'users.php') {
        if (isset($_REQUEST['deactivated']) && (int) $_REQUEST['deactivated']) {
            $message = sprintf( _n( 'Пользовательский аккаунт заблокирован.', '%s пользовательских аккаунтов заблокировано.', $_REQUEST['deactivated'], 'mbl_admin' ), number_format_i18n( $_REQUEST['deactivated'] ) );
            echo "<div class=\"updated\"><p>$message</p></div>";
        } elseif(isset($_REQUEST['activated']) && (int) $_REQUEST['activated']) {
            $message = sprintf( _n( 'Пользовательский аккаунт разблокирован.', '%s пользовательских аккаунтов разблокировано.', $_REQUEST['activated'], 'mbl_admin' ), number_format_i18n( $_REQUEST['activated'] ) );
            echo "<div class=\"updated\"><p>$message</p></div>";
        }
    }
}
add_action('admin_notices', 'custom_bulk_admin_notices');

/*
* Display status of each account in the WordPress users table
*/
function wpm_add_user_id_column($columns)
{
    $columns['wpm_status'] = __('Состояние аккаунта', 'mbl_admin');

    return $columns;
}
add_filter('manage_users_columns', 'wpm_add_user_id_column');


function wpm_show_user_id_column_content($value, $column_name, $user_id, $ret = 'users')
{
    $account_status = wpm_get_user_status($user_id);
    $user           = get_user_by('id', $user_id );

    if (!in_array('administrator', $user->roles) && 'wpm_status' == $column_name ) {
        return $account_status=='active'
            ? '<b style="color: #008000;">' . __('Активен', 'mbl_admin') . '</b> <br /><a href="' . admin_url('/users.php?action=wpm_deactivate_single_account&user=' . $user_id . '&ret=' . $ret) . '">' . __('Блокировать', 'mbl_admin') . '</a>'
            : '<b style="color: red;">' . __('Блокирован', 'mbl_admin') . '</b> <br /><a href="' . admin_url('/users.php?action=wpm_activate_single_account&user=' . $user_id . '&ret=' . $ret) . '">' . __('Снять блок', 'mbl_admin') . '</a>';
    }

    return $value;
}
add_action('manage_users_custom_column',  'wpm_show_user_id_column_content', 10, 3);

function wpm_pre_user_query($userQuery)
{
    if (!isset($_GET['s']) || trim($_GET['s']) == '' || stripos($_SERVER['REQUEST_URI'], 'users.php') === false) {
        return;
    }

    $term = sanitize_text_field(trim($_GET['s']));
    $userId = wpm_array_get(MBLTermKeysQuery::findOne(array('key' => $term), array('user_id')), 'user_id');

    if ($userId) {
        $extraSql = " OR ID = {$userId}";

        $addAfter = 'WHERE ';
        $addPosition = strpos($userQuery->query_where, $addAfter) + strlen($addAfter);

        $userQuery->query_where = substr($userQuery->query_where, 0, $addPosition) . '(' . substr($userQuery->query_where, $addPosition) . ')' . $extraSql;
    }
}
add_action('pre_user_query', 'wpm_pre_user_query', 100);

function wpm_check_registration_errors($errors, $update, $user)
{
	if (!$update && wpm_is_users_overflow()) {
		$errors->add('users_overflow_error', __('<strong>ОШИБКА</strong>: Регистрация временно недоступна.', 'mbl'));
	}

	return $errors;
}

add_filter('user_profile_update_errors', 'wpm_check_registration_errors', 10, 3);
