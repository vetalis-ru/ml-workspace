<?php
add_action('edit_user_profile', function ($profile_user) {
    if (!in_array('coach', $profile_user ->roles)) return;
    $userAccess = get_user_meta($profile_user->ID, '_mblc_coach_certificate_access', true);
    include "template.php";
}, 10, 1);

add_action('edit_user_profile_update', function ($user_id) {
    if (isset($_POST['_mblc_coach_certificate_access'])) {
        $post = $_POST['_mblc_coach_certificate_access'];
        $val = $post !== 'all' ? array_values(array_map(fn($v) => absint($v), $post)) : 'all';
        update_user_meta($user_id, "_mblc_coach_certificate_access", $val);
    } else {
        delete_user_meta($user_id, "_mblc_coach_certificate_access");
    }
}, 10, 1);

add_action('admin_head-user-edit.php', function () {
    ?>
    <style>
        .mblc-settings-color, .mbl-settings-color.mblc-settings-color{
            box-shadow: #2E7F84 4px 4px 0 0!important;
        }
    </style>
    <?php
});