<?php /** @var $statuses array */ ?>
<div style="display: none" id="mbl_hw_bulk_statuses">
    <select name="hw-bulk-action"
            data-mbl-select-2-html
            data-width="155"
            data-placeholder="<?php _e('Статус выбранных', 'mbl_admin'); ?>"
            data-minimum-results-for-search="-1">
        <option value=""></option>
        <?php foreach ($statuses as $status => $label) : ?>
            <option
                data-html='<?php wpm_render_partial('homework/status-icon', 'admin', compact('status')); ?><span><?php echo $label; ?></span>'
                value="<?php echo $status; ?>"><?php echo $label; ?></option>
        <?php endforeach; ?>
    </select>
    <span class="buttons">
        <a href="#"
           class="wpm-hw-status-bulk-change accept"
        ><i class="fa fa-check-square"></i></a>
        <a href="#" class="wpm-hw-status-bulk-change decline"><i class="fa fa-times-rectangle"></i></a>
    </span>
    <a href="#" class="cancel-button wpm-hw-status-bulk-cancel"><i class="fa fa-times-rectangle"></i></a>
</div>
