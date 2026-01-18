<p class="user-agreement">
    <label>
        <input name="user_agreement" type="checkbox" id="user_agreement_input" tabindex="91" />
        &nbsp;<?php _e('Принимаю', 'wpm'); ?>
        <a class="link"
           data-toggle="modal"
           data-target="#wpm_user_agreement"><?php echo wpm_get_option('user_agreement.registration_link_title', __('пользовательское соглашение', 'wpm')); ?></a>
    </label>
</p>
<script type="text/javascript">
    jQuery(function ($) {
        $(document).on('click', '#wpm_user_agreement_reject', function(){
            $('#wpm_user_agreement').modal('hide');
            $('#user_agreement_input').prop('checked', false);
            $('#register-submit').prop('disabled', true);

            return false;
        });
        $(document).on('click', '#wpm_user_agreement_accept', function(){
            $('#wpm_user_agreement').modal('hide');
            $('#user_agreement_input').prop('checked', true);
            $('#register-submit').prop('disabled', false);

            return false;
        });
        $(document).on('change', '#user_agreement_input', function() {
            $('#register-submit').prop('disabled', !$(this).prop('checked'));
        });
    });
</script>