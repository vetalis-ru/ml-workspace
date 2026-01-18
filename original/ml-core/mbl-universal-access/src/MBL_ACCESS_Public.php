<?php

use function Sodium\add;

class MBL_ACCESS_Public
{
    public static $isCheckout = false;
    public static $registerRedirect = false;
    public static $isAutoreg = false;
    public static $autoregHash = null;

    public function __construct()
    {
        if (!is_admin()) {
            $this->_addStyles();
            $this->_addScripts();
            $this->_addMBLHooks();
        }

        add_action('wpm_ajax_register_user_registered', [$this, 'setUser']);
        add_filter('wpm_ajax_register_user_form_filter', [$this, 'registerFormFilter']);
        add_filter('wpm_ajax_register_empty_index_filter', [$this, 'emptyIndexFilter']);

        add_filter('wsl_hook_process_login_alter_redirect_to', [$this, 'wslRedirect']); //Hook for WP Social Login
        add_filter('wsl_redirect_after_registration', [$this, 'pluginsRegistrationRedirect'], 20, 1);

        //autoreg
        if (wpm_get_option('mbl_access.apply_to_autoreg_form') == 'on') {
            add_filter('autoreg_redirect', [$this, 'autoregRedirect'], 10, 2);
            add_action('user_register', [$this, 'autoregUserRegistered']);
            add_action('wp_login', [$this, 'autoregUserLogin'], 10, 2);
        }
    }

    private function _addMBLHooks()
    {
        add_action('user_register', [$this, 'pluginUserRegister']);
        add_filter('gettext', [$this, 'wslGetText'], 10, 3); //Hook for WP Social Login

        if (wpm_get_option('mbl_access.apply_to_register_form') == 'on') {
            //login forms
            add_filter('mbl_registration_field_login_display', [$this, 'displayLoginField']);
            add_filter('mbl_registration_field_pass_display', [$this, 'displayPassField']);
            add_filter('mbl_registration_field_code_display', [$this, 'displayCodeField']);

            add_action('mbl_registration_form_after_submit_btn', [$this, 'registrationShortcodesLoginForm']);
            add_action('mbl_login_form_after_submit_btn', [$this, 'loginShortcodesLoginForm']);

            add_action('mbl_registration_form_scripts', [$this, 'printScripts']);
        }

        //checkout forms
        if (wpm_get_option('mbl_access.apply_to_peyments_form') == 'on') {
            add_filter('wc_get_template', [$this, 'rewriteWCTemplates'], 15, 5);
            add_filter('woocommerce_registration_error_email_exists', [$this, 'changeEmailErrorMessage'], 20, 2);
            add_filter('try_to_login_exist_user', [$this, 'loginUser']);
        }

        //autoreg
        if (wpm_get_option('mbl_access.apply_to_autoreg_form') == 'on') {
            add_action('autoreg_email_exists', [$this, 'printAutoregScript']);
            add_action('autoreg_register_tab', [$this, 'autoregRegisterShortcodes']);
            add_action('wsl_hook_process_login_before_wp_safe_redirect', [$this, 'autoregWslForceRedirect'], 10, 5);
        }

        //navpanel
        add_action('mbl_user_profile_dropdown_before_logout', [$this, 'renderCustomLinks']);

        add_action('mblp_show_return_btn', [$this, 'filterShowCartReturnButton']);
        add_filter('mblp_show_price_block', [$this, 'filterShowCartPriceBlock'], 10, 2);
        add_filter('mblp_show_order_total_block', [$this, 'filterShowOrderTotalBlock']);
        add_filter('mblp_texts_cart_order', [$this, 'filterCartOrderText']);
        add_filter('woocommerce_order_button_text', [$this, 'filterOrderButtonText']);
        add_filter('mbl_user_agreement_enabled_registration', [$this, 'mblUserAgreementEnabledRegistration']);
        add_filter('mbl_user_agreement_login_required', [$this, 'mblUserAgreementLoginRequired']);
        add_filter('mbl_user_agreement_login_option', [$this, 'userAgreementLoginRequiredOption']);
        add_action('mbl_registration_form_after_submit_btn', [$this, 'mblUserAgreementForRegistration'], 9);
        add_action('mbl_registration_before_captcha', [$this, 'mblUserAgreementForLogin']);
        add_action('woocommerce_review_order_before_submit', array($this, '_addUserAgreement'));
    }

