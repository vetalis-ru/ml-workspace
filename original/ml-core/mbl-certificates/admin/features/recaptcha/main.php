<?php
add_action('mbl_settings_recaptcha', function () {
    ?>
    <div class="wpm-row">
        <label>
            <input type="checkbox"
                   name="main_options[main][enable_captcha_certificate]"
                <?php echo wpm_option_is('main.enable_captcha_certificate', 'on') ? ' checked' : ''; ?> >
            <?php _e('Скачать сертификат', 'mbl_admin') ?><br/>
        </label>
    </div>
    <?php
});

add_action('mblc_certificate_render', function ($show) {
    if (
        !$show
        && wpm_option_is('main.enable_captcha', 'on')
        && wpm_option_is('main.enable_captcha_certificate', 'on')
    ) {
        global $wp_query;
        $wp_query->is_404 = false;
        status_header(200);
        include "template.php";
    }
}, 10, 1);

add_filter('mblc_certificate_show', function ($show) {
    if (!wpm_option_is('main.enable_captcha', 'on') || !wpm_option_is('main.enable_captcha_certificate', 'on')) {
        $return = $show;
    } else {
        if (isset($_POST['g-recaptcha-response']) && MBLReCaptcha::check('certificate', $_POST)) {
            $return = $show;
        } else {
            $return = false;
        }
    }
    return $return;
}, 10, 1);

add_action('mblc_form_fields_presence_check', function () {
    if (wpm_option_is('main.enable_captcha', 'on')
        && wpm_option_is('main.enable_captcha_certificate', 'on')) {
        ?><div class="g-recaptcha" data-callback="onClick"
               data-expired-callback="expiredCallback"
               data-error-callback="errorCallback"
               data-sitekey="<?= wpm_get_option('main.captcha_key') ?>"></div><?php
    }
});