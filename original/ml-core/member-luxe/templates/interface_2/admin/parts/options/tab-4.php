<div class="wpm-tab-content">
    <div class="wpm-inner-tabs" tab-id="h-tabs-4">
        <ul class="wpm-inner-tabs-nav">
            <li><a href="#mbl_inner_tab_4_1"><?php _e('Материалы', 'mbl_admin') ?></a></li>
            <li><a href="#mbl_inner_tab_4_2"><?php _e('Домашние задания', 'mbl_admin') ?></a></li>
            <li><a href="#mbl_inner_tab_4_3"><?php _e('Редактор', 'mbl_admin') ?></a></li>
        </ul>
        <div id="mbl_inner_tab_4_1" class="wpm-tab-content">
            <div class="wpm-control-row">
                <label>
                    <input type="hidden" name="design_options[page][show_all]" value="0">
                    <input type="checkbox"
                              name="design_options[page][show_all]"
                        <?php if (wpm_get_design_option('page.show_all') == 'on') echo 'checked'; ?>>
                    <?php _e('Отображать все уроки автотренингов', 'mbl_admin') ?></label>
            </div>
            <?php wpm_render_partial('settings-save-button', 'common'); ?>
        </div>
        <div id="mbl_inner_tab_4_2" class="wpm-tab-content">
            <div class="wpm-row">
                <dl>
                    <dt><?php _e('Выберите порядок сортировки комментариев к домашним заданиям:', 'mbl_admin') ?><br><br></dt>
                    <dd>
                        <label for="comments_order_asc">
                            <input type="radio" name="main_options[main][comments_order]"
                                   id="comments_order_asc"
                                   value="asc" <?php echo !wpm_option_is('main.comments_order', 'desc') ? 'checked' : ''; ?> />
                            <?php _e('От более ранних к более поздним', 'mbl_admin') ?></label>
                    </dd>
                    <dd>
                        <label for="comments_order_desc">
                            <input type="radio" name="main_options[main][comments_order]"
                                   id="comments_order_desc"
                                   value="desc" <?php echo wpm_option_is('main.comments_order', 'desc') ? 'checked' : ''; ?> />
                            <?php _e('От более поздних до более ранних', 'mbl_admin') ?></label>
                    </dd>
                </dl>
            </div>

            <div class="wpm-row">
                <dl>
                    <dt><?php _e('Добавление файлов к домашним заданиям:', 'mbl_admin') ?><br><br></dt>

                    <dd>
                         <label>
                            <input type="radio"
                                   name="main_options[homework_attachments_mode]"
                                   value="allowed_to_all" <?php echo wpm_option_is('homework_attachments_mode', 'allowed_to_all', 'allowed_to_all') ? 'checked' : ''; ?>> <?php _e('Доступно всем пользователям', 'mbl_admin') ?>
                        </label>
                    </dd>

                    <dd>
                        <label>
                            <input type="radio"
                                   name="main_options[homework_attachments_mode]"
                                   value="allowed_to_admin" <?php echo wpm_option_is('homework_attachments_mode', 'allowed_to_admin') ? 'checked' : ''; ?>> <?php _e('Только администратору', 'mbl_admin') ?></label>
                    </dd>

                    <dd>
                        <label>
                            <input type="radio"
                                   name="main_options[homework_attachments_mode]"
                                   value="disabled" <?php echo wpm_option_is('homework_attachments_mode', 'disabled') ? 'checked' : ''; ?>> <?php _e('Недоступно', 'mbl_admin') ?></label>
                    </dd>
                </dl>
            </div>

            <?php wpm_render_partial('settings-save-button', 'common'); ?>
        </div>

        <div id="mbl_inner_tab_4_3" class="wpm-tab-content">
            <div class="wpm-row">
                <dl>
                    <dt><?php _e('Выберите версию редактора:', 'mbl_admin') ?><br><br></dt>
                    <dd>
                        <label for="redactor_standart_version">
                            <input type="radio" name="main_options[main][redactor]"
                                   id="redactor_standart_version"
                                   value="standart" <?php echo !wpm_option_is('main.redactor', 'new') ? 'checked' : ''; ?> />
                            <?php _e('Стандартный', 'mbl_admin') ?></label>
                    </dd>
                    <dd>
                        <label for="redactor_new_version">
                            <input type="radio" name="main_options[main][redactor]"
                                   id="redactor_new_version"
                                   value="new" <?php echo wpm_option_is('main.redactor', 'new') ? 'checked' : ''; ?> />
                            <?php _e('Новая версия (BETA)', 'mbl_admin') ?></label>
                    </dd>
                </dl>
            </div>

            <?php wpm_render_partial('settings-save-button', 'common'); ?>
        </div>
    </div>
</div>