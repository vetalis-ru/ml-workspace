<?php $standalone = isset($standalone) ? $standalone : false; ?>
<form class="checkout woocommerce-checkout" name="checkout" action="<?php echo esc_url(wc_get_checkout_url()); ?>"
      method="post" id="chekout-register-form">
    <?php if (wpm_option_is('mblp.new_clients.surname', 'on') || wpm_option_is('mblp.new_clients.name', 'on') || wpm_option_is('mblp.new_clients.patronymic', 'on')) : ?>
        <div class="form-fields-group">
            <?php if (wpm_option_is('mblp.new_clients.surname', 'on')) : ?>
                <div class="form-group form-icon form-icon-user">
                    <input type="text" name="billing_last_name" value="" class="form-control"
                           placeholder="<?php _e('Фамилия', 'mbl'); ?>" required="">
                </div>
            <?php endif; ?>
            <?php if (wpm_option_is('mblp.new_clients.name', 'on')) : ?>
                <div class="form-group form-icon form-icon-user">
                    <input type="text" name="billing_first_name" value="" class="form-control"
                           placeholder="<?php _e('Имя', 'mbl'); ?>" required="">
                </div>
            <?php endif; ?>
            <?php if (wpm_option_is('mblp.new_clients.patronymic', 'on')) : ?>
                <div class="form-group form-icon form-icon-user">
                    <input type="text" name="patronymic" value="" class="form-control"
                           placeholder="<?php _e('Отчество', 'mbl'); ?>" required="">
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <div class="form-fields-group">
        <div class="form-group form-icon form-icon-email">
            <input type="email" name="billing_email" value="" class="form-control"
                   placeholder="<?php _e('Email', 'mbl'); ?>" required="">
        </div>
        <?php if (wpm_option_is('mblp.new_clients.phone', 'on')) : ?>
            <div class="form-group form-icon form-icon-phone">
                <input type="text" name="billing_phone" value="" class="form-control"
                       placeholder="<?php _e('Телефон', 'mbl'); ?>" required="">
            </div>
        <?php endif; ?>
    </div>


    <div class="form-fields-group">

        <?php if (wpm_option_is('mblp.new_clients.login', 'on')) : ?>
            <div class="form-group form-icon form-icon-user">
                <input type="text" name="account_username" value="" class="form-control"
                       placeholder="<?php _e('Желаемый логин', 'mbl'); ?>" required="">
            </div>
        <?php endif; ?>

        <?php if (wpm_option_is('mblp.new_clients.pass', 'on')) : ?>
            <div class="form-group form-icon form-icon-lock">
                <input type="password" name="account_password" value="" class="form-control"
                       placeholder="<?php _e('Желаемый пароль', 'mbl'); ?>" required="">
            </div>
        <?php endif; ?>
    </div>

    <?php if (wpm_option_is('mblp.new_clients.comment', 'on')) { ?>
        <div class="form-fields-group">
            <div class="form-group form-icon form-icon-comment">
                <textarea name="order_comments"
                          id="order_comments"
                          class="form-control"
                          placeholder="<?php echo wpm_get_option('mblp_texts.order_comment', __('Комментарий к заказу', 'mbl_admin')); ?>"
                          rows="6" cols="5"></textarea>
            </div>
        </div>
    <?php } ?>

    <div id="order_review" class="woocommerce-checkout-review-order">
        <?php do_action('woocommerce_checkout_order_review'); ?>

        <?php if (wpm_option_is('user_agreement.enabled_login', 'on') && !wpm_option_is('user_agreement.login_required', 'on')) : ?>
            <div id="user-agreement-login" style="display: none;">
                <?php mbl_access_render_partial('parts/user-agreement'); ?>
            </div>
        <?php endif; ?>
        <?php if (wpm_option_is('user_agreement.enabled_registration', 'on') && !wpm_option_is('user_agreement.registration_required', 'on')) : ?>
            <div id="user-agreement-register">
                <?php mbl_access_render_partial('parts/user-agreement'); ?>
            </div>
        <?php endif; ?>
    </div>

</form>
<br>

<?php if (wpm_get_option('mbl_access.codes_register_peyments_form') == 'on') {
    echo '<div class="mbl-access-sc mbl-access-sc-checkout">';
    echo do_shortcode(stripslashes(wpm_get_option('mbl_access.codes_register')));
    echo '</div>';
    ?><br><?php
} ?>

<div class="mbl-user-agreement-row">
    <a href="#"
       onclick="activateLoginTab()"
       class="helper-link"
    >
        <?php echo wpm_get_option('mbl_access.reg_link_text') ?>
    </a>
</div>

<script>
    var $orderReview = $('#order_review');
    var $wslContainer = $('.mbl-access-sc-checkout');
    var $reqLoginAgreement = $orderReview.find('#user-agreement-login-req');
    var $reqRegisterAgreement = $orderReview.find('#user-agreement-register-req');

    $(document.body).on('checkout_error', function (event, message) {
        let email = $(message).find('[data-already-registered]');
        if (email.length) {
            $('.checkout-login .status').html(message);
            $('#checkout-login-field').val(email.data('already-registered'));
            $('#checkout-login-tab').click();
            $('#chekout-register-form .woocommerce-NoticeGroup').remove();
        }

        if ($reqRegisterAgreement.length) {
            $('[name="user_agreement"]').prop('checked', false).trigger('change');
            $('#offer_checkbox_input').prop('checked', false).trigger('change');
            lockSubmit();
        }
    });

    function activateLoginTab() {
        $('#checkout-login-tab').click();
    }

    function lockSubmit() {
        $orderReview.find('.cart-proceed-button').prop('disabled', true);
        $wslContainer.addClass('mbl-access-locked');
    }

    function unlockSubmit() {
        var $offerCheckbox = $orderReview.find('#offer_checkbox_input');
        if (!$offerCheckbox.length || $offerCheckbox.prop('checked')) {
            $orderReview.find('.cart-proceed-button').prop('disabled', false);
            $wslContainer.removeClass('mbl-access-locked');
        }
    }

    $(document).on('shown.bs.tab', 'a[data-toggle="tab"]', function (e) {
        $('[name="user_agreement"]').prop('checked', false).trigger('change');
        $('#offer_checkbox_input').prop('checked', false).trigger('change');

        if (e.target.id == 'checkout-login-tab') {
            $orderReview.appendTo('#chekout-login-form')
            $('#user-agreement-login-req, #user-agreement-login').show();
            $('#user-agreement-register-req, #user-agreement-register').hide();

            if ($reqLoginAgreement.length) {
                lockSubmit();
            } else {
                unlockSubmit();
            }
        }
        if (e.target.id == 'checkout-register-tab') {
            $orderReview.appendTo('#chekout-register-form')
            $('#user-agreement-login-req, #user-agreement-login').hide();
            $('#user-agreement-register-req, #user-agreement-register').show();

            if ($reqRegisterAgreement.length) {
                lockSubmit();
            } else {
                unlockSubmit();
            }
        }
    })

</script>
