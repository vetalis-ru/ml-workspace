<?php
/*

Plugin Name: MEMBERLUX AUTO REGISTRATION
Plugin URI: https://memberlux.com
Description: МОДУЛЬ АВТОРЕГИСТРАЦИИ
Version: 1.3
Author: Виктор Левчук
Author URI: https://t.me/leviktor

*/
/**
 *  If no Wordpress, go home
 */

if (!defined('ABSPATH')) { exit; }

define('MBLR_VERSION', '1.3');
define('MBLR_DIR', plugin_dir_path(__FILE__));

include_once('app/app.php');

function mblr_activate()
{
    MBLRCore::install();
}

function mblr_deactivate()
{
    MBLRCore::uninstall();
}

register_activation_hook(__FILE__, 'mblr_activate');
register_deactivation_hook(__FILE__, 'mblr_deactivate');

//Updater
require 'plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
	'https://github.com/MEMBERLUX/mbl-autoregistration/',
	__FILE__,
	'mbl-autoregistration'
);
$myUpdateChecker->setAuthentication('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');
