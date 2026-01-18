<?php
add_filter('mblc_get_default_text', function ($options) {
    return $options + [
            'presence_check_msg' => 'Прежде чем скачать заполните недостающие данные:',
            'save_user_name_btn' => 'Сохранить',
        ];
}, 10, 1);

add_filter('mblc_certificate_show', function ($show, $certificate) {
    $user_id = $certificate->user_id;
    $first_name = get_user_meta($user_id, 'first_name', true);
    $last_name = get_user_meta($user_id, 'last_name', true);

    return !empty($first_name) && !empty($last_name);
}, 10, 2);

add_action('mblc_certificate_render', function ($show, $certificate) {
    $user_id = $certificate->user_id;
    $first_name = get_user_meta($user_id, 'first_name', true);
    $last_name = get_user_meta($user_id, 'last_name', true);
    if (empty($first_name) || empty($last_name)) {
        global $wp_query;
        $wp_query->is_404 = false;
        status_header(200);
        include "template.php";

        die();
    }
}, 10, 2);

add_action('mblc_certificate_before_render', function (Certificate $certificate) {
    if (isset($_POST['action']) && $_POST['action'] === 'mblc_form_presence_check') {
        $user_id = $certificate->user_id;
        $userdata = [];
        if (isset($_POST['first_name']) && !empty($_POST['first_name'])) {
            $userdata['first_name'] = $_POST['first_name'];
        }
        if (isset($_POST['last_name']) && !empty($_POST['last_name'])) {
            $userdata['last_name'] = $_POST['last_name'];
        }
        if (!empty($userdata)) {
            $userdata['ID'] = $user_id;
            wp_update_user($userdata);
        }
    }
}, 10, 3);
