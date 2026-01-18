<?php

/**
 *
 */
add_action( 'restrict_manage_posts', 'wpm_restrict_manage_posts' );
function wpm_restrict_manage_posts() {
    global $typenow;
    if ($typenow != 'wpm-page') {
        return;
    }

    $filters = array(
        'wpm-category',
        'wpm-levels'
    );
    foreach ($filters as $tax_slug) {
        $tax_obj = get_taxonomy($tax_slug);
        $tax_name = $tax_obj->labels->name;
        $tax_name = mb_strtolower($tax_name);
        $terms = get_terms($tax_slug);
        echo "<select name='$tax_slug' id='$tax_slug' class='postform'>";
        echo "<option value=''>" . __('Все', 'mbl_admin') . " {$tax_name}</option>";
        echo "<option value='__none__' " . (wpm_array_get($_GET, $tax_slug) == '__none__' ? 'selected="selected"' : '') . ">" . __('Без', 'mbl_admin') . " " . ($tax_slug == 'wpm-category' ? __('рубрики', 'mbl_admin') : __('уровня доступа', 'mbl_admin')) ."</option>";
        foreach ($terms as $term) {
            $isCurrent = wpm_array_get($_GET, $tax_slug) == $term->slug;
            echo '<option value=' . $term->slug, $isCurrent ? ' selected="selected"' : '', '>' . $term->name . ' (' . $term->count . ')</option>';
        }
        echo "</select>";
    }

}

add_filter('posts_search', 'wpm_search_where');
function wpm_search_where($where)
{
    global $pagenow, $wpdb;

    if (is_admin() && 'edit.php' === $pagenow && 'wpm-page' === wpm_array_get($_GET, 'post_type') && !empty($_GET['s'])) {
        $where = preg_replace(
            "/\s*OR\s*\(\s*" . $wpdb->posts . ".post_content\s+LIKE\s*\'[^\']+\'\s*\)/",
            '', $where);
    }

    return $where;
}

add_filter('parse_query', 'wpm_posts_filter');
function wpm_posts_filter($query)
{
    global $pagenow;
    $type = 'post';
    if (isset($_GET['post_type'])) {
        $type = $_GET['post_type'];
    }

    if ('wpm-page' == $type && is_admin() && ($pagenow == 'edit.php' || (isset($_GET['page']) && $_GET['page'] == 'wpm-autotraining'))) {
        $taxQuery = array();

        if (wpm_array_get($_GET, 'wpm-category') == '__none__') {
            $query->set('wpm-category', '');
            $taxQuery[] = array(
                'taxonomy' => 'wpm-category',
                'field' => 'term_id',
                'operator' => 'NOT IN',
                'terms' => get_terms('wpm-category', array(
                    'fields' => 'ids'
                ))
            );
        }

        if (wpm_array_get($_GET, 'wpm-levels') == '__none__') {
            $query->set('wpm-levels', '');
            $taxQuery[] = array(
                'taxonomy' => 'wpm-levels',
                'field' => 'term_id',
                'operator' => 'NOT IN',
                'terms' => get_terms('wpm-levels', array(
                    'fields' => 'ids'
                ))
            );
        }

        if (count($taxQuery)) {
            $query->set('tax_query', $taxQuery);
        }
    }
}

//---------------

add_action('wpm-category_edit_form_fields', 'wpm_category_edit', 10, 2);
function wpm_category_edit($term, $taxonomy){
    $term_id = $term->term_id;
    $term_meta = get_option( "taxonomy_term_$term_id" );
    if(!isset($term_meta['hide_for_not_registered'])){
        $term_meta['hide_for_not_registered'] = 'hide';
    }

    wpm_render_partial('category-edit', 'admin', compact('term', 'taxonomy', 'term_id', 'term_meta'));
}

add_action('wpm-category_edit_form_fields', 'wpm_category_edit_add', 15, 2);
function wpm_category_edit_add($term, $taxonomy){
    $term_id = $term->term_id;
    $term_meta = get_option( "taxonomy_term_$term_id" );
    ?>
    <tr>
        <th>&nbsp;</th>
        <td>
            <p>
                <label>
                    <input type="checkbox"
                           name="term_meta[hide_materials]"
                        <?php echo ($term_meta && array_key_exists('hide_materials', $term_meta) && $term_meta['hide_materials']=='on') ? 'checked' : ''; ?>>
                    <?php _e('Скрыть материалы, показывать только описание.', 'mbl_admin'); ?>
                </label>
            </p>
        </td>
    </tr>
    <tr>
        <th>Перенаправления</th>
        <td>
            <div>
                <label for="page_redirect"><?php _e('Введите URL для перенаправления', 'mbl_admin'); ?></label>
                <div>
                    <input type="text" name="custom_meta[redirect_page]" id="page_redirect"
                           style="width: 99%"
                           value="<?= get_term_meta($term_id, 'redirect_page', true);?>">
                </div>
            </div>
            <div>
                <p>
                    <label>
                        <input type="hidden" value="0" name="custom_meta[redirect_page_blank]">
                        <input type="checkbox"
                               name="custom_meta[redirect_page_blank]"
                               value="1"
                               <?php checked(get_term_meta($term_id, 'redirect_page_blank', true), '1') ?>
				            >
			            <?php _e('Открывать в новой вкладке', 'mbl_admin'); ?>
                    </label>
                </p>
            </div>
            <div>
                <p>
                    <label>
                        <input type="hidden" value="0" name="custom_meta[redirect_page_on]">
                        <input type="checkbox"
                               name="custom_meta[redirect_page_on]"
                               value="1"
                               <?php checked(get_term_meta($term_id, 'redirect_page_on', true), '1') ?>
				            >
			            <?php _e('Включить перенаправление', 'mbl_admin'); ?>
                    </label>
                </p>
            </div>
        </td>
    </tr>
    <tr>
        <th>Скрипты</th>
        <td>
            <div>
                <p>
                    <label>
                        <textarea name="wpm_category_meta[head_code]" class="wpm-wide code-style"
                                  placeholder="<?php _e('Ваш код', 'mbl_admin') ?>"
                                  rows="20"><?= esc_textarea(wpm_get_category_meta($term_id, 'head_code')); ?></textarea>
                    </label>
                </p>
            </div>
        </td>
    </tr>
    <?php
}


