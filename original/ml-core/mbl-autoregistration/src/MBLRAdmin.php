<?php

class MBLRAdmin
{
    /**
     * MBLRAdmin constructor.
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
        add_action('admin_enqueue_scripts', [$this, 'addAdminScripts']);
        add_action('admin_enqueue_scripts', [$this, 'addAdminStyles']);
    }

    public function addAdminScripts()
    {
        wp_register_script('mblr_admin', plugins_url('/mbl-autoregistration/assets/js/mblr_admin.js'), ['jquery'], MBLR_VERSION);

        if (is_admin()) {
            wp_enqueue_script('mblr_admin');
        }
    }

    public function addAdminStyles()
    {
        wp_register_style('mblr_admin_style', plugins_url('/mbl-autoregistration/assets/css/admin.css'), [], MBLR_VERSION);

        if (is_admin()) {
            wp_enqueue_style('mblr_admin_style');
        }
    }

    private function _addMBLHooks()
    {
        add_action('mbl_options_items_after', [$this, 'optionsTab'], 20);
        add_action('mbl_options_content_after', [$this, 'optionsContent'], 20);
        add_action('wp_ajax_mblr_get_auto_reg_link', [$this, 'getAutoRegistrationLink']);

        add_action('mbl_settings_recaptcha', [$this, 'captchaSettings']);
    }

    public function optionsTab()
    {
        mblr_render_partial('options/tab', 'admin');
    }

    public function optionsContent($args)
    {
        $args['levels'] = get_terms('wpm-levels', ['hide_empty' => 0]);

        mblr_render_partial('options/content', 'admin', $args);
    }

    public function getAutoRegistrationLink()
    {
        $level = wpm_array_get($_POST, 'level');
        $duration = wpm_array_get($_POST, 'duration');
        $units = wpm_array_get($_POST, 'units') === 'days' ? 'days' : 'months';
        $is_unlimited = intval(wpm_array_get($_POST, 'is_unlimited'));
        $user_agreement = wpm_array_get($_POST, 'user_agreement') === 'on' ? 'on' : 'off';
        $variableName = wpm_array_get($_POST, 'variable');

        if ($level && $duration) {
            $links = wpm_get_option('mblr_auto_registration', []);

            $hash = wp_hash($level . $duration . $units . time() . rand(1, 1000));

            if(get_option('permalink_structure') != '') {
                $url = str_replace(':hash', $hash, MBLRCore::AUTO_REGISTRATION_LINK);
            } else {
                $url = sprintf('?wpm-page=mblr_auto_registration&mblr_hash=%s&mblr_email={email}', $hash);
            }

            if($variableName !== '{email}') {
                $url = str_replace('{email}', $variableName, $url);
            }

            $link = [
                'url'            => home_url($url),
                'level'          => $level,
                'duration'       => $duration,
                'units'          => $units,
                'is_unlimited'   => $is_unlimited,
                'user_agreement' => $user_agreement,
            ];

            $links[$hash] = apply_filters('mblr_auto_registration_link', $link);

            mblr_update_option('mblr_auto_registration', $links);

            echo json_encode(['link' => $link['url']]);
            die();
        }
    }

    public function captchaSettings()
    {
        mblr_render_partial('options/captcha', 'admin');
    }
}