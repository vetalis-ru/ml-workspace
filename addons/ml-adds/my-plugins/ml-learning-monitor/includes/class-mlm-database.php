<?php
/**
 * Класс для работы с базой данных
 * 
 * @package ML_Learning_Monitor
 */

if (!defined('ABSPATH')) {
    exit;
}

class MLM_Database {
    /**
     * Запрос списка "сонь" из базы данных
     *
     * @param int    $term_id   ID термина
     * @param int    $page      Номер страницы
     * @param int    $per_page  Количество на странице
     * @param string $date_from Дата начала периода
     * @param string $date_to   Дата окончания периода
     * @return array
     */
    public function query_sleepers($term_id, $page, $per_page, $date_from, $date_to) {
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
}