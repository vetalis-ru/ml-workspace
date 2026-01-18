<?php

class MBLRedactor
{
    const SUPPORTED_IMAGE_TYPES = [
        'image/jpeg',
        'image/gif',
        'image/png',
    ];

    public static function summernote($content, $id, $options = [], $required = false, $name = null, $empty = false)
    {
        if ($name === null) {
            $name = $id;
        }

        $textOnly = wpm_array_get($options, 'text-only');

        $toolbar = [];

        if(!$empty) {
            $toolbar[] = ['font', ['bold', 'italic', 'underline']];
            $toolbar[] = ['color', ['color']];
            $toolbar[] = ['para', ['paragraph', 'link']];

            if(!$textOnly && wpm_option_is('main.redactor', 'new')) {
                $toolbar[] = ['insert', ['picture', 'videoUpload', 'audioUpload', 'emojiList']];
            } elseif(!$textOnly) {
                $toolbar[] = ['insert', ['picture']];
            }
        }

        $defaultOptions = [
            'height'        => 200,
            'dialogsInBody' => true,
            'toolbar'       => $toolbar,
        ];

        if (get_locale() == 'ru_RU') {
            $defaultOptions['lang'] = 'ru-RU';
        }

        $options = json_encode(array_merge($defaultOptions, $options));

        return self::_summernoteCodeFront($content, $id, $options, $required, $name);
    }

    public static function summernoteAdmin($content, $id, $options = [], $required = false, $name = null)
    {
        if ($name === null) {
            $name = $id;
        }
        $defaultOptions = [
            'height'        => 200,
            'dialogsInBody' => false,
            'toolbar'       => (wpm_option_is('main.redactor', 'new')
                ?[
                    ['font', ['bold', 'underline', 'clear']],
                    ['fontname', ['fontname']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link']],
                    ['view', ['codeview']],
                    ['mybutton', ['customPhoto', 'customAudio', 'customVideo', 'emojiList']]
                ]
                :[
                    ['font', ['bold', 'underline', 'clear']],
                    ['fontname', ['fontname']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'customPhoto']],
                    ['view', ['codeview']],
                ]
            )
        ];

        if (get_locale() == 'ru_RU') {
            $defaultOptions['lang'] = 'ru-RU';
        }

        $options = json_encode(array_merge($defaultOptions, $options));

        return sprintf('<div class="bootstrap-admin-wrap" style="font-weight: normal">%s</div>', self::_summernoteCode($content, $id, $options, $required, $name));
    }

    public static function getWpPageTinyMCEOptions()
    {
        if (version_compare(get_bloginfo('version'), '3.9', '>=')) {
            $wppage_tinymce_options = [
                'quicktags'     => false,
                'media_buttons' => false,
                'editor_height' => 100,
                'editor_class'  => 'wppage-footer-content',
                'tinymce'       => [
                    'toolbar1'          => 'bold italic underline strikethrough | forecolor backcolor | justifyleft justifycenter justifyright | bullist numlist outdent indent |removeformat | link unlink hr',
                    'toolbar2'          => false,
                    'toolbar3'          => false,
                    'forced_root_block' => 'p',
                    'force_br_newlines' => false,
                    'force_p_newlines'  => true,
                    'remove_linebreaks' => true,
                    'wpautop'           => true,
                    'content_css_force' => (plugins_url() . '/member-luxe/templates/base/bootstrap/css/bootstrap.min.css'
                        . ', ' . plugins_url() . '/member-luxe/css/editor-style-wpm-homework.css?' . time()
                        . ', ' . plugins_url() . '/member-luxe/css/bullets.css'
                    ),
                ],
            ];

        } else {
            $wppage_tinymce_options = [
                'media_buttons' => false,
                'teeny'         => false,
                'quicktags'     => false,
                'textarea_rows' => 20,
                'editor_class'  => 'wppage-footer-content',
                'content_css'   => '',
                'tinymce'       => [
                    'theme_advanced_buttons1'   => 'bold,italic,underline,strikethrough,|,forecolor,backcolor,|,justifyleft,justifycenter,justifyright,|,bullist,numlist,outdent,indent,|,removeformat,|,link,unlink,hr',
                    'theme_advanced_buttons2'   => '',
                    'theme_advanced_buttons3'   => '',
                    'theme_advanced_buttons4'   => '',
                    'theme_advanced_font_sizes' => '10px,11px,12px,13px,14px,15px,16px,17px,18px,19px,20px,21px,22px,23px,24px,25px,26px,27px,28px,29px,30px,32px,42px,48px,52px',
                    'forced_root_block'         => 'p',
                    'force_br_newlines'         => false,
                    'force_p_newlines'          => true,
                    'remove_linebreaks'         => true,
                    'wpautop'                   => true,
                    'content_css_force'         => (plugins_url() . '/member-luxe/templates/base/bootstrap/css/bootstrap.min.css'
                        . ', ' . plugins_url() . '/member-luxe/css/editor-style-wpm-homework.css?' . time()
                        . ', ' . plugins_url() . '/member-luxe/css/bullets.css'
                    ),
                ],
            ];
        }

        return $wppage_tinymce_options;
    }

