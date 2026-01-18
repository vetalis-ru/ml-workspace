<?php

function getCategoryByWPMLevel($wpmLevelId)
{
    $lesson = get_posts([
        'post_type' => 'wpm-page',
        'numberposts' => 1,
        'suppress_filters' => true,
        'tax_query' => [
            [
                'taxonomy' => 'wpm-levels',
                'field' => 'id',
                'terms' => $wpmLevelId
            ]
        ]
    ]);
    return wp_get_object_terms($lesson[0]->ID, 'wpm-category')[0];
}

function customerHasCertificate($userId, $wpmlevel_id): bool
{
    global $wpdb;
    $sql = $wpdb->prepare(
        "SELECT certificate_id FROM `{$wpdb->prefix}memberlux_certificate`
              WHERE `wpmlevel_id` = %d AND user_id = %d",
        $wpmlevel_id, $userId
    );

    return !empty($wpdb->get_var($sql));
}

add_action('wp_ajax_ml_select_user', function () {
    //    $levelId = (int)get_post_meta($productId, '_mbl_key_pin_code_level_id', true);
    $users = [];
    $user = get_user_by('ID', get_current_user_id());
    $isCoach = in_array('coach', $user->roles);
    $levelId = intval($_POST['wpm_level']);
    $query = [];
    if (!empty($_POST['date_start'])) {
        $query['date_start'] = date('Y-m-d', strtotime($_POST['date_start']));
    }
    if (!empty($_POST['date_end'])) {
        $query['date_end'] = date('Y-m-d', strtotime($_POST['date_end']));
    }
    $query['active'] = isset($_POST['active_wpmlevel']);
    if (empty($_POST['user_email'])) {
        $users = array_values(
            array_filter(
                getUsersByWPMLevelId($levelId, $query),
                fn($userObj) => !customerHasCertificate($userObj->ID, $levelId)
            )
        );
    } else {
        $user = get_user_by('email', $_POST['user_email']);
        if ($user) {
            $users[] = $user;
            $link = get_edit_user_link($user->ID);
            if (customerHasCertificate($user->ID, $levelId)) {
                die(json_encode(['html' =>
                    "<tr>
                        <td colspan=\"5\">
                            По данному курсу у пользователя <a href='$link' target='_blank'>
                            {$user->user_login}</a> уже есть сертификат
                        </td>
                    </tr>",
                    'count' => 0,
                ]));
            }
        }
    }
    if (empty($users)) {
        die(json_encode([
            'html' => '<tr><td colspan="5">Не результатов по запросу</td></tr>',
            'count' => 0,
        ]));
    }

    ob_start();
    foreach ($users as $userObj):
        $user = $userObj->data;
        $userId = intval($user->ID);
        $last_name = get_user_meta($userId, 'last_name', true);
        $first_name = get_user_meta($userId, 'first_name', true);
        $isDisabled = empty($last_name) || empty($first_name);
        ?>
        <tr id="user-<?= $userId ?>" <?= $isDisabled ? 'style="background-color: #fafafa;"' : ''; ?> >
            <th scope="row" class="check-column">
                <label class="screen-reader-text" for="user_<?= $userId ?>">Выбрать <?= $user->user_login; ?></label>
                <input type="checkbox"
                       name="users[]"
                       id="user_<?= $userId ?>"
                       value="<?= $userId ?>">
            </th>
            <td class="username column-username has-row-actions column-primary">
                <?php if ($isCoach): ?>
                    <?= $user->user_login; ?>
                <?php else: ?>
                    <a href='<?= get_edit_user_link($userId); ?>' target="_blank">
                        <?= $user->user_login; ?>
                    </a>
                <?php endif ?>
            </td>
            <td class="name column-name">
                <!-- strong -->
                <?= $last_name; ?>
                <!-- /strong -->
            </td>
            <td class="name column-name">
                <!-- strong -->
                <?= $first_name; ?>
                <!-- /strong -->
            </td>
            <td class="name column-name">
                <!-- strong -->
                <?= get_user_meta($userId, 'surname', true); ?>
                <!-- /strong -->
            </td>
        </tr>
    <?php
    endforeach;
    echo json_encode([
        'html' => (new MBLCCompressedHTML(ob_get_clean()))->__toString(),
        'count' => count($users),
    ]);
    wp_die();
});

add_action('wp_ajax_ml_new_certificate_template', function () {
    CertificateTemplate::saveTemplates('create');
});

add_action('wp_ajax_ml_update_certificate_template', function () {
    CertificateTemplate::saveTemplates('save');
});

add_action('wp_ajax_mblc_save_option', function () {
    foreach ($_POST['option'] as $key => $value) {
        mblc_update_option($key, $value);
    }
    wp_send_json_success(['message' => 'Настройки сохранены!']);
});

