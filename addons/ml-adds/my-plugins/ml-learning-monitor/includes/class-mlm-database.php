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
    public function query_sleepers($term_id, $page, $per_page, $date_from, $date_to, $sort = 'user_id', $order = 'DESC') {
        global $wpdb;

        $offset = ($page - 1) * $per_page;

        $keys_table   = $wpdb->prefix . 'memberlux_term_keys';
        $users_table  = $wpdb->users;
        $usermeta_table = $wpdb->usermeta;
        $certs_table  = $wpdb->prefix . 'memberlux_certificate';

        // --- СОРТИРОВКА (оставляем как есть, даже если временно отключена в UI) ---
        $allowed_sort = [
            'user_id'         => 'u.ID',
            'email'           => 'u.user_email',
            'first_name'      => 'um_first.meta_value',
            'last_name'       => 'um_last.meta_value',
            'last_issue_date' => 's.last_issue_date',
            'last_end_date'   => 's.last_end_date',
        ];

        $sort = is_string($sort) ? $sort : 'user_id';
        $sort_sql = $allowed_sort[$sort] ?? $allowed_sort['user_id'];

        $order = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC';

        /**
         * =========================
         * COUNT QUERY (TOTAL)
         * =========================
         */
        $count_sql = "
            SELECT COUNT(*)
            FROM wp_users u
            INNER JOIN (
              SELECT user_id, MAX(date_end) AS last_date_end
              FROM wp_memberlux_term_keys
              WHERE term_id = %d
                AND is_banned = 0
              GROUP BY user_id
            ) lk ON lk.user_id = u.ID
            LEFT JOIN wp_memberlux_certificate c
              ON c.user_id = u.ID
             AND c.wpmlevel_id = %d
            WHERE u.user_status = 0
              AND c.certificate_id IS NULL
              AND lk.last_date_end BETWEEN %s AND %s
              AND NOT EXISTS (
                SELECT 1
                FROM wp_memberlux_term_keys k2
                WHERE k2.user_id = u.ID
                  AND k2.term_id = %d
                  AND k2.is_banned = 0
                  AND (
                    k2.is_unlimited = 1
                    OR k2.date_end > lk.last_date_end
                  )
              )";
              
        $total = (int) $wpdb->get_var(
            $wpdb->prepare(
                $count_sql,
                $term_id,   // s.term_id
                $term_id,   // c.wpmlevel_id
                $date_from, // date_from
                $date_to,   // date_to
                $term_id    // k2.term_id
            )
        );

        /**
         * =========================
         * ROWS QUERY
         * =========================
         */
        $rows_sql = "
            SELECT
                u.ID                         AS user_id,
                u.user_email                 AS email,
                um_fn.meta_value             AS first_name,
                um_ln.meta_value             AS last_name,
                k_last.date_start            AS last_date_start,
                lk.last_date_end             AS last_date_end,
                0                            AS reminders_sent
            FROM wp_users u
            INNER JOIN (
                SELECT
                    user_id,
                    MAX(date_end) AS last_date_end
                FROM wp_memberlux_term_keys
                WHERE term_id = %d
                  AND is_banned = 0
                GROUP BY user_id
            ) lk
                ON lk.user_id = u.ID

            -- привязываем ИМЕННО тот ключ, который соответствует last_date_end
            INNER JOIN wp_memberlux_term_keys k_last
                ON k_last.user_id = u.ID
               AND k_last.term_id = %d
               AND k_last.is_banned = 0
               AND k_last.date_end = lk.last_date_end

            LEFT JOIN wp_usermeta um_fn
                ON um_fn.user_id = u.ID
               AND um_fn.meta_key = 'first_name'
            LEFT JOIN wp_usermeta um_ln
                ON um_ln.user_id = u.ID
               AND um_ln.meta_key = 'last_name'

            LEFT JOIN wp_memberlux_certificate c
                ON c.user_id = u.ID
               AND c.wpmlevel_id = %d

            WHERE
                u.user_status = 0
                AND c.certificate_id IS NULL
                AND lk.last_date_end BETWEEN %s AND %s
                AND NOT EXISTS (
                    SELECT 1
                    FROM wp_memberlux_term_keys k2
                    WHERE k2.user_id = u.ID
                      AND k2.term_id = %d
                      AND k2.is_banned = 0
                      AND (
                          k2.is_unlimited = 1
                          OR k2.date_end > lk.last_date_end
                      )
                )

            ORDER BY lk.last_date_end ASC
            LIMIT %d OFFSET %d;

        ";

         $rows = $wpdb->get_results(
            $wpdb->prepare(
                $rows_sql,
                $term_id,   // lk.term_id
                $term_id,   // k_last.term_id
                $term_id,   // c.wpmlevel_id  ← ВАЖНО: этого не хватало
                $date_from, // date_from
                $date_to,   // date_to
                $term_id,   // k2.term_id
                $per_page,  // LIMIT
                $offset     // OFFSET
            ),
            ARRAY_A
        );

        return [
            'total' => $total,
            'rows'  => is_array($rows) ? $rows : [],
        ];
    }

}
