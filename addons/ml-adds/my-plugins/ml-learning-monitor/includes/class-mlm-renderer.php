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
     * @param array  $rows      Массив данных пользователей
     * @param int    $total     Общее количество записей
     * @param int    $page      Текущая страница
     * @param int    $per_page  Количество на странице
     * @param int    $term_id   ID термина
     * @param string $sort      Поле для сортировки
     * @param string $order     Порядок сортировки (asc/desc)
     * @return string HTML код таблицы
     */
    public function render_sleepers_table($rows, $total, $page, $per_page, $term_id, $sort = 'user_id', $order = 'desc') {
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
        
        // КРИТИЧНО: все заголовки теперь кликабельны для сортировки
        echo $this->render_sortable_header('user_id', 'User ID', $sort, $order, $term_id, 'desc');
        echo $this->render_sortable_header('email', 'Email', $sort, $order, $term_id, 'asc');
        echo $this->render_sortable_header('first_name', 'Имя', $sort, $order, $term_id, 'asc');
        echo $this->render_sortable_header('last_name', 'Фамилия', $sort, $order, $term_id, 'asc');
        echo $this->render_sortable_header('last_issue_date', 'Последняя выдача УД', $sort, $order, $term_id, 'desc');
        echo $this->render_sortable_header('last_end_date', 'Окончание УД', $sort, $order, $term_id, 'desc');
        
        // Последний столбец без сортировки
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

        // ДОБАВЛЕНО: передача параметров сортировки в кнопки пагинации
        echo '<button type="button" class="button mlm-page-btn" data-term-id="' . esc_attr($term_id) . '" data-page="' . esc_attr($page - 1) . '" data-sort="' . esc_attr($sort) . '" data-order="' . esc_attr($order) . '"' . $prev_disabled . '>&laquo;</button>';
        echo '<span style="display:inline-block; padding:0 10px;">' . esc_html($page . ' / ' . $total_pages) . '</span>';
        echo '<button type="button" class="button mlm-page-btn" data-term-id="' . esc_attr($term_id) . '" data-page="' . esc_attr($page + 1) . '" data-sort="' . esc_attr($sort) . '" data-order="' . esc_attr($order) . '"' . $next_disabled . '>&raquo;</button>';

        echo '</div></div>';

        echo '</div>';

        return ob_get_clean();
    }
    
    /**
     * Рендер сортируемого заголовка таблицы
     *
     * @param string $field     Поле для сортировки
     * @param string $title     Заголовок столбца
     * @param string $current   Текущее поле сортировки
     * @param string $order     Текущий порядок сортировки
     * @param int    $term_id   ID термина
     * @param string $default   Порядок по умолчанию (asc/desc)
     * @return string HTML заголовка
     */
    private function render_sortable_header($field, $title, $current, $order, $term_id, $default = 'asc') {
        $class = '';
        $new_order = $default;
        
        // Если это текущее поле сортировки
        if ($field === $current) {
            $class = ' sorted ' . $order;
            // Определяем противоположный порядок
            $new_order = ($order === 'asc') ? 'desc' : 'asc';
        } else {
            // Если не текущее поле, используем порядок по умолчанию
            $new_order = $default;
        }
        
        return sprintf(
            '<th class="sortable%s"><a href="#" class="mlm-sort" data-sort="%s" data-order="%s" data-term-id="%d">%s</a></th>',
            $class,
            esc_attr($field),
            esc_attr($new_order),
            esc_attr($term_id),
            esc_html($title)
        );
    }
}