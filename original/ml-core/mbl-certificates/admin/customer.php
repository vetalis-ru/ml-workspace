<?php
function getUsersByWPMLevelId($wpmLevelID, $query = []): array
{
    global $wpdb;
    $dayAfterEndCourse = get_option('ml_day_after_course_end') ? get_option('ml_day_after_course_end') : 0;
    $baseSql = "SELECT *
        FROM `{$wpdb->prefix}memberlux_term_keys`
        WHERE term_id = $wpmLevelID AND user_id != 'NULL'
    ";
    $sql = $baseSql;
    if (!empty($query['active'])) {
        $sql .= " AND date_end >= CURDATE() - INTERVAL $dayAfterEndCourse DAY";
    }
    if (!empty($query['date_start'])) {
        $sql .= $wpdb->prepare(" AND date_start >= %s", $query['date_start']);
    }
    if (!empty($query['date_end'])) {
        $sql .= $wpdb->prepare(" AND date_start <= %s", $query['date_end']);
    }
    $memberLuxTermKeys = $wpdb->get_results($sql);
    $userIds = array_map(fn($memberLuxTermKey) => intval($memberLuxTermKey->user_id), $memberLuxTermKeys);
    $params = [
        'include' => $userIds,
    ];
    if (isset($_POST['orderby'])) {
        $params = array_merge($params, getOrderBy());
    }

    return !empty($userIds) ? get_users($params) : [];
}

function getOrderBy(): array
{
    if ($_POST['orderby'] === 'user_login') {
        return [
            'orderby' => $_POST['orderby'],
            'order' => $_POST['order']
        ];
    }
    return [
        'meta_query' => [
            'meta_field' => [
                'key' => $_POST['orderby']
            ]
        ],
        'orderby' => 'meta_field',
        'order' => $_POST['order']
    ];
}