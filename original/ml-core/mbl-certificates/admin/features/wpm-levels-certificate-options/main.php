<?php
require_once "mblc_save_certificate_fields.php";
$tax_name = 'wpm-levels';
add_action("{$tax_name}_edit_form_fields", 'mblc_wpm_levels_certificate_options', 10, 2);
/**
 * @param WP_Term $tag Current taxonomy term object.
 * @param string $taxonomy Current taxonomy slug.
 *
 * @return void
 */
function mblc_wpm_levels_certificate_options(WP_Term $tag, string $taxonomy)
{
    $has_certificate = get_term_meta($tag->term_id, '_mblc_has_certificate', true);
    $how_to_issue = get_term_meta($tag->term_id, '_mblc_how_to_issue', true) ?: 'employee';
    $certificate_series = get_term_meta($tag->term_id, '_mblc_certificate_series', true);
    $course_name = get_term_meta($tag->term_id, '_mblc_course_name', true);
    $template_id = (int)get_term_meta($tag->term_id, '_mblc_template_id', true);
    include "template.php";
}

add_action("admin_print_styles-term.php", function () {
    if (isset($_GET['taxonomy']) && $_GET['taxonomy'] === 'wpm-levels') {
        mblc_enqueue_style("mblc-wpm-levels", mblc_plugin_assets_uri('css/mblc-wpm-levels.css'));
    }
});

add_action("admin_print_scripts-term.php", function () {
    if (isset($_GET['taxonomy']) && $_GET['taxonomy'] === 'wpm-levels') {
        mblc_enqueue_script(
            "mblc-wpm-levels-certificate-options",
            mblc_plugin_assets_uri('js/wpm-levels-certificate-options.js'),
            [],
            false,
            true
        );
        wp_localize_script(
            'mblc-wpm-levels-certificate-options',
            'mblc',
            ['wpmLvlId' => $_GET['tag_ID']]
        );
    }
});

add_action("edited_{$tax_name}", 'mblc_save_certificate_fields', 10, 2);

add_action('wp_ajax_mblc_verify_certificate_series', function () {
    $wpmLvlId = intval($_POST['wpmLvlId']);
    $series = $_POST['series'];
    $seriesExist = (new MBLC_WpmLevels())->existCertificateSeries($series, $wpmLvlId);
    if ($seriesExist) {
        wp_send_json_error(["series" => $series]);
    }
    wp_send_json_success(["series" => $_POST['series']]);
});