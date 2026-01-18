<?php

use function Sodium\add;

class MBL_ACCESS_Admin
{
    /**
     * MBL_ACCESS_Admin constructor.
     */
    public function __construct()
    {
        $this->_setHooks();
    }

    private function _setHooks()
    {
        $this->_addMBLHooks();
        $this->_addScripts();
    }


    private function _addScripts()
    {
        //add_action('admin_enqueue_scripts', [$this, 'addAdminScripts']);
        add_action('admin_enqueue_scripts', [$this, 'addAdminStyles']);
    }

    public function addAdminScripts()
    {
        wp_register_script('mbl_access_admin', plugins_url('/mbl-universal-access/assets/js/admin.js'), ['jquery'], MBL_ACCESS_VERSION);

        if (is_admin()) {
            wp_enqueue_script('mbl_access_admin');
        }
    }

    public function addAdminStyles()
    {
        wp_register_style('mbl_access_admin_style', plugins_url('/mbl-universal-access/assets/css/admin.css'), [], MBL_ACCESS_VERSION);

        if (is_admin()) {
            wp_enqueue_style('mbl_access_admin_style');
        }
    }

    private function _addMBLHooks()
    {
        add_action('mbl_options_items_after', [$this, 'optionsTab'], 80);
        add_action('mbl_options_content_after', [$this, 'optionsContent'], 80);
        add_action('pin_generator_setting_tab_after_wpm_level', [$this, 'newCodeSettings']);
        add_filter('mbl_render_setting_pincode_page_link_name', '__return_empty_string');

        //mbl hooks
        if (wpm_get_option('mbl_access.apply_to_register_form') == 'on') {
            add_filter('registration_form_disabled_row', [$this, 'row_filter']);
            add_filter('registration_form_login_enabled', [$this, 'login_enabled']);
            add_filter('registration_form_pass_enabled', [$this, 'pass_enabled']);
            add_filter('registration_form_code_enabled', [$this, 'code_enabled']);
        }


        //mbl-payments hooks
        if (wpm_get_option('mbl_access.apply_to_peyments_form') == 'on') {
            add_filter('payments_form_disabled_row', [$this, 'row_filter']);
            add_filter('payments_form_login_enabled', [$this, 'payments_login_enabled']);
            add_filter('payments_form_pass_enabled', [$this, 'payments_pass_enabled']);
            add_action('mblp_after_settings_tab', [$this, 'addIconsTab'], 10);
            add_action('mblp_after_settings_content', [$this, 'addIconsContent'], 10);
            add_action('mblp_texts_tabs', [$this, 'addTextTab'], 10);
            add_action('mblp_texts_content', [$this, 'addTextcontent'], 10);
        }


        //autoregistration hooks
        if (wpm_get_option('mbl_access.apply_to_autoreg_form') == 'on') {
            add_action('autoregistration_options', [$this, 'addRedirectField'], 10);
        }

        //navpanel
        add_action('mbl_navpanel_options_tabs', [$this, 'navPanelTab']);
        add_action('mbl_navpanel_options_tab_content', [$this, 'navPanelContent']);

        add_action('mblp_settings_texts_after_cart_order', [$this, 'mblpSettingsTextsAfterCartOrder']);

        add_action('mbl_user_agreement_login_setting_after', [$this, 'addMBLUserAgreementLoginCheckbox']);
        add_action('mbl_user_agreement_registration_setting_after', [$this, 'addMBLUserAgreementRegistrationCheckbox']);

        add_filter('mblr_auto_registration_link', [$this, 'addAutoregRedirect']);
    }

    public function optionsTab()
    {
        mbl_access_render_partial('options/tab', 'admin');
    }

    public function optionsContent($args)
    {
        mbl_access_render_partial('options/content', 'admin');
    }

    public function navPanelTab()
    {
        mbl_access_render_partial('navpanel/tab', 'admin');
    }

    public function navPanelContent()
    {
        mbl_access_render_partial('navpanel/content', 'admin');
    }

    public function row_filter()
    {
        return '';
    }

    public function login_enabled()
    {
        return wpm_get_option('registration_form.login') == 'on' ? 'checked' : '';
    }

    public function pass_enabled()
    {
        return wpm_get_option('registration_form.pass') == 'on' ? 'checked' : '';
    }

    public function code_enabled()
    {
        return wpm_get_option('registration_form.code') == 'on' ? 'checked' : '';
    }

    public function newCodeSettings()
    {
        mbl_access_render_partial('options/pin-code', 'admin');
    }

    public function payments_login_enabled()
    {
        return wpm_get_option('mblp.new_clients.login') == 'on' ? 'checked' : '';
    }

    public function payments_pass_enabled()
    {
        return wpm_get_option('mblp.new_clients.pass') == 'on' ? 'checked' : '';
    }

    function addIconsTab()
    {
        ?>
        <li><a href="#mbl_inner_tab_mblp_icons"><?php _e('Иконки платежных систем', 'mbl_admin') ?></a></li>
        <?php
    }

    function addTextTab()
    {
        ?>
        <li><a href="#header-tab-mblp-4-form"><?php _e('Вкладки', 'mbl_admin') ?></a></li>
        <?php
    }

    function addIconsContent()
    {
        $gateways = WC()->payment_gateways->get_available_payment_gateways();
        $available_gateways = [];

        if ($gateways) {
            foreach ($gateways as $gateway) {
                if ($gateway->enabled == 'yes') {
                    $available_gateways[] = $gateway;
                }
            }
        }

        mbl_access_render_partial('options/payments-icons', 'admin', compact('available_gateways'));
    }

    function addTextcontent()
    {
        mbl_access_render_partial('options/texts', 'admin');
    }

    function addRedirectField()
    {
        mbl_access_render_partial('options/autoreg-redirect-field', 'admin');
    }

    public function mblpSettingsTextsAfterCartOrder()
    {
        wpm_render_partial('options/text-row', 'admin', ['label' => __('Кнопка оформления заказа для товаров с нулевой ценой', 'mbl_admin'), 'key' => 'mbl_access.cart_order_empty_text']);
    }

    public function addMBLUserAgreementLoginCheckbox()
    {
        mbl_access_render_partial('options/checkbox',
            'admin',
            [
                'label' => __('Обязательно', 'mbl_admin'),
                'name'  => 'main_options[user_agreement][login_required]',
                'value' => (wpm_option_is('user_agreement.login_required', 'on') ? 'on' : 'off'),
            ]);
    }

    public function addMBLUserAgreementRegistrationCheckbox()
    {
        mbl_access_render_partial('options/checkbox',
            'admin',
            [
                'label' => __('Обязательно', 'mbl_admin'),
                'name'  => 'main_options[user_agreement][registration_required]',
                'value' => (wpm_option_is('user_agreement.registration_required', 'off') ? 'off' : 'on'),
            ]);
    }

    public function addAutoregRedirect($data)
    {
        $redirect = wpm_array_get($_POST, 'redirect_link');
        $redirect_new_users = wpm_array_get($_POST, 'redirect_link_new_users');

        if ($redirect && $redirect != '') {
            $data['redirect_link'] = trim($redirect);
        }

        if ($redirect_new_users && $redirect_new_users != '') {
            $data['redirect_link_new_users'] = trim($redirect_new_users);
        }

        return $data;
    }
}