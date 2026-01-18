<?php
require 'certificate-verification-shortcode.php';
require 'search-results.php';

add_action('wp_enqueue_scripts', function () {
    global $post;
    if (isset($post->post_content) && has_shortcode($post->post_content, 'm_certificate_verification')) {
        mblc_enqueue_script("alpinejs", mblc_plugin_assets_uri("js/alpine.min.js"));
        mblc_enqueue_script(
            "certificate-verification-shortcode",
            mblc_plugin_assets_uri("js/certificate-verification-shortcode.js"),
            ['jquery']
        );
        wp_localize_script("certificate-verification-shortcode", 'mbl_data', [
            'ajax_url' => admin_url('admin-ajax.php')
        ]);
	    mblc_enqueue_style( "certificate-shortcodes" );
    }
    if (isset($post->post_content) && has_shortcode($post->post_content, 'm_certificate_list')) {
	    mblc_enqueue_style( "certificate-shortcodes" );
    }
});
add_filter('script_loader_tag', function ($tag, $handle, $src) {
    if ($handle === 'alpinejs') {
        return str_replace(' src', ' defer src', $tag);
    }
    return $tag;
}, 10, 3);

add_shortcode('m_certificate_verification', 'm_certificate_verification');


add_action('wp_ajax_ml_verify_certificate_by_fio', 'existCertificateByAjax');
add_action('wp_ajax_nopriv_ml_verify_certificate_by_fio', 'existCertificateByAjax');
add_action('wp_ajax_ml_verify_certificate_by_series', 'existCertificateByAjax');
add_action('wp_ajax_nopriv_ml_verify_certificate_by_series', 'existCertificateByAjax');

function existCertificateByAjax()
{
    $result = [];
    if ($_POST['action'] === 'ml_verify_certificate_by_fio') {
        $result = Certificate::getGroupingCertificateByFIO(
            $_POST['graduate_last_name'],
            $_POST['graduate_first_name'],
            $_POST['graduate_surname'] ?? ''
        );
    } elseif ($_POST['action'] === 'ml_verify_certificate_by_series') {
        $certificates = Certificate::getCertificateBySeriesNumber(
            $_POST['series'],
            $_POST['number']
        );
        foreach ($certificates as $certificate) {
            /**
             * @var Certificate $certificate
             */
            $result[$certificate->user_id]['fio'] = $certificate->getFIO();
            $result[$certificate->user_id]['certificates'][] = $certificate;
        }
    } else {
        die();
    }

    if (empty($result)) {
        die(json_encode([
            'status' => 'warning',
            'message' => mblc_get_option_with_default('not_found')
        ]));
    }
    die(json_encode([
        'status' => 'success',
        'message' => printSearchResult($result)
    ]));
}
