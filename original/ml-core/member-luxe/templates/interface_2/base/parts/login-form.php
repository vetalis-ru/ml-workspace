<?php $standalone = isset($standalone) ? $standalone : false; ?>
<?php
$userAgreementRequired = apply_filters('mbl_user_agreement_login_required', wpm_option_is('user_agreement.enabled_login', 'on'))
        && apply_filters('mbl_user_agreement_login_option', false);
$userAgreement2Required = apply_filters('mbl_user_agreement_2_login_required', wpm_option_is('user_agreement_2.login_required', 'on'));
$userAgreement3Required = apply_filters('mbl_user_agreement_3_login_required', wpm_option_is('user_agreement_3.login_required', 'on'));
$userAgreement4Required = apply_filters('mbl_user_agreement_4_login_required', wpm_option_is('user_agreement_4.login_required', 'on'));
?>
<form class="login" method="post">
    <?php if ($full && !$standalone) : ?>
        <div class="dropdown-panel-header text-right">
            <a class="close-dropdown-panel"><?php _e('закрыть', 'mbl'); ?><span class="close-button"><span class="icon-close"></span></span> </a>
        </div>
    <?php endif; ?>
    <div class="form-fields-group">
        <p class="status"></p>
        <div class="form-group form-icon form-icon-user">
            <input type="text" name="username"  class="form-control" placeholder="<?php _e('Логин', 'mbl'); ?>" required="">
        </div>
        <div class="form-group form-icon form-icon-lock">
            <input type="password" name="password" class="form-control" placeholder="<?php _e('Пароль', 'mbl'); ?>" required="">
        </div>
        <?php if (!$standalone) : ?>
            <div class="form-group">
                <a href="<?php echo wp_lostpassword_url(); ?>" class="helper-link"><?php _e('Забыли пароль?', 'mbl'); ?></a>
            </div>
        <?php else : ?>
            <div class="row">
                <div class="col-sm-6"></div>
                <div class="col-sm-6 text-right">
                    <div class="form-group">
                        <a href="<?php echo wp_lostpassword_url(); ?>" class="helper-link"><?php _e('Забыли пароль?', 'mbl'); ?></a>
                    </div>
                </div>
            </div>
        <?php endif; ?>


    </div>

    <?php do_action('mbl_registration_before_captcha'); ?>

    <?php if (wpm_option_is('main.enable_captcha', 'on') && wpm_option_is('main.enable_captcha_login', 'on')) : ?>
        <div class="form-fields-group">
            <div class="g-recaptcha"
                 data-sitekey="<?php echo wpm_get_option('main.captcha_key'); ?>"
                 data-is-mobile="<?php echo MBLStats::isMobile() ? 1 : 0; ?>"
            ></div>
        </div>
    <?php endif; ?>
    
    <?php // Дополнительные соглашения с обязательным принятием при входе ?>
    <?php if ($userAgreement2Required && wpm_option_is('user_agreement_2.enabled_login', 'on')) : ?>
        <?php wpm_render_partial('user-agreements-multiple', 'base', array('agreement_num' => '2', 'is_login' => true)) ?>
    <?php endif; ?>
    
    <?php if ($userAgreement3Required && wpm_option_is('user_agreement_3.enabled_login', 'on')) : ?>
        <?php wpm_render_partial('user-agreements-multiple', 'base', array('agreement_num' => '3', 'is_login' => true)) ?>
    <?php endif; ?>
    
    <?php if ($userAgreement4Required && wpm_option_is('user_agreement_4.enabled_login', 'on')) : ?>
        <?php wpm_render_partial('user-agreements-multiple', 'base', array('agreement_num' => '4', 'is_login' => true)) ?>
    <?php endif; ?>

    <?php
    // Проверяем, есть ли хотя бы одно обязательное соглашение
    $hasRequiredAgreements = $userAgreementRequired || 
        ($userAgreement2Required && wpm_option_is('user_agreement_2.enabled_login', 'on')) ||
        ($userAgreement3Required && wpm_option_is('user_agreement_3.enabled_login', 'on')) ||
        ($userAgreement4Required && wpm_option_is('user_agreement_4.enabled_login', 'on'));
    ?>
    <button type="submit"
            <?php echo $hasRequiredAgreements ? 'disabled="disabled"' : ''; ?>
            class="mbr-btn btn-default btn-solid btn-green text-uppercase wpm-sign-in-button <?php echo $hasRequiredAgreements ? 'mbl-uareq' : ''; ?>"
    ><?php _e('Войти', 'mbl'); ?></button>

    <?php 
    // Показываем ссылки на соглашения для ознакомления (без обязательного принятия)
    // Каждое соглашение с новой строки
    ?>
    
    <?php if (!$userAgreementRequired && wpm_option_is('user_agreement.enabled_login', 'on')) : ?>
        <div class="mbl-user-agreement-row">
            <a href="#wpm_user_agreement_text" data-toggle="modal" data-target="#wpm_user_agreement_text">
                <?php echo wpm_get_option('user_agreement.login_link_title', __('Пользовательское соглашение', 'mbl')); ?>
            </a>
        </div>
    <?php endif; ?>
    
    <?php if (!$userAgreement2Required && wpm_option_is('user_agreement_2.enabled_login', 'on')) : ?>
        <div class="mbl-user-agreement-row">
            <a href="#wpm_user_agreement_2_text" data-toggle="modal" data-target="#wpm_user_agreement_2_text">
                <?php echo wpm_get_option('user_agreement_2.login_link_title', __('Соглашение №2', 'mbl')); ?>
            </a>
        </div>
    <?php endif; ?>
    
    <?php if (!$userAgreement3Required && wpm_option_is('user_agreement_3.enabled_login', 'on')) : ?>
        <div class="mbl-user-agreement-row">
            <a href="#wpm_user_agreement_3_text" data-toggle="modal" data-target="#wpm_user_agreement_3_text">
                <?php echo wpm_get_option('user_agreement_3.login_link_title', __('Соглашение №3', 'mbl')); ?>
            </a>
        </div>
    <?php endif; ?>
    
    <?php if (!$userAgreement4Required && wpm_option_is('user_agreement_4.enabled_login', 'on')) : ?>
        <div class="mbl-user-agreement-row">
            <a href="#wpm_user_agreement_4_text" data-toggle="modal" data-target="#wpm_user_agreement_4_text">
                <?php echo wpm_get_option('user_agreement_4.login_link_title', __('Соглашение №4', 'mbl')); ?>
            </a>
        </div>
    <?php endif; ?>

	<?php do_action('mbl_login_form_after_submit_btn'); ?>

