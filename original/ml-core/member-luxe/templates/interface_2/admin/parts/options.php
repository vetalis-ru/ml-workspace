<?php wpm_render_partial('options/js', 'admin') ?>
<?php wpm_render_partial('options/css', 'admin') ?>
<div class="wrap wpm-options-page">
    <div id="icon-options-general" class="icon32"></div>
    <div class="wpm-admin-page-header">
        <h2><?php _e('Настройки', 'mbl_admin') ?></h2>
    </div>
    <?php $default_design_options = get_option('wpm_design_options_default'); ?>

    <div class="mbl-options-preloader">
      <div class="loader-ellipse">
        <span class="loader-ellipse__dot"></span>
        <span class="loader-ellipse__dot"></span>
        <span class="loader-ellipse__dot"></span>
        <span class="loader-ellipse__dot"></span>
      </div>
    </div>

    <form name="wpm-settings-form" class="wpm-settings-form" method="post" action="">
        <div class="options-wrap wpm-ui-wrap">
            <div id="wpm-options-tabs" tab-id="vertical-menu-1" class="wpm-tabs wpm-tabs-vertical">
                <ul class="tabs-nav">
                    <li><a href="#tab-1"><?php _e('Общие', 'mbl_admin') ?></a></li>
                    <li><a href="#tab-2"><?php _e('Страницы', 'mbl_admin') ?></a></li>
                    <li><a href="#tab-3"><?php _e('Внешний вид', 'mbl_admin') ?></a></li>
                    <li><a href="#tab-4"><?php _e('Автотренинги', 'mbl_admin') ?></a></li>
                    <li><a href="#tab-5"><?php _e('Письма', 'mbl_admin') ?></a></li>
                    <li><a href="#tab-6"><?php _e('Защита', 'mbl_admin') ?></a></li>
                    <li><a href="#tab-7"><?php _e('Массовые операции', 'mbl_admin') ?></a></li>
                    <?php do_action('mbl_options_items_after') ?>
                    <li style="display: none"><a href="#tab-0"></a></li>
                </ul>
                <div id="tab-1" class="tab mbl-color-content">
                    <?php wpm_render_partial('options/tab-1', 'admin', compact('main_options', 'design_options')) ?>
                </div>
                <div id="tab-2" class="tab mbl-color-content">
                    <?php wpm_render_partial('options/tab-2', 'admin', compact('main_options', 'design_options')) ?>
                </div>
                <div id="tab-3" class="tab mbl-color-content">
                    <?php wpm_render_partial('options/tab-3', 'admin', compact('main_options', 'design_options')) ?>
                </div>
                <div id="tab-4" class="tab mbl-color-content">
                    <?php wpm_render_partial('options/tab-4', 'admin', compact('main_options', 'design_options')) ?>
                </div>
                <div id="tab-5" class="tab mbl-color-content">
                    <?php wpm_render_partial('options/tab-5', 'admin', compact('main_options', 'design_options')) ?>
                </div>
                <div id="tab-6" class="tab mbl-color-content">
                    <?php wpm_render_partial('options/tab-6', 'admin', compact('main_options', 'design_options')) ?>
                </div>
                <div id="tab-7" class="tab mbl-color-content">
                    <?php wpm_render_partial('options/tab-7', 'admin', compact('main_options', 'design_options')) ?>
                </div>
                <?php do_action('mbl_options_content_after', compact('main_options', 'design_options')) ?>
            </div>
        </div>
    </form>
</div>
<?php wpm_render_partial('icons-dialog', 'admin') ?>
