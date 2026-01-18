<?php

namespace Mbl\AutoResponder;

class MBLARCore
{
    public function __construct()
    {
        if ($this->_pluginEnabled()) {
            $this->_run();
        } elseif (is_admin()) {
            add_action('admin_notices', array($this, 'notActiveNotice'));
        }
    }

    public function notActiveNotice()
    {
        $class = 'notice notice-error';
        $message = __('МОДУЛЬ АВТОМАТИЧЕСКИХ РАССЫЛОК не активирован!', 'mbl_admin');

        if(!defined('WP_MEMBERSHIP_VERSION')) {
            $message .= ' ' . __('Не установлен плагин MEMBERLUX.', 'mbl_admin');
        } elseif (version_compare(get_option('wpm_version'), '2.3.4', '<')) {
            $message .= ' ' . __('Устаревшая версия MEMBERLUX.', 'mbl_admin');
        } elseif (!$this->_pluginKeyActive()) {
            $message .= ' ' . __('Ключ не активирован.', 'mbl_admin');
        }
        printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
    }

    private function _pluginEnabled()
    {
        return defined('WP_MEMBERSHIP_VERSION')
            && !version_compare(get_option('wpm_version'), '2.3.4', '<')
            && $this->_pluginKeyActive();
    }

    private function _pluginKeyActive()
    {
        $activeKeys = wpm_array_pluck(wpm_array_get(wpm_get_key_data(), 'keys.active', array()), 'type');

        if (count(wpm_array_filter($activeKeys, 'fullpackaccess'))) {
            return true;
        }

        return (bool)count(wpm_array_filter($activeKeys, 'auto-responder'));
    }

    private function _run()
    {
        $plugin = new Plugin();
        require_once $plugin->path('admin/main.php');
    }

    public static function install()
    {
        defined('WP_MEMBERSHIP_VERSION') || exit;
    }

    public static function uninstall()
    {

    }
}