    public function wslGetText($translation, $text, $domain)
    {
        if ($domain == 'wordpress-social-login' && $translation == $text) {
            return __($text, 'mbl');
        }

        return $translation;
    }

    public function wslRedirect($redirectTo)
    {
        if ($this->isAutoregistrationLink($redirectTo)) {
            self::$isAutoreg = true;

            return $redirectTo;
        }

        $postId = url_to_postid($redirectTo);

        if (!$postId) {
            return $redirectTo;
        }

        if (function_exists('wc_get_page_id') && wc_get_page_id('checkout') == $postId) {
            self::$isCheckout = true;
        }

        return $redirectTo;
    }

    private function _addStyles()
    {
        add_action('wpm_head', [$this, 'addStyles'], 921);
    }

    public function addStyles()
    {
        wpm_enqueue_style('mbl_access_style', plugins_url('/mbl-universal-access/assets/css/main.css'), [], MBL_ACCESS_VERSION);
    }

    private function _addScripts()
    {
        add_action("wpm_footer", [$this, 'addScripts'], 901);
    }

    public function addScripts()
    {
        if (defined('WORDPRESS_SOCIAL_LOGIN_PLUGIN_URL')) { //Hook for WP Social Login

            $wsl_settings_use_popup = get_option('wsl_settings_use_popup');

            // if a user is visiting using a mobile device, WSL will fall back to more in page
            $wsl_settings_use_popup = function_exists('wp_is_mobile') ? wp_is_mobile() ? 2 : $wsl_settings_use_popup : $wsl_settings_use_popup;

            if (!wp_script_is('wsl-widget', 'registered') && $wsl_settings_use_popup == 1) {
                wpm_enqueue_script("mblap_wsl-widget", WORDPRESS_SOCIAL_LOGIN_PLUGIN_URL . "assets/js/widget.js?v=" . MBL_ACCESS_VERSION);
            }

        }
    }

    public function displayLoginField()
    {
        return wpm_get_option('registration_form.login') == 'on';
    }

    public function displayPassField()
    {
        return wpm_get_option('registration_form.pass') == 'on';
    }

    public function displayCodeField()
    {
        return !(wpm_get_option('mbl_access.pin_code_hide_from_user') == 'on');
    }

    public function generatePinCodeOnlyForRegistrationField()
    {
        return (wpm_get_option('mbl_access.pin_code_register_only') == 'on');
    }

    public function registrationShortcodesLoginForm()
    {
        if (wpm_get_option('mbl_access.codes_register_login_form') == 'on') {
            printf(
                '<br><div class="mbl-access-sc %s">%s</div>',
                apply_filters('mbl_user_agreement_enabled_registration', wpm_option_is('user_agreement.enabled_registration', 'on')) ? 'mbl-access-locked' : '',
                do_shortcode(stripslashes(wpm_get_option('mbl_access.codes_register')))
            );
        }
    }

    public function loginShortcodesLoginForm()
    {
        global $wp_query;

        $hash = wpm_array_get($wp_query->query_vars, 'mblr_hash');
        $email = wpm_array_get($wp_query->query_vars, 'mblr_email');

        $isAutoRegPage = wpm_array_get($wp_query->query_vars, 'wpm-page') === 'mblr_auto_registration'
                         && $hash !== null
                         && $email !== null
                         && !wpm_is_users_overflow();

        if ($isAutoRegPage) {
            if (wpm_get_option('mbl_access.codes_login_autoreg_form') == 'on') {
                printf(
                    '<br><div class="mbl-access-sc %s">%s</div>',
                    apply_filters('mbl_user_agreement_login_required', wpm_option_is('user_agreement.enabled_login', 'on')) ? 'mbl-access-locked' : '',
                    do_shortcode(stripslashes(wpm_get_option('mbl_access.codes_login')))
                );
            }
        } else {
            if (wpm_get_option('mbl_access.codes_login_login_form') == 'on') {
                printf(
                    '<br><div class="mbl-access-sc %s">%s</div>',
                    apply_filters('mbl_user_agreement_login_required', wpm_option_is('user_agreement.enabled_login', 'on')) ? 'mbl-access-locked' : '',
                    do_shortcode(stripslashes(wpm_get_option('mbl_access.codes_login')))
                );
            }
        }
    }

