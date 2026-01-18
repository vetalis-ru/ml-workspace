<?php /** @var object $response */ ?>
<?php /** @var MBLPage $mblPage */ ?>
<?php $mblPage = $response->mblPage; ?>
<div class="discussion-row bootstrap-admin-wrap wpm_homework_reviews_wrap">
    <ul class="discussion-list wpm_homework_reviews" id="wpm_homework_reviews_<?php echo $response->id; ?>">
        <?php $lastUserId = $mblPage->getHomeworkResponse('reviews.0.user_id', $response->user_id) ?: get_current_user_id(); ?>
        <?php foreach ($mblPage->getHomeworkResponse('reviews', $response->user_id) as $review) : ?>
            <?php wpm_render_partial('homework/review', 'admin', compact('review', 'lastUserId', 'response')) ?>
            <?php $lastUserId = wpm_array_get($review, 'user_id'); ?>
        <?php endforeach; ?>
    </ul>
    <?php wpm_render_partial('homework/review-form', 'admin', compact('mblPage', 'lastUserId', 'response')) ?>
</div>