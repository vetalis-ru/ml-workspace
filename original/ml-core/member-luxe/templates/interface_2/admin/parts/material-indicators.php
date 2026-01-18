<div class="wpm-row">
    <label>
        <input type="hidden" name="page_meta[indicators][individual_indicators]" value="0">
        <input
            type="checkbox"
            id="individual_indicators_checkbox"
            name="page_meta[indicators][individual_indicators]"
            value="1"
            <?php echo wpm_array_get($page_meta, 'indicators.individual_indicators', 0) ? 'checked="checked"' : ''; ?>
        >
        <?php _e('Индивидуальные настройки индикаторов данного материала', 'mbl_admin') ?></label>
</div>
<div
    style="padding-left: 25px; <?php echo wpm_array_get($page_meta, 'indicators.individual_indicators', 0) ? '' : 'display:none;'; ?>"
    class="wpm-control-row"
    id="individual_indicators">
    <div class="wpm-row">
        <label>
            <input type="hidden" name="page_meta[indicators][number_show]" value="0">
            <input
                type="checkbox"
                name="page_meta[indicators][number_show]"
                value="1"
                <?php echo wpm_array_get($page_meta, 'indicators.number_show', 0) ? 'checked="checked"' : ''; ?>
            >
            <?php _e('Отображать порядковый номер материала', 'mbl_admin') ?></label>
    </div>

    <div class="wpm-row">
        <label>
            <input type="hidden" name="page_meta[indicators][content_type_show]" value="0">
            <input
                type="checkbox"
                name="page_meta[indicators][content_type_show]"
                value="1"
                <?php echo wpm_array_get($page_meta, 'indicators.content_type_show', 0) ? 'checked="checked"' : ''; ?>
            >
            <?php _e('Отображать тип контента', 'mbl_admin') ?></label>
    </div>

    <div class="wpm-row">
        <label>
            <input type="hidden" name="page_meta[indicators][homework_status_show]" value="0">
            <input
                type="checkbox"
                name="page_meta[indicators][homework_status_show]"
                value="1"
                <?php echo wpm_array_get($page_meta, 'indicators.homework_status_show', 0) ? 'checked="checked"' : ''; ?>
            >
            <?php _e('Отображать статус ДЗ', 'mbl_admin') ?></label>
    </div>

    <div class="wpm-row">
        <label>
            <input type="hidden" name="page_meta[indicators][date_show]" value="0">
            <input
                type="checkbox"
                name="page_meta[indicators][date_show]"
                value="1"
                <?php echo wpm_array_get($page_meta, 'indicators.date_show', 0) ? 'checked="checked"' : ''; ?>
            >
            <?php _e('Отображать дату', 'mbl_admin') ?></label>
    </div>

    <div class="wpm-row">
        <label>
            <input type="hidden" name="page_meta[indicators][comments_show]" value="0">
            <input
                type="checkbox"
                name="page_meta[indicators][comments_show]"
                value="1"
                <?php echo wpm_array_get($page_meta, 'indicators.comments_show', 0) ? 'checked="checked"' : ''; ?>
            >
            <?php _e('Отображать комментарии', 'mbl_admin') ?></label>
    </div>

    <div class="wpm-row">
        <label>
            <input type="hidden" name="page_meta[indicators][views_show]" value="0">
            <input
                type="checkbox"
                name="page_meta[indicators][views_show]"
                value="1"
                <?php echo wpm_array_get($page_meta, 'indicators.views_show', 0) ? 'checked="checked"' : ''; ?>
            >
            <?php _e('Отображать количество просмотров', 'mbl_admin') ?></label>
    </div>

    <div class="wpm-row">
        <label>
            <input type="hidden" name="page_meta[indicators][access_show]" value="0">
            <input
                type="checkbox"
                name="page_meta[indicators][access_show]"
                value="1"
                <?php echo wpm_array_get($page_meta, 'indicators.access_show', 0) ? 'checked="checked"' : ''; ?>
            >
            <?php _e('Отображать доступность', 'mbl_admin') ?></label>
    </div>
</div>

<script>
    jQuery(function ($) {
        $(document).on('change', '#individual_indicators_checkbox', function() {
            var $holder = $('#individual_indicators');

            if($(this).prop('checked')) {
                $holder.slideDown();
            } else {
                $holder.slideUp();
            }
        });
    });
</script>