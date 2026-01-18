<?php
/*
Plugin Name: MEMBERLUX API
Plugin URI: https://memberlux.com
Description: МОДУЛЬ API
Version: 1.25
Author: Виктор Левчук
Author URI: https://t.me/leviktor
*/

namespace Mbl\Api;

use Puc_v4_Factory;

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('get_plugin_data')) {
    require_once(ABSPATH . 'wp-admin/includes/plugin.php');
}

$plugin_data = get_plugin_data(__FILE__);
define('MB_API_VERSION', $plugin_data['Version']);
define('MB_API_PATH', plugin_dir_path(__FILE__));
define('MB_API_DB_VERSION', '2.1');
require_once __DIR__ . '/vendor/autoload.php';

$MbApiPlugin = new Plugin();
register_activation_hook(__FILE__, [$MbApiPlugin, 'activate']);
register_deactivation_hook(__FILE__, [$MbApiPlugin, 'deactivate']);
$MbApiPlugin->init();

require_once __DIR__ . '/plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
    'https://github.com/MEMBERLUX/mbl-api/',
    __FILE__,
    'mbl-api'
);
$myUpdateChecker->setAuthentication('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');
