<label>
    <input type="hidden" name="<?php echo $name; ?>" value="off">
    <input type="checkbox"
           name="<?php echo $name; ?>"
           value="on"
        <?php echo $value == 'on' ? ' checked' : ''; ?> />
    <?php echo $label; ?>
</label>