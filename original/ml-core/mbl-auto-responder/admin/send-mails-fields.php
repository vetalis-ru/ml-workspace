<?php

use Mbl\AutoResponder\MailTemplates;

add_action('wpm_send_mails_fields', 'mblar_send_mails_fields');
function mblar_send_mails_fields()
{
    $templates = (new MailTemplates($GLOBALS['wpdb']))->list();
    ?>
    <tr>
    <th scope="row" valign="top"><label for="mblar_mail_template"><?php _e('Выберите Шаблон письма'); ?></label></th>
    <td>
        <select id="mblar_mail_template" name="template_id">
            <option value="">Без шаблона</option>
            <?php foreach ($templates as $template) : ?>
                <option value="<?= $template['id'] ?>"><?= $template['name'] ?></option>
            <?php endforeach; ?>
        </select>
    </td>
    </tr><?php
}

add_filter('wpm_send_mails_mail_content', 'mblar_send_mails_mail_content', 10, 1);
function mblar_send_mails_mail_content($mail_content)
{
    $content = $mail_content;
    if (!empty($_POST['template_id'])) {
        $template = (new MailTemplates($GLOBALS['wpdb']))->byId($_POST['template_id']);
        if ($template) {
            $content = mblar_render_email($template['data'], $content);
        }
    }

    return $content;
}

add_action('wpm_before_send_mails', 'mblar_before_send_mails');
function mblar_before_send_mails()
{
    add_filter('wpm_mailer_message', 'mblar_mailer_message', 10, 2);
}

add_action('wpm_after_send_mails', 'mblar_after_send_mails');
function mblar_after_send_mails()
{
    remove_filter('wpm_mailer_message', 'mblar_mailer_message');
}

function mblar_mailer_message($filterMessage, $message)
{
    if (!empty($_POST['template_id'])) {
        return $message;
    }
    return $filterMessage;
}

add_filter('tg_mblmail_raw_message', 'mblar_tg_message', 10, 1);
function mblar_tg_message($message) {
    return preg_replace('#<a [^>]*href="[^"]+/mblar-unsubscribe/[^"]+"[^>]*>[^<]+</a>#', '', $message);
}