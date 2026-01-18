<?php /**
 * @global $label
 * @global $key
 */
?>
<label data-icon="<?= $key ?>">
    <button type="button">Выбрать иконку</button>
    <span class="fa<?= ' ' . wpm_get_option($key, 'fa-link') ?>"></span>
    <input type="hidden"
           name="main_options[<?php echo implode('][', explode('.', $key)); ?>]"
           value="<?= wpm_get_option($key, 'fa-link'); ?>">
</label>
