<?php do_action('mbl_before_category_edit', $term, $term_meta); ?>
<tr class="form-field term-description-wrap">
    <th scope="row"><label for="short_description"><?php _e('Подзаголовок', 'mbl_admin') ?></label></th>
    <td>
        <textarea
                name="term_meta[short_description]"
                id="short_description"
                rows="2"
                cols="50"
                class="large-text"
        ><?php echo stripslashes_deep(wpm_array_get($term_meta, 'short_description')); ?></textarea>
    </td>
</tr>

<tr class="mbl-autotraining-wrap" id="mbl-autotraining-settings">
    <th><?php _e('Автотренинг', 'mbl_admin') ?></th>
    <td>
        <label>
            <input
                type="checkbox"
                name="term_meta[category_type]"
                <?php echo wpm_array_get($term_meta, 'category_type') == 'on' ? 'checked' : ''; ?>
                id="mbl_autotraining_checkbox"
            >
            <?php _e('Сделать автотренингом', 'mbl_admin') ?>
        </label>
        <?php do_action('mbl_category_autotraining_before_shift', compact('term_meta')); ?>
        <div id="mbl_autotraining_first_material" class="mbl_autotraining_first_material <?php echo wpm_array_get($term_meta, 'category_type') == 'on' ? '' : 'hidden'; ?>">
            <dl>
                <dt><h4><?php _e('Доступность первого материала:','mbl_admin');?></h4></dt>
                <dd>
                    <label>
                        <input type="radio"
                               name="term_meta[autotraining_first_material]"
                               id="autotraining_first_material_opened"
                               value="opened"
                               <?php echo wpm_array_get($term_meta, 'autotraining_first_material', 'opened') == 'opened' ? 'checked' : '';?> />
                        <b><?php _e('Доступен сразу после открытия рубрики (папки)', 'mbl_admin'); ?></b>
                    </label>
                </dd>
                <dd>
                    <label>
                        <input type="radio"
                               name="term_meta[autotraining_first_material]"
                               id="autotraining_first_material_shift"
                               value="shift"
                               <?php echo wpm_array_get($term_meta, 'autotraining_first_material') == 'shift' ? 'checked' : '';?> />
                        <b><?php _e('Доступен после смещения от регистрации пользователя', 'mbl_admin'); ?></b>
                    </label>
                    <div id="autotraining_first_material_shift_label" class="<?php echo wpm_array_get($term_meta, 'autotraining_first_material', 'opened') == 'shift' ? '' : 'disabled_field';?>">
                        <?php wpm_render_partial('shift/config', 'admin', array('page_meta' => $term_meta, 'key' => 'first_material_shift', 'meta_key' => 'term_meta')) ?>
                    </div>
                </dd>
                <?php /*
                    <dd>
                        <label>
                            <input type="radio"
                                   name="term_meta[autotraining_first_material]"
                                   id="autotraining_first_material_shift_activation"
                                   value="shift_activation"
                                   <?php echo wpm_array_get($term_meta, 'autotraining_first_material') == 'shift_activation' ? 'checked' : '';?> />
                            <b><?php _e('Доступен после смещения от активации уровня доступа', 'mbl_admin'); ?></b>
                        </label>
                        <div id="autotraining_first_material_shift_activation_label" class="<?php echo wpm_array_get($term_meta, 'autotraining_first_material') == 'shift_activation' ? '' : 'disabled_field';?>">
                            <?php wpm_render_partial('shift/config', 'admin', array('page_meta' => $term_meta, 'key' => 'first_material_activation_shift', 'meta_key' => 'term_meta')) ?>
                        </div>
                    </dd>
                */ ?>
            </dl>
        </div>
        <?php do_action('mbl_category_autotraining_after_shift', compact('term_meta')); ?>
    </td>
</tr>

<tr>
    <th><?php _e('Стикер', 'mbl_admin') ?></th>
    <td>
        <div class="wpm-control-row">
            <label>
                <input type="hidden" name="term_meta[sticker_show]" value="off">
                <input
                    type="checkbox"
                    name="term_meta[sticker_show]"
                    <?php echo wpm_array_get($term_meta, 'sticker_show') == 'on' ? 'checked="checked"' : ''; ?>
                >
                <?php _e('Отображать', 'mbl_admin') ?></label>
        </div>
        <table>
            <tr class="form-field">
                <th style="width: 10%;"><?php _e('Цвет фона', 'mbl_admin') ?></th>
                <td>
                    <input
                        type="text"
                        name="term_meta[sticker_bg_color]"
                        class="color"
                        style="width: auto"
                        value="<?php echo wpm_array_get($term_meta, 'sticker_bg_color', 'cd814e'); ?>">
                </td>
            </tr>
            <tr class="form-field">
                <th><?php _e('Цвет текста', 'mbl_admin') ?></th>
                <td>
                    <input
                        type="text"
                        name="term_meta[sticker_text_color]"
                        style="width: auto"
                        class="color"
                        value="<?php echo wpm_array_get($term_meta, 'sticker_text_color'); ?>">
                </td>
            </tr>
            <tr class="form-field">
                <th><?php _e('Текст', 'mbl_admin') ?></th>
                <td><input
                    type="text"
                    maxlength="40"
                    name="term_meta[sticker_text]"
                    value="<?php echo wpm_array_get($term_meta, 'sticker_text'); ?>"></td>
            </tr>
        </table>
    </td>
