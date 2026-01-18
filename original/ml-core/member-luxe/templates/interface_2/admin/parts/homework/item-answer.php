<?php /** @var object $response */ ?>
<div class=answer-meta>
    <div class="wpm-user-info">
        <i class="fa fa-user-circle-o" aria-hidden="true"></i>
        <a
            href="<?php echo admin_url('/user-edit.php?user_id=' . $response->user_id); ?>"
            data-mbl-tooltip
            title="<?php _e('Открыть профиль', 'mbl_admin'); ?>"
            target="_blank"><?php echo wpm_get_user($response->user_id, 'display_name'); ?></a>
    </div>
    <span class="meta-item date">
        <i class="fa fa-calendar"></i>
        <span id="response-date"><?php echo $response->mblPage->getHomeworkResponseDate($response->user_id); ?></span>
    </span>
    <span class="meta-item time">
        <i class="fa fa-clock-o"></i>
        <span id="response-time"><?php echo $response->mblPage->getHomeworkResponseTime($response->user_id); ?></span>
    </span>
    <span class="mbl-hw-actions">
        <select name="hw-action"
                data-mbl-select-2-html
                data-width="155"
                data-placeholder="<?php _e('Статус задания', 'mbl_admin'); ?>"
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
               class="wpm-hw-status-change accept"
               data-response-id="<?php echo $response->id; ?>"
               data-post-id="<?php echo $response->mblPage->getId(); ?>"
            ><i class="fa fa-check-square"></i></a>
            <a href="#" class="wpm-hw-status-change decline"><i class="fa fa-times-rectangle"></i></a>
        </span>
    </span>
</div>
<div class="content" id="homework-response-content">
    <?php do_action('mbl_admin_hw_before_response', $response); ?>
    <?php echo apply_filters('the_content', $response->mblPage->getHomeworkResponseContent($response->user_id)); ?>
    <?php echo $response->mblPage->getHomeworkResponseAttachmentsAdminHTML($response->user_id); ?>
</div>