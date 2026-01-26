<?php
/**
 * config.php — конфигурация интеграции amoCRM → /api/ml-grant.php → WP REST (ML Grant Bridge)
 *
 * Где лежит:
 *   /api/config.php (вне WordPress)
 *
 * Перед тестом обязательно заполните REPLACE_ME_*.
 */

// ===================== AMO CRM =====================

// Базовый URL вашего amoCRM (без слэша на конце), например: https://example.amocrm.ru
const AMO_BASE_URL = 'https://progv.amocrm.ru';

// Режим работы интеграции
// 'diag' — расширенные логи (отладка)
// 'prod' — боевой режим (минимальные логи)
define('MLGB_MODE', 'prod'); // или 'diag'

if (!file_exists('/home/v/vetal/academy.sppm.su/wp-secrets.php')) {
  die('Secrets file not found');
}
require_once '/home/v/vetal/academy.sppm.su/wp-secrets.php';

// ===================== ML GRANT BRIDGE (WP) =====================

// URL WordPress REST endpoint плагина ML Grant Bridge
const MLGB_GRANT_URL = 'https://academy.sppm.su/wp-json/mlgb/v1/grant';

// Допустимое рассогласование времени (сек) при проверке HMAC (защита от replay)
const MLGB_ALLOWED_SKEW_SECONDS = 300; // 5 минут

// ===================== SECURITY (optional) =====================

// Разрешённые IP для входящего webhook в /api/ml-grant.php
// Пока можно оставить пустым массивом и включить позже.
const AMO_IP_ALLOWLIST = [
  // '1.2.3.4',
];

// ===================== LOGGING =====================

// Лог /api/ml-grant.php
const ML_GRANT_LOG_PATH = __DIR__ . '/ml-grant.log';

// DIAG-режим: если true — не шлём запрос в WP, а только логируем payload
const ML_GRANT_DIAG = false;