</tr>

<tr>
    <th><?php _e('Индикаторы', 'mbl_admin') ?></th>
    <td>
        <div class="wpm-control-row">
            <label>
                <input type="hidden" name="term_meta[individual_indicators]" value="0">
                <input
                    type="checkbox"
                    id="individual_indicators_checkbox"
                    name="term_meta[individual_indicators]"
                    value="1"
                    <?php echo wpm_array_get($term_meta, 'individual_indicators', 0) ? 'checked="checked"' : ''; ?>
                >
                <?php _e('Индивидуальные настройки индикаторов данной рубрики', 'mbl_admin') ?></label>
        </div>
        <div
            style="padding-left: 25px; <?php echo wpm_array_get($term_meta, 'individual_indicators', 0) ? '' : 'display:none;'; ?>"
            class="wpm-control-row"
            id="individual_indicators">
            <div class="wpm-control-row">
                <label>
                    <input type="hidden" name="term_meta[comments_show]" value="0">
                    <input
                        type="checkbox"
                        name="term_meta[comments_show]"
                        value="1"
                        <?php echo wpm_array_get($term_meta, 'comments_show', 0) ? 'checked="checked"' : ''; ?>
                    >
                    <?php _e('Отображать количество комментариев', 'mbl_admin') ?></label>
            </div>
            <div class="wpm-control-row">
                <label>
                    <input type="hidden" name="term_meta[views_show]" value="0">
                    <input
                        type="checkbox"
                        name="term_meta[views_show]"
                        value="1"
                        <?php echo wpm_array_get($term_meta, 'views_show', 0) ? 'checked="checked"' : ''; ?>
                    >
                    <?php _e('Отображать количество просмотров', 'mbl_admin') ?></label>
            </div>
            <div class="wpm-control-row">
                <label>
                    <input type="hidden" name="term_meta[progress_show]" value="0">
                    <input
                        type="checkbox"
                        name="term_meta[progress_show]"
                        value="1"
                        <?php echo wpm_array_get($term_meta, 'progress_show', 0) ? 'checked="checked"' : ''; ?>
                    >
                    <?php _e('Отображать прогресс курса', 'mbl_admin') ?></label>
            </div>
            <div class="wpm-control-row">
                <label>
                    <input type="hidden" name="term_meta[access_show]" value="0">
                    <input
                        type="checkbox"
                        name="term_meta[access_show]"
                        value="1"
                        <?php echo wpm_array_get($term_meta, 'access_show', 0) ? 'checked="checked"' : ''; ?>
                    >
                    <?php _e('Отображать доступность', 'mbl_admin') ?></label>
            </div>
        </div>
    </td>
</tr>

<tr>
    <th><?php _e('Описание рубрики', 'mbl_admin') ?></th>
    <td>
        <div class="wpm-control-row">
            <label>
                <input type="hidden" name="term_meta[individual_description]" value="0">
                <input
                    type="checkbox"
                    id="individual_description_checkbox"
                    name="term_meta[individual_description]"
                    value="1"
                    <?php echo wpm_array_get($term_meta, 'individual_description', 0) ? 'checked="checked"' : ''; ?>
                >
                <?php _e('Индивидуальные настройки описания данной рубрики', 'mbl_admin') ?></label>
        </div>
        <div
            style="padding-left: 25px; <?php echo wpm_array_get($term_meta, 'individual_description', 0) ? '' : 'display:none;'; ?>"
            class="wpm-control-row"
            id="individual_description">
            <div class="wpm-control-row">
                <label>
                    <input type="hidden" name="term_meta[show_description]" value="0">
                    <input
                        type="checkbox"
                        name="term_meta[show_description]"
                        value="1"
                        <?php echo wpm_array_get($term_meta, 'show_description', 0) ? 'checked="checked"' : ''; ?>
                    >
                    <?php _e('Отображать', 'mbl_admin') ?></label>
            </div>
            <div class="wpm-control-row">
                <label>
                    <input type="hidden" name="term_meta[description_expand]" value="0">
                    <input
                        type="checkbox"
                        name="term_meta[description_expand]"
                        value="1"
                        <?php echo wpm_array_get($term_meta, 'description_expand', 0) ? 'checked="checked"' : ''; ?>
                    >
                    <?php _e('Открывать при входе', 'mbl_admin') ?></label>
            </div>
            <div class="wpm-control-row">
                <label>
                    <input type="hidden" name="term_meta[show_description_always]" value="0">
                    <input
                        type="checkbox"
                        name="term_meta[show_description_always]"
                        value="1"
                        <?php echo wpm_array_get($term_meta, 'show_description_always', 0) ? 'checked="checked"' : ''; ?>
                    >
                    <?php _e('Зафиксировать открытым', 'mbl_admin') ?></label>
            </div>
        </div>
    </td>
