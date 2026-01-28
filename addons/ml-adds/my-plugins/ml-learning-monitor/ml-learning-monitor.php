<?php
// ml-learning-monitor.php - НОВАЯ ВЕРСИЯ
/**
 * Plugin Name: ML Learning Monitor
 * Description: Term-level (wpm-levels) мониторинг "сонь" и настройки писем-напоминаний.
 * Version: 0.1.0
 * Author: Vetalis
 */

if (!defined('ABSPATH')) {
    exit;
}

// Автозагрузка классов
require_once __DIR__ . '/includes/class-mlm-core.php';
require_once __DIR__ . '/includes/class-mlm-assets.php';
require_once __DIR__ . '/includes/class-mlm-term-fields.php';
require_once __DIR__ . '/includes/class-mlm-email-fields.php';
require_once __DIR__ . '/includes/class-mlm-ajax-handler.php';
require_once __DIR__ . '/includes/class-mlm-database.php';
require_once __DIR__ . '/includes/class-mlm-renderer.php';

// Инициализация плагина
new ML_Learning_Monitor();