    public function renderCustomLinks()
    {
        if (wpm_get_option('mbl_access.enable_partner_program') == 'on') {
            printf(
                '<li><a href="%s" rel="noopener noreferrer"><span class="fa %s" style="margin-right: 5px;"></span> %s</a></li>',
                wpm_get_option('mbl_access.partner_link_url'),
                wpm_get_option('mbl_access.partner_link_icon') ?: 'fa-link',
                wpm_get_option('mbl_access.partner_link_name')
            );
        }

        foreach (wpm_get_option('mbl_access.enable_custom_link', []) as $key => $custom_link) {
            if ($custom_link === 'on') {
                printf(
                    '<li><a href="%s" rel="noopener noreferrer"><span class="fa %s" style="margin-right: 5px;"></span> %s</a></li>',
                    wpm_get_option('mbl_access.custom_link_url.' . $key),
                    wpm_get_option('mbl_access.custom_link_icon.' . $key) ?: 'fa-link',
                    wpm_get_option('mbl_access.custom_link_name.' . $key)
                );
            }
        }
    }

    public function printScripts()
    {
        $redirect_url = wpm_get_start_url();

        if (wpm_get_option('mbl_access.pin_code_redirect') == 'on') {
            $redirect_url = wpm_get_option('mbl_access.pin_code_redirect_url');
        }

        mbl_access_render_partial('scripts', 'public', compact('redirect_url'));
    }

    public function registerFormFilter($form)
    {
        if (!isset($form['login'])) {
            $form['login'] = wpm_email_to_login($form['email']);
        }

        if (!isset($form['pass'])) {
            $form['pass'] = wp_generate_password();
        }

        if (!isset($form['code']) && wpm_option_is('pincode_page.generate', 'on')) {
            $term_id = wpm_get_option('pincode_page.lvl');
            $duration = wpm_get_option('mbl_access.pin_code_duration');
            $units = wpm_get_option('mbl_access.pin_code_units');
            $is_unlimited = intval(wpm_get_option('mbl_access.pin_code_is_unlimited') == 'on');

            $form['code'] = wpm_insert_one_user_key($term_id, $duration, $units, $is_unlimited);
        }

        return $form;
    }

    public function setUser($user_id)
    {
        wp_clear_auth_cookie();
        wp_set_current_user($user_id);
        wp_set_auth_cookie($user_id, true);
    }

    public function pluginUserRegister($user_id)
    {
        //check who registers user
        $user_meta = get_userdata($user_id);

        if ($user_meta->user_url == 'http://ajax_user_registration') {
            return;
        }

        if (!wpm_option_is('pincode_page.generate', 'on')) {
            $term_id = null;
        } elseif (!self::$isAutoreg) {
            $term_id = wpm_get_option('pincode_page.lvl');
            $duration = wpm_get_option('mbl_access.pin_code_duration');
            $units = wpm_get_option('mbl_access.pin_code_units');
            $is_unlimited = intval(wpm_get_option('mbl_access.pin_code_is_unlimited') == 'on');
        } else {
            $data = wpm_get_option('mblr_auto_registration.' . self::$autoregHash);
            $term_id = wpm_array_get($data, 'level');
            $duration = wpm_array_get($data, 'duration');
            $units = wpm_array_get($data, 'units');
            $is_unlimited = intval(wpm_array_get($data, 'is_unlimited'));
        }

        $index = null;

        if ((!$this->getIsCheckoutPage() || !$this->generatePinCodeOnlyForRegistrationField()) && $term_id) {
            // generate code for user
            $code = wpm_insert_one_user_key($term_id, $duration, $units, $is_unlimited);

            // check if code was generated
            $index = wpm_search_key_id($code);

            $form['code'] = $code;
        }

        $user = get_userdata($user_id);

        $form['first_name'] = $user->first_name;
        $form['login'] = $user->user_login;
        $form['email'] = $user->user_email;

        if (!$this->getIsCheckoutPage()) {
            //set new password
            $form['pass'] = wp_generate_password();
            wp_set_password($form['pass'], $user_id);
        }

        //update role
        $user->remove_role('subscriber');
        $user->add_role('customer');

        wpm_register_user([
            'user_id'    => $user_id,
            'user_data'  => $form,
            'index'      => $index,
            'source'     => 'auto_registration',
            'send_email' => !$this->getIsCheckoutPage(),
        ]);

        if (wpm_get_option('mbl_access.pin_code_redirect') == 'on') {
            self::$registerRedirect = true;
        }

    }

