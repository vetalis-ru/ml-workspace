<?php
add_action('login_form', 'mbl_login_form');
add_action('lostpassword_form', 'mbl_login_form');
function mbl_login_form()
{
    if (!isset($_GET['action']) && !isset($_GET['checkemail'])) {
        if (!wpm_option_is('wp_login_page_memberlux', 'on')) {
            return;
        }
    } else {
        if (!wpm_option_is('lostpassword_page_memberlux', 'on')) {
            return;
        }
    }
    ?>
    <?php if (
    wpm_option_is('main.enable_captcha', 'on')
    && wpm_option_is('main.enable_captcha_login', 'on')
) : ?>
    <div class="form-fields-group">
        <div class="g-recaptcha"
             data-sitekey="<?php echo wpm_get_option('main.captcha_key'); ?>"
             data-expired-callback="mblCCaptchaExpired"
             data-is-mobile="<?php echo MBLStats::isMobile() ? 1 : 0; ?>"
        ></div>
    </div>
<?php endif; ?>
    <?php
}

function mbl_login_styles()
{
    if (!isset($_GET['action']) && !isset($_GET['checkemail'])) {
        if (!wpm_option_is('wp_login_page_memberlux', 'on')) {
            return;
        }
    } else {
        if (!wpm_option_is('lostpassword_page_memberlux', 'on')) {
            return;
        }
    }
    wp_enqueue_style('mbl-icomoon', plugins_url('/member-luxe/2_0/fonts/icomoon/icomoon.css'), array(), WP_MEMBERSHIP_VERSION);
    wp_enqueue_style('mbl-login', plugins_url('/member-luxe/css/login.css'), array(), WP_MEMBERSHIP_VERSION);
}

add_action('login_enqueue_scripts', 'mbl_login_styles');

function mbl_login_head()
{
    if (!isset($_GET['action']) && !isset($_GET['checkemail'])) {
        if (!wpm_option_is('wp_login_page_memberlux', 'on')) {
            return;
        }
    } else {
        if (!wpm_option_is('lostpassword_page_memberlux', 'on')) {
            return;
        }
    }
    ?>
    <style>
        .login {
            background-color: #<?php echo wpm_get_design_option('main.background_color', 'f7f8f9'); ?>;
            background-repeat: <?php echo wpm_get_design_option('main.background_image.repeat', 'repeat'); ?>;
            background-position: <?php echo wpm_get_design_option('main.background_image.position', 'center top'); ?>;
            background-size: <?php echo wpm_get_design_option('main.background_image.repeat', 'repeat') == 'no-repeat' ? 'cover' : 'auto'; ?>;
        <?php if (wpm_get_design_option('main.background_image.url')) : ?> background-image: url('<?php echo wpm_remove_protocol(wpm_get_design_option('main.background_image.url', '')); ?>');
            background-attachment: <?php echo wpm_get_design_option('main.background-attachment-fixed')=='on' ? 'fixed' : 'inherit'; ?><?php endif; ?>
        }

        .login h1 a {
            background-image: none;
        }

        .login h1 a {
            height: auto;
            width: 100%;
            max-height: 200px;
        }

        .login form [type="submit"], .login #wp-submit {
            background: #<?php echo wpm_get_design_option('toolbar.button_color', 'a0b0a1'); ?>;
            color: #<?php echo wpm_get_design_option('toolbar.button_text_color', 'ffffff'); ?>;
        }

        .login form [type="submit"]:hover:not(:disabled), .login #wp-submit:hover:not(:disabled) {
            background: #<?php echo wpm_get_design_option('toolbar.button_hover_color', 'adbead'); ?>;
            color: #<?php echo wpm_get_design_option('toolbar.button_text_hover_color', 'ffffff'); ?>;
        }

        .login form [type="submit"]:active, #wp-submit:active, .login form [type="submit"]:focus, .login #wp-submit:focus {
            background: #<?php echo wpm_get_design_option('toolbar.button_active_color', '8e9f8f'); ?>;
            color: #<?php echo wpm_get_design_option('toolbar.button_text_active_color', 'ffffff'); ?>;
        }

        .login form .input::placeholder {
            color: #<?php echo wpm_get_design_option('toolbar.placeholder_color', 'a9a9a9'); ?>;
        }

        .login .form-fields-group {
            margin-bottom: 30px;
        }

        .login .form-fields-group .g-recaptcha > div {
            margin: auto;
        }

        .login .mbr-btn.btn-solid.btn-green:disabled, .login #wp-submit:disabled {
            background: #c7cac7 !important;
            color: #fff !important;
        }
        a.wpm_default_header .brand-logo {
            display: block;
            margin: 0 auto;
            width: auto;
            max-width: 100%;
            max-height: 200px;
        }
    </style>
    <?php if (wpm_option_is('main.enable_captcha', 'on') && wpm_option_is('main.enable_captcha_login', 'on')) {
    ?>
    <script>
        function mblCCaptchaExpired() {
            document.querySelector('#loginform [type="submit"]').disabled = true;
        }

        function mblRecaptchaLoadCallback() {
            var elements = document.querySelectorAll('.g-recaptcha');

            if (typeof grecaptcha === 'undefined') {
                return;
            }

            elements.forEach(function (element) {
                var elem = element,
                    submit = elem.closest('form').querySelector('[type="submit"]'),
                    id;

                submit.disabled = true;

                if (elem.children.length === 0) {
                    id = grecaptcha.render(elem, {
                        sitekey: elem.getAttribute('data-sitekey'),
                        size: (window.innerWidth < 768 || +elem.getAttribute('data-is-mobile') ? 'compact' : 'normal'),
                        callback: function () {
                            submit.disabled = false;
                        }
                    });

                    elem.setAttribute('data-widget-id', id);
                } else {
                    grecaptcha.reset(elem.getAttribute('data-widget-id'));
                }
            });
        }

        (function () {
            window.addEventListener('DOMContentLoaded', init)

            function init() {
                window.addEventListener("orientationchange", function () {
                    mblRecaptchaLoadCallback()
                });
            }
        })();
    </script>
    <?php
    $id = 'wpm-scripts-captcha';
    $path = 'https://www.google.com/recaptcha/api.js?onload=mblRecaptchaLoadCallback&render=explicit';
    echo '<script defer id="' . $id . '-js" src="' . $path . '"></script>' . "\n";
} ?>
    <?php
}

