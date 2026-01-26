<?php
/**
 * ml-grant.php — bridge от amoCRM к WordPress (ML Grant Bridge)
 *
 * Поток:
 * amoCRM webhook (смена статуса сделки) → этот скрипт →
 *   1) читает lead/contact из amo API
 *   2) парсит поля сделки в параметры выдачи УД (term_id, duration, units)
 *   3) формирует payload
 *   4) POST в WP REST /wp-json/mlgb/v1/grant c HMAC
 *
 * ВАЖНО ПРО date_start:
 * - Поле "Дата старта" (если есть в amo) мы МОЖЕМ читать и логировать,
 *   но в MemberLux оно не используется для отложенного старта выдачи УД.
 * - Поэтому date_start НЕ участвует в валидации и НЕ передаётся как параметр ключа.
 * - При желании вы можете использовать date_start в amo для внутренних сценариев (бот/отложенная смена статуса).
 */

file_put_contents(
  __DIR__ . '/_ml-grant-entry.log',
  '[' . date('Y-m-d H:i:s') . "] ENTRY\n",
  FILE_APPEND
);


require_once __DIR__ . '/config.php';

/* - ПРОВЕРКА нА КОРРЕКТНОСТЬ РАЗМЕЩЕНИЯ config.php */
ml_grant_log('CONFIG CHECK', [
  'config_path' => __DIR__ . '/config.php',
  'amo_base' => AMO_BASE_URL,
  'amo_token_len' => defined('AMO_ACCESS_TOKEN') ? strlen(AMO_ACCESS_TOKEN) : null,
]);

/** Логгер в /api/ml-grant.log */
function ml_grant_log($msg, $ctx = []) {
  $line = '[' . date('Y-m-d H:i:s') . '] ' . $msg;
  if (!empty($ctx)) {
    $line .= ' | ' . json_encode($ctx, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  }
  $line .= PHP_EOL;
  file_put_contents(ML_GRANT_LOG_PATH, $line, FILE_APPEND);
}

/** Быстрый JSON-ответ */
function ml_grant_json($code, $data) {
  http_response_code($code);
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  exit;
}

/** Проверка IP allow-list (если включено) */
function ml_grant_check_ip_allowlist() {
  if (!defined('AMO_IP_ALLOWLIST') || !is_array(AMO_IP_ALLOWLIST) || count(AMO_IP_ALLOWLIST) === 0) {
    return true; // allow-list не включён
  }
  $ip = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? '';
  return in_array($ip, AMO_IP_ALLOWLIST, true);
}

/** Вызов amoCRM API (GET) */
function amo_api_get($path) {
  $url = rtrim(AMO_BASE_URL, '/') . $path;

  $ch = curl_init($url);
  curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 10,
    CURLOPT_HTTPHEADER => [
      'Authorization: Bearer ' . AMO_ACCESS_TOKEN,
      'Content-Type: application/json',
      'Accept: application/json',
    ],
  ]);

  $body = curl_exec($ch);
  $err  = curl_error($ch);
  $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);

  if ($body === false) {
    return ['ok' => false, 'code' => 0, 'error' => $err];
  }

  $json = json_decode($body, true);
  return ['ok' => ($code >= 200 && $code < 300), 'code' => $code, 'json' => $json, 'raw' => $body];
}

/**
 * Извлекает значение кастомного поля amo по названию.
 * ВАЖНО: названия должны совпадать с вашими полями в amo.
 */
function amo_cf_value_by_name($entity, $field_name) {
  $cfs = $entity['custom_fields_values'] ?? [];
  foreach ($cfs as $cf) {
    if (($cf['field_name'] ?? '') === $field_name) {
      $vals = $cf['values'] ?? [];
      if (isset($vals[0]['value'])) return $vals[0]['value'];
    }
  }
  return null;
}
  
/** Парсинг term_id из текста вида "Название курса (157)". */
function parse_term_id($text) {
  if (!is_string($text)) return null;
  if (preg_match('~\((\d+)\)\s*$~', trim($text), $m)) {
    return (int)$m[1];
  }
  return null;
}

/** Нормализация units */
function normalize_units($raw) {
  $raw = mb_strtolower(trim((string)$raw));
  if (in_array($raw, ['day', 'days', 'день', 'дней'], true)) return 'days';
  if (in_array($raw, ['month', 'months', 'месяц', 'месяцев'], true)) return 'months';
  if (in_array($raw, ['days', 'months'], true)) return $raw;
  return null;
}

/**
 * HMAC подпись тела запроса:
 * signature = HMAC_SHA256( timestamp + "." + body, secret )
 */