<?php if ($full) : ?>
    <?php wp_nonce_field('wpm-ajax-login-nonce', 'security'); ?>
<?php endif; ?>
</form>
<?php if ($full) : ?>
    <script>
        jQuery(function ($) {
            $(document).off('submit', 'form.login');
            $(document).on('submit', 'form.login', function (e) {
                var $form = $(this),
                    $button = $('.wpm-sign-in-button'),
                    $recaptcha = $form.find('[name="g-recaptcha-response"]');
                $button.addClass('progress-button-active');
                $form.find('p.status').show().text('<?php _e('Проверка...', 'mbl'); ?>');
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: ajaxurl,
                    xhrFields: { withCredentials: true },
                    data: {
                        'action': 'wpm_ajaxlogin',
                        'username': $form.find('[name="username"]').val(),
                        'password': $form.find('[name="password"]').val(),
                        'security': $('[name="security"]').val(),
                        'remember': $form.find('[name="remember"]').val(),
                        '_wp_http_referer': ($form.find('[name="_wp_http_referer"]').length ? $form.find('[name="_wp_http_referer"]').val() : ''),
                        'g-recaptcha-response': ($recaptcha.length ? $recaptcha.val() : '')
                    },
                    success: function (data) {
                        $form.find('p.status').text(data.message);
                        if (data.loggedin == true) {
                            location.reload(false);
                        }
                    }
                })
                .always(function(){
                    $button.removeClass('progress-button-active');
                });
                e.preventDefault();
                return false;
            });
        });
    </script>
<?php endif; ?>
