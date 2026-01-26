<?php
/**
 * Plugin Name: ML Learning Monitor
 * Description: Term-level (wpm-levels) мониторинг "сонь" и настройки писем-напоминаний.
 * Version: 0.1.0
 * Author: Vetalis
 */

if (!defined('ABSPATH')) {
    exit;
}

final class ML_Learning_Monitor {
    const TAXONOMY = 'wpm-levels';
    const NONCE_ACTION = 'mlm_ajax_nonce';
    const NONCE_NAME = 'mlm_nonce';
    const PER_PAGE = 20;

    public function __construct() {
        add_action(self::TAXONOMY . '_edit_form_fields', [$this, 'render_term_fields'], 20, 1);
        add_action('edited_' . self::TAXONOMY, [$this, 'save_term_fields'], 20, 2);

        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);

        add_action('wp_ajax_mlm_get_sleepers', [$this, 'ajax_get_sleepers']);
    }

    /**
     * Подключаем ассеты только на редактировании терма MemberLux (wpm-levels).
     */
    public function enqueue_assets($hook) {
        if ($hook !== 'term.php') {
            return;
        }

        $taxonomy = isset($_GET['taxonomy']) ? sanitize_text_field($_GET['taxonomy']) : '';
        if ($taxonomy !== self::TAXONOMY) {
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

        $base_url = plugin_dir_url(__FILE__);
        wp_enqueue_style('mlm-admin', $base_url . 'assets/mlm-admin.css', [], '0.1.0');
        wp_enqueue_script('mlm-admin', $base_url . 'assets/mlm-admin.js', ['jquery', 'jquery-ui-tabs'], '0.1.0', true);

        wp_localize_script('mlm-admin', 'MLM', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce(self::NONCE_ACTION),
            'perPage' => self::PER_PAGE,
        ]);
    }

    /**
     * Рендер в форме редактирования УД (терм таксономии wpm-levels).
     */
    public function render_term_fields($term) {
        $term_id = (int) $term->term_id;

        $enabled = (int) get_term_meta($term_id, 'mlm_enabled', true);

        echo '<tr class="form-field">';
        echo '<th scope="row"><label>' . esc_html__('ML Learning Monitor', 'mlm') . '</label></th>';
        echo '<td>';

        // Чекбокс включения.
        echo '<label style="display:inline-flex; gap:8px; align-items:center;">';
        echo '<input type="checkbox" id="mlm_enabled" name="mlm_enabled" value="1" ' . checked(1, $enabled, false) . ' />';
        echo '<span><strong>' . esc_html__('Включить мониторинг для этого УД', 'mlm') . '</strong></span>';
        echo '</label>';

        // Нонсы для сохранения.
        wp_nonce_field('mlm_save_term_' . $term_id, 'mlm_save_nonce');

        // Контейнер, который должен скрываться, если чекбокс выключен.
        echo '<div id="mlm_monitor_block" style="margin-top:16px;' . ($enabled ? '' : 'display:none;') . '">';

        // --- ТОЛЬКО вкладки писем ---
        echo '<div id="mlm_email_tabs" class="mlm-tabs">';

        echo '<ul>';
        echo '<li><a href="#mlm-email-1">' . esc_html__('Письмо 1', 'mlm') . '</a></li>';
        echo '<li><a href="#mlm-email-2">' . esc_html__('Письмо 2', 'mlm') . '</a></li>';
        echo '<li><a href="#mlm-email-3">' . esc_html__('Письмо 3', 'mlm') . '</a></li>';
        echo '<li><a href="#mlm-email-admin">' . esc_html__('Администратор', 'mlm') . '</a></li>';
        echo '</ul>';

        echo '<div id="mlm-email-1">';
        $this->render_student_email_fields($term_id, 1);
        echo '</div>';

        echo '<div id="mlm-email-2">';
        $this->render_student_email_fields($term_id, 2);
        echo '</div>';

        echo '<div id="mlm-email-3">';
        $this->render_student_email_fields($term_id, 3);
        echo '</div>';

        echo '<div id="mlm-email-admin">';
        $this->render_admin_email_fields($term_id);
        echo '</div>';

        echo '</div>'; // #mlm_email_tabs

        // --- Блок "Сони" НИЖЕ вкладок, таблица только по кнопке ---
        echo '<div class="mlm-sleepers" style="margin-top:16px;">';

        $today = current_time('Y-m-d');
        echo '<div class="mlm-sleepers-filters">';
        echo '<label for="mlm_sleepers_from">' . esc_html__('Срок истёк после', 'mlm') . '</label> ';
        echo '<input type="date" id="mlm_sleepers_from" name="mlm_sleepers_from" value="' . esc_attr($today) . '"> ';
        echo '<label for="mlm_sleepers_to" style="margin-left:8px;">' . esc_html__('и до', 'mlm') . '</label> ';
        echo '<input type="date" id="mlm_sleepers_to" name="mlm_sleepers_to" value="' . esc_attr($today) . '">';
        echo '</div>';

        echo '<button type="button" class="button button-primary" id="mlm_show_sleepers" data-term-id="' . esc_attr($term_id) . '">';
        echo esc_html__('Показать сонь', 'mlm');
        echo '</button>';

        echo '<span id="mlm_sleepers_status" class="mlm-status" style="margin-left:10px;"></span>';

        echo '<div id="mlm_sleepers_container" style="margin-top:12px;"></div>';

        echo '</div>'; // .mlm-sleepers

        echo '</div>'; // #mlm_monitor_block

        echo '</td>';
        echo '</tr>';
    }

    private function render_student_email_fields($term_id, $index) {
        $index = (int) $index;

        $days    = get_term_meta($term_id, "mlm_email_{$index}_days", true);
        $subject = get_term_meta($term_id, "mlm_email_{$index}_subject", true);
        $body    = get_term_meta($term_id, "mlm_email_{$index}_body", true);

        echo '<table class="form-table mlm-form-table"><tbody>';

        echo '<tr>';
        echo '<th><label for="mlm_email_' . esc_attr($index) . '_days">' . esc_html__('Дней после окончания', 'mlm') . '</label></th>';
        echo '<td><input type="number" min="0" step="1" class="regular-text" id="mlm_email_' . esc_attr($index) . '_days" name="mlm_email_' . esc_attr($index) . '_days" value="' . esc_attr($days) . '"></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<th><label for="mlm_email_' . esc_attr($index) . '_subject">' . esc_html__('Заголовок', 'mlm') . '</label></th>';
        echo '<td><input type="text" class="regular-text" id="mlm_email_' . esc_attr($index) . '_subject" name="mlm_email_' . esc_attr($index) . '_subject" value="' . esc_attr($subject) . '"></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<th><label for="mlm_email_' . esc_attr($index) . '_body">' . esc_html__('Тело письма', 'mlm') . '</label></th>';
        echo '<td><textarea class="large-text" rows="8" id="mlm_email_' . esc_attr($index) . '_body" name="mlm_email_' . esc_attr($index) . '_body">' . esc_textarea($body) . '</textarea></td>';
        echo '</tr>';

        echo '</tbody></table>';

        echo '<div class="mlm-shortcodes">';
        echo '<strong>' . esc_html__('Доступные шорткоды:', 'mlm') . '</strong> ';
        echo '<code>[user_email]</code>, <code>[user_login]</code>, <code>[course_name]</code>, <code>[expired_date]</code>, <code>[is_bundle_course]</code>, <code>[bundle_name]</code>';
        echo '</div>';
    }

    private function render_admin_email_fields($term_id) {
        $days_after_last = get_term_meta($term_id, 'mlm_admin_days_after_last', true);
        $admin_email     = get_term_meta($term_id, 'mlm_admin_email', true);
        $subject         = get_term_meta($term_id, 'mlm_admin_subject', true);
        $body            = get_term_meta($term_id, 'mlm_admin_body', true);

        echo '<table class="form-table mlm-form-table"><tbody>';

        echo '<tr>';
        echo '<th><label for="mlm_admin_days_after_last">' . esc_html__('Дней после последнего письма студенту', 'mlm') . '</label></th>';
        echo '<td><input type="number" min="0" step="1" class="regular-text" id="mlm_admin_days_after_last" name="mlm_admin_days_after_last" value="' . esc_attr($days_after_last) . '"></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<th><label for="mlm_admin_email">' . esc_html__('Email администратора', 'mlm') . '</label></th>';
        echo '<td><input type="email" class="regular-text" id="mlm_admin_email" name="mlm_admin_email" value="' . esc_attr($admin_email) . '"></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<th><label for="mlm_admin_subject">' . esc_html__('Заголовок', 'mlm') . '</label></th>';
        echo '<td><input type="text" class="regular-text" id="mlm_admin_subject" name="mlm_admin_subject" value="' . esc_attr($subject) . '"></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<th><label for="mlm_admin_body">' . esc_html__('Тело письма', 'mlm') . '</label></th>';
        echo '<td><textarea class="large-text" rows="8" id="mlm_admin_body" name="mlm_admin_body">' . esc_textarea($body) . '</textarea></td>';
        echo '</tr>';

        echo '</tbody></table>';

        echo '<div class="mlm-shortcodes">';
        echo '<strong>' . esc_html__('Доступные шорткоды:', 'mlm') . '</strong> ';
        echo '<code>[user_email]</code>, <code>[user_login]</code>, <code>[course_name]</code>, <code>[expired_date]</code>, <code>[is_bundle_course]</code>, <code>[bundle_name]</code>';
        echo '</div>';
    }

    public function save_term_fields($term_id, $tt_id) {
        $term_id = (int) $term_id;

        if (!current_user_can('manage_options')) {
            return;
        }

        if (!isset($_POST['mlm_save_nonce']) || !wp_verify_nonce(sanitize_text_field($_POST['mlm_save_nonce']), 'mlm_save_term_' . $term_id)) {
            return;
        }

        $enabled = isset($_POST['mlm_enabled']) ? 1 : 0;
        update_term_meta($term_id, 'mlm_enabled', $enabled);

        for ($i = 1; $i <= 3; $i++) {
            $days    = isset($_POST["mlm_email_{$i}_days"]) ? (int) $_POST["mlm_email_{$i}_days"] : '';
            $subject = isset($_POST["mlm_email_{$i}_subject"]) ? sanitize_text_field($_POST["mlm_email_{$i}_subject"]) : '';
            $body    = isset($_POST["mlm_email_{$i}_body"]) ? wp_kses_post($_POST["mlm_email_{$i}_body"]) : '';

            update_term_meta($term_id, "mlm_email_{$i}_days", $days);
            update_term_meta($term_id, "mlm_email_{$i}_subject", $subject);
            update_term_meta($term_id, "mlm_email_{$i}_body", $body);
        }

        $admin_days = isset($_POST['mlm_admin_days_after_last']) ? (int) $_POST['mlm_admin_days_after_last'] : '';
        $admin_email = isset($_POST['mlm_admin_email']) ? sanitize_email($_POST['mlm_admin_email']) : '';
        $admin_subject = isset($_POST['mlm_admin_subject']) ? sanitize_text_field($_POST['mlm_admin_subject']) : '';
        $admin_body = isset($_POST['mlm_admin_body']) ? wp_kses_post($_POST['mlm_admin_body']) : '';

        update_term_meta($term_id, 'mlm_admin_days_after_last', $admin_days);
        update_term_meta($term_id, 'mlm_admin_email', $admin_email);
        update_term_meta($term_id, 'mlm_admin_subject', $admin_subject);
        update_term_meta($term_id, 'mlm_admin_body', $admin_body);
    }

    public function ajax_get_sleepers() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'forbidden'], 403);
        }

        $nonce = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '';
        if (!wp_verify_nonce($nonce, self::NONCE_ACTION)) {
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

        $data = $this->query_sleepers($term_id, $page, self::PER_PAGE, $date_from, $date_to);

        $html = $this->render_sleepers_table_html($data['rows'], $data['total'], $page, self::PER_PAGE, $term_id);

        wp_send_json_success([
            'html'  => $html,
            'total' => $data['total'],
            'page'  => $page,
        ]);
    }

    private function query_sleepers($term_id, $page, $per_page, $date_from, $date_to) {
        global $wpdb;

        $offset = ($page - 1) * $per_page;

        $keys_table = $wpdb->prefix . 'memberlux_term_keys';
        $users_table = $wpdb->users;
        $usermeta_table = $wpdb->usermeta;

        $count_sql = "
            SELECT COUNT(1)
            FROM (
                SELECT k.user_id
                FROM {$keys_table} k
                INNER JOIN (
                    SELECT user_id, MAX(date_end) AS max_end
                    FROM {$keys_table}
                    WHERE term_id = %d
                    GROUP BY user_id
                ) lk ON lk.user_id = k.user_id AND lk.max_end = k.date_end
                WHERE k.term_id = %d
                  AND k.user_id IS NOT NULL
                  AND k.user_id > 0
                  AND k.is_banned = 0
                  AND k.is_unlimited = 0
                  AND k.date_end IS NOT NULL
                  AND k.date_end >= %s
                  AND k.date_end <= %s
                  AND NOT EXISTS (
                      SELECT 1
                      FROM {$keys_table} ka
                      WHERE ka.term_id = %d
                        AND ka.user_id = k.user_id
                        AND ka.is_banned = 0
                        AND (
                            ka.is_unlimited = 1
                            OR (ka.date_end IS NOT NULL AND ka.date_end >= %s)
                        )
                  )
            ) t
        ";

        $total = (int) $wpdb->get_var(
            $wpdb->prepare(
                $count_sql,
                $term_id,
                $term_id,
                $date_from,
                $date_to,
                $term_id,
                $date_to
            )
        );

        $rows_sql = "
            SELECT u.ID AS user_id,
                   u.user_email AS email,
                   um_first.meta_value AS first_name,
                   um_last.meta_value AS last_name,
                   s.last_issue_date,
                   s.last_end_date,
                   s.reminders_sent
            FROM {$users_table} u
            LEFT JOIN {$usermeta_table} um_first
              ON um_first.user_id = u.ID AND um_first.meta_key = 'first_name'
            LEFT JOIN {$usermeta_table} um_last
              ON um_last.user_id = u.ID AND um_last.meta_key = 'last_name'
            INNER JOIN (
                SELECT k.user_id,
                       MAX(k.date_registered) AS last_issue_date,
                       MAX(k.date_end) AS last_end_date,
                       0 AS reminders_sent
                FROM {$keys_table} k
                INNER JOIN (
                    SELECT user_id, MAX(date_end) AS max_end
                    FROM {$keys_table}
                    WHERE term_id = %d
                    GROUP BY user_id
                ) lk ON lk.user_id = k.user_id AND lk.max_end = k.date_end
                WHERE k.term_id = %d
                  AND k.user_id IS NOT NULL
                  AND k.user_id > 0
                  AND k.is_banned = 0
                  AND k.is_unlimited = 0
                  AND k.date_end IS NOT NULL
                  AND k.date_end >= %s
                  AND k.date_end <= %s
                  AND NOT EXISTS (
                      SELECT 1
                      FROM {$keys_table} ka
                      WHERE ka.term_id = %d
                        AND ka.user_id = k.user_id
                        AND ka.is_banned = 0
                        AND (
                            ka.is_unlimited = 1
                            OR (ka.date_end IS NOT NULL AND ka.date_end >= %s)
                        )
                  )
                GROUP BY k.user_id
            ) s ON s.user_id = u.ID
            ORDER BY u.ID DESC
            LIMIT %d OFFSET %d
        ";

        $rows = $wpdb->get_results(
            $wpdb->prepare(
                $rows_sql,
                $term_id,
                $term_id,
                $date_from,
                $date_to,
                $term_id,
                $date_to,
                $per_page,
                $offset
            ),
            ARRAY_A
        );

        return [
            'total' => $total,
            'rows'  => is_array($rows) ? $rows : [],
        ];
    }

    private function render_sleepers_table_html($rows, $total, $page, $per_page, $term_id) {
        $total_pages = max(1, (int) ceil($total / $per_page));

        ob_start();

        echo '<div class="mlm-sleepers-wrap">';

        if ($total <= 0) {
            echo '<div class="notice notice-info inline"><p>' . esc_html__('Сони не найдены.', 'mlm') . '</p></div>';
            echo '</div>';
            return ob_get_clean();
        }

        echo '<div class="mlm-sleepers-meta" style="margin:6px 0 10px 0;">';
        echo esc_html(sprintf('Найдено: %d. Страница %d из %d.', $total, $page, $total_pages));
        echo '</div>';

        echo '<table class="widefat striped">';
        echo '<thead><tr>';
        echo '<th>' . esc_html__('User ID', 'mlm') . '</th>';
        echo '<th>' . esc_html__('Email', 'mlm') . '</th>';
        echo '<th>' . esc_html__('Имя', 'mlm') . '</th>';
        echo '<th>' . esc_html__('Фамилия', 'mlm') . '</th>';
        echo '<th>' . esc_html__('Последняя выдача УД', 'mlm') . '</th>';
        echo '<th>' . esc_html__('Окончание УД', 'mlm') . '</th>';
        echo '<th>' . esc_html__('Напоминаний', 'mlm') . '<br>' . esc_html__('отправлено', 'mlm') . '</th>';
        echo '</tr></thead>';

        echo '<tbody>';
        foreach ($rows as $r) {
            $uid = isset($r['user_id']) ? (int) $r['user_id'] : 0;
            $email = isset($r['email']) ? $r['email'] : '';
            $first_name = isset($r['first_name']) ? $r['first_name'] : '';
            $last_name = isset($r['last_name']) ? $r['last_name'] : '';
            $last_issue = isset($r['last_issue_date']) ? $r['last_issue_date'] : '';
            $last_end = isset($r['last_end_date']) ? $r['last_end_date'] : '';
            $reminders_sent = isset($r['reminders_sent']) ? (int) $r['reminders_sent'] : 0;
            echo '<tr>';
            echo '<td>' . esc_html($uid) . '</td>';
            echo '<td>' . esc_html($email) . '</td>';
            echo '<td>' . esc_html($first_name) . '</td>';
            echo '<td>' . esc_html($last_name) . '</td>';
            echo '<td>' . esc_html($last_issue) . '</td>';
            echo '<td>' . esc_html($last_end) . '</td>';
            echo '<td>' . esc_html($reminders_sent) . '</td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';

        // Пагинация (стрелки, по 20).
        echo '<div class="tablenav" style="margin-top:10px;">';
        echo '<div class="tablenav-pages">';

        $prev_disabled = ($page <= 1) ? ' disabled' : '';
        $next_disabled = ($page >= $total_pages) ? ' disabled' : '';

        echo '<button type="button" class="button mlm-page-btn" data-term-id="' . esc_attr($term_id) . '" data-page="' . esc_attr($page - 1) . '"' . $prev_disabled . '>&laquo;</button>';
        echo '<span style="display:inline-block; padding:0 10px;">' . esc_html($page . ' / ' . $total_pages) . '</span>';
        echo '<button type="button" class="button mlm-page-btn" data-term-id="' . esc_attr($term_id) . '" data-page="' . esc_attr($page + 1) . '"' . $next_disabled . '>&raquo;</button>';

        echo '</div></div>';

        echo '</div>';

        return ob_get_clean();
    }
}

new ML_Learning_Monitor();