function wpm_save_category_fields($term_id)
{
    if (isset($_POST['term_meta'])) {

        $term_meta = get_option("taxonomy_term_$term_id");
        $cat_keys = array_keys($_POST['term_meta']);

        if(!array_key_exists('category_type',$cat_keys)) {
            $term_meta['category_type'] = 'off';
        }

        if(!array_key_exists('hide_for_not_registered',$cat_keys)) {
            $term_meta['hide_for_not_registered'] = 'off';
        }

        if(!array_key_exists('hide_materials',$cat_keys)) {
            $term_meta['hide_materials'] = 'off';
        }

        foreach ($cat_keys as $key) {
            if (isset($_POST['term_meta'][$key])) {
                if ($key == 'exclude_levels') {
                    $term_meta[$key] = implode(',', $_POST['term_meta'][$key]);
                } else {
                    $term_meta[$key] = stripslashes(wp_filter_post_kses(addslashes($_POST['term_meta'][$key])));
                }
            }
        }
        if(!isset($_POST['term_meta']['exclude_levels'])) {
            unset($term_meta['exclude_levels']);
        }

        if(wpm_array_get($term_meta, 'first_material_shift_type', 'interval') == 'interval') {
            if (isset($term_meta['first_material_shift_minutes']) && intval($term_meta['first_material_shift_minutes'])) {
                $shiftHours = intval($term_meta['first_material_shift']);
                $shiftHours += wpm_minutes2hours($term_meta['first_material_shift_minutes']);
                $term_meta['first_material_shift'] = $shiftHours;
            }

            if (intval(wpm_array_get($term_meta, 'first_material_shift_days'))) {
                $term_meta['first_material_shift'] += intval(wpm_array_get($term_meta, 'first_material_shift_days')) * 24;
            }
        }

        if(wpm_array_get($term_meta, 'first_material_activation_shift_type', 'interval') == 'interval') {
            if (isset($term_meta['first_material_activation_shift_minutes']) && intval($term_meta['first_material_activation_shift_minutes'])) {
                $shiftHours = intval($term_meta['first_material_activation_shift']);
                $shiftHours += wpm_minutes2hours($term_meta['first_material_activation_shift_minutes']);
                $term_meta['first_material_activation_shift'] = $shiftHours;
            }

            if (intval(wpm_array_get($term_meta, 'first_material_activation_shift_days'))) {
                $term_meta['first_material_activation_shift'] += intval(wpm_array_get($term_meta, 'first_material_activation_shift_days')) * 24;
            }
        }

        update_option("taxonomy_term_$term_id", $term_meta);

        if ($term_meta['category_type'] == 'on') {
            wpm_autotraining_schedule_option($term_id);
        }
    }elseif(!isset($_POST['_inline_edit'])){
        update_option("taxonomy_term_$term_id", array());
    }
    if (isset($_POST['custom_meta'])) {
        foreach ($_POST['custom_meta'] as $key => $value) {
            update_term_meta($term_id, $key, $value);
        }
    }
    if (isset($_POST['wpm_category_meta'])) {
        foreach ($_POST['wpm_category_meta'] as $key => $value) {
            wpm_update_category_meta($term_id, $key, $value);
        }
    }
}

add_action('edited_wpm-category', 'wpm_save_category_fields', 10, 2);

//-----------

function wpm_for_array_map($data){
    $units = array_key_exists('units', $data) ? $data['units'] : 'months';

    return intval(wpm_array_get($data, 'is_unlimited'))
        ? '0_0_1'
        : $data['duration'] . '_' . $units . '_0';
}

function wpm_get_keys_html_list ($term_keys, $term_id)
{
    $data = '';
    $result = array();

    if(is_array($term_keys) && !empty($term_keys)){

        $keys_by_period = array_count_values(array_map('wpm_for_array_map', $term_keys));

        foreach ($keys_by_period as $key => $value) {
            $key_params = explode('_', $key);
            $duration = $key_params[0];
            $units = $key_params[1];
            $is_unlimited = $key_params[2];
            $result[$key]['new'] = 0;
            $result[$key]['used'] = 0;

            foreach ($term_keys as $item) {
                $status = $item['status'];

                if(isset($item['sent']) && $item['sent']) {
                    $status = 'used';
                }

                $isCurrent = ($item['duration'] == $duration && wpm_is_units_equal($item, $units)) || ($is_unlimited && $item['is_unlimited']);

                if ($status == 'new' && $isCurrent) {
                    $result[$key]['new']++;
                }
                if ($status == 'used' && $isCurrent) {
                    $result[$key]['used']++;
                }
            }

            switch ($units) {
                case 'months':
                    $units_msg = __('мес.', 'mbl_admin');
                    break;
                case 'days':
                    $units_msg = __('дн.', 'mbl_admin');
                    break;
            }

            $durationText = $is_unlimited ? __('Неограниченный доступ', 'mbl_admin') : ($duration . ' ' . $units_msg);

            $data .= '<tr>' .
                '<td>' . $durationText . '</td>' .
                '<td>' . $value . '</td>' .
                '<td style="white-space: nowrap">' .
                $result[$key]["used"] .
                ($result[$key]["used"] ? ' <button type="button" class="button  show-used" term_id="' . $term_id . '" duration="' . $duration . '" units="' . $units . '" is_unlimited="'.$is_unlimited.'">' . __('Показать', 'mbl_admin') . '</button>' : '') .
                '</td>' .
                '<td style="white-space: nowrap">' .
                $result[$key]["new"] .
                ' <button type="button" class="button show-keys" term_id="' . $term_id . '" duration="' . $duration . '" units="' . $units . '" is_unlimited="'.$is_unlimited.'">' . __('Показать', 'mbl_admin') . '</button>' .
                '<a class="delete-button delete-keys remove-keys" term_id="' . $term_id . '" duration="' . $duration . '" units="' . $units . '" is_unlimited="'.$is_unlimited.'">' . __('Удалить', 'mbl_admin') . '</a>' .
                '</td>' .
                '</tr>';
        }
    }

    return $data;
}

