<<?php echo isset($inline) && $inline ? 'span' : 'div class="wpm-row"'; ?>>
    <label>
        <input type="hidden" name="main_options[<?php echo implode('][', explode('.', $key)); ?>]" value="off">
        <input type="checkbox"
               name="main_options[<?php echo implode('][', explode('.', $key)); ?>]"
               class="option_<?php echo $key; ?>"
               value="on"
            <?php echo wpm_option_is($key, 'on', isset($default) ? $default : null) ? ' checked' : ''; ?> />
        <?php echo $label; ?>
    </label>
</<?php echo isset($inline) && $inline ? 'span' : 'div'; ?>>