    public function rewriteWCTemplates($template, $template_name, $args = [], $template_path = '', $default_path = '')
    {
        if ($template_name == 'checkout/form-checkout.php') {
            $template = $this->_getPartialPath('checkout/form-checkout');
        }

        if ($template_name == 'checkout/payment-method.php') {
            $template = $this->_getPartialPath('checkout/payment-method');
        }

        return $template;
    }

    private function _getPartialPath($partial)
    {
        $path = "templates/public/{$partial}.php";

        return MBL_ACCESS_DIR . $path;
    }

    function changeEmailErrorMessage($__, $email)
    {
        $__ = __('Этот Email уже используется. Введите пароль', 'mbl');
        $__ = '<div data-already-registered="' . $email . '" >' . $__ . '</div>';

        return $__;
    }

    function printAutoregScript($email)
    {
        ?>
        <script>
            $('[name="username"]').val('<?php echo $email; ?>');
            $('.login-form .status').html(`
            	<div class="result alert alert-warning ajax-result" style="display: block;">
            		<?php _e('Этот email уже используется. Введите пароль.', 'mbl'); ?>
				</div>
            `);
            $('[aria-controls="login-tab"]').click();
        </script>
        <?php
    }

    private function _getAutoregRedirectUrl($hash, $force_new = false)
    {
        $data = wpm_get_option('mblr_auto_registration.' . $hash);

        if ($this->_isUserNewlyRegistered(get_current_user_id()) || $force_new) {
            return wpm_array_get($data, 'redirect_link_new_users');
        }

        return wpm_array_get($data, 'redirect_link');
    }

    public function autoregRedirect($redirect, $hash)
    {
        return $this->_getAutoregRedirectUrl($hash) ? $this->_getAutoregRedirectUrl($hash) : $redirect;
    }

    public function autoregUserRegistered($user_id)
    {
        update_user_meta($user_id, 'mbl_is_new_user', 1);
    }

    public function autoregUserLogin($login, $user)
    {
        if (get_user_meta($user->ID, 'mbl_is_new_user', true) == '1') {
            update_user_meta($user->ID, 'mbl_is_new_user', 0);
        }
    }

    public function autoregWslForceRedirect($user_id, $provider, $hybridauth_user_profile, $redirect_to)
    {
        if (self::$isAutoreg) {
            do_action('wsl_clear_user_php_session');
            wp_redirect($redirect_to);
            die();
        }
    }

    private function _isUserNewlyRegistered($user_id)
    {
        return get_user_meta($user_id, 'mbl_is_new_user', true) == '1';
    }

    public function autoregRegisterShortcodes()
    {
        global $wp_query;

        if (wpm_get_option('mbl_access.codes_register_autoreg_form') == 'on') {
            $hash = wpm_array_get($wp_query->query_vars, 'mblr_hash');
            $data = wpm_get_option('mblr_auto_registration.' . $hash);
            $userAgreement = wpm_array_get($data, 'user_agreement') === 'on';

            printf(
                '<br><div class="mbl-access-sc %s">%s</div>',
                $userAgreement ? 'mbl-access-locked' : '',
                do_shortcode(stripslashes(wpm_get_option('mbl_access.codes_register')))
            );
        }

        mbl_access_render_partial('autoreg-scripts', 'public');

    }

    public function loginUser($args)
    {
        $info = [];
        $info['user_login'] = $args['username'];
        $info['user_password'] = $args['password'];
        if (wpm_array_get($args, 'remember')) {
            $info['remember'] = 'forever';
        }

        $user_signon = wp_signon($info, false);

        if (is_wp_error($user_signon)) {
            return new WP_Error('registration-error-invalid-email', __('Логин или пароль неправильные. Попробуйте ввести еще раз, возможно включен CapsLock.', 'mbl'));
        } else {
            return $user_signon->ID;
        }
    }