add_action('login_head', 'mbl_login_head');

function mbl_login_footer()
{
    if (!isset($_GET['action']) && !isset($_GET['checkemail'])) {
        if (!wpm_option_is('wp_login_page_memberlux', 'on')) {
            return;
        }
    } else {
        if (!wpm_option_is('lostpassword_page_memberlux', 'on')) {
            return;
        }
    }
    $logo_url = esc_url(wpm_remove_protocol(wpm_get_option('logo.url')));
    ?>
    <script>
        let user_login = document.getElementById('user_login');
        user_login && user_login.setAttribute('placeholder', '<?php _e('Логин', 'mbl') ?>');
        let user_pass = document.getElementById('user_pass');
        user_pass && user_pass.setAttribute('placeholder', '<?php _e('Пароль', 'mbl') ?>');
        let btn = document.querySelector('#lostpasswordform #wp-submit');
        btn && btn.setAttribute('value', '<?= esc_js(wpm_get_option('user_new_pass_btn', 'Получить новый пароль')) ?>');
        let elementToReplace = document.querySelector('h1 a');
        let newElement = document.createElement('a');
        newElement.href = "<?= esc_url(get_permalink(wpm_get_option('home_id')))  ?>";
        newElement.className = "wpm_default_header";
        let img = document.createElement('img');
        img.className = "brand-logo";
        img.src = "<?= esc_url($logo_url) ?>";
        img.alt = "logo";
        newElement.appendChild(img);
        elementToReplace.parentNode.replaceChild(newElement, elementToReplace);
    </script>
    <?php
}

add_action('login_footer', 'mbl_login_footer');

add_filter('authenticate', 'mbl_login_validation', 99);
function mbl_login_validation($user)
{
    if (!wpm_option_is('wp_login_page_memberlux', 'on')) {
        return $user;
    }
    if ($user instanceof WP_User && !MBLReCaptcha::check('login')) {
        return new WP_Error('captcha_error', __('Сaptcha is invalid'));
    }

    return $user;
}

function mbl_lost_password_validation($errors)
{
    if (!wpm_option_is('lostpassword_page_memberlux', 'on')) {
        return $errors;
    }
    if (!MBLReCaptcha::check('login')) {
        $errors->add('captcha_error', __('Сaptcha is invalid'));
    }
    return $errors;
}

add_filter('lostpassword_errors', 'mbl_lost_password_validation');
