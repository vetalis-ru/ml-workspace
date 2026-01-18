<?php
add_action('show_user_profile', 'mblc_user_profile_available_certificates', 10);
add_action('edit_user_profile', 'mblc_user_profile_available_certificates', 10);

function mblc_user_profile_available_certificates($profile_user)
{
    try {
        $certificates = array_map(
            function ($certificate) {
                return [
                    'text' => $certificate->getCertificateName(),
                    'view_pdf' => certUrl($certificate->id, 'pdf', 'view'),
                    'download_pdf' => certUrl($certificate->id, 'pdf', 'download'),
                    'download_jpg' => certUrl($certificate->id, 'jpg', 'download'),
                    'view_jpg' => certUrl($certificate->id, 'jpg', 'view'),
                ];
            },
            Certificate::getCustomerCertificates($profile_user->ID)
        );
    } catch (Exception $e) {
        $certificates = [];
    }

    include 'view.php';
}

add_filter('admin_enqueue_scripts', 'mblc_post_save_access_key');
function mblc_post_save_access_key()
{
    if (in_array(get_current_screen()->id, ['user-edit', 'profile'])) {
	    mblc_enqueue_style( "certificate-shortcodes" );
    }
}
