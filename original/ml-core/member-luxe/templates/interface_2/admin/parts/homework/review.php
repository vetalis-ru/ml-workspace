<?php /** @var object $response */ ?>
<?php $attachments = UploadHandler::getHomeworkReviewAttachmentsAdminHtml(wpm_array_get($review, 'id'), wpm_array_get($review, 'user_id')); ?>
<li class="comment-item <?php echo wpm_array_get($review, 'user_id') == $response->user_id ? '' : 'answer'; ?>">
    <article class="comment">
        <div class="comment-meta-wrap">
            <div class="comment-meta">
                <span class="comment-author-name">
                    <i class="fa fa-user-circle-o" aria-hidden="true"></i>
                    <?php echo wpm_get_user(wpm_array_get($review, 'user_id'), 'display_name'); ?>
                </span>
                <span class="date"><i class="fa fa-calendar"></i> <?php echo date_format(wpm_array_get($review, 'date_object'), 'd.m.Y'); ?></span>
                <span class="time"><i class="fa fa-clock-o"></i> <?php echo date_format(wpm_array_get($review, 'date_object'), 'H:i'); ?></span>
            </div>
        </div>
        <div class="comment-content">
            <div class="comment-text clearfix"><?php echo apply_filters('the_content', stripslashes(wpm_array_get($review, 'content', ''))); //echo apply_filters('the_content', wpautop(stripslashes(wpm_array_get($review, 'content', '')))); -222- ?></div>
            <?php echo $attachments; ?>
        </div>
    </article>
</li>