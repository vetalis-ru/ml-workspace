<?php

require_once "page.php";
require_once "shortcode.php";
require_once "mailing-shortcode.php";

/**
 * @param int $id
 * @param string $ext 'pdf' | 'jpg'
 * @param string $type 'view' | 'download'
 * @return string
 */
function certUrl(int $id, string $ext, string $type): string
{
    $urlGen = new UrlCrypt();
    $certificate_id = $urlGen->encode($id);

    return home_url() . "/mbl-certificates/$type/$ext/$certificate_id";
}