    /**
     * @param $content
     * @param $id
     * @param $options
     * @param $required
     * @param $name
     * @return string
     */
    private static function _summernoteCode($content, $id, $options, $required, $name)
    {
        $redactor_version = wpm_get_option('main.redactor');
        $requiredText = $required ? 'required' : '';
        $html = <<<HTML
	<label for="{$id}" style="display:none;"></label>
    <textarea id="{$id}" name="{$name}" {$requiredText}>{$content}</textarea>
    <script type="text/javascript">
        jQuery(function ($) {
            $('#{$id}').summernote(Object.assign(
            {
            codeviewIframeFilter: true,
            callbacks: {
                    onImageUpload: function(image) {
                        if ('{$redactor_version}' !== 'new'){
                               uploadSummernoteImage(image[0], $(this)); // if summernote options toolbar 'insert' have 'picture' and redactor have standart vervion
                        }
                    },
                    onPaste: function (e) {
                         let redactor_id = $(this).attr('id');
                         let editorContainer = $('#'+redactor_id).next('.note-editor').find('.note-editable.panel-body').filter('[contenteditable=true]');
                         setTimeout(function () {
                            $(editorContainer).find('img.twa').each(function(key, value){
                                  $(value).removeAttr('style');
                              });
                         }, 10); 
                    },
                    onChange: function() {
                        let redactor_id = $(this).attr('id');
                        let editorContainer = $('#'+redactor_id).next('.note-editor').find('.note-editable.panel-body').filter('[contenteditable=true]');
                        $(editorContainer).find('p').each(function(key, value){
                            if ($(editorContainer).find('p').length == 1){
                                if($(value).length > 0 && $(value)[0].innerHTML === '<br>' && $(value).hasClass('fixedPar')) {
                                    $(editorContainer).find('p.fixedPar').removeAttr('style').removeAttr('class');
                                }
                            }
                        });
                        
                        $(editorContainer).find('span').each(function(key, value){
                            if ($(value).css('font-size') == '16px') {
                                setTimeout(function () {
                                    $(value).removeAttr('style').replaceWith($(value).html());
                                }, 10); 
                            }
                        });
                    },
                    onInit:function(){
                        let redactor_id = $(this).attr('id');
                        if ('{$redactor_version}' !== 'new'){
                            $('#'+redactor_id).attr('data-version', 'standart');
                        } else {
                            $('#'+redactor_id).attr('data-version', 'new');
                        }
                        $('body > .note-popover').appendTo($('#'+redactor_id).next('.note-editor').find('.note-editing-area'));
                    }
                },
                // add buttons to summernote when calling editor as static php function
                buttons: {
                  customVideo: function(context) {
                    let ui = $.summernote.ui;
                    let button = ui.button({
                        contents: '<i class="fa fa-video-camera"/>',
                        tooltip: dataToScript.add_video_tooltip, 
                        click: function() {
                            context.invoke('editor.saveRange');
                            //call function from plugins/member-luxe/js/admin/script.js
                            insertAudioVideoToSummernote(context, 'video_summernote', dataToScript.add_video_title);
                        }
                    }); 
                    return button.render();    
                  },
                  customAudio: function(context) {
                    let ui = $.summernote.ui;
                    let button = ui.button({
                      contents: '<i class="fa fa-volume-up"/>',
                      tooltip: dataToScript.add_audio_tooltip,
                      click: function() {
                          context.invoke('editor.saveRange');
                          //call function from plugins/member-luxe/js/admin/script.js
                          insertAudioVideoToSummernote(context, 'audio_summernote', dataToScript.add_audio_title);
                      }
                    });
                    return button.render();
                  },
                  customPhoto: function(context) {
                    let ui = $.summernote.ui;
                    let button = ui.button({
                      contents: '<i class="fa fa-picture-o"/>',
                      tooltip: dataToScript.add_photo_tooltip,
                      click: function() {
                          context.invoke('editor.saveRange');
                          //call function from plugins/member-luxe/js/admin/script.js
                          insertPhotoInWPMedia(context, dataToScript);
                      }
                    }); 
                    return button.render();
                  }
                }
            }, JSON.parse(JSON.stringify($options))));
        });
    </script>
HTML;

        return $html;
    }

