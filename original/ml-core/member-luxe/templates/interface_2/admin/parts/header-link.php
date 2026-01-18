<div class="wpm-control-row">
    <label><?php _e('Ссылка', 'mbl_admin') ?><br>
        <input type="text"
               class="large-text"
               name="main_options[header_bg][<?php echo $name; ?>][link]"
               value="<?php echo wpm_get_option('header_bg.' . $name . '.link'); ?>"
        >
    </label>
</div>
<div class="wpm-control-row">
    <label><?php _e('Открывать в:', 'mbl_admin') ?></label>
    <label><input type="radio"
                  name="main_options[header_bg][<?php echo $name; ?>][link_target]"
                  value="self"
                 <?php echo wpm_option_is('header_bg.' . $name . '.link_target', 'self') ? 'checked' : ''; ?>
        ><?php _e('текущем окне', 'mbl_admin') ?></label>
    &nbsp;
    <label><input type="radio"
                  name="main_options[header_bg][<?php echo $name; ?>][link_target]"
                  value="blank"
                 <?php echo wpm_option_is('header_bg.' . $name . '.link_target', 'blank', 'blank') ? 'checked' : ''; ?>
        ><?php _e('новом окне', 'mbl_admin') ?></label>
</div>
