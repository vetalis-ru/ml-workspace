<?php

//define('MLGB_LOG_DIR', plugin_dir_path(__DIR__) . 'logs');
//define('MLGB_LOG_PATH', MLGB_LOG_DIR . '/mlgb.log');

/* ============================================================
==========    Унифицированный логгер с учётом режима   ========== 
 ============================================================ */
function mlgb_log_safe(string $msg, array $ctx = []) {
    // В prod режем чувствительные данные
    if (defined('MLGB_MODE') && MLGB_MODE === 'prod') {
        // Разрешённые ключи контекста
        $allowed = [
            'deal_id',
            'term_id',
            'user_id',
            'result',
            'error',
        ];

        $ctx = array_intersect_key($ctx, array_flip($allowed));
    }

    $line = '[' . date('Y-m-d H:i:s') . '] ' . $msg;
    if (!empty($ctx)) {
        $line .= ' | ' . json_encode($ctx, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
    $line .= PHP_EOL;

    file_put_contents(MLGB_LOG_PATH, $line, FILE_APPEND);
}

/* ============================================================
==========              Логгер плагина              ========== 
 ============================================================ */
/**
 * MLGB logger
 *
 * @param string $msg
 * @param array  $context

function mlgb_log($msg, array $context = [])
{
    $log_dir  = __DIR__ . '/logs';
    $log_file = $log_dir . '/mlgb.log';

    if (!is_dir($log_dir)) {
        return;
    }

    $line = '[' . date('Y-m-d H:i:s') . '] ' . $msg;

    if (!empty($context)) {
        $line .= ' | ' . json_encode(
            $context,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
    }

    $line .= PHP_EOL;

    @file_put_contents($log_file, $line, FILE_APPEND);
} */

/**
 * ============================================================================
 * MLGB: Новый пользователь → УД → письмо (боевой вариант)
 * ============================================================================
 */
function mlgb_create_user_and_grant(
    string $email,
    int $term_id,
    int $duration,
    string $units,
    string $source = 'amoCRM'
): array {

    // ---------------------------------------------------------------------
    // 0. Пользователь уже существует (new user) → сюда не заходим
    // ---------------------------------------------------------------------
    if (email_exists($email)) {
        return [
            'ok'      => false,
            'status'  => 'user_exists',
            'user_id' => null,
            'code'    => null,
        ];
    }

    // ---------------------------------------------------------------------
    // 1. (new user) - Создание пользователя
    // ---------------------------------------------------------------------
    mlgb_log_safe('CREATE USER: START', [
        'email'  => $email,
        'result' => 'start',
    ]);

    $login = sanitize_user(strtolower(current(explode('@', $email))));
    if (username_exists($login)) {
        $login .= '_' . wp_generate_password(4, false);

        mlgb_log_safe('CREATE USER: LOGIN COLLISION', [
            'email'  => $email,
            'login'  => $login,
            'result' => 'adjusted',
        ]);
    }

    $password = wp_generate_password(12, false);

    mlgb_log_safe('CREATE USER: BEFORE wp_insert_user', [
        'email'  => $email,
        'login'  => $login,
        'result' => 'attempt',
    ]);

    $user_id = wp_insert_user([
        'user_login' => $login,
        'user_pass'  => $password,
        'user_email' => $email,
        'role'       => 'customer',
    ]);

    if (is_wp_error($user_id)) {
        mlgb_log_safe('CREATE USER: FAILED', [
            'email'  => $email,
            'error'  => $user_id->get_error_message(),
            'result' => 'error',
        ]);

        return [
            'ok'      => false,
            'status'  => 'user_create_failed',
            'error'   => $user_id->get_error_message(),
            'user_id' => null,
            'code'    => null,
        ];
    }

    mlgb_log_safe('CREATE USER: SUCCESS', [
        'user_id' => (int)$user_id,
        'email'   => $email,
            'result'  => 'created',
        ]);


    // ---------------------------------------------------------------------
    // 2. (new user) - Генерация ключа
    // ---------------------------------------------------------------------
    mlgb_log_safe('KEY GENERATION: START', [
        'user_id' => (int)$user_id,
        'term_id' => (int)$term_id,
        'result'  => 'attempt',
    ]);

    $code = wpm_insert_one_user_key($term_id, $duration, $units, false);

    if (!$code) {
        mlgb_log_safe('KEY GENERATION: FAILED', [
            'user_id' => (int)$user_id,
            'term_id' => (int)$term_id,
            'error'   => 'key_generation_failed',
        ]);

        return [
            'ok'      => false,
            'status'  => 'key_generation_failed',
            'user_id' => $user_id,
            'code'    => null,
        ];
    }

    mlgb_log_safe('KEY GENERATION: SUCCESS', [
        'user_id' => (int)$user_id,
        'term_id' => (int)$term_id,
        'result'  => 'generated',
    ]);


    // ---------------------------------------------------------------------
    // 3. (new user) - DUPLICATE CHECK (БОЕВОЙ)
    // ---------------------------------------------------------------------
    mlgb_log_safe('DUPLICATE CHECK: START', [
        'user_id' => (int)$user_id,
        'term_id' => (int)$term_id,
        'result'  => 'check',
    ]);

    if (
        mlgb_check_duplicate_term_key(
            $user_id,
            $term_id,
            $duration,
            $units
        )
    ) {
        mlgb_log_safe('DUPLICATE CHECK: DUPLICATE FOUND', [
            'user_id' => (int)$user_id,
            'term_id' => (int)$term_id,
            'result'  => 'duplicate',
        ]);

        mlgb_delete_term_key_by_code($code);

        mlgb_log_safe('DUPLICATE CHECK: NEW KEY DELETED', [
            'user_id' => (int)$user_id,
            'term_id' => (int)$term_id,
            'result'  => 'deleted',
        ]);

        return [
            'ok'      => true,
            'status'  => 'duplicate',
            'user_id' => $user_id,
            'code'    => null,
        ];
    }

    mlgb_log_safe('DUPLICATE CHECK: PASSED', [
        'user_id' => (int)$user_id,
        'term_id' => (int)$term_id,
        'result'  => 'ok',
    ]);


    // ---------------------------------------------------------------------
    // 4. (new user) - Получаем index ключа
    // ---------------------------------------------------------------------
    mlgb_log_safe('KEY INDEX: SEARCH START', [
        'user_id' => (int)$user_id,
        'term_id' => (int)$term_id,
        'result'  => 'search',
    ]);

    $index = wpm_search_key_id($code);

    if (empty($index)) {

        mlgb_log_safe('KEY INDEX: NOT FOUND', [
            'user_id' => (int)$user_id,
            'term_id' => (int)$term_id,
            'result'  => 'not_found',
        ]);

        return [
            'ok'      => false,
            'status'  => 'key_index_not_found',
            'user_id' => $user_id,
            'code'    => $code,
        ];
    }

    mlgb_log_safe('KEY INDEX: FOUND', [
        'user_id' => (int)$user_id,
        'term_id' => (int)$term_id,
        'index' => $index,
        'result'  => 'ok',
    ]);


    // ---------------------------------------------------------------------
    // 5. (new user) - КАНОНИЧЕСКАЯ регистрация + EMAIL
    // ---------------------------------------------------------------------
    mlgb_log_safe('REGISTER USER: START', [
        'user_id' => (int)$user_id,
        'term_id' => (int)$term_id,
        'result'  => 'start',
    ]);

    $registered = wpm_register_user([
        'user_id'    => $user_id,
        'user_data'  => [
            'email' => $email,
            'login' => $login,
            'pass'  => $password,
            'code'  => $code,   // CHANGED: добавили user_data['code'] для активации ключа
        ],
        'index'      => $index,
        'source'     => $source,
        'send_email' => false,
    ]);

    if (!$registered) {

        mlgb_log_safe('REGISTER USER: FAILED', [
            'user_id' => (int)$user_id,
            'term_id' => (int)$term_id,
            'error'   => 'memberlux_registration_failed',
        ]);

        return [
            'ok'      => false,
            'status'  => 'memberlux_registration_failed',
            'user_id' => $user_id,
            'code'    => $code,
        ];
    }

    mlgb_log_safe('REGISTER USER: SUCCESS', [
        'user_id' => (int)$user_id,
        'term_id' => (int)$term_id,
        'result'  => 'ok',
    ]);


    // Получаем WP-пользователя
    $user = get_user_by('ID', $user_id);
    if ($user) {
        // Отправляем письмо о выдаче доступа (new key / access granted)
        wpm_send_email_about_new_key($user, $code, $term_id);
    }


    // ---------------------------------------------------------------------
    // 6. (new user) - УСПЕХ
    // ---------------------------------------------------------------------
    mlgb_log_safe('FLOW FINISHED: USER CREATED AND GRANTED', [
        'user_id' => (int)$user_id,
        'term_id' => (int)$term_id,
        'result'  => 'success',
    ]);

    return [
        'ok'      => true,
        'status'  => 'created',
        'user_id' => $user_id,
        'code'    => $code,
    ];

}
/**



 * ============================================================================
 * MLGB: Выдача УД СУЩЕСТВУЮЩЕМУ пользователю + письмо
 *
 * Используется ТОЛЬКО если пользователь уже есть в WP
 *
 * @param string $email
 * @param int    $term_id
 * @param int    $duration
 * @param string $units      ('days' | 'months')
 * @param string $source     ('amocrm' | 'api' | ...)
 *
 * @return array
 *   [
 *     'ok'      => bool,
 *     'status'  => 'created' | 'duplicate' | 'error',
 *     'user_id' => int|null,
 *     'code'    => string|null,
 *     'error'   => string|null,
 *   ]
 */
function mlgb_grant_existing_user(
    $email,
    $term_id,
    $duration,
    $units,
    $source = 'amoCRM'
) {
    // ---------------------------------------------------------------------
    // 0. Пользователь должен существовать
    // ---------------------------------------------------------------------
    $user = get_user_by('email', $email);

    if (!$user) {
        return [
            'ok'      => false,
            'status'  => 'error',
            'error'   => 'user_not_found',
            'user_id' => null,
            'code'    => null,
        ];
    }

    $user_id = (int)$user->ID;

    // ---------------------------------------------------------------------
    // 1. Генерация нового ключа доступа
    // ---------------------------------------------------------------------
    $code = wpm_insert_one_user_key(
        $term_id,
        $duration,
        $units,
        false // is_unlimited
    );

    mlgb_log_safe('EXISTING USER: KEY GENERATED', [
    'user_id' => $user->ID,
    'term_id' => $term_id,
    'result'  => $code ? 'ok' : 'failed',
    ]);

    if (!$code) {
        return [
            'ok'      => false,
            'status'  => 'error',
            'error'   => 'key_generation_failed',
            'user_id' => $user_id,
            'code'    => null,
        ];
    }

    // ---------------------------------------------------------------------
    // 2. Проверка на дубль (БД MemberLux)
    // ---------------------------------------------------------------------
    $duplicate_result = mlgb_check_duplicate_term_key(
        $user_id,
        $term_id,
        $duration,
        $units
    );

    if ($duplicate_result === 'duplicate') {
        return [
            'ok'      => true,
            'status'  => 'duplicate',
            'user_id' => $user_id,
            'code'    => $code,
            'error'   => null,
        ];
    }

    // ---------------------------------------------------------------------
    // 3. Получаем index ключа
    // ---------------------------------------------------------------------
    $index = wpm_search_key_id($code);

    if (empty($index)) {
        return [
            'ok'      => false,
            'status'  => 'error',
            'error'   => 'key_index_not_found',
            'user_id' => $user_id,
            'code'    => $code,
        ];
    }

    // ---------------------------------------------------------------------
    // 4. Активация ключа пользователю
    //    (запускает wpm_update_user_key_dates и всю логику ML)
    // ---------------------------------------------------------------------
    wpm_update_user_key_dates(
        $user_id,
        $code,
        false,
        $source
    );

    mlgb_log_safe('EXISTING USER: KEY ACTIVATED', [
    'user_id' => $user->ID,
    'term_id' => $term_id,
    'result'  => 'activated',
    ]);

    // ---------------------------------------------------------------------
    // 5. ОТПРАВКА ПИСЬМА (критично)
    // ---------------------------------------------------------------------
    
    mlgb_log_safe('EXISTING USER: BEFORE SEND EMAIL', [
    'user_id' => $user->ID,
    'term_id' => $term_id,
    'result'  => 'attempt',
    ]);

    wpm_send_email_about_new_key(
        $user,
        $code,
        $term_id
    );

    mlgb_log_safe('EXISTING USER: AFTER SEND EMAIL', [
    'user_id' => $user->ID,
    'term_id' => $term_id,
    'result'  => 'called',
    ]);

    // ---------------------------------------------------------------------
    // 6. УСПЕХ
    // ---------------------------------------------------------------------
    return [
        'ok'      => true,
        'status'  => 'created',
        'user_id' => $user_id,
        'code'    => $code,
        'error'   => null,
    ];
}



/** ============================================================================
 * Проверяет дублирующийся запрос на ключ доступа тому же пользователю, 
 * на тот же term_id, на тот же срок, за текущую дату.
 * При обнаружении — удаляет новый ключ и возвращает WP_REST_Response.
 * При отсутствии дубля — возвращает null.
 */
function mlgb_check_duplicate_term_key(
    int $user_id,
    int $term_id,
    int $duration,
    string $units

) {
    global $wpdb;

    $date_registered = current_time('Y-m-d');

    mlgb_log_safe('DUPLICATE CHECK START', [
    'user_id' => (int)$user_id,
    'term_id' => (int)$term_id,
    'result'  => 'start',
    ]);

    // ищем уже существующий такой же доступ
    $duplicate_key_id = $wpdb->get_var(
        $wpdb->prepare(
            "
            SELECT id
            FROM {$wpdb->prefix}memberlux_term_keys
            WHERE user_id = %d
              AND term_id = %d
              AND duration = %d
              AND units = %s
              AND date_registered = %s
            LIMIT 1
            ",
            $user_id,
            $term_id,
            $duration,
            $units,
            $date_registered
        )
    );

    if ($duplicate_key_id) {

        mlgb_log_safe('DUPLICATE FOUND — DELETING KEY', [
            'result'  => 'duplicate',
            'term_id' => (int)$term_id,
            'user_id' => (int)$user_id,
        ]);

        // удаляем только что созданный ключ
        $wpdb->delete(
            "{$wpdb->prefix}memberlux_term_keys",
            ['id' => $duplicate_key_id],
            ['%d']
        );

        mlgb_log_safe('DUPLICATE FLOW STOPPED', [
            'result' => 'stopped',
        ]);

        return new WP_REST_Response([
            'status' => 'duplicate_skipped',
            'reason' => 'same term/user/duration/unit/date',
        ], 200);
    }

    mlgb_log_safe('DUPLICATE CHECK PASSED', [
        'result' => 'ok',
    ]);

    return null;
}
