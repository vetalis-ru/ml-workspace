<?php $standalone = isset($standalone) ? $standalone : false; ?>
<form class="checkout woocommerce-checkout" name="checkout" action="" method="post" id="chekout-login-form">
    
    <div class="">
        <div class="form-group form-icon form-icon-user">
            <input type="text" name="account_username" id="checkout-login-field" class="form-control"
                   placeholder="<?php _e('Логин', 'mbl'); ?>" required="">
        </div>
        <div class="form-group form-icon form-icon-lock">
            <input type="password" name="account_password" class="form-control"
                   placeholder="<?php _e('Пароль', 'mbl'); ?>" required="">
        </div>
        
        <?php if (!$standalone) : ?>
            <div class="form-group">
                <a href="<?php echo wp_lostpassword_url(); ?>"
                   class="helper-link"><?php _e('Забыли пароль?', 'mbl'); ?></a>
            </div>
        <?php else : ?>
            <div class="row">
                <div class="col-sm-6"></div>
                <div class="col-sm-6 text-right">
                    <div class="form-group">
                        <a href="<?php echo wp_lostpassword_url(); ?>"
                           class="helper-link"><?php _e('Забыли пароль?', 'mbl'); ?></a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <?php if (wpm_option_is('main.enable_captcha', 'on') && wpm_option_is('main.enable_captcha_login', 'on')) : ?>
            <div class="g-recaptcha"
                 data-sitekey="<?php echo wpm_get_option('main.captcha_key'); ?>"
                 data-is-mobile="<?php echo MBLStats::isMobile() ? 1 : 0; ?>"
            ></div>
        <?php endif; ?>
    </div>

    <?php mbl_access_render_partial('login-billing-fields', 'public'); ?>
    <input type="hidden" name="try_to_login" value="yes">
    
</form>
<br>
<?php if (wpm_get_option('mbl_access.codes_login_peyments_form') == 'on') {
    echo '<div class="mbl-access-sc mbl-access-sc-checkout">';
    echo do_shortcode(stripslashes(wpm_get_option('mbl_access.codes_login')));
    echo '</div>';
} ?>