<?php
/*

Plugin Name: MEMBERLUX
Plugin URI: https://memberlux.com
Description: MEMBERLUX
Version: 2.97
Author: Виктор Левчук
Author URI: https://t.me/leviktor

*/
/**
 *  If no Wordpress, go home
 */

if (!defined('ABSPATH')) { exit; }

define('WP_MEMBERSHIP_VERSION', '2.97');
define('WP_MEMBERSHIP_DIR', plugin_dir_path(__FILE__));
define('WP_MEMBERSHIP_DIR_URL', plugin_dir_url(__FILE__));
define('SUMMERNOTE_UPLOADS_DIR', 'summernote_uploads');

include_once('inc/pluggable.php');
include_once('inc/functions.php');

function wpm_activate()
{
    wpm_install();
}

register_activation_hook(__FILE__, 'wpm_activate');
register_deactivation_hook(__FILE__, 'wpm_deactivate');

//Updater
require 'plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
	'https://github.com/MEMBERLUX/member-luxe/',
	__FILE__,
	'member-luxe'
);
$myUpdateChecker->setAuthentication('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');
