<?php
add_action('init', 'mblc_certificate_rewrite');
function mblc_certificate_rewrite()
{
    $page_name = 'mbl-certificates';
    $query = 'index.php?pagename=' . $page_name . '&type=$matches[2]&ext=$matches[3]&certificate_id=$matches[4]';
    add_rewrite_rule('^(mbl-certificates)/(download)/(pdf)/([^/]*)/?', $query, 'top');
    add_rewrite_rule('^(mbl-certificates)/(view)/(pdf)/([^/]*)/?', $query, 'top');
    add_rewrite_rule('^(mbl-certificates)/(download)/(jpg)/([^/]*)/?', $query, 'top');
    add_rewrite_rule('^(mbl-certificates)/(view)/(jpg)/([^/]*)/?', $query, 'top');
    add_filter('query_vars', 'mblc_certificate_query_vars');
}

function mblc_certificate_query_vars($vars)
{
    $vars[] = 'type';
    $vars[] = 'ext';
    $vars[] = 'certificate_id';
    return $vars;
}

add_action('set_404', 'mblc_certificate_view_download_page_not404', 1, 10);
function mblc_certificate_view_download_page_not404(WP_Query $wp_query)
{
    if ($wp_query->get('pagename') === 'mbl-certificates') {
        $wp_query->is_404 = false;
        status_header(200);
    }
}


add_filter('template_include', 'mblc_certificate_view_download_page_template', 1, 999);
function mblc_certificate_view_download_page_template($original_template)
{
    if (get_query_var('pagename') === 'mbl-certificates') {
        try {
            if (!get_query_var('certificate_id')) {
                die("Не указан id сертификата");
            }
            $ext = get_query_var('ext');
            $type = get_query_var('type');
            $certificate_id = (new UrlCrypt())->decode(get_query_var('certificate_id'));
            $certificate = Certificate::getCertificate($certificate_id);
            do_action('mblc_certificate_before_render', $certificate);
            $generator = CertificateTemplate::getCertificateGeneratorByCertificate($certificate, $ext);

            if (!in_array($type, [CertificateGenerator::VIEW, CertificateGenerator::DOWNLOAD])) {
                die("Указан неверный тип вывода");
            }

            $show = apply_filters("mblc_certificate_show", true, $certificate);
            if ($show) {
                $generator->render("certificate.$ext", $type);
            }
            do_action("mblc_certificate_render", $show, $certificate);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        exit();
    } else {
        return $original_template;
    }
}
