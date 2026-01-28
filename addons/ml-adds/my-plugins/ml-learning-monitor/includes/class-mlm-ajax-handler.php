<?php
/**
 * Класс для обработки AJAX запросов
 * 
 * @package ML_Learning_Monitor
 */

if (!defined('ABSPATH')) {
    exit;
}

class MLM_Ajax_Handler {
    /**
     * Обработчик AJAX запроса для получения списка "сонь"
     */
    public function get_sleepers() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'forbidden'], 403);
        }

        $nonce = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '';
        if (!wp_verify_nonce($nonce, ML_Learning_Monitor::NONCE_ACTION)) {
            wp_send_json_error(['message' => 'bad_nonce'], 400);
        }

        $term_id = isset($_POST['term_id']) ? (int) $_POST['term_id'] : 0;
        $page    = isset($_POST['page']) ? max(1, (int) $_POST['page']) : 1;
        $today   = current_time('Y-m-d');
        $date_from = isset($_POST['date_from']) ? sanitize_text_field($_POST['date_from']) : '';
        $date_to = isset($_POST['date_to']) ? sanitize_text_field($_POST['date_to']) : '';
        $date_from = preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_from) ? $date_from : $today;
        $date_to = preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_to) ? $date_to : $today;

        if ($term_id <= 0) {
            wp_send_json_error(['message' => 'bad_term_id'], 400);
        }

        $database = new MLM_Database();
        $data = $database->query_sleepers($term_id, $page, ML_Learning_Monitor::PER_PAGE, $date_from, $date_to);

        $renderer = new MLM_Renderer();
        $html = $renderer->render_sleepers_table($data['rows'], $data['total'], $page, ML_Learning_Monitor::PER_PAGE, $term_id);

        wp_send_json_success([
            'html'  => $html,
            'total' => $data['total'],
            'page'  => $page,
        ]);
    }
}