add_action('wp_ajax_save_cert_mailer', function () {
    mblc_update_option('cert_mailer_topic', esc_html($_POST['cert_mailer_topic']));
    mblc_update_option('cert_mailer_text', $_POST['cert_mailer_text']);
    die(json_encode(['success' => true, 'message' => 'Настройки обновлены успешно!']));
});

add_action('wp_ajax_ml_certificate_delivery', function () {
    if (!isset($_POST['users'])) {
        die(json_encode(['error' => 'Пожалуйста выберите хотя бы одного пользователя!']));
    }
    $dateIssue = $_POST['date_issue'];
    $wpm_level = (int)$_POST['wpm_level'];
    $users = $_POST['users'];
    foreach ($users as $user_id) {
        Certificate::create(
            intval($user_id),
            get_term_meta($wpm_level, '_mblc_course_name', true),
            (int)get_term_meta($wpm_level, '_mblc_template_id', true),
            $wpm_level,
            get_user_meta($user_id, 'first_name', true),
            get_user_meta($user_id, 'last_name', true),
            get_user_meta($user_id, 'surname', true),
            date('Y-m-d', strtotime($dateIssue)),
            get_term_meta($wpm_level, '_mblc_certificate_series', true),
            get_current_user_id(),
            date('Y-m-d'),
            get_term_meta($wpm_level, '_mblc_course_name', true)
        );
    }
    die(json_encode(['success' => true]));
});

add_action('wp_ajax_ml_certificate_filtered', function () {
    require_once 'features/certificates-table/table/table-row.php';
    $data = json_decode(stripslashes($_POST['data']));
    if (isset($data->filter) && isset($data->filter->date_issue)) {
        if (isset($data->filter->date_issue->from)) {
            $data->filter->date_issue->from = date('Y-m-d', strtotime($data->filter->date_issue->from));
        }
        if (isset($data->filter->date_issue->to)) {
            $data->filter->date_issue->to = date('Y-m-d', strtotime($data->filter->date_issue->to));
        }
    }
    $params = [
        'page_num' => $data->page_num,
        'per_page' => $data->per_page,
        'order_by' => $data->orderby,
        'order' => $data->order,
        'email' => $data->search_by_email ?? '',
        'filter' => $data->filter ?? null
    ];
    $query = Certificate::query($params);
    $pageNum = $query['page_num'];
    $perPage = $query['per_page'];
    $totalPages = $query['total_pages'];
    $total = $query['total'];
    die(json_encode([
        'html' => [
            'tbody' => empty($query['result']) ? '<tr><td colspan="10">Не найдено ни одного результата</td></tr>' : renderTbody($query['result'], true),
            'result_count' => renderResultCount($pageNum, $perPage, $totalPages, $total),
            'pagination' => getPagination([
                'link' => "?page_num=",
                'page' => $pageNum,
                'total' => $totalPages
            ]),
            'data' => ['page' => $pageNum, 'per_page' => $perPage, 'total_pages' => $totalPages, 'total' => $total],
            'res' => $query['result']
        ],
        'sql' => $query['sql']
    ]));
});

add_action('wp_ajax_ml_delete_certificate', function () {
    $data = json_decode(stripslashes($_POST['data']), true);
    foreach ($data as $certificate_id) {
        Certificate::delete(intval($certificate_id));
    }
    die(json_encode([
        'data' => $data,
        'status' => 'success'
    ]));
});

add_action('wp_ajax_ml_update_certificates_template_id', function () {
    $data = json_decode(stripslashes($_POST['data']), true);
    $certificate_template_id = intval($data['template_id']);
    foreach ($data['certificates'] as $certificate_id) {
        Certificate::update(intval($certificate_id), [
            'certificate_template_id' => $certificate_template_id
        ]);
    }
    die(json_encode([
        'status' => 'success'
    ]));
});

add_action('wp_ajax_ml_delete_certificate_template', function () {
    $certificate_template_id = intval($_POST['id']);
    if (!CertificateTemplate::isAccessToDelete($certificate_template_id)) {
        die(json_encode([
            'status' => 'error',
            'message' => 'Нет разрешения на удаление'
        ]));
    }
    CertificateTemplate::delete($certificate_template_id);
    die(json_encode([
        'status' => 'success',
        'id' => $certificate_template_id
    ]));
});

add_action('wp_ajax_ml_save_day_after_course_end', function () {
    if (!isset($_POST['day_number'])) {
        die(json_encode(['status' => 'error', 'message' => 'Не указано количество дней']));
    }
    update_option('ml_day_after_course_end', intval($_POST['day_number']), 'no');
    die(json_encode(['status' => 'success']));
});
