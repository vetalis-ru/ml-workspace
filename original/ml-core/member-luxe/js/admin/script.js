jQuery(function ($) {
    initMblTooltips();
});

// Options for summernote

const summernoteOptions = {
    height : 200,
    focus: true,
    dialogsInBody : false,
    lang: (typeof dataToScript !== 'undefined' && dataToScript.locale === 'ru_RU') ? 'ru-RU' : 'en-US',
    toolbar: (typeof dataToScript !== 'undefined' && dataToScript.main_options.main.redactor === 'new')
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
            ['view', ['codeview']]
        ],
    callbacks: {
        onImageUpload: function(image) {
            if (typeof dataToScript !== 'undefined' && dataToScript.main_options.main.redactor !== 'new'){
                uploadSummernoteImage(image[0], jQuery(this)); // if summernote options toolbar 'insert' have 'picture' and redactor have standart vervion
            }
        },
        onPaste: function (e) {
            let redactor_id = jQuery(this).attr('id');
            let editorContainer = jQuery('#'+redactor_id).next('.note-editor').find('.note-editable.panel-body').filter('[contenteditable=true]');
            setTimeout(function () {
                jQuery(editorContainer).find('img.twa').each(function(key, value){
                    jQuery(value).removeAttr('style');
                });
            }, 10);
        },
        onChange: function() {
            var redactor_id = jQuery(this).attr('id');
            var editorContainer = jQuery('#'+redactor_id).next('.note-editor').find('.note-editable.panel-body').filter('[contenteditable=true]');
            jQuery(editorContainer).find('p').each(function(key, value){
                if (jQuery(editorContainer).find('p').length == 1){
                    if(jQuery(value).length > 0 && jQuery(value)[0].innerHTML === '<br>' && jQuery(value).hasClass('fixedPar')) {
                        jQuery(editorContainer).find('p.fixedPar').removeAttr('style').removeAttr('class');
                    }
                }
            });

            jQuery(editorContainer).find('span').each(function(key, value){
                if (jQuery(value).css('font-size') == '16px') {
                    setTimeout(function () {
                        jQuery(value).removeAttr('style').replaceWith(jQuery(value).html());
                    }, 10);
                }
            });
        },
        onInit: function(){
            let redactor_id = jQuery(this).attr('id');
            if (typeof dataToScript !== 'undefined' && dataToScript.main_options.main.redactor !== 'new'){
                jQuery('#'+redactor_id).attr('data-version', 'standart');
            } else {
                jQuery('#'+redactor_id).attr('data-version', 'new');
            }

            jQuery('body > .note-popover').appendTo(jQuery('#'+redactor_id).next('.note-editor').find('.note-editing-area'));
        }
    },
    buttons: {
        customVideo: function(context) {
            let ui = jQuery.summernote.ui;
            let button = ui.button({
                contents: '<i class="fa fa-video-camera"/>',
                tooltip: dataToScript.add_video_tooltip,
                click: function() {
                    insertAudioVideoToSummernote(context, 'video_summernote', dataToScript.add_video_title);
                }
            });
            return button.render();
        },
        customAudio: function(context) {
            let ui = jQuery.summernote.ui;
            let button = ui.button({
                contents: '<i class="fa fa-volume-up"/>',
                tooltip: dataToScript.add_audio_tooltip,
                click: function() {
                    insertAudioVideoToSummernote(context, 'audio_summernote',  dataToScript.add_audio_title);
                }
            });
            return button.render();
        },
        customPhoto: function(context) {
            let ui = jQuery.summernote.ui;
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
};

function initMblTooltips(holder) {
    var $elems;

    holder = holder || false;


    if(holder === false) {
        $elems = jQuery('[data-mbl-tooltip]');
    } else {
        $elems = holder.find('[data-mbl-tooltip]');
    }
    if($elems.tooltip === undefined) {
        return;
    }
    $elems.tooltip(
        {
            classes: {
                "ui-tooltip": "ui-corner-all ui-widget-shadow mbl-ui-tooltip"
            },
            hide: {
                delay: 10000,
                effect: "explode",
                duration: 10000
            }
        }
    );
}

function bytesToSize(bytes) {
    var sizes = ['Байт', 'КБ', 'Мб', 'Гб', 'Тб'];
    if (bytes == 0) return '0 Byte';
    var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
    return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
}

// Insert photo as file for summernote

function uploadSummernoteImage(image, editor) {
    let editorId = editor[0].getAttribute('id');
    if( image && typeof image !== undefined ) {
        if ( image['size'] > dataToScript.wp_max_uload_size ) {
            alert('Файл не должен быть больше '+ bytesToSize(wp_max_uload_size));
            return false;
        }
        if ( image['type'].indexOf('image') == -1 ) {
            alert('Загружаемый файл должен быть изображением!');
            return false;
        }
        const fd = new FormData();
        fd.append('action', 'uploadSummernoteFile');
        fd.append('file', image);
        fd.append('file_type', 'image');

        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: fd,
            contentType: false,
            processData: false,
            success: function (response) {
                if(response.success){
                    var image = jQuery('<img>').attr('src', response.data).addClass('summerNoteImgMargin');
                    jQuery('#'+editorId).summernote("insertNode", image[0]);
                } else {
                    alert(response.data);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR.status, textStatus, errorThrown);
            }
        });
    }
}

