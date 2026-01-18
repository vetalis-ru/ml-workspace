<?php

require_once __DIR__ . "/shortcodes.php";
require_once __DIR__ . "/wpm-levels-options.php";
require_once __DIR__ . "/pages.php";
require_once __DIR__ . "/mailing-save.php";
require_once __DIR__ . "/key-activate-time.php";
require_once __DIR__ . "/rest.php";
require_once __DIR__ . "/send-mails-fields.php";

add_filter('script_loader_tag', function ($tag, $handle, $src) {
    if (preg_match('/^mblar-.+$/', $handle) === 1) {
        return str_replace(' src', ' defer src', $tag);
    }
    return $tag;
}, 10, 3);
add_action('admin_enqueue_scripts', function ($hook_suffix) {
    $plugin = new Mbl\AutoResponder\Plugin();
    $version = $plugin->assets_version();
    wp_register_style('mblar-bootstrap', $plugin->assets_uri('css/bootstrap.min.css?v=' . $version));
    wp_register_script('mblar-admin', $plugin->assets_uri('js/main.js?v=' . $version));
    wp_register_script('mblar-template-list', $plugin->assets_uri('js/template-list.js?v=' . $version));
    wp_register_script('mblar-template-edit', $plugin->assets_uri('js/template-edit.js?v=' . $version));
    wp_register_script('mblar-options', $plugin->assets_uri('js/options.js?v=' . $version));
    wp_localize_script('mblar-template-list', '__mblar_data__', $plugin->js_data());
    wp_localize_script('mblar-options', '__mblar_data__', $plugin->js_data());
}, 10);
add_action('wpm-levels_edit_form_fields', 'mblar_admin_view', 25);
function mblar_admin_view()
{
    ob_start();
    wp_editor('', '__editor__');
    ob_end_clean();
    ?>
    <tr class="form-field">
        <th scope="row">Рассылка</th>
        <td>
            <div class="wpm-inner-wrap mbl-color-content" style="width: 95%;box-sizing: border-box;box-shadow: #edc308 4px 4px 0 0 !important;">
                <div id="mblar-root"></div>
            </div>
        </td>
    </tr>
    <?php
}