    public function pluginsRegistrationRedirect($redirect_to)
    {
        if (self::$isAutoreg) {
            $autoreg_redirect_url = $this->_getAutoregRedirectUrl(self::$autoregHash, true);

            if (!empty($autoreg_redirect_url)) {
                return $autoreg_redirect_url;
            }
        }

        if (!self::$registerRedirect) {
            return $redirect_to;
        }

        if ($this->getIsCheckoutPage()) {
            return $redirect_to;
        }

        return wpm_get_option('mbl_access.pin_code_redirect_url');
    }

    public function getIsCheckoutPage()
    {
        return function_exists('is_checkout') && (is_checkout() || self::$isCheckout);
    }

    public function filterShowCartPriceBlock($val, $product)
    {
        return $val && $product->get_price();
    }

    public function filterShowOrderTotalBlock($val)
    {
        return $val && WC()->cart->get_total('edit') > 0;
    }

    public function filterCartOrderText($text)
    {
        return WC()->cart->get_total('edit') > 0
            ? $text
            : wpm_get_option('mbl_access.cart_order_empty_text');
    }

    public function filterShowCartReturnButton($val)
    {
        return $val && WC()->cart->get_total('edit') > 0;
    }

    public function filterOrderButtonText($text)
    {
        return WC()->cart->get_total('edit') > 0
            ? $text
            : wpm_get_option('mbl_access.cart_order_empty_text');
    }

    public function mblUserAgreementEnabledRegistration($value)
    {
        return ($value && !wpm_option_is('user_agreement.registration_required', 'off'));
    }

    public function mblUserAgreementLoginRequired($value)
    {
        return ($value && wpm_option_is('user_agreement.login_required', 'on'));
    }

    public function userAgreementLoginRequiredOption($value)
    {
        return wpm_option_is('user_agreement.login_required', 'on');
    }

    public function mblUserAgreementForRegistration()
    {
        if (!$this->mblUserAgreementEnabledRegistration(wpm_option_is('user_agreement.enabled_registration', 'on')) && wpm_option_is('user_agreement.enabled_registration', 'on')) {
            mbl_access_render_partial('user-agreement');
        }
    }

    public function mblUserAgreementForLogin()
    {
        if ($this->mblUserAgreementLoginRequired(wpm_option_is('user_agreement.enabled_login', 'on'))) {
            wpm_render_partial('user-agreement');
        }
    }

    public function isAutoregistrationLink($link)
    {
        if (get_option('permalink_structure') != '' && strpos($link, 'wpma/join/') !== false) {

            $url = wpm_array_get(explode('wpma/join/', $link), 1, '');
            self::$autoregHash = wpm_array_get(explode('/', $url), 0, '');

            return true;
            //$url = str_replace(':hash', $hash, 'wpma/join/:hash/{email}');

        } elseif (get_option('permalink_structure') == '' && strpos($link, 'wpm-page=mblr_auto_registration') !== false) {

            $url = wpm_array_get(explode('mblr_hash', $link), 1, '');
            self::$autoregHash = wpm_array_get(explode('&', $url), 0, '');

            //$url = sprintf('?wpm-page=mblr_auto_registration&mblr_hash=%s&mblr_email={email}', $hash);
            return true;
        }

        return false;
    }

    public function emptyIndexFilter($isEmpty)
    {
        $codeHidden = wpm_get_option('mbl_access.apply_to_register_form') == 'on'
            && wpm_get_option('mbl_access.pin_code_hide_from_user') == 'on';

        return $codeHidden ? false : $isEmpty;
    }

    public function _addUserAgreement() {
        ?><?php if (wpm_option_is('user_agreement.enabled_login', 'on') && wpm_option_is('user_agreement.login_required', 'on')) : ?>
            <div id="user-agreement-login-req" style="display: none;">
                <?php mbl_access_render_partial('parts/user-agreement', 'public', ['required' => true]); ?>
            </div>
        <?php endif; ?>

        <?php if (wpm_option_is('user_agreement.enabled_registration', 'on') && wpm_option_is('user_agreement.registration_required', 'on')) : ?>
            <div id="user-agreement-register-req">
                <?php mbl_access_render_partial('parts/user-agreement', 'public', ['required' => true]); ?>
            </div>
        <?php endif; ?><?php
    }
}
