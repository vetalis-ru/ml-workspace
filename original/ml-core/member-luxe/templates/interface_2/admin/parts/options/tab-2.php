<div class="wpm-tab-content">
    <div class="wpm-inner-tabs" tab-id="h-tabs-2">
        <ul class="wpm-inner-tabs-nav">
            <li><a href="#mbl_inner_tab_2_1"><?php _e('Стартовая', 'mbl_admin') ?></a></li>
            <li><a href="#mbl_inner_tab_2_2"><?php _e('Расписание', 'mbl_admin') ?></a></li>
            <li><a href="#mbl_inner_tab_2_3"><?php _e('Вход', 'mbl_admin') ?></a></li>
            <li><a href="#mbl_inner_tab_2_4"><?php _e('Регистрация', 'mbl_admin') ?></a></li>
            <li><a href="#mbl_inner_tab_2_5"><?php _e('Пользовательское соглашение', 'mbl_admin') ?></a></li>
            <li><a href="#mbl_inner_tab_2_5_2"><?php _e('Соглашение №2', 'mbl_admin') ?></a></li>
            <li><a href="#mbl_inner_tab_2_5_3"><?php _e('Соглашение №3', 'mbl_admin') ?></a></li>
            <li><a href="#mbl_inner_tab_2_5_4"><?php _e('Соглашение №4', 'mbl_admin') ?></a></li>
            <li><a href="#mbl_inner_tab_2_6"><?php _e('Постраничная навигация', 'mbl_admin') ?></a></li>
            <li><a href="#mbl_inner_tab_2_7"><?php _e('Восстановление пароля', 'mbl_admin') ?></a></li>
            <li><a href="#mbl_inner_tab_2_8"><?php _e('Доступные', 'mbl_admin') ?></a></li>
            <li><a href="#mbl_inner_tab_2_9"><?php _e('Избранное', 'mbl_admin') ?></a></li>
            <?php $tabs = []; $tabs = apply_filters('mbl_settings_page_tabs', $tabs);
                $offset = 10;
                foreach ($tabs as $i => $tab): ?>
                    <li>
                        <a href="#mbl_inner_tab_2_<?= $offset + $i ?>">
                            <?= $tab['title'] ?>
                        </a>
                    </li>
            <?php endforeach; ?>
        </ul>
        <div id="mbl_inner_tab_2_1" class="wpm-tab-content">
            <?php _e('Стартовая страница:', 'mbl_admin') ?><?php
            $start_page = '';
            $args = array(
                'post_type' => 'wpm-page',
                'nopaging' => true
            );
            $wpm_pages = new WP_Query($args);
            $wpm_pages_select = '';
            if ($wpm_pages->have_posts()): while ($wpm_pages->have_posts()): $wpm_pages->the_post();

                $selected = '';
                if (apply_filters('mbl_get_main_page', $main_options['home_id']) == get_the_ID()) {
                    $selected = 'selected';
                    $start_page = get_permalink();
                }
                $wpm_pages_select .= '<option value="' . get_the_ID() . '" ' . $selected . '>' . get_the_title() . '</option>';
            endwhile;
	            $wpm_pages_select = apply_filters( 'start_page_options_select', $wpm_pages_select );
	            $start_page = apply_filters( 'start_page_url', $start_page );
                $wpm_pages_select = '<select id="mbl_main_options_home_id" name="' . apply_filters('mbl_home_id_field_name', 'main_options[home_id]') . '">' . $wpm_pages_select . '</select>';
            endif;
            echo $wpm_pages_select;

            do_action('mbl_after_home_id_select');
            ?>
            <br>
            <label>
                <?php
                if ($main_options['make_home_start']) {
                    ?>
                    <input type="checkbox" name="main_options[make_home_start]" checked>
                <?php } else { ?>
                    <input type="checkbox" name="main_options[make_home_start]">
                <?php } ?>
                <?php _e('Сделать главной страницей сайта', 'mbl_admin') ?></label>
            <br>
            <br>

            <?php _e('Описание:', 'mbl_admin') ?>
            <div class="wpm-row" style="padding-left: 25px; margin-bottom: 0; margin-top: 5px">
                <label>
                    <input type="hidden" name="main_options[homepage][show_description]" value="0">
                    <input
                        type="checkbox"
                        name="main_options[homepage][show_description]"
                        value="1"
                        <?php echo wpm_get_option('homepage.show_description', 1) ? 'checked="checked"' : ''; ?>
                    >
                    <?php _e('Отображать', 'mbl_admin') ?></label>
            </div>
            <div class="wpm-row" style="padding-left: 50px; margin-bottom: 0; margin-top: 5px">
                <label>
                    <input type="hidden" name="main_options[homepage][description_expand]" value="0">
                    <input
                        type="checkbox"
                        name="main_options[homepage][description_expand]"
                        value="1"
                        <?php echo wpm_get_option('homepage.description_expand', 0) ? 'checked="checked"' : ''; ?>
                    >
                    <?php _e('Открывать при входе', 'mbl_admin') ?></label>
            </div>
            <div class="wpm-row" style="padding-left: 50px; margin-bottom: 0; margin-top: 5px">
                <label>
                    <input type="hidden" name="main_options[homepage][show_description_always]" value="0">
                    <input
                        type="checkbox"
                        name="main_options[homepage][show_description_always]"
                        value="1"
                        <?php echo wpm_get_option('homepage.show_description_always', 0) ? 'checked="checked"' : ''; ?>
                    >
                    <?php _e('Зафиксировать открытым', 'mbl_admin') ?></label>
            </div>
            <br>

            <div class="wpm-row">
                <label>
                    <?php _e('Стартовая страница', 'mbl_admin') ?></label><br>

                <div class="code">
                    <?php echo mb_convert_encoding( $start_page, 'UTF-8', 'ISO-8859-1' ); ?>
                </div>
                <label>
                    <?php _e('Страница входа пользователя', 'mbl_admin') ?></label><br>

                <div class="code">
                    <?php echo mb_convert_encoding( $start_page, 'UTF-8', 'ISO-8859-1' ); ?>#login
                </div>
                <label>
                    <?php _e('Страница регистрации пользователя', 'mbl_admin') ?></label><br>

                <div class="code">
                    <?php echo mb_convert_encoding( $start_page, 'UTF-8', 'ISO-8859-1' ); ?>#registration
                </div>

            </div>

            <?php wpm_render_partial('settings-save-button', 'common'); ?>
        </div>
        <div id="mbl_inner_tab_2_2" class="wpm-tab-content">
            <div class="wpm-row">
                <?php
                $schedule_page = '';
                $args = array(
                    'post_type' => 'wpm-page',
                    'nopaging' => true
                );
                $wpm_pages = new WP_Query($args);
                $wpm_pages_select = '';
                if ($main_options['schedule_id'] == 'no') {
                    $wpm_pages_select .= '<option value="no" selected>' . __("-- Не задано --", "mbl_admin") . '</option>';
                } else {
                    $wpm_pages_select .= '<option value="no">' . __("-- Не задано --", "mbl_admin") . '</option>';
                }
                if ($wpm_pages->have_posts()): while ($wpm_pages->have_posts()): $wpm_pages->the_post();
                    $selected = '';
                    if ($main_options['schedule_id'] == get_the_ID()) {
                        $selected = 'selected';
                        $schedule_page = get_the_permalink();
                    }
                    $wpm_pages_select .= '<option value="' . get_the_ID() . '" ' . $selected . '>' . get_the_title() . '</option>';
                endwhile;
                    $wpm_pages_select = '<select name="main_options[schedule_id]">' . $wpm_pages_select . '</select>';
                endif;
                echo $wpm_pages_select;
                ?>
            </div>
            <div class="wpm-row">
                <label>
                    <input type="hidden" name="main_options[hide_schedule]" value="off"/>
                    <input type="checkbox"
                           name="main_options[hide_schedule]"
                        <?php echo (array_key_exists('hide_schedule', $main_options) && $main_options['hide_schedule'] == 'on') ? 'checked' : ''; ?>>
                    <?php _e('Спрятать расписание', 'mbl_admin') ?></label>
            </div>
            <?php wpm_render_partial('settings-save-button', 'common'); ?>
        </div>
        <div id="mbl_inner_tab_2_3" class="wpm-tab-content">
            <div class="wpm-control-row">
                <p><?php _e('Контент для отображения на странице входа в систему.', 'mbl_admin') ?></p>
            </div>
            <div class="wpm-control-row">
                <label>
                    <input type="hidden" name="main_options[login_content][visible]" value="off">
                    <input type="checkbox"
                              name="main_options[login_content][visible]" <?php if ($main_options['login_content']['visible'] == 'on') echo 'checked'; ?>><?php _e('Отображать', 'mbl_admin') ?></label>
                &nbsp;&nbsp;&nbsp;
                <label>
                    <input type="hidden" name="main_options[login_content_opened]" value="off">
                    <input type="checkbox"
                              name="main_options[login_content_opened]" <?php if (wpm_option_is('login_content_opened', 'on')) echo 'checked'; ?>><?php _e('Зафиксировать открытым', 'mbl_admin') ?></label>
                &nbsp;&nbsp;&nbsp;
                <label><input type="radio"
                              name="main_options[login_content][position]"
                              value="top" <?php if ($main_options['login_content']['position'] == 'top') echo 'checked'; ?>><?php _e('Вверху', 'mbl_admin') ?></label>
                &nbsp;
                <label><input type="radio"
                              name="main_options[login_content][position]"
                              value="bottom" <?php if ($main_options['login_content']['position'] == 'bottom') echo 'checked'; ?>><?php _e('Внизу', 'mbl_admin') ?></label>
            </div>
            <div class="wpm-control-row">
                <?php wp_editor($main_options['login_content']['content'], 'wpm_login_content', array('textarea_name' => 'main_options[login_content][content]', 'editor_height' => 300)); ?>
            </div>
            <div class="wpm-row wpm-row-2-labels">
                <?php
                wpm_render_partial('fields/checkbox', 'admin',
                    array(
                        'label' => __('Использовать форму входа в панель администратора MEMBERUX', 'mbl_admin'),
                        'name' => 'main_options[wp_login_page_memberlux]',
                        'value' => wpm_get_option('wp_login_page_memberlux')
                    ))
                ?>
            </div>

            <?php wpm_render_partial('settings-save-button', 'common'); ?>
        </div>
        <div id="mbl_inner_tab_2_4" class="wpm-tab-content">
            <div class="wpm-row">
                <label>
                    <input type="checkbox"
                           name="main_options[registration_form][surname]"
                        <?php echo wpm_reg_field_is_enabled($main_options, 'surname') ? ' checked' : ''; ?> />
                    <?php _e('Фамилия', 'mbl_admin'); ?>
                </label>
            </div>
            <div class="wpm-row">
                <label>
                    <input type="checkbox"
                           name="main_options[registration_form][name]"
                        <?php echo wpm_reg_field_is_enabled($main_options, 'name') ? ' checked' : ''; ?> />
                    <?php _e('Имя', 'mbl_admin'); ?>
                </label>
            </div>
            <div class="wpm-row">
                <label>
                    <input type="checkbox"
                           name="main_options[registration_form][patronymic]"
                        <?php echo wpm_reg_field_is_enabled($main_options, 'patronymic') ? ' checked' : ''; ?> />
                    <?php _e('Отчество', 'mbl_admin'); ?>
                </label>
            </div>
            <div class="wpm-row wpm-row-disabled"
                 title="<?php _e('Это поле нельзя убрать из регистрационной формы', 'mbl_admin') ?>">
                <label>
                    <input type="checkbox" disabled checked/> <?php _e('Email', 'mbl'); ?>
                </label>
            </div>
            <div class="wpm-row">
                <label>
                    <input type="checkbox"
                           name="main_options[registration_form][phone]"
                        <?php echo wpm_reg_field_is_enabled($main_options, 'phone') ? ' checked' : ''; ?> />
                    <?php _e('Телефон', 'mbl_admin'); ?>
                </label>
            </div>

            <div class="wpm-row <?php echo apply_filters('registration_form_disabled_row', 'wpm-row-disabled'); ?>"
                 title="<?php echo apply_filters('registration_form_disabled_row', __('Это поле нельзя убрать из регистрационной формы', 'mbl_admin')); ?>">
                <label>
                    <input type="checkbox"
                           name="main_options[registration_form][login]"
                           <?php echo apply_filters('registration_form_login_enabled', 'disabled checked'); ?>
                    /> <?php _e('Желаемый логин', 'mbl_admin'); ?>
                </label>
            </div>
            <div class="wpm-row <?php echo apply_filters('registration_form_disabled_row', 'wpm-row-disabled'); ?>"
                 title="<?php echo apply_filters('registration_form_disabled_row', __('Это поле нельзя убрать из регистрационной формы', 'mbl_admin')); ?>">
                <label>
                    <input type="checkbox"
                           name="main_options[registration_form][pass]"
	                       <?php echo apply_filters('registration_form_pass_enabled', 'disabled checked'); ?>
                    /> <?php _e('Желаемый пароль', 'mbl_admin'); ?>
                </label>
            </div>
            <div class="wpm-row <?php echo apply_filters('registration_form_disabled_row', 'wpm-row-disabled'); ?>"
                 title="<?php echo apply_filters('registration_form_disabled_row', __('Это поле нельзя убрать из регистрационной формы', 'mbl_admin')); ?>">
                <label>
                    <input type="checkbox"
                           name="main_options[registration_form][code]"
	                       <?php echo apply_filters('registration_form_code_enabled', 'disabled checked'); ?>
                    /> <?php _e('Код доступа', 'mbl_admin'); ?>
                </label>
            </div>

            <div class="wpm-row">
                <label>
                    <input type="checkbox"
                           name="main_options[registration_form][custom1]"
                        <?php echo wpm_reg_field_is_enabled($main_options, 'custom1') ? ' checked' : ''; ?> />
                </label>
                <input type="text" name="main_options[registration_form][custom1_label]"
                       placeholder="<?php echo __('Дополнительное поле', 'mbl_admin'); ?>"
                       value="<?php echo $main_options['registration_form']['custom1_label'] ?>">
            </div>
            <div class="wpm-row">
                <label>
                    <input type="checkbox"
                           name="main_options[registration_form][custom2]"
                        <?php echo wpm_reg_field_is_enabled($main_options, 'custom2') ? ' checked' : ''; ?> />
                </label>
                <input type="text" name="main_options[registration_form][custom2_label]"
                       placeholder="<?php echo __('Дополнительное поле', 'mbl_admin'); ?>"
                       value="<?php echo $main_options['registration_form']['custom2_label'] ?>">
            </div>
            <div class="wpm-row">
                <label>
                    <input type="checkbox"
                           name="main_options[registration_form][custom3]"
                        <?php echo wpm_reg_field_is_enabled($main_options, 'custom3') ? ' checked' : ''; ?> />
                </label>
                <input type="text" name="main_options[registration_form][custom3_label]"
                       placeholder="<?php echo __('Дополнительное поле', 'mbl_admin'); ?>"
                       value="<?php echo $main_options['registration_form']['custom3_label'] ?>">
            </div>
            <div class="wpm-row">
                <label>
                    <input type="checkbox"
                           name="main_options[registration_form][custom4]"
                        <?php echo wpm_reg_field_is_enabled($main_options, 'custom4') ? ' checked' : ''; ?> />
                </label>
                <input type="text" name="main_options[registration_form][custom4_label]"
                       placeholder="<?php echo __('Дополнительное поле', 'mbl_admin'); ?>"
                       value="<?php echo $main_options['registration_form']['custom4_label'] ?>">
            </div>
            <div class="wpm-row">
                <label>
                    <input type="checkbox"
                           name="main_options[registration_form][custom5]"
                        <?php echo wpm_reg_field_is_enabled($main_options, 'custom5') ? ' checked' : ''; ?> />
                </label>
                <input type="text" name="main_options[registration_form][custom5_label]"
                       placeholder="<?php echo __('Дополнительное поле', 'mbl_admin'); ?>"
                       value="<?php echo $main_options['registration_form']['custom5_label'] ?>">
            </div>
            <div class="wpm-row">
                <label>
                    <input type="checkbox"
                           name="main_options[registration_form][custom6]"
                        <?php echo wpm_reg_field_is_enabled($main_options, 'custom6') ? ' checked' : ''; ?> />
                </label>
                <input type="text" name="main_options[registration_form][custom6_label]"
                       placeholder="<?php echo __('Дополнительное поле', 'mbl_admin'); ?>"
                       value="<?php echo $main_options['registration_form']['custom6_label'] ?>">
            </div>
            <div class="wpm-row">
                <label>
                    <input type="checkbox"
                           name="main_options[registration_form][custom7]"
				        <?php echo wpm_reg_field_is_enabled($main_options, 'custom7') ? ' checked' : ''; ?> />
                </label>
                <input type="text" name="main_options[registration_form][custom7_label]"
                       placeholder="<?php echo __('Дополнительное поле', 'mbl_admin'); ?>"
                       value="<?php echo $main_options['registration_form']['custom7_label'] ?>">
            </div>
            <div class="wpm-row">
                <label>
                    <input type="checkbox"
                           name="main_options[registration_form][custom8]"
				        <?php echo wpm_reg_field_is_enabled($main_options, 'custom8') ? ' checked' : ''; ?> />
                </label>
                <input type="text" name="main_options[registration_form][custom8_label]"
                       placeholder="<?php echo __('Дополнительное поле', 'mbl_admin'); ?>"
                       value="<?php echo $main_options['registration_form']['custom8_label'] ?>">
            </div>
            <div class="wpm-row">
                <label>
                    <input type="checkbox"
                           name="main_options[registration_form][custom9]"
				        <?php echo wpm_reg_field_is_enabled($main_options, 'custom9') ? ' checked' : ''; ?> />
                </label>
                <input type="text" name="main_options[registration_form][custom9_label]"
                       placeholder="<?php echo __('Дополнительное поле', 'mbl_admin'); ?>"
                       value="<?php echo $main_options['registration_form']['custom9_label'] ?>">
            </div>
            <div class="wpm-row">
                <label>
                    <input type="checkbox"
                           name="main_options[registration_form][custom10]"
				        <?php echo wpm_reg_field_is_enabled($main_options, 'custom10') ? ' checked' : ''; ?> />
                </label>
                <input type="text" name="main_options[registration_form][custom10_label]"
                       placeholder="<?php echo __('Дополнительное поле', 'mbl_admin'); ?>"
                       value="<?php echo $main_options['registration_form']['custom10_label'] ?>">
            </div>

            <div class="wpm-row">
                <label>
                    <input type="checkbox"
                           name="main_options[registration_form][custom1textarea]"
                        <?php echo wpm_reg_field_is_enabled($main_options, 'custom1textarea') ? ' checked' : ''; ?> />
                </label>
                <textarea
                        name="main_options[registration_form][custom1textarea_label]"
                        style="height: 120px; min-width: 280px;"
                        placeholder="<?php echo __('Дополнительная текстовая область', 'mbl_admin'); ?>"><?php echo $main_options['registration_form']['custom1textarea_label'] ?></textarea>
            </div>

            <?php wpm_render_partial('settings-save-button', 'common'); ?>
        </div>
        <div id="mbl_inner_tab_2_5" class="wpm-tab-content">
            <div class="wpm-row wpm-row-2-labels">
                <label>
                    <input type="checkbox"
                           name="main_options[user_agreement][enabled_login]"
                        <?php echo wpm_option_is('user_agreement.enabled_login', 'on') ? ' checked' : ''; ?> >
                    <?php _e('Вход в систему', 'mbl_admin') ?>
                    <input type="text"
                           name="main_options[user_agreement][login_link_title]"
                           class="regular-text"
                           value="<?php echo wpm_get_option('user_agreement.login_link_title', __('Пользовательское соглашение', 'mbl_admin')); ?>"
                    >
                </label>
                <?php do_action('mbl_user_agreement_login_setting_after'); ?>
            </div>
            <div class="wpm-row wpm-row-2-labels">
                <label>
                    <input type="checkbox"
                           name="main_options[user_agreement][enabled_registration]"
                        <?php echo wpm_option_is('user_agreement.enabled_registration', 'on') ? ' checked' : ''; ?> >
                    <?php _e('Регистрация', 'mbl_admin') ?>
                    <input type="text"
                           name="main_options[user_agreement][registration_link_title]"
                           class="regular-text"
                           value="<?php echo wpm_get_option('user_agreement.registration_link_title', __('пользовательское соглашение', 'mbl_admin')); ?>"
                    >
                </label>
                <?php do_action('mbl_user_agreement_registration_setting_after'); ?>
            </div>
            <div class="wpm-row">
                <label>
                    <input type="checkbox"
                           name="main_options[user_agreement][enabled_footer]"
                        <?php echo wpm_option_is('user_agreement.enabled_footer', 'on') ? ' checked' : ''; ?> >
                    <?php _e('Подвал системы', 'mbl_admin') ?>
                    <input type="text"
                           name="main_options[user_agreement][footer_link_title]"
                           class="regular-text"
                           value="<?php echo wpm_get_option('user_agreement.footer_link_title', __('Пользовательское соглашение', 'mbl_admin')); ?>"
                    >
                </label>
            </div>
            <div class="wpm-row">
                <label>
                    <?php _e('Название', 'mbl_admin') ?>
                    <input type="text"
                           name="main_options[user_agreement][title]"
                           class="large-text"
                           value="<?php echo wpm_get_option('user_agreement.title', __('Пользовательское соглашение', 'mbl_admin')); ?>"
                    >
                </label>
            </div>
            <div class="wpm-control-row">
                <?php wp_editor(stripslashes(wpm_get_option('user_agreement.text')), 'wpm_user_agreement_text', array('textarea_name' => 'main_options[user_agreement][text]', 'editor_height' => 300)); ?>
            </div>
            <?php wpm_render_partial('settings-save-button', 'common'); ?>
        </div>
        
        <!-- Соглашение №2 -->
        <div id="mbl_inner_tab_2_5_2" class="wpm-tab-content">
            <div class="wpm-row wpm-row-2-labels">
                <label>
                    <input type="checkbox"
                           name="main_options[user_agreement_2][enabled_login]"
                        <?php echo wpm_option_is('user_agreement_2.enabled_login', 'on') ? ' checked' : ''; ?> >
                    <?php _e('Вход в систему', 'mbl_admin') ?>
                    <input type="text"
                           name="main_options[user_agreement_2][login_link_title]"
                           class="regular-text"
                           value="<?php echo wpm_get_option('user_agreement_2.login_link_title', __('Соглашение №2', 'mbl_admin')); ?>"
                    >
                </label>
                &nbsp;&nbsp;
                <label>
                    <input type="checkbox"
                           name="main_options[user_agreement_2][login_required]"
                        <?php echo wpm_option_is('user_agreement_2.login_required', 'on') ? ' checked' : ''; ?> >
                    <?php _e('Обязательно при входе', 'mbl_admin') ?>
                </label>
                <?php do_action('mbl_user_agreement_2_login_setting_after'); ?>
            </div>
            <div class="wpm-row wpm-row-2-labels">
                <label>
                    <input type="checkbox"
                           name="main_options[user_agreement_2][enabled_registration]"
                        <?php echo wpm_option_is('user_agreement_2.enabled_registration', 'on') ? ' checked' : ''; ?> >
                    <?php _e('Регистрация', 'mbl_admin') ?>
                    <input type="text"
                           name="main_options[user_agreement_2][registration_link_title]"
                           class="regular-text"
                           value="<?php echo wpm_get_option('user_agreement_2.registration_link_title', __('соглашение №2', 'mbl_admin')); ?>"
                    >
                </label>
                &nbsp;&nbsp;
                <label>
                    <input type="checkbox"
                           name="main_options[user_agreement_2][registration_required]"
                        <?php echo wpm_option_is('user_agreement_2.registration_required', 'on') ? ' checked' : ''; ?> >
                    <?php _e('Обязательно при регистрации', 'mbl_admin') ?>
                </label>
                <?php do_action('mbl_user_agreement_2_registration_setting_after'); ?>
            </div>
            <div class="wpm-row">
                <label>
                    <input type="checkbox"
                           name="main_options[user_agreement_2][enabled_footer]"
                        <?php echo wpm_option_is('user_agreement_2.enabled_footer', 'on') ? ' checked' : ''; ?> >
                    <?php _e('Подвал системы', 'mbl_admin') ?>
                    <input type="text"
                           name="main_options[user_agreement_2][footer_link_title]"
                           class="regular-text"
                           value="<?php echo wpm_get_option('user_agreement_2.footer_link_title', __('Соглашение №2', 'mbl_admin')); ?>"
                    >
                </label>
            </div>
            <div class="wpm-row">
                <label>
                    <?php _e('Название', 'mbl_admin') ?>
                    <input type="text"
                           name="main_options[user_agreement_2][title]"
                           class="large-text"
                           value="<?php echo wpm_get_option('user_agreement_2.title', __('Соглашение №2', 'mbl_admin')); ?>"
                    >
                </label>
            </div>
            <div class="wpm-control-row">
                <?php wp_editor(stripslashes(wpm_get_option('user_agreement_2.text')), 'wpm_user_agreement_2_text', array('textarea_name' => 'main_options[user_agreement_2][text]', 'editor_height' => 300)); ?>
            </div>
            <?php wpm_render_partial('settings-save-button', 'common'); ?>
        </div>
        
        <!-- Соглашение №3 -->
        <div id="mbl_inner_tab_2_5_3" class="wpm-tab-content">
            <div class="wpm-row wpm-row-2-labels">
                <label>
                    <input type="checkbox"
                           name="main_options[user_agreement_3][enabled_login]"
                        <?php echo wpm_option_is('user_agreement_3.enabled_login', 'on') ? ' checked' : ''; ?> >
                    <?php _e('Вход в систему', 'mbl_admin') ?>
                    <input type="text"
                           name="main_options[user_agreement_3][login_link_title]"
                           class="regular-text"
                           value="<?php echo wpm_get_option('user_agreement_3.login_link_title', __('Соглашение №3', 'mbl_admin')); ?>"
                    >
                </label>
                &nbsp;&nbsp;
                <label>
                    <input type="checkbox"
                           name="main_options[user_agreement_3][login_required]"
                        <?php echo wpm_option_is('user_agreement_3.login_required', 'on') ? ' checked' : ''; ?> >
                    <?php _e('Обязательно при входе', 'mbl_admin') ?>
                </label>
                <?php do_action('mbl_user_agreement_3_login_setting_after'); ?>
            </div>
            <div class="wpm-row wpm-row-2-labels">
                <label>
                    <input type="checkbox"
                           name="main_options[user_agreement_3][enabled_registration]"
                        <?php echo wpm_option_is('user_agreement_3.enabled_registration', 'on') ? ' checked' : ''; ?> >
                    <?php _e('Регистрация', 'mbl_admin') ?>
                    <input type="text"
                           name="main_options[user_agreement_3][registration_link_title]"
                           class="regular-text"
                           value="<?php echo wpm_get_option('user_agreement_3.registration_link_title', __('соглашение №3', 'mbl_admin')); ?>"
                    >
                </label>
                &nbsp;&nbsp;
                <label>
                    <input type="checkbox"
                           name="main_options[user_agreement_3][registration_required]"
                        <?php echo wpm_option_is('user_agreement_3.registration_required', 'on') ? ' checked' : ''; ?> >
                    <?php _e('Обязательно при регистрации', 'mbl_admin') ?>
                </label>
                <?php do_action('mbl_user_agreement_3_registration_setting_after'); ?>
            </div>
            <div class="wpm-row">
                <label>
                    <input type="checkbox"
                           name="main_options[user_agreement_3][enabled_footer]"
                        <?php echo wpm_option_is('user_agreement_3.enabled_footer', 'on') ? ' checked' : ''; ?> >
                    <?php _e('Подвал системы', 'mbl_admin') ?>
                    <input type="text"
                           name="main_options[user_agreement_3][footer_link_title]"
                           class="regular-text"
                           value="<?php echo wpm_get_option('user_agreement_3.footer_link_title', __('Соглашение №3', 'mbl_admin')); ?>"
                    >
                </label>
            </div>
            <div class="wpm-row">
                <label>
                    <?php _e('Название', 'mbl_admin') ?>
                    <input type="text"
                           name="main_options[user_agreement_3][title]"
                           class="large-text"
                           value="<?php echo wpm_get_option('user_agreement_3.title', __('Соглашение №3', 'mbl_admin')); ?>"
                    >
                </label>
            </div>
            <div class="wpm-control-row">
                <?php wp_editor(stripslashes(wpm_get_option('user_agreement_3.text')), 'wpm_user_agreement_3_text', array('textarea_name' => 'main_options[user_agreement_3][text]', 'editor_height' => 300)); ?>
            </div>
            <?php wpm_render_partial('settings-save-button', 'common'); ?>
        </div>
        
        <!-- Соглашение №4 -->
        <div id="mbl_inner_tab_2_5_4" class="wpm-tab-content">
            <div class="wpm-row wpm-row-2-labels">
                <label>
                    <input type="checkbox"
                           name="main_options[user_agreement_4][enabled_login]"
                        <?php echo wpm_option_is('user_agreement_4.enabled_login', 'on') ? ' checked' : ''; ?> >
                    <?php _e('Вход в систему', 'mbl_admin') ?>
                    <input type="text"
                           name="main_options[user_agreement_4][login_link_title]"
                           class="regular-text"
                           value="<?php echo wpm_get_option('user_agreement_4.login_link_title', __('Соглашение №4', 'mbl_admin')); ?>"
                    >
                </label>
                &nbsp;&nbsp;
                <label>
                    <input type="checkbox"
                           name="main_options[user_agreement_4][login_required]"
                        <?php echo wpm_option_is('user_agreement_4.login_required', 'on') ? ' checked' : ''; ?> >
                    <?php _e('Обязательно при входе', 'mbl_admin') ?>
                </label>
                <?php do_action('mbl_user_agreement_4_login_setting_after'); ?>
            </div>
            <div class="wpm-row wpm-row-2-labels">
                <label>
                    <input type="checkbox"
                           name="main_options[user_agreement_4][enabled_registration]"
                        <?php echo wpm_option_is('user_agreement_4.enabled_registration', 'on') ? ' checked' : ''; ?> >
                    <?php _e('Регистрация', 'mbl_admin') ?>
                    <input type="text"
                           name="main_options[user_agreement_4][registration_link_title]"
                           class="regular-text"
                           value="<?php echo wpm_get_option('user_agreement_4.registration_link_title', __('соглашение №4', 'mbl_admin')); ?>"
                    >
                </label>
                &nbsp;&nbsp;
                <label>
                    <input type="checkbox"
                           name="main_options[user_agreement_4][registration_required]"
                        <?php echo wpm_option_is('user_agreement_4.registration_required', 'on') ? ' checked' : ''; ?> >
                    <?php _e('Обязательно при регистрации', 'mbl_admin') ?>
                </label>
                <?php do_action('mbl_user_agreement_4_registration_setting_after'); ?>
            </div>
            <div class="wpm-row">
                <label>
                    <input type="checkbox"
                           name="main_options[user_agreement_4][enabled_footer]"
                        <?php echo wpm_option_is('user_agreement_4.enabled_footer', 'on') ? ' checked' : ''; ?> >
                    <?php _e('Подвал системы', 'mbl_admin') ?>
                    <input type="text"
                           name="main_options[user_agreement_4][footer_link_title]"
                           class="regular-text"
                           value="<?php echo wpm_get_option('user_agreement_4.footer_link_title', __('Соглашение №4', 'mbl_admin')); ?>"
                    >
                </label>
            </div>
            <div class="wpm-row">
                <label>
                    <?php _e('Название', 'mbl_admin') ?>
                    <input type="text"
                           name="main_options[user_agreement_4][title]"
                           class="large-text"
                           value="<?php echo wpm_get_option('user_agreement_4.title', __('Соглашение №4', 'mbl_admin')); ?>"
                    >
                </label>
            </div>
            <div class="wpm-control-row">
                <?php wp_editor(stripslashes(wpm_get_option('user_agreement_4.text')), 'wpm_user_agreement_4_text', array('textarea_name' => 'main_options[user_agreement_4][text]', 'editor_height' => 300)); ?>
            </div>
            <?php wpm_render_partial('settings-save-button', 'common'); ?>
        </div>
        
        <div id="mbl_inner_tab_2_6" class="wpm-tab-content">
            <div class="wpm-row">
                <label><?php _e('Сколько рубрик отображать на одной странице?', 'mbl_admin') ?><br>
                    <input type="number" name="main_options[main][terms_per_page]"
                           value="<?php echo wpm_get_option('main.terms_per_page', 12); ?>"
                           size="3"
                           maxlength="3">
                </label>
            </div>

            <div class="wpm-help-wrap">
                <p>(-1) <?php _e('показать все на одной странице', 'mbl_admin') ?></p>
            </div>

            <div class="wpm-row">
                <label><?php _e('Сколько материалов отображать на одной странице?', 'mbl_admin') ?><br>
                    <input type="number" name="main_options[main][posts_per_page]"
                           value="<?php echo wpm_get_option('main.posts_per_page', -1); ?>"
                           size="3"
                           maxlength="3">
                </label>
            </div>

            <div class="wpm-help-wrap">
                <p>(-1) <?php _e('показать все на одной странице', 'mbl_admin') ?></p>
            </div>

            <div class="wpm-row">
                <label><?php _e('Сколько товаров отображать на одной странице?', 'mbl_admin') ?><br>
                    <input type="number" name="main_options[main][product_per_page]"
                           value="<?php echo wpm_get_option('main.product_per_page', -1); ?>"
                           size="3"
                           maxlength="3">
                </label>
            </div>

            <div class="wpm-help-wrap">
                <p>(-1) <?php _e('показать все на одной странице', 'mbl_admin') ?></p>
            </div>

            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет фона', 'mbl_admin'), 'key' => 'pagination.bg_color', 'default' => 'fbfbfb')) ?>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет текста', 'mbl_admin'), 'key' => 'pagination.color', 'default' => '7e7e7e')) ?>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет рамки', 'mbl_admin'), 'key' => 'pagination.border_color', 'default' => 'c1c1c1')) ?>
            <br>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет фона при наведении', 'mbl_admin'), 'key' => 'pagination.hover_bg_color', 'default' => 'fbfbfb')) ?>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет текста при наведении', 'mbl_admin'), 'key' => 'pagination.hover_color', 'default' => '000000')) ?>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет рамки при наведении', 'mbl_admin'), 'key' => 'pagination.hover_border_color', 'default' => 'c1c1c1')) ?>
            <br>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет фона текущей страницы', 'mbl_admin'), 'key' => 'pagination.active_bg_color', 'default' => 'c1c1c1')) ?>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет текста текущей страницы', 'mbl_admin'), 'key' => 'pagination.active_color', 'default' => 'ffffff')) ?>
            <?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет рамки текущей страницы', 'mbl_admin'), 'key' => 'pagination.active_border_color', 'default' => 'c1c1c1')) ?>

            <?php wpm_render_partial('settings-save-button', 'common'); ?>
        </div>
        <div id="mbl_inner_tab_2_7" class="wpm-tab-content">
            <div class="wpm-row wpm-row-2-labels">
                <label>
                    <?php _e('Получить новый пароль', 'mbl_admin') ?>
                    <input type="text"
                           name="main_options[user_new_pass_btn]"
                           class="regular-text"
                           value="<?= wpm_get_option('user_new_pass_btn', 'Получить новый пароль'); ?>"
                    >
                </label>
            </div>
            <div class="wpm-row wpm-row-2-labels">
                <?php
                wpm_render_partial('fields/checkbox', 'admin',
                    array(
                        'label' => __('Использовать форму восстановления пароля MEMBERLUX', 'mbl_admin'),
                        'name' => 'main_options[lostpassword_page_memberlux]',
                        'value' => wpm_get_option('lostpassword_page_memberlux')
                    ))
                ?>
            </div>
            <?php wpm_render_partial('settings-save-button', 'common'); ?>
        </div>
        <div id="mbl_inner_tab_2_8" class="wpm-tab-content">
            <div class="wpm-row wpm-row-2-labels">
                <label>
                    <?php _e('Текст:', 'mbl_admin') ?>
                    <input type="text"
                           name="main_options[my_courses_empty_text]"
                           class="regular-text"
                           value="<?= wpm_get_option('my_courses_empty_text', 'Раздел доступных курсов пока пуст'); ?>"
                    >
                </label>
            </div>
            <?php wpm_render_partial('settings-save-button', 'common'); ?>
        </div>
        <div id="mbl_inner_tab_2_9" class="wpm-tab-content">
            <div class="wpm-row wpm-row-2-labels">
                <label>
                    <?php _e('Текст:', 'mbl_admin') ?>
                    <input type="text"
                           name="main_options[favorite_empty_text]"
                           class="regular-text"
                           value="<?= wpm_get_option('favorite_empty_text', 'Раздел избранного пока пуст'); ?>"
                    >
                </label>
            </div>
            <?php wpm_render_partial('settings-save-button', 'common'); ?>
        </div>
        <?php foreach ($tabs as $i => $tab): $tab_id = $tab['id'] ?>
            <div id="mbl_inner_tab_2_<?= $offset + $i ?>" class="wpm-tab-content">
                <?php do_action("mbl_settings_page_tab_content_$tab_id") ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>
