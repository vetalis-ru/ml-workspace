<?php


function wpm_page_extra()
{
    global $post;
    // Noncename needed to verify where the data originated
    echo '<input type="hidden" name="wpm_page_noncename" id="wpm_page_noncename" value="' . wp_create_nonce(plugin_basename(__FILE__)) . '" />';

    include_once('js/wpm-admin-js.php');

    //add_filter('tiny_mce_before_init', 'wpm_footer_tinymce_config', 9999);

    if (version_compare(get_bloginfo('version'), '3.9', '>=')) {
        $wpm_tinymce_options = array(
            'quicktags'     => true,
            'media_buttons' => true,
            'editor_height' => 300,
            'textarea_name' => 'page_meta[homework_description]',
            'editor_class'  => 'large-text',
            'tinymce'       => array(
                'toolbar1'          => 'bold italic underline strikethrough | forecolor backcolor | justifyleft justifycenter justifyright | bullist numlist outdent indent |removeformat | link unlink hr',
                'toolbar2'          => false,
                'toolbar3'          => false,
                'forced_root_block' => 'p',
                'force_br_newlines' => false,
                'force_p_newlines'  => true,
                'remove_linebreaks' => true,
                'wpautop'           => true,
                'content_css_force' => ( plugins_url() . '/member-luxe/css/editor-style.css?' . time()
                                                    .', ' . plugins_url() . '/member-luxe/templates/base/bootstrap/css/bootstrap.min.css'
                                                    .', ' . plugins_url() . '/member-luxe/css/editor-style-wpm-homework.css?' . time()
                                                    .', ' . plugins_url() . '/member-luxe/css/bullets.css'
                                                )
            )
        );

    } else {
        $wpm_tinymce_options = array(
            'media_buttons'     => true,
            'teeny'             => false,
            'quicktags'         => true,
            'textarea_rows'     => 60,
            'textarea_name'     => 'page_meta[homework_description]',
            'editor_class'      => 'large-text',
            'content_css'       => '',
            'tinymce'           => array(
                'theme_advanced_buttons1'   => 'bold,italic,underline,strikethrough,|,forecolor,backcolor,|,justifyleft,justifycenter,justifyright,|,bullist,numlist,outdent,indent,|,removeformat,|,link,unlink,hr',
                'theme_advanced_buttons2'   => '',
                'theme_advanced_buttons3'   => '',
                'theme_advanced_buttons4'   => '',
                'theme_advanced_font_sizes' => '10px,11px,12px,13px,14px,15px,16px,17px,18px,19px,20px,21px,22px,23px,24px,25px,26px,27px,28px,29px,30px,32px,42px,48px,52px',
                'forced_root_block'         => 'p',
                'wpautop'                   => true,
                'force_br_newlines'         => false,
                'force_p_newlines'          => true,
                'remove_linebreaks'         => true,
                'content_css_force'         => ( plugins_url() . '/member-luxe/css/editor-style.css?' . time()
                                                    .', ' . plugins_url() . '/member-luxe/templates/base/bootstrap/css/bootstrap.min.css'
                                                    .', ' . plugins_url() . '/member-luxe/css/editor-style-wpm-homework.css?' . time()
                                                    .', ' . plugins_url() . '/member-luxe/css/bullets.css'
                                                )
            )
        );

    }
    //=====

    $page_meta = get_post_meta(get_the_ID(), '_wpm_page_meta', true);

    if(empty($page_meta)){
        $page_meta = array(
            /* Cyber.Paw Edition Start */
            'startday' => '', // переменная старт
            'stopday' => '',   // переменная стоп
            /* Cyber.Paw Edition End */
            'description' => '',
            'shift_is_on' => false,
            'is_homework' => false,
            'shift_value' => 0,
            'confirmation_method' => 'auto',
            'homework_shift_value' => 0,
            'homework_description' => '',
            'subscription' => array(
//                'getresponse' => '',
//                'mailchimp' => '',
//                'unisender' => '',
//                'smartresponder' => '',
                'justclick' => '',
            ),
            'interkassa' => array(
                'name' => '',
                'desc' => '',
                'price' => '',
                'currency' => '',
                'id' => '',
                'fields' => array(
                    'Ф.И.О',
                    'Страна',
                    'Город',
                    'Адрес',
                    'Индекс',
                    'Телефон',
                    'E-mail'
                ),
                'show_fields' => array(
                    'Ф.И.О'
                )
            ),
            'comments' => array(
                'show' => true,
                'layout_list' => array(
                    'Закладки',
                    'Друг под другом',
                    'Колонки'
                ),
                'layout' => 'Закладки',
                'comments' => array(
                    'Wordpress',
                    'Facebook',
                    'VKontakte'
                ),
                'comments_to_show' => array(
                    'Wordpress',
                    'Facebook',
                    'VKontakte'
                ),
                'order' => array(
                    'Wordpress',
                    'Facebook',
                    'VKontakte'
                )
            ),
            'code' => array(
                'head' => '',
                'body' => '',
                'footer' => ''
            ),
            'feedback' => array(
                'title' => __('Обратная связь', 'mbl_admin'),
                'email' => '',
                'href' => '#wpm_contact_form',
                'fields' => array(
                    '0' => '',
                    '1' => '',
                    '2' => ''
                ),
                'show' => array(
                    '0',
                    '1',
                    '2'
                ),
                'message' => '',
                'show_message' => false
            ),
            'homework' => array(
                'visible' => '',
                'required' => '',
                'content' => '',
                'checking_type' => '',
                'type_list' => array(
                    'manual',
                    'auto',
                    'semi-auto')
                )
        );
    }
    update_post_meta(get_the_ID(), '_wpm_page_meta', $page_meta);

    ?>

    <script type="text/javascript">




        // Uploading files
        var wpm_file_frame;

        jQuery(function ($) {
            function countShortDescription(text) {
                var maxLength = Number('<?php echo apply_filters('mbl_short_description_maxlength', false) ;?>');
                var currentLength = text ? text.length : 0;

                $('#mbl_short_description_counter')
                    .text(currentLength + ' <?php _e('Символов из', 'mbl_admin'); ?> ' + maxLength)
                    .css('color', currentLength > maxLength ? 'red' : 'inherit');
            }

            $('[name="mbl_short_description"][maxlength]').on('keyup', function (event) {
                countShortDescription(event.target.value);
            });

            countShortDescription($('[name="mbl_short_description"][maxlength]').val());

            $(document).on('change', '.homework-checking-type', function (){
                if($(this).val() == 'auto'){
                    $('.auto-settings').show();
                    $('.semi-auto-settings').hide();
                }
                if($(this).val() == 'semi-auto'){
                    $('.auto-settings').hide();
                    $('.semi-auto-settings').show();
                }
                if($(this).val() == 'manual'){
                    $('.auto-settings').hide();
                    $('.semi-auto-settings').hide();
                }

            });

            $('input[data-minutes]')
                .on('keyup', function () {
                    parseMinutes($(this));
                })
                .on('change', function () {
                    parseMinutes($(this));
                });

            function parseMinutes($elem) {
                var value = parseInt($elem.val());

                if(!isNaN(value)) {
                    $elem.val(Math.min(value, 59));
                } else {
                    $elem.val(0);
                }
            }

            function pasteInteger($elem) {
                var value = parseInt($elem.val());

                if(!isNaN(value)) {
                    $elem.val(value);
                } else {
                    $elem.val(0);
                }
            }

            $('input[data-hours],input[data-days]')
                .on('keyup', function () {
                    pasteInteger($(this));
                })
                .on('change', function () {
                    pasteInteger($(this));
                });

            $('#wpm-sortable-comments-1').sortable();

            // Tabs
            $(".wpm-tabs").tabs({
                autoHeight: false,
                collapsible: false,
                fx: {
                    opacity: 'toggle',
                    duration: 'fast'
                },
                activate: function (e, ui) {
                    $.cookie('selected-tab', ui.newTab.index(), { path: '/' });
                },
                active: $.cookie('selected-tab')
            }).addClass('ui-tabs-vertical ui-helper-clearfix');

            $('.wpm-inner-tabs').tabs({
                collapsible: false,
                fx: {
                    opacity: 'toggle',
                    duration: 'fast'
                }
            });


            <?php if(!empty($wpm_head_image)){ ?>
            $('#delete-wpm-head-image').show();
            <?php } ?>
            $(document).on('click', '.upload_wpm_head_image_button', function (event) {
                event.preventDefault();

                // If the media frame already exists, reopen it.
                if (wpm_file_frame) {
                    wpm_file_frame.open();
                    return;
                }

                // Create the media frame.
                wpm_file_frame = wp.media.frames.downloadable_file = wp.media({
                    title: '<?php _e('Выберите файл', 'mbl_admin'); ?>',
                    button: {
                        text: '<?php _e('Использовать изображение', 'mbl_admin'); ?>'
                    },
                    multiple: false
                });

                // When an image is selected, run a callback.
                wpm_file_frame.on('select', function () {
                    var attachment = wpm_file_frame.state().get('selection').first().toJSON();
                    // console.log(attachment);
                    $('input[name=wpm_head_image]').val(attachment.url);
                    $('input[name=wpm_head_image_id]').val(attachment.id);
                    $('#wpm-head-image-preview').attr('src', attachment.url);
                    $('#delete-wpm-head-image').show();
                    $('.wpm-head-image-preview-box').show();

                });

                // Finally, open the modal.
                wpm_file_frame.open();
            });
            $(document).on('click', '#delete-wpm-head-image', function () {
                $('input[name=wpm_head_image]').val('');
                $('input[name=wpm_head_image_id]').val('');
                $('#delete-wpm-head-image').hide();
                $('.wpm-head-image-preview-box').hide();
            });

            /**/
            $('.wpm-code').click(function () {
                $(this).select();
            });

            $(document).on('submit', 'form#post', function(){
                var $textarea = $('#page_meta_homework_description');
                $textarea.val($textarea.summernote('code'));
            });
        });
    </script>
    <div class="wpm_box" style="position:static;">
    <div class="options-wrap wpm-ui-wrap">
    <div class="wpm-tabs wpm-tabs-vertical ui-tabs" id="wpm-options-tabs">
    <ul class="ui-tabs-nav tabs-nav">
        <li><a href="#wpm_tab_1"><?php _e('Краткое описание', 'mbl_admin'); ?></a></li>
        <li><a href="#wpm_tab_3"><?php _e('Подписки', 'mbl_admin'); ?></a></li>
        <li><a href="#wpm_tab_7"><?php _e('Скрипты', 'mbl_admin'); ?></a></li>
        <li><a href="#wpm_tab_10"><?php _e('Автотренинг', 'mbl_admin'); ?></a></li>
        <?php if (wpm_is_interface_2_0()) : ?>
            <li><a href="#wpm_tab_11"><?php _e('Вложения', 'mbl_admin'); ?></a></li>
            <li><a href="#wpm_tab_12"><?php _e('Фоновое изображение', 'mbl_admin'); ?></a></li>
            <li><a href="#wpm_tab_13"><?php _e('Индикаторы', 'mbl_admin'); ?></a></li>
            <li><a href="#wpm_tab_14"><?php _e('Настройки', 'mbl_admin'); ?></a></li>
        <?php endif; ?>
    </ul>
    <div class="tab" id="wpm_tab_1">

        <div class="wpm-tab-content">
            <?php if (!wpm_is_interface_2_0()) : ?>
                <textarea class="large-text" name="page_meta[description]" rows="10"><?php echo $page_meta['description']; ?></textarea>
            <?php else : ?>
                <textarea class="large-text" name="mbl_short_description" rows="10" id="mbl_short_description"
                          <?php echo (apply_filters('mbl_short_description_maxlength', false) ? 'maxlength="' . apply_filters('mbl_short_description_maxlength', 0) . '"' : ''); ?>><?php echo get_post_meta(get_the_ID(), 'mbl_short_description', true); ?></textarea>
                    <?php if (apply_filters('mbl_short_description_maxlength', false)) : ?>
                        <span id="mbl_short_description_counter"></span>
                    <?php endif; ?>
                <br style="clear: both;">
            <?php endif; ?>
            <div class="wpm-row bottom-row">
                <input name="save" type="submit" class="button-primary" tabindex="5" accesskey="p" value="<?php _e('Обновить', 'mbl_admin'); ?>">
            </div>
        </div>
    </div>
    <div class="tab" id="wpm_tab_3">
        <div class="wpm-tab-content">
            <div class="wpm-inner-tabs">
                <ul class="wpm-inner-tabs-nav">
                    <?php /*
                        <li><a href="#wpm_inner_tab_3_1"><?php _e('GetResponse', 'wpm'); ?></a></li>
                        <li><a href="#wpm_inner_tab_3_2"><?php _e('MailChimp', 'wpm'); ?></a></li>
                        <li><a href="#wpm_inner_tab_3_3"><?php _e('UniSender', 'wpm'); ?></a></li>
                        <li><a href="#wpm_inner_tab_3_4"><?php _e('SmartResponder', 'wpm'); ?></a></li>
                    */ ?>
                    <li><a href="#wpm_inner_tab_3_5"><?php _e('JustClick', 'wpm'); ?></a></li>
                </ul>
                <?php /*
                    <div class="wpm-inner-tab-content" id="wpm_inner_tab_3_1">
                        <div class="section">
                            <label for="wpp_getresponse"><?php _e('Код формы', 'wpm'); ?></label>
                            <textarea name="page_meta[subscription][getresponse]" id="wpp_getresponse" class="wpp_textarea large-text" rows="10"><?php echo $page_meta['subscription']['getresponse']; ?></textarea>
                        </div>
                        <div class="wpm-helper-box"><a
                                onclick="wpm_open_help_win('http://www.youtube.com/watch?v=546KJehwzzw&list=PLI8Gq0WzVWvJ60avoe8rMyfoV5qZr3Atm&index=5')"><?php _e('Видео урок', 'wpm'); ?></a>
                        </div>
                    </div>
                    <div class="wpm-inner-tab-content" id="wpm_inner_tab_3_2">
                        <div class="section">
                            <label for="wpp_mailchimp_code"><?php _e('Код формы', 'wpm'); ?></label>
                            <textarea name="page_meta[subscription][mailchimp]" id="wpp_mailchimp_code" class="wpp_textarea large-text" rows="10"><?php echo $page_meta['subscription']['mailchimp']; ?></textarea>
                        </div>
                        <div class="wpm-helper-box"><a
                                onclick="wpm_open_help_win('http://www.youtube.com/watch?v=546KJehwzzw&list=PLI8Gq0WzVWvJ60avoe8rMyfoV5qZr3Atm&index=5')"><?php _e('Видео урок', 'wpm'); ?></a>
                        </div>
                    </div>
                    <div class="wpm-inner-tab-content" id="wpm_inner_tab_3_3">
                        <div class="section">
                            <label for="wpp_unisender_code"><?php _e('Код формы', 'wpm'); ?></label>
                            <textarea name="page_meta[subscription][unisender]" id="wpp_unisender_code"
                                      class="wpp_textarea large-text" rows="10"><?php echo $page_meta['subscription']['unisender']; ?></textarea>
                        </div>
                        <div class="wpm-helper-box"><a
                                onclick="wpm_open_help_win('http://www.youtube.com/watch?v=546KJehwzzw&list=PLI8Gq0WzVWvJ60avoe8rMyfoV5qZr3Atm&index=5')"><?php _e('Видео урок', 'wpm'); ?></a>
                        </div>
                    </div>
                    <div class="wpm-inner-tab-content" id="wpm_inner_tab_3_4">
                        <div class="section">
                            <label for="wpp_smartresponder_code"><?php _e('Код формы', 'wpm'); ?></label>
                            <textarea name="page_meta[subscription][smartresponder]" id="wpp_smartresponder_code"
                                      class="wpp_textarea large-text" rows="10"><?php echo $page_meta['subscription']['smartresponder']; ?></textarea>
                        </div>
                        <div class="wpm-helper-box"><a
                                onclick="wpm_open_help_win('http://www.youtube.com/watch?v=LI5TqWaH-qg&feature=youtu.be')"><?php _e('Видео урок', 'wpm'); ?></a>
                        </div>
                    </div>
                */ ?>
                <div class="wpm-inner-tab-content" id="wpm_inner_tab_3_5">
                    <div class="section">
                        <label for="wpp_unisender_code"><?php _e('Код формы', 'mbl_admin'); ?></label>
                        <textarea name="page_meta[subscription][justclick]" id="wpp_justclick_code"
                                  class="wpp_textarea large-text" rows="10"><?php echo $page_meta['subscription']['justclick']; ?></textarea>
                    </div>
                    <div class="wpm-helper-box"><a
                            onclick="wpm_open_help_win('http://www.youtube.com/watch?v=LI5TqWaH-qg&feature=youtu.be')"><?php _e('Видео урок', 'mbl_admin'); ?></a>
                    </div>
                </div>
            </div>
            <div class="wpm-row bottom-row">
                <input name="save" type="submit" class="button-primary" tabindex="5" accesskey="p" value="<?php _e('Обновить', 'mbl_admin'); ?>">
            </div>
        </div>
    </div>

    <div class="tab" id="wpm_tab_7">

        <div class="wpm-tab-content">
            <div class="wpp_top_nav_bar">

            </div>
            <div class="section ">
                <label>&lt;head&gt;&nbsp; <span class="text_green"><?php _e('ваш код', 'mbl_admin'); ?></span> &lt;/head&gt;</label><br>
                <textarea name="page_meta[code][head]" id="wpm_head_code"
                          class="wpp_textarea large-text"><?php echo $page_meta['code']['head'];
                    ?></textarea>
                <label>&lt;body <span class="text_green"><?php _e('ваш код', 'mbl_admin'); ?></span> &gt;&nbsp;&lt;/body&gt;</label><br>
                <textarea name="page_meta[code][body]" id="wpm_body_code"
                          class="wpp_textarea large-text"><?php echo $page_meta['code']['body'];
                    ?></textarea>
                <label>&lt;body&gt;&nbsp; <span class="text_green"><?php _e('ваш код', 'mbl_admin'); ?></span> &lt;/body&gt;</label><br>
                <textarea name="page_meta[code][footer]" id="wpm_footer_code"
                          class="wpp_textarea large-text"><?php echo $page_meta['code']['footer'];
                    ?></textarea>
            </div>
            <div class="wpm-helper-box"><a
                    onclick="wpm_open_help_win('http://www.youtube.com/watch?v=_kTPYCTPGYA&list=PLI8Gq0WzVWvJ60avoe8rMyfoV5qZr3Atm&index=18')"><?php _e('Видео урок', 'mbl_admin'); ?></a>
            </div>
            <div class="wpm-row bottom-row">
                <input name="save" type="submit" class="button-primary" tabindex="5" accesskey="p" value="<?php _e('Обновить', 'mbl_admin'); ?>">
            </div>
        </div>
    </div>

    <div class="tab" id="wpm_tab_10">

        <div class="wpm-tab-content" style="overflow: visible;">
            <div class="wpm-row overflow-visible">
                <label>
                    <input type="checkbox"
                           name="page_meta[shift_is_on]"
                           id="shift_is_on"
                        <?php echo $page_meta['shift_is_on'] ? 'checked' : '';?>
                    ><?php _e('Смещение', 'mbl_admin'); ?>
                </label>
                <div id="shift_value_label" class="<?php echo $page_meta['shift_is_on'] ? '' : 'hidden';?>">
                    <?php wpm_render_partial('shift/config', 'admin', array('page_meta' => $page_meta, 'key' => 'shift_value')) ?>
                </div>
            </div>

            <div class="wpm-row overflow-visible">
                <label>
                    <input type="checkbox" name="page_meta[is_homework]" id="is_homework" <?php echo $page_meta['is_homework'] ? 'checked' : '';?>><?php _e('Домашнее задание','mbl_admin');?>
                </label>
                <div id="homework_options" class="<?php echo $page_meta['is_homework'] ? '' : 'invisible';?>">
                    <dl>
                        <?php do_action('mbl_admin_hw_after', wpm_array_get($page_meta, 'homework_type')); ?>
                        <dt><?php _e('Выберите способ подтверждения:','mbl_admin');?></dt>
                        <dd>
                            <label for="confirmation_method_auto">
                                <input type="radio" name="page_meta[confirmation_method]" id="confirmation_method_auto" value="auto" <?php echo ($page_meta['confirmation_method']=='auto' || !$page_meta['confirmation_method']) ? 'checked' : '';?> />
                                <?php _e('Автоматически', 'mbl_admin'); ?>
                                <i class="fa fa-cogs" aria-hidden="true"></i>
                            </label>
                        </dd>
                        <dd>
                            <label for="confirmation_method_manually">
                                <input type="radio" name="page_meta[confirmation_method]" id="confirmation_method_manually" value="manually" <?php echo $page_meta['confirmation_method']=='manually' ? 'checked' : '';?> />
                                <?php _e('Вручную', 'mbl_admin'); ?>
                                <i class="fa fa-hand-pointer-o" aria-hidden="true"></i>
                            </label>
                        </dd>
                        <dd>
                            <label for="confirmation_method_auto_with_shift">
                                <input type="radio"
                                       name="page_meta[confirmation_method]"
                                       id="confirmation_method_auto_with_shift"
                                       value="auto_with_shift"
                                       <?php echo $page_meta['confirmation_method']=='auto_with_shift' ? 'checked' : '';?>
                                    />
                                <?php _e('Автоматически со смещением', 'mbl_admin'); ?>
                                <i class="fa fa-cogs" aria-hidden="true"></i>
                                <i class="fa fa-hourglass-half" aria-hidden="true"></i>
                            </label>
                            <div id="homework_shift_value_label" class="<?php echo $page_meta['confirmation_method']=='auto_with_shift' ? '' : 'disabled_field';?>">
                                <?php wpm_render_partial('shift/config', 'admin', array('page_meta' => $page_meta, 'key' => 'homework_shift_value')) ?>
                            </div>
                        </dd>
                        <?php do_action('mbl_admin_hw_test', $page_meta); ?>
                        <dt><br /><?php _e('Описание задания:','mbl_admin');?></dt>
                        <dt>
                            <?php wpm_editor_admin($page_meta['homework_description'], 'page_meta_homework_description', array(), false, 'page_meta[homework_description]'); ?>
                        </dt>
                    </dl>
                </div>

            </div>

            <div class="wpm-row bottom-row">
                <input name="save" type="submit" class="button-primary" tabindex="10" accesskey="p" value="<?php _e('Обновить', 'mbl_admin'); ?>">
            </div>
        </div>
    </div>

    <?php if (wpm_is_interface_2_0()) : ?>
        <div class="tab" id="wpm_tab_11">

            <div class="wpm-tab-content">
                <div class="wpm-row" style="min-height: 150px">
                    <?php wpm_render_partial('file-upload', 'admin', array('id' => get_the_ID(), 'name' => 'wpm_material')); ?>
                </div>

                <div class="wpm-row bottom-row">
                    <input name="save" type="submit" class="button-primary" tabindex="10" accesskey="p" value="<?php _e('Обновить', 'mbl_admin'); ?>">
                </div>
            </div>
        </div>
        <div class="tab" id="wpm_tab_12">

            <div class="wpm-tab-content">
                <div class="wpm-row" style="min-height: 150px">
                    <?php wpm_render_partial('material-bg', 'admin', compact('page_meta')); ?>
                </div>

                <div class="wpm-row bottom-row">
                    <input name="save" type="submit" class="button-primary" tabindex="10" accesskey="p" value="<?php _e('Обновить', 'mbl_admin'); ?>">
                </div>
            </div>
        </div>
        <div class="tab" id="wpm_tab_13">

            <div class="wpm-tab-content">
                <?php wpm_render_partial('material-indicators', 'admin', compact('page_meta')); ?>

                <div class="wpm-row bottom-row">
                    <input name="save" type="submit" class="button-primary" tabindex="10" accesskey="p" value="<?php _e('Обновить', 'mbl_admin'); ?>">
                </div>
            </div>
        </div>
        <div class="tab" id="wpm_tab_14">
            <div class="wpm-tab-content">
                <div class="wpm-row">
                    <label>
                        <input type="radio"
                               name="page_meta[content_width]"
                               id="content_width_default"
                               value=""
			                <?php checked($page_meta['content_width'] ?? '', '');?>
                        ><?php _e('Ширина по умолчанию', 'mbl_admin'); ?>
                    </label>
                </div>
                <div class="wpm-row">
                    <label>
                        <input type="radio"
                               name="page_meta[content_width]"
                               id="content_width_800"
                               value="fixed"
			                <?php checked($page_meta['content_width'] ?? '', 'fixed');?>
                        ><?php _e('Ширина контента 820px', 'mbl_admin'); ?>
                    </label>
                </div>
                <div class="wpm-row">
                    <label>
                        <input type="radio"
                               name="page_meta[content_width]"
                               id="content_width_wide"
                               value="wide"
			                <?php checked($page_meta['content_width'] ?? '', 'wide');?>
                        ><?php _e('Ширина контента 100%', 'mbl_admin'); ?>
                    </label>
                </div>
                <hr>
                <div class="wpm-row">
                    <label for="page_redirect">
                        <?php _e('Введите URL для перенаправления', 'mbl_admin'); ?>
                    </label>
                    <div>
                        <input type="text" name="page_meta[redirect_page]" id="page_redirect"
                               style="width: 99%"
                               value="<?= $page_meta['redirect_page'] ?? '' ?>">
                    </div>
                </div>
                <div class="wpm-row">
                    <input type="hidden" name="page_meta[redirect_page_blank]" value="0">
                    <label>
                        <input type="checkbox"
                               name="page_meta[redirect_page_blank]"
                               id="content_width_wide"
                               value="1"
	                        <?php checked($page_meta['redirect_page_blank'] ?? '0', '1') ?>
                        ><?php _e('Открывать в новой вкладке', 'mbl_admin'); ?>
                    </label>
                </div>
                <div class="wpm-row">
                    <input type="hidden" name="page_meta[redirect_page_on]" value="0">
                    <label>
                        <input type="checkbox"
                               name="page_meta[redirect_page_on]"
                               id="content_width_wide"
                               value="1"
	                        <?php checked($page_meta['redirect_page_on'] ?? '0', '1') ?>
                        ><?php _e('Включить перенаправление', 'mbl_admin'); ?>
                    </label>
                </div>
                <div class="wpm-row bottom-row">
                    <input name="save" type="submit" class="button-primary" tabindex="10" accesskey="p" value="<?php _e('Обновить', 'mbl_admin'); ?>">
                </div>
            </div>
        </div>
    <?php endif; ?>
    </div>
    </div>
    <div class="wpm_clear"></div>
    </div>
<?php
}

