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

// ===================== SECURITY (optional) =====================

// Разрешённые IP для входящего webhook в /api/ml-grant.php
// Пока можно оставить пустым массивом и включить позже.
const AMO_IP_ALLOWLIST = [
  // '1.2.3.4',
];