function make_hmac_headers($body) {
  $ts = (string)time();
  $base = $ts . '.' . $body;


  if (!defined('MLGB_HMAC_SECRET') || MLGB_HMAC_SECRET === '') {
  // логируешь и прерываешь выполнение (иначе подпись будет некорректной)
  return new WP_Error('mlgb_secret_missing', 'MLGB_HMAC_SECRET is not defined');
  }
  $sig = hash_hmac('sha256', $base, MLGB_HMAC_SECRET);
  return [$ts, $sig];
}

/** Отправка payload в WP REST. */
function post_to_wp($payload) {
  $body = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

  [$ts, $sig] = make_hmac_headers($body);

  $ch = curl_init(MLGB_GRANT_URL);
  curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 15,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $body,
    CURLOPT_HTTPHEADER => [
      'Content-Type: application/json',
      'Accept: application/json',
      'X-MLGB-Timestamp: ' . $ts,
      'X-MLGB-Signature: ' . $sig,
    ],
  ]);

  $resp = curl_exec($ch);
  $err  = curl_error($ch);
  $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);

  return ['code' => $code, 'resp' => $resp, 'err' => $err];
}

// ===================== ENTRYPOINT =====================

if (!ml_grant_check_ip_allowlist()) {
  ml_grant_log('BLOCKED BY IP ALLOWLIST');
  ml_grant_json(403, ['ok' => false, 'error' => 'Forbidden (IP)']);
}

$raw = file_get_contents('php://input') ?: '';
ml_grant_log('WEBHOOK RECEIVED', ['raw_len' => strlen($raw)]);

// Ожидаем JSON или x-www-form-urlencoded
$contentType = $_SERVER['CONTENT_TYPE'] ?? '';

if (stripos($contentType, 'application/json') !== false) {
  $event = json_decode($raw, true);
} else {
  // amoCRM webhook приходит как application/x-www-form-urlencoded
  parse_str($raw, $event);
}

if (!is_array($event) || empty($event)) {
  ml_grant_log('INVALID WEBHOOK BODY', [
    'content_type' => $contentType,
    'raw' => $raw,
  ]);
  ml_grant_json(400, ['ok' => false, 'error' => 'Invalid webhook body']);
}


// Пытаемся достать lead_id из нескольких популярных форматов webhook amo
$lead_id = $event['leads']['status'][0]['id'] ?? $event['leads'][0]['id'] ?? $event['lead_id'] ?? null;
$lead_id = $lead_id ? (int)$lead_id : 0;

if ($lead_id <= 0) {
  ml_grant_log('LEAD_ID NOT FOUND', ['event' => $event]);
  ml_grant_json(400, ['ok' => false, 'error' => 'lead_id not found']);
}

ml_grant_log('LEAD_ID', ['lead_id' => $lead_id]);

// 1) Читаем сделку
$lead = amo_api_get('/api/v4/leads/' . $lead_id . '?with=contacts');
if (!$lead['ok']) {
  ml_grant_log('AMO LEAD GET FAILED', ['code' => $lead['code'], 'raw' => $lead['raw'] ?? null]);
  ml_grant_json(502, ['ok' => false, 'error' => 'amo lead get failed', 'code' => $lead['code']]);
}
$lead_json = $lead['json'];
ml_grant_log('AMO LEAD LOADED', ['id' => $lead_json['id'] ?? null]);


// 2) Контакт (первый связанный)
$contacts = $lead_json['_embedded']['contacts'] ?? [];
$contact_id = isset($contacts[0]['id']) ? (int)$contacts[0]['id'] : 0;

if ($contact_id <= 0) {
  ml_grant_log('CONTACT NOT FOUND IN LEAD', ['lead_id' => $lead_id]);
  ml_grant_json(400, ['ok' => false, 'error' => 'contact not found in lead']);
}

$contact = amo_api_get('/api/v4/contacts/' . $contact_id);
if (!$contact['ok']) {
  ml_grant_log('AMO CONTACT GET FAILED', ['code' => $contact['code'], 'raw' => $contact['raw'] ?? null]);
  ml_grant_json(502, ['ok' => false, 'error' => 'amo contact get failed', 'code' => $contact['code']]);
}
$contact_json = $contact['json'];
ml_grant_log('AMO CONTACT LOADED', ['contact_id' => $contact_id]);

// 3) Email контакта
$email = null;
foreach (($contact_json['custom_fields_values'] ?? []) as $cf) {
  if (($cf['field_code'] ?? '') === 'EMAIL') {
    $vals = $cf['values'] ?? [];
    if (isset($vals[0]['value'])) $email = $vals[0]['value'];
  }
}
$email = is_string($email) ? trim(mb_strtolower($email)) : '';

