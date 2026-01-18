<?php
add_action('admin_enqueue_scripts', 'mblc_main_page_assets');
add_action('admin_menu', 'mblc_register_main_admin_page');
function mblc_register_main_admin_page()
{
    add_menu_page(
        'Сертификаты MEMBERLUX',
        'Сертификаты',
        CERTIFICATE_DELIVERY,
        'mblc_certificate',
        'render_graduates_list_page',
        'dashicons-portfolio',
        2
    );
}

function mblc_main_page_assets()
{
    mblc_register_script(
        'mblc-vanilla-js-datepicker',
        mblc_plugin_assets_uri('js/datepicker.min.js'),
        [],
        '1.2.0'
    );
    mblc_register_script(
        'mblc-vanilla-js-datepicker-ru',
        mblc_plugin_assets_uri('js/datepicker-ru.js'),
        ['mblc-vanilla-js-datepicker'],
        '1.2.0'
    );
    mblc_register_script(
        'mblc-popper',
        mblc_plugin_assets_uri('js/popper.min.js'),
        [],
        '1.16.1'
    );
    mblc_register_script(
        'mblc-bootstrap',
        mblc_plugin_assets_uri('js/bootstrap.min.js'),
        ['jquery', 'mblc-popper'],
        '4.5.2'
    );
    mblc_register_style(
        'mblc-vanilla-js-datepicker',
        mblc_plugin_assets_uri('css/datepicker.min.css'),
        [],
        '1.2.0'
    );
    mblc_register_style('mblc-bootstrap4',
        mblc_plugin_assets_uri('css/bootstrap.min.css'),
        [],
        '4.5.2'
    );
    mblc_register_style(
        'mblc-fontawesome',
        mblc_plugin_assets_uri('css/font-awesome-5.15.1.min.css'),
        [],
        '5.15.1'
    );
    mblc_register_style(
        'mblc-style',
        mblc_plugin_assets_uri('css/style.css'),
        ['mblc-bootstrap4', 'mblc-fontawesome']
    );
    mblc_register_style('mblc_select2', mblc_plugin_assets_uri('select2/css/select2.min.css'));
    mblc_register_script('mblc_select2', mblc_plugin_assets_uri('select2/js/select2.min.js'));
}
