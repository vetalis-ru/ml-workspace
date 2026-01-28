<?php
/**
 * Класс для рендеринга HTML
 * 
 * @package ML_Learning_Monitor
 */

if (!defined('ABSPATH')) {
    exit;
}

class MLM_Renderer {
    /**
     * Рендер HTML таблицы "сонь"
     *
     * @param array $rows      Массив данных пользователей
     * @param int   $total     Общее количество записей
     * @param int   $page      Текущая страница
     * @param int   $per_page  Количество на странице
     * @param int   $term_id   ID термина
     * @return string HTML код таблицы
     */
    public function render_sleepers_table($rows, $total, $page, $per_page, $term_id) {
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