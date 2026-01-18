<?php /** @var string $key  */?>
<?php /** @var MBLPage $page */?>
<span class="mbl-autotraining-view-shift"
      <?php if (!isset($disableTooltip)) : ?>
          data-mbl-tooltip
          title="<?php _e($key . '.info.' . $page->getMeta($key . '_type', 'interval'), 'mbl_admin'); ?>"
      <?php endif; ?>
>
    <?php if ($page->getMeta($key . '_type', 'interval') == 'interval') : ?>
        <i class="fa fa-hourglass-half" aria-hidden="true"></i>
        <?php echo sprintf('%02d', wpm_get_days($page->getMeta($key))); ?><!--
        -->:<!--
        --><?php echo sprintf('%02d', intval(fmod($page->getMeta($key), 24))); ?><!--
        -->:<!--
        --><?php echo sprintf('%02d', wpm_get_minutes($page->getMeta($key))); ?>
    <?php elseif($page->getMeta($key . '_type', 'interval') == 'time') : ?>
        <i class="fa fa-hourglass-half" aria-hidden="true"></i>
        <?php echo sprintf('%02d', $page->getMeta($key . '_time_hours', '00')); ?><!--
        -->:<!--
        --><?php echo sprintf('%02d', $page->getMeta($key . '_time_minutes', '00')); ?>
    <?php elseif($page->getMeta($key . '_type', 'interval') == 'weekday') : ?>
        <i class="fa fa-calendar" aria-hidden="true"></i>
        <?php echo mbl_weekday($page->getMeta($key . '_weekday_day')); ?>
        &nbsp;&nbsp;
        <i class="fa fa-hourglass-half" aria-hidden="true"></i>
        <?php echo sprintf('%02d', $page->getMeta($key . '_weekday_hours', '00')); ?><!--
        -->:<!--
        --><?php echo sprintf('%02d', $page->getMeta($key . '_weekday_minutes', '00')); ?>
    <?php elseif($page->getMeta($key . '_type', 'interval') == 'day') : ?>
        <i class="fa fa-calendar" aria-hidden="true"></i>
        <?php echo sprintf('%02d', $page->getMeta($key . '_day_day', '00')); ?>
        &nbsp;&nbsp;
        <i class="fa fa-hourglass-half" aria-hidden="true"></i>
        <?php echo sprintf('%02d', $page->getMeta($key . '_day_hours', '00')); ?><!--
        -->:<!--
        --><?php echo sprintf('%02d', $page->getMeta($key . '_day_minutes', '00')); ?>
    <?php elseif($page->getMeta($key . '_type', 'interval') == 'date') : ?>
        <i class="fa fa-calendar" aria-hidden="true"></i>
        <?php echo $page->getMeta($key . '_date_date'); ?>
        &nbsp;&nbsp;
        <i class="fa fa-hourglass-half" aria-hidden="true"></i>
        <?php echo sprintf('%02d', $page->getMeta($key . '_date_hours', '00')); ?><!--
        -->:<!--
        --><?php echo sprintf('%02d', $page->getMeta($key . '_date_minutes', '00')); ?>
<?php endif; ?>
</span>
