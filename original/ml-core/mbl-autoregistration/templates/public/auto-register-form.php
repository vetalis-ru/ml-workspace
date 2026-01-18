<form class="wpm-registration-form" name="mblr-auto-register-form" action="" method="post">
    <?php if ($emailValid) : ?>
        <input type="hidden" name="email" value="<?php echo $email; ?>" />
    <?php else : ?>
        <div class="form-fields-group">
            <div class="form-group form-icon form-icon-email">
                <input
                    type="email"
                    name="email"
                    value="<?php echo $emailExists ? $email : ''; ?>"
                    class="form-control"
                    placeholder="<?php _e('Email', 'mbl'); ?>"
                    required="">
            </div>
        </div>
    <?php endif; ?>
    <input type="hidden" name="_mblr_hash" value="<?php echo $hash; ?>" />

    <?php if ($captcha) : ?>
        <div class="form-fields-group">
            <div class="g-recaptcha"
                 data-sitekey="<?php echo wpm_get_option('main.captcha_key'); ?>"
                 data-is-mobile="<?php echo MBLStats::isMobile() ? 1 : 0; ?>"
            ></div>
        </div>
    <?php endif; ?>

    <?php if ($userAgreement) : ?>
        <?php wpm_render_partial('user-agreement') ?>
    <?php endif; ?>

    <?php if (!$emailExists) : ?>
        <div class="result alert alert-warning ajax-result"></div>
    <?php else : ?>
        <div class="result alert alert-warning ajax-result" style="display: block;"><?php _e('Этот email уже используется', 'mbl'); ?></div>
        <?php do_action('autoreg_email_exists', $email); ?>
    <?php endif; ?>

    <input
        type="submit"
        class="mbr-btn btn-default btn-solid btn-green text-uppercase register-submit <?php echo $userAgreement ? 'mbl-uareq' : ''; ?>"
        <?php echo $userAgreement ? 'disabled="disabled"' : ''; ?>
        name="wpm-register-submit"
        value="<?php _e('Зарегистрироваться', 'mbl'); ?>"
    >
    <br>
    <div class="result alert alert-success text-center ajax-user-registered "></div>
    <?php do_action('autoreg_register_tab'); ?>
</form>
<script type="text/javascript">
    jQuery(function ($) {
        $('form[name=mblr-auto-register-form]').submit(function (e) {
            var $form = $(this),
                result = $form.find('.ajax-result'),
                registered_alert = $form.find('.ajax-user-registered'),
                $button = $('[name="wpm-register-submit"]');

            $button.val('<?php _e('Регистрация...', 'mbl'); ?>');
            $button.addClass('progress-button-active');
            $button.prop('disabled', 1);
            result.html('').css({'display' : 'none'});
            $.ajax({
                type     : 'POST',
                dataType : 'json',
                url      : ajaxurl,
                data     : {
                    'action' : 'mblr_auto_register_user_action',
                    'fields' : $form.serializeArray()
                },
                success  : function (data) {
                    if (data.registered === true) {
                      if (data.clear_utm) {
                        window.wpmClearUtmCookie && window.wpmClearUtmCookie();
                      }
                        registered_alert.html(data.message).fadeIn('fast', function() {
                            setTimeout(function () {
                                location.href = data.link;
                            }, 2000);
                        });
                    } else {
                        result.html(data.message).fadeIn();
                        $form.find('[name="wpm-register-submit"]').val('<?php _e('Зарегистрироваться', 'mbl'); ?>');
                    }

                },
                error    : function (data) {
                    result.html('<?php _e('Произошла ошибка.', 'mbl'); ?>').fadeIn();
                    $button.val('<?php _e('Зарегистрироваться', 'mbl'); ?>');
                    $button.prop('disabled', 0);
                }
            })
            .always(function(){
                $button.removeClass('progress-button-active');
            });
            e.preventDefault();
        });

        <?php if($isAutoConfirm): ?>
            $('.register-submit').click();
        <?php endif; ?>
    });
</script>