if ($email === '') {
  ml_grant_log('EMAIL NOT FOUND', ['contact_id' => $contact_id]);
  ml_grant_json(400, ['ok' => false, 'error' => 'email not found']);
}

// 4) Кастомные поля сделки (по НАЗВАНИЯМ — подставьте ваши реальные field_name)
$course_text  = amo_cf_value_by_name($lead_json, 'Курс');
$duration_raw = amo_cf_value_by_name($lead_json, 'Срок выдачи');
$units_raw    = amo_cf_value_by_name($lead_json, 'Тип срока');

// date_start читаем ОПЦИОНАЛЬНО (для ваших сценариев в amo), но НЕ используем для выдачи в ML
$date_start_raw = amo_cf_value_by_name($lead_json, 'Дата старта');
$date_start_raw = is_string($date_start_raw) ? trim((string)$date_start_raw) : '';

$term_id  = parse_term_id($course_text);
$duration = (int)$duration_raw;
$units    = normalize_units($units_raw);

ml_grant_log('PARSED FIELDS', [
  'email' => $email,
  'course_text' => $course_text,
  'term_id' => $term_id,
  'duration' => $duration,
  'units' => $units,
  'date_start_raw_ignored' => $date_start_raw, // для справки (не влияет на выдачу)
]);

if (!$term_id || $duration <= 0 || !$units) {
  ml_grant_log('FIELDS INVALID', [
    'term_id' => $term_id,
    'duration' => $duration,
    'units' => $units,
  ]);
  ml_grant_json(400, ['ok' => false, 'error' => 'invalid parsed fields']);
}

/**
 * STEP 6. Идемпотентность webhook amoCRM
 *
 * Логика:
 *  - формируем уникальный ключ события:
 *      deal_id + stage_id + updated_at
 *  - если такой ключ уже обрабатывался — считаем это дублем
 *  - дубли не должны приводить к повторной выдаче / продлению УД

// формируем ключ идемпотентности
$idempotencyKey = sprintf(
    'amo:%d:%d:%d',
    (int)$dealId,
    (int)$stageId,
    (int)$updatedAt
);

// имя опции в WP
$optionName = 'ml_grant_processed_' . md5($idempotencyKey);

// проверяем, обрабатывалось ли событие ранее
if (get_option($optionName)) {
    // логируем дубль
    file_put_contents(
        __DIR__ . '/ml-grant.log',
        '[' . date('Y-m-d H:i:s') . '] DUPLICATE WEBHOOK SKIPPED: ' . $idempotencyKey . PHP_EOL,
        FILE_APPEND
    );

    // ВАЖНО: возвращаем 200 OK, чтобы amoCRM не ретраила
    http_response_code(200);
    echo json_encode([
        'status' => 'duplicate',
        'idempotency_key' => $idempotencyKey,
    ]);
    exit;
}

// помечаем событие как обработанное
add_option($optionName, time(), '', false);

// логируем новое событие
file_put_contents(
    __DIR__ . '/ml-grant.log',
    '[' . date('Y-m-d H:i:s') . '] NEW WEBHOOK ACCEPTED: ' . $idempotencyKey . PHP_EOL,
    FILE_APPEND
);
 */

$payload = [
  'email' => $email,
  'key' => [
    'term_id' => (int)$term_id,
    'duration' => (int)$duration,
    'units' => $units,
  ],
  // meta — всё, что не участвует в ключе/сроке, но полезно для трассировки/аудита
  'meta' => [
    'amo_lead_id' => $lead_id,
    'amo_contact_id' => $contact_id,
    'date_start_raw' => $date_start_raw, // храните/используйте в amo как хотите
  ],
];

if (ML_GRANT_DIAG) {
  ml_grant_log('DIAG MODE — NOT SENDING TO WP', ['payload' => $payload]);
  ml_grant_json(200, ['ok' => true, 'diag' => true, 'payload' => $payload]);
}

// 5) POST в WP
ml_grant_log('POST TO WP', ['url' => MLGB_GRANT_URL]);

$resp = post_to_wp($payload);

ml_grant_log('WP RESPONSE', ['code' => $resp['code'], 'err' => $resp['err'], 'resp' => $resp['resp']]);

if ($resp['code'] >= 200 && $resp['code'] < 300) {
  ml_grant_json(200, ['ok' => true]);
}

ml_grant_json(502, ['ok' => false, 'error' => 'wp grant failed', 'wp_code' => $resp['code'], 'wp_resp' => $resp['resp']]);
