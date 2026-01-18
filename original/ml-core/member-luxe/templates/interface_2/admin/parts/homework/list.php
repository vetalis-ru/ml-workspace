<?php /** @var WP_Term[] $categories  */ ?>
<form id="wpm-hw-filter" method="post">
    <input type="hidden" name="order_by" value="date" id="wpm-hw-filter-order-by">
    <input type="hidden" name="order" value="desc" id="wpm-hw-filter-order">
    <input type="hidden" name="action" value="wpm_admin_hw_list_content">
    <input type="hidden" name="status" value="opened" id="wpm-hw-filter-status">

    <?php if (wpm_option_is('hw.enabled_fields.search', 'on', 'on')) : ?>
        <p class="search-box" id="wpm-hw-filter-search">
            <label class="screen-reader-text" for="post-search-input"><?php _e('Поиск', 'mbl_admin'); ?>
                :</label>
            <input type="search" id="post-search-input" name="s" value="">
            <input type="submit" name="" id="search-submit" class="button" value="<?php _e('Поиск', 'mbl_admin'); ?>">
        </p>
    <?php endif; ?>

    <div class="responses wpm-hw-table-wrapper">
        <table class="wpm-hw-table widefat fixed pages">
            <colgroup>
                <col style="width:2.5%;">
                <col>
                <?php if (wpm_option_is('hw.enabled_fields.type', 'on', 'on')) : ?>
                    <col style="width:8%;">
                <?php endif; ?>
                <?php if (wpm_option_is('hw.enabled_fields.date', 'on', 'on')) : ?>
                    <col style="width: 150px">
                <?php endif; ?>
                <?php if (wpm_option_is('hw.enabled_fields.comments', 'on', 'on')) : ?>
                    <col style="width:5%;">
                <?php endif; ?>
                <?php if (wpm_option_is('hw.enabled_fields.categories', 'on', 'on')) : ?>
                    <col>
                <?php endif; ?>
                <?php if (wpm_option_is('hw.enabled_fields.materials', 'on', 'on')) : ?>
                    <col>
                <?php endif; ?>
                <?php if (wpm_option_is('hw.enabled_fields.levels', 'on', 'on')) : ?>
                    <col>
                <?php endif; ?>
                <?php do_action('wpm_admin_hw_list_col_after'); ?>
                <col style="width:7%;">
            </colgroup>
            <thead>
            <tr class="mbl-hw-first-tr">
                <th colspan="2" class="wpm-status-filters-th">
                    <div class="wpm-status-filters">
                        <a href="#" class="opened active" data-status="opened" data-mbl-tooltip title="<?php _e('На рассмотрении', 'mbl_admin'); ?>">
                            <i class="fa fa-clock-o"></i>
                            <span class="wpm-status-nb"></span>
                        </a>
                        <a href="#" class="approved" data-status="approved" data-mbl-tooltip title="<?php _e('Одобренные', 'mbl_admin'); ?>">
                            <i class="fa fa-check-circle-o"></i>
                            <span class="wpm-status-nb"></span>
                        </a>
                        <a href="#" class="rejected" data-status="rejected" data-mbl-tooltip title="<?php _e('Отклоненные', 'mbl_admin'); ?>">
                            <i class="fa fa-times-circle-o"></i>
                            <span class="wpm-status-nb"></span>
                        </a>
                        <a href="#" class="trash" data-status="trash" data-mbl-tooltip title="<?php _e('Архив',  'mbl_admin'); ?>">
                            <i class="fa fa-trash"></i>
                            <span class="wpm-status-nb"></span>
                        </a>
                    </div>
                </th>
                <?php if (wpm_option_is('hw.enabled_fields.type', 'on', 'on')) : ?>
                    <th>
                        <?php if (wpm_option_is('hw.enabled_filters.type', 'on', 'on')) : ?>
                            <select name="type" data-mbl-select-2 data-placeholder="<?php _e('Все типы', 'mbl_admin'); ?>" data-allow-clear="true" data-width="100%"  data-minimum-results-for-search="-1">
                                <option value=""></option>
                                <option value="question"><?php _e('Вопросы', 'mbl_admin'); ?></option>
                                <option value="test"><?php _e('Тесты', 'mbl_admin'); ?></option>
                            </select>
                        <?php endif; ?>
                    </th>
                <?php endif; ?>
                <?php if (wpm_option_is('hw.enabled_fields.date', 'on', 'on')) : ?>
                    <th>
                        <?php if (wpm_option_is('hw.enabled_filters.date', 'on', 'on')) : ?>
                            <?php wpm_render_partial('homework/dates-select', 'admin') ?>
                        <?php endif; ?>
                    </th>
                <?php endif; ?>
                <?php if (wpm_option_is('hw.enabled_fields.comments', 'on', 'on')) : ?>
                    <th class="mbl-comments-th-filter"></th>
                <?php endif; ?>
                <?php if (wpm_option_is('hw.enabled_fields.categories', 'on', 'on')) : ?>
                    <th>
                        <?php if (wpm_option_is('hw.enabled_filters.categories', 'on', 'on')) : ?>
                            <select name="wpm-category" data-mbl-select-2 data-width="100%">
                                <option value="" selected><?php _e('Все рубрики', 'mbl_admin'); ?></option>
                                <?php foreach ($categories as $category) : ?>
                                    <option value="<?php echo $category->slug; ?>"><?php echo $category->name; ?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php endif; ?>
                    </th>
                <?php endif; ?>
                <?php if (wpm_option_is('hw.enabled_fields.materials', 'on', 'on')) : ?>
                    <th>
                        <?php if (wpm_option_is('hw.enabled_filters.materials', 'on', 'on')) : ?>
                            <select name="material" id="mbl_stats_material_select" data-placeholder="<?php _e('Все материалы', 'mbl_admin'); ?>" data-width="100%"></select>
                        <?php endif; ?>
                    </th>
                <?php endif; ?>
                <?php if (wpm_option_is('hw.enabled_fields.levels', 'on', 'on')) : ?>
                    <th>
                        <?php if (wpm_option_is('hw.enabled_filters.levels', 'on', 'on')) : ?>
                            <select name="wpm-levels"  data-mbl-select-2 data-width="100%">
                                <option value="" selected><?php _e('Все уровни доступа', 'mbl_admin'); ?></option>
                                <?php foreach ($levels AS $level) : ?>
                                    <option value="<?php echo $level->slug; ?>"><?php echo $level->name; ?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php endif; ?>
                    </th>
                <?php endif; ?>
                <?php do_action('wpm_admin_hw_list_filters_after'); ?>
                <th>
                    <button type="submit" class="button"><?php _e('OK', 'mbl_admin'); ?></button>
                </th>
            </tr>
            <tr id="mbl_hw_order_buttons">
                <th class="column-primary column-user mbl-hw-select-all-th" colspan="2">
                    <label>
                        <input type="checkbox" id="wpm-hw-select-all">
                        <span class="text" id="wpm-hw-select-all-label"><?php _e('Выбрать всех', 'mbl_admin'); ?></span>
                    </label>
                    <span id="wpm-hw-select-all-action" style="display:none;"></span>
                </th>
                <?php if (wpm_option_is('hw.enabled_fields.type', 'on', 'on')) : ?>
                    <th data-order="desc" data-order-by="type"  class="hw-column column-type" style="text-align: center">
                        <i class="fa fa-flask" aria-hidden="true"></i>
                        <span><?php _e('Тип', 'mbl_admin'); ?></span>
                    </th>
                <?php endif; ?>
                <?php if (wpm_option_is('hw.enabled_fields.date', 'on', 'on')) : ?>
                    <th data-order="desc" data-order-by="date"  class="hw-column column-date order-desc">
                        <i class="fa fa-calendar" aria-hidden="true"></i>
                        <span><?php _e('Дата и время', 'mbl_admin'); ?></span>
                    </th>
                <?php endif; ?>
                <?php if (wpm_option_is('hw.enabled_fields.comments', 'on', 'on')) : ?>
                    <th data-order="asc" data-order-by="comment"  class="hw-column column-comment">
                        <i class="fa fa-comment-o" title="<?php _e('Количество комментариев', 'mbl_admin'); ?>"></i>
                    </th>
                <?php endif; ?>
                <?php if (wpm_option_is('hw.enabled_fields.categories', 'on', 'on')) : ?>
                    <th data-order="desc" data-order-by="category" class="hw-column column-category">
                        <i class="fa fa-folder-open-o" aria-hidden="true"></i>
                        <span><?php _e('Рубрика', 'mbl_admin'); ?></span>
                    </th>
                <?php endif; ?>
                <?php if (wpm_option_is('hw.enabled_fields.materials', 'on', 'on')) : ?>
                    <th data-order="desc" data-order-by="material" class="hw-column column-material">
                        <i class="fa fa-file-text-o" aria-hidden="true"></i>
                        <span><?php _e('Материал', 'mbl_admin'); ?></span>
                    </th>
                <?php endif; ?>
                <?php if (wpm_option_is('hw.enabled_fields.levels', 'on', 'on')) : ?>
                    <th data-order="desc" data-order-by="level" class="hw-column column-level">
                        <i class="fa fa-sitemap" aria-hidden="true"></i>
                        <span>
                            <?php _e('Уровень доступа', 'mbl_admin'); ?>
                        </span>
                    </th>
                <?php endif; ?>
                <?php do_action('wpm_admin_hw_list_th_after'); ?>
                <th class="column-actions"></th>
            </tr>
            </thead>
            <tbody id="wpm-hw-content">
                <?php do_action('wpm_admin_hw_list_content'); ?>
            </tbody>
        </table>
    </div>
</form>
