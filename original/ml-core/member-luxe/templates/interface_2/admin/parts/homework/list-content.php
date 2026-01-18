<?php /** @var $responses array */ ?>
<?php /** @var $statuses array */ ?>

<?php if (count($responses)) : ?>
    <?php $i = 0; ?>
    <?php foreach ($responses as $response) : ?>
        <?php $alternative = (++$i % 2) ? 'alternate' : ''; ?>
        <tr class="status-publish hentry iedit <?php echo $alternative ?> mbl-hw-content-row" data-toggle-row="<?php echo $response->id ?>">
            <td class="mbl-checkbox-td">
                <input type="checkbox" name="bulk_statuses[]" class="mbl-hw-status-checkbox" value="<?php echo $response->id; ?>">

                <?php if ($i == 1) : ?>
                    <?php wpm_render_partial('homework/stats-values', 'admin', compact('stats')) ?>
                    <?php wpm_render_partial('homework/bulk-select', 'admin', compact('statuses')); ?>
                <?php endif; ?>
            </td>
            <td class="mbl-hw-user">
                <?php wpm_render_partial('homework/status-icon', 'admin', ['status' => $response->is_archived ? 'archive' : $response->response_status]); ?>
                <?php echo wpm_get_user($response->user_id, 'display_name'); ?>
            </td>
            <?php if (wpm_option_is('hw.enabled_fields.type', 'on', 'on')) : ?>
                <td class="text-center mbl-hw-type">
                        <?php if ($response->mblPage->getMeta('homework_type') == 'test') : ?>
                            <i class="fa fa-list-alt mbl-homework-type" aria-hidden="true" data-mbl-tooltip title="<?php _e('Тест', 'mbl_admin'); ?>" data-original-title="<?php _e('Тест', 'mbl_admin'); ?>"></i>
                        <?php else: ?>
                            <i class="fa fa-question-circle mbl-homework-type" aria-hidden="true" data-mbl-tooltip title="<?php _e('Вопрос', 'mbl_admin'); ?>" data-original-title="<?php _e('Вопрос', 'mbl_admin'); ?>"></i>
                        <?php endif; ?>
                </td>
            <?php endif; ?>
            <?php if (wpm_option_is('hw.enabled_fields.date', 'on', 'on')) : ?>
                <td class="mbl-hw-date">
                    <?php echo date_i18n('d.m.y', strtotime($response->response_date)); ?>
                    <span><?php echo date_i18n('H:i', strtotime($response->response_date)); ?></span>
                </td>
            <?php endif; ?>
            <?php if (wpm_option_is('hw.enabled_fields.date', 'on', 'on')) : ?>
                <td class="mbl-hw-datetime">
                    <?php echo date_i18n('H:i', strtotime($response->response_date)); ?>
                </td>
            <?php endif; ?>
            <?php if (wpm_option_is('hw.enabled_fields.comments', 'on', 'on')) : ?>
                <td class="mbl-hw-comments">
                    <?php echo $response->reviews_nb; ?>
                </td>
            <?php endif; ?>
            <?php if (wpm_option_is('hw.enabled_fields.categories', 'on', 'on')) : ?>
                <td class="mbl-hw-categories">
                    <?php echo $response->mblPage->getAutotraining()->getName(); ?>
                </td>
            <?php endif; ?>
            <?php if (wpm_option_is('hw.enabled_fields.materials', 'on', 'on')) : ?>
                <td class="mbl-hw-materials">
                    <?php echo $response->mblPage->getTitle(); ?>
                </td>
            <?php endif; ?>
            <?php if (wpm_option_is('hw.enabled_fields.levels', 'on', 'on')) : ?>
                <td class="mbl-hw-levels">
                    <?php echo implode(', ', $response->mblPage->getAccessLevelNames()); ?>
                </td>
            <?php endif; ?>
            <?php do_action('wpm_admin_hw_list_content_row_after', $response); ?>
            <td class="mbl-row-toggler">
                <i class="fa fa-chevron-down mbl-state-closed"></i>
                <i class="fa fa-chevron-circle-up mbl-state-opened"></i>
            </td>
        </tr>
        <tr class="mbl-details-row" id="details-row-<?php echo $response->id ?>">
            <td colspan="<?php echo apply_filters('wpm_hw_colspan', $colspan); ?>" class="details-row">
                <div style="display: none" class="mbl-hw-details-row-inner"></div>
            </td>
        </tr>
    <?php endforeach; ?>
<?php else : ?>
    <tr>
        <td colspan="<?php echo apply_filters('wpm_hw_colspan', $colspan); ?>" class="wpm-no-results">
            <?php _e('hw_no_results.' . $curStatus, 'mbl_admin') ?>
            <?php wpm_render_partial('homework/stats-values', 'admin', compact('stats')) ?>
        </td>
    </tr>
<?php endif; ?>
