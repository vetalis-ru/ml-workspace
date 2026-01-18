<?php
/*

Plugin Name: MEMBERLUX UNIVERSAL ACCESS
Plugin URI: https://memberlux.com
Description: МОДУЛЬ УНИВЕРСАЛЬНОГО ДОСТУПА
Version: 1.48
Author: Виктор Левчук
Author URI: https://t.me/leviktor

*/

/**
 *  If no Wordpress, go home
 */
if (!defined('ABSPATH')) { exit; }

define('MBL_ACCESS_VERSION', '1.48');
define('MBL_ACCESS_DIR', plugin_dir_path(__FILE__));

include_once('app/app.php');

function mbl_access_activate()
{
    MBL_ACCESS_Core::install();
}

function mbl_access_deactivate()
{
    MBL_ACCESS_Core::uninstall();
}

register_activation_hook(__FILE__, 'mbl_access_activate');
register_deactivation_hook(__FILE__, 'mbl_access_deactivate');

//Updater
require 'plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
	'https://github.com/MEMBERLUX/mbl-universal-access/',
	__FILE__,
	'mbl-universal-access'
);
$myUpdateChecker->setAuthentication('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');
