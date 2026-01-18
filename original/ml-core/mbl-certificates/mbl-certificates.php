<?php
/*

Plugin Name: MEMBERLUX CERTIFICATES
Plugin URI: https://memberlux.com
Description: МОДУЛЬ СЕРТИФИКАТОВ
Version: 2.12
Author: Виктор Левчук
Author URI: https://t.me/leviktor

 */
if (!defined('ABSPATH')) {
    exit;
}
if (!function_exists( 'get_plugin_data')) {
    require_once(ABSPATH . 'wp-admin/includes/plugin.php');
}
$plugin_data = get_plugin_data(__FILE__);
define( "MBLC_PLUGIN_VERSION", $plugin_data['Version'] );
// capabilities
const CERTIFICATE_DELIVERY = 'certificate-delivery'; // Выдача сертификатов
const CERTIFICATE_EDIT = 'certificate-edit'; // Редактирование выданных сертификатов (стр. Выданные сертификаты)
spl_autoload_register(function ($class_name) {
    $file_name = __DIR__ . "/core/{$class_name}.php";
    if (file_exists($file_name)) require $file_name;
});
require_once "functions/main.php";
add_action('init', 'mbl_certificate_init', 1);
function mbl_certificate_init()
{
    new MBLCERTCore();
}
register_activation_hook(__FILE__, 'mbl_certificate_activate');
register_deactivation_hook(__FILE__, 'mbl_certificate_deactivate');
function mbl_certificate_activate()
{
    MBLCERTCore::install();
    $role_administrator = get_role('administrator');
    $role_administrator->add_cap(CERTIFICATE_DELIVERY);
    $role_administrator->add_cap(CERTIFICATE_EDIT);
    $role_coach = get_role('coach');
    if (!is_null($role_coach)) $role_coach->add_cap(CERTIFICATE_DELIVERY);

    global $wpdb;
    $table_name = $wpdb->get_blog_prefix() . 'memberlux_certificate_templates';
    $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset} COLLATE {$wpdb->collate}";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    $sql = "CREATE TABLE {$table_name} (
		certificate_template_id int(11) unsigned NOT NULL auto_increment,
		name varchar(255) NOT NULL default '',
		content longtext NOT NULL default '',
		orientation ENUM('vertical', 'horizontal') NOT NULL DEFAULT 'vertical', 
		PRIMARY KEY  (certificate_template_id)
	) $charset_collate;";

    dbDelta($sql);

    $table_name = $wpdb->get_blog_prefix() . 'memberlux_certificate';
    $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset} COLLATE {$wpdb->collate}";

    $sql = "CREATE TABLE {$table_name} (
		certificate_id  bigint(20) unsigned NOT NULL auto_increment,
		user_id bigint(20) unsigned NOT NULL,
		wpmlevel_id bigint(20) unsigned NOT NULL,
		certificate_name varchar(100) NOT NULL,
		certificate_template_id bigint(20) unsigned NOT NULL, 
		graduate_first_name varchar(60) default '',
		graduate_last_name varchar(60) default '',
		graduate_surname varchar(60) default '',		
		date_issue date NOT NULL,
		series 	varchar(20) NOT NULL,
		number 	varchar(20)  NOT NULL,
		responsible_person 	bigint(20),
		create_date date NOT NULL,
		course_name varchar(256) default '',
		custom_fields text default '',
		PRIMARY KEY  (certificate_id)
	) $charset_collate;";

    dbDelta($sql);
}

function mbl_certificate_deactivate()
{
    MBLCERTCore::uninstall();
}

add_action('plugins_loaded', 'mblc_check_update');
function mblc_check_update()
{
    if (version_compare(get_option('mblc_version'), MBLC_PLUGIN_VERSION, '<')) {
        mbl_certificate_activate();
        update_option('mblc_version', MBLC_PLUGIN_VERSION);
    }
}

//Updater
require 'plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
    'https://github.com/MEMBERLUX/mbl-certificates/',
    __FILE__,
    'mbl-certificates'
);
$myUpdateChecker->setAuthentication('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');
