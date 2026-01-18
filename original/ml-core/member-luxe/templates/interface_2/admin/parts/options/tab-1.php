<div class="wpm-tab-content">
    <div class="wpm-inner-tabs" tab-id="h-tabs-1">
        <ul class="wpm-inner-tabs-nav">
            <li><a href="#mbl_inner_tab_1_1"><?php _e('Генерация пин-кодов', 'mbl_admin') ?></a></li>
            <li><a href="#mbl_inner_tab_1_2"><?php _e('Блокировка', 'mbl_admin') ?></a></li>
            <li><a href="#mbl_inner_tab_1_4"><?php _e('Комментарии', 'mbl_admin') ?></a></li>
            <li><a href="#mbl_inner_tab_1_5"><?php _e('Поиск', 'mbl_admin') ?></a></li>
            <li><a href="#mbl_inner_tab_1_6"><?php _e('Тексты', 'mbl_admin') ?></a></li>
            <li><a href="#mbl_inner_tab_1_8"><?php _e('Автоподписки', 'mbl_admin') ?></a></li>
            <li><a href="#mbl_inner_tab_1_9"><?php _e('Аудио', 'mbl_admin') ?></a></li>
            <li><a href="#mbl_inner_tab_1_10"><?php _e('Скрипты', 'mbl_admin') ?></a></li>
            <li><a href="#mbl_inner_tab_1_11"><?php _e('UTM метки', 'mbl_admin') ?></a></li>
        </ul>
        <div id="mbl_inner_tab_1_1" class="wpm-tab-content">
            <div class="wpm-row">
                <label>
                    <input type="hidden" name="main_options[pincode_page][generate]" value="off"/>
                    <input type="checkbox"
                           name="main_options[pincode_page][generate]"
                        <?php echo wpm_option_is('pincode_page.generate', 'on') ? 'checked' : ''; ?>>
                    <?php _e('Включить генерацию', 'mbl_admin') ?></label>
            </div>

            <?php
                echo apply_filters('mbl_render_setting_pincode_page_link_name', wpm_render_partial('options/text-row', 'admin', [
                        'key' => 'pincode_page.link_name',
                        'label' => __('Название ссылки', 'mbl_admin'),
                        'default' => __('Генерировать', 'mbl_admin'),
                        'rowClass' => 'wpm-row',
                        'class' => ''
                ], true));
            ?>

            <div class="wpm-row">
                <label><?php _e('Выберите уровень доступа', 'mbl_admin') ?></label>
                <?php $plain_levels = get_terms('wpm-levels', array()); ?>
                <div>
                    <select id="send_term_key_lvl" name="main_options[pincode_page][lvl]"
                            onchange="changeLinkedList(this, '#send_term_key')">
                        <option value="" disabled hidden ><?php _e('Не выбрано', 'mbl_admin') ?></option>
                        <?php foreach ($plain_levels AS $level) : ?>
                            <option
                                    value="<?php echo $level->term_id; ?>"
                                <?php echo wpm_is_pin_code_page_lvl($level->term_id) ? 'selected="selected"' : '' ?>
                            ><?php echo $level->name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

	        <?php do_action('pin_generator_setting_tab_after_wpm_level'); ?>

            <div class="wpm-row">
                <label><?php _e('Выберите код доступа', 'mbl_admin') ?></label>

                <div>
                    <select id="send_term_key" name="main_options[pincode_page][term_key]">
                        <option value=""></option>
                    </select>
                    <select id="send_term_key_src" name="term_key_src" style="display: none">
                        <option value=""></option>
                        <?php echo wpm_get_term_keys_options_for_pin_code_page($plain_levels); ?>
                    </select>
                </div>
            </div>
            <script type="text/javascript">
                function changeLinkedList(main, linked) {
                    var $ = jQuery,
                        val = $(main).val(),
                        linkedSrc,
                        options;

                    linked = $(linked);

                    if (linked.length) {
                        linkedSrc = $('#' + linked.attr('id') + '_src');
                        options = linkedSrc.find('option');

                        if (val != '') {
                            linked.prop('disabled', false);
                            if (linked.data('empty') == '1') {
                                linked.html('<option value=""></option>');
                            } else {
                                linked.html('');
                            }
                            options
                                .filter(function () {
                                    return $(this).data('main') == val;
                                })
                                .clone()
                                .appendTo(linked);
                        } else {
                            linked.prop('disabled', true);
                        }
                    }
                }

                jQuery(function () {
                    changeLinkedList('#send_term_key_lvl', '#send_term_key');
                });
            </script>

            <?php wpm_render_partial('settings-save-button', 'common'); ?>
        </div>
        <div id="mbl_inner_tab_1_2" class="wpm-tab-content">
            <div class="wpm-row">
                <label><input type="checkbox"
                              name="main_options[main][opened]"<?php if ($main_options['main']['opened'] == true) echo 'checked'; ?> ><?php _e('Снять блокировку', 'mbl_admin') ?><br>
                </label>
            </div>
            <div class="wpm-row">
                <label>
                    <input type="checkbox"
                           name="main_options[main][enable_captcha]"
                           id="mbl_enable_captcha"
                        <?php echo wpm_option_is('main.enable_captcha', 'on') ? ' checked' : ''; ?> >
                    <?php _e('Добавить капчу', 'mbl_admin') ?><br/>
                </label>
            </div>
            <div class="wpm-row" id="mbl_captcha_options" <?php echo wpm_option_is('main.enable_captcha', 'on') ? '' : 'style="display:none;"'; ?>>
                <label><?php _e('Ключ', 'mbl_admin') ?> reCAPTCHA<br>
                    <input type="text"
                           name="main_options[main][captcha_key]"
                           class="regular-text"
                           value="<?php echo wpm_get_option('main.captcha_key'); ?>">
                </label>
                <br>
                <br>
                <label><?php _e('Секретный ключ', 'mbl_admin') ?> reCAPTCHA<br>
                    <input type="text"
                           name="main_options[main][captcha_secret]"
                           class="regular-text"
                           value="<?php echo wpm_get_option('main.captcha_secret'); ?>">
                </label>
                <br>
                <h4><?php _e('Включить reCAPTCHA на следующих страницах:', 'mbl_admin') ?></h4>

                <div class="wpm-row">
                    <label>
                        <input type="checkbox"
                               name="main_options[main][enable_captcha_login]"
                            <?php echo wpm_option_is('main.enable_captcha_login', 'on') ? ' checked' : ''; ?> >
                        <?php _e('Вход', 'mbl_admin') ?><br/>
                    </label>
                </div>
                <div class="wpm-row">
                    <label>
                        <input type="checkbox"
                               name="main_options[main][enable_captcha_registration]"
                            <?php echo wpm_option_is('main.enable_captcha_registration', 'on') ? ' checked' : ''; ?> >
                        <?php _e('Регистрация', 'mbl_admin') ?><br/>
                    </label>
                </div>

                <?php do_action('mbl_settings_recaptcha'); ?>

                <div class="wpm-row">
                    <label>
                        <input type="checkbox"
                               name="main_options[main][enable_captcha_ask]"
                            <?php echo wpm_option_is('main.enable_captcha_ask', 'on') ? ' checked' : ''; ?> >
                        <?php _e('Задать вопрос', 'mbl_admin') ?><br/>
                    </label>
                </div>

            </div>
            <?php wpm_render_partial('settings-save-button', 'common'); ?>
        </div>
        <div id="mbl_inner_tab_1_4" class="wpm-tab-content">
            <div class="wpm-row">
                <div class="wpm-control-row">
                    <p><b><?php _e('Тип комментариев:', 'mbl_admin') ?></b></p>
                </div>
                <label>
                    <input type="radio"
                           class="wpm_comments_mode"
                           name="main_options[main][comments_mode]"
                           value="standard" <?php if (!wpm_option_is('main.comments_mode', 'cackle')) echo 'checked'; ?>> <?php _e('Стандартные', 'mbl_admin') ?></label><br/><br/>
                <label>
                    <input type="radio"
                           class="wpm_comments_mode"
                           name="main_options[main][comments_mode]"
                           value="cackle" <?php if (wpm_option_is('main.comments_mode', 'cackle')) echo 'checked'; ?>> Cackle
                </label>

                <?php if (defined('WPTELEGRAM_COMMENTS_VER')) : ?>
                    <br/><br/>
                    <label>
                        <input type="radio"
                               class="wpm_comments_mode"
                               name="main_options[main][comments_mode]"
                               value="telegram" <?php if (wpm_option_is('main.comments_mode', 'telegram')) echo 'checked'; ?>> Telegram
                    </label>
                <?php endif; ?>
            </div>
            <div class="wpm-row wpm-comment-cackle-row" <?php if (!wpm_option_is('main.comments_mode', 'cackle')) echo 'style="display:none;"'; ?>>
                <div class="wpm-control-row">
                    <p><b><?php _e('ID сайта Cackle:', 'mbl_admin') ?></b></p>
                </div>

                <input type="text"
                       name="main_options[main][cackle_id]"
                       id="cackle_id"
                       value="<?php echo wpm_get_option('main.cackle_id'); ?>"/>

                <br>
                <br>
                <label>
                    <input type="checkbox"
                           name="main_options[main][cackle_auto_update]"
                        <?php echo wpm_option_is('main.cackle_auto_update', 'on') ? ' checked' : ''; ?> >
                    <?php _e('Автообновление комментариев', 'mbl_admin') ?><br/>
                </label>

            </div>
            <div class="wpm-row wpm-comment-images-row" <?php if (wpm_option_is('main.comments_mode', 'cackle') || wpm_option_is('main.comments_mode', 'telegram')) echo 'style="display:none;"'; ?>>
                <div class="wpm-control-row">
                    <p><b><?php _e('Добавление файлов к комментариям:', 'mbl_admin') ?></b></p>
                </div>

                <?php $attachments_mode = array_key_exists('attachments_mode', $main_options['main']) ? $main_options['main']['attachments_mode'] : 'allowed_to_all'; ?>

                <label>
                    <input type="radio"
                           name="main_options[main][attachments_mode]"
                           value="allowed_to_all" <?php if ($attachments_mode == 'allowed_to_all') echo 'checked'; ?>> <?php _e('Доступно всем пользователям', 'mbl_admin') ?></label><br/><br/>
                <label>
                    <input type="radio"
                           name="main_options[main][attachments_mode]"
                           value="allowed_to_admin" <?php if ($attachments_mode == 'allowed_to_admin') echo 'checked'; ?>> <?php _e('Только администратору', 'mbl_admin') ?></label><br/><br/>
                <label>
                    <input type="radio"
                           name="main_options[main][attachments_mode]"
                           value="disabled" <?php if ($attachments_mode == 'disabled') echo 'checked'; ?>> <?php _e('Недоступно', 'mbl_admin') ?></label>

            </div>

            <div class="wpm-row">
                <div class="wpm-control-row">
                    <p><b><?php _e('Видимость комментариев:', 'mbl_admin') ?></b></p>
                </div>

                <?php $visibility = array_key_exists('visibility', $main_options['main']) ? $main_options['main']['visibility'] : 'to_all'; ?>

                <label>
                    <input type="radio"
                           name="main_options[main][visibility]"
                           value="to_all" <?php if ($visibility == 'to_all') echo 'checked'; ?>> <?php _e('Показывать всем', 'mbl_admin') ?></label><br/><br/>
                <label>
                    <input type="radio"
                           name="main_options[main][visibility]"
                           value="to_registered" <?php if ($visibility == 'to_registered') echo 'checked'; ?>> <?php _e('Только зарегистрированным пользователям', 'mbl_admin') ?></label>
            </div>

            <?php wpm_render_partial('settings-save-button', 'common'); ?>
        </div>
        <div id="mbl_inner_tab_1_5" class="wpm-tab-content">
            <div class="wpm-row">
                <label>
                    <input type="hidden" name="main_options[main][search_visible]" value="off">
                    <input type="checkbox"
                           name="main_options[main][search_visible]"
                        <?php echo wpm_option_is('main.search_visible', 'on', 'on') ? 'checked' : ''; ?>
                    >
                    <?php _e('Отображать', 'mbl_admin') ?></label>
            </div>

            <?php wpm_render_partial('settings-save-button', 'common'); ?>
        </div>
        <div id="mbl_inner_tab_1_6" class="wpm-tab-content">
            <div class="wpm-control-row">
                <p><?php _e('Редактирование текстов', 'mbl_admin') ?> MEMBERLUX</p>
            </div>

            <!-- Табы выбора языка -->
            <?php
            $current_lang = MBLTranslator::getCurrentLanguage();
            $available_languages = MBLTranslator::getAvailableLanguages();

            // Находим индекс текущего языка
            $active_tab_index = 0;
            foreach ($available_languages as $index => $lang) {
                if ($lang['code'] === $current_lang) {
                    $active_tab_index = $index;
                    break;
                }
            }
            ?>
            <!-- Скрытое поле для сохранения текущего активного языка -->
            <input type="hidden" name="mbl_active_language" id="mbl_active_language" value="<?php echo esc_attr($current_lang); ?>">

            <div class="wpm-inner-tabs mbl-language-tabs" tab-id="mbl-lang-tabs" data-active-tab="<?php echo $active_tab_index; ?>">
                <ul class="wpm-inner-tabs-nav">
                    <?php foreach ($available_languages as $lang) : ?>
                        <li>
                            <a href="#mbl_lang_tab_<?php echo esc_attr($lang['code']); ?>" data-lang-code="<?php echo esc_attr($lang['code']); ?>">
                                <?php echo esc_html($lang['name']); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <!-- Контент табов с переводами -->
                <?php foreach (MBLTranslator::getAvailableLanguages() as $lang) : 
                    $is_active = ($lang['code'] === $current_lang);
                    $translations = MBLTranslator::getTranslationsForLanguage($lang['code']);
                ?>
                    <div id="mbl_lang_tab_<?php echo esc_attr($lang['code']); ?>" 
                         class="wpm-tab-content mbl-lang-tab"
                         data-lang="<?php echo esc_attr($lang['code']); ?>"
                         data-is-active="<?php echo $is_active ? '1' : '0'; ?>">
                        <ol class="mbl-translations-list">
                            <?php foreach ($translations as $translationRow) : ?>
                                <li class="wpm-control-row" 
                                    data-hash="<?php echo esc_attr($translationRow->hash); ?>"
                                    data-msgstr="<?php echo esc_attr($translationRow->msgstr); ?>">
                                    <label>
                                        <?php if ($is_active) : ?>
                                            <input type="text"
                                                   class="large-text mbl-translation-input"
                                                   name="translations[<?php echo esc_attr($translationRow->hash); ?>]"
                                                   value="<?php echo esc_attr($translationRow->msgstr); ?>"
                                            >
                                        <?php else : ?>
                                            <span class="mbl-translation-text"><?php echo esc_html($translationRow->msgstr); ?></span>
                                        <?php endif; ?>
                                    </label>
                                </li>
                            <?php endforeach; ?>
                        </ol>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php wpm_render_partial('settings-save-button', 'common'); ?>
        </div>
        <div id="mbl_inner_tab_1_8" class="wpm-tab-content">

            <div class="wpm-row">
                <label>
                    <input type="radio" name="main_options[subscribe][type]" value="subscribe_just_click"
                           class="letter_options"
                        <?= wpm_option_is('auto_subscriptions.justclick.active', 'on') ? 'checked' : '' ?>>
                    <?php _e('Автоподписка JustClick', 'mbl_admin') ?>
                </label>
                <div id="subscribe_just_click_api_key_label" class="letter_options_label
                    <?= wpm_option_is('auto_subscriptions.justclick.active', 'on') ? '':'invisible'?>">
                    <div>
                        <p>
                            <label>
                                <input type="checkbox"
                                       name="main_options[auto_subscriptions][justclick][active]"
                                    <?=wpm_option_is('auto_subscriptions.justclick.active', 'on') ? 'checked' :''?>
                                       class="active_checkbox">
                                <?php _e('Включить', 'mbl_admin') ?>
                            </label>
                        </p>

                        <p>
                            <label>
                                <input type="checkbox"
                                       name="main_options[auto_subscriptions][justclick][auto_disable]"
                                    <?php echo autoDisable('justclick', $main_options) ? ' checked' : ''; ?>/>
                                <?php _e('По истечению срока действия пин-кода удалить пользователя из рассылки', 'mbl_admin') ?></label>
                        </p>

                        <div>
                            <p>
                                <label><?php _e('Логин', 'mbl_admin') ?><br>
                                    <input type="text"
                                           name="main_options[auto_subscriptions][justclick][user_id]"
                                           value="<?php echo $main_options['auto_subscriptions']['justclick']['user_id']; ?>">
                                </label>
                            </p>
                            <p>
                                <label><?php _e('Секретный ключ для подписи', 'mbl_admin') ?><br>
                                    <input type="text"
                                           name="main_options[auto_subscriptions][justclick][user_rps_key]"
                                           value="<?php echo $main_options['auto_subscriptions']['justclick']['user_rps_key']; ?>">
                                </label>
                            </p>
                        </div>

                        <?php if (MBLSubscription::direct('justclick', 'credentialsFilled')) : ?>
                            <div>
                                <div id="justclick_groups"><div class="wpm-inline-loader"></div></div>
                            </div>

                            <p>
                                <label><?php _e('Адрес после подтверждения', 'mbl_admin') ?> (<?php _e('не обязательно', 'mbl_admin') ?>)<br>
                                    <input type="text"
                                           name="main_options[auto_subscriptions][justclick][doneurl2]"
                                           value="<?php echo $main_options['auto_subscriptions']['justclick']['doneurl2']; ?>">
                                </label>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
                <br>
                <label>
                    <input type="radio" name="main_options[subscribe][type]" value="subscribe_send_pulse"
                           class="letter_options"
                        <?= wpm_option_is('auto_subscriptions.sendpulse.active', 'on') ? 'checked' :''?>>
                    <?php _e('Автоподписка SendPulse', 'mbl_admin') ?>
                </label>
                <div id="subscribe_send_pulse_api_key_label" class="letter_options_label <?=
                    wpm_option_is('auto_subscriptions.sendpulse.active', 'on') ? '' : 'invisible'?>">
                <p>
                    <label>
                        <input type="checkbox"
                               name="main_options[auto_subscriptions][sendpulse][active]"
                               class="active_checkbox"
                            <?= wpm_option_is('auto_subscriptions.sendpulse.active', 'on') ? 'checked' : ''?>>
                        <?php _e('Включить', 'mbl_admin') ?></label>
                </p>
                <p>
                    <label>
                        <input type="checkbox"
                               name="main_options[auto_subscriptions][sendpulse][auto_disable]"
                            <?php echo autoDisable('sendpulse', $main_options) ? ' checked' : ''; ?>/>
                        <?php _e('По истечению срока действия пин-кода удалить пользователя из рассылки', 'mbl_admin') ?></label>
                </p>
                <p>
                    <label><?php _e('Секретный API USER ID', 'mbl_admin') ?><br>
                        <input type="text"
                               name="main_options[auto_subscriptions][sendpulse][apiUserId]"
                               value="<?php echo $main_options['auto_subscriptions']['sendpulse']['apiUserId']; ?>">
                    </label>
                </p>
                <p>
                    <label><?php _e('Секретный Api Secret', 'mbl_admin') ?><br>
                        <input type="text"
                               name="main_options[auto_subscriptions][sendpulse][apiSecret]"
                               value="<?php echo $main_options['auto_subscriptions']['sendpulse']['apiSecret']; ?>">
                    </label>
                </p>
                <?php if (MBLSubscription::direct('sendpulse', 'credentialsFilled')) : ?>
                    <div>
                        <div id="sendpulse_groups"><div class="wpm-inline-loader"></div></div>
                    </div>
                <?php endif; ?>

            </div>
            <br>
            <label>
                <input type="radio" name="main_options[subscribe][type]" value="subscribe_auto_web"
                       class="letter_options"
                    <?= wpm_option_is('auto_subscriptions.autoweb.active', 'on') ? 'checked' : ''?>>
                <?php _e('Автоподписка АвтоВебОфис', 'mbl_admin') ?>
            </label>
            <div id="subscribe_auto_web_api_key_label"
                 class="letter_options_label <?= wpm_option_is('auto_subscriptions.autoweb.active', 'on') ? '' : 'invisible'?>">
                <div>
                    <p>
                        <label>
                            <input type="checkbox"
                                   name="main_options[auto_subscriptions][autoweb][active]"
                                   class="active_checkbox"
                                <?= wpm_option_is('auto_subscriptions.autoweb.active', 'on') ? 'checked' : ''?>>
                            <?php _e('Включить', 'mbl_admin') ?></label>
                    </p>
                    <p>
                        <label>
                            <input type="checkbox"
                                   name="main_options[auto_subscriptions][autoweb][auto_disable]"
                                <?php echo autoDisable('autoweb', $main_options) ? ' checked' : ''; ?>/>
                            <?php _e('По истечению срока действия пин-кода удалить пользователя из рассылки', 'mbl_admin') ?></label>
                    </p>
                    <p>
                        <label><?php _e('Секретный ключ API KEY GET', 'mbl_admin') ?><br>
                            <input type="text"
                                   name="main_options[auto_subscriptions][autoweb][apiKeyRead]"
                                   value="<?php echo $main_options['auto_subscriptions']['autoweb']['apiKeyRead']; ?>">
                        </label>
                    </p>
                    <p>
                        <label><?php _e('Секретный ключ API KEY SET', 'mbl_admin') ?><br>
                            <input type="text"
                                   name="main_options[auto_subscriptions][autoweb][apiKeyWrite]"
                                   value="<?php echo $main_options['auto_subscriptions']['autoweb']['apiKeyWrite']; ?>">
                        </label>
                    </p>
                    <p>
                        <label><?php _e('Идентификатор магазина', 'mbl_admin') ?><br>
                            <input type="text"
                                   name="main_options[auto_subscriptions][autoweb][subdomain]"
                                   value="<?php echo $main_options['auto_subscriptions']['autoweb']['subdomain']; ?>">
                        </label>
                    </p>
                    <?php if (MBLSubscription::direct('autoweb', 'credentialsFilled')) : ?>
                        <div>
                            <div id="autoweb_groups"><div class="wpm-inline-loader"></div></div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php wpm_render_partial('settings-save-button', 'common'); ?>
    </div>
    <div id="mbl_inner_tab_1_9" class="wpm-tab-content">
        <div class="wpm-control-row">
            <h3><?php _e('Аудио-плеер', 'mbl_admin') ?></h3>
        </div>
        <div class="wpm-control-row wpm-audio-settings">
            <label>
                <input type="radio"
                       name="main_options[audio][player]"
                       data-radio-toggle="wpm_audio"
                       value="mediaelement"
                    <?php echo !wpm_option_is('audio.player', 'wavesurfer') ? 'checked' : ''; ?>
                >Mediaelement Player
            </label>
            <div data-radio-toggle-holder="wpm_audio"
                <?php echo wpm_option_is('audio.player', 'wavesurfer') ? 'style="display:none;"' : ''; ?>
                 data-value="mediaelement"
                 class="mediaelement-preview">
            </div>
            <br>
            <br>
            <label>
                <input type="radio"
                       name="main_options[audio][player]"
                       data-radio-toggle="wpm_audio"
                       value="wavesurfer"
                    <?php echo wpm_option_is('audio.player', 'wavesurfer') ? 'checked' : ''; ?>
                >Wave Surfer Player
            </label>
            <div data-radio-toggle-holder="wpm_audio"
                <?php echo !wpm_option_is('audio.player', 'wavesurfer') ? 'style="display:none;"' : ''; ?>
                 data-value="wavesurfer">
                <div class="wavesurfer-preview"></div>

                <div class="wpm-help-wrap">
                    <p><?php _e('Аудио файлы должны быть загружены на ваш сайт', 'mbl_admin') ?> (<?php _e('например в Медиафайлы', 'mbl_admin') ?>)</p>
                </div>
            </div>
        </div>

        <?php wpm_render_partial('settings-save-button', 'common'); ?>
    </div>
    <div id="mbl_inner_tab_1_10" class="wpm-tab-content">
        <div class="wpm-control-row">
            <p>&lt;head&gt; <span class="text_green"><?php _e('Ваш код', 'mbl_admin') ?></span> &lt;/head&gt;</p>
            <label>
                    <textarea name="main_options[header_scripts]" class="wpm-wide code-style"
                              placeholder="<?php _e('Ваш код', 'mbl_admin') ?>"
                              rows="20"><?php echo stripslashes($main_options['header_scripts']); ?></textarea>
            </label>
        </div>
        <?php wpm_render_partial('settings-save-button', 'common'); ?>
    </div>
    <div id="mbl_inner_tab_1_11" class="wpm-tab-content">
        <div class="wpm-control-row">
            <label>
                <?php _e('Время хранения', 'mbl_admin')
                ?> <input name="main_options[utm_expiration_days]"
                          type="number"
                          value="<?= $main_options['utm_expiration_days'] ?? '7' ?>"
                          min="1" max="366"/> дней</label>
        </div>
        <?php wpm_render_partial('settings-save-button', 'common'); ?>
    </div>
</div>
</div>
