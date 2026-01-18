<?php

class MBLRPublic
{
    /**
     * MBLRPublic constructor.
     */
    public function __construct()
    {
        $this->_setHooks();
    }

    private function _setHooks()
    {
        if (!is_admin()) {
            $this->_configureTemplates();
            $this->_addStyles();
            $this->_addScripts();
            $this->_addMBLHooks();
        }

        add_action('wp_ajax_nopriv_mblr_auto_register_user_action', [$this, 'autoRegister']);
    }

    private function _addMBLHooks()
    {
        add_filter('rewrite_rules_array', [$this, 'addAutoRegRule']);
        add_filter('template_include', [$this, 'addAutoRegTpl'], 100);

        add_filter('query_vars', [$this, 'addQueryVars']);

        add_action('mblr_auto_register_form', [$this, 'autoRegisterForm']);
    }

    private function _configureTemplates()
    {
    }

    private function _addStyles()
    {
        add_action("wpm_head", [$this, 'addStyles'], 901);
    }

    public function addStyles()
    {
        wpm_enqueue_style('mblr_main', plugins_url('/mbl-autoregistration/assets/css/main.css?v=' . MBLR_VERSION));
    }

    private function _addScripts()
    {
        add_action("wpm_footer", [$this, 'addScripts'], 901);
    }

    public function addScripts()
    {
        wpm_enqueue_script('mblr_js_main', plugins_url('/mbl-autoregistration/assets/js/mblr_public.js?v=' . MBLR_VERSION));
    }

    public function addAutoRegTpl($template)
    {
        global $wp_query;

        $hash = wpm_array_get($wp_query->query_vars, 'mblr_hash');
        $email = wpm_array_get($wp_query->query_vars, 'mblr_email');

        $isAutoRegPage = wpm_array_get($wp_query->query_vars, 'wpm-page') === 'mblr_auto_registration'
                         && $hash !== null
                         && $email !== null
                         && !wpm_is_users_overflow();

        if ($isAutoRegPage && !is_user_logged_in()) {
            status_header(200);

            $template = MBLR_DIR . '/templates/public/layouts/auto-registration.php';
        } elseif($isAutoRegPage && is_user_logged_in()) {
            wp_redirect(apply_filters('autoreg_redirect', wpm_get_start_url(), $hash));
        }

        return apply_filters('mblr_template_include', $template);
    }

    public function addAutoRegRule($rules)
    {
        $newRules = [];

        $key = str_replace([':hash', '{email}'], ['([^/]+)', '([^/]+)'], MBLRCore::AUTO_REGISTRATION_LINK) . '/?';

        $newRules[$key] = 'index.php?wpm-page=mblr_auto_registration&mblr_hash=$matches[1]&mblr_email=$matches[2]';

        return array_merge($newRules, $rules);
    }

    public function addQueryVars($vars)
    {
        $vars[] = 'mblr_hash';
        $vars[] = 'mblr_email';

        return $vars;
    }

    public function autoRegisterForm()
    {
        $email = get_query_var('mblr_email');
        $hash = get_query_var('mblr_hash');
        $captcha = wpm_option_is('main.enable_captcha', 'on') && wpm_option_is('main.enable_captcha_auto_registration', 'on');

        $link = wpm_get_option('mblr_auto_registration.' . $hash);
        $userAgreement = wpm_array_get($link, 'user_agreement') === 'on';

        $emailExists = email_exists($email);
        $emailValid = filter_var($email, FILTER_VALIDATE_EMAIL) && !$emailExists;

        $isAutoConfirm = $link !== null && !$userAgreement && !$captcha && $emailValid;

        mblr_render_partial('auto-register-form', 'public', compact('email', 'hash', 'userAgreement', 'captcha', 'isAutoConfirm', 'emailValid', 'emailExists'));
    }