add_action('wpm-levels_edit_form_fields', 'wpm_level_taxonomy_keys', 10, 2);
function wpm_level_taxonomy_keys($term, $taxonomy)
{
    add_thickbox();

    // put the term ID into a variable
    $term_id = $term->term_id;
    $term_meta = get_option("taxonomy_term_$term_id" );
    $defaultTermMeta = mbl_default_term_meta();
    $term_keys = MBLTermKeysQuery::find(array('term_id' => $term_id, 'key_type' => 'wpm_term_keys', 'is_banned' => 'all'));

    ?>
    <script>
        jQuery(function ($) {

            $(document).on('click', '.add-manual-keys', function () {

                var manual_message_wrap = $('.add-manual-keys-message');
                manual_message_wrap.html('');
                var duration_manual = $('#duration-manual').val();
                var units_manual = $('#units-manual').val();
                var keys_manual = $('#manual-keys').val();
                var is_unlimited_manual = $('#duration-manual').closest('.mbl_term_keys_period').find('.option_is_unlimited').prop('checked') ? 'on' : 'off';


                keys_manual = keys_manual.replace(/  +/g, ' ');

                if(keys_manual == "" || keys_manual == ' '){
                    alert('<?php esc_html_e('Сначала Вставьте свои коды доступа.', 'mbl_admin'); ?>');
                    $('#manual-keys').val('');
                    return;
                }

                if(Math.floor(duration_manual) != duration_manual || !$.isNumeric(duration_manual) || duration_manual < 0){
                    alert('<?php esc_html_e('Неверное значение периода. Только целые числа, больше нуля.', 'mbl_admin'); ?>');
                    return;
                }


                $.ajax({
                    type     : 'POST',
                    url      : ajaxurl,
                    dataType : 'json',
                    data     : {
                        action       : "wpm_add_manual_user_level_keys_action",
                        term_id      : <?php echo $term_id; ?>,
                        duration     : duration_manual,
                        units        : units_manual,
                        keys         : keys_manual,
                        is_unlimited : is_unlimited_manual
                    },
                    success  : function (data) {
                        if (!data.error) {
                            $('#keys-list').html(data.html);
                            manual_message_wrap.html('<span class="wpm-message wpm-message-success">' + data.message + '</span>');

                        } else {
                            manual_message_wrap.html('<span class="wpm-message wpm-message-fail">' + data.message + '</span>');
                        }


                    },
                    error    : function (errorThrown) {

                    }
                });
            });

            $(document).on('click', '.add-keys', function () {
                var duration = $('#duration').val();
                var units = $('#units').val();
                var count = $('#count').val();
                var is_unlimited = $('#duration').closest('.mbl_term_keys_period').find('.option_is_unlimited').prop('checked') ? 'on' : 'off';
                var format = $('');
                if(Math.floor(duration) != duration || !$.isNumeric(duration) || duration < 0){
                    alert('<?php esc_html_e('Неверное значение периода. Только целые числа, больше нуля.', 'mbl_admin'); ?>');
                    return;
                }
                if(Math.floor(count) != count || !$.isNumeric(count) || count < 0){
                    alert('<?php esc_html_e('Неверное значение количества ключей. Только целые числа, больше нуля.', 'mbl_admin'); ?>');
                    return;
                }

                tb_show("<?php esc_html_e('Оставшиеся коды доступа', 'mbl_admin'); ?>", "#TB_inline?width=640&&height=550&inlineId=wpm_popup_box");
                $('#TB_ajaxContent').find('#user-level-keys').html('');
                $('.wpm-top-popup-nav .message').html('');
                $('#TB_ajaxContent').css({'width': '640', 'height': ($('#TB_window').height() - 50) + 'px'}).addClass('wpm-loader');
                $.ajax({
                    type     : 'POST',
                    url      : ajaxurl,
                    dataType : 'json',
                    data : {
                        action       : "wpm_add_user_level_keys_action",
                        term_id      : <?php echo $term_id; ?>,
                        count        : count,
                        duration     : duration,
                        units        : units,
                        is_unlimited : is_unlimited
                    },
                    success  : function (data) {
                        var $TBAjaxContent = $('#TB_ajaxContent');

                        $TBAjaxContent.find('#user-level-keys').html(data.keys);
                        $TBAjaxContent.removeClass('wpm-loader');

                        $('#keys-list').html(data.html);
                    },
                    error    : function (errorThrown) {

                    }
                });
            });


            /*  show keys in popup */
            $(document).on('click', '.show-keys', function () {
                var $TBAjaxContent;

                tb_show("<?php esc_html_e('Оставшиеся коды доступа', 'mbl_admin'); ?>", "#TB_inline?width=640&&height=550&inlineId=wpm_popup_box");

                $TBAjaxContent = $('#TB_ajaxContent');
                $TBAjaxContent.find('#user-level-keys').html('');
                $TBAjaxContent.find('.wpm-top-popup-nav').hide();
                $('.wpm-top-popup-nav .message').html('');
                $TBAjaxContent.css({'width': '640', 'height': ($('#TB_window').height() - 50) + 'px'}).addClass('wpm-loader');

                var $duration = $(this).attr('duration');
                var $units = $(this).attr('units');
                var $is_unlimited = $(this).attr('is_unlimited');

                $.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    data: {
                        action: "wpm_get_keys_action",
                        term_id: $(this).attr('term_id'),
                        duration: $duration,
                        units: $units,
                        is_unlimited : $is_unlimited
                    },
                    success : function (data) {
                        var $TBAjaxContent = $('#TB_ajaxContent');
                        $TBAjaxContent.find('#user-level-keys').html(data);
                        $TBAjaxContent.removeClass('wpm-loader');
                        $TBAjaxContent.find('[name="wpm_move_keys_duration_from"]').val($duration);
                        $TBAjaxContent.find('[name="wpm_move_keys_units_from"]').val($units);
                        $TBAjaxContent.find('[name="wpm_move_keys_is_unlimited_from"]').val($is_unlimited);

                        if (data == '') {
                            $TBAjaxContent.find('.wpm-top-popup-nav').hide();
                        } else {
                            $TBAjaxContent.find('.wpm-top-popup-nav').show();
                        }
                    }
                });
            });
            $(document).on('click', '.show-used', function () {
                var $TBAjaxContent;

                tb_show("<?php esc_html_e('Использованные коды доступа', 'mbl_admin'); ?>", "#TB_inline?width=640&&height=550&inlineId=wpm_popup_box");

                $TBAjaxContent = $('#TB_ajaxContent');
                $TBAjaxContent.find('#user-level-keys').html('');
                $TBAjaxContent.find('.wpm-top-popup-nav').hide();
                $TBAjaxContent.find('#wpm-move-keys-tools').hide();

                $('.wpm-top-popup-nav .message').html('');
                $TBAjaxContent.css({'width': '640', 'height': ($('#TB_window').height() - 50) + 'px'}).addClass('wpm-loader');

                $.ajax({
                    type    : 'POST',
                    url     : ajaxurl,
                    data    : {
                        action       : "wpm_get_used_keys_action",
                        term_id      : $(this).attr('term_id'),
                        duration     : $(this).attr('duration'),
                        units        : $(this).attr('units'),
                        is_unlimited : $(this).attr('is_unlimited')
                    },
                    success : function (data) {
                        var $TBAjaxContent = $('#TB_ajaxContent');
                        $TBAjaxContent.find('#user-level-keys').html(data);
                        $TBAjaxContent.removeClass('wpm-loader')
                    }
                });
            });

            var notification = $('.notification');

            $(document).on('click', '.remove-keys', function () {
                var duration = $(this).attr('duration');
                var units = $(this).attr('units');
                var is_unlimited = $(this).attr('is_unlimited');
                var do_remove = confirm("<?php esc_html_e('Вы действительно хотите удалить коды доступа?', 'mbl_admin'); ?>");
                if (do_remove) {
                    $.ajax({
                        type    : 'POST',
                        url     : ajaxurl,
                        data    : {
                            action       : "wpm_remove_user_level_keys_action",
                            term_id      : <?php echo $term_id; ?>,
                            duration     : duration,
                            units        : units,
                            is_unlimited : is_unlimited
                        },
                        success : function (data) {
                            if (!data.error) {
                                location.reload();
                            } else {
                                alert('<?php esc_html_e('Коды не удалены', 'mbl_admin'); ?>');
                            }

                        }
                    });
                }

            });

            /* copy keys to clipboard */
            var copy_keys = new ZeroClipboard( $('.wpm-copy-keys'), {
                moviePath: "<?php echo plugins_url('/member-luxe/js/zeroclipboard/ZeroClipboard.swf') ?>"
            } );
            copy_keys.on("aftercopy", function(e) {
                $('.wpm-top-popup-nav .message').html('<?php esc_html_e('Скопировано!','mbl_admin'); ?>');
            });


            $('.wpm-tabs').tabs({
                autoHeight: false,
                collapsible: false,
                fx: {
                    opacity: 'toggle',
                    duration: 'fast'
                }
            });

            $(document).on('change', '.justclick_subscribe_trigger', function () {
                var $trigger = $(this),
                    $wrapper = $trigger.closest('.justclick_subscribe'),
                    $holder = $wrapper.find('.justclick_subscribe_holder'),
                    $isActive = $trigger.prop('checked');

                if ($isActive) {
                    $wrapper.addClass('active');
                    $holder.slideDown();
                } else {
                    $wrapper.removeClass('active');
                    $holder.slideUp();
                }

                if(!$isActive) {
                    $holder.find('select').val([]);
                }

                return false;
            });

            $(document).on('change', '.sendpulse_subscribe_trigger', function () {
                var $trigger = $(this),
                    $wrapper = $trigger.closest('.sendpulse_subscribe'),
                    $holder = $wrapper.find('.sendpulse_subscribe_holder'),
                    $isActive = $trigger.prop('checked');

                if ($isActive) {
                    $wrapper.addClass('active');
                    $holder.slideDown();
                } else {
                    $wrapper.removeClass('active');
                    $holder.slideUp();
                }

                if(!$isActive) {
                    $holder.find('select').val([]);
                }

                return false;
            });

            $(document).on('change', '.autoweb_subscribe_trigger', function () {
                var $trigger = $(this),
                    $wrapper = $trigger.closest('.autoweb_subscribe'),
                    $holder = $wrapper.find('.autoweb_subscribe_holder'),
                    $isActive = $trigger.prop('checked');

                if ($isActive) {
                    $wrapper.addClass('active');
                    $holder.slideDown();
                } else {
                    $wrapper.removeClass('active');
                    $holder.slideUp();
                }

                if(!$isActive) {
                    $holder.find('select').val([]);
                }

                return false;
            });

            $(document).on('change', '.justclick_unsubscribe', function () {
                var $holder = $('#justclick_del_level_groups');
                if($(this).val() == '') {
                    $holder.slideDown();
                } else {
                    $holder.find('select').val([]);
                    $holder.slideUp();
                }
            });

            var $justclickLevelGroups = $('#justclick_level_groups');
            if ($justclickLevelGroups.length) {
                $.post(ajaxurl, {
                    'action' : 'wpm_justclick_groups_level_select',
                    'term_id' : '<?php echo $term_id; ?>'
                }, function (html) {
                    $justclickLevelGroups.html(html);
                    $justclickLevelGroups.closest('.justclick_subscribe_holder').addClass('loaded');
                })
            }

            var $sendPulseLevelGroups = $('#sendpulse_level_groups');
            if ($sendPulseLevelGroups.length) {
                $.post(ajaxurl, {
                    'action' : 'wpm_sendpulse_groups_level_select',
                    'term_id' : '<?php echo $term_id; ?>'
                }, function (html) {
                    $sendPulseLevelGroups.html(html);
                    $sendPulseLevelGroups.closest('.sendpulse_subscribe_holder').addClass('loaded');
                })
            }

            var $autoWebLevelGroups = $('#autoweb_level_groups');
            if ($autoWebLevelGroups.length) {
                $.post(ajaxurl, {
                    'action' : 'wpm_autoweb_groups_level_select',
                    'term_id' : '<?php echo $term_id; ?>'
                }, function (html) {
                    console.log('autoweb_level_groups');
                    $autoWebLevelGroups.html(html);
                    $autoWebLevelGroups.closest('.autoweb_subscribe_holder').addClass('loaded');
                })
            }

            var $justclickDelLevelGroups = $('#justclick_del_level_groups');
            if ($justclickDelLevelGroups.length) {
                $.post(ajaxurl, {
                    'action' : 'wpm_justclick_groups_del_level_select',
                    'term_id' : '<?php echo $term_id; ?>'
                }, function (html) {
                    $justclickDelLevelGroups.html(html);
                    $justclickDelLevelGroups.closest('.justclick_subscribe_holder').addClass('loaded');
                })
            }

        });
    </script>

    </pre>
    <tr>
        <th>
            <?php esc_html_e('Видимость', 'mbl_admin'); ?>
        </th>
        <td>
            <p><label><input type="checkbox" name="term_meta[hide_for_no_access]" value="hide" <?php if(wpm_array_get($term_meta, 'hide_for_no_access') == 'hide') echo 'checked'; ?> ><?php _e('Скрывать материалы если нет доступа к этому уровню', 'mbl_admin'); ?></label></p>
        </td>
    </tr>
    <tr class="form-field">
        <th><label><?php _e('При отсутствии доступа', 'mbl_admin'); ?></label></th>
        <td id="mbl_no_access_redirect_container">
            <input type="hidden" name="term_meta[no_access_redirect_cart]" value="off">
            <input type="hidden" name="term_meta[no_access_redirect_url]" value="off">
            <?php if (defined('MBLP_VERSION') && class_exists('woocommerce')) : ?>
                <div>
                    <label>
                        <input type="checkbox" data-no-access-redirect="cart" name="term_meta[no_access_redirect_cart]" value="on" <?php echo wpm_array_get($term_meta, 'no_access_redirect_cart') == 'on' ? 'checked' : ''; ?>><?php _e('Перенаправлять на покупку товара', 'mbl_admin'); ?>:
                        <?php $products = wc_get_products(apply_filters('mbl_wc_get_products_args', ['type' => [MBLPProduct::MBL_PRODUCT_TYPE, MBLPProduct::IPR_PRODUCT_TYPE]])); ?>
                    </label>
                    <select name="term_meta[no_access_product_id]">
                        <?php foreach ($products as $product) : ?>
                            <option value="<?php echo $product->get_id(); ?>" <?php echo wpm_array_get($term_meta, 'no_access_product_id') == $product->get_id() ? 'selected' : ''; ?>><?php echo $product->get_name(); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>
            <div>
                <label>
                    <input type="checkbox" data-no-access-redirect="url" name="term_meta[no_access_redirect_url]" value="on" <?php echo wpm_array_get($term_meta, 'no_access_redirect_url') == 'on' ? 'checked' : ''; ?>><?php _e('Перенаправлять на URL', 'mbl_admin'); ?>:
                </label>
                <div>
                    <input type="text" name="term_meta[no_access_redirect_url_value]" value="<?php echo wpm_array_get($term_meta, 'no_access_redirect_url_value'); ?>">
                </div>
            </div>
        </td>
    </tr>
    <tr class="form-field">
        <th><label><?php _e('Продажа доступа', 'mbl_admin'); ?></label></th>
        <td>
            <div style="width: 95%">
                <?php wp_editor( stripslashes(wpm_array_get($term_meta, 'no_access_content')), 'no_access_content', array('textarea_name' => 'term_meta[no_access_content]', 'textarea_rows' => '20')); ?>
            </div>

        </td>
    </tr>
    <tr class="form-field">
        <th scope="row"><label for="wpm_keys_new"><?php _e('Письма напоминания', 'mbl_admin'); ?></label></th>
        <td>
            <div class="wpm-tabs-wrap postbox wpm-ui-wrap">
                <div class="wpm-inner-wrap mbl-color-content">
                    <div class="wpm-tabs wpm-inner-tabs">
                        <ul class="wpm-inner-tabs-nav">
                            <li><a href="#tab-1"><?php _e('Письмо 1', 'mbl_admin'); ?></a></li>
                            <li><a href="#tab-2"><?php _e('Письмо 2', 'mbl_admin'); ?></a></li>
                            <li><a href="#tab-3"><?php _e('Письмо 3', 'mbl_admin'); ?></a></li>
                            <li><a href="#tab-4"><?php _e('Массовые операции', 'mbl_admin'); ?></a></li>
                        </ul>
                        <div id="tab-1" class="tab">
                            <div class="wpm-tab-content">
                                <div class="wpm-row">
                                    <label class="schedule-days-wrap">
                                        <?php _e('Отправить письмо за', 'mbl_admin'); ?> <input type="number" min="1" size="2" maxlength="2" name="term_meta[letter_1_days]" value="<?= wpm_array_get($term_meta, 'letter_1_days') ?>">  <?php _e('дней до окончания срока', 'mbl_admin'); ?>
                                    </label>
                                </div>
                                <div class="wpm-row">
                                    <label>
                                        <input name="term_meta[letter_1_title]" type="text" class="large-text" value="<?= wpm_array_get($term_meta, 'letter_1_title') ?>" placeholder="<?php _e('Заголовок письма', 'mbl_admin'); ?>">
                                    </label>
                                </div>
                                <div class="wpm-row">
                                    <?php wp_editor( stripslashes(wpm_array_get($term_meta, 'letter_1')), 'letter_1', array('textarea_name' => 'term_meta[letter_1]', 'textarea_rows' => '10')); ?>
                                </div>
                                <div class="wpm-help-wrap">
                                    <p>
                                        <span class="code-string">[user_name]</span> - <?php _e('имя пользователя', 'mbl_admin'); ?> <br>
                                        <span class="code-string">[user_login]</span> - <?php _e('логин пользователя', 'mbl_admin'); ?> <br>
                                        <span class="code-string">[start_page]</span> - <?php _e('страница входа', 'mbl_admin'); ?> <br>
                                        <?php wpm_auto_login_shortcodes_tips() ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div id="tab-2" class="tab">
                            <div class="wpm-tab-content">
                                <div class="wpm-row">
                                    <label class="schedule-days-wrap">
                                        <?php _e('Отправить письмо за', 'mbl_admin'); ?> <input type="number" min="1" size="2" maxlength="2" name="term_meta[letter_2_days]" value="<?= wpm_array_get($term_meta, 'letter_2_days') ?>">  <?php _e(' дней до окончания срока', 'mbl_admin'); ?>
                                    </label>
                                </div>
                                <div class="wpm-row">
                                    <label>
                                        <input name="term_meta[letter_2_title]" type="text" class="large-text" value="<?= wpm_array_get($term_meta, 'letter_2_title') ?>" placeholder="<?php _e('Заголовок письма', 'mbl_admin'); ?>">
                                    </label>
                                </div>
                                <div class="wpm-row">
                                    <?php wp_editor( stripslashes(wpm_array_get($term_meta, 'letter_2')), 'letter_2', array('textarea_name' => 'term_meta[letter_2]', 'textarea_rows' => '20')); ?>
                                </div>
                                <div class="wpm-help-wrap">
                                    <p>
                                        <span class="code-string">[user_name]</span> - <?php _e('имя пользователя', 'mbl_admin'); ?> <br>
                                        <span class="code-string">[user_login]</span> - <?php _e('логин пользователя', 'mbl_admin'); ?> <br>
                                        <span class="code-string">[start_page]</span> - <?php _e('страница входа', 'mbl_admin'); ?> <br>
                                        <?php wpm_auto_login_shortcodes_tips() ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div id="tab-3" class="tab">
                            <div class="wpm-tab-content">
                                <div class="wpm-row">
                                    <label class="schedule-days-wrap">
                                        <?php _e('Отправить письмо за', 'mbl_admin'); ?> <input type="number" min="1" size="2" maxlength="2" name="term_meta[letter_3_days]" value="<?= wpm_array_get($term_meta, 'letter_3_days') ?>">  <?php _e(' дней до окончания срока', 'mbl_admin'); ?>
                                    </label>
                                </div>
                                <div class="wpm-row">
                                    <label>
                                        <input name="term_meta[letter_3_title]" type="text" class="large-text" value="<?= wpm_array_get($term_meta, 'letter_3_title') ?>" placeholder="<?php _e('Заголовок письма', 'mbl_admin'); ?>">
                                    </label>
                                </div>
                                <div class="wpm-row">
                                    <?php wp_editor( stripslashes(wpm_array_get($term_meta, 'letter_3')), 'letter_3', array('textarea_name' => 'term_meta[letter_3]', 'textarea_rows' => '20')); ?>
                                </div>
                                <div class="wpm-help-wrap">
                                    <p>
                                        <span class="code-string">[user_name]</span> - <?php _e('имя пользователя', 'mbl_admin'); ?> <br>
                                        <span class="code-string">[user_login]</span> - <?php _e('логин пользователя', 'mbl_admin'); ?> <br>
                                        <span class="code-string">[start_page]</span> - <?php _e('страница входа', 'mbl_admin'); ?> <br>
                                        <?php wpm_auto_login_shortcodes_tips() ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div id="tab-4" class="tab">
                            <div class="wpm-tab-content">
                                <br>
                                <div class="wpm-tabs wpm-inner-tabs">
                                    <ul class="wpm-inner-tabs-nav">
                                        <li><a href="#tab-1"><?php _e('Регистрация новых пользователей', 'mbl_admin'); ?></a></li>
                                        <li><a href="#tab-2"><?php _e('Добавление ключей', 'mbl_admin'); ?></a></li>
                                    </ul>
                                    <div id="tab-1" class="tab">
                                        <div class="wpm-tab-content">
                                            <div class="wpm-row">
                                                <label>
                                                    <input name="term_meta[mass_users_title]" type="text" class="large-text" value="<?php echo wpm_array_get($term_meta, 'mass_users_title', '') !== '' ? wpm_array_get($term_meta, 'mass_users_title') : $defaultTermMeta['mass_users_title'] ?>" placeholder="<?php _e('Заголовок письма', 'mbl_admin'); ?>">
                                                </label>
                                            </div>
                                            <div class="wpm-row">
                                                <?php wp_editor( stripslashes(wpm_array_get($term_meta, 'mass_users_message', '') !== '' ? wpm_array_get($term_meta, 'mass_users_message') : $defaultTermMeta['mass_users_message']), 'mass_users_message', array('textarea_name' => 'term_meta[mass_users_message]', 'textarea_rows' => '20')); ?>
                                            </div>
                                        </div>
                                        <div class="wpm-help-wrap">
                                            <p>
                                                <span class="code-string">[user_login]</span> - <?php _e('логин пользователя', 'mbl_admin'); ?> <br>
                                                <span class="code-string">[user_pass]</span> - <?php _e('пароль пользователя', 'mbl_admin'); ?> <br>
                                                <span class="code-string">[start_page]</span> - <?php _e('страница входа', 'mbl_admin'); ?> <br>
                                                <span class="code-string">[term_name]</span> - <?php _e('название уровня доступа', 'mbl_admin'); ?> <br>
                                                <?php wpm_auto_login_shortcodes_tips() ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div id="tab-2" class="tab">
                                        <div class="wpm-tab-content">
                                            <div class="wpm-row">
                                                <label>
                                                    <input name="term_meta[mass_keys_title]" type="text" class="large-text" value="<?php echo wpm_array_get($term_meta, 'mass_keys_title', '') !== '' ? wpm_array_get($term_meta, 'mass_keys_title') : $defaultTermMeta['mass_keys_title'] ?>" placeholder="<?php esc_attr_e('Заголовок письма', 'mbl_admin'); ?>">
                                                </label>
                                            </div>
                                            <div class="wpm-row">
                                                <?php wp_editor( stripslashes(wpm_array_get($term_meta, 'mass_keys_message', '') !== '' ? wpm_array_get($term_meta, 'mass_keys_message') : $defaultTermMeta['mass_keys_message']), 'mass_keys_message', array('textarea_name' => 'term_meta[mass_keys_message]', 'textarea_rows' => '20')); ?>
                                            </div>
                                        </div>
                                        <div class="wpm-help-wrap">
                                            <p>
                                                <span class="code-string">[user_name]</span> - <?php _e('имя пользователя', 'mbl_admin'); ?> <br>
                                                <span class="code-string">[user_login]</span> - <?php _e('логин пользователя', 'mbl_admin'); ?> <br>
                                                <span class="code-string">[start_page]</span> - <?php _e('страница входа', 'mbl_admin'); ?> <br>
                                                <span class="code-string">[term_name]</span> - <?php _e('название уровня доступа', 'mbl_admin'); ?> <br>
                                                <?php wpm_auto_login_shortcodes_tips() ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </td>

    </tr>


    <?php if (wpm_levels_show_subscriptions()) : ?>
    <tr>
        <th><label for="wpm_keys_new"><?php _e('Автоподписки', 'mbl_admin'); ?></label></th>
        <td>
            <div class="wpm-tabs-wrap postbox wpm-ui-wrap">
                <div class="wpm-inner-wrap mbl-color-content">
                    <div class="wpm-tabs wpm-inner-tabs">
                        <ul class="wpm-inner-tabs-nav">
                            <li>
                                <a href="#wpm_subscr_tab_1">
                                    <?php
                                    if (array_key_exists('active', wpm_get_option('auto_subscriptions')['justclick'])) { ?>
                                        <?php _e('JustClick', 'wpm'); ?>
                                    <?php } else if (array_key_exists('active', wpm_get_option('auto_subscriptions')['sendpulse'])){ ?>
                                        <?php _e('SendPulse', 'wpm'); ?>
                                    <?php } else if (array_key_exists('active', wpm_get_option('auto_subscriptions')['autoweb'])){ ?>
                                        <?php _e('АвтоВебОфис', 'wpm'); ?>
                                    <?php } ?>
                                </a>
                            </li>
                            <?php /*
                                    <li><a href="#wpm_subscr_tab_2"><?php _e('SmartResponder', 'wpm'); ?></a></li>
                                    <li><a href="#wpm_subscr_tab_3"><?php _e('GetResponce', 'wpm'); ?></a></li>
                                    <li><a href="#wpm_subscr_tab_4"><?php _e('UniSender', 'wpm'); ?></a></li>
                                */ ?>
                        </ul>
                        <div id="wpm_subscr_tab_1" class="tab">
                            <?php if (array_key_exists('active', wpm_get_option('auto_subscriptions')['justclick'])) { ?>

                                <?php $justclickRid =  wpm_array_get($term_meta, 'auto_subscriptions.justclick.rid'); ?>
                                <?php $justclickDelRid =  wpm_array_get($term_meta, 'auto_subscriptions.justclick.del_rid'); echo $justclickDelRid;?>
                                <div>
                                    <div class="justclick_subscribe">
                                        <input type="hidden" name="term_meta[auto_subscriptions][justclick][rid]" value="">
                                        <label>
                                            <input
                                                    type="checkbox"
                                                    class="justclick_subscribe_trigger"
                                                <?php echo $justclickRid ? 'checked="checked"' : ''; ?>>
                                            <?php _e('Подписать', 'mbl_admin'); ?>
                                        </label>
                                        <div class="justclick_subscribe_holder" <?php echo !$justclickRid ? 'style="display:none;"' : ''; ?>>
                                            <div id="justclick_level_groups"><div class="wpm-inline-loader"></div></div>
                                        </div>
                                    </div>
                                    <div class="justclick_subscribe">
                                        <input type="hidden" name="term_meta[auto_subscriptions][justclick][del_rid]" value="">
                                        <label>
                                            <input
                                                    type="checkbox"
                                                    name="term_meta[auto_subscriptions][justclick][del_rid]"
                                                <?php echo $justclickDelRid == 'all' ? 'checked="checked"' : ''; ?>
                                                    value="all"
                                                <?php echo $justclickDelRid ? 'checked="checked"' : ''; ?>>
                                            <?php _e('Отписать от всех остальных групп', 'mbl_admin'); ?>
                                        </label>
                                    </div>
                                </div>
                            <?php } else if (array_key_exists('active', wpm_get_option('auto_subscriptions')['sendpulse'])){ ?>
                                <?php $sendPulseRid =  wpm_array_get($term_meta, 'auto_subscriptions.sendpulse.rid'); ?>
                                <?php $sendPulseDelRid =  wpm_array_get($term_meta, 'auto_subscriptions.sendpulse.del_rid'); ?>
                                <div>
                                    <div class="sendpulse_subscribe">
                                        <input type="hidden" name="term_meta[auto_subscriptions][sendpulse][rid]" value="">
                                        <label>
                                            <input
                                                    type="checkbox"
                                                    class="sendpulse_subscribe_trigger"
                                                <?php echo $sendPulseRid ? 'checked="checked"' : ''; ?>>
                                            <?php _e('Подписать', 'mbl_admin'); ?>
                                        </label>
                                        <div class="sendpulse_subscribe_holder" <?php echo !$sendPulseRid ? 'style="display:none;"' : ''; ?>>
                                            <div id="sendpulse_level_groups"><div class="wpm-inline-loader"></div></div>
                                        </div>
                                    </div>
                                    <div class="sendpulse_subscribe">
                                        <input type="hidden" name="term_meta[auto_subscriptions][sendpulse][del_rid]" value="">
                                        <label>
                                            <input
                                                    type="checkbox"
                                                    name="term_meta[auto_subscriptions][sendpulse][del_rid]"
                                                <?php echo $sendPulseDelRid == 'all' ? 'checked="checked"' : ''; ?>
                                                    value="all"
                                                <?php echo $sendPulseDelRid ? 'checked="checked"' : ''; ?>>
                                            <?php _e('Отписать от всех остальных адресных книг', 'mbl_admin'); ?>
                                        </label>
                                    </div>
                                </div>
                            <?php } else if (array_key_exists('active', wpm_get_option('auto_subscriptions')['autoweb'])){ ?>
                                <?php $autoWebRid =  wpm_array_get($term_meta, 'auto_subscriptions.autoweb.rid'); ?>
                                <?php $autoWebDelRid =  wpm_array_get($term_meta, 'auto_subscriptions.autoweb.del_rid'); ?>
                                <div>
                                    <div class="autoweb_subscribe">
                                        <input type="hidden" name="term_meta[auto_subscriptions][autoweb][rid]" value="">
                                        <label>
                                            <input
                                                    type="checkbox"
                                                    class="autoweb_subscribe_trigger"
                                                <?php echo $autoWebRid ? 'checked="checked"' : ''; ?>>
                                            <?php _e('Подписать', 'mbl_admin'); ?>
                                        </label>
                                        <div class="autoweb_subscribe_holder" <?php echo !$autoWebRid ? 'style="display:none;"' : ''; ?>>
                                            <div id="autoweb_level_groups"><div class="wpm-inline-loader"></div></div>
                                        </div>
                                    </div>
                                    <div class="autoweb_subscribe">
                                        <input type="hidden" name="term_meta[auto_subscriptions][autoweb][del_rid]" value="">
                                        <label>
                                            <input
                                                    type="checkbox"
                                                    name="term_meta[auto_subscriptions][autoweb][del_rid]"
                                                <?php echo $autoWebDelRid == 'all' ? 'checked="checked"' : ''; ?>
                                                    value="all"
                                                <?php echo $autoWebDelRid ? 'checked="checked"' : ''; ?>>
                                            <?php _e('Отписать от всех остальных групп', 'mbl_admin'); ?>
                                        </label>
                                    </div>
                                </div>
                            <?php } ?>
                            <div class="wpm-tab-footer">
                                <button type="submit"
                                        class="button button-primary wpm-save-options"><?php _e('Сохранить', 'mbl_admin'); ?></button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </td>
    </tr>