    private static function _summernoteCodeFront($content, $id, $options, $required, $name)
    {
	    $onPaste = apply_filters('wpm_summernote_onpaste', '', $id);
	    $onEnter = apply_filters('wpm_summernote_onenter', '', $id);
        $requiredText = $required ? 'required' : '';
        $html = <<<HTML
        <div id="{$id}-upload-image-progress-bar-wrap" class="form-group" style="margin-bottom: 40px; display: none; width: 100%;">
            <div class="col-xs-12">
                <div class="reading-status-row" style="margin: 0 0 10px;">
                    <div class="progress-wrap">
                        <div class="course-progress-wrap">
                            <div class="progress" style="width: 100%">
                                <div class="progress-bar progress-bar-success" role="progressbar"></div>
                            </div>
                        </div>
                    </div>
                    <div class="right-wrap">
                        <a class="next ui-icon-wrap cancel-upload">
                            <span>Отмена</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <label for="{$id}" style="display:none;"></label>    
    <textarea id="{$id}" name="{$name}" {$requiredText}>{$content}</textarea>
    <script type="text/javascript">
        jQuery(function ($) {
            $('.note-image-input.form-control').prop("multiple", false);
            $('.note-toolbar.panel-heading').find('.note-btn-group.note-insert > button:first-child').find('i').removeClass('note-icon-picture').addClass('fa fa-picture-o');
            $('#{$id}').summernote(Object.assign(
            {callbacks: {
                    // add callback to summernote when calling editor img button
                    onImageUpload: function(image) {
                        uploadSummernoteImageFront(image[0], $(this));
                    },
                    onPaste: function (e) {
                        {$onPaste}
                         var redactor_id = $(this).attr('id');
                         var editorContainer = $('#'+redactor_id).next('.note-editor').find('.note-editable.panel-body').filter('[contenteditable=true]');
                         setTimeout(function () {
                            $(editorContainer).find('img.twa').each(function(key, value){
                                  $(value).removeAttr('style');
                              });
                         }, 10); 
                    },
                    onChange: function() {
                        var editorContainer = $('#{$id}').next('.note-editor').find('.note-editable.panel-body').filter('[contenteditable=true]');
                        $(editorContainer).find('p').each(function(key, value){
                           if ($(editorContainer).find('p').length == 1){
                                if($(value).length > 0 && $(value)[0].innerHTML === '<br>' && $(value).hasClass('fixedPar')) {
                                    $(editorContainer).find('p.fixedPar').removeAttr('style').removeAttr('class');
                                }
                           }
                        });

                        var player = $(editorContainer).find('.video-iframe');
                        if($(player).length){
                             $(player).css({'height':($(player).width() * 0.5625) + 'px'});
                        }
                        
                        $(editorContainer).find('span').each(function(key, value){
                            if ($(value).css('font-size') == '16px') {
                                setTimeout(function () {
                                    $(value).removeAttr('style').replaceWith($(value).html());
                                }, 10); 
                            }
                        });
                        
                    },
                    onInit:function(){
                        var redactor_id = $(this).attr('id');                     
                        $('body > .note-popover').appendTo($('#'+redactor_id).next('.note-editor').find('.note-editing-area'));
                    },
                    onEnter: function(e) {
				      {$onEnter}
				    },
                }
            }, JSON.parse(JSON.stringify($options))));
        });
    </script>
HTML;

        return $html;
    }


