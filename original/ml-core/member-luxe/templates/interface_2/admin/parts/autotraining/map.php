<?php /** @var MBLCategory $category */ ?>
<?php /** @var MBLPage[] $pages */ ?>
<div class="wrap nosubsub mbl-autotraining-map">
    <div id="col-container">
        <div class="col-wrap">
            <h2>
                <?php _e('Обзор автотренинга', 'mbl_admin'); ?>
                «<a
                    target="_blank"
                    class="mbl-autotraining-map-title"
                    href="<?php echo admin_url('/edit-tags.php?action=edit&taxonomy=wpm-category&tag_ID=' . $category->getTermId() . '&post_type=wpm-page'); ?>"
                ><?php echo $category->getName(); ?></a>»
            </h2>

            <div class="flex-row">
                <div class="mbl-map-subtitle">
                    <?php do_action('mbl_autotraining_map_subtitle', $category); ?>
                </div>
                <div class="mbl-current-time">
                    <?php _e('Текущее время системы', 'mbl_admin'); ?>:
                    <span class="nowrap">
                        <i class="fa fa-calendar" aria-hidden="true"></i>
                        <?php echo date('d.m.y', current_time('timestamp')) ?>
                    </span>
                    <span class="nowrap">
                        <i class="fa fa-hourglass-half" aria-hidden="true"></i>
                        <span
                            id="mbl_clock"
                            data-now="<?php echo current_time('timestamp') ?>"
                        ><?php echo date('H:i', current_time('timestamp')) ?></span>
                    </span>
                </div>
            </div>

            <table class="wp-list-table widefat fixed striped pages">
                <thead>
                <tr>
                    <th scope="col" class="manage-column">
                        <i class="fa fa-arrow-circle-o-up"></i>
                        <?php _e('Смещение Материала', 'mbl_admin'); ?>
                    </th>
                    <th scope="col" style="width: 50px" class="manage-column"># <?php _e('Номер', 'mbl_admin'); ?></th>
                    <th scope="col" class="manage-column">
                        <i class="fa fa-arrow-circle-o-down"></i>
                        <?php _e('Смещение Автопроверки ДЗ', 'mbl_admin'); ?>
                    </th>
                    <th scope="col"  style="width: 40px" class="manage-column">
                        <i class="fa fa-flask" aria-hidden="true"></i>
                        <?php _e('Тип', 'mbl_admin'); ?>
                    </th>
                    <th scope="col" class="manage-column" style="width: 75px;">
                        <i class="fa fa-check-square-o" aria-hidden="true"></i>
                        <?php _e('Проверка', 'mbl_admin'); ?>
                    </th>
                    <th scope="col" class="manage-column column-primary">
                        <i class="fa fa-file-text-o" aria-hidden="true"></i>
                        <?php _e('Название Материала', 'mbl_admin'); ?>
                    </th>
                    <th scope="col" class="manage-column">
                        <i class="fa fa-folder-open-o" aria-hidden="true"></i>
                        <?php _e('Рубрика', 'mbl_admin'); ?>
                    </th>
                    <th scope="col" class="manage-column">
                        <i class="fa fa-sitemap" aria-hidden="true"></i>
                        <?php _e('Уровень доступа', 'mbl_admin'); ?>
                    </th>
                    <th scope="col" class="manage-column" style="width:30px;">
                        <i class="fa fa-comment-o fa-md" aria-hidden="true" data-mbl-tooltip title="<?php _e('Комментарии', 'mbl_admin'); ?>"></i>
                    </th>
                    <th scope="col" class="manage-column" style="width:30px;">
                        <i class="fa fa-eye fa-md" aria-hidden="true" data-mbl-tooltip title="<?php _e('Просмотры', 'mbl_admin'); ?>"></i>
                    </th>
                    <th scope="col" class="manage-column" style="width:30px;">
                        <i class="fa fa-external-link fa-md" aria-hidden="true" data-mbl-tooltip title="<?php _e('Открыть страницу материала', 'mbl_admin'); ?>"></i>
                    </th>
                    <th scope="col" class="manage-column" style="width:30px;">
                        <i class="fa fa-pencil-square-o fa-md" aria-hidden="true" data-mbl-tooltip title="<?php _e('Редактировать страницу материала', 'mbl_admin'); ?>"></i>
                    </th>
                </tr>
                </thead>
                <tbody>
                <?php $i = 0; ?>
                <?php foreach ($pages as $page) : ?>
                    <tr>
                        <td data-colname="<?php _e('Смещение Материала', 'mbl_admin'); ?>">
                            <?php if ($i) : ?>
                                <?php if ($page->getMeta('shift_is_on')) : ?>
                                    <?php wpm_render_partial('autotraining/shift', 'admin', array('page' => $page, 'key' => 'shift_value')) ?>
                                <?php endif; ?>
                            <?php else : ?>
                                <?php wpm_render_partial('autotraining/shift_first_material', 'admin', array('page' => $page, 'key' => 'shift_value', 'category' => $category)) ?>
                            <?php endif; ?>
                        </td>
                        <td><span class="mbl-autotraining-number"><?php echo ++$i; ?></span></td>
                        <td>
                            <?php if ($page->hasHomework() && $page->getMeta('confirmation_method') == 'auto_with_shift') : ?>
                                <?php wpm_render_partial('autotraining/shift', 'admin', array('page' => $page, 'key' => 'homework_shift_value')) ?>
                            <?php endif; ?>
                        </td>
                        <td data-colname="<?php _e('Смещение Автопроверки ДЗ', 'mbl_admin'); ?>">
                            <?php if ($page->hasHomework()) : ?>
                                <?php if ($page->getMeta('homework_type') == 'test') : ?>
                                    <i class="fa fa-list-alt mbl-homework-type" aria-hidden="true" data-mbl-tooltip title="<?php _e('Тест', 'mbl_admin'); ?>"></i>
                                <?php else : ?>
                                    <i class="fa fa-question-circle mbl-homework-type" aria-hidden="true" data-mbl-tooltip title="<?php _e('Вопрос', 'mbl_admin'); ?>"></i>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                        <td data-colname="<?php _e('Тип', 'mbl_admin'); ?>">
                            <?php if ($page->hasHomework()) : ?>
                                <?php if ($page->getMeta('confirmation_method') == 'auto_with_shift') : ?>
                                    <i class="fa <?php echo apply_filters('homework_check_icon', 'fa-cogs', $page->getMeta('homework_type') ); ?> mbl-homework-type" aria-hidden="true" data-mbl-tooltip title="<?php _e('Автоматически со смещением', 'mbl_admin'); ?>"></i>
                                    <i class="fa fa-hourglass-half mbl-homework-type" aria-hidden="true" data-mbl-tooltip title="<?php _e('Автоматически со смещением', 'mbl_admin'); ?>"></i>
                                <?php elseif ($page->getMeta('confirmation_method') == 'auto') : ?>
                                    <i class="fa <?php echo apply_filters('homework_check_icon', 'fa-cogs', $page->getMeta('homework_type') ); ?> mbl-homework-type" aria-hidden="true" data-mbl-tooltip title="<?php _e('Автоматически', 'mbl_admin'); ?>"></i>
                                <?php elseif ($page->getMeta('confirmation_method') == 'manually') : ?>
                                    <i class="fa fa-hand-pointer-o mbl-homework-type" aria-hidden="true" data-mbl-tooltip title="<?php _e('Вручную', 'mbl_admin'); ?>"></i>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                        <td class="column-primary" data-colname="<?php _e('Название Материала', 'mbl_admin'); ?>">
                            <?php echo $page->getTitle(); ?>
                            <button type="button" class="toggle-row"><span class="screen-reader-text"><?php _e('Show more details'); ?></span></button>
                        </td>
                        <td data-colname="<?php _e('Рубрика', 'mbl_admin'); ?>">
                            <?php echo $category->getName(); ?>
                        </td>
                        <td data-colname="<?php _e('Уровень доступа', 'mbl_admin'); ?>">
                            <?php echo implode(', ', $page->getAccessLevelNames()); ?>
                        </td>
                        <td><?php echo $page->getCommentsNumber(); ?></td>
                        <td><?php echo $page->getViewsNumber(); ?></td>
                        <td data-colname="<?php _e('Перейти', 'mbl_admin'); ?>">
                            <a
                                href="<?php echo wpm_material_link($category->getWpCategory(), $page->getPost()); ?>"
                                data-mbl-tooltip
                                title="<?php _e('Открыть страницу материала', 'mbl_admin'); ?>"
                                target="_blank"
                                class="mbl-autotraining-link"
                            ><i class="fa fa-external-link fa-md" aria-hidden="true"></i></a>
                        </td>
                        <td data-colname="<?php _e('Редактировать', 'mbl_admin'); ?>">
                            <a
                                href="<?php echo $page->getEditLink(); ?>"
                                data-mbl-tooltip
                                title="<?php _e('Редактировать страницу материала', 'mbl_admin'); ?>"
                                target="_blank"
                                class="mbl-autotraining-link"
                            ><i class="fa fa-pencil-square-o fa-md" aria-hidden="true"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    (function () {
        var elem = document.getElementById('mbl_clock'),
            ts = parseInt(elem.getAttribute('data-now')) * 1000,
            offset = ts - Date.now();

        startTime();

        function startTime() {
            var today = new Date(Date.now() + offset + (new Date()).getTimezoneOffset() * 60 * 1000),
                h = today.getHours(),
                m = today.getMinutes();
            m = checkTime(m);
            elem.innerHTML = h + ":" + m;
            setTimeout(startTime, 1000 * 30);
        }

        function checkTime(i) {
            if (i < 10) {
                i = "0" + i
            }
            return i;
        }
    })();
</script>