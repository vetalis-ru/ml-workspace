<?php
add_action('admin_menu', 'mblc_admin_menu_mailer_submenu_page');
function mblc_admin_menu_mailer_submenu_page()
{
    $my_page = add_submenu_page('mblc_certificate',
        'Настройки',
        'Настройки',
        CERTIFICATE_EDIT,
        'mblc_certificate_mailer',
        'render_mailer_list_page'
    );
    add_action('load-' . $my_page, function () {
        add_action('admin_enqueue_scripts', 'mblc_admin_menu_mailer_submenu_page_assets');
    });
}

function mblc_admin_menu_mailer_submenu_page_assets()
{
    mblc_enqueue_style('mblc-style');
    mblc_enqueue_script('mblc-bootstrap');
    mblc_enqueue_script(
        'mblc-certs',
        mblc_plugin_assets_uri('js/script.js'),
        array(),
        '',
        true
    );
    mblc_enqueue_script('mblc-color-picker', mblc_plugin_assets_uri('js/jscolor/jscolor.js'));
}

function render_mailer_list_page()
{
    include 'view.php';
}
