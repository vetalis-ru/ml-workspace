<div class="wpm-tab-content">
    <div class="wpm-inner-tabs" tab-id="h-tabs-5">
        <ul class="wpm-inner-tabs-nav">
            <li><a href="#mbl_inner_tab_5_1"><?php _e('Настройки отправки писем', 'mbl_admin') ?></a></li>
            <li><a href="#mbl_inner_tab_5_2"><?php _e('Письмо при регистрации пользователя', 'mbl_admin') ?></a></li>
            <li><a href="#mbl_inner_tab_5_3"><?php _e('Автотренинги', 'mbl_admin') ?></a></li>
            <li><a href="#mbl_inner_tab_5_4"><?php _e('Уведомление о новом комментарии', 'mbl_admin') ?></a></li>
            <li><a href="#mbl_inner_tab_5_5"><?php _e('Задать вопрос', 'mbl_admin') ?></a></li>
        </ul>
        <div id="mbl_inner_tab_5_1" class="wpm-tab-content">
            <div class="wpm-row">
                <?php $mandrill_is_on = (array_key_exists('mandrill_is_on', $main_options['letters']) && $main_options['letters']['mandrill_is_on'] == 'on'); ?>
                <?php $ses_is_on = (array_key_exists('ses_is_on', $main_options['letters']) && $main_options['letters']['ses_is_on'] == 'on') ?>
                <?php
                $ses_hosts = array(
                    'EU (Ireland)'          => 'email.eu-west-1.amazonaws.com',
                    'US East (N. Virginia)' => 'email.us-east-1.amazonaws.com',
                    'US West (Oregon)'      => 'email.us-west-2.amazonaws.com',
                );
                ?>

                <label>
                    <input type="radio"
                           name="main_options[letters][type]"
                           value="mandrill"
                           class="letter_options"
                           id="mandrill_is_on" <?php echo $mandrill_is_on ? 'checked' : ''; ?>><?php _e('Отправлять письма через', 'mbl_admin') ?> Mandrill</label>
                <div id="mandrill_api_key_label"
                       class="<?php echo $mandrill_is_on ? '' : 'invisible'; ?> letter_options_label">
                    <?php _e('Укажите', 'mbl_admin') ?> Mandrill API key  &nbsp; <input type="text"
                                                                                   name="main_options[letters][mandrill_api_key]"
                                                                                   id="mandrill_api_key"
                                                                                   class="large-text"
                                                                                   value="<?php echo $main_options['letters']['mandrill_api_key']; ?>"/>
                </div>
                <br/>
                <label>
                    <input type="radio"
                           name="main_options[letters][type]"
                           value="ses"
                           class="letter_options"
                           id="ses_is_on" <?php echo $ses_is_on ? 'checked' : ''; ?>><?php _e('Отправлять письма через', 'mbl_admin') ?> Amazon SES</label>
                <div id="ses_api_key_label"
                       class="<?php echo $ses_is_on ? '' : 'invisible'; ?> letter_options_label">
                    <?php _e('Укажите', 'mbl_admin') ?> Amazon SES Access Key ID &nbsp;
                    <input type="text"
                           name="main_options[letters][ses_access_key]"
                           id="ses_access_key"
                           class="large-text"
                           value="<?php echo $main_options['letters']['ses_access_key']; ?>"/>
                    <br><br/>
                    <?php _e('Укажите', 'mbl_admin') ?> Amazon SES Secret Access Key &nbsp;
                    <input type="text"
                           name="main_options[letters][ses_secret_key]"
                           id="ses_secret_key"
                           class="large-text"
                           value="<?php echo $main_options['letters']['ses_secret_key']; ?>"/>
                    <br><br/>
                    <?php _e('Укажите верифицированный email', 'mbl_admin') ?> &nbsp;
                    <input type="text"
                           name="main_options[letters][ses_email]"
                           id="ses_email"
                           class="large-text"
                           value="<?php echo $main_options['letters']['ses_email']; ?>"/>
                    <br><br/>
                    <?php _e('Укажите регион', 'mbl_admin') ?> &nbsp;
                    <select id="ses_host"
                            name="main_options[letters][ses_host]"
                            class="users-level">
                        <?php foreach ($ses_hosts AS $host_name => $ses_host) : ?>
                            <option value="<?php echo $ses_host; ?>"
                                <?php echo $main_options['letters']['ses_host'] == $ses_host ? 'selected="selected"' : '' ?>
                            ><?php echo $host_name; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <br/>
                    <br/>
                    <div>
                        <button type="button" class="button" id="test_ses"><?php _e('Отправить тестовое письмо', 'mbl_admin') ?></button>
                        <div id="test_ses_response"></div>
                    </div>
                </div>
                <br/>
                <label>
                    <input type="radio"
                           name="main_options[letters][type]"
                           value="wp"
                           class="letter_options"
                           id="wpmail_is_on" <?php echo !$ses_is_on && !$mandrill_is_on ? 'checked' : ''; ?>><?php _e('Отправлять письма через Wordpress', 'mbl_admin') ?></label>
            </div>
            <div>
                <p>Применить авторизацию к шорткодам:</p>
                <?php wpm_render_partial(
                    'fields/checkbox',
                    'admin',
                    array(
                        'label' => '[start_page]',
                        'name' => 'main_options[start_page_is_auth]',
                        'value' => wpm_get_option( 'start_page_is_auth', 'off' )
                    )
                ) ?>
                <?php wpm_render_partial(
                    'fields/checkbox',
                    'admin',
                    array(
                        'label' => '[material_url]',
                        'name' => 'main_options[material_url_is_auth]',
                        'value' => wpm_get_option( 'material_url_is_auth', 'off' )
                    )
                ) ?>
                <?php wpm_render_partial(
                    'fields/checkbox',
                    'admin',
                    array(
                        'label' => '[page_link]',
                        'name' => 'main_options[page_link_is_auth]',
                        'value' => wpm_get_option( 'page_link_is_auth', 'off' )
                    )
                ) ?>
                <?php wpm_render_partial(
                    'fields/checkbox',
                    'admin',
                    array(
                        'label' => '[user_key_link]',
                        'name' => 'main_options[user_key_link_is_auth]',
                        'value' => wpm_get_option( 'user_key_link_is_auth', 'off' )
                    )
                ) ?>
            </div>
            <?php wpm_render_partial('settings-save-button', 'common'); ?>
        </div>
        <div id="mbl_inner_tab_5_2" class="wpm-tab-content">
            <div class="wpm-row">
                <label>
                    <?php _e('Заголовок письма', 'mbl_admin') ?><br>
                    <input type="text" name="main_options[letters][registration][title]"
                           value="<?php echo $main_options['letters']['registration']['title'] ?>"
                           class="large-text">
                </label>

            </div>
            <div class="wpm-control-row">
                <?php
                wp_editor(stripslashes($main_options['letters']['registration']['content']), 'wpm_letter_registration', array('textarea_name' => 'main_options[letters][registration][content]', 'editor_height' => 300));
                ?>
            </div>
            <div class="wpm-help-wrap">
                <p>
                    <span class="code-string">[user_name]</span> - <?php _e('имя пользователя', 'mbl_admin') ?><br>
                    <span class="code-string">[user_login]</span> - <?php _e('логин пользователя', 'mbl_admin') ?><br>
                    <span class="code-string">[user_pass]</span> - <?php _e('пароль пользователя', 'mbl_admin') ?><br>
                    <span class="code-string">[start_page]</span> - <?php _e('страница входа', 'mbl_admin') ?><br>
                    <?php wpm_auto_login_shortcodes_tips() ?>
                </p>
            </div>

            <?php wpm_render_partial('settings-save-button', 'common'); ?>
        </div>
        <div id="mbl_inner_tab_5_3" class="wpm-tab-content">
            <div id="tabs-level-5-3-1"
                 tab-id="headers-tabs-5-mails"
                 class="tabs-level-3 headers-design-tabs wpm-inner-tabs-nav">
                <ul>
                    <li class="ui-state-default ui-state-disabled" header-id="mail_user">
                        <a href='#header-tab-mails-user'><?php _e('Пользователь', 'mbl_admin') ?></a>
                    </li>
                    <li class="ui-state-default ui-state-disabled" header-id="mail_coach">
                        <a href='#header-tab-mails-coach'><?php _e('Тренер', 'mbl_admin') ?></a>
                    </li>
                    <li class="ui-state-default ui-state-disabled" header-id="mail_admin">
                        <a href='#header-tab-mails-admin'><?php _e('Администратор', 'mbl_admin') ?></a>
                    </li>
                </ul>

                <?php wpm_render_partial('options/mails/user', 'admin', compact('main_options')) ?>
                <?php wpm_render_partial('options/mails/coach', 'admin', compact('main_options')) ?>
                <?php wpm_render_partial('options/mails/admin', 'admin', compact('main_options')) ?>

                <?php wpm_render_partial('settings-save-button', 'common'); ?>
            </div>
        </div>
        <div id="mbl_inner_tab_5_4" class="wpm-tab-content">
            <div class="wpm-row">
                <label>
                    <?php _e('Заголовок письма', 'mbl_admin') ?><br>
                    <input type="text" name="main_options[letters][comment_subscription][title]"
                           value="<?php echo wpm_get_option('letters.comment_subscription.title') ?>"
                           class="large-text">
                </label>

            </div>
            <div class="wpm-control-row">
                <?php
                wp_editor(stripslashes(wpm_get_option('letters.comment_subscription.content')), 'wpm_letter_comment_subscription', array('textarea_name' => 'main_options[letters][comment_subscription][content]', 'editor_height' => 300));
                ?>
            </div>
            <div class="wpm-help-wrap">
                <p>
                    <span class="code-string">[user_name]</span> - <?php _e('имя пользователя', 'mbl_admin') ?><br>
                    <span class="code-string">[page_link]</span> - <?php _e('ссылка на страницу', 'mbl_admin') ?><br>
                    <span class="code-string">[page_title]</span> - <?php _e('название страницы', 'mbl_admin') ?><br>
                </p>
            </div>

            <?php wpm_render_partial('settings-save-button', 'common'); ?>
        </div>
        <div id="mbl_inner_tab_5_5" class="wpm-tab-content">
            <div class="wpm-row">
                <label>
                    <?php if (array_key_exists('hide_ask', $main_options['main']) && $main_options['main']['hide_ask'] == 'hide') { ?>
                        <input type="checkbox" name="main_options[main][hide_ask]" value="hide"
                               checked="checked">
                        <?php
                    } else { ?>
                        <input type="checkbox" name="main_options[main][hide_ask]" value="hide">
                    <?php } ?>

                    <?php _e('Не отображать', 'mbl_admin') ?></label>
            </div>
            <div class="wpm-row">
                <?php if (empty($main_options['main']['ask_email'])) $main_options['main']['ask_email'] = get_option('admin_email'); ?>
                <label><?php _e('Емейл для получения вопросов от пользователя.', 'mbl_admin') ?><br>
                    <input type="text" name="main_options[main][ask_email]"
                           class="large-text"
                           value="<?php echo $main_options['main']['ask_email']; ?>">
                </label>
            </div>

            <div class="wpm-row">
                <label><input type="checkbox"
                              name="main_options[main][hide_ask_for_not_registered]"
                        <?php echo (array_key_exists('hide_ask_for_not_registered', $main_options['main']) && $main_options['main']['hide_ask_for_not_registered'] == 'on') ? ' checked' : ''; ?>>
                    <?php _e('Не отображать "Задать вопрос" для незарегистрированных пользователей', 'mbl_admin') ?>
                    </label>
            </div>

           <?php wpm_render_partial('settings-save-button', 'common'); ?>
        </div>
    </div>
</div>
