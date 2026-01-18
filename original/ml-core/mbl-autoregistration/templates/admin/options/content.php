<?php /** @var $main_options [] */ ?>
<?php /** @var $design_options [] */ ?>
<div id="tab-mblr-options" class="tab ma-color-content">
    <div class="wpm-tab-content mblr_autoreg">
        <div class="wpm-row">
            <label>
                <?php _e('Переменная для подстановки данных', 'mbl_admin') ?>
                <br>
                <input type="text"
                       name="mblr_auto_registration[variable]"
                       class="large-text"
                       style="max-width: 500px;"
                       placeholder="<?php _e('Введите переменную', 'mbl_admin') ?>"
                       value="{email}"
                >
            </label>
        </div>
        <div class="wpm-row">
            <div class="flex-row flex-wrap">
                <div class="pr-30">
                    <?php _e('Выберите уровень доступа', 'mbl_admin') ?>:<br>
                    <select name="mblr_auto_registration[level]">
                        <?php foreach ($levels AS $level) : ?>
                            <option
                                    value="<?php echo $level->term_id; ?>"
                            ><?php echo $level->name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="pr-15 form-field">
                    <?php
                    wpm_render_partial('term-keys-period',
                        'admin',
                        [
                            'durationName'    => 'mblr_auto_registration[duration]',
                            'unitsName'       => 'mblr_auto_registration[units]',
                            'isUnlimitedName' => 'mblr_auto_registration[is_unlimited]',
                        ])
                    ?>
                </div>
            </div>
        </div>
        <div class="wpm-row">
            <div class="wpm-row mb-0">
                <textarea id="mblr-autoreg-link" class="wpm-wide" rows="5"></textarea>
            </div>
        </div>
        <?php wpm_render_partial('fields/checkbox', 'admin', ['label' => __('Политика конфиденциальности', 'mbl_admin'), 'name' => 'mblr_auto_registration[user_agreement]', 'value' => 'on']) ?>
        <?php do_action('autoregistration_options'); ?>
        <div class="mblr_autoreg_buttons">
            <button type="button" class="button button-primary" data-mblr-get-ar-code><?php _e('Получить код', 'mbl_admin'); ?></button>
            <button type="button" class="button button-primary" data-clipboard-target="#mblr-autoreg-link"><?php _e('Скопировать', 'mbl_admin'); ?></button>
        </div>
    </div>
</div>