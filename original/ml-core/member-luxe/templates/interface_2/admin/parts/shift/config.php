<?php $meta_key = isset($meta_key) ? $meta_key : 'page_meta' ?>
<div class="">
    <div class="row mbl-admin-shift-row">
        <div class="mbl-admin-shift-col col-lg-2 col-sm-4 mbl-admin-shift-interval">
            <label class="mbl-admin-shift-radio">
                <input
                    type="radio"
                    name="<?php echo $meta_key; ?>[<?php echo $key; ?>_type]"
                    value="interval"
                    <?php echo wpm_array_get($page_meta, $key . '_type', 'interval') == 'interval' ? 'checked' : ''; ?>
                />&nbsp;<?php _e('Интервал', 'mbl_admin'); ?>
                <i class="fa fa-info-circle wpm-info" data-mbl-tooltip title="<?php _e($key . '.info.interval', 'mbl_admin'); ?>"></i>
            </label>
            <label class="mbl-admin-shift-value">
                <i class="fa fa-hourglass-half" aria-hidden="true"></i>
                <input
                    type="text"
                    name="<?php echo $meta_key; ?>[<?php echo $key; ?>_days]"
                    id="<?php echo $key; ?>_days"
                    class="wpp_input_text"
                    data-days
                    size="4"
                    maxlength="4"
                    value="<?php echo wpm_get_days(wpm_array_get($page_meta, $key)); ?>"
                >
                <?php _e('дней', 'mbl_admin'); ?>
            </label>
            <label class="mbl-admin-shift-value">
                <input
                        type="text"
                        name="<?php echo $meta_key; ?>[<?php echo $key; ?>]"
                        id="<?php echo $key; ?>"
                        class="wpp_input_text"
                        data-hours
                        size="4"
                        maxlength="2"
                        value="<?php echo intval(fmod(wpm_array_get($page_meta, $key), 24)); ?>"
                >
                <?php _e('часов', 'mbl_admin'); ?>
            </label>
            <label class="mbl-admin-shift-value">
                <input
                       type="text"
                       name="<?php echo $meta_key; ?>[<?php echo $key; ?>_minutes]"
                       id="<?php echo $key; ?>_minutes"
                       class="wpp_input_text"
                       data-minutes
                       size="2"
                       maxlength="2"
                       value="<?php echo wpm_get_minutes(wpm_array_get($page_meta, $key)); ?>"
                >
                <?php _e('минут', 'mbl_admin'); ?>
            </label>
        </div>
        <div class="mbl-admin-shift-col col-lg-2 col-sm-4">
            <label class="mbl-admin-shift-radio">
                <input
                    type="radio"
                    name="<?php echo $meta_key; ?>[<?php echo $key; ?>_type]"
                    value="time"
                    <?php echo wpm_array_get($page_meta, $key . '_type', 'interval') == 'time' ? 'checked' : ''; ?>
                />&nbsp;<?php _e('Время', 'mbl_admin'); ?>
                <i class="fa fa-info-circle wpm-info" data-mbl-tooltip title="<?php _e($key . '.info.time', 'mbl_admin'); ?>"></i>
            </label>
            <div class="mbl-inline-holder">
                <label class="mbl-admin-shift-value">
                    <i class="fa fa-hourglass-half" aria-hidden="true"></i>
                    <input
                        type="text"
                        name="<?php echo $meta_key; ?>[<?php echo $key; ?>_time_hours]"
                        class="wpp_input_text"
                        size="2"
                        maxlength="2"
                        value="<?php echo wpm_array_get($page_meta, $key . '_time_hours', '00') ?>"
                    >
                </label>
                    :
                <label class="mbl-admin-shift-value mbl-pl-0">
                    <input
                        type="text"
                        name="<?php echo $meta_key; ?>[<?php echo $key; ?>_time_minutes]"
                        class="wpp_input_text"
                        size="2"
                        maxlength="2"
                        value="<?php echo wpm_array_get($page_meta, $key . '_time_minutes', '00') ?>"
                    >
                </label>
            </div>
        </div>
        <div class="mbl-admin-shift-col col-lg-2 col-sm-4">
            <label class="mbl-admin-shift-radio">
                <input
                    type="radio"
                    name="<?php echo $meta_key; ?>[<?php echo $key; ?>_type]"
                    value="weekday"
                    <?php echo wpm_array_get($page_meta, $key . '_type', 'interval') == 'weekday' ? 'checked' : ''; ?>
                />&nbsp;<?php _e('День', 'mbl_admin'); ?>
                <i class="fa fa-info-circle wpm-info" data-mbl-tooltip title="<?php _e($key . '.info.weekday', 'mbl_admin'); ?>"></i>
            </label>
            <label class="mbl-admin-shift-value">
                <i class="fa fa-calendar" aria-hidden="true"></i>
                <select name="<?php echo $meta_key; ?>[<?php echo $key; ?>_weekday_day]">
                    <?php foreach (mbl_weekdays_range() as $day) : ?>
                        <option
                            value="<?php echo $day; ?>"
                            <?php echo wpm_array_get($page_meta, $key . '_weekday_day') == $day ? 'selected' : ''; ?>
                        ><?php echo mbl_weekday($day); ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <div class="mbl-inline-holder">
                <label class="mbl-admin-shift-value">
                    <i class="fa fa-hourglass-half" aria-hidden="true"></i>
                    <input
                        type="text"
                        name="<?php echo $meta_key; ?>[<?php echo $key; ?>_weekday_hours]"
                        class="wpp_input_text"
                        size="2"
                        maxlength="2"
                        value="<?php echo wpm_array_get($page_meta, $key . '_weekday_hours', '00') ?>"
                    >
                </label>
                    :
                <label class="mbl-admin-shift-value mbl-pl-0">
                    <input
                        type="text"
                        name="<?php echo $meta_key; ?>[<?php echo $key; ?>_weekday_minutes]"
                        class="wpp_input_text"
                        size="2"
                        maxlength="2"
                        value="<?php echo wpm_array_get($page_meta, $key . '_weekday_minutes', '00') ?>"
                    >
                </label>
            </div>
        </div>
        <div class="mbl-admin-shift-col col-lg-2 col-sm-6">
            <label class="mbl-admin-shift-radio">
                <input
                    type="radio"
                    name="<?php echo $meta_key; ?>[<?php echo $key; ?>_type]"
                    value="day"
                    <?php echo wpm_array_get($page_meta, $key . '_type', 'interval') == 'day' ? 'checked' : ''; ?>
                />&nbsp;<?php _e('Число', 'mbl_admin'); ?>
                <i class="fa fa-info-circle wpm-info" data-mbl-tooltip title="<?php _e($key . '.info.day', 'mbl_admin'); ?>"></i>
            </label>
            <label class="mbl-admin-shift-value">
                <i class="fa fa-calendar" aria-hidden="true"></i>
                <select name="<?php echo $meta_key; ?>[<?php echo $key; ?>_day_day]">
                    <?php foreach (range(1, 31) as $day) : ?>
                        <option
                            value="<?php echo $day; ?>"
                            <?php echo wpm_array_get($page_meta, $key . '_day_day') == $day ? 'selected' : ''; ?>
                        ><?php echo $day; ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <div class="mbl-inline-holder">
                <label class="mbl-admin-shift-value">
                    <i class="fa fa-hourglass-half" aria-hidden="true"></i>
                    <input
                        type="text"
                        name="<?php echo $meta_key; ?>[<?php echo $key; ?>_day_hours]"
                        class="wpp_input_text"
                        size="2"
                        maxlength="2"
                        value="<?php echo wpm_array_get($page_meta, $key . '_day_hours', '00') ?>"
                    >
                </label>
                    :
                <label class="mbl-admin-shift-value mbl-pl-0">
                    <input
                        type="text"
                        name="<?php echo $meta_key; ?>[<?php echo $key; ?>_day_minutes]"
                        class="wpp_input_text"
                        size="2"
                        maxlength="2"
                        value="<?php echo wpm_array_get($page_meta, $key . '_day_minutes', '00') ?>"
                    >
                </label>
            </div>
        </div>
        <div class="mbl-admin-shift-col col-lg-4 col-sm-6">
            <label class="mbl-admin-shift-radio">
                <input
                    type="radio"
                    name="<?php echo $meta_key; ?>[<?php echo $key; ?>_type]"
                    value="date"
                    <?php echo wpm_array_get($page_meta, $key . '_type', 'interval') == 'date' ? 'checked' : ''; ?>
                />&nbsp;<?php _e('Дата', 'mbl_admin'); ?>
                <i class="fa fa-info-circle wpm-info" data-mbl-tooltip title="<?php _e($key . '.info.date', 'mbl_admin'); ?>"></i>
            </label>
            <label class="mbl-admin-shift-value">
                <i class="fa fa-calendar" aria-hidden="true"></i>
                <input type="text"
                       readonly="readonly"
                       name="<?php echo $meta_key; ?>[<?php echo $key; ?>_date_date]"
                       data-mbl-datepicker
                       value="<?php echo wpm_array_get($page_meta, $key . '_date_date') ?>"
                       maxlength="10"
                       size="10"/>
            </label>
            <div class="mbl-inline-holder">
                <label class="mbl-admin-shift-value">
                    <i class="fa fa-hourglass-half" aria-hidden="true"></i>
                    <input
                        type="text"
                        name="<?php echo $meta_key; ?>[<?php echo $key; ?>_date_hours]"
                        class="wpp_input_text"
                        size="2"
                        maxlength="2"
                        value="<?php echo wpm_array_get($page_meta, $key . '_date_hours', '00') ?>"
                    >
                </label>
                    :
                <label class="mbl-admin-shift-value mbl-pl-0">
                    <input
                        type="text"
                        name="<?php echo $meta_key; ?>[<?php echo $key; ?>_date_minutes]"
                        class="wpp_input_text"
                        size="2"
                        maxlength="2"
                        value="<?php echo wpm_array_get($page_meta, $key . '_date_minutes', '00') ?>"
                    >
                </label>
            </div>
        </div>
    </div>
</div>