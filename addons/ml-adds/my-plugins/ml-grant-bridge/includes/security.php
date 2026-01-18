<?php

/**
 * Проверяет HMAC-подпись REST-запроса (timestamp + raw body).
 * Возвращает WP_REST_Response при ошибке или массив с данными при успехе.
 */
function mlgb_verify_hmac_request() {

    /* ===== VERIFY HMAC HEADERS ===== */

    $ts  = $_SERVER['HTTP_X_MLGB_TIMESTAMP'] ?? '';
    $sig = $_SERVER['HTTP_X_MLGB_SIGNATURE'] ?? '';

    if (!$ts || !$sig) {
    mlgb_log_safe('SECURITY FAIL: HMAC HEADERS MISSING', [
        'error' => 'hmac_headers_missing',
    ]);
            return new WP_REST_Response(['error' => 'hmac_headers_missing'], 401);
    }

    // читаем сырое тело запроса
    $raw_body = file_get_contents('php://input');

    // формируем базовую строку
    $base = $ts . '.' . $raw_body;

    // вычисляем ожидаемую подпись
    $expected = hash_hmac('sha256', $base, MLGB_HMAC_SECRET);

    // безопасное сравнение подписей
    if (!hash_equals($expected, $sig)) {
    mlgb_log_safe('SECURITY FAIL: INVALID HMAC', [
        'error'  => 'invalid_hmac',
        'result' => 'rejected',
    ]);
        return new WP_REST_Response(['error' => 'invalid_hmac'], 403);
    }

    // проверка допустимого сдвига времени
    $max_skew = 300; // 5 минут
    if (abs(time() - (int)$ts) > $max_skew) {
    mlgb_log_safe('SECURITY FAIL: TIMESTAMP EXPIRED', [
        'error'  => 'timestamp_expired',
        'result' => 'rejected',
    ]);
        return new WP_REST_Response(['error' => 'timestamp_expired'], 401);
    }

    mlgb_log_safe('HMAC VERIFIED', [
        'result' => 'ok',
    ]);

    return [
        'timestamp' => (int)$ts,
        'raw_body'  => $raw_body,
    ];
}
