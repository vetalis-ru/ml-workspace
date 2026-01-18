<div id="tab_mbl_access_options" class="tab universal-access-color-content">
  <div class="wpm-tab-content">
    <div class="wpm-inner-tabs">
      
      <ul class="wpm-inner-tabs-nav">
        <li><a href="#mbl_access_options_tab_1" class="universal-aсcess-color-tab">Настройки форм</a></li>
        <li><a href="#mbl_access_options_tab_2" class="universal-aсcess-color-tab">Вставить код в форму</a></li>
      </ul>
      
      <div id="mbl_access_options_tab_1" class="wpm-tab-content">
	
	      <?php wpm_render_partial('fields/checkbox', 'admin', array('label' => __('Применить к форме Регистрации', 'mbl_admin'), 'name' => 'main_options[mbl_access][apply_to_register_form]', 'value' => wpm_get_option('mbl_access.apply_to_register_form') )) ?>
	      <?php wpm_render_partial('fields/checkbox', 'admin', array('label' => __('Применить к форме Приема Платежей', 'mbl_admin'), 'name' => 'main_options[mbl_access][apply_to_peyments_form]', 'value' => wpm_get_option('mbl_access.apply_to_peyments_form') )) ?>
	      <?php wpm_render_partial('fields/checkbox', 'admin', array('label' => __('Применить к форме Авторегистрации', 'mbl_admin'), 'name' => 'main_options[mbl_access][apply_to_autoreg_form]', 'value' => wpm_get_option('mbl_access.apply_to_autoreg_form') )) ?>
  
        <br>
        <br>
        <br>
	      <?php wpm_render_partial('settings-save-button', 'common'); ?>
      </div>
      
      <div id="mbl_access_options_tab_2" class="wpm-tab-content">
        
        <h3>Формы входа:</h3>
        <div class="row">
          <div class="col-1">
	          <?php wpm_render_partial('fields/checkbox', 'admin', array('label' => __('Вход в школу', 'mbl_admin'), 'name' => 'main_options[mbl_access][codes_login_login_form]', 'value' => wpm_get_option('mbl_access.codes_login_login_form') )) ?>
	          <?php wpm_render_partial('fields/checkbox', 'admin', array('label' => __('Приема платежей', 'mbl_admin'), 'name' => 'main_options[mbl_access][codes_login_peyments_form]', 'value' => wpm_get_option('mbl_access.codes_login_peyments_form') )) ?>
	          <?php wpm_render_partial('fields/checkbox', 'admin', array('label' => __('Авторегистрации', 'mbl_admin'), 'name' => 'main_options[mbl_access][codes_login_autoreg_form]', 'value' => wpm_get_option('mbl_access.codes_login_autoreg_form') )) ?>
          </div>
          <div class="col-2">
            <textarea
              name="main_options[mbl_access][codes_login]"
              style="width: 100%"
              rows="7"
              placeholder="<?php _e('Вставьте код в это поле', 'mbl_admin') ?>"
            ><?php echo stripslashes(wpm_get_option('mbl_access.codes_login')); ?></textarea>
          </div>
        </div>
        
        <br>
        
        <h3>Формы регистрации:</h3>
        <div class="row">
          <div class="col-1">
		      <?php wpm_render_partial('fields/checkbox', 'admin', array('label' => __('Вход в школу', 'mbl_admin'), 'name' => 'main_options[mbl_access][codes_register_login_form]', 'value' => wpm_get_option('mbl_access.codes_register_login_form') )) ?>
		      <?php wpm_render_partial('fields/checkbox', 'admin', array('label' => __('Приема платежей', 'mbl_admin'), 'name' => 'main_options[mbl_access][codes_register_peyments_form]', 'value' => wpm_get_option('mbl_access.codes_register_peyments_form') )) ?>
		      <?php wpm_render_partial('fields/checkbox', 'admin', array('label' => __('Авторегистрации', 'mbl_admin'), 'name' => 'main_options[mbl_access][codes_register_autoreg_form]', 'value' => wpm_get_option('mbl_access.codes_register_autoreg_form') )) ?>
          </div>
          <div class="col-2">
            <textarea
              name="main_options[mbl_access][codes_register]"
              style="width: 100%"
              rows="7"
              placeholder="<?php _e('Вставьте код в это поле', 'mbl_admin') ?>"
            ><?php echo stripslashes(wpm_get_option('mbl_access.codes_register')); ?></textarea>
          </div>
        </div>
  
        <br>
	      <?php wpm_render_partial('settings-save-button', 'common'); ?>
      </div>
    
    </div>
  </div>
</div>