<?php endif; ?>


    <tr class="form-field" style="vertical-align: top">
        <th scope="row"><label for="wpm_keys_new"><?php _e('Коды доступа', 'mbl_admin'); ?></label></th>
        <td>
            <div class="wpm-tabs-wrap postbox wpm-ui-wrap">
                <div class="wpm-inner-wrap mbl-color-content">
                    <div class="wpm-tabs wpm-inner-tabs">
                        <ul class="wpm-inner-tabs-nav">
                            <li><a href="#tab-keys-1"><?php _e('Сгенерировать коды доступа', 'mbl_admin'); ?></a></li>
                            <li><a href="#tab-keys-2"><?php _e('Добавить свои', 'mbl_admin'); ?></a></li>
                        </ul>
                        <div id="tab-keys-1" class="tab">
                            <div class="wpm-tab-content">
                                <div class="wpm-row">
                                        <?php _e('количество', 'mbl_admin'); ?>
                                        <input type="number" size="5" min="1" id="count" value="500" maxlength="4"
                                               style="width: 100px"> <?php _e('(шт.)', 'mbl_admin'); ?>, &nbsp;&nbsp;&nbsp;
                                        <?php wpm_render_partial('term-keys-period', 'admin') ?>
                                </div>
                                <div class="wpm-tab-footer">
                                    <button type="button" class="button button-primary add-keys"
                                            data-keys="new"><?php _e('Сгенерировать', 'mbl_admin'); ?></button>

                                    <textarea id="wpm_keys" rows="20" class="large-text" readonly></textarea>
                                </div>
                            </div>
                        </div>
                        <div id="tab-keys-2" class="tab">
                            <div class="wpm-tab-content">
                                <div class="wpm-row">
                                    <?php wpm_render_partial('term-keys-period', 'admin', ['durationId' => 'duration-manual', 'unitsId' => 'units-manual']) ?>
                                    <div class="add-manual-keys-message">

                                    </div>
                                    <p class="description">
                                        <?php _e('Вставьте список кодов доступа (каждый в отдельной строке)', 'mbl_admin'); ?>
                                    </p><br>
                                    <label>
                                        <textarea id="manual-keys" rows="10"></textarea>

                                    </label>

                                    <p class="description">
                                        <?php _e('Разрешено использовать только: A-Z, a-z, А-Я, а-я, 0-9, ! @ # $ % ^ & * ( ) - _ [ ] { } < > ~ + = , . ; : / ? |', 'mbl_admin'); ?>
                                    </p>

                                </div>
                                <div class="wpm-tab-footer">
                                    <button type="button" class="button button-primary add-manual-keys"
                                            data-keys="new"><?php _e('Добавить', 'mbl_admin'); ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div></div>
            <div>
                <table class="wpm-table">
                    <tr>
                        <th><?php _e('Период', 'mbl_admin'); ?></th>
                        <th><?php _e('Всего', 'mbl_admin'); ?></th>
                        <th><?php _e('Использовано', 'mbl_admin'); ?></th>
                        <th><?php _e('Осталось', 'mbl_admin'); ?></th>
                    </tr>

                    <tbody id="keys-list">
                    <?php echo wpm_get_keys_html_list($term_keys, $term_id); ?>
                    </tbody>

                </table>
                <p>
                    <button type="button"
                            class="button button-primary remove-keys" duration="all"><?php _e('Удалить все коды доступа', 'mbl_admin'); ?></button>
                </p>

            </div>
            <div style="display: none">
                <div id="wpm_popup_box">
                    <div class="wpm-top-popup-nav">
                        <button type="button" class="button button-primary wpm-move-keys"><?php _e('Переместить', 'mbl_admin'); ?></button>

                        <span class="message" style="float: left"></span>
                        <button type="button" class="button button-primary wpm-copy-keys" data-clipboard-target="#user-level-keys"><?php _e('Копировать', 'mbl_admin'); ?></button>
                    </div>
                    <div id="wpm-move-keys-tools">
                        <h4><?php _e('Перемещение кодов доступа', 'mbl_admin'); ?></h4>
                        <input type="hidden" name="wpm_move_keys_term_id" value="<?php echo $term_id; ?>">
                        <input type="hidden" name="wpm_move_keys_duration_from">
                        <input type="hidden" name="wpm_move_keys_units_from">
                        <input type="hidden" name="wpm_move_keys_is_unlimited_from">
                        <label>
                            <?php _e('Уровень доступа', 'mbl_admin'); ?>
                            <select name="wpm_move_keys">
                                <?php foreach (get_terms('wpm-levels', array()) as $level) : ?>
                                    <option value="<?php echo $level->term_id; ?>"><?php echo $level->name; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <p>
                            <?php wpm_render_partial('term-keys-period', 'admin', ['durationName' => 'wpm_move_keys_duration', 'unitsName' => 'wpm_move_keys_units', 'isUnlimitedName' => 'wpm_move_keys_is_unlimited']) ?>
                        </p>
                        <button type="button" class="button button-primary wpm-copy-keys-trigger" style="float: right;">
                            <?php _e('Переместить', 'mbl_admin'); ?>
                        </button>
                        <button type="button" class="button button-danger button-cancel wpm-copy-keys-cancel" style="float: right; margin-right: 3px">
                            <?php _e('Отменить', 'mbl_admin'); ?>
                        </button>
                    </div>
                    <pre id="user-level-keys" class="popup-content-wrap">

                    </pre>
                </div>
            </div>

        </td>
    </tr>

    <?php
    include_once('js/wpm-admin-js.php');

}

