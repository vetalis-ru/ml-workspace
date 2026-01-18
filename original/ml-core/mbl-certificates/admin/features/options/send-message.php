<?php

add_action('mbl_certificate_issued', 'certMailAfterIssuance', 10, 2);
function certMailAfterIssuance($userId, $cert_id)
{
    $recipient = get_userdata($userId)->user_email;
    $subject = mblc_get_option_with_default('cert_mailer_topic');
    $message = wp_unslash(mblc_mail_text_with_variables($userId, $cert_id));
    $attachments = array();
    if (function_exists('wpm_send_mail')) {
        $message = apply_filters('wpm_user_mail_shortcode_replacement', $message, $userId, []);
        wpm_send_mail([$recipient], $subject, $message, '', '', [], null, $attachments);
    }
}

add_action('wpm_update_user_key_dates', 'mblc_certificate_issuance_after_update_user_key_dates', 10, 2);
function mblc_certificate_issuance_after_update_user_key_dates($user_id, $wpmlevel_id) {
    if (get_term_meta($wpmlevel_id, '_mblc_how_to_issue', true) === 'auto') {
        Certificate::create(
            $user_id,
            get_term_meta($wpmlevel_id, '_mblc_course_name', true),
            (int)get_term_meta($wpmlevel_id, '_mblc_template_id', true),
            $wpmlevel_id,
            get_user_meta($user_id, 'first_name', true),
            get_user_meta($user_id, 'last_name', true),
            get_user_meta($user_id, 'surname', true),
            date('Y-m-d'),
            get_term_meta($wpmlevel_id, '_mblc_certificate_series', true),
            get_current_user_id(),
            date('Y-m-d'),
            get_term_meta($wpmlevel_id, '_mblc_course_name', true)
        );
    }
}