    public static function saveImage()
    {
        check_ajax_referer('mbl_redactor_insert_image', 'nonce');

        $postData = wpm_array_get($_POST);
        $files = wpm_array_get($_FILES);
        $data = array_merge($postData, $files);

        if (empty($files)) {
            wp_send_json_error(__('Файлы не были загружены', 'mbl_admin'));
        }

        $uploadedType = wpm_array_get(wp_check_filetype(basename($data['file']['name'])), 'type');

        if (!in_array($uploadedType, self::SUPPORTED_IMAGE_TYPES, true)) {
            wp_send_json_error(__('Тип файла не поддерживается', 'mbl_admin'));
        }

        $uploadResult = wp_handle_upload($data['file'], ['test_form' => false]);

        if ($uploadResult && !isset($uploadResult['error'])) {
            wp_send_json_success([
                'name' => basename($uploadResult['url']),
                'url'  => $uploadResult['url'],
            ]);
        } else {
            wp_send_json_error(__('Ошибка при загрузке файла', 'mbl_admin'));
        }
    }

    public static function saveSummernoteUploadFile()
    {
        if (!empty($_FILES)) {
            $cur_user_id = get_current_user_id();
            $uploads = wp_upload_dir();

            if (!empty($_POST['file_type'])) {
                switch ($_POST['file_type']) {
                    case 'image':
                        $folderPath = $cur_user_id ? SUMMERNOTE_UPLOADS_DIR.'/authorized_users/'.$cur_user_id.'/images' : SUMMERNOTE_UPLOADS_DIR.'/unauthorized_users/images';
                        break;
                    case 'video':
                        $folderPath = $cur_user_id ? SUMMERNOTE_UPLOADS_DIR.'/authorized_users/'.$cur_user_id.'/video' : SUMMERNOTE_UPLOADS_DIR.'/unauthorized_users/video';
                        break;
                    case 'audio':
                        $folderPath = $cur_user_id ? SUMMERNOTE_UPLOADS_DIR.'/authorized_users/'.$cur_user_id.'/audio' : SUMMERNOTE_UPLOADS_DIR.'/unauthorized_users/audio';
                        break;
                    default:
                        return wp_send_json_error(__('Не указан тип файла', 'mbl_admin'));
                }
            }

            if (!file_exists($uploads['basedir'].'/'.$folderPath)) {
                mkdir($uploads['basedir'].'/'.$folderPath, 0755, true);
            }

            //$ext = explode('.', $_FILES['file']['name'])[1];
            $ext = strtolower(preg_replace('/^.*\.([^.]+)$/D', '$1', $_FILES['file']['name']));
            $name = str_replace(' ', '_', strtolower(substr($_FILES['file']['name'], 0, strpos($_FILES['file']['name'], '.'))));
            $suporrtedUploadsExt = ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'ogg', 'ogv', 'mov', 'webm', 'mp3', 'wav', 'm4a'];

            if (!in_array($ext, $suporrtedUploadsExt, true)) {
                $comma_separated = implode(";", $_FILES['file']);
                wp_send_json_error(__('Не поддерживаемый формат - '.$ext.'|'.$comma_separated.'|'.$name, 'mbl_admin'));
                //var_dump($_FILES, $ext, $name);
            } else {
                $fullPathImage = $uploads['basedir'].'/'.$folderPath.'/'.$name.'-'.time().'.'.$ext;
                if (move_uploaded_file($_FILES['file']['tmp_name'], $fullPathImage)) {
                    $file_return_url = $uploads['baseurl'].'/'.$folderPath.'/'.$name.'-'.time().'.'.$ext;
                    wp_send_json_success( $file_return_url, 200 );
                }else{
                    wp_send_json_error(__('Ошибка при загрузке файла', 'mbl_admin'));
                }
            }
        } else {
            wp_send_json_error(__('Файл не загружен', 'mbl_admin'));
        }
        wp_reset_postdata();
    }
    public static function summernoteLocale($interface = 'mbl')
    {
        return [
            'font'=>
                [
                    'bold'=> __('Полужирный', $interface),
                    'italic'=> __('Курсив', $interface),
                    'underline' => __('Подчёркнутый', $interface),
                    'clear' => __('Убрать стили шрифта', $interface),
                    'height' => __('Высота линии', $interface),
                    'name' => __('Шрифт', $interface),
                    'strikethrough' => __('Зачёркнутый', $interface),
                    'subscript' => __('Нижний индекс', $interface),
                    'superscript' => __('Верхний индекс', $interface),
                    'size' => __('Размер шрифта', $interface)
                ],
            'image'=>
                [
                    'image'=> __('Картинка', $interface),
                    'insert'=> __('Вставить картинку', $interface),
                    'resizeFull' => __('Восстановить размер', $interface),
                    'resizeHalf' => __('Уменьшить до 50%', $interface),
                    'resizeQuarter' => __('Уменьшить до 25%', $interface),
                    'floatLeft' => __('Расположить слева', $interface),
                    'floatRight' => __('Расположить справа', $interface),
                    'floatNone' => __('Расположение по умолчанию', $interface),
                    'shapeRounded' => __('Форма: Закругленная', $interface),
                    'shapeCircle' => __('Форма: Круг', $interface),
                    'shapeThumbnail'=> __('Форма: Миниатюра', $interface),
                    'shapeNone'=> __('Форма: Нет', $interface),
                    'dragImageHere'=> __('Перетащите сюда картинку', $interface),
                    'dropImage'=> __('Перетащите картинку', $interface),
                    'selectFromFiles'=> __('Выбрать из файлов', $interface),
                    'url'=> __('URL картинки', $interface),
                    'remove'=> __('Удалить картинку', $interface)
                ],
            'video'=>
                [
                    'video'=> __('Видео', $interface),
                    'videoLink'=> __('Ссылка на видео', $interface),
                    'insert'=> __('Вставить видео', $interface),
                    'url'=> __('URL видео', $interface),
                    'providers'=> __('(YouTube, Vimeo, Vine, Instagram, DailyMotion или Youku)', $interface)
                ],
            'link'=>
                [
                    'link'=> __('Ссылка', $interface),
                    'insert'=> __('Вставить ссылку', $interface),
                    'unlink'=> __('Убрать ссылку', $interface),
                    'edit'=> __('Редактировать', $interface),
                    'textToDisplay'=> __('Отображаемый текст', $interface),
                    'url'=> __('URL для перехода', $interface),
                    'openInNewWindow'=> __('Открывать в новом окне', $interface)
                ],
            'table'=>
                [
                    'table'=> __('Таблица', $interface),
                ],
            'hr'=>
                [
                    'insert'=> __('Вставить горизонтальную линию', $interface),
                ],
            'style'=>
                [
                    'style'=> __('Стиль', $interface),
                    'p'=> __('Нормальный', $interface),
                    'blockquote'=> __('Цитата', $interface),
                    'pre'=> __('Код', $interface),
                    'h1'=> __('Заголовок 1', $interface),
                    'h2'=> __('Заголовок 2', $interface),
                    'h3'=> __('Заголовок 3', $interface),
                    'h4'=> __('Заголовок 4', $interface),
                    'h5'=> __('Заголовок 5', $interface),
                    'h6'=> __('Заголовок 6', $interface)
                ],
            'lists'=>
                [
                    'unordered'=> __('Маркированный список', $interface),
                    'ordered'=> __('Нумерованный список', $interface),
                ],
            'options'=>
                [
                    'help'=> __('Помощь', $interface),
                    'fullscreen'=> __('На весь экран', $interface),
                    'codeview'=> __('Исходный код', $interface),
                ],
            'paragraph'=>
                [
                    'paragraph'=> __('Параграф', $interface),
                    'outdent'=> __('Уменьшить отступ', $interface),
                    'indent'=> __('Увеличить отступ', $interface),
                    'left'=> __('Выровнять по левому краю', $interface),
                    'center'=> __('Выровнять по центру', $interface),
                    'right'=> __('Выровнять по правому краю', $interface),
                    'justify'=> __('Растянуть по ширине', $interface)
                ],
            'color'=>
                [
                    'recent'=> __('Последний цвет', $interface),
                    'more'=> __('Еще цвета', $interface),
                    'background'=> __('Цвет фона', $interface),
                    'foreground'=> __('Цвет шрифта', $interface),
                    'transparent'=> __('Прозрачный', $interface),
                    'setTransparent'=> __('Сделать прозрачным', $interface),
                    'reset'=> __('Сброс', $interface),
                    'resetToDefault'=> __('По умолчанию', $interface),
                ],
            'shortcut'=>
                [
                    'shortcuts'=> __('Сочетания клавиш', $interface),
                    'close'=> __('Закрыть', $interface),
                    'textFormatting'=> __('Форматирование текста', $interface),
                    'action'=> __('Действие', $interface),
                    'paragraphFormatting'=> __('Форматирование параграфа', $interface),
                    'documentStyle'=> __('Стиль документа', $interface),
                    'extraKeys'=> __('Дополнительные комбинации', $interface)
                ],
            'history'=>
                [
                    'undo'=> __('Отменить', $interface),
                    'redo'=> __('Повтор', $interface),
                ],
            'videoUpload'=>
                [
                    'dialogTitle'=> __('Добавить видео, макс. - ', $interface),
                    'tooltip'=> __('Видео [beta]', $interface),
                    'href'=> __('URL', $interface),
                    'note'=> __('URL видео файла', $interface),
                    'somethingWrong'=> __('Ой. Что то пошло не так!', $interface),
                    'note'=> __('URL видео (YouTube, Vimeo, Vine, Instagram, DailyMotion или Youku)', $interface),
                    'selectFromFiles'=> __('Выбрать из файлов (поддерживаемые форматы: mp4/mov)', $interface),
                    'maxUploadSize'=> __('Файл не должен быть больше ', $interface),
                    'videoFileType'=> __('Загружаемый файл должен быть видео файлом!', $interface),
                    'ok'=> __('Вставить видео', $interface),
                    'cancel'=> __('Отменить', $interface)
                ],
            'audioUpload'=>
                [
                    'dialogTitle'=> __('Добавить аудио, макс. - ', $interface),
                    'tooltip'=> __('Аудио [beta]', $interface),
                    'href'=> __('URL', $interface),
                    'note'=> __('URL аудио файла', $interface),
                    'somethingWrong'=> __('Ой. Что то пошло не так!', $interface),
                    'selectFromFiles'=> __('Выбрать из файлов (поддерживаемые форматы: mp3/m4a/wav)', $interface),
                    'maxUploadSize'=> __('Файл не должен быть больше ', $interface),
                    'audioFileType'=> __('Загружаемый файл должен быть аудио файлом!', $interface),
                    'ok'=> __('Вставить аудио', $interface),
                    'cancel'=> __('Отменить', $interface)
                ],
            'emojiUpload'=>
                [
                    'tooltip'=> __('Эмоджи [beta]', $interface),
                ],
        ];

    }
}

