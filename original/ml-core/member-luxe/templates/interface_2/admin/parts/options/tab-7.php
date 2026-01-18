<div class="wpm-tab-content">
    <div class="wpm-inner-tabs" tab-id="h-tabs-7">
        <ul class="wpm-inner-tabs-nav">
            <li><a href="#mbl_inner_tab_7_1"><?php _e('Регистрация пользователей', 'mbl_admin') ?></a></li>
            <li><a href="#mbl_inner_tab_7_2"><?php _e('Добавление ключей', 'mbl_admin') ?></a></li>
        </ul>
        <div id="mbl_inner_tab_7_1" class="wpm-tab-content">
            <span><?php _e('Массовая регистрация пользователей и предоставление им доступа к материалам.', 'mbl_admin') ?></span>

            <div id="bulk-import-users" class="wpm-ajax-box-wrap">
                <div class="wpm-ajax-overlay">
                    <p><?php _e('Выполнение данной операции может занять до 10 минут.', 'mbl_admin') ?> <br> <?php _e('Пожалуйста не закрывайте страницу!', 'mbl_admin') ?></p>

                    <div class="wpm-spinner">
                        <div class="rect1"></div>
                        <div class="rect2"></div>
                        <div class="rect3"></div>
                        <div class="rect4"></div>
                        <div class="rect5"></div>
                    </div>
                </div>
                <div class="wpm-import-new wpm-ajax-tab">
                    <div class="wpm-row">
                        <label>
                            <?php _e('Вставьте список емейлов (через запятую или каждый емейл в отдельной строке)', 'mbl_admin') ?><br>
                            <textarea name="import-users" id="users-emails-str"
                                      class="wpm-wide"></textarea>
                        </label>
                    </div>
                    <div class="wpm-tab-footer">
                        <button type="button"
                                class="button button-primary wpm-import-users"
                                id="import-add"><?php _e('Импортировать', 'mbl_admin') ?></button>
                        <span class="buttom-preloader"></span>
                    </div>
                </div>
                <div class="wpm-no-emails wpm-ajax-tab">
                    <div class="wpm-row">
                        <div class="wpm-col">
                            <p><?php _e('Не найдено корректных емейлов.', 'mbl_admin') ?></p>
                        </div>
                    </div>
                    <div class="wpm-tab-footer">
                        <button type="button"
                                class="button button-primary import-new-emails"><?php _e('Ввести новые емейлы', 'mbl_admin') ?></button>
                    </div>
                </div>
                <div class="wpm-import-confirm wpm-ajax-tab">
                    <div class="wpm-row">
                        <div class="wpm-col">
                            <p class="wpm-emails-count"></p>
                            <ul id="import-emails">
                            </ul>
                        </div>
                        <div class="wpm-col left-border">
                            <p><?php _e('Выберите уровень доступа', 'mbl_admin') ?></p>
                            <select id="users-level" class="users-level"><?php echo wpm_get_levels_select(); ?></select>

                            <?php
                                wpm_render_partial('term-keys-period',
                                   'admin',
                                    [
                                        'durationId'    => 'users-level-duration',
                                        'unitsName'     => 'units',
                                        'unitsId'       => 'users-units',
                                        'isUnlimitedId' => 'mbl-users-is-unlimited',
                                    ])
                            ?>
                        </div>
                    </div>
                    <div class="wpm-tab-footer">
                        <button type="button"
                                class="button import-new-emails"><?php _e('Ввести новые емейлы', 'mbl_admin') ?></button>
                        <button type="button"
                                class="button button-primary wpm-import-users"
                                id="import-send"><?php _e('Создать пользователей', 'mbl_admin') ?></button>
                    </div>
                </div>
                <div class="wpm-import-result wpm-ajax-tab">
                    <div class="wpm-row">
                        <div class="wpm-ajax-import-result"></div>
                    </div>
                    <div class="wpm-tab-footer">
                        <button type="button"
                                class="button button-primary import-new-emails"><?php _e('Ввести новые емейлы', 'mbl_admin') ?></button>
                    </div>
                </div>
            </div>
        </div>
        <div id="mbl_inner_tab_7_2" class="wpm-tab-content">
            <span><?php _e('Массовое добавление ключей пользователям.', 'mbl_admin') ?></span>

            <div id="bulk-addkeys-users" class="wpm-ajax-box-wrap">
                <div class="wpm-ajax-overlay">
                    <p><?php _e('Выполнение данной операции может занять до 10 минут.', 'mbl_admin') ?> <br> <?php _e('Пожалуйста не закрывайте страницу!', 'mbl_admin') ?></p>

                    <div class="wpm-spinner">
                        <div class="rect1"></div>
                        <div class="rect2"></div>
                        <div class="rect3"></div>
                        <div class="rect4"></div>
                        <div class="rect5"></div>
                    </div>
                </div>
                <div class="wpm-import-new wpm-ajax-tab">
                    <div class="wpm-row">
                        <label>
                            <?php _e('Вставьте список емейлов (через запятую или каждый емейл в отдельной строке)', 'mbl_admin') ?>
                            <br>
                            <textarea name="import-users" id="users-emails-str"
                                      class="wpm-wide"></textarea>
                        </label>
                    </div>
                    <div class="wpm-tab-footer">
                        <button type="button"
                                class="button button-primary wpm-import-users"
                                id="import-add"><?php _e('Проверить', 'mbl_admin') ?></button>
                        <span class="buttom-preloader"></span>
                    </div>
                </div>
                <div class="wpm-no-emails wpm-ajax-tab">
                    <div class="wpm-row">
                        <div class="wpm-col">
                            <div class="message">
                                <p><?php _e('Не найдено корректных емейлов.', 'mbl_admin') ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="wpm-tab-footer">
                        <button type="button"
                                class="button button-primary import-new-emails"><?php _e('Ввести новые емейлы', 'mbl_admin') ?></button>
                    </div>
                </div>
                <div class="wpm-import-confirm wpm-ajax-tab">
                    <div class="wpm-row">
                        <div class="wpm-col">
                            <div class="wpm-users-check-result"></div>
                        </div>
                        <div class="wpm-col left-border">
                            <p><?php _e('Выберите уровень доступа', 'mbl_admin') ?></p>
                            <select
                                class="users-level"><?php echo wpm_get_levels_select(); ?></select>
                            
                            <?php
                                wpm_render_partial('term-keys-period',
                                   'admin',
                                    [
                                        'durationId'    => 'users-level-duration',
                                        'unitsName'     => 'units',
                                        'unitsId'       => 'users-units',
                                        'isUnlimitedId' => 'mbl-users-is-unlimited',
                                    ])
                            ?>
                        </div>
                    </div>
                    <div class="wpm-tab-footer">
                        <button type="button"
                                class="button import-new-emails"><?php _e('Ввести новые емейлы', 'mbl_admin') ?></button>
                        <button type="button"
                                class="button button-primary wpm-import-users"
                                id="import-send"><?php _e('Добавить коды доступа', 'mbl_admin') ?></button>
                    </div>
                </div>
                <div class="wpm-import-result wpm-ajax-tab">
                    <div class="wpm-row">
                        <div class="wpm-ajax-import-result"></div>
                    </div>
                    <div class="wpm-tab-footer">
                        <button type="button"
                                class="button button-primary import-new-emails"><?php _e('Ввести новые емейлы', 'mbl_admin') ?></button>
                    </div>
                </div>
            </div>
            <style type="text/css">
                #users-emails-str {
                    max-width: 500px;
                    margin: 5px 0;
                }

                #users-emails-str.wpm-wide {
                    height: 200px;

                }

                .wpm-ajax-tab {
                    display: none;
                }

                .wpm-ajax-tab.wpm-import-new {
                    display: block;
                }

                .wpm-row .wpm-col {
                    min-width: 200px;
                    min-height: 1px;
                    display: inline-block;
                    vertical-align: top;
                    padding-right: 20px;
                }

                .wpm-col.left-border {
                    border-left: 1px solid #dddddd;
                    padding-left: 30px;
                    padding-bottom: 30px;
                }

                .wpm-row .wpm-col:last-child {
                    padding-right: 0;
                }

                .wpm-ajax-import-result .success span {
                    color: #1abc9c;
                }

                .wpm-ajax-import-result .fail span {
                    color: #d61e32;
                }

                .wpm-ajax-box-wrap {
                    position: relative;
                    padding: 0 0 5px 2px;
                }

                .wpm-ajax-box-wrap #users-units {
                    margin-top: -2px;
                }

                .wpm-ajax-overlay {
                    display: none;
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background-color: rgba(255, 255, 255, 1);
                    z-index: 100;
                }

                .wpm-ajax-overlay p {
                    padding: 100px 0 0;
                    max-width: 400px;
                    text-align: center;
                }

                .wpm-spinner {
                    position: absolute;
                    top: 70px;
                    left: 175px;
                    width: 50px;
                    height: 30px;
                    text-align: center;
                    font-size: 10px;
                }

                .wpm-spinner > div {
                    background-color: #1abc9c;
                    height: 100%;
                    width: 6px;
                    display: inline-block;

                    -webkit-animation: stretchdelay 1s infinite ease-in-out;
                    animation: stretchdelay 1s infinite ease-in-out;
                }

                .wpm-spinner .rect2 {
                    -webkit-animation-delay: -0.9s;
                    animation-delay: -0.9s;
                }

                .wpm-spinner .rect3 {
                    -webkit-animation-delay: -0.8s;
                    animation-delay: -0.8s;
                }

                .wpm-spinner .rect4 {
                    -webkit-animation-delay: -0.7s;
                    animation-delay: -0.7s;
                }

                .wpm-spinner .rect5 {
                    -webkit-animation-delay: -0.6s;
                    animation-delay: -0.6s;
                }

                @-webkit-keyframes stretchdelay {
                    0%, 40%, 100% {
                        -webkit-transform: scaleY(0.4)
                    }
                    20% {
                        -webkit-transform: scaleY(1.0)
                    }
                }

                @keyframes stretchdelay {
                    0%, 40%, 100% {
                        transform: scaleY(0.4);
                        -webkit-transform: scaleY(0.4);
                    }
                    20% {
                        transform: scaleY(1.0);
                        -webkit-transform: scaleY(1.0);
                    }
                }

            </style>
        </div>
    </div>
</div>