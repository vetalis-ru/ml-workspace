<?php /** @var object $response */ ?>
<?php /** @var array $statuses */ ?>
<div class="panel with-nav-tabs panel-default mbl-hw-inner-panel">
    <div class="panel-heading">
        <ul class="nav nav-tabs mbl-homework-inner-tabs mbl-homework-<?php echo $response->mblPage->getPostMeta('homework_type'); ?>">
            <li class="active">
                <a href="#mbl_hw_inner_answer_<?php echo $response->id; ?>">
                    <?php do_action('mbl_admin_hw_response_first_tab_name', $response->mblPage); ?>
                    <i class="fa fa-exclamation-circle mbl-homework-type" aria-hidden="true"></i>
                    <span><?php _e('Ответ', 'mbl_admin'); ?></span>
                </a>
            </li>
            <li>
                <a href="#mbl_hw_inner_comments_<?php echo $response->id; ?>">
                    <i class="fa fa-comment mbl-homework-type" aria-hidden="true"></i>
                    <span><?php _e('Комментарии', 'mbl_admin'); ?></span>
                </a>
            </li>
            <li>
                <a href="#mbl_hw_inner_question_<?php echo $response->id; ?>">
                    <?php do_action('mbl_admin_hw_response_last_tab_name', $response->mblPage); ?>
                    <i class="fa fa-question-circle mbl-homework-type" aria-hidden="true"></i>
                    <span><?php _e('Вопрос', 'mbl_admin'); ?></span>
                </a>
            </li>
        </ul>

        <div class="mbl-hw-actions-row">
            <div class="mbl-hw-outer-link">
                <a
                    href="<?php echo $response->mblPage->getLink(); ?>"
                    data-mbl-tooltip
                    title="<?php _e('Открыть страницу материала', 'mbl_admin'); ?>"
                    target="_blank">
                    <i class="fa fa-external-link" aria-hidden="true"></i>
                </a>
            </div>
        </div>
    </div>
    <div class="panel-body">
        <div class="tab-content clearfix">
            <div class="tab-pane active" id="mbl_hw_inner_answer_<?php echo $response->id; ?>">
                <div class="page-content-wrap">
                    <?php wpm_render_partial('homework/item-answer', 'admin', compact('response', 'statuses')) ?>
                </div>
            </div>
            <div class="tab-pane mbl_hw_inner_comments" id="mbl_hw_inner_comments_<?php echo $response->id; ?>">
                <div class="page-content-wrap">
                    <?php wpm_render_partial('homework/item-comments', 'admin', compact('response')) ?>
                </div>
            </div>
            <div class="tab-pane" id="mbl_hw_inner_question_<?php echo $response->id; ?>">
                <div class="page-content-wrap">
                    <?php wpm_render_partial('homework/item-question', 'admin', compact('response')) ?>
                </div>
            </div>
        </div>
    </div>
</div>