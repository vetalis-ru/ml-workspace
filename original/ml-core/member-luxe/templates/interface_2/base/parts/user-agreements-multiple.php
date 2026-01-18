<?php
// Компонент для отображения множественных соглашений
// Можно использовать в формах регистрации и входа

// Определяем, какой номер соглашения использовать (по умолчанию - основное соглашение)
$agreement_num = isset($agreement_num) ? $agreement_num : '';
$agreement_key = $agreement_num ? 'user_agreement_' . $agreement_num : 'user_agreement';
$modal_id = $agreement_num ? 'wpm_user_agreement_' . $agreement_num : 'wpm_user_agreement';
$input_class = $agreement_num ? 'user_agreement_' . $agreement_num . '_input' : 'user_agreement_input';

// Получаем текст ссылки в зависимости от контекста (регистрация или вход)
$link_title_key = isset($is_login) && $is_login ? 'login_link_title' : 'registration_link_title';
$default_title = $agreement_num ? __('соглашение №' . $agreement_num, 'wpm') : __('пользовательское соглашение', 'wpm');
$link_title = wpm_get_option($agreement_key . '.' . $link_title_key, $default_title);
?>
<div class="user-agreement">
    <label>
        <input name="<?php echo $agreement_key; ?>" type="checkbox" class="<?php echo $input_class; ?>" tabindex="91" />
        &nbsp;<?php _e('Принимаю', 'wpm'); ?>
        <a class="link"
           data-toggle="modal"
           data-target="#<?php echo $modal_id; ?>"><?php echo $link_title; ?></a>
    </label>
</div>
<script type="text/javascript">
    jQuery(function ($) {
        // Обработчик для кнопки "Не принимаю"
        $(document).on('click', '#<?php echo $modal_id; ?>_reject', function(){
            $('#<?php echo $modal_id; ?>').modal('hide');
            $('.<?php echo $input_class; ?>').prop('checked', false);
            
            // Триггерим событие change для обновления состояния кнопок
            $('.<?php echo $input_class; ?>').trigger('change');

            return false;
        });
        
        // Обработчик для кнопки "Принимаю"
        $(document).on('click', '#<?php echo $modal_id; ?>_accept', function(){
            $('#<?php echo $modal_id; ?>').modal('hide');
            $('.<?php echo $input_class; ?>').prop('checked', true);
            
            // Триггерим событие change для обновления состояния кнопок
            $('.<?php echo $input_class; ?>').trigger('change');

            return false;
        });
        
        // Обработчик изменения чекбокса
        $(document).on('change', '.<?php echo $input_class; ?>', function() {
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
