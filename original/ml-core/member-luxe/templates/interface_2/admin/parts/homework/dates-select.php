<input type="hidden" id="mbl_stats_date_from_input" name="date_from" value="<?php echo date('d.m.y', current_time('timestamp') - 7*24*60*60); ?>">
<input type="hidden" id="mbl_stats_date_to_input" name="date_to" value="<?php echo date('d.m.y', current_time('timestamp')); ?>">
<div class="select2 select2-container select2-container--default select2-container--above mbl-dates-select mbl-border-opened" dir="ltr" id="mbl-hw-dates">
    <span class="selection">
        <span class="select2-selection select2-selection--single" role="combobox" aria-haspopup="true">
            <span class="select2-selection__rendered" role="textbox" aria-readonly="true" id="mbl-dates-placeholder"><?php echo date('d.m.y', current_time('timestamp') - 7*24*60*60); ?> - <?php echo date('d.m.y', current_time('timestamp')); ?></span>
            <span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span>
        </span>
    </span>
    <div class="mbl-dates-popup panel panel-default">
        <div class="date-title"><?php _e('Дата', 'mbl_admin'); ?></div>
        <div class="flex-row flex-wrap">
            <div class="flex-col-6 pr-10">
                <div class="mbl-date-label"><?php _e('Дата начала', 'mbl_admin'); ?></div>
                <div id="mbl_stats_date_from" class="mbl-date" data-start-date="<?php echo date('d.m.Y', current_time('timestamp') - 7*24*60*60); ?>"></div>
            </div>
            <div class="flex-col-6 pr-10">
                <div class="mbl-date-label"><?php _e('Дата окончания', 'mbl_admin'); ?></div>
                <div id="mbl_stats_date_to" class="mbl-date"></div>
            </div>
        </div>
        <div class="mbl-dates-buttons">
            <a href="#" class="close mbl-date-close"><?php _e('Отмена', 'mbl_admin'); ?></a>
            <a href="#" class="mbl-date-submit button button-primary"><?php _e('Применить', 'mbl_admin'); ?></a>
        </div>
    </div>
</div>