<?php
/**
 * config.php — конфигурация интеграции amoCRM → /api/ml-grant.php → WP REST (ML Grant Bridge) 
 *
 * Где лежит:
 *   /api/config.php (вне WordPress)
 *
 * Перед тестом обязательно заполните REPLACE_ME_*.
 */

// Режим работы интеграции
// 'diag' — расширенные логи (отладка)
// 'prod' — боевой режим (минимальные логи)
define('MLGB_MODE', 'diag'); // 'prod' или 'diag'

define('MLGB_LOG_PATH', plugin_dir_path(__FILE__) . 'logs/mlgb.log');

// ===================== ML GRANT BRIDGE (WP) =====================

// Секрет для HMAC (ДОЛЖЕН совпадать в /api/config.php и в wp-плагине ml-grant-bridge.php)
const MLGB_HMAC_SECRET = 'd393766111a61bdd803286fe9954237ee933a5f1eda3acf9f2d82c28a86d2f6a';


// ===================== SECURITY (optional) =====================

// Разрешённые IP для входящего webhook в /api/ml-grant.php
// Пока можно оставить пустым массивом и включить позже.
const AMO_IP_ALLOWLIST = [
  // '1.2.3.4',
];