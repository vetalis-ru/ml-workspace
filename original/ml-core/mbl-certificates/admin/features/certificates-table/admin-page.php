<?php
add_action('admin_menu', 'mblc_admin_menu_edit_certificate_submenu_page');
function mblc_admin_menu_edit_certificate_submenu_page()
{
    $my_page = add_submenu_page('mblc_certificate',
        'Выданные сертификаты',
        'Выданные сертификаты',
        CERTIFICATE_EDIT,
        'mblc_certificate_edit',
        'render_edit_certificate_page'
    );
    add_action('load-' . $my_page, 'mblc_certificate_edit_assets');
}

function mblc_certificate_edit_assets()
{
    add_action('admin_enqueue_scripts', function () {
        mblc_enqueue_style('mblc-style');
        mblc_enqueue_script('mblc-bootstrap');
        mblc_enqueue_style('mblc_select2');
        mblc_enqueue_script(
            "mblc-formToObject",
            mblc_plugin_assets_uri('js/formToObject.min.js')
        );
        mblc_enqueue_script(
            "mblc-certificate",
            mblc_plugin_assets_uri('js/certificate.js'),
            ['mblc-formToObject', 'jquery', 'mblc_select2'],
        );
        mblc_enqueue_style('mblc-vanilla-js-datepicker');
        mblc_enqueue_script('mblc-vanilla-js-datepicker-ru');
        add_action('admin_footer', function () {
            ?>
            <script>
                window.addEventListener('load', function () {
                    Array.from(document.querySelectorAll('[data-toggle="datepicker"]')).forEach(elem => {
                        const datepicker = new Datepicker(elem, {
                            autohide: true,
                            language: 'ru',
                            format: 'dd.mm.yyyy',
                        });
                    });
                })
            </script>
            <?php
        }, 99);
    });
}
