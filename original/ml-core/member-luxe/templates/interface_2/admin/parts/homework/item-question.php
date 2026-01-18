<?php /** @var object $response */ ?>
<?php do_action('mbl_admin_hw_before_question', $response->mblPage); ?>
<?php echo apply_filters('the_content', $response->mblPage->getHomeworkDescription()); ?>