// Insert audio and video shortcodes for summernote

function insertAudioVideoToSummernote(context, button_id, title){
    let editorId = context.layoutInfo.note[0].getAttribute('id');
    let range = jQuery('#'+editorId).summernote('saveRange');

    tb_show(title, "#TB_inline?width=640&inlineId=shortcode-settings-conent");
    jQuery('#TB_window').css({'margin-top': '-'+(((window.innerHeight-100)/2)+19).toFixed()+'px', 'top': '50%'});
    jQuery('#TB_ajaxContent').html('');
    jQuery('#TB_ajaxContent').css({'width': '640', 'height': (window.innerHeight - 112) + 'px'}).addClass('wpm-loader');

    jQuery.ajax({
        type: 'GET',
        url: ajaxurl,
        data: {
            action: "get_wpm_shortcode_settings",
            button: button_id,
            editor_id: editorId
        },
        success: function (data) {
            //console.log(data);
            jQuery('#TB_ajaxContent').html(data).removeClass('wpm-loader');
        },
        error: function(errorThrown){
            //console.log(errorThrown);
        }
    });
}
// Insert photo from WP media shortcodes for summernote
function  insertPhotoInWPMedia (context, texts) {
    let wpm_file_frame;
    event.preventDefault();
    // If the media frame already exists, reopen it.
    if (wpm_file_frame) {
        wpm_file_frame.open();
        return;
    }
    // Create the media frame.
    wpm_file_frame = wp.media.frames.downloadable_file = wp.media({
        title    : texts.add_photo_title,
        button   : {
            text : texts.add_photo_button_title
        },
        multiple : false
    });
    // When an image is selected, run a callback.
    wpm_file_frame.on('select', function () {
        let attachment = wpm_file_frame.state().get('selection').first().toJSON();
        if (attachment.type.indexOf('image') != -1) {
            let range = jQuery('#'+context.layoutInfo.note[0].getAttribute('id')).summernote('restoreRange');
            let image = jQuery('<img>').attr('src', attachment.url).addClass('summerNoteImgMargin');
            jQuery('#'+context.layoutInfo.note[0].getAttribute('id')).summernote("insertNode", image[0]);
        } else {
            alert(dataToScript.error_file_type);
        }
    });
    // Finally, open the modal.
    wpm_file_frame.open();
}

// init FancyBox on homework page images
jQuery('body.wpm-page_page_wpm-autotraining').on('click', '.summerNoteImgMargin', function(){
    var img = jQuery(this);
    if ( !img.data('fancy') ) {
        img.wrap('<a href="' + img.attr('src') + '" data-fancybox />')
        .data('fancy', 'true')
        .parent().trigger('click');
    }
});

jQuery(function ($) {
    $('#mbl_no_access_redirect_container').on('change', '[data-no-access-redirect]', function () {
        const
            $this = $(this),
            $other = $('[data-no-access-redirect]').not(this);

        if ($this.prop('checked')) {
            $other.prop('checked', false);
        }
    });
});
