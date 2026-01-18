<div class="wpm-tab-content">
    <div class="wpm-inner-tabs" tab-id="h-tabs-6">
        <ul class="wpm-inner-tabs-nav">
            <li><a href="#mbl_inner_tab_6_1"><?php _e('Защита контента', 'mbl_admin') ?></a></li>
            <li><a href="#mbl_inner_tab_6_2"><?php _e('Ограничения для пользователей', 'mbl_admin') ?></a></li>
        </ul>
        <div id="mbl_inner_tab_6_1" class="wpm-tab-content">
            <div class="wpm-row">
                <label>
                    <input type="hidden" name="main_options[protection][video_url_encoded]" value="off"/>
                    <input type="checkbox"
                           id="video_url_encoded"
                           name="main_options[protection][video_url_encoded]"
                           <?php echo wpm_option_is('protection.video_url_encoded', 'on') ? 'checked="checked"' : '' ?> >
                    <?php _e('Включить шифрование ссылок .mp4', 'mbl_admin') ?>
                    <br>
                </label>
            </div>

            <div class="wpm-row">
                <label>
                    <input type="checkbox"
                           name="main_options[protection][right_button_disabled]" <?php echo wpm_option_is('protection.right_button_disabled', 'on') ? 'checked' : ''; ?> > <?php _e('Отключить правую кнопку мыши', 'mbl_admin') ?></label>
            </div>

            <div class="wpm-row">
                <?php $text_protection_is_enabled = wpm_text_protection_is_enabled($main_options); ?>
                <label>
                    <input id="wpm_text_protection_chbx" type="checkbox"
                           name="main_options[protection][text_protected]" <?php echo $text_protection_is_enabled ? 'checked' : ''; ?> > <?php _e('Запретить копирование текста', 'mbl_admin') ?></label>
            </div>
            <?php
                $args = array(
                    'post_type' => 'wpm-page',
                    'nopaging' => true
                );
                $wpm_pages = new WP_Query($args);
            ?>
            <?php if ($wpm_pages->have_posts()) : ?>
                <div
                    class="wpm-protection-exceptions" <?php echo $text_protection_is_enabled ? '' : 'style="display:none;"' ?>>
                    <?php _e('Исключения:', 'mbl_admin') ?>
                    <?php while ($wpm_pages->have_posts()): ?>
                        <?php $wpm_pages->the_post(); ?>
                        <div class="wpm-row">
                            <label>
                                <?php $checked = $text_protection_is_enabled && !wpm_text_protection_is_enabled($main_options, get_the_ID()); ?>
                                <input type="checkbox"
                                       name="main_options[protection][text_protected_exceptions][]"
                                       value="<?php echo get_the_ID(); ?>" <?php echo $checked ? 'checked="checked"' : '' ?>>
                                <?php echo get_the_title(); ?>
                            </label>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php endif; ?>

            <div class="wpm-row">
                <label>
                    <input type="hidden" name="main_options[protection][youtube_standard_player]" value="0">
                    <input type="checkbox"
                           name="main_options[protection][youtube_standard_player]"
                           value="1"
                        <?php echo wpm_option_is('protection.youtube_standard_player', '1') ? 'checked' : ''; ?>
                    > <?php _e('Использовать Стандартный YouTube плеер для воспроизведения видео с YouTube', 'mbl_admin') ?>
                </label>
            </div>

            <div class="wpm-row">
                <label>
                    <input type="hidden" name="main_options[protection][vimeo_standard_player]" value="0">
                    <input type="checkbox"
                           name="main_options[protection][vimeo_standard_player]"
                           value="1"
                        <?php echo wpm_option_is('protection.vimeo_standard_player', '1') ? 'checked' : ''; ?>
                    > <?php _e('Использовать Стандартный Vimeo плеер для воспроизведения видео с Vimeo', 'mbl_admin') ?>
                </label>
            </div>
            
            <div class="wpm-row">
               <p><b><?php _e('Версия плеера Plyr', 'mbl_admin') ?></b></p>
                <label>
                    <input type="radio"
                           name="main_options[protection][plyr_version]"
                           value="2.0.11"
                        <?php echo wpm_option_is('protection.plyr_version', '2.0.11', '3.6.7') ? 'checked' : ''; ?>
                    > 2.0.11
                </label>
                <br>
                <label>
                    <input type="radio"
                           name="main_options[protection][plyr_version]"
                           value="3.4.7"
                        <?php echo wpm_option_is('protection.plyr_version', '3.4.7', '3.6.7') ? 'checked' : ''; ?>
                    > 3.4.7
                </label>
                <br>
                <label>
                    <input type="radio"
                           name="main_options[protection][plyr_version]"
                           value="3.6.7"
                        <?php echo wpm_option_is('protection.plyr_version', '3.6.7', '3.6.7') ? 'checked' : ''; ?>
                    > 3.6.7
                </label>
                <br>
                <label>
                    <input type="radio"
                           name="main_options[protection][plyr_version]"
                           value="3.8.3"
                        <?php echo wpm_option_is('protection.plyr_version', '3.8.3', '3.6.7') ? 'checked' : ''; ?>
                    > 3.8.3
                </label>
            </div>

            <div class="wpm-row" id="mbl_youtube_protection" <?php echo !wpm_option_is('protection.plyr_version', '2.0.11') ? '' : 'style="display:none;"'; ?>>
                <label>
                    <input type="hidden" name="main_options[protection][youtube_hide_controls]" value="0">
                    <input type="checkbox"
                           name="main_options[protection][youtube_hide_controls]"
                           value="1"
                        <?php echo wpm_option_is('protection.youtube_hide_controls', '1') ? 'checked' : ''; ?>
                    > <?php _e('Скрыть элементы управления YouTube', 'mbl_admin') ?>
                </label>
            </div>

            <?php wpm_render_partial('settings-save-button', 'common'); ?>
        </div>
        <div id="mbl_inner_tab_6_2" class="wpm-tab-content">
            <div class="wpm-row">
                <label>
                    <input id="wpm_session_protection" type="checkbox"
                           name="main_options[protection][one_session][status]" <?php echo (wpm_array_get($main_options, 'protection.one_session.status') == 'on') ? 'checked' : ''; ?> > <?php _e('Запретить множественную авторизацию.', 'mbl_admin') ?></label>
            </div>
            <div class="wpm-row">
                <label>
                    <?php _e('Интервал проверки акаунтов:', 'mbl_admin') ?> <input id="wpm_session_protection_interval" type="number"
                           name="main_options[protection][one_session][interval]" value="<?php echo wpm_array_get($main_options, 'protection.one_session.interval'); ?>" > <?php _e('секунд', 'mbl_admin') ?></label>
            </div>

            <?php wpm_render_partial('settings-save-button', 'common'); ?>
        </div>
    </div>
</div>