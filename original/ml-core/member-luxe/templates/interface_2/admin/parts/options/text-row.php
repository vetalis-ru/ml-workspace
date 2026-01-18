<?php $default = isset($default) ? $default : '' ?>
<?php $rowClass = isset($rowClass) ? $rowClass : 'wpm-control-row' ?>
<?php $class = isset($class) ? $class : 'large-text' ?>
<?php $id = isset($id) ? $id : '' ?>
<div class="<?php echo $rowClass; ?>">
    <label><?php echo $label; ?>:<br>
        <input type="text"
               name="main_options[<?php echo implode('][', explode('.', $key)); ?>]"
               class="<?php echo $class; ?>"
               id="<?php echo $id; ?>"
               value="<?php echo wpm_get_option($key, $default); ?>">
    </label>
</div>