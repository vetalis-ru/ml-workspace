<?php

class MBLRCore
{
    const AUTO_REGISTRATION_LINK = 'wpma/join/:hash/{email}';

    public function __construct()
    {
        if ($this->_pluginEnabled()) {
            $this->_run();
        } elseif (is_admin()) {
            add_action('admin_notices', [$this, 'notActiveNotice']);
        }
    }

    public function notActiveNotice()
    {
        $class = 'notice notice-error';
        $message = __('МОДУЛЬ АВТОРЕГИСТРАЦИИ не активирован!', 'mbl_admin');

        printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
    }

    private function _pluginEnabled()
    {
        return defined('WP_MEMBERSHIP_VERSION')
               && !version_compare(get_option('wpm_version'), '2.5.9', '<')
               && $this->_pluginKeyActive();
    }

    private function _pluginKeyActive()
    {
        $activeKeys = wpm_array_pluck(wpm_array_get(wpm_get_key_data(), 'keys.active', array()), 'type');

        if (count(wpm_array_filter($activeKeys, 'fullpackaccess'))) {
            return true;
        }

        return (bool)count(wpm_array_filter($activeKeys, 'autoreg'));
    }

    private function _run()
    {
        new MBLRAdmin();
        new MBLRPublic();
    }

    public static function install()
    {
        defined('WP_MEMBERSHIP_VERSION') || exit;

        self::_setDefaultOptions();
    }

    private static function _getDefaultOptions()
    {
        return [
            'mblr' => [
                'hide_main' => 'off',
            ],
        ];
    }

    private static function _setDefaultOptions()
    {
        foreach (self::_getDefaultOptions() as $key => $value) {
            self::_setDefaultOption($key, $value);
        }
    }

    private static function _setDefaultOption($key, $value)
    {
        if (is_array($value)) {
            foreach ($value as $_key => $_value) {
                self::_setDefaultOption($key . '.' . $_key, $_value);
            }
        } elseif (wpm_get_option($key) === null) {
            mblr_update_option($key, $value);
        }
    }

    public static function uninstall()
    {

    }
}
