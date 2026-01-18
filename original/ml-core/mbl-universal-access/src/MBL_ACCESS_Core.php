<?php

class MBL_ACCESS_Core
{
    const VERSION_KEY = 'mbl_access_version';

    public function __construct()
    {
        if ($this->_pluginEnabled()) {
            $this->_run();
            add_action('plugins_loaded', [$this, 'checkVersion']);
        } elseif (is_admin()) {
            add_action('admin_notices', [$this, 'notActiveNotice']);
        }
    }

    public function checkVersion()
    {
        if (version_compare(wpm_get_option(self::VERSION_KEY, '0.0.1'), MBL_ACCESS_VERSION, '<')) {
            self::install();
        }
    }

    public function notActiveNotice()
    {
        $class = 'notice notice-error';
        $message = __('МОДУЛЬ УНИВЕРСАЛЬНОГО ДОСТУПА не активирован!', 'mbl_admin');

        printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
    }

    private function _pluginEnabled()
    {
        return defined('WP_MEMBERSHIP_VERSION')
               && !version_compare(get_option('wpm_version'), '2.9.9', '<')
               && $this->_pluginKeyActive();
    }

    private function _pluginKeyActive()
    {
        $activeKeys = wpm_array_pluck(wpm_array_get(wpm_get_key_data(), 'keys.active', array()), 'type');

        if (count(wpm_array_filter($activeKeys, 'fullpackaccess'))) {
            return true;
        }

        return (bool)count(wpm_array_filter($activeKeys, 'uaccess'));
    }

    private function _run()
    {
        new MBL_ACCESS_Admin();
        new MBL_ACCESS_Public();
    }

    public static function install()
    {
        defined('WP_MEMBERSHIP_VERSION') || exit;

        self::_setDefaultOptions();

        mbl_access_update_option(self::VERSION_KEY, MBL_ACCESS_VERSION);
    }

    private static function _getDefaultOptions()
    {
        return [
            'mbl_access' => [
                'apply_to_register_form' => 'on',
                'apply_to_peyments_form' => 'on',
                'apply_to_autoreg_form'  => 'on',

                'codes_login'               => '',
                'codes_login_login_form'    => 'on',
                'codes_login_peyments_form' => 'on',
                'codes_login_autoreg_form'  => 'on',

                'codes_register'               => '',
                'codes_register_login_form'    => 'on',
                'codes_register_peyments_form' => 'on',
                'codes_register_autoreg_form'  => 'on',

                'pin_code_duration'       => 12,
                'pin_code_units'          => 'months',
                'pin_code_is_unlimited'   =>  'off',
                'pin_code_hide_from_user' => 'on',
                'pin_code_register_only'  => 'off',
                'pin_code_redirect'       => 'off',
                'pin_code_redirect_url'   => '',

                'tab_login_text'        => __('Вход', 'mbl'),
                'tab_register_text'     => __('Новый аккаунт', 'mbl'),
                'reg_link_text'         => __('Уже зарегистрированы?', 'mbl'),
                'cart_order_empty_text' => __('ПОЛУЧИТЬ ДОСТУП БЕСПЛАТНО', 'mbl'),

                'partner_link_name' => __('Партнерская программа', 'mbl'),
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
            mbl_access_update_option($key, $value);
        }
    }

    public static function uninstall()
    {

    }
}
