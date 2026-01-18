<?php $durationId = isset($durationId) ? $durationId : 'duration' ?>
<?php $durationName = isset($durationName) ? $durationName : 'duration' ?>
<?php $duration = isset($duration) ? $duration : '12' ?>
<?php $unitsId = isset($unitsId) ? $unitsId : 'units' ?>
<?php $unitsName = isset($unitsName) ? $unitsName : 'units' ?>
<?php $units = isset($units) ? $units : 'months' ?>
<?php $isUnlimitedName = isset($isUnlimitedName) ? $isUnlimitedName : 'is_unlimited' ?>
<?php $isUnlimitedValue = isset($isUnlimitedValue) ? $isUnlimitedValue : 'off' ?>
<?php $isUnlimitedId = isset($isUnlimitedId) ? $isUnlimitedId : '' ?>
<?php $isUnlimitedClass = 'option_' . implode('_', explode('[', str_replace('][', '[', trim($isUnlimitedName, '[]')))); ?>
<span class="mbl_term_keys_period">
    <label for="<?php echo $durationId; ?>"><?php _e('Время действия', 'mbl_admin'); ?>&nbsp;</label>
    <span class="wrap mbl_term_keys_period_inner">
        <input type="number" size="2" min="1" max="99" id="<?php echo $durationId; ?>" name="<?php echo $durationName; ?>" class="mbl_term_period" value="<?php echo $duration; ?>" maxlength="2" style="width: 100px">
        <select name="<?php echo $unitsName; ?>" id="<?php echo $unitsId; ?>" class="mbl_term_period">
            <option value="months" <?php echo $units == 'months' ? 'selected' : ''; ?>><?php _e('месяцев', 'mbl_admin'); ?></option>
            <option value="days" <?php echo $units == 'days' ? 'selected' : ''; ?>><?php _e('дней', 'mbl_admin'); ?></option>
        </select>
        &nbsp;
        &nbsp;
        <?php wpm_render_partial('fields/checkbox', 'admin', ['label' => __('Неограниченный доступ', 'mbl_admin'), 'name' => $isUnlimitedName, 'value' => $isUnlimitedValue, 'id' => $isUnlimitedId, 'inline' => true]) ?>
    </span> 
</span>
<script>
    jQuery(function($) {
        $('.<?php echo $isUnlimitedClass?>')
            .on('change', function () {
                updateUnlimitedTermOption($(this));
            })
            .each(function () {
                updateUnlimitedTermOption($(this));
            });
        
        function updateUnlimitedTermOption($checkbox) {
            $checkbox.closest('.mbl_term_keys_period').find('.mbl_term_period').prop('disabled', $checkbox.prop('checked'));
        }
    })
</script>
