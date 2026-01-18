<<?php echo isset($inline) && $inline ? 'span' : 'div class="wpm-row"'; ?>>
    <label>
        <input type="hidden" name="<?php echo $name; ?>" value="off">
        <input type="checkbox"
               name="<?php echo $name; ?>"
               id="<?php echo isset($id) ? $id : ''; ?>"
               class="option_<?php echo implode('_', explode('[', str_replace('][', '[', trim($name, '[]')))); ?>"
               value="on"
            <?php echo $value == 'on' ? ' checked' : ''; ?> />
        <?php echo $label; ?>
    </label>
</<?php echo isset($inline) && $inline ? 'span' : 'div'; ?>>