function wpm_save_levels_fields( $term_id ) {
//
    if ( isset( $_POST['term_meta'] ) ) {

        $term_meta = get_option( "taxonomy_term_$term_id" );
        $cat_keys  = array_keys( $_POST['term_meta'] );
        foreach ($cat_keys as $key){
            if ( isset( $_POST['term_meta'][$key] ) ){
                if($key=='auto_subscriptions') {
                    $term_meta[$key] = wpm_array_clear($_POST['term_meta'][$key]);
                } else {
                    $term_meta[$key] = stripslashes(wp_filter_post_kses(addslashes($_POST['term_meta'][$key])));
                }
            }
        }
        if(isset($_POST['term_meta']['hide_for_no_access']) && $_POST['term_meta']['hide_for_no_access'] == 'hide'){
            $term_meta['hide_for_no_access'] = 'hide';
        }else{
            $term_meta['hide_for_no_access'] = false;
        }
        //save the option array
        update_option( "taxonomy_term_$term_id", $term_meta );
    }
}
add_action( 'edited_wpm-levels', 'wpm_save_levels_fields', 10, 2 );



/**
 *
 */

function wpm_add_manual_user_level_keys()
{
    $result = array(
        'updated' => true,
        'error' => false,
        'message' => '',
        'html' => ''
    );
    $term_id = $_POST['term_id'];
    $duration = $_POST['duration'];
    $units = $_POST['units'];
    $is_unlimited = intval($_POST['is_unlimited'] === 'on');
    $input_keys = isset($_POST['keys']) ? $_POST['keys'] : "";
    $input_keys = str_replace("\r", "", $input_keys);
    $input_keys = str_replace(array(" "), "\n", $input_keys);

    $new_keys = array();

    $input_keys = explode("\n", $input_keys);
    $input_keys = array_filter($input_keys);

    $keys_count = count($input_keys);

    for ($i = 0; $i < count($input_keys); $i++) {
        $new_keys[$i]['key'] = trim($input_keys[$i]);
        $new_keys[$i]['status'] = 'new';
        $new_keys[$i]['duration'] = $duration;
        $new_keys[$i]['units'] = $units;
        $new_keys[$i]['is_unlimited'] = $is_unlimited;
    }

    if (empty($new_keys)) {
        $result['message'] = __('Ошибка! Неверно введенные коды доступа.', 'mbl_admin');
        $result['error'] = true;
    } else {
        $term_keys = wpm_get_term_keys($term_id);

        if (empty($term_keys)) {
            $term_keys = array();
        }

        $term_keys = array_merge($term_keys, $new_keys);
        $result['updated'] = wpm_set_term_keys($term_id, $term_keys);

        $result['message'] = __("Коды доступа добавлены", 'mbl_admin') .  " ({$keys_count} " . __('шт', 'mbl_admin') . ").";
        $result['html'] = wpm_get_keys_html_list($term_keys, $term_id);
    }

    echo json_encode($result);
    die();
}

