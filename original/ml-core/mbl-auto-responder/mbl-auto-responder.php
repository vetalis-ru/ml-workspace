<?php
/*

Plugin Name: MEMBERLUX AUTO RESPONDER
Plugin URI: https://memberlux.com
Description: МОДУЛЬ АВТОМАТИЧЕСКИХ РАССЫЛОК
Version: 2.4
Author: Виктор Левчук
Author URI: https://t.me/leviktor

 */
/** @noinspection PhpDefineCanBeReplacedWithConstInspection */

use Mbl\AutoResponder\MBLARCore;

if (!defined('ABSPATH')) {
    exit;
}
if (!function_exists('get_plugin_data')) {
    require_once(ABSPATH . 'wp-admin/includes/plugin.php');
}
$plugin_data = get_plugin_data(__FILE__);
define('MBL_AUTO_RESPONDER_VERSION', $plugin_data['Version']);
define('MBL_AUTO_RESPONDER_PATH', plugin_dir_path(__FILE__));
define('MBL_AUTO_RESPONDER_URI', plugin_dir_url(__FILE__));
define('MBLAR_CRON_INTERVAL', 60);
define('MBL_AUTO_RESPONDER_DEBUG', false);

require_once __DIR__ . "/vendor/autoload.php";
require_once __DIR__ . "/admin/unsubscribe.php";
require_once __DIR__ . "/inc/render-mail.php";
require_once __DIR__ . "/inc/cron.php";
require_once __DIR__ . "/admin/letters.php";
add_action('init', 'mblar_init', 1);
function mblar_init()
{
    new MBLARCore();
}
register_activation_hook(__FILE__, 'mblar_plugin_activate');
register_deactivation_hook(__FILE__, 'mblar_plugin_deactivation');
function mblar_plugin_activate()
{
    MBLARCore::install();
    global $wpdb;
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    $table_name_mailing = $wpdb->get_blog_prefix() . 'memberlux_mailing';
    $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset} COLLATE {$wpdb->collate}";
    $sql_mailing = "CREATE TABLE {$table_name_mailing} (
		id bigint(20) unsigned NOT NULL auto_increment,
		term_id bigint(20) unsigned NOT NULL,
		mail_order int NOT NULL,
		days int NOT NULL,
		hour int NOT NULL,
		minute int NOT NULL,
		interval_type ENUM('interval','day_of_the_week') NOT NULL,
		subject varchar(256) NOT NULL default '',
		message longtext NOT NULL default '',
		PRIMARY KEY  (id),
        FOREIGN KEY (term_id) REFERENCES $wpdb->terms (term_id) ON DELETE CASCADE
	) {$charset_collate};";
    $table_name_mailing_templates = $wpdb->get_blog_prefix() . 'memberlux_mailing_templates';
    $sql_mailing_templates = "CREATE TABLE {$table_name_mailing_templates} (
		id bigint(20) unsigned NOT NULL auto_increment,
		name varchar(256) NOT NULL default '',
		data longtext NOT NULL default '',
		PRIMARY KEY  (id)
	) {$charset_collate};";
    $table_mailing_results = $wpdb->get_blog_prefix() . 'memberlux_mailing_results';
    $sql_mailing_results = "CREATE TABLE $table_mailing_results (
        id bigint(20) unsigned NOT NULL auto_increment,
        user_id bigint(20) unsigned NOT NULL,
        datetime DATETIME NOT NULL,
        term_id bigint(20) unsigned NOT NULL,
        m_id bigint(20) unsigned NOT NULL,
        mail_order int NOT NULL,
        mailing_datetime DATETIME NOT NULL,
        subject varchar(256) NOT NULL default '',
		message longtext NOT NULL default '',
        PRIMARY KEY  (id),
        FOREIGN KEY (user_id) REFERENCES $wpdb->users (ID) ON DELETE CASCADE
    ) {$charset_collate};";
    $table_keys_meta = $wpdb->get_blog_prefix() . 'memberlux_keys_meta';
    $table_references = $wpdb->get_blog_prefix() . 'memberlux_term_keys';
    $sql_keys_meta = "CREATE TABLE $table_keys_meta (
        `key_id` BIGINT(20) unsigned PRIMARY KEY,
        `activation_datetime` DATETIME NOT NULL,
        FOREIGN KEY (key_id) REFERENCES $table_references (id) ON DELETE CASCADE
    ) {$charset_collate};";
    $mailing_list_table = $wpdb->get_blog_prefix() . 'memberlux_mailing_list';
    $sql_mailing_list = "CREATE TABLE $mailing_list_table (
        term_id BIGINT(20) unsigned PRIMARY KEY,
        is_on TINYINT NOT NULL,
        datetime DATETIME NOT NULL,
        template_id BIGINT(20) NOT NULL,
        unsubscribe TINYINT NOT NULL,
        FOREIGN KEY (term_id) REFERENCES $wpdb->terms (term_id) ON DELETE CASCADE
    ) {$charset_collate}";
    $user_mailing_list = $wpdb->get_blog_prefix() . 'memberlux_user_mailing_list';
    $sql_user_mailing_list = "CREATE TABLE $user_mailing_list (
        user_id BIGINT(20) unsigned NOT NULL,
        term_id BIGINT(20) unsigned NOT NULL ,
        mailing_datetime DATETIME NOT NULL ,
        datetime_start DATETIME NOT NULL ,
        status ENUM('processing','unsubscribe','finish','disabled') NOT NULL,
        FOREIGN KEY (user_id) REFERENCES $wpdb->users (ID) ON DELETE CASCADE,
        PRIMARY KEY (user_id, term_id, mailing_datetime)
    ) {$charset_collate}";
    dbDelta($sql_mailing);
    dbDelta($sql_mailing_templates);
    dbDelta($sql_mailing_results);
    dbDelta($sql_keys_meta);
    dbDelta($sql_mailing_list);
    dbDelta($sql_user_mailing_list);
    wp_clear_scheduled_hook('mbl_auto_responder_event');
    wp_schedule_event(time(), 'mblar_interval', 'mbl_auto_responder_event');
}

function mblar_plugin_deactivation()
{
    MBLARCore::uninstall();
    wp_clear_scheduled_hook('mbl_auto_responder_event');
}
add_action('init', function () {
   if (isset($_GET['debug'])) {
       header('Content-Type: application/json');
       print_r(json_encode(get_post_meta(28, '_wpm_page_meta', true)));die();
   }
});
//Updater
require 'plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
    'https://github.com/MEMBERLUX/mbl-auto-responder/',
    __FILE__,
    'mbl-auto-responder'
);
$myUpdateChecker->setAuthentication('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');
