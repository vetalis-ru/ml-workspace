<div class="wpm-tab-content">
    <div class="wpm-inner-tabs" tab-id="h-tabs-3">
        <ul class="wpm-inner-tabs-nav">
            <?php /*
                <li><a href="#mbl_inner_tab_3_1"><?php _e('Интерфейс', 'mbl_admin') ?></a></li>
            */ ?>
            <li><a href="#mbl_inner_tab_3_2"><?php _e('Логотип и favicon', 'mbl_admin') ?></a></li>
            <li><a href="#mbl_inner_tab_3_3"><?php _e('Фон', 'mbl_admin') ?></a></li>
            <li><a href="#mbl_inner_tab_3_4"><?php _e('Панель управления', 'mbl_admin') ?></a></li>
            <li><a href="#mbl_inner_tab_3_5"><?php _e('Шапка', 'mbl_admin') ?></a></li>
            <li><a href="#mbl_inner_tab_3_12"><?php _e('Подвал', 'mbl_admin') ?></a></li>
            <li><a href="#mbl_inner_tab_3_6"><?php _e('Хлебные крошки', 'mbl_admin') ?></a></li>
            <li><a href="#mbl_inner_tab_3_9"><?php _e('Рубрика', 'mbl_admin') ?></a></li>
            <li><a href="#mbl_inner_tab_3_7"><?php _e('Заголовок и подзаголовок рубрики', 'mbl_admin') ?></a></li>
            <li><a href="#mbl_inner_tab_3_8"><?php _e('Описание рубрики', 'mbl_admin') ?></a></li>
            <li><a href="#mbl_inner_tab_3_10"><?php _e('Материал', 'mbl_admin') ?></a></li>
            <li><a href="#mbl_inner_tab_3_11"><?php _e('Контент материала', 'mbl_admin') ?></a></li>
            <li><a href="#mbl_inner_tab_3_14"><?php _e('Прогресс курса', 'mbl_admin') ?></a></li>
            <li><a href="#mbl_inner_tab_3_15"><?php _e('Плеер', 'mbl_admin') ?></a></li>
            <li><a href="#mbl_inner_tab_3_16_1"><?php _e('Прелодер', 'mbl_admin') ?></a></li>
            <?php do_action('mbl_options_tab_3_header_after'); ?>
        </ul>
        <?php /*
            <div id="mbl_inner_tab_3_1" class="wpm-tab-content">
                <?php wpm_render_partial('settings-interface', 'common'); ?>
            </div>
        */ ?>
        <div id="mbl_inner_tab_3_2" class="wpm-tab-content">
            <label><?php _e('Логотип', 'mbl_admin') ?><br>
                <input type="hidden" id="wpm_logo" name="main_options[logo][url]"
                       value="<?php echo $main_options['logo']['url']; ?>"
                       class="wide"></label>

            <div class="wpm-control-row">
                <p>
                    <button type="button" class="wpm-media-upload-button button"
                            data-id="logo"><?php _e('Загрузить', 'mbl_admin') ?></button>
                    &nbsp;&nbsp; <a id="delete-wpm-logo"
                                    class="wpm-delete-media-button button submit-delete"
                                    data-id="logo"><?php _e('Удалить', 'mbl_admin') ?></a>
                </p>
            </div>
            <div class="wpm-logo-preview-wrap">
                <div class="wpm-logo-preview-box">
                    <img src="<?php echo wpm_remove_protocol($main_options['logo']['url']); ?>" title="" alt=""
                         id="wpm-logo-preview">
                </div>
            </div>

            <label>Favicon<br>
                <input type="hidden" id="wpm_favicon" name="main_options[favicon][url]"
                       value="<?php echo $main_options['favicon']['url']; ?>" class="wide"></label>

            <div class="wpm-control-row">
                <p>
                    <button type="button" class="wpm-media-upload-button button"
                            data-id="favicon"><?php _e('Загрузить', 'mbl_admin') ?></button>
                    &nbsp;&nbsp; <a id="delete-wpm-favicon"
                                    class="wpm-delete-media-button button submit-delete"
                                    data-id="favicon"><?php _e('Удалить', 'mbl_admin') ?></a>
                </p>
            </div>
            <div class="wpm-favicon-preview-wrap">
                <div class="wpm-favicon-preview-box">
                    <img src="<?php echo wpm_remove_protocol($main_options['favicon']['url']); ?>" title="" alt=""
                         id="wpm-favicon-preview">
                </div>
            </div>

            <?php wpm_render_partial('settings-save-button', 'common'); ?>
        </div>
        <div id="mbl_inner_tab_3_3" class="wpm-tab-content">
            <div class="wpm-control-row">
                <label><?php _e('Цвет фона', 'mbl_admin') ?><br>
                    <input type="text" name="design_options[main][background_color]"
                           class="color"
                           value="<?php echo $design_options['main']['background_color']; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Фоновое изображение', 'mbl_admin') ?><br>
                    <input type="text" id="wpm_background"
                           name="design_options[main][background_image][url]"
                           value="<?php echo $design_options['main']['background_image']['url']; ?>"
                           class="wide"></label>

                <div class="wpm-control-row upload-image-row">
                    <p>
                        <button type="button" class="wpm-media-upload-button button"
                                data-id="background"><?php _e('Загрузить', 'mbl_admin') ?></button>
                        &nbsp;&nbsp; <a id="delete-wpm-background"
                                        class="wpm-delete-media-button button submit-delete"
                                        data-id="background"><?php _e('Удалить', 'mbl_admin') ?></a>
                    </p>
                </div>
                <div class="wpm-background-preview-wrap">
                    <div class="wpm-background-preview-box preview-box">
                        <img
                            src="<?php echo wpm_remove_protocol($design_options['main']['background_image']['url']); ?>"
                            title="" alt=""
                            id="wpm-background-preview">
                    </div>
                </div>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Выравнивание изображения', 'mbl_admin') ?></label><br>
                <?php
                $background_position = array(
                    'left top' => __('сверху слева', 'mbl_admin'),
                    'right top' => __('сверху справа', 'mbl_admin'),
                    'center top' => __('сверху по центру', 'mbl_admin'),
                    'left bottom' => __('снизу слева', 'mbl_admin'),
                    'right bottom' => __('снизу справа', 'mbl_admin'),
                    'center bottom' => __('снизу по центру', 'mbl_admin')
                );
                $html = '';
                foreach ($background_position as $key => $value) {
                    if ($design_options['main']['background_image']['position'] == $key)
                        $html .= "<option value='$key' selected>$value</option>";
                    else
                        $html .= "<option value='$key'>$value</option>";
                }
                $html = '<select name="design_options[main][background_image][position]">' . $html . '</select>';
                echo $html;
                ?>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Повторение изображения', 'mbl_admin') ?></label><br>
                <?php
                $background_repeat = array(
                    'no-repeat' => __('не повторять', 'mbl_admin'),
                    'repeat' => __('повторять', 'mbl_admin'),
                    'repeat-x' => __('повторять по горизонтали', 'mbl_admin'),
                    'repeat-y' => __('повторять по вертикали', 'mbl_admin')
                );
                $html = '';
                foreach ($background_repeat as $key => $value) {
                    if ($design_options['main']['background_image']['repeat'] == $key)
                        $html .= "<option value='$key' selected>$value</option>";
                    else
                        $html .= "<option value='$key'>$value</option>";
                }
                $html = '<select name="design_options[main][background_image][repeat]">' . $html . '</select>';
                echo $html;
                ?>
            </div>
            <br/>

            <div class="wpm-control-row">
                <label><input type="checkbox"
                              name="design_options[main][background-attachment-fixed]" <?php echo wpm_array_get($design_options, 'main.background-attachment-fixed') == 'on' ? 'checked' : ''; ?> >
                    &nbsp;<?php _e('Зафиксировать фон', 'mbl_admin') ?>
                </label><br>
            </div>

            <?php do_action('mbl_options_background_after', $design_options); ?>

            <?php wpm_render_partial('settings-save-button', 'common'); ?>
        </div>
        <div id="mbl_inner_tab_3_4" class="wpm-tab-content">
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет фона', 'mbl_admin'), 'key' => 'toolbar.background_color', 'default' => 'f9f9f9')) ?>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет рамки', 'mbl_admin'), 'key' => 'toolbar.border_bottom_color', 'default' => 'e7e7e7')) ?>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет иконок', 'mbl_admin'), 'key' => 'toolbar.icon_color', 'default' => '868686')) ?>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет иконок при наведении', 'mbl_admin'), 'key' => 'toolbar.icon_hover_color', 'default' => '2e2e2e')) ?>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет текста', 'mbl_admin'), 'key' => 'toolbar.text_color', 'default' => '868686')) ?>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет текста при наведении', 'mbl_admin'), 'key' => 'toolbar.hover_color', 'default' => '2e2e2e')) ?>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет фона поиска', 'mbl_admin'), 'key' => 'toolbar.search_bg_color', 'default' => 'f9f9f9')) ?>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет рамки поиска', 'mbl_admin'), 'key' => 'toolbar.search_border_color', 'default' => 'ededed')) ?>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет фона выпадающего меню', 'mbl_admin'), 'key' => 'toolbar.menu_bg_color', 'default' => 'efefef')) ?>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет фона выпадающего меню при наведении', 'mbl_admin'), 'key' => 'toolbar.hover_menu_bg_color', 'default' => 'f0f0f0')) ?>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет текста-подсказки в полях форм', 'mbl_admin'), 'key' => 'toolbar.placeholder_color', 'default' => 'a9a9a9')) ?>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет заполняемого текста в полях форм', 'mbl_admin'), 'key' => 'toolbar.input_color', 'default' => '555555')) ?>
            <br>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет кнопок', 'mbl_admin'), 'key' => 'toolbar.button_color', 'default' => 'a0b0a1')) ?>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет кнопок при наведении', 'mbl_admin'), 'key' => 'toolbar.button_hover_color', 'default' => 'adbead')) ?>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет кнопок при клике', 'mbl_admin'), 'key' => 'toolbar.button_active_color', 'default' => '8e9f8f')) ?>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет текста кнопок', 'mbl_admin'), 'key' => 'toolbar.button_text_color', 'default' => 'ffffff')) ?>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет текста кнопок при наведении', 'mbl_admin'), 'key' => 'toolbar.button_text_hover_color', 'default' => 'ffffff')) ?>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет текста кнопок при клике', 'mbl_admin'), 'key' => 'toolbar.button_text_active_color', 'default' => 'ffffff')) ?>
            <br>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет текста «Закрыть»', 'mbl_admin'), 'key' => 'toolbar.close_text_color', 'default' => '868686')) ?>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет иконки «Закрыть»', 'mbl_admin'), 'key' => 'toolbar.close_icon_color', 'default' => 'ffffff')) ?>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет иконки «Закрыть» при наведении', 'mbl_admin'), 'key' => 'toolbar.close_icon_hover_color', 'default' => 'ffffff')) ?>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет иконки «Закрыть» при клике', 'mbl_admin'), 'key' => 'toolbar.close_icon_active_color', 'default' => 'ffffff')) ?>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет фона иконки «Закрыть»', 'mbl_admin'), 'key' => 'toolbar.close_icon_bg_color', 'default' => 'c1c1c1')) ?>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет фона иконки «Закрыть» при наведении', 'mbl_admin'), 'key' => 'toolbar.close_icon_bg_hover_color', 'default' => 'd4d4d4')) ?>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет фона иконки «Закрыть» при клике', 'mbl_admin'), 'key' => 'toolbar.close_icon_bg_active_color', 'default' => 'b4b4b4')) ?>
            <br>
            <br>
            <div class="wpm-control-row">
                <label>
                    <input type="hidden" name="design_options[panel_options][transparent]" value="0">
                    <input type="checkbox"
                              name="design_options[panel_options][transparent]"
                              <?php if (wpm_get_design_option('panel_options.transparent', '0') == '1') echo 'checked'; ?>
                              value="1"
                    ><?php _e('Сделать прозрачной', 'mbl_admin') ?></label>
            </div>
            <br>
            <?php wpm_render_partial('settings-save-button', 'common'); ?>
        </div>
        <div id="mbl_inner_tab_3_5" class="wpm-tab-content">
            <div class="wpm-control-row">
                <label>
                    <input type="checkbox"
                           name="main_options[header][visible]" <?php if ($main_options['header']['visible'] == 'on') echo 'checked'; ?>><?php _e('Включить шапки для страниц', 'mbl_admin') ?></label>

                <input type="hidden" id="wpm-design-headers-priority"
                       name="main_options[header_bg][priority]"
                       value="<?php echo wpm_get_option('header_bg.priority', 'default,login') ?: 'default,login'; ?>">
            </div>
            <br>

            <div class="wpm-control-row">
                <select id="users-level-for-header"
                        class="users-level"><?php echo wpm_get_levels_select(); ?></select>
                <a class="button button-primary page-header-add" data-action="add"><?php _e('Добавить шапку для уровня доступа', 'mbl_admin') ?></a>
            </div>
            <br>

            <div id="tabs-level-3" tab-id="headers-tabs"
                 class="tabs-level-3 headers-design-tabs wpm-inner-tabs-nav">

                <?php
                $page_headers = array_filter(explode(',', wpm_get_option('header_bg.priority', 'default,login')));

                if(empty($page_headers)) {
                    $page_headers = explode(',', 'default,login');
                }

                if (!empty($page_headers)) {
                    echo '<ul>';
                    foreach ($page_headers as $item) {
                        $wpm_term = get_term($item, 'wpm-levels');
                        if ($item == 'default') { ?>
                            <li class="ui-state-default ui-state-disabled" header-id="default">
                                <a href='#header-tab-default'><?php _e('По умолчанию', 'mbl_admin') ?></a></li>
                        <?php } elseif($item == 'login' ) { ?>
                            <li class="ui-state-default" header-id="login"><a
                                    href='#header-tab-<?php echo $item; ?>'><?php _e('Страница Входа', 'mbl_admin') ?></a>
                            </li>
                        <?php } elseif($item != 'pincodes') { ?>
                            <li class="ui-state-default" header-id="<?php echo $item; ?>"><a
                                    href='#header-tab-<?php echo $item; ?>'><?php echo $wpm_term->name; ?></a>
                            </li>
                        <?php } ?>
                    <?php }
                    echo '</ul>';

                    foreach ($page_headers as $item) {
                        if ($item == 'default') { ?>
                            <div id="header-tab-default" class="wpm-inner-tab-content">
                                <div class="wpm-control-row">
                                    <p>
                                        <label>
                                            <input type="hidden" name="main_options[header_bg][default][hide_logo]" value="off">
                                            <input type="checkbox"
                                                   name="main_options[header_bg][default][hide_logo]"
                                                <?php echo wpm_option_is("header_bg.default.hide_logo", 'on') ? 'checked' : ''; ?>>
                                            <?php _e('Скрыть логотип', 'mbl_admin') ?></label>
                                    </p>
                                    <?php
                                    wpm_render_partial('gallery-image-upload', 'admin', array(
                                            'name' => 'main_options[header_bg][default][url]',
                                            'value' => wpm_get_option('header_bg.default.url'),
                                            'id' => 'header_url_default',
                                            'previewClass' => 'wpm-header-img-preview'
                                    ));
                                   ?>
                                </div>
                                <?php wpm_render_partial('header-link', 'admin', array('name' => 'default')) ?>
                            </div>
                        <?php } elseif($item == 'login') { ?>
                            <div id="header-tab-login" class="wpm-inner-tab-content">
                                <div class="wpm-control-row">
                                    <p>
                                        <label>
                                            <input type="hidden" name="main_options[header_bg][login][hide_logo]" value="off">
                                            <input type="checkbox"
                                                   name="main_options[header_bg][login][hide_logo]"
                                                <?php echo wpm_option_is("header_bg.login.hide_logo", 'on') ? 'checked' : ''; ?>>
                                            <?php _e('Скрыть логотип', 'mbl_admin') ?></label>
                                    </p>
                                    <?php
                                    wpm_render_partial('gallery-image-upload', 'admin', array(
                                            'name' => 'main_options[header_bg][login][url]',
                                            'value' => wpm_get_option('header_bg.login.url'),
                                            'id' => 'header_url_login',
                                            'previewClass' => 'wpm-header-img-preview'
                                    ));
                                   ?>
                                </div>
                                <?php wpm_render_partial('header-link', 'admin', array('name' => 'login')) ?>
                            </div>
                        <?php } elseif($item != 'pincodes') { ?>
                            <div id="header-tab-<?php echo $item; ?>"
                                 class="wpm-inner-tab-content">
                                <div class="wpm-control-row">
                                    <p>
                                        <label>
                                            <input type="checkbox"
                                                   value="disabled"
                                                   name="main_options[header_bg][<?php echo $item; ?>][disabled]"
                                                <?php echo ($main_options['header_bg'][$item]['disabled'] ?? 'disabled') == 'disabled' ? 'checked' : ''; ?>>
                                            <?php _e('Временно отключить эту шапку', 'mbl_admin') ?></label>
                                        <span class="trash">
                                            <a class="page-header-remove"
                                               header-id="<?php echo $item; ?>"><?php _e('Удалить шапку', 'mbl_admin') ?></a>
                                        </span>
                                    </p>
                                    <p>
                                        <label>
                                            <input type="hidden" name="main_options[header_bg][<?php echo $item; ?>][hide_logo]" value="off">
                                            <input type="checkbox"
                                                   name="main_options[header_bg][<?php echo $item; ?>][hide_logo]"
                                                <?php echo wpm_option_is("header_bg.{$item}.hide_logo", 'on') ? 'checked' : ''; ?>>
                                            <?php _e('Скрыть логотип', 'mbl_admin') ?></label>
                                    </p>
                                    <?php
                                    wpm_render_partial('gallery-image-upload', 'admin', array(
                                            'name' => ('main_options[header_bg]['.$item.'][url]'),
                                            'value' => wpm_get_option('header_bg.'.$item.'.url'),
                                            'id' => ('header_url_' . $item),
                                            'previewClass' => 'wpm-header-img-preview'
                                    ));
                                    ?>
                                </div>
                                <?php wpm_render_partial('header-link', 'admin', array('name' => $item)) ?>
                            </div>
                        <?php } ?>
                    <?php }

                } ?>
                <?php wpm_render_partial('settings-save-button', 'common'); ?>
            </div>
        </div>
        <div id="mbl_inner_tab_3_6" class="wpm-tab-content">
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет иконок', 'mbl_admin'), 'key' => 'breadcrumbs.icon_color', 'default' => '8e8e8e')) ?>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет текстов', 'mbl_admin'), 'key' => 'breadcrumbs.color', 'default' => '8e8e8e')) ?>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет иконки активной крошки', 'mbl_admin'), 'key' => 'breadcrumbs.icon_active_color', 'default' => '8e8e8e')) ?>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет текста активной крошки', 'mbl_admin'), 'key' => 'breadcrumbs.active_color', 'default' => '8e8e8e')) ?>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет иконки при наведении', 'mbl_admin'), 'key' => 'breadcrumbs.icon_hover_color', 'default' => '000000')) ?>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет текста при наведении', 'mbl_admin'), 'key' => 'breadcrumbs.hover_color', 'default' => '000000')) ?>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет линии под крошками', 'mbl_admin'), 'key' => 'breadcrumbs.border_color', 'default' => 'e7e7e7')) ?>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет разделителей', 'mbl_admin'), 'key' => 'breadcrumbs.separator_color', 'default' => '8e8e8e')) ?>
            <br>
            <?php wpm_render_partial('settings-save-button', 'common'); ?>
        </div>
        <div id="mbl_inner_tab_3_7" class="wpm-tab-content">
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет заголовка', 'mbl_admin'), 'key' => 'term_header.color', 'default' => '000000')) ?>
            <div class="wpm-control-row">
                <label>
                    <input type="checkbox"
                              name="design_options[term_header][bold]" <?php echo wpm_get_design_option('term_header.bold') == 'on' ? 'checked' : ''; ?> >
                    <?php _e('Жирность', 'mbl_admin') ?></label>
                &nbsp;&nbsp;&nbsp;
                <label>
                    <input type="checkbox"
                              name="design_options[term_header][bordered]" <?php echo wpm_get_design_option('term_header.bordered') == 'on' ? 'checked' : ''; ?> >
                    <?php _e('Подчеркнутость', 'mbl_admin') ?></label>
                &nbsp;&nbsp;&nbsp;
                <label>
                    <input type="checkbox"
                              name="design_options[term_header][italic]" <?php echo wpm_get_design_option('term_header.italic') == 'on' ? 'checked' : ''; ?> >
                    <?php _e('Курсив', 'mbl_admin') ?></label>

            </div>
            <div class="wpm-control-row">
                <?php _e('Размер заголовка', 'mbl_admin') ?> &nbsp;
                <select
                    name="design_options[term_header][font_size]">
                    <?php
                    for ($i = 14; $i < 26; $i++) {
                        if ($i != wpm_get_design_option('term_header.font_size', '20')) {
                            echo '<option value="' . $i . '">' . $i . 'px</option>';
                        } else {
                            echo '<option selected value="' . $i . '">' . $i . 'px</option>';
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="wpm-control-row">
                <label>
                    <input type="checkbox"
                              name="design_options[term_header][hide]" <?php echo wpm_get_design_option('term_header.hide') == 'on' ? 'checked' : ''; ?> >
                    <?php _e('Не показывать заголовок', 'mbl_admin') ?></label>
            </div>

            <br><br>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет подзаголовка', 'mbl_admin'), 'key' => 'term_subheader.color', 'default' => '949494')) ?>
            <div class="wpm-control-row">
                <label>
                    <input type="checkbox"
                              name="design_options[term_subheader][bold]" <?php echo wpm_get_design_option('term_subheader.bold') == 'on' ? 'checked' : ''; ?> >
                    <?php _e('Жирность', 'mbl_admin') ?></label>
                &nbsp;&nbsp;&nbsp;
                <label>
                    <input type="checkbox"
                              name="design_options[term_subheader][bordered]" <?php echo wpm_get_design_option('term_subheader.bordered') == 'on' ? 'checked' : ''; ?> >
                    <?php _e('Подчеркнутость', 'mbl_admin') ?></label>
                &nbsp;&nbsp;&nbsp;
                <label>
                    <input type="checkbox"
                              name="design_options[term_subheader][italic]" <?php echo wpm_get_design_option('term_subheader.italic') == 'on' ? 'checked' : ''; ?> >
                    <?php _e('Курсив', 'mbl_admin') ?></label>

            </div>
            <div class="wpm-control-row">
                <?php _e('Размер подзаголовка', 'mbl_admin') ?> &nbsp;
                <select
                    name="design_options[term_subheader][font_size]">
                    <?php
                    for ($i = 14; $i < 26; $i++) {
                        if ($i != wpm_get_design_option('term_subheader.font_size', '16')) {
                            echo '<option value="' . $i . '">' . $i . 'px</option>';
                        } else {
                            echo '<option selected value="' . $i . '">' . $i . 'px</option>';
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="wpm-control-row">
                <label>
                    <input type="checkbox"
                              name="design_options[term_subheader][hide]" <?php echo wpm_get_design_option('term_subheader.hide') == 'on' ? 'checked' : ''; ?> >
                    <?php _e('Не показывать подзаголовок', 'mbl_admin') ?></label>
            </div>

            <br>
            <?php wpm_render_partial('settings-save-button', 'common'); ?>
        </div>
        <div id="mbl_inner_tab_3_8" class="wpm-tab-content">
            <div class="wpm-row">
                <label>
                    <input type="hidden" name="main_options[folders][show_description]" value="0">
                    <input
                        type="checkbox"
                        name="main_options[folders][show_description]"
                        value="1"
                        <?php if (wpm_get_option('folders.show_description', 1)) : ?>
                            checked="checked"
                        <?php endif; ?>
                    >
                    <?php _e('Отображать описание рубрик', 'mbl_admin') ?></label>
            </div>

            <div class="wpm-row">
                <label>
                    <input type="hidden" name="main_options[folders][description_expand]" value="0">
                    <input
                        type="checkbox"
                        name="main_options[folders][description_expand]"
                        value="1"
                        <?php if (wpm_get_option('folders.description_expand')) : ?>
                            checked="checked"
                        <?php endif; ?>
                    >
                    <?php _e('Развернуть описание рубрик по умолчанию', 'mbl_admin') ?></label>
            </div>

            <h3><?php _e('Свернутая кнопка «подробнее»', 'mbl_admin') ?>:</h3>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет фона', 'mbl_admin'), 'key' => 'more_button.collapsed.bg_color', 'default' => 'ffffff')) ?>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет текста', 'mbl_admin'), 'key' => 'more_button.collapsed.color', 'default' => 'acacac')) ?>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет иконки', 'mbl_admin'), 'key' => 'more_button.collapsed.icon_color', 'default' => 'acacac')) ?>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет рамки', 'mbl_admin'), 'key' => 'more_button.collapsed.border_color', 'default' => 'c1c1c1')) ?>
            <br><br>
            <h3><?php _e('Развернутая кнопка «подробнее»', 'mbl_admin') ?>:</h3>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет фона', 'mbl_admin'), 'key' => 'more_button.expanded.bg_color', 'default' => 'fbfbfb')) ?>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет текста', 'mbl_admin'), 'key' => 'more_button.expanded.color', 'default' => 'acacac')) ?>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет иконки', 'mbl_admin'), 'key' => 'more_button.expanded.icon_color', 'default' => 'acacac')) ?>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет рамки', 'mbl_admin'), 'key' => 'more_button.expanded.border_color', 'default' => 'e3e3e3')) ?>

            <?php wpm_render_partial('settings-save-button', 'common'); ?>
        </div>
        <div id="mbl_inner_tab_3_9" class="wpm-tab-content">
            <div class="wpm-control-row">
                <?php _e('Размер шрифта', 'mbl_admin') ?> &nbsp; <select
                    name="design_options[term_font_size]">
                    <?php
                    for ($i = 14; $i < 26; $i++) {
                        if ($i != wpm_get_design_option('term_font_size', 17)) {
                            echo '<option value="' . $i . '">' . $i . 'px</option>';
                        } else {
                            echo '<option selected value="' . $i . '">' . $i . 'px</option>';
                        }
                    }
                    ?>
                </select>
            </div>

            <div class="wpm-row">
                <div class="wpm-control-row">
                    <p><?php _e('Фоновое изображение по умолчанию', 'mbl_admin') ?></p>
                </div>
                <input type="hidden"
                       id="wpm_term_bg_url"
                       name="main_options[folders][bg_url]"
                       value="<?php echo wpm_get_option('folders.bg_url'); ?>">

                <input type="hidden"
                       id="wpm_term_bg_url_original"
                       name="main_options[folders][bg_url_original]"
                       value="<?php echo wpm_get_option('folders.bg_url_original', wpm_get_option('folders.bg_url')); ?>">

                <div class="wpm-control-row">
                    <p>
                        <button type="button" class="wpm-media-upload-button button"
                                data-id="term_bg_url"><?php _e('Загрузить', 'mbl_admin') ?></button>
                        <a
                              id="delete-wpm-favicon"
                              class="wpm-delete-media-button button submit-delete"
                              data-id="term_bg_url"><?php _e('Удалить', 'mbl_admin') ?></a>
                        <?php wpm_render_partial('crop-button', 'admin', array('key' => wpm_get_option('folders.bg_url'), 'id' => 'term_bg_url', 'w' => 345, 'h' => 260)) ?>
                    </p>
                </div>
                <div class="wpm-term_bg_url-preview-wrap inactive">
                    <div class="wpm-term_bg_url-preview-box">
                        <img
                            src="<?php echo wpm_get_option('folders.bg_url'); ?>"
                            title=""
                            alt=""
                            id="wpm-term_bg_url-preview">
                    </div>
                </div>
            </div>

            <div class="wpm-row">
                <label>
                    <input type="hidden" name="main_options[folders][comments_show]" value="0">
                    <input
                        type="checkbox"
                        name="main_options[folders][comments_show]"
                        value="1"
                        <?php echo wpm_get_option('folders.comments_show', 1) ? 'checked="checked"' : ''; ?>
                    >
                    <?php _e('Отображать количество комментариев', 'mbl_admin') ?></label>
            </div>
            <div class="wpm-row">
                <label>
                    <input type="hidden" name="main_options[folders][views_show]" value="0">
                    <input
                        type="checkbox"
                        name="main_options[folders][views_show]"
                        value="1"
                        <?php echo wpm_get_option('folders.views_show', 1) ? 'checked="checked"' : ''; ?>
                    >
                    <?php _e('Отображать количество просмотров', 'mbl_admin') ?></label>
            </div>
            <div class="wpm-row">
                <label>
                    <input type="hidden" name="main_options[folders][access_show]" value="0">
                    <input
                        type="checkbox"
                        name="main_options[folders][access_show]"
                        value="1"
                        <?php echo wpm_get_option('folders.access_show', 1) ? 'checked="checked"' : ''; ?>
                    >
                    <?php _e('Отображать доступность', 'mbl_admin') ?></label>
            </div>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет названия рубрики', 'mbl_admin'), 'key' => 'folders.color', 'default' => 'ffffff')) ?>

            <?php wpm_render_partial('settings-save-button', 'common'); ?>
        </div>
        <div id="mbl_inner_tab_3_10" class="wpm-tab-content">
            <div class="wpm-row">
                <div class="wpm-control-row"><?php _e('Расположение материалов:', 'mbl_admin') ?></div>
                <label>
                    <input type="radio"
                           name="main_options[main][posts_row_nb]"
                           value="2"
                        <?php echo wpm_option_is('main.posts_row_nb', 2, 2) ? 'checked="checked"' : ''; ?>
                    >
                    <?php _e('2 колонки', 'mbl_admin') ?></label>
                &nbsp;&nbsp;
                <label>
                    <input type="radio"
                           name="main_options[main][posts_row_nb]"
                           value="1"
                        <?php echo wpm_option_is('main.posts_row_nb', 1, 2) ? 'checked="checked"' : ''; ?>
                    >
                    <?php _e('1 колонка', 'mbl_admin') ?></label>
            </div>

            <div class="wpm-row">
                <div class="wpm-control-row">
                    <p><?php _e('Фоновое изображение по умолчанию', 'mbl_admin') ?></p>
                </div>
                <input type="hidden"
                       id="wpm_post_bg_url"
                       name="main_options[materials][bg_url]"
                       value="<?php echo wpm_get_option('materials.bg_url'); ?>">

                <input type="hidden"
                       id="wpm_post_bg_url_original"
                       name="main_options[materials][bg_url_original]"
                       value="<?php echo wpm_get_option('materials.bg_url_original', wpm_get_option('materials.bg_url')); ?>">

                <div class="wpm-control-row">
                    <p>
                        <button type="button" class="wpm-media-upload-button button"
                                data-id="post_bg_url"><?php _e('Загрузить', 'mbl_admin'); ?></button>
                        <a
                              id="delete-wpm-favicon"
                              class="wpm-delete-media-button button submit-delete"
                              data-id="post_bg_url"><?php _e('Удалить', 'mbl_admin'); ?></a>
                        <?php wpm_render_partial('crop-button', 'admin', array('key' => wpm_get_option('materials.bg_url'), 'id' => 'post_bg_url', 'w' => 295, 'h' => 220)) ?>
                    </p>
                </div>
                <div class="wpm-post_bg_url-preview-wrap inactive">
                    <div class="wpm-post_bg_url-preview-box">
                        <img
                            src="<?php echo wpm_remove_protocol(wpm_get_option('materials.bg_url')); ?>"
                            title=""
                            alt=""
                            id="wpm-post_bg_url-preview">
                    </div>
                </div>
            </div>

            <div class="wpm-row">
                <label>
                    <input type="hidden" name="main_options[materials][number_show]" value="0">
                    <input
                        type="checkbox"
                        name="main_options[materials][number_show]"
                        value="1"
                        <?php echo wpm_get_option('materials.number_show', 1) ? 'checked="checked"' : ''; ?>
                    >
                    <?php _e('Отображать порядковый номер материала', 'mbl_admin') ?></label>
            </div>

            <div class="wpm-row">
                <label>
                    <input type="hidden" name="main_options[materials][content_type_show]" value="0">
                    <input
                        type="checkbox"
                        name="main_options[materials][content_type_show]"
                        value="1"
                        <?php echo wpm_get_option('materials.content_type_show', 1) ? 'checked="checked"' : ''; ?>
                    >
                    <?php _e('Отображать тип контента', 'mbl_admin') ?></label>
            </div>

            <div class="wpm-row">
                <label>
                    <input type="hidden" name="main_options[materials][homework_status_show]" value="0">
                    <input
                        type="checkbox"
                        name="main_options[materials][homework_status_show]"
                        value="1"
                        <?php echo wpm_get_option('materials.homework_status_show', 1) ? 'checked="checked"' : ''; ?>
                    >
                    <?php _e('Отображать статус ДЗ', 'mbl_admin') ?></label>
            </div>

            <div class="wpm-row">
                <label>
                    <input type="hidden" name="main_options[materials][date_show]" value="0">
                    <input
                        type="checkbox"
                        name="main_options[materials][date_show]"
                        value="1"
                        <?php echo wpm_get_option('materials.date_show', 1) ? 'checked="checked"' : ''; ?>
                    >
                    <?php _e('Отображать дату', 'mbl_admin') ?></label>
            </div>

            <div class="wpm-row">
                <label>
                    <input type="hidden" name="main_options[materials][comments_show]" value="0">
                    <input
                        type="checkbox"
                        name="main_options[materials][comments_show]"
                        value="1"
                        <?php echo wpm_get_option('materials.comments_show', 1) ? 'checked="checked"' : ''; ?>
                    >
                    <?php _e('Отображать комментарии', 'mbl_admin') ?></label>
            </div>

            <div class="wpm-row">
                <label>
                    <input type="hidden" name="main_options[materials][views_show]" value="0">
                    <input
                        type="checkbox"
                        name="main_options[materials][views_show]"
                        value="1"
                        <?php echo wpm_get_option('materials.views_show', 1) ? 'checked="checked"' : ''; ?>
                    >
                    <?php _e('Отображать количество просмотров', 'mbl_admin') ?></label>
            </div>

            <div class="wpm-row">
                <label>
                    <input type="hidden" name="main_options[materials][access_show]" value="0">
                    <input
                        type="checkbox"
                        name="main_options[materials][access_show]"
                        value="1"
                        <?php echo wpm_get_option('materials.access_show', 1) ? 'checked="checked"' : ''; ?>
                    >
                    <?php _e('Отображать доступность', 'mbl_admin') ?></label>
            </div>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет заголовка', 'mbl_admin'), 'key' => 'material_item.title', 'default' => '000000')) ?>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет описания', 'mbl_admin'), 'key' => 'material_item.description', 'default' => '666')) ?>

            <br>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет индикаторов', 'mbl_admin'), 'key' => 'materials.indicator_color', 'default' => 'ffffff')) ?>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет иконок «тип контента»', 'mbl_admin'), 'key' => 'materials.filetype_color', 'default' => 'ffffff')) ?>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет фона иконок «тип контента»', 'mbl_admin'), 'key' => 'materials.filetype_bg_color', 'default' => '000000')) ?>
            <br>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет фона блока с описанием', 'mbl_admin'), 'key' => 'materials.desc_bg_color', 'default' => 'fafafa')) ?>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет рамки блока с описанием', 'mbl_admin'), 'key' => 'materials.desc_border_color', 'default' => 'eaeaea')) ?>
            <br>
            <h4 style="margin-bottom: 0"><?php _e('Доступ открыт:', 'mbl_admin') ?></h4>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет фона блока с описанием при наведении', 'mbl_admin'), 'key' => 'materials.open_hover_desc_bg_color', 'default' => 'dfece0')) ?>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет рамки блока с описанием при наведении', 'mbl_admin'), 'key' => 'materials.open_hover_desc_border_color', 'default' => 'cedccf')) ?>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет текста на кнопке', 'mbl_admin'), 'key' => 'materials.open_button_color', 'default' => 'fff')) ?>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет фона кнопки', 'mbl_admin'), 'key' => 'materials.open_button_bg_color', 'default' => 'a0b0a1')) ?>
            <br>
            <h4 style="margin-bottom: 0"><?php _e('Доступ закрыт:', 'mbl_admin') ?></h4>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет фона блока с описанием при наведении', 'mbl_admin'), 'key' => 'materials.closed_hover_desc_bg_color', 'default' => 'eed5d5')) ?>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет рамки блока с описанием при наведении', 'mbl_admin'), 'key' => 'materials.closed_hover_desc_border_color', 'default' => 'ddc4c4')) ?>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет текста на кнопке', 'mbl_admin'), 'key' => 'materials.closed_button_color', 'default' => 'fff')) ?>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет фона кнопки', 'mbl_admin'), 'key' => 'materials.closed_button_bg_color', 'default' => 'd29392')) ?>

            <br>
            <h4 style="margin-bottom: 0"><?php _e('Не пройденный урок автотренинга:', 'mbl_admin') ?></h4>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет фона блока с описанием при наведении', 'mbl_admin'), 'key' => 'materials.inaccessible_hover_desc_bg_color', 'default' => 'd8d8d8')) ?>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет рамки блока с описанием при наведении', 'mbl_admin'), 'key' => 'materials.inaccessible_hover_desc_border_color', 'default' => 'bebebe')) ?>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет текста на кнопке', 'mbl_admin'), 'key' => 'materials.inaccessible_button_color', 'default' => 'fff')) ?>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет фона кнопки', 'mbl_admin'), 'key' => 'materials.inaccessible_button_bg_color', 'default' => '838788')) ?>


            <?php wpm_render_partial('settings-save-button', 'common'); ?>
        </div>
        <div id="mbl_inner_tab_3_11" class="wpm-tab-content">
            <h3><?php _e('Контент:', 'mbl_admin') ?></h3>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет иконок на вкладках', 'mbl_admin'), 'key' => 'material_inner.tab_icon_color', 'default' => '9b9b9b')) ?>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет текста на вкладках', 'mbl_admin'), 'key' => 'material_inner.tab_color', 'default' => '9b9b9b')) ?>
            <br>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет иконок на активных вкладках', 'mbl_admin'), 'key' => 'material_inner.active_tab_icon_color', 'default' => '555555')) ?>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет текста на активных вкладках', 'mbl_admin'), 'key' => 'material_inner.active_tab_color', 'default' => '555555')) ?>
            <br>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет фона контента', 'mbl_admin'), 'key' => 'material_inner.content_bg_color', 'default' => 'ffffff')) ?>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет рамки контента', 'mbl_admin'), 'key' => 'material_inner.content_border_color', 'default' => 'e3e3e3')) ?>
            <br>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет фона неактивной вкладки', 'mbl_admin'), 'key' => 'material_inner.inactive_tab_bg_color', 'default' => 'efefef')) ?>
            <br><br>
            <h3><?php _e('Переключение между уроками:', 'mbl_admin') ?></h3>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет текста', 'mbl_admin'), 'key' => 'material_inner.lesson_status_color', 'default' => '9b9b9b')) ?>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет текста при наведении', 'mbl_admin'), 'key' => 'material_inner.lesson_status_hover_color', 'default' => '555555')) ?>
            <br><br>
            <h3><?php _e('Комментарии:', 'mbl_admin') ?></h3>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет иконок на вкладках', 'mbl_admin'), 'key' => 'material_comments.tabs_icon_color', 'default' => '9b9b9b')) ?>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет текста на вкладках', 'mbl_admin'), 'key' => 'material_comments.tabs_color', 'default' => '9b9b9b')) ?>
            <br>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет иконок на активных вкладках', 'mbl_admin'), 'key' => 'material_comments.tabs_active_icon_color', 'default' => '555555')) ?>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет текста на активных вкладках', 'mbl_admin'), 'key' => 'material_comments.tabs_active_color', 'default' => '555555')) ?>
            <br>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет фона контента', 'mbl_admin'), 'key' => 'material_comments.bg_color', 'default' => 'ffffff')) ?>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет рамки контента', 'mbl_admin'), 'key' => 'material_comments.border_color', 'default' => 'e3e3e3')) ?>
            <br>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет фона неактивной вкладки', 'mbl_admin'), 'key' => 'material_comments.inactive_tab_bg_color', 'default' => 'efefef')) ?>

            <?php wpm_render_partial('settings-save-button', 'common'); ?>

            <br><br>
            <h3><?php _e('Ширина контента', 'mbl_admin') ?>:</h3>
            <br>
            <label>
                    <input type="radio"
                           name="main_options[main][content_width]"
                           value="fixed"
                        <?php echo wpm_option_is('main.content_width', 'fixed', 'fixed') ? 'checked="checked"' : ''; ?>
                    >
                    <?php _e('Ширина контента', 'mbl_admin') ?> 820px</label>
                <br>
                <label>
                    <input type="radio"
                           name="main_options[main][content_width]"
                           value="wide"
                        <?php echo wpm_option_is('main.content_width', 'wide', 'fixed') ? 'checked="checked"' : ''; ?>
                    >
                   <?php _e('Ширина контента', 'mbl_admin') ?> 100%</label>

            <?php wpm_render_partial('settings-save-button', 'common'); ?>

        </div>
        <div id="mbl_inner_tab_3_12" class="wpm-tab-content">
            <div class="wpm-control-row">
                <label><input type="checkbox"
                              name="main_options[footer][visible]" <?php if (wpm_option_is('footer.visible', 'on')) echo 'checked'; ?>><?php _e('Отображать подвал', 'mbl_admin') ?></label>
            </div>
            <div class="wpm-control-row">
                <?php wp_editor($main_options['footer']['content'], 'wpm_footer', array('textarea_name' => 'main_options[footer][content]', 'editor_height' => 300)); ?>
            </div>

            <br>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет фона', 'mbl_admin'), 'key' => 'footer_options.background', 'default' => 'f9f9f9')) ?>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет рамки', 'mbl_admin'), 'key' => 'footer_options.border', 'default' => 'e7e7e7')) ?>
            <br>
            <div class="wpm-control-row">
                <label>
                    <input type="hidden" name="design_options[footer_options][transparent]" value="0">
                    <input type="checkbox"
                              name="design_options[footer_options][transparent]"
                              <?php if (wpm_get_design_option('footer_options.transparent', '0') == '1') echo 'checked'; ?>
                              value="1"
                    ><?php _e('Сделать прозрачным', 'mbl_admin') ?></label>
            </div>


            <?php wpm_render_partial('settings-save-button', 'common'); ?>
        </div>
        <div id="mbl_inner_tab_3_14" class="wpm-tab-content">
            <div class="wpm-row">
                <label>
                    <input type="hidden" name="main_options[progress][show]" value="0">
                    <input
                        type="checkbox"
                        name="main_options[progress][show]"
                        id="wpm-show-progress"
                        value="1"
                        <?php if (wpm_get_option('progress.show', 1)) : ?>
                            checked="checked"
                        <?php endif; ?>
                    >
                    <?php _e('Показывать прогресс курса', 'mbl_admin') ?></label>
            </div>

            <div
                id="wpm-progress-options"
                <?php if (!wpm_get_option('progress.show', 1)) : ?>
                    style="display: none;"
                <?php endif; ?>
            >
                <div class="wpm-row">
                    <label>
                        <input type="hidden" name="main_options[progress][folders]" value="0">
                        <input
                            type="checkbox"
                            name="main_options[progress][folders]"
                            value="1"
                            <?php if (wpm_get_option('progress.folders', 1)) : ?>
                                checked="checked"
                            <?php endif; ?>
                        >
                        <?php _e('На папках с файлами', 'mbl_admin') ?></label>
                </div>
                <div class="wpm-row">
                    <label>
                        <input type="hidden" name="main_options[progress][parent_folders]" value="0">
                        <input
                            type="checkbox"
                            name="main_options[progress][parent_folders]"
                            value="1"
                            <?php if (wpm_get_option('progress.parent_folders', 1)) : ?>
                                checked="checked"
                            <?php endif; ?>
                        >
                        <?php _e('На родительских папках', 'mbl_admin') ?></label>
                </div>
                <div class="wpm-row">
                    <label>
                        <input type="hidden" name="main_options[progress][subfolders]" value="0">
                        <input
                            type="checkbox"
                            name="main_options[progress][subfolders]"
                            value="1"
                            <?php if (wpm_get_option('progress.subfolders', 1)) : ?>
                                checked="checked"
                            <?php endif; ?>
                        >
                        <?php _e('На подпапках', 'mbl_admin') ?></label>
                </div>
                <div class="wpm-row">
                    <label>
                        <input type="hidden" name="main_options[progress][materials]" value="0">
                        <input
                            type="checkbox"
                            name="main_options[progress][materials]"
                            value="1"
                            <?php if (wpm_get_option('progress.materials', 1)) : ?>
                                checked="checked"
                            <?php endif; ?>
                        >
                        <?php _e('На странице списка материалов', 'mbl_admin') ?></label>
                </div>
                <div class="wpm-row">
                    <label>
                        <input type="hidden" name="main_options[progress][content]" value="0">
                        <input
                            type="checkbox"
                            name="main_options[progress][content]"
                            value="1"
                            <?php if (wpm_get_option('progress.content', 1)) : ?>
                                checked="checked"
                            <?php endif; ?>
                        >
                        <?php _e('Под контентом в открытом файле', 'mbl_admin') ?></label>
                </div>
            </div>

            <br>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет линии прогресса', 'mbl_admin'), 'key' => 'progress.line_color', 'default' => '65bf49')) ?>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет фона', 'mbl_admin'), 'key' => 'progress.bg_color', 'default' => 'f5f5f5')) ?>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет процентов', 'mbl_admin'), 'key' => 'progress.text_color', 'default' => '4a4a4a')) ?>

            <?php wpm_render_partial('settings-save-button', 'common'); ?>
        </div>
        <div id="mbl_inner_tab_3_15" class="wpm-tab-content">
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет элементов видео-плеера', 'mbl_admin'), 'key' => 'player.color', 'default' => '3498db')) ?>

            <?php wpm_render_partial('settings-save-button', 'common'); ?>
        </div>
        <div id="mbl_inner_tab_3_16_1" class="wpm-tab-content">
            <div class="wpm-control-row">
                <label>
                    <input type="hidden" name="design_options[preloader_on]" value="0"/>
                    <input type="checkbox"
                           name="design_options[preloader_on]"
                           value="1"
	                    <?= wpm_get_design_option('preloader_on', '0') == '1' ? 'checked' : '' ?>
                    >
			        <?php _e('Включить', 'mbl_admin') ?></label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет прелодера', 'mbl_admin') ?><br>
                    <input type="text" name="design_options[preloader_color]"
                           class="color"
                           value="<?= wpm_get_design_option('preloader_color', '3A8D69'); ?>">
                </label>
            </div>
	        <?php wpm_render_partial( 'settings-save-button', 'common' ); ?>
        </div>
        <?php do_action('mbl_options_tab_3_content_after'); ?>
    </div>
</div>