add_action('wp_ajax_wpm_add_manual_user_level_keys_action', 'wpm_add_manual_user_level_keys'); //

/**
 *
 */

function wpm_add_user_level_keys()
{
    $result = [
        'updated' => 'true',
        'keys'    => '',
        'message' => '',
        'html'    => '',
    ];
    $term_id = $_POST['term_id'];
    $count = $_POST['count'];
    $duration = $_POST['duration'];
    $units = $_POST['units'];
    $is_unlimited = intval($_POST['is_unlimited'] === 'on');

    $term_keys = wpm_get_term_keys($term_id);

    $new_keys = wpm_generate_keys([
        'count'        => $count,
        'duration'     => $duration,
        'date_start'   => '',
        'date_end'     => '',
        'units'        => $units,
        'is_unlimited' => $is_unlimited,
    ]);

    if (empty($term_keys)) {
        $term_keys = [];
    }
    $term_keys = array_merge($term_keys, $new_keys);
    $result['updated'] = wpm_add_term_keys($term_id, $new_keys);

    foreach ($new_keys as $key => $item) {
        if (!empty($result['keys'])) {
            $result['keys'] .= "\n" . $item['key'];
        } else {
            $result['keys'] .= $item['key'];
        }
    }

    $result['html'] = wpm_get_keys_html_list($term_keys, $term_id);

    echo json_encode($result);
    die();
}

