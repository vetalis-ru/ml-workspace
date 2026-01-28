<?php
/**
 * Plugin Name: ML Learning Monitor
 * Description: Term-level (wpm-levels) мониторинг "сонь" и настройки писем-напоминаний.
 * Version: 0.1.0
 * Author: Vetalis
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * ВАЖНО: после выноса логики в /includes нельзя использовать plugin_dir_url(__FILE__) внутри include-файлов,
 * иначе путь станет /includes/... и ассеты сломаются. Поэтому задаём канонические константы здесь.
 */
define('MLM_VERSION', '0.1.0');
define('MLM_PLUGIN_FILE', __FILE__);
define('MLM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('MLM_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Разносим код по файлам БЕЗ изменения поведения:
 * - UI (вкладки/поля/сохранение/ассеты)
 * - Sleepers (AJAX/выборка/рендер таблицы)
 * - Класс-оркестратор (хуки/константы/constructor)
 */
require_once MLM_PLUGIN_DIR . 'includes/trait-mlm-admin-ui.php';
require_once MLM_PLUGIN_DIR . 'includes/trait-mlm-sleepers.php';
require_once MLM_PLUGIN_DIR . 'includes/class-ml-learning-monitor.php';

new ML_Learning_Monitor();
