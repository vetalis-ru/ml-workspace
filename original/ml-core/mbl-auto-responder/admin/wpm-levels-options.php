<?php

use Mbl\AutoResponder\Mailing;
use Mbl\AutoResponder\Plugin;
use Mbl\AutoResponder\MailTemplates;

add_action('admin_enqueue_scripts', function ($hook_suffix) {
    if ($hook_suffix === 'term.php' && isset($_GET['taxonomy']) && $_GET['taxonomy'] === 'wpm-levels') {
        global $wpdb;
        $plugin = new Plugin();
        $term_id = (int)$_GET['tag_ID'];
        $templates = (new MailTemplates($GLOBALS['wpdb']))->list();
        $mailing = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$wpdb->prefix}memberlux_mailing_list WHERE term_id = %d", [$term_id]),
            ARRAY_A
        );
        if (!$mailing) {
            $mailing = [
                'term_id' => $term_id,
                'is_on' => 0,
                'datetime' => null,
                'template_id' => !empty($templates) ? $templates[0]['id'] : 0,
                'unsubscribe' => 0,
            ];
        } else {
            $mailing = [
                'term_id' => (int)$mailing['term_id'],
                'is_on' => (int)$mailing['is_on'],
                'datetime' => $mailing['datetime'],
                'template_id' => (int)$mailing['template_id'],
                'unsubscribe' => (int)$mailing['unsubscribe'],
            ];
        }
        wp_enqueue_editor();
        wp_localize_script('mblar-admin', '__mblar_data__', $plugin->js_data([
            'state' => [
                'templates' => $templates,
                'mails' => (new Mailing($term_id))->toArray(),
                'isOn' => $mailing['is_on'],
                'datetime' => $mailing['datetime'],
                'templateId' => $mailing['template_id'],
                'unsubscribe' => $mailing['unsubscribe'],
            ]
        ]));
        wp_enqueue_script('mblar-admin');
    }
}, 20);