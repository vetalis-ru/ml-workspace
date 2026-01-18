<?php
function mblc_enqueue_script($handle, $src = '', $deps = array(), $ver = false, $in_footer = false)
{
    wp_enqueue_script($handle, $src, $deps, mblc_version($ver), $in_footer);
}

function mblc_enqueue_style($handle, $src = '', $deps = array(), $ver = false, $media = 'all')
{
    wp_enqueue_style($handle, $src, $deps, mblc_version($ver), $media);
}

function mblc_register_script($handle, $src, $deps = array(), $ver = false, $in_footer = false)
{
    wp_register_script($handle, $src, $deps, mblc_version($ver), $in_footer);
}

function mblc_register_style($handle, $src, $deps = array(), $ver = false, $media = 'all') {
    wp_register_style($handle, $src, $deps, mblc_version($ver), $media);
}

function mblc_plugin_assets_uri($relative_path = ''): string
{
    return plugins_url(basename(dirname(__DIR__, 1)) . "/assets/$relative_path");
}

function mblc_version(string $version): string
{
    return $version ?: MBLC_PLUGIN_VERSION;
}