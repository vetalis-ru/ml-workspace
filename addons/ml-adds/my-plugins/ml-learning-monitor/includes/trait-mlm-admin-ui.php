<?php
if (!defined('ABSPATH')) {
    exit;
}

trait MLM_Admin_UI {

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

        wp_enqueue_style(
            'jquery-ui-tabs',
            'https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css',
            [],
            '1.13.2'
        );

        wp_enqueue_style(
            'mlm-admin',
            MLM_PLUGIN_URL . 'assets/mlm-admin.css',
            [],
            MLM_VERSION
        );

        wp_enqueue_script(
            'mlm-admin',
            MLM_PLUGIN_URL . 'assets/mlm-admin.js',
            ['jquery', 'jquery-ui-tabs'],
            MLM_VERSION,
            true
        );

        wp_localize_script('mlm-admin', 'MLM', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce(self::NONCE_ACTION),
            'perPage' => self::PER_PAGE,
        ]);
    }

    /**
     * Рендер блока ML Learning Monitor в форме редактирования УД (wpm-levels).
     */
    public function render_term_fields($term) {
        $term_id = (int) $term->term_id;
        $enabled = (int) get_term_meta($term_id, 'mlm_enabled', true);

        echo '<tr class="form-field">';
        echo '<th scope="row"><label>' . esc_html__('ML Learning Monitor', 'mlm') . '</label></th>';
        echo '<td>';

        echo '<label style="display:inline-flex; gap:8px; align-items:center;">';
        echo '<input type="checkbox" id="mlm_enabled" name="mlm_enabled" value="1" ' . checked(1, $enabled, false) . ' />';
        echo '<span><strong>' . esc_html__('Включить мониторинг для этого УД', 'mlm') . '</strong></span>';
        echo '</label>';

        wp_nonce_field('mlm_save_term_' . $term_id, 'mlm_save_nonce');

        echo '<div id="mlm_monitor_block" style="margin-top:16px;' . ($enabled ? '' : 'display:none;') . '">';

        // Вкладки писем
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

        echo '</div>'; // tabs

        // Блок сонь (кнопка + контейнер)
        echo '<div class="mlm-sleepers" style="margin-top:16px;">';

        $today = current_time('Y-m-d');
        echo '<div class="mlm-sleepers-filters">';
        echo '<label>' . esc_html__('Срок истёк после', 'mlm') . '</label> ';
        echo '<input type="date" id="mlm_sleepers_from" value="' . esc_attr($today) . '"> ';
        echo '<label style="margin-left:8px;">' . esc_html__('и до', 'mlm') . '</label> ';
        echo '<input type="date" id="mlm_sleepers_to" value="' . esc_attr($today) . '">';
        echo '</div>';

        echo '<button type="button" class="button button-primary" id="mlm_show_sleepers" data-term-id="' . esc_attr($term_id) . '">';
        echo esc_html__('Показать сонь', 'mlm');
        echo '</button>';

        echo '<span id="mlm_sleepers_status" class="mlm-status" style="margin-left:10px;"></span>';
        echo '<div id="mlm_sleepers_container" style="margin-top:12px;"></div>';

        echo '</div>'; // sleepers

        echo '</div>'; // monitor_block
        echo '</td>';
        echo '</tr>';
    }

    private function render_student_email_fields($term_id, $index) {
        $days    = get_term_meta($term_id, "mlm_email_{$index}_days", true);
        $subject = get_term_meta($term_id, "mlm_email_{$index}_subject", true);
        $body    = get_term_meta($term_id, "mlm_email_{$index}_body", true);

        echo '<table class="form-table mlm-form-table"><tbody>';

        echo '<tr><th>' . esc_html__('Дней после окончания', 'mlm') . '</th>';
        echo '<td><input type="number" name="mlm_email_' . $index . '_days" value="' . esc_attr($days) . '"></td></tr>';

        echo '<tr><th>' . esc_html__('Заголовок', 'mlm') . '</th>';
        echo '<td><input type="text" name="mlm_email_' . $index . '_subject" value="' . esc_attr($subject) . '"></td></tr>';

        echo '<tr><th>' . esc_html__('Тело письма', 'mlm') . '</th>';
        echo '<td><textarea rows="8" name="mlm_email_' . $index . '_body">' . esc_textarea($body) . '</textarea></td></tr>';

        echo '</tbody></table>';
    }

    private function render_admin_email_fields($term_id) {
        $days    = get_term_meta($term_id, 'mlm_admin_days_after_last', true);
        $email   = get_term_meta($term_id, 'mlm_admin_email', true);
        $subject = get_term_meta($term_id, 'mlm_admin_subject', true);
        $body    = get_term_meta($term_id, 'mlm_admin_body', true);

        echo '<table class="form-table mlm-form-table"><tbody>';

        echo '<tr><th>' . esc_html__('Дней после последнего письма', 'mlm') . '</th>';
        echo '<td><input type="number" name="mlm_admin_days_after_last" value="' . esc_attr($days) . '"></td></tr>';

        echo '<tr><th>Email</th>';
        echo '<td><input type="email" name="mlm_admin_email" value="' . esc_attr($email) . '"></td></tr>';

        echo '<tr><th>' . esc_html__('Заголовок', 'mlm') . '</th>';
        echo '<td><input type="text" name="mlm_admin_subject" value="' . esc_attr($subject) . '"></td></tr>';

        echo '<tr><th>' . esc_html__('Тело письма', 'mlm') . '</th>';
        echo '<td><textarea rows="8" name="mlm_admin_body">' . esc_textarea($body) . '</textarea></td></tr>';

        echo '</tbody></table>';
    }

    public function save_term_fields($term_id, $tt_id) {
        if (!current_user_can('manage_options')) {
            return;
        }

        if (!isset($_POST['mlm_save_nonce']) || !wp_verify_nonce($_POST['mlm_save_nonce'], 'mlm_save_term_' . $term_id)) {
            return;
        }

        update_term_meta($term_id, 'mlm_enabled', isset($_POST['mlm_enabled']) ? 1 : 0);

        for ($i = 1; $i <= 3; $i++) {
            update_term_meta($term_id, "mlm_email_{$i}_days", (int) ($_POST["mlm_email_{$i}_days"] ?? 0));
            update_term_meta($term_id, "mlm_email_{$i}_subject", sanitize_text_field($_POST["mlm_email_{$i}_subject"] ?? ''));
            update_term_meta($term_id, "mlm_email_{$i}_body", wp_kses_post($_POST["mlm_email_{$i}_body"] ?? ''));
        }

        update_term_meta($term_id, 'mlm_admin_days_after_last', (int) ($_POST['mlm_admin_days_after_last'] ?? 0));
        update_term_meta($term_id, 'mlm_admin_email', sanitize_email($_POST['mlm_admin_email'] ?? ''));
        update_term_meta($term_id, 'mlm_admin_subject', sanitize_text_field($_POST['mlm_admin_subject'] ?? ''));
        update_term_meta($term_id, 'mlm_admin_body', wp_kses_post($_POST['mlm_admin_body'] ?? ''));
    }
}
