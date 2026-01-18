<div class="wpm-row mbl-access-pin-code-intervention">
    <?php
        wpm_render_partial('term-keys-period',
           'admin',
           [
               'durationName'     => 'main_options[mbl_access][pin_code_duration]',
               'durationId'       => 'mbl-access-duration-manual',
               'duration'         => wpm_get_option('mbl_access.pin_code_duration'),
               'unitsName'        => 'main_options[mbl_access][pin_code_units]',
               'unitsId'          => 'units-manual',
               'units'            => wpm_get_option('mbl_access.pin_code_units'),
               'isUnlimitedName'  => 'main_options[mbl_access][pin_code_is_unlimited]',
               'isUnlimitedValue' => wpm_option_is('mbl_access.pin_code_is_unlimited', 'on') ? 'on' : 'off',
           ])
    ?>
	
	<br>
	
	<?php wpm_render_partial('fields/checkbox', 'admin', array('label' => __('Применять только к форме регистрации', 'mbl_admin'), 'name' => 'main_options[mbl_access][pin_code_register_only]', 'value' => wpm_get_option('mbl_access.pin_code_register_only', 'off') )) ?>
	<?php wpm_render_partial('fields/checkbox', 'admin', array('label' => __('Скрыть процесс от пользователя', 'mbl_admin'), 'name' => 'main_options[mbl_access][pin_code_hide_from_user]', 'value' => wpm_get_option('mbl_access.pin_code_hide_from_user') )) ?>
	<?php wpm_render_partial('fields/checkbox', 'admin', array('label' => __('Перенаправлять на URL:', 'mbl_admin'), 'name' => 'main_options[mbl_access][pin_code_redirect]', 'value' => wpm_get_option('mbl_access.pin_code_redirect') )) ?>
	
	<input
		name="main_options[mbl_access][pin_code_redirect_url]"
		type="text"
		value="<?php echo wpm_get_option('mbl_access.pin_code_redirect_url'); ?>"
        style="width: 99%"
	>

</div>