add_action('wp_ajax_wpm_add_user_level_keys_action', 'wpm_add_user_level_keys'); //

/**
 *
 */

function wpm_is_units_equal ($key, $cur_units)
{
    $units = array_key_exists('units', $key) ? $key['units'] : 'months';

    return $cur_units==$units ? true : false;
}

function wpm_remove_user_level_keys()
{
    $result = [
        'message' => '',
        'error'   => '',
    ];

    $term_id = intval($_POST['term_id']);
    $duration = $_POST['duration'];
    $units = $_POST['units'];
    $is_unlimited = wpm_array_get($_POST, 'is_unlimited', 0);

    $where = [
        'term_id' => $term_id,
    ];

    if ($duration !== 'all' && $is_unlimited) {
        $where['is_unlimited'] = 1;
    } elseif ($duration !== 'all') {
        $where['duration'] = $duration;
        $where['units'] = $units;
        $where['is_unlimited'] = 0;
    }

    $update = MBLTermKeysQuery::update(['key_type' => 'wpm_keys_basket'], $where);

    $result['error'] = (bool)($update === false);

    echo json_encode($result);
    die();
}

add_action('wp_ajax_wpm_remove_user_level_keys_action', 'wpm_remove_user_level_keys'); //

/**
 *
 */

function wpm_get_keys()
{
    $term_id = intval($_POST['term_id']);
    $duration = $_POST['duration'];
    $units = $_POST['units'];

    $where = [
        'term_id'   => $term_id,
        'status'    => 'new',
        'sent'      => 0,
        'is_banned' => 0,
        'key_type'  => 'wpm_term_keys',
    ];

    if(wpm_array_get($_POST, 'is_unlimited')) {
        $where['is_unlimited'] = 1;
    } else {
        $where['duration'] = $duration;
        $where['units'] = $units;
        $where['is_unlimited'] = 0;
    }

    $keys = '';

    $termKeys = MBLTermKeysQuery::find($where, ['key']);
    if (!empty($termKeys)) {
        $keys = implode("\n", wpm_array_pluck($termKeys, 'key')) . "\n";
    }

    echo $keys;
    die();
}

add_action('wp_ajax_wpm_get_keys_action', 'wpm_get_keys'); //


function wpm_get_used_keys()
{
    $term_id = intval($_POST['term_id']);
    $duration = $_POST['duration'];
    $units = $_POST['units'];

    $where = [
        'term_id'   => $term_id,
        'status'    => 'used',
        'key_type'  => 'wpm_term_keys',
        'is_banned' => 'all',
    ];

    if (wpm_array_get($_POST, 'is_unlimited')) {
        $where['is_unlimited'] = 1;
    } else {
        $where['duration'] = $duration;
        $where['units'] = $units;
        $where['is_unlimited'] = 0;
    }

    $keys = '';

    $termKeys = MBLTermKeysQuery::find($where, ['key', 'user_id']);

    $sentWhere = [
        'term_id'   => $term_id,
        'status'    => 'new',
        'duration'  => $duration,
        'units'     => $units,
        'key_type'  => 'wpm_term_keys',
        'is_banned' => 'all',
        'sent'      => 1,
    ];

    if (wpm_array_get($_POST, 'is_unlimited')) {
        $sentWhere['is_unlimited'] = 1;
    } else {
        $sentWhere['duration'] = $duration;
        $sentWhere['units'] = $units;
        $sentWhere['is_unlimited'] = 0;
    }

    $sentTermKeys = MBLTermKeysQuery::find($sentWhere, ['key', 'user_id']);

    $termKeys = array_merge($termKeys, $sentTermKeys);

    foreach ($termKeys as $key) {
        $keys .= (wpm_array_get($key, 'user_id')
            ? sprintf("<a href='%s' target='_blank'>%s</a>\n", admin_url('/user-edit.php?user_id=' . wpm_array_get($key, 'user_id')), wpm_array_get($key, 'key'))
            : (wpm_array_get($key, 'key') . "\n"));
    }

    echo $keys;
    die();
}

add_action('wp_ajax_wpm_get_used_keys_action', 'wpm_get_used_keys');


/**
 * @param $term_id
 */


function wpm_create_levels_taxonomy_term($term_id)
{
    if (!$term_id) {
        return;
    }

    $term_keys = wpm_generate_keys(array('count' => 500, 'duration' => 12, 'units' => 'months'));

    wpm_set_term_keys($term_id, $term_keys, 'wpm_term_keys', true);
}

add_action('create_wpm-levels', 'wpm_create_levels_taxonomy_term', 10, 2);


/**
 * @param $term_id
 */

function wpm_delete_levels_taxonomy_term($term_id)
{
    if (!$term_id) {
        return;
    }

    delete_option("wpm_term_keys_$term_id");
}

add_action('delete_wpm-levels', 'wpm_delete_levels_taxonomy_term', 10, 2);

/**
 *
 */

function wpm_generate_keys($args)
{
    $count = intval($args['count']);
    $duration = $args['duration'];
    $units = $args['units'];
    $keys_array = array();
    $str = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ' . time();
    for ($i = 0; $i < $count; $i++) {
        $keys_array[$i]['key'] = str_shuffle($str);
        $keys_array[$i]['status'] = 'new';
        $keys_array[$i]['duration'] = $duration;
        $keys_array[$i]['units'] = $units;
        $keys_array[$i]['is_unlimited'] = isset($args['is_unlimited']) ? $args['is_unlimited'] : 0;
    }
    return $keys_array;
}

function wpm_fix_keys($term_id){

    $term_keys = wpm_get_term_keys($term_id);
    if(is_array($term_keys)){
        foreach($term_keys as $key_id => $key){
            $term_keys[$key_id]['key'] = trim($term_keys[$key_id]['key']);
        }
    }
    wpm_set_term_keys($term_id, $term_keys);
}

function wpm_migrate_keys(){

    $term_meta = get_option("wpm_user_level_keys");
    if(!empty($term_meta)){
        foreach($term_meta as $term_id => $term_array){
            wpm_set_term_keys($term_id, $term_array);
        }
    }

}


/**
 *  Migrate keys to separate table
 */

function migrate_keys_0_3_0()
{

    global $wpdb;
    $codes_table = $wpdb->prefix . "memberlux_codes";

    $sql_response_table = "CREATE TABLE IF NOT EXISTS $codes_table (
          id bigint(20) NOT NULL AUTO_INCREMENT,
          created datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
          code longtext NOT NULL,
          user_id bigint(11) NOT NULL,
          status varchar(20) DEFAULT 'new' NOT NULL,
          duration bigint(11) NOT NULL,
          date_start datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
          date_end datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
          UNIQUE KEY id (id)
            )
            DEFAULT CHARACTER SET utf8
            DEFAULT COLLATE utf8_general_ci;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql_response_table);
    add_option("memberlux_db_version", '0.3.0');


    $terms_table = $wpdb->prefix . "term_taxonomy";
    $options_table = $wpdb->prefix . "options";

    $terms = $wpdb->get_results( "SELECT term_taxonomy_id
                FROM $terms_table
                WHERE taxonomy='wpm-levels'", OBJECT );

    $i = 0;
    if(!empty($terms)){
        foreach($terms as $term){
            $option_name = 'wpm_term_keys_'.$term->term_taxonomy_id;
            $options = $wpdb->get_results( "SELECT option_value
                FROM $options_table
                WHERE option_name='$option_name'", OBJECT);
        }
    }

}

add_action( 'wpm_head', 'wpm_category_head_code' );
function wpm_category_head_code()
{
    if ( is_tax( 'wpm-category' ) ) {
        $term_id = get_queried_object_id();
        echo wpm_get_category_meta( $term_id, 'head_code' );
    }
}

add_filter( 'body_class', 'wpm_category_body_class' );
function wpm_category_body_class( $classes )
{
    if ( is_tax( 'wpm-category' ) && !wpm_category_meta_empty( get_queried_object_id(), 'head_code' ) ) {
        $classes[] = 'custom-super-style';
    }
    return $classes;
}
