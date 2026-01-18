<form name="wpm-settings-form" method="post" action="">
    <h4><?php _e('Фильтры', 'mbl_admin') ?>:</h4>
    <div class="flex-row">
        <?php $label = wpm_render_partial('homework/select-imitation', 'admin', ['label' => __('Тип', 'mbl_admin')], true) ?>
        <?php wpm_render_partial('options/checkbox-row', 'admin', ['label' => $label, 'key' => 'hw.enabled_filters.type', 'default' => 'on']) ?>

        <?php $label = wpm_render_partial('homework/select-imitation', 'admin', ['label' => __('Дата', 'mbl_admin')], true) ?>
        <?php wpm_render_partial('options/checkbox-row', 'admin', ['label' => $label, 'key' => 'hw.enabled_filters.date', 'default' => 'on']) ?>

        <?php $label = wpm_render_partial('homework/select-imitation', 'admin', ['label' => __('Все рубрики', 'mbl_admin')], true) ?>
        <?php wpm_render_partial('options/checkbox-row', 'admin', ['label' => $label, 'key' => 'hw.enabled_filters.categories', 'default' => 'on']) ?>

        <?php $label = wpm_render_partial('homework/select-imitation', 'admin', ['label' => __('Все материалы', 'mbl_admin')], true) ?>
        <?php wpm_render_partial('options/checkbox-row', 'admin', ['label' => $label, 'key' => 'hw.enabled_filters.materials', 'default' => 'on']) ?>

        <?php $label = wpm_render_partial('homework/select-imitation', 'admin', ['label' => __('Все уровни доступа', 'mbl_admin')], true) ?>
        <?php wpm_render_partial('options/checkbox-row', 'admin', ['label' => $label, 'key' => 'hw.enabled_filters.levels', 'default' => 'on']) ?>

        <?php do_action('wpm_admin_hw_settings_filter'); ?>

    </div>
    <br>
    <h4><?php _e('Поля таблицы', 'mbl_admin') ?>:</h4>
    <div class="flex-row">
        <?php $label = '<i class="fa fa-flask" aria-hidden="true"></i>' . __('Тип', 'mbl_admin') ?>
        <?php wpm_render_partial('options/checkbox-row', 'admin', ['label' => $label, 'key' => 'hw.enabled_fields.type', 'default' => 'on']) ?>

        <?php $label = '<i class="fa fa-calendar" aria-hidden="true"></i>' . __('Дата и время', 'mbl_admin') ?>
        <?php wpm_render_partial('options/checkbox-row', 'admin', ['label' => $label, 'key' => 'hw.enabled_fields.date', 'default' => 'on']) ?>

        <?php $label = '<i class="fa fa-comment-o" aria-hidden="true"></i>' . __('Количество комментариев', 'mbl_admin') ?>
        <?php wpm_render_partial('options/checkbox-row', 'admin', ['label' => $label, 'key' => 'hw.enabled_fields.comments', 'default' => 'on']) ?>

        <?php $label = '<i class="fa fa-folder-open-o" aria-hidden="true"></i>' . __('Рубрика', 'mbl_admin') ?>
        <?php wpm_render_partial('options/checkbox-row', 'admin', ['label' => $label, 'key' => 'hw.enabled_fields.categories', 'default' => 'on']) ?>

        <?php $label = '<i class="fa fa-file-text-o" aria-hidden="true"></i>' . __('Материал', 'mbl_admin') ?>
        <?php wpm_render_partial('options/checkbox-row', 'admin', ['label' => $label, 'key' => 'hw.enabled_fields.materials', 'default' => 'on']) ?>

        <?php $label = '<i class="fa fa-sitemap" aria-hidden="true"></i>' . __('Уровень доступа', 'mbl_admin') ?>
        <?php wpm_render_partial('options/checkbox-row', 'admin', ['label' => $label, 'key' => 'hw.enabled_fields.levels', 'default' => 'on']) ?>

        <?php do_action('wpm_admin_hw_settings_fields'); ?>

    </div>
    <br>

    <h4><?php _e('Поиск', 'mbl_admin') ?>:</h4>
    <?php $label = wpm_render_partial('homework/search-imitation', 'admin', [], true) ?>
    <?php wpm_render_partial('options/checkbox-row', 'admin', ['label' => $label, 'key' => 'hw.enabled_fields.search', 'default' => 'on']) ?>
    <br>

    <h4><?php _e('Другие настройки', 'mbl_admin') ?></h4>
    <?php wpm_render_partial('options/checkbox-row', 'admin', ['label' => __('Отображать только рубрики-автотренинги', 'mbl_admin'), 'key' => 'hw.autotrainings_only', 'default' => 'off']) ?>


    <?php wpm_render_partial('settings-save-button', 'common'); ?>
</form>
