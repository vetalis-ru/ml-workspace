<?php
add_action('admin_menu', 'mblc_admin_menu_certificate_templates_submenu_page');
function mblc_admin_menu_certificate_templates_submenu_page()
{
    $my_page = add_submenu_page('mblc_certificate',
        'Шаблоны сертификатов',
        'Шаблоны сертификатов',
        CERTIFICATE_EDIT,
        'mblc_certificate_templates',
        'mblc_render_certificate_templates'
    );
    add_action('load-' . $my_page, 'mblc_load_certificate_templates_page_js');
}

add_action('init', 'mblc_template_view_ob_start');
function mblc_template_view_ob_start() {
    if (isset($_GET['page']) && $_GET['page'] === 'mblc_certificate_templates' && !empty($_GET['download'])) {
        ob_start();
    }
}

function mblc_load_certificate_templates_page_js()
{
    add_action('admin_enqueue_scripts', function () {
        mblc_enqueue_style(
            'font-awesome',
            mblc_plugin_assets_uri('css/font-awesome-4.7.0.min.css'),
            [],
            '4.7.0'
        );
        mblc_enqueue_style('jquery-ui', mblc_plugin_assets_uri('css/jquery-ui.min.css'));
        mblc_enqueue_style('mblc-style');
        wp_add_inline_style('mblc-style', FontHandler::generateDynamic());

        mblc_enqueue_script("mblc-jquery", mblc_plugin_assets_uri('js/jquery-3.5.1.min.js'));
        mblc_enqueue_script(
            "mblc-jquery-ui",
            mblc_plugin_assets_uri('js/jquery-ui.min.js'),
            ['mblc-jquery'],
            '',
            true
        );

        mblc_enqueue_script(
            'mblc-templates',
            mblc_plugin_assets_uri('js/script.js'),
            ['mblc-jquery-ui'],
            '',
            true
        );
        wp_localize_script('mblc-templates', '_MBC_',
            ['default_field_settings' => mblc_default_field_settings()]
        );
    });
}