</tr>

<tr>
    <th><?php _e('Доступ', 'mbl_admin') ?></th>
    <td>
        <select name="term_meta[visibility_level_action]">
            <option value="hide" <?php echo (wpm_array_get($term_meta, 'visibility_level_action') == 'hide')? 'selected':''; ?>><?php _e('Не отображать для следующих уровней доступа:', 'mbl_admin') ?></option>
            <option value="show_only" <?php echo (wpm_array_get($term_meta, 'visibility_level_action') == 'show_only')? 'selected':''; ?>><?php _e('Отображать только для следующих уровней доступа:', 'mbl_admin') ?></option>
        </select>
            <?php  wpm_hierarchical_category_tree(0, $term_meta); ?>
        <p>
            <label>
                <input type="checkbox"
                       name="term_meta[hide_for_not_registered]"
                    <?php echo wpm_array_get($term_meta, 'hide_for_not_registered')=='on' ? 'checked' : ''; ?>>
                <?php _e('Не отображать для незарегистрированных пользователей.', 'mbl_admin') ?></label>
        </p>
    </td>
</tr>

<tr>
    <th><?php _e('Фоновое изображение', 'mbl_admin') ?></th>
    <td>
        <input type="hidden"
               id="wpm_term_bg_url"
               name="term_meta[bg_url]"
               value="<?php echo wpm_array_get($term_meta, 'bg_url'); ?>">

        <input type="hidden"
               id="wpm_term_bg_url_original"
               name="term_meta[bg_url_original]"
               value="<?php echo  wpm_array_get($term_meta, 'bg_url_original', wpm_array_get($term_meta, 'bg_url')); ?>">

        <div class="wpm-control-row">
            <p>
                <button type="button" class="wpm-media-upload-button button"
                        data-id="term_bg_url"><?php _e('Загрузить', 'mbl_admin'); ?></button>
                <a id="delete-wpm-favicon"
                                class="wpm-delete-media-button button submit-delete"
                                data-id="term_bg_url"><?php _e('Удалить', 'mbl_admin'); ?></a>
                <?php wpm_render_partial('crop-button', 'admin', array('key' => wpm_array_get($term_meta, 'bg_url'), 'id' => 'term_bg_url', 'w' => 345, 'h' => 260)) ?>
            </p>
        </div>
        <div class="wpm-term_bg_url-preview-wrap inactive grey">
            <div class="wpm-term_bg_url-preview-box">
                <img src="<?php echo wpm_array_get($term_meta, 'bg_url'); ?>" title="" alt=""
                     id="wpm-term_bg_url-preview">
            </div>
        </div>
    </td>
</tr>
<?php do_action('mbl_after_category_edit', $term, $term_meta); ?>

<script>
    jQuery(function ($) {
        // Upload media file ====================================
        var wpm_file_frame;
        var image_id = '';
        $(document).on('click', '.wpm-media-upload-button', function (event) {
            image_id = $(this).attr('data-id');
            event.preventDefault();
            // If the media frame already exists, reopen it.
            if (wpm_file_frame) {
                wpm_file_frame.open();
                return;
            }
            // Create the media frame.
            wpm_file_frame = wp.media.frames.downloadable_file = wp.media({
                title    : '<?php _e('Выберите файл', 'mbl_admin') ?>',
                button   : {
                    text : '<?php _e('Использовать изображение', 'mbl_admin') ?>'
                },
                multiple : false
            });
            // When an image is selected, run a callback.
            wpm_file_frame.on('select', function () {
                var attachment = wpm_file_frame.state().get('selection').first().toJSON();
                // console.log(attachment);
                $('input#wpm_' + image_id).val(attachment.url);
                if($('input#wpm_' + image_id + '_original').length) {
                    $('input#wpm_' + image_id + '_original').val(attachment.url);
                }
                $('#wpm-' + image_id + '-preview').attr('src', attachment.url).show();
                $('#delete-wpm-' + image_id).show();
                $('.wpm-crop-media-button[data-id="' + image_id +'"]').show();
            });
            // Finally, open the modal.
            wpm_file_frame.open();
        });
        $(document).on('click', '.wpm-delete-media-button', function () {
            image_id = $(this).attr('data-id');
            $('input#wpm_' + image_id).val('');
            if($('input#wpm_' + image_id + '_original').length) {
                $('input#wpm_' + image_id + '_original').val('');
            }
            $('#delete-wpm-' + image_id).hide();
            $('#wpm-' + image_id + '-preview').hide();
            $('.wpm-crop-media-button[data-id="' + image_id +'"]').hide();
        });
        $(document).on('change', '#individual_indicators_checkbox', function() {
            var $holder = $('#individual_indicators');

            if($(this).prop('checked')) {
                $holder.slideDown();
            } else {
                $holder.slideUp();
            }
        });
        $(document).on('change', '#individual_description_checkbox', function() {
            var $holder = $('#individual_description');

            if($(this).prop('checked')) {
                $holder.slideDown();
            } else {
                $holder.slideUp();
            }
        });
        //--------
    });
</script>