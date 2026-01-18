<div id="tab-mblr-options" class="tab ma-color-content mask-tab">
    <?php wpm_render_partial('masks/mask-ma', 'admin'); ?>
    <div class="wpm-tab-content mblr_autoreg">
        <div class="wpm-row">
            <label>
                <?php _e('Переменная для подстановки данных', 'mbl_admin') ?>
                <br>
                <input type="text"
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
                    <select name="">
                        <?php foreach ($levels AS $level) : ?>
                            <option
                                    value="<?php echo $level->term_id; ?>"
                            ><?php echo $level->name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="pr-15 form-field">
                    <?php _e('Время действия', 'mbl_admin'); ?>: <br>

                    <span class="wrap">
                       <input
                               type="number"
                               size="2"
                               value="12"
                               maxlength="2"
                               style="width: 55px; position: relative; top: 1px; margin-bottom: 5px">

                       <select name="mblr_auto_registration[units]" style="margin-left: 5px; margin-top: 0">
                            <option value="months" selected><?php _e('месяцев', 'mbl_admin'); ?></option>
                       </select>
                   </span>
                </div>
            </div>
        </div>
        <div class="wpm-row">
            <div class="wpm-row mb-0">
                <textarea id="" class="wpm-wide" rows="5"></textarea>
            </div>
        </div>
        <?php wpm_render_partial('fields/checkbox', 'admin', ['label' => __('Политика конфиденциальности', 'mbl_admin'), 'name' => '', 'value' => 'on']) ?>
        <div class="mblr_autoreg_buttons">
            <button type="button" class="button button-primary"><?php _e('Получить код', 'mbl_admin'); ?></button>
            <button type="button" class="button button-primary"><?php _e('Скопировать', 'mbl_admin'); ?></button>
        </div>
    </div>
</div>