<?php
/**
 * Класс для рендеринга и сохранения полей термина
 * 
 * @package ML_Learning_Monitor
 */

if (!defined('ABSPATH')) {
    exit;
}

class MLM_Term_Fields {
    /**
     * Рендер в форме редактирования УД (терм таксономии wpm-levels).
     *
     * @param WP_Term $term Объект термина
     */
    public function render($term) {
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

    /**
     * Сохранение полей термина
     *
     * @param int $term_id ID термина
     * @param int $tt_id   ID таксономии
     */
    public function save($term_id, $tt_id) {
        $term_id = (int) $term_id;

        if (!current_user_can('manage_options')) {
            return;
        }

        if (!isset($_POST['mlm_save_nonce']) || !wp_verify_nonce(sanitize_text_field($_POST['mlm_save_nonce']), 'mlm_save_term_' . $term_id)) {
            return;
        }

        $enabled = isset($_POST['mlm_enabled']) ? 1 : 0;
        update_term_meta($term_id, 'mlm_enabled', $enabled);

        // Сохранение полей писем делегируем классу MLM_Email_Fields
        $email_fields = new MLM_Email_Fields();
        $email_fields->save_student_fields($term_id);
        $email_fields->save_admin_fields($term_id);
    }

    /**
     * Рендер полей письма для студента
     * 
     * @param int $term_id ID термина
     * @param int $index   Номер письма (1, 2, 3)
     */
    private function render_student_email_fields($term_id, $index) {
        $email_fields = new MLM_Email_Fields();
        $email_fields->render_student($term_id, $index);
    }

    /**
     * Рендер полей письма для администратора
     * 
     * @param int $term_id ID термина
     */
    private function render_admin_email_fields($term_id) {
        $email_fields = new MLM_Email_Fields();
        $email_fields->render_admin($term_id);
    }
}