<?php
/**
 * Класс для управления ассетами (скриптами и стилями)
 * 
 * @package ML_Learning_Monitor
 */

if (!defined('ABSPATH')) {
    exit;
}

class MLM_Assets {
    /**
     * Подключаем ассеты только на редактировании терма MemberLux (wpm-levels).
     *
     * @param string $hook Текущий хук админки
     */
    public function enqueue($hook) {
        if ($hook !== 'term.php') {
            return;
        }

        $taxonomy = isset($_GET['taxonomy']) ? sanitize_text_field($_GET['taxonomy']) : '';
        if ($taxonomy !== ML_Learning_Monitor::TAXONOMY) {
            return;
        }

        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-ui-tabs');

        // CSS для jQuery UI Tabs, иначе будет "список ссылок".
        wp_enqueue_style(
            'jquery-ui-tabs',
            'https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css',
            [],
            '1.13.2'
        );

        $base_url = plugin_dir_url(dirname(__FILE__));
        wp_enqueue_style('mlm-admin', $base_url . 'assets/mlm-admin.css', [], '0.1.0');
        wp_enqueue_script('mlm-admin', $base_url . 'assets/mlm-admin.js', ['jquery', 'jquery-ui-tabs'], '0.1.0', true);

        wp_localize_script('mlm-admin', 'MLM', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce(ML_Learning_Monitor::NONCE_ACTION),
            'perPage' => ML_Learning_Monitor::PER_PAGE,
        ]);
    }
}