<?php
/**
 * Класс для рендеринга и сохранения полей email
 * 
 * @package ML_Learning_Monitor
 */

if (!defined('ABSPATH')) {
    exit;
}

class MLM_Email_Fields {
    /**
     * Рендер полей письма для студента
     * 
     * @param int $term_id ID термина
     * @param int $index   Номер письма (1, 2, 3)
     */
    public function render_student($term_id, $index) {
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

    /**
     * Рендер полей письма для администратора
     * 
     * @param int $term_id ID термина
     */
    public function render_admin($term_id) {
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

    /**
     * Сохранение полей писем для студента
     * 
     * @param int $term_id ID термина
     */
    public function save_student_fields($term_id) {
        for ($i = 1; $i <= 3; $i++) {
            $days    = isset($_POST["mlm_email_{$i}_days"]) ? (int) $_POST["mlm_email_{$i}_days"] : '';
            $subject = isset($_POST["mlm_email_{$i}_subject"]) ? sanitize_text_field($_POST["mlm_email_{$i}_subject"]) : '';
            $body    = isset($_POST["mlm_email_{$i}_body"]) ? wp_kses_post($_POST["mlm_email_{$i}_body"]) : '';

            update_term_meta($term_id, "mlm_email_{$i}_days", $days);
            update_term_meta($term_id, "mlm_email_{$i}_subject", $subject);
            update_term_meta($term_id, "mlm_email_{$i}_body", $body);
        }
    }

    /**
     * Сохранение полей письма для администратора
     * 
     * @param int $term_id ID термина
     */
    public function save_admin_fields($term_id) {
        $admin_days = isset($_POST['mlm_admin_days_after_last']) ? (int) $_POST['mlm_admin_days_after_last'] : '';
        $admin_email = isset($_POST['mlm_admin_email']) ? sanitize_email($_POST['mlm_admin_email']) : '';
        $admin_subject = isset($_POST['mlm_admin_subject']) ? sanitize_text_field($_POST['mlm_admin_subject']) : '';
        $admin_body = isset($_POST['mlm_admin_body']) ? wp_kses_post($_POST['mlm_admin_body']) : '';

        update_term_meta($term_id, 'mlm_admin_days_after_last', $admin_days);
        update_term_meta($term_id, 'mlm_admin_email', $admin_email);
        update_term_meta($term_id, 'mlm_admin_subject', $admin_subject);
        update_term_meta($term_id, 'mlm_admin_body', $admin_body);
    }
}