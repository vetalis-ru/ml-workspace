<div class="user-agreement">
    <label>
        <input name="user_agreement" type="checkbox" class="user_agreement_input" tabindex="91" />
        &nbsp;<?php _e('Принимаю', 'wpm'); ?>
        <a class="link"
           data-toggle="modal"
           data-target="#wpm_user_agreement"><?php echo wpm_get_option('user_agreement.registration_link_title', __('пользовательское соглашение', 'wpm')); ?></a>
    </label>
</div>
<script type="text/javascript">
    jQuery(function ($) {
        $(document).on('click', '#wpm_user_agreement_reject', function(){
            $('#wpm_user_agreement').modal('hide');
            $('.user_agreement_input').prop('checked', false);
            
            // Триггерим событие change для обновления состояния кнопок
            $('.user_agreement_input').trigger('change');

            return false;
        });
        $(document).on('click', '#wpm_user_agreement_accept', function(){
            $('#wpm_user_agreement').modal('hide');
            $('.user_agreement_input').prop('checked', true);
            
            // Триггерим событие change для обновления состояния кнопок
            $('.user_agreement_input').trigger('change');

            return false;
        });
        $(document).on('change', '.user_agreement_input', function() {
            var $this = $(this),
                $form = $this.closest('form');
            
            // Проверяем, все ли обязательные соглашения приняты
            var allChecked = true;
            $form.find('input[name^="user_agreement"]').each(function() {
                if (!$(this).prop('checked')) {
                    allChecked = false;
                }
            });
            
            // Обновляем состояние кнопок с классом mbl-uareq
            $form.find('.register-submit.mbl-uareq, .wpm-sign-in-button.mbl-uareq').prop('disabled', !allChecked);
            $form.find('.mbl-access-sc')[allChecked ? 'removeClass' : 'addClass']('mbl-access-locked');
        });
    });
</script>
