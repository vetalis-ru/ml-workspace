<?php
add_action('admin_menu', 'mblc_register_main_admin_page_submenu');
function mblc_register_main_admin_page_submenu()
{
    $my_page = add_submenu_page('mblc_certificate',
        'Выдача сертификатов',
        'Выдача сертификатов',
        CERTIFICATE_DELIVERY,
        'mblc_certificate',
        'render_graduates_list_page'
    );
    add_action('load-' . $my_page, function () {
        add_action('admin_enqueue_scripts', function ()
        {
            mblc_enqueue_style('mblc-style');
            mblc_enqueue_style('mblc-vanilla-js-datepicker');
            mblc_enqueue_script('mblc-vanilla-js-datepicker-ru');
            mblc_enqueue_style('mblc_select2');
            mblc_enqueue_script('mblc_select2');
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
    });
}
