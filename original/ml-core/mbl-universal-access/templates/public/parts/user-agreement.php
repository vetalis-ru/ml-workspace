<?php if (isset($required) && $required) : ?>
    <div class="user-agreement">
        <label>
            <input name="user_agreement" type="checkbox" class="user_agreement_input" tabindex="91"/>
            &nbsp;<?php _e('Принимаю', 'wpm'); ?>
            <a class="link"
               data-toggle="modal"
               data-target="#wpm_user_agreement"><?php echo wpm_get_option('user_agreement.registration_link_title', __('пользовательское соглашение', 'wpm')); ?></a>
        </label>
    </div>
    <script type="text/javascript">
        jQuery(function ($) {
            var $orderReview = $('#order_review');
            var $wslContainer = $('.mbl-access-sc-checkout');
            <?php if(wpm_option_is('user_agreement.registration_required', 'on')): ?>
            if (!$('#user-agreement-register-req .user_agreement_input').prop('checked')) {
                mbluaLockSubmit();
            }
            <?php endif ?>

            $(document).on('click', '#wpm_user_agreement_reject', function () {
                $('#wpm_user_agreement').modal('hide');
                $('.user_agreement_input').prop('checked', false);

                mbluaLockSubmit();

                return false;
            });
            $(document).on('click', '#wpm_user_agreement_accept', function () {
                $('#wpm_user_agreement').modal('hide');
                $('.user_agreement_input').prop('checked', true);

                mbluaUnlockSubmit();

                return false;
            });

            $(document).on('change', '.user_agreement_input', function () {
                if($(this).prop('checked')) {
                    mbluaUnlockSubmit()
                } else {
                    mbluaLockSubmit();
                }
            });

            function mbluaLockSubmit() {
                $orderReview.find('.cart-proceed-button').prop('disabled', true);
                $wslContainer.addClass('mbl-access-locked');
            }

            function mbluaUnlockSubmit() {
                var $offerCheckbox = $orderReview.find('#offer_checkbox_input');
                if (!$offerCheckbox.length || $offerCheckbox.prop('checked')) {
                    $orderReview.find('.cart-proceed-button').prop('disabled', false);
                    $wslContainer.removeClass('mbl-access-locked');
                }
            }
        });
    </script>
<?php else : ?>
    <div class="mbl-user-agreement-row">
        <a href="#wpm_user_agreement_text"
           data-toggle="modal"
           data-target="#wpm_user_agreement_text"
        ><?php echo wpm_get_option('user_agreement.login_link_title', __('Пользовательское соглашение', 'mbl')); ?></a>
    </div>
<?php endif; ?>
