<div id="tab-mblp" class="tab mpp-color-content mask-tab">
    <?php wpm_render_partial('masks/mask-mpp', 'admin'); ?>
    <div class="wpm-tab-content">
        <div class="wpm-inner-tabs" tab-id="h-tabs-7">
            <ul class="wpm-inner-tabs-nav">
                <li><a href="#mbl_inner_tab_mblp_1"><?php _e('Поля формы оплаты', 'mbl_admin') ?></a></li>
                <li><a href="#mbl_inner_tab_mblp_2"><?php _e('Письма уведомлений', 'mbl_admin') ?></a></li>
                <li><a href="#mbl_inner_tab_mblp_3"><?php _e('Активация', 'mbl_admin') ?></a></li>
                <li><a href="#mbl_inner_tab_mblp_4"><?php _e('Дизайн корзины', 'mbl_admin') ?></a></li>
                <li><a href="#mbl_inner_tab_mblp_5"><?php _e('Тексты', 'mbl_admin') ?></a></li>
            </ul>
            <div id="mbl_inner_tab_mblp_1" class="wpm-tab-content">
                <div id="tabs-level-3-1-mblp"
                     tab-id="headers-tabs-fields"
                     class="tabs-level-3 headers-design-tabs wpm-inner-tabs-nav">
                    <ul>
                        <li class="ui-state-default ui-state-disabled" header-id="new_clients">
                            <a href='#header-tab-mblp-1-new-clients'><?php _e('Новые клиенты', 'mbl_admin') ?></a>
                        </li>
                        <li class="ui-state-default" header-id="existing_clients">
                            <a href='#header-tab-mblp-1-existing-clients'><?php _e('Уже существующие', 'mbl_admin') ?></a>
                        </li>
                    </ul>
    
                    <div id="header-tab-mblp-1-new-clients">
                        <div class="wpm-control-row wpm-inner-tab-content">
                            <div class="wpm-row">
                                <label>
                                    <input type="checkbox"
                                           name="main_options[mblp][new_clients][surname]"
                                        <?php echo wpm_option_is('mblp.new_clients.surname', 'on') ? ' checked' : ''; ?> />
                                    <?php _e('Фамилия', 'mbl_admin'); ?>
                                </label>
                            </div>
                            <div class="wpm-row">
                                <label>
                                    <input type="checkbox"
                                           name="main_options[mblp][new_clients][name]"
                                        <?php echo wpm_option_is('mblp.new_clients.name', 'on') ? ' checked' : ''; ?> />
                                    <?php _e('Имя', 'mbl_admin'); ?>
                                </label>
                            </div>
                            <div class="wpm-row">
                                <label>
                                    <input type="checkbox"
                                           name="main_options[mblp][new_clients][patronymic]"
                                        <?php echo wpm_option_is('mblp.new_clients.patronymic', 'on') ? ' checked' : ''; ?> />
                                    <?php _e('Отчество', 'mbl_admin'); ?>
                                </label>
                            </div>
                            <div class="wpm-row wpm-row-disabled"
                                 title="<?php _e('Это поле нельзя убрать из формы', 'mbl_admin') ?>">
                                <label>
                                    <input type="checkbox" disabled checked/> <?php _e('Email', 'mbl'); ?>
                                </label>
                            </div>
                            <div class="wpm-row">
                                <label>
                                    <input type="checkbox"
                                           name="main_options[mblp][new_clients][phone]"
                                        <?php echo wpm_option_is('mblp.new_clients.phone', 'on') ? ' checked' : ''; ?> />
                                    <?php _e('Телефон', 'mbl_admin'); ?>
                                </label>
                            </div>
                            <div class="wpm-row wpm-row-disabled"
                                 title="<?php _e('Это поле нельзя убрать из формы', 'mbl_admin') ?>">
                                <label>
                                    <input type="checkbox" disabled checked/> <?php _e('Желаемый логин', 'mbl_admin'); ?>
                                </label>
                            </div>
                            <div class="wpm-row wpm-row-disabled"
                                 title="<?php _e('Это поле нельзя убрать из формы', 'mbl_admin') ?>">
                                <label>
                                    <input type="checkbox" disabled checked/> <?php _e('Желаемый пароль', 'mbl_admin'); ?>
                                </label>
                            </div>
                            <div class="wpm-row">
                                <label>
                                    <input type="checkbox"
                                           name="main_options[mblp][new_clients][comment]"
                                        <?php echo wpm_option_is('mblp.new_clients.comment', 'on') ? ' checked' : ''; ?> />
                                    <?php _e('Комментарий к заказу', 'mbl_admin'); ?>
                                </label>
                            </div>
                        </div>
                    </div>
                    <?php wpm_render_partial('settings-save-button', 'common'); ?>
                </div>
            </div>
        </div>
    </div>
</div>