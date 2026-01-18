<?php $standalone = isset($standalone) ? $standalone : false; ?>
<?php
$userAgreementRequired = apply_filters('mbl_user_agreement_enabled_registration', wpm_option_is('user_agreement.enabled_registration', 'on'));
$userAgreement2Required = apply_filters('mbl_user_agreement_2_registration_required', wpm_option_is('user_agreement_2.registration_required', 'on'));
$userAgreement3Required = apply_filters('mbl_user_agreement_3_registration_required', wpm_option_is('user_agreement_3.registration_required', 'on'));
$userAgreement4Required = apply_filters('mbl_user_agreement_4_registration_required', wpm_option_is('user_agreement_4.registration_required', 'on'));
?>
<form class="wpm-registration-form" name="wpm-user-register-form" action="" method="post">
    <?php if ($full && !$standalone) : ?>
        <div class="dropdown-panel-header text-right">
            <a class="close-dropdown-panel"><?php _e('закрыть', 'mbl'); ?><span class="close-button"><span class="icon-close"></span></span> </a>
        </div>
    <?php endif; ?>
    <?php if (wpm_option_is('registration_form.surname', 'on') || wpm_option_is('registration_form.name', 'on') || wpm_option_is('registration_form.patronymic', 'on')) : ?>
        <div class="form-fields-group">
            <?php if (wpm_option_is('registration_form.surname', 'on')) : ?>
                <div class="form-group form-icon form-icon-user">
                    <input type="text" name="last_name" value="" class="form-control" placeholder="<?php _e('Фамилия', 'mbl'); ?>" required="">
                </div>
            <?php endif; ?>
            <?php if (wpm_option_is('registration_form.name', 'on')) : ?>
                <div class="form-group form-icon form-icon-user">
                    <input type="text" name="first_name" value="" class="form-control" placeholder="<?php _e('Имя', 'mbl'); ?>" required="">
                </div>
            <?php endif; ?>
            <?php if (wpm_option_is('registration_form.patronymic', 'on')) : ?>
                <div class="form-group form-icon form-icon-user">
                    <input type="text" name="surname" value="" class="form-control" placeholder="<?php _e('Отчество', 'mbl'); ?>" required="">
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <div class="form-fields-group">
        <div class="form-group form-icon form-icon-email">
            <input type="email" name="email" value="" class="form-control" placeholder="<?php _e('Email', 'mbl'); ?>" required="">
        </div>
        <?php if (wpm_option_is('registration_form.phone', 'on')) : ?>
            <div class="form-group form-icon form-icon-phone">
                <input type="text" name="phone" value="" class="form-control" placeholder="<?php _e('Телефон', 'mbl'); ?>" required="">
            </div>
        <?php endif; ?>
    </div>


    <div class="form-fields-group">

	    <?php if ( apply_filters('mbl_registration_field_login_display', true) ) : ?>
            <div class="form-group form-icon form-icon-user">
                <input type="text" name="login"  value="" class="form-control" placeholder="<?php _e('Желаемый логин', 'mbl'); ?>" required="">
            </div>
	    <?php endif; ?>

	    <?php if ( apply_filters('mbl_registration_field_pass_display', true) ) : ?>
            <div class="form-group form-icon form-icon-lock">
                <input type="password" name="pass" value="" class="form-control" placeholder="<?php _e('Желаемый пароль', 'mbl'); ?>" required="">
            </div>
	    <?php endif; ?>
    </div>

	<?php if ( apply_filters('mbl_registration_field_code_display', true) ) : ?>
        <div class="form-fields-group">
            <div class="form-group form-icon form-icon-key wpm-pin-code-row">
                <input type="text" name="code" value="" class="form-control" placeholder="<?php _e('Код доступа', 'mbl'); ?>" required="">
                <?php if (wpm_option_is('pincode_page.generate', 'on')) : ?>
                    <i class="input-loader wpm_generate_pin_code_loader"></i>
                    <a href="#" class="wpm_generate_pin_code_button"><?php echo wpm_get_option('pincode_page.link_name', __('Генерировать', 'wpm')); ?></a>
                    <div class="pin-code-error"></div>
                <?php endif; ?>
            </div>
        </div>
	<?php endif; ?>

    <?php if (wpm_option_is('registration_form.custom1', 'on')) : ?>
        <div class="form-group form-icon form-icon-pencil">
            <input type="text"
                   name="custom1"
                   value=""
                   class="form-control"
                   placeholder="<?php echo wpm_get_option('registration_form.custom1_label'); ?>"
                   required="">
        </div>
    <?php endif; ?>
    <?php if (wpm_option_is('registration_form.custom2', 'on')) : ?>
        <div class="form-group form-icon form-icon-pencil">
            <input type="text"
                   name="custom2"
                   value=""
                   class="form-control"
                   placeholder="<?php echo wpm_get_option('registration_form.custom2_label'); ?>"
                   required="">
        </div>
    <?php endif; ?>
    <?php if (wpm_option_is('registration_form.custom3', 'on')) : ?>
        <div class="form-group form-icon form-icon-pencil">
            <input type="text"
                   name="custom3"
                   value=""
                   class="form-control"
                   placeholder="<?php echo wpm_get_option('registration_form.custom3_label'); ?>"
                   required="">
        </div>
    <?php endif; ?>
    <?php if (wpm_option_is('registration_form.custom4', 'on')) : ?>
        <div class="form-group form-icon form-icon-pencil">
            <input type="text"
                   name="custom4"
                   value=""
                   class="form-control"
                   placeholder="<?php echo wpm_get_option('registration_form.custom4_label'); ?>"
                   required="">
        </div>
    <?php endif; ?>
    <?php if (wpm_option_is('registration_form.custom5', 'on')) : ?>
        <div class="form-group form-icon form-icon-pencil">
            <input type="text"
                   name="custom5"
                   value=""
                   class="form-control"
                   placeholder="<?php echo wpm_get_option('registration_form.custom5_label'); ?>"
                   required="">
        </div>
    <?php endif; ?>
    <?php if (wpm_option_is('registration_form.custom6', 'on')) : ?>
        <div class="form-group form-icon form-icon-pencil">
            <input type="text"
                   name="custom6"
                   value=""
                   class="form-control"
                   placeholder="<?php echo wpm_get_option('registration_form.custom6_label'); ?>"
                   required="">
        </div>
    <?php endif; ?>
    <?php if (wpm_option_is('registration_form.custom7', 'on')) : ?>
        <div class="form-group form-icon form-icon-pencil">
            <input type="text"
                   name="custom7"
                   value=""
                   class="form-control"
                   placeholder="<?php echo wpm_get_option('registration_form.custom7_label'); ?>"
                   required="">
        </div>
    <?php endif; ?>
    <?php if (wpm_option_is('registration_form.custom8', 'on')) : ?>
        <div class="form-group form-icon form-icon-pencil">
            <input type="text"
                   name="custom8"
                   value=""
                   class="form-control"
                   placeholder="<?php echo wpm_get_option('registration_form.custom8_label'); ?>"
                   required="">
        </div>
    <?php endif; ?>
    <?php if (wpm_option_is('registration_form.custom9', 'on')) : ?>
        <div class="form-group form-icon form-icon-pencil">
            <input type="text"
                   name="custom9"
                   value=""
                   class="form-control"
                   placeholder="<?php echo wpm_get_option('registration_form.custom9_label'); ?>"
                   required="">
        </div>
    <?php endif; ?>
    <?php if (wpm_option_is('registration_form.custom10', 'on')) : ?>
        <div class="form-group form-icon form-icon-pencil">
            <input type="text"
                   name="custom10"
                   value=""
                   class="form-control"
                   placeholder="<?php echo wpm_get_option('registration_form.custom10_label'); ?>"
                   required="">
        </div>
    <?php endif; ?>
    <?php if (wpm_option_is('registration_form.custom1textarea', 'on')) : ?>
        <div class="form-group form-icon form-icon-pencil">
            <textarea
                   name="custom1textarea"
                   class="form-control"
                   rows="5"
                   placeholder="<?php echo wpm_get_option('registration_form.custom1textarea_label'); ?>"
                   style="resize: none;"
                   required=""></textarea>
        </div>
    <?php endif; ?>

    <?php if ($userAgreementRequired) : ?>
        <?php wpm_render_partial('user-agreement') ?>
    <?php endif; ?>
    
    <?php if ($userAgreement2Required && wpm_option_is('user_agreement_2.enabled_registration', 'on')) : ?>
        <?php wpm_render_partial('user-agreements-multiple', 'base', array('agreement_num' => '2', 'is_login' => false)) ?>
    <?php endif; ?>
    
    <?php if ($userAgreement3Required && wpm_option_is('user_agreement_3.enabled_registration', 'on')) : ?>
        <?php wpm_render_partial('user-agreements-multiple', 'base', array('agreement_num' => '3', 'is_login' => false)) ?>
    <?php endif; ?>
    
    <?php if ($userAgreement4Required && wpm_option_is('user_agreement_4.enabled_registration', 'on')) : ?>
        <?php wpm_render_partial('user-agreements-multiple', 'base', array('agreement_num' => '4', 'is_login' => false)) ?>
    <?php endif; ?>

    <?php if (wpm_option_is('main.enable_captcha', 'on') && wpm_option_is('main.enable_captcha_registration', 'on')) : ?>
        <div class="form-fields-group">
            <div class="g-recaptcha"
                 data-sitekey="<?php echo wpm_get_option('main.captcha_key'); ?>"
                 data-is-mobile="<?php echo MBLStats::isMobile() ? 1 : 0; ?>"
            ></div>
        </div>
    <?php endif; ?>

    <div class="result alert alert-warning ajax-result"></div>

    <?php
    // Проверяем, есть ли хотя бы одно обязательное соглашение
    $hasRequiredAgreements = $userAgreementRequired || 
        ($userAgreement2Required && wpm_option_is('user_agreement_2.enabled_registration', 'on')) ||
        ($userAgreement3Required && wpm_option_is('user_agreement_3.enabled_registration', 'on')) ||
        ($userAgreement4Required && wpm_option_is('user_agreement_4.enabled_registration', 'on'));
    ?>
    <input
        type="submit"
        class="mbr-btn btn-default btn-solid btn-green text-uppercase register-submit <?php echo $hasRequiredAgreements ? 'mbl-uareq' : ''; ?>"
        <?php echo $hasRequiredAgreements ? 'disabled="disabled"' : ''; ?>
        name="wpm-register-submit"
        value="<?php _e('Зарегистрироваться', 'mbl'); ?>"
    >

    <?php
        add_action('mbl_registration_form_after_submit_btn', function () {
            $userAgreement2Required = apply_filters('mbl_user_agreement_2_registration_required', wpm_option_is('user_agreement_2.registration_required', 'on'));
            $userAgreement3Required = apply_filters('mbl_user_agreement_3_registration_required', wpm_option_is('user_agreement_3.registration_required', 'on'));
            $userAgreement4Required = apply_filters('mbl_user_agreement_4_registration_required', wpm_option_is('user_agreement_4.registration_required', 'on'));
            ?>
            <?php if (!$userAgreement2Required && wpm_option_is('user_agreement_2.enabled_registration', 'on')) : ?>
                <div class="mbl-user-agreement-row">
                    <a href="#wpm_user_agreement_2_text" data-toggle="modal" data-target="#wpm_user_agreement_2_text">
                        <?php echo wpm_get_option('user_agreement_2.login_link_title', __('Соглашение №2', 'mbl')); ?>
                    </a>
                </div>
            <?php endif; ?>

            <?php if (!$userAgreement3Required && wpm_option_is('user_agreement_3.enabled_registration', 'on')) : ?>
                <div class="mbl-user-agreement-row">
                    <a href="#wpm_user_agreement_3_text" data-toggle="modal" data-target="#wpm_user_agreement_3_text">
                        <?php echo wpm_get_option('user_agreement_3.login_link_title', __('Соглашение №3', 'mbl')); ?>
                    </a>
                </div>
            <?php endif; ?>

            <?php if (!$userAgreement4Required && wpm_option_is('user_agreement_4.enabled_registration', 'on')) : ?>
                <div class="mbl-user-agreement-row">
                    <a href="#wpm_user_agreement_4_text" data-toggle="modal" data-target="#wpm_user_agreement_4_text">
                        <?php echo wpm_get_option('user_agreement_4.login_link_title', __('Соглашение №4', 'mbl')); ?>
                    </a>
                </div>
            <?php endif; ?>
            <?php
        }, 9);
    ?>
	<?php do_action('mbl_registration_form_after_submit_btn'); ?>

    <div class="result alert alert-success text-center ajax-user-registered"></div>
</form>
<?php if ($full) : ?>
    <script type="text/javascript">
        jQuery(function ($) {
            $('form[name=wpm-user-register-form]').submit(function (e) {
                var $form = $(this),
                    $holder = $form.closest('.holder'),
                    $loginForm = $('form.login:first'),
                    result = $form.find('.ajax-result'),
                    registered_alert = $form.find('.ajax-user-registered'),
                    $button = $('[name="wpm-register-submit"]');

                $button.val('<?php _e('Регистрация...', 'mbl'); ?>');
                $button.addClass('progress-button-active');
                result.html('').css({'display' : 'none'});
                $.ajax({
                    type     : 'POST',
                    dataType : 'json',
                    url      : ajaxurl,
                    data     : {
                        'action' : 'wpm_ajax_register_user_action',
                        'fields' : $form.serializeArray()
                    },
                    success  : function (data) {
                        if (data.registered === true) {
                            if (data.clear_utm) {
                              window.wpmClearUtmCookie && window.wpmClearUtmCookie();
                            }
                            $form[0].dispatchEvent(new CustomEvent("ajax-user-registered-success"));

                            registered_alert.html(data.message).fadeIn('fast', function() {
                                setTimeout(function () {
                                    $('a[href="#wpm-login"]').click();
                                    $loginForm.find('input[name=username]').val($form.find('input[name=login]').val());
                                    $loginForm.find('input[name=password]').val($form.find('input[name=pass]').val());

                                    if(!$loginForm.find('[name="g-recaptcha-response"]').length) {
                                        $loginForm.submit();
                                    }
                                }, 2000);
                            });

                        } else {
                            result.html(data.message).fadeIn();
                            $form.find('[name="wpm-register-submit"]').val('<?php _e('Зарегистрироваться', 'mbl'); ?>');
                        }

                    },
                    error    : function (data) {
                        result.html('<?php _e('Произошла ошибка.', 'mbl'); ?>').fadeIn();
                        $form.find('[name="wpm-register-submit"]').val('<?php _e('Зарегистрироваться', 'mbl'); ?>');
                    }
                })
                .always(function(){
                    $button.removeClass('progress-button-active');
                });
                e.preventDefault();
            });

            $(document).on('click', '.wpm_generate_pin_code_button', function () {
                var $this = $(this),
                    $form = $this.closest('form'),
                    $loader = $('.wpm_generate_pin_code_loader'),
                    $wpmUserCode = $form.find('[name="code"]');
                $loader.css('display', 'block');
                $wpmUserCode.prop('disabled', true);
                $this.slideUp();
                $('.pin-code-error').hide();
                $.post(ajaxurl, {action : 'wpm_get_pin_code_action'}, function (data) {
                    $wpmUserCode.prop('disabled', false);
                    if (data.success && data.code) {
                        $wpmUserCode.val(data.code);
                        $wpmUserCode.focus();
                        $wpmUserCode.blur();
                        $loader.hide();
                    } else {
                        $this.slideDown();
                        $('.pin-code-error').show().html(data.error).show();
                    }
                }, "json");

                return false;
            });
        });
    </script>
<?php endif; ?>

<?php do_action('mbl_registration_form_scripts'); ?>
