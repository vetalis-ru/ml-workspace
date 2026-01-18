<?php

class MBLHomeworkAdmin
{
    public static function statsMaterials()
    {
        $result = [];
        $isCoach = wpm_is_coach();

        add_filter('wp_query_search_exclusion_prefix', function ($prefix) {
            return false;
        });

        add_filter('posts_search', ['MBLHomeworkAdmin', 'search_by_title_only_prv' ], 500, 2);

        $args = [
            's'              => wpm_array_get($_GET, 'q'),
            'post_status'    => 'publish',
            'post_type'      => 'wpm-page',
            'posts_per_page' => 50,
            'orderby'        => 'menu_order title',
            'order'          => 'ASC',
        ];

        if ($isCoach) {
            $categoryIds = apply_filters('wpm_homework_filters_wpm_category_ids', get_terms('wpm-category', ['hide_empty' => 0, 'fields' => 'ids']));

            $args['tax_query'] = [
                [
                    'taxonomy' => 'wpm-category',
                    'field'    => 'term_id',
                    'terms'    => $categoryIds,
                ],
            ];
        }

        $search_results = new WP_Query($args);

        if ($search_results->have_posts()) {
            while ($search_results->have_posts()) {
                $search_results->the_post();
                $title = (mb_strlen($search_results->post->post_title) > 50) ? mb_substr($search_results->post->post_title, 0, 49) . '...' : $search_results->post->post_title;
                $result[] = [$search_results->post->ID, $title];
            }
        }

        if ($isCoach) {
            //TODO add filter by allowed homework type
        }

        echo json_encode($result);
        die;
    }

    public static function search_by_title_only_prv( $search, &$wp_query)
    {
        global $wpdb;
        if (empty($search)) {
            return $search;
        }
        $q = $wp_query->query_vars;
        $n = !empty($q['exact']) ? '' : '%';
        $search =
        $searchand = '';
        foreach ((array)$q['search_terms'] as $term) {
            $term = esc_sql($wpdb->esc_like($term));
            $search .= "{$searchand}($wpdb->posts.post_title LIKE '{$n}{$term}{$n}')";
            $searchand = ' AND ';
        }
        if (!empty($search)) {
            $search = " AND ({$search}) ";
            if (!is_user_logged_in()) {
                $search .= " AND ($wpdb->posts.post_password = '') ";
            }
        }
        return $search;
    }

    public static function ajaxListContent()
    {
        self::listContent();
        die();
    }

    public static function listContent()
    {
        global $wpdb;

        $filters = [
            'wpm-category' => wpm_array_get($_POST, 'wpm-category', ''),
            'wpm-levels'   => wpm_array_get($_POST, 'wpm-levels', ''),
            'search'       => wpm_array_get($_POST, 's', ''),
            'type'         => wpm_array_get($_POST, 'type', ''),
            'status'       => wpm_array_get($_POST, 'status', 'opened'),
            'material'     => wpm_array_get($_POST, 'material', ''),
            'date_from'    => wpm_array_get($_POST, 'date_from', date('d.m.Y', current_time('timestamp') - 7*24*60*60)),
            'date_to'      => wpm_array_get($_POST, 'date_to', date('d.m.Y', current_time('timestamp'))),
        ];

        $orderBy = wpm_array_get($_POST, 'order_by', 'date');
        $order = wpm_array_get($_POST, 'order', 'desc') == 'asc' ? 'ASC' : 'DESC';

        $response_table = $wpdb->prefix . "memberlux_responses";
        $response_review_table = $wpdb->prefix . "memberlux_response_review";
        $condition = ' 1=1';

        switch ($filters['status']) {
            case 'approved':
                $statusCondition = " AND `response_status` IN ('approved','accepted') AND `is_archived`=0";
                break;
            case 'rejected':
                $statusCondition = " AND `response_status`='rejected' AND `is_archived`=0";
                break;
            case 'trash':
                $statusCondition = " AND `is_archived`=1";
                break;
            default:
                $statusCondition = " AND `response_status`='opened' AND `is_archived`=0";
        }

        if ($filters['wpm-category'] !== '') {
            $condition .= self::_homeworkStatsFilterByTermsCondition($filters['wpm-category']);
        }

        if ($filters['wpm-levels'] !== '') {
            $condition .= self::_homeworkStatsFilterByTermsCondition($filters['wpm-levels'], 'wpm-levels');
        }

        if ($filters['type'] !== '') {
            if ($filters['type'] == 'test') {
                $metaQuery = [
                    [
                        'key'   => '_wpm_page_meta',
                        'value' => 's:13:"homework_type";s:4:"test"',
                        'compare' => 'LIKE'
                    ],
                ];
            } else {
                $metaQuery = [
                    [
                        'key'   => '_wpm_page_meta',
                        'value' => 's:13:"homework_type";s:4:"test"',
                        'compare' => 'NOT LIKE'
                    ],
                ];
            }

            $_postIds = get_posts([
                    'post_type'      => 'wpm-page',
                    'posts_per_page' => -1,
                    'fields'         => 'ids',
                    'meta_query'     => $metaQuery,
                ]
            );

            if (!empty($_postIds)) {
                $condition .= " AND post_id IN (" . implode(',', $_postIds) . ") ";
            } else {
                $condition .= " AND 1<>1";
            }
        }

        if (wpm_option_is('hw.autotrainings_only', 'on')) {
            $_categories = get_terms('wpm-category', ['hide_empty' => 0, "fields" => "ids"]);
            if (!is_array($_categories)) {
                $_categories = [];
            }
            $_categories = array_filter($_categories, function ($categoryId) {
                return wpm_is_autotraining($categoryId);
            });

            $__postIds = get_posts([
                    'post_type'      => 'wpm-page',
                    'posts_per_page' => -1,
                    'fields'         => 'ids',
                    'tax_query'      => [
                        [
                            'taxonomy' => 'wpm-category',
                            'field'    => 'term_id',
                            'terms'    => $_categories,
                        ],
                    ],
                ]
            );

            if (!empty($__postIds)) {
                $condition .= " AND post_id IN (" . implode(',', $__postIds) . ") ";
            } else {
                $condition .= " AND 1<>1";
            }
        }

        if ($filters['material'] !== '') {
            $condition .= sprintf(' AND post_id=%d', intval($filters['material']));
        }

        if ($filters['date_from'] !== '') {
            $dateFrom = date_create_from_format('d.m.Y', $filters['date_from']);
            $condition .= sprintf(" AND DATE(response_date) >= '%s'", $dateFrom->format('Y-m-d'));
        }

        if ($filters['date_to'] !== '') {
            $dateTo = date_create_from_format('d.m.Y', $filters['date_to']);
            $condition .= sprintf(" AND DATE(response_date) <= '%s'", $dateTo->format('Y-m-d'));
        }

        $join = '';

        if ($filters['search'] !== '') {
            $userTerm = esc_sql($filters['search']);
            $userByTermKey = MBLTermKeysQuery::getUserByTermKey($userTerm);

            if($userByTermKey) {
                $condition .= " AND user_id=" . $userByTermKey->ID;
            } else {
                $usersTable = $wpdb->prefix . "users";
                $userMetaTable = $wpdb->prefix . "usermeta";

                $join .= " JOIN $usersTable AS b ON user_id=b.ID ";
                $userMetaWhere = "EXISTS (SELECT um.umeta_id FROM {$userMetaTable} um WHERE b.ID = um.user_id AND um.meta_key IN ('first_name', 'last_name') AND um.meta_value LIKE '%%%s%%')";
                $userWhere = " AND (b.display_name LIKE '%%%s%%' OR b.user_email LIKE '%%%s%%' OR b.user_login LIKE '%%%s%%' OR {$userMetaWhere})";
                $condition .= sprintf($userWhere, $userTerm, $userTerm, $userTerm, $userTerm);
            }
        }

        $reviewsQuery = "(SELECT COUNT(rr.id) FROM {$response_review_table} rr WHERE rr.response_id=r.id)";

        if ($orderBy == 'date') {
            $sortBy = "ORDER BY `response_date` {$order}";
        } elseif ($orderBy == 'comment') {
            $sortBy = "ORDER BY {$reviewsQuery} {$order}, `response_date` DESC";
        } else {
            $sortBy = "ORDER BY `response_date` DESC";
        }

        $condition = apply_filters('wpm_admin_hw_condition', $condition);

        $responses = $wpdb->get_results("SELECT *, {$reviewsQuery} as reviews_nb 
                                           FROM {$response_table} r
                                           {$join}
                                           WHERE {$condition}
                                           {$statusCondition}
                                           {$sortBy}", OBJECT);

        if (!is_array($responses)) {
            $responses = [];
        }

        $responses = array_map(function ($response) {
            $response->mblPage = new MBLPage(get_post($response->post_id));

            return $response;
        }, $responses);

        if (in_array($orderBy, ['type', 'category', 'material', 'level'])) {
            usort($responses, function ($a, $b) use ($orderBy, $order) {
                switch ($orderBy) {
                    case 'type':
                        $aType = intval($a->mblPage->getMeta('homework_type') == 'test');
                        $bType = intval($b->mblPage->getMeta('homework_type') == 'test');
                        return $order == 'ASC' ? ($aType - $bType) : ($bType - $aType);

                    case 'category':
                        $val = strcmp($a->mblPage->getAutotraining()->getName(), $b->mblPage->getAutotraining()->getName());
                        return $order == 'ASC' ? $val : -$val;

                    case 'material':
                        $val = strcmp($a->mblPage->getTitle(), $b->mblPage->getTitle());
                        return $order == 'ASC' ? $val : -$val;

                    case 'level':
                        $val = strcmp(implode('', $a->mblPage->getAccessLevelNames()), implode('', $b->mblPage->getAccessLevelNames()));
                        return $order == 'ASC' ? $val : -$val;
                }

                return 0;
            });
        }

        $responses = apply_filters('wpm_admin_hw_responses', $responses);

        $responsesNb = $wpdb->get_results("SELECT COUNT(r.`id`) as nb, `response_status`
                                           FROM {$response_table} r
                                           {$join}
                                           WHERE {$condition}
                                           AND `is_archived`=0
                                           GROUP BY `response_status`", ARRAY_A);

        $archivedNb = $wpdb->get_results("SELECT COUNT(r.`id`) as nb
                                           FROM {$response_table} r
                                           {$join}
                                           WHERE {$condition}
                                           AND `is_archived`=1", ARRAY_A);

        $statsRaw = [];

        foreach ($responsesNb as $data) {
            $statsRaw[$data['response_status']] = $data['nb'];
        }

        $stats = [
            'opened'   => wpm_array_get($statsRaw, 'opened', 0),
            'approved' => (wpm_array_get($statsRaw, 'approved', 0) + wpm_array_get($statsRaw, 'accepted', 0)),
            'rejected' => wpm_array_get($statsRaw, 'rejected', 0),
            'trash'    => wpm_array_get($archivedNb, '0.nb', 0),
        ];

        $statuses = self::getActiveStatuses();

        if ($filters['status'] !== 'trash') {
            if (isset($statuses[$filters['status']])) {
                unset($statuses[$filters['status']]);
            }
        } else {
            $statuses = self::getArchivedStatuses();
        }

        $colspan = self::_colspan();
        $curStatus = $filters['status'];

        wpm_render_partial('homework/list-content', 'admin', compact('responses', 'stats', 'statuses', 'colspan', 'curStatus'));
    }

    private static function _homeworkStatsFilterByTermsCondition($terms, $tax = 'wpm-category', $field = 'slug')
    {
        $_postIds = get_posts([
                'post_type'      => 'wpm-page',
                'posts_per_page' => -1,
                'fields'         => 'ids',
                'tax_query'      => [
                    [
                        'taxonomy' => $tax,
                        'field'    => $field,
                        'terms'    => $terms,
                    ],
                ],
            ]
        );

        if (!empty($_postIds)) {
            $condition = " AND post_id IN (" . implode(',', $_postIds) . ") ";
        } else {
            $condition = " AND 1<>1";
        }

        return $condition;
    }

    public static function itemContent()
    {
        global $wpdb;

        $response_table = $wpdb->prefix . "memberlux_responses";
        $id = wpm_array_get($_POST, 'response_id');

        $response = $wpdb->get_row(sprintf("SELECT * FROM `%s` WHERE `id` = %d;", $response_table, $id), OBJECT);

        $response->mblPage = new MBLPage(get_post($response->post_id));

        $statuses = self::getActiveStatuses();

        if (!$response->is_archived) {
            if (isset($statuses[$response->response_status])) {
                unset($statuses[$response->response_status]);
            }

            if ($response->response_status == 'accepted') {
                unset($statuses['approved']);
            }
        } else {
            $statuses = self::getArchivedStatuses();
        }

        wpm_render_partial('homework/item-content', 'admin', compact('response', 'statuses'));
        die();
    }

    /**
     * @return array
     */
    private static function getActiveStatuses()
    {
        return [
            'approved' => __('Одобрить', 'mbl_admin'),
            'opened'   => __('На рассмотрение', 'mbl_admin'),
            'rejected' => __('Отклонить', 'mbl_admin'),
            'archive'  => __('Архивировать', 'mbl_admin'),
        ];
    }

    /**
     * @return array
     */
    private static function getArchivedStatuses()
    {
        return [
            'unarchive' => __('Восстановить', 'mbl_admin'),
            'delete'    => __('Удалить', 'mbl_admin'),
        ];
    }

    public function render()
    {
        $this->saveOptions();

        wpm_enqueue_style('homework_response_css', plugins_url('/member-luxe/css/homework-response.css'));
        wpm_enqueue_style('wpm-fancybox', plugins_url('/member-luxe/2_0/fancybox/jquery.fancybox.min.css?v=' . WP_MEMBERSHIP_VERSION));

        $categories = apply_filters('wpm_homework_filters_wpm-category', get_terms('wpm-category', ['hide_empty' => 0]));
        $levels = get_terms('wpm-levels', ['hide_empty' => 0]);

        wpm_render_partial('homework/layout', 'admin', compact('categories', 'levels'));
    }

    private function saveOptions()
    {
        $options = wpm_array_get($_POST, 'main_options', []);
        if (count($options)) {
            $this->_setOptions($options);
        }
    }

    private function _updateOption($key, $value)
    {
        $main_options = get_option('wpm_main_options');

        if (!isset($main_options) || !is_array($main_options)) {
            $main_options = [];
        }

        update_option('wpm_main_options', wpm_array_set($main_options, $key, $value));
    }

    private function _setOptions($options)
    {
        foreach ($options as $key => $value) {
            self::_setOption($key, $value);
        }
    }

    private function _setOption($key, $value)
    {
        if (is_array($value)) {
            foreach ($value as $_key => $_value) {
                $this->_setOption($key . '.' . $_key, $_value);
            }
        } else {
            $this->_updateOption($key, $value);
        }
    }

    private static function _colspan()
    {
        $fields = ['type', 'date', 'comments', 'categories', 'materials', 'levels'];
        $i = 0;

        foreach ($fields as $field) {
            if(wpm_option_is('hw.enabled_fields.'.$field, 'on', 'on')) {
                $i++;
            }
        }

        return $i + 3;
    }
}