function wpm_page_content_types_metabox()
{
    global $post;

    $mblPage = new MBLPage($post);

    wpm_render_partial('content-types-metabox', 'admin', compact('mblPage'));
}

add_action('save_post', 'wpm_page_save_meta', 1, 2);
function wpm_page_save_meta($post_id, $post)
{
    // verify this came from the our screen and with proper authorization,
    // because save_post can be triggered at other times

    if (array_key_exists('wpm_page_noncename', $_POST) && !wp_verify_nonce($_POST['wpm_page_noncename'], plugin_basename(__FILE__))) {
        return $post_id;
    }

    // Is the user allowed to edit the post or page?
    if (!current_user_can('edit_post', $post_id)) {
        return $post_id;
    }

    if(isset($_POST['page_meta'])){
        $new_meta = $_POST['page_meta'];

        if(!isset($new_meta['is_homework'])) {
            $new_meta['is_homework'] = false;
        }

        if(!isset($new_meta['shift_is_on'])) {
            $new_meta['shift_is_on'] = false;
        }

        if(wpm_array_get($new_meta, 'shift_value_type', 'interval') == 'interval') {
            if (isset($new_meta['shift_value_minutes']) && intval($new_meta['shift_value_minutes'])) {
                $shiftHours = intval($new_meta['shift_value']);
                $shiftHours += wpm_minutes2hours($new_meta['shift_value_minutes']);
                $new_meta['shift_value'] = $shiftHours;
            }

            if (intval(wpm_array_get($new_meta, 'shift_value_days'))) {
                $new_meta['shift_value'] += intval(wpm_array_get($new_meta, 'shift_value_days')) * 24;
            }
        }

        if(wpm_array_get($new_meta, 'homework_shift_value_type', 'interval') == 'interval') {
            if (isset($new_meta['homework_shift_value_minutes']) && intval($new_meta['homework_shift_value_minutes'])) {
                $shiftHours = intval($new_meta['homework_shift_value']);
                $shiftHours += wpm_minutes2hours($new_meta['homework_shift_value_minutes']);
                $new_meta['homework_shift_value'] = $shiftHours;
            }

            if (intval(wpm_array_get($new_meta, 'homework_shift_value_days'))) {
                $new_meta['homework_shift_value'] += intval(wpm_array_get($new_meta, 'homework_shift_value_days')) * 24;
            }
        }

        $page_meta = get_post_meta(get_the_ID(), '_wpm_page_meta', true);
        $page_meta = array_merge($page_meta, $new_meta );
        update_post_meta($post_id, '_wpm_page_meta', $page_meta);
    }

    if(isset($_POST['mbl_short_description'])) {
        update_post_meta($post_id, 'mbl_short_description', $_POST['mbl_short_description']);
    }

    wpm_update_rearranged_schedules($post_id);
}

function wpm_form_to_array(&$array, $path, $value)
{
    $key = array_shift($path);
    if (empty($path)) {
        $array[$key] = stripslashes(wp_filter_post_kses(addslashes($value)));
    } else {
        if (!isset($array[$key]) || !is_array($array[$key])) {
            $array[$key] = array();
        }
        wpm_set_value($array[$key], $path, $value);
    }
}
