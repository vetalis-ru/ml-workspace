<div id="tab-mbl-protection" class="tab mwp-color-content mask-tab">
	<?php wpm_render_partial('masks/mask-mwp', 'admin'); ?>
    <div class="wpm-tab-content">
		<?php wpm_render_partial('fields/checkbox', 'admin', array('label' => __('Применить ко всем видео', 'mbl_admin'), 'name' => 'main_options[mbl_wp][display_all]', 'value' => wpm_get_option('mbl_wp.display_all'))) ?>

        <h3><?php _e('Блок данных "Случайный"', 'mbl_admin'); ?></h3>
        <div class="row">
            <div class="col col-33">
				<?php wpm_render_partial('fields/checkbox', 'admin', array('label' => __('Отображать', 'mbl_admin'), 'name' => 'main_options[mbl_wp][block_0][display]', 'value' => wpm_get_option('mbl_wp.block_0.display'))) ?>
                <br>
                <div class="wpm-control-row">
                    <label><?php _e('Прозрачность', 'mbl_admin'); ?>
                        <input type="number"
                               name="main_options[mbl_wp][block_0][opacity]"
                               min="0"
                               max="100"
                               class="min-input"
                               value="<?php echo wpm_get_option('mbl_wp.block_0.opacity'); ?>">
                        %
                    </label>
                </div>
				<?php wpm_render_partial(
                        'options/color-row',
                    'admin',
                    array(
                        'label' => __('Цвет текста:', 'mbl_admin'),
                        'key' => 'mbl_wp.block_0.color',
                        'main' => true,
                        'default' => '009f10'
                    )
                ) ?>
            </div>

            <div class="col col-33">
                <div class="dinamic-settings">
					<?php _e('Интервал отображения, каждых', 'mbl_admin'); ?>:
                    <div>
                        <input type="number"
                               name="main_options[mbl_wp][block_0][interval]"
                               min="1"
                               max="100"
                               value="<?php echo wpm_get_option('mbl_wp.block_0.interval'); ?>">
						<?php _e('сек', 'mbl_admin'); ?>.
                    </div>
					<?php _e('Длительность показа', 'mbl_admin'); ?>:
                    <div>
                        <input type="number"
                               name="main_options[mbl_wp][block_0][duration]"
                               min="1"
                               max="100"
                               value="<?php echo wpm_get_option('mbl_wp.block_0.duration'); ?>">
						<?php _e('сек', 'mbl_admin'); ?>.
                    </div>
					<?php _e('Плавность появления', 'mbl_admin'); ?>:
                    <div>
                        <input type="number"
                               name="main_options[mbl_wp][block_0][animation]"
                               min="1"
                               max="100"
                               value="<?php echo wpm_get_option('mbl_wp.block_0.animation'); ?>">
						<?php _e('сек', 'mbl_admin'); ?>.
                    </div>
                </div>
            </div>

            <div class="col col-33">
                <span class="bold">
					<?php _e('Тип данных', 'mbl_admin'); ?>:
                </span><br><br>
                <label class="type-label">
                    <input type="radio" name="main_options[mbl_wp][block_0][data_type]" value="fio" <?php echo wpm_get_option('mbl_wp.block_0.data_type') == 'fio'? 'checked' : ''; ?>>
					<?php _e('ФИО', 'mbl_admin'); ?>
                </label>
                <label class="type-label">
                    <input type="radio" name="main_options[mbl_wp][block_0][data_type]" value="mail" <?php echo wpm_get_option('mbl_wp.block_0.data_type') == 'mail'? 'checked' : ''; ?>>
					<?php _e('E-mail', 'mbl_admin'); ?>
                </label>
                <label class="type-label">
                    <input type="radio" name="main_options[mbl_wp][block_0][data_type]" value="pin" <?php echo wpm_get_option('mbl_wp.block_0.data_type') == 'pin'? 'checked' : ''; ?>>
					<?php _e('Пин-код', 'mbl_admin'); ?>
                </label>
                <label class="type-label">
                    <input type="radio" name="main_options[mbl_wp][block_0][data_type]" value="login" <?php echo wpm_get_option('mbl_wp.block_0.data_type') == 'login'? 'checked' : ''; ?>>
					<?php _e('Login', 'mbl_admin'); ?>
                </label>
            </div>

        </div>

        <h3><?php _e('Блок #1 (Вверху слева)', 'mbl_admin'); ?></h3>
        <div class="row">
            <div class="col col-33">
				<?php wpm_render_partial('fields/checkbox', 'admin', array('label' => __('Отображать', 'mbl_admin'), 'name' => 'main_options[mbl_wp][block_1][display]', 'value' => wpm_get_option('mbl_wp.block_1.display'))) ?>
                <br>
                <div class="wpm-control-row">
                    <label><?php _e('Прозрачность', 'mbl_admin'); ?>
                        <input type="number"
                               name="main_options[mbl_wp][block_1][opacity]"
                               min="0"
                               max="100"
                               class="min-input"
                               value="<?php echo wpm_get_option('mbl_wp.block_1.opacity'); ?>">
                        %
                    </label>
                </div>
                <?php wpm_render_partial('options/color-row', 'admin',
                    array(
                        'label' => __('Цвет текста:', 'mbl_admin'),
                        'key' => 'mbl_wp.block_1.color',
                        'main' => true,
                        'default' => '009f10'
                    )
                ) ?>
            </div>

            <div class="col col-33">
                <label class="bold">
                    <input type="radio" name="main_options[mbl_wp][block_1][content]" value="dinamic" <?php echo wpm_get_option('mbl_wp.block_1.content') == 'dinamic'? 'checked' : ''; ?>>
					<?php _e('Динамический контент', 'mbl_admin'); ?>
                </label><br>
                <div class="dinamic-settings">
					<?php _e('Интервал отображения, каждых', 'mbl_admin'); ?>:
                    <div>
                        <input type="number"
                               name="main_options[mbl_wp][block_1][interval]"
                               min="1"
                               max="100"
                               value="<?php echo wpm_get_option('mbl_wp.block_1.interval'); ?>">
						<?php _e('сек', 'mbl_admin'); ?>.
                    </div>
					<?php _e('Длительность показа', 'mbl_admin'); ?>:
                    <div>
                        <input type="number"
                               name="main_options[mbl_wp][block_1][duration]"
                               min="1"
                               max="100"
                               value="<?php echo wpm_get_option('mbl_wp.block_1.duration'); ?>">
						<?php _e('сек', 'mbl_admin'); ?>.
                    </div>
					<?php _e('Плавность появления', 'mbl_admin'); ?>:
                    <div>
                        <input type="number"
                               name="main_options[mbl_wp][block_1][animation]"
                               min="1"
                               max="100"
                               value="<?php echo wpm_get_option('mbl_wp.block_1.animation'); ?>">
						<?php _e('сек', 'mbl_admin'); ?>.
                    </div>
                </div>
                <label class="bold">
                    <input type="radio" name="main_options[mbl_wp][block_1][content]" value="static" <?php echo wpm_get_option('mbl_wp.block_1.content') == 'static'? 'checked' : ''; ?>>
					<?php _e('Статический контент', 'mbl_admin'); ?>
                </label>
            </div>

            <div class="col col-33">
                <span class="bold">
					<?php _e('Тип данных', 'mbl_admin'); ?>:
                </span><br><br>
                <label class="type-label">
                    <input type="radio" name="main_options[mbl_wp][block_1][data_type]" value="fio" <?php echo wpm_get_option('mbl_wp.block_1.data_type') == 'fio'? 'checked' : ''; ?>>
					<?php _e('ФИО', 'mbl_admin'); ?>
                </label>
                <label class="type-label">
                    <input type="radio" name="main_options[mbl_wp][block_1][data_type]" value="mail" <?php echo wpm_get_option('mbl_wp.block_1.data_type') == 'mail'? 'checked' : ''; ?>>
					<?php _e('E-mail', 'mbl_admin'); ?>
                </label>
                <label class="type-label">
                    <input type="radio" name="main_options[mbl_wp][block_1][data_type]" value="pin" <?php echo wpm_get_option('mbl_wp.block_1.data_type') == 'pin'? 'checked' : ''; ?>>
					<?php _e('Пин-код', 'mbl_admin'); ?>
                </label>
                <label class="type-label">
                    <input type="radio" name="main_options[mbl_wp][block_1][data_type]" value="login" <?php echo wpm_get_option('mbl_wp.block_1.data_type') == 'login'? 'checked' : ''; ?>>
					<?php _e('Login', 'mbl_admin'); ?>
                </label>
            </div>

        </div>

        <h3><?php _e('Блок #2 (По центру)', 'mbl_admin'); ?></h3>
        <div class="row">
            <div class="col col-33">
				<?php wpm_render_partial('fields/checkbox', 'admin', array('label' => __('Отображать', 'mbl_admin'), 'name' => 'main_options[mbl_wp][block_2][display]', 'value' => wpm_get_option('mbl_wp.block_2.display'))) ?>
                <br>
                <div class="wpm-control-row">
                    <label><?php _e('Прозрачность', 'mbl_admin'); ?>
                        <input type="number"
                               name="main_options[mbl_wp][block_2][opacity]"
                               min="0"
                               max="100"
                               step="1"
                               class="min-input"
                               value="<?php echo wpm_get_option('mbl_wp.block_2.opacity'); ?>">
                        %
                    </label>
                </div>
                <?php wpm_render_partial(
                    'options/color-row',
                    'admin',
                    array(
                        'label' => __('Цвет текста:', 'mbl_admin'),
                        'key' => 'mbl_wp.block_2.color',
                        'main' => true,
                        'default' => '009f10'
                    )
                ) ?>
            </div>

            <div class="col col-33">
                <label class="bold">
                    <input type="radio" name="main_options[mbl_wp][block_2][content]" value="dinamic" <?php echo wpm_get_option('mbl_wp.block_2.content') == 'dinamic'? 'checked' : ''; ?>>
					<?php _e('Динамический контент', 'mbl_admin'); ?>
                </label><br>
                <div class="dinamic-settings">
					<?php _e('Интервал отображения, каждых', 'mbl_admin'); ?>:
                    <div>
                        <input type="number"
                               name="main_options[mbl_wp][block_2][interval]"
                               min="1"
                               max="100"
                               step="1"
                               value="<?php echo wpm_get_option('mbl_wp.block_2.interval'); ?>">
						<?php _e('сек', 'mbl_admin'); ?>.
                    </div>
					<?php _e('Длительность показа', 'mbl_admin'); ?>:
                    <div>
                        <input type="number"
                               name="main_options[mbl_wp][block_2][duration]"
                               min="1"
                               max="100"
                               step="1"
                               value="<?php echo wpm_get_option('mbl_wp.block_2.duration'); ?>">
						<?php _e('сек', 'mbl_admin'); ?>.
                    </div>
					<?php _e('Плавность появления', 'mbl_admin'); ?>:
                    <div>
                        <input type="number"
                               name="main_options[mbl_wp][block_2][animation]"
                               min="1"
                               max="100"
                               step="1"
                               value="<?php echo wpm_get_option('mbl_wp.block_2.animation'); ?>">
						<?php _e('сек', 'mbl_admin'); ?>.
                    </div>
                </div>
                <label class="bold">
                    <input type="radio" name="main_options[mbl_wp][block_2][content]" value="static" <?php echo wpm_get_option('mbl_wp.block_2.content') == 'static'? 'checked' : ''; ?>>
					<?php _e('Статический контент', 'mbl_admin'); ?>
                </label>
            </div>

            <div class="col col-33">
                <span class="bold">
					<?php _e('Тип данных', 'mbl_admin'); ?>:
                </span><br><br>
                <label class="type-label">
                    <input type="radio" name="main_options[mbl_wp][block_2][data_type]" value="fio" <?php echo wpm_get_option('mbl_wp.block_2.data_type') == 'fio'? 'checked' : ''; ?>>
					<?php _e('ФИО', 'mbl_admin'); ?>
                </label>
                <label class="type-label">
                    <input type="radio" name="main_options[mbl_wp][block_2][data_type]" value="mail" <?php echo wpm_get_option('mbl_wp.block_2.data_type') == 'mail'? 'checked' : ''; ?>>
					<?php _e('E-mail', 'mbl_admin'); ?>
                </label>
                <label class="type-label">
                    <input type="radio" name="main_options[mbl_wp][block_2][data_type]" value="pin" <?php echo wpm_get_option('mbl_wp.block_2.data_type') == 'pin'? 'checked' : ''; ?>>
					<?php _e('Пин-код', 'mbl_admin'); ?>
                </label>
                <label class="type-label">
                    <input type="radio" name="main_options[mbl_wp][block_2][data_type]" value="login" <?php echo wpm_get_option('mbl_wp.block_2.data_type') == 'login'? 'checked' : ''; ?>>
					<?php _e('Login', 'mbl_admin'); ?>
                </label>
            </div>

        </div>

        <h3><?php _e('Блок #3 (Внизу справа)', 'mbl_admin'); ?></h3>
        <div class="row">
            <div class="col col-33">
				<?php wpm_render_partial('fields/checkbox', 'admin', array('label' => __('Отображать', 'mbl_admin'), 'name' => 'main_options[mbl_wp][block_3][display]', 'value' => wpm_get_option('mbl_wp.block_3.display'))) ?>
                <br>
                <div class="wpm-control-row">
                    <label><?php _e('Прозрачность', 'mbl_admin'); ?>
                        <input type="number"
                               name="main_options[mbl_wp][block_3][opacity]"
                               min="0"
                               max="100"
                               step="1"
                               class="min-input"
                               value="<?php echo wpm_get_option('mbl_wp.block_3.opacity'); ?>">
                        %
                    </label>
                </div>
                <?php wpm_render_partial(
                    'options/color-row',
                    'admin',
                    array(
                        'label' => __('Цвет текста:', 'mbl_admin'),
                        'key' => 'mbl_wp.block_3.color',
                        'main' => true,
                        'default' => '009f10'
                    )
                ) ?>
            </div>

            <div class="col col-33">
                <label class="bold">
                    <input type="radio" name="main_options[mbl_wp][block_3][content]" value="dinamic" <?php echo wpm_get_option('mbl_wp.block_3.content') == 'dinamic'? 'checked' : ''; ?>>
					<?php _e('Динамический контент', 'mbl_admin'); ?>
                </label><br>
                <div class="dinamic-settings">
					<?php _e('Интервал отображения, каждых', 'mbl_admin'); ?>:
                    <div>
                        <input type="number"
                               name="main_options[mbl_wp][block_3][interval]"
                               min="1"
                               max="100"
                               step="1"
                               value="<?php echo wpm_get_option('mbl_wp.block_3.interval'); ?>">
						<?php _e('сек', 'mbl_admin'); ?>.
                    </div>
					<?php _e('Длительность показа', 'mbl_admin'); ?>:
                    <div>
                        <input type="number"
                               name="main_options[mbl_wp][block_3][duration]"
                               min="1"
                               max="100"
                               step="1"
                               value="<?php echo wpm_get_option('mbl_wp.block_3.duration'); ?>">
						<?php _e('сек', 'mbl_admin'); ?>.
                    </div>
					<?php _e('Плавность появления', 'mbl_admin'); ?>:
                    <div>
                        <input type="number"
                               name="main_options[mbl_wp][block_3][animation]"
                               min="1"
                               max="100"
                               step="1"
                               value="<?php echo wpm_get_option('mbl_wp.block_3.animation'); ?>">
						<?php _e('сек', 'mbl_admin'); ?>.
                    </div>
                </div>
                <label class="bold">
                    <input type="radio" name="main_options[mbl_wp][block_3][content]" value="static" <?php echo wpm_get_option('mbl_wp.block_3.content') == 'static'? 'checked' : ''; ?>>
					<?php _e('Статический контент', 'mbl_admin'); ?>
                </label>
            </div>

            <div class="col col-33">
                <span class="bold">
					<?php _e('Тип данных', 'mbl_admin'); ?>:
                </span><br><br>
                <label class="type-label">
                    <input type="radio" name="main_options[mbl_wp][block_3][data_type]" value="fio" <?php echo wpm_get_option('mbl_wp.block_3.data_type') == 'fio'? 'checked' : ''; ?>>
					<?php _e('ФИО', 'mbl_admin'); ?>
                </label>
                <label class="type-label">
                    <input type="radio" name="main_options[mbl_wp][block_3][data_type]" value="mail" <?php echo wpm_get_option('mbl_wp.block_3.data_type') == 'mail'? 'checked' : ''; ?>>
					<?php _e('E-mail', 'mbl_admin'); ?>
                </label>
                <label class="type-label">
                    <input type="radio" name="main_options[mbl_wp][block_3][data_type]" value="pin" <?php echo wpm_get_option('mbl_wp.block_3.data_type') == 'pin'? 'checked' : ''; ?>>
					<?php _e('Пин-код', 'mbl_admin'); ?>
                </label>
                <label class="type-label">
                    <input type="radio" name="main_options[mbl_wp][block_3][data_type]" value="login" <?php echo wpm_get_option('mbl_wp.block_3.data_type') == 'login'? 'checked' : ''; ?>>
					<?php _e('Login', 'mbl_admin'); ?>
                </label>
            </div>

        </div>

        <h3><?php _e('Предупреждение об ответственности', 'mbl_admin'); ?></h3>
		<?php wpm_render_partial('fields/checkbox', 'admin', array('label' => __('Отображать', 'mbl_admin'), 'name' => 'main_options[mbl_wp][block_4][display]', 'value' => wpm_get_option('mbl_wp.block_4.display'))) ?>
        <div class="row">
            <div class="col col-33">
                <?php wpm_render_partial(
                    'options/color-row',
                    'admin',
                    array(
                        'label' => __('Цвет текста:', 'mbl_admin'),
                        'key' => 'mbl_wp.block_4.color',
                        'main' => true,
                        'default' => '009f10'
                    )
                ) ?>
            </div>
            <div class="col col-33">
                <?php wpm_render_partial(
                    'options/color-row',
                    'admin',
                    array(
                        'label' => __('Цвет фона:', 'mbl_admin'),
                        'key' => 'mbl_wp.block_4.background',
                        'main' => true,
                        'default' => 'ffffff'
                    )
                ) ?>
            </div>
            <div class="col col-33">
                <?php wpm_render_partial(
                    'options/color-row',
                    'admin',
                    array(
                        'label' => __('Цвет рамки:', 'mbl_admin'),
                        'key' => 'mbl_wp.block_4.border',
                        'main' => true,
                        'default' => 'ffffff'
                    )
                ) ?>
            </div>
        </div>

        <span class="bold">
            <?php _e('Содержимое', 'mbl_admin'); ?>:
        </span>
        <textarea name="main_options[mbl_wp][block_4][text]" id=""><?php echo wpm_get_option('mbl_wp.block_4.text'); ?></textarea>

        <h3><?php _e('Сообщение для Android UC Browser', 'mbl_admin'); ?></h3>
        <textarea name="main_options[mbl_wp][uc_text]" id=""><?php echo wpm_get_option('mbl_wp.uc_text'); ?></textarea>

		<?php wpm_render_partial('settings-save-button', 'common'); ?>
    </div>
</div>

<style>
    #tab-mbl-protection h3:first-child {
        margin-top: 0;
    }
    #tab-mbl-protection .row {
        display: flex;
        justify-content: space-between;
    }
    #tab-mbl-protection .row .wpm-row {
        margin: 0;
        margin-right: 25px;
    }
    #tab-mbl-protection .row .wpm-row:last-child {
        margin: 0;
    }
    #tab-mbl-protection .row .col:not(:last-child) {
        margin-right: 15px;
    }
    #tab-mbl-protection .row .col input.color {
        width: 100%;
    }
    #tab-mbl-protection .row .col-33 {
        min-width: calc(33.3% - 25px);
    }
    #tab-mbl-protection .row .col-33 .wpm-control-row {
        margin-top: 0;
    }
    #tab-mbl-protection .d-flex {
        display: flex;
    }
    #tab-mbl-protection .column {
        flex-direction: column;
    }
    #tab-mbl-protection .justify-end {
        justify-content: flex-end;
    }
    #tab-mbl-protection .mb-15 {
        margin-bottom: 15px;
    }
    #tab-mbl-protection .mt-15 {
        margin-top: 15px;
    }
    #tab-mbl-protection .bold {
        font-weight: bold;
    }
    #tab-mbl-protection .min-input {
        max-width: 70px;
    }
    #tab-mbl-protection label.bold {
        margin-bottom: 10px;
        display: inline-block;
    }
    #tab-mbl-protection .dinamic-settings input {
        margin-bottom: 10px;
    }
    #tab-mbl-protection textarea{
        width: 100%;
        height: 80px;
        padding: 10px 15px;
        margin-top: 5px;
    }
    #tab-mbl-protection .type-label {
        margin-bottom: 10px;
        display: block;
    }
    #tab-mbl-protection h3{
        margin-top: 2em;
    }
</style>
