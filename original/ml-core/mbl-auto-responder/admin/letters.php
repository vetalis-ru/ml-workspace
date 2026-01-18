<?php

use Mbl\AutoResponder\MailTemplates;

add_action('wpm_mail_templates_field', function ($key, $main_options) {
    $templates = (new MailTemplates($GLOBALS['wpdb']))->list();
    ?>
    <div class="wpm-row">
    <label for="mblar_letters_registration_template"><?php _e('Выберите Шаблон письма'); ?></label>
    <div>
        <select id="mblar_letters_registration_template"
                name="main_options[letters][registration][template_id]">
            <option value="">Без шаблона</option>
            <?php foreach ($templates as $template) : ?>
                <option value="<?= $template['id'] ?>"<?=
                ((int)$main_options['letters']['registration']['template_id'] ?? 0) === $template['id'] ? ' selected="selected"' : ''
                ?>><?= $template['name'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    </div><?php
}, 10, 2);

add_filter('wpm_send_mail_from_db', 'mblar_send_mail_from_db', 10, 3);
function mblar_send_mail_from_db($message_m, $message, $key) {
    file_put_contents(__DIR__ . '/log.txt', var_export([
        'template_id' => 'where'
    ], true), FILE_APPEND);
    if ($key === 'registration') {
        $template_id = wpm_get_option('letters.registration.template_id');
        if ($template_id) {
            $template = (new MailTemplates($GLOBALS['wpdb']))->byId($template_id);
            $message_m = mblar_render_email($template['data'], $message);
        }
    }
    return $message_m;
}