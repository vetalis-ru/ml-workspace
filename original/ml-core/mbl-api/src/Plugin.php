<?php

namespace Mbl\Api;

class Plugin
{
    public function activate()
    {
        $this->setup_db();
    }

    public function deactivate()
    {

    }

    public function init()
    {
        if ($this->pluginEnabled()) {
            if (is_admin() && version_compare(get_option('mb_api_db_version', '1.0'), MB_API_DB_VERSION, '<')) {
                $this->setup_db();
                update_option('mb_api_db_version', MB_API_DB_VERSION);
            }

            require_once MB_API_PATH . '/rest.php';
            require_once MB_API_PATH . '/webhooks.php';
            require_once MB_API_PATH . '/admin.php';
            (new OptionPage())->register();
        } elseif (is_admin()) {
            add_action('admin_notices', array($this, 'notActiveNotice'));
        }
    }

    public function notActiveNotice(): void
    {
        $class = 'notice notice-error';
        $message = __('МОДУЛЬ API не активирован!', 'mbl_admin');
        $hasError = false;
        if (!defined('WP_MEMBERSHIP_VERSION')) {
            $message .= ' ' . __('Не установлен плагин MEMBERLUX.', 'mbl_admin');
            $hasError = true;
        } elseif (version_compare(WP_MEMBERSHIP_VERSION, '2.73', '<')) {
            $message .= ' ' . __('Устаревшая версия MEMBERLUX.', 'mbl_admin');
            $hasError = true;
        } elseif (!$this->_pluginKeyActive()) {
            $message .= ' ' . __('Ключ не активирован.', 'mbl_admin');
            $hasError = true;
        }

        if ($hasError) {
            printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
        }
    }

    public function pluginEnabled(): bool
    {
        return !version_compare(get_option('wpm_version'), '2.73', '<') && $this->_pluginKeyActive();
    }

    private function _pluginKeyActive(): bool
    {
        $default = [
            'response_status' => '',
            'response_code' => '',
            'keys' => [],
            'keys_info' => [],
            'total_users' => 0,
            'last_check' => 0,
            'offer' => []
        ];

        $data = get_option('wpm_key_data');

        if (!is_array($data)) {
            $data = $default;
        }

        $keys = array_map(fn($i) => $i['type'], $data['keys']['active']);

        return in_array('fullpackaccess', $keys) || in_array('api', $keys);
    }

    public function setup_db() {
        global $wpdb;
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . 'memberlux_hook';
        $action_table_name = $wpdb->prefix . 'memberlux_hook_action';

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id int NOT NULL AUTO_INCREMENT,
        destination varchar(255) NOT NULL,
        sort mediumint(9) NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        active TINYINT(1) NOT NULL DEFAULT 1,
        PRIMARY KEY  (id)
    ) $charset_collate;";
        $sql_action = "CREATE TABLE IF NOT EXISTS $action_table_name (
        hook_id int NOT NULL,
        action varchar(255) NOT NULL,
        PRIMARY KEY  (hook_id, action)
    ) $charset_collate;";

        dbDelta($sql);
        dbDelta($sql_action);
    }
}