    public function autoRegister()
    {
        $error = false;
        $registered = false;
        $message = '';
        $form = [
            'first_name' => '',
            'surname'    => '',
            'phone'      => '',
            'name'       => '',
            'patronymic' => '',
            'custom1'    => '',
            'custom2'    => '',
            'custom3'    => '',
            'custom4'    => '',
            'custom5'    => '',
            'custom6'    => '',
            'custom1textarea'    => '',
        ];


        foreach ($_POST['fields'] as $item) {
            $form[$item['name']] = trim($item['value']);
        }

        $hash = wpm_array_get($form, '_mblr_hash');

        if (!$hash) {
            $message = __('Неактивный код регистрации', 'mbl');
            $error = true;
            wpm_registration_result($message, $registered);
        }

        $link = wpm_get_option('mblr_auto_registration.' . $hash);

        if (!$link) {
            $message = __('Неактивный код регистрации', 'mbl');
            $error = true;
            wpm_registration_result($message, $registered);
        }

        $term_id = wpm_array_get($link, 'level');
        $duration = wpm_array_get($link, 'duration');
        $units = wpm_array_get($link, 'units');
        $is_unlimited = intval(wpm_array_get($link, 'is_unlimited'));

        if (!filter_var(wpm_array_get($form, 'email'), FILTER_VALIDATE_EMAIL)) {
            $message = __('Некорректный email', 'mbl');
            $error = true;
            wpm_registration_result($message, $registered);
        }

        $form['login'] = wpm_email_to_login($form['email']);
        $form['pass'] = wp_generate_password();


        if (!MBLReCaptcha::check('auto_registration', $form)) {
            $message = __('Необходимо пройти проверку reCAPTCHA', 'mbl');
            $error = true;
            wpm_registration_result($message, $registered);
        }


        if (wpm_is_users_overflow()) {
            $message = __('Регистрация временно недоступна', 'mbl');
            $error = true;
            wpm_registration_result($message, $registered);
        }

        if (!validate_username($form['login'])) {
            $message .= __('Некорректный логин. Для логина разрешены только буквы латинского алфавита и цифры', 'mbl');
            wpm_registration_result($message, $registered);
        }


        // check if username exist
        if (username_exists($form['login'])) {
            $message .= __('Этот логин уже используется', 'mbl');
            wpm_registration_result($message, $registered);
        }
        // check if email exist
        if (email_exists($form['email'])) {
            $message .= '<div data-already-registered='. $form['email'] . '>'. __('Этот email уже используется', 'mbl') . '</div>';
            wpm_registration_result($message, $registered);
        }

        // generate code for user
        $code = wpm_insert_one_user_key($term_id, $duration, $units, $is_unlimited);

        // check if code was generated
        $index = wpm_search_key_id($code);

        $form['code'] = $code;

        // check if user key exist
        if ($index === null) {
            // if not exist
            $error = true;
            $message .= (__('Неверный ключ', 'mbl') . '<br>');

            wpm_registration_result($message, $registered);

        } elseif ($index['key_info']['status'] === 'used') {
            $message .= __('Этот код доступа уже используется', 'mbl');
            $error = true;
            wpm_registration_result($message, $registered);
        }

        if ($error) {
            wpm_registration_result($message, $registered);
        } else {

            $user_id = wp_insert_user(
                [
                    'user_login' => $form['login'],
                    'user_pass'  => $form['pass'],
                    'user_email' => $form['email'],
                    'first_name' => wpm_array_get($form, 'first_name'),
                    'last_name'  => wpm_array_get($form, 'last_name'),
                    'role'       => 'customer',
					'user_url'   => 'ajax_user_registration',
                ]
            );

            $registered = wpm_register_user([
                'user_id'    => $user_id,
                'user_data'  => $form,
                'index'      => $index,
                'source'     => 'auto_registration',
                'send_email' => true,
            ]);

            if ($registered) {
                $message = __('Спасибо за регистрацию!', 'mbl') . '<br>' . __('Сообщение с подтверждением отправлено на указанный адрес', 'mbl');

                wp_clear_auth_cookie();
                wp_set_current_user($user_id);
                wp_set_auth_cookie($user_id);

                echo json_encode([
                    'message'    => $message,
                    'registered' => $registered,
                    'link'       => apply_filters('autoreg_redirect', wpm_get_start_url(), $hash),
                    'clear_utm' => apply_filters('wpm_clear_utm', false),
                ]);
                die();
            }
        }
    }
}
