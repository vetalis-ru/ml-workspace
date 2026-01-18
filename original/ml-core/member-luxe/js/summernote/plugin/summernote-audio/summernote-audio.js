(function (factory) {
    if (typeof define === 'function' && define.amd) {
        define(['jquery'],factory);
    } else if(typeof module === 'object' && module.exports) {
        module.exports = factory(require('jquery'));
    } else {
        factory(window.jQuery);
    }
}(function ($) {
    $.extend($.summernote.options, {
        audioUpload: {
            icon: '<i class="fa fa-volume-up"></i>'
        }
    });
    $.extend($.summernote.plugins, {
        'audioUpload': function (context) {
            var redactor_id = context.layoutInfo.note[0].getAttribute('id');
            var self      = this,
                ui        = $.summernote.ui,
                $editor   = context.layoutInfo.editor,
                $editable = context.layoutInfo.editable,
                options   = context.options,
                lang      = options.langInfo;
            context.memo('button.audioUpload', function() {
                var button = ui.button({
                    contents: options.audioUpload.icon,
                    tooltip:  lang.audioUpload.tooltip,
                    click:    function (e) {
                        context.invoke('saveRange');
                        context.invoke('audioUpload.show');
                    }
                });
                return button.render();
            });
            this.initialize = function () {
                var $container = options.dialogsInBody ? $(document.body) : $editor;
                var body =
                    '<div class="form-group">' +
                    '  <div class="col-xs-12 help-block">' + lang.audioUpload.selectFromFiles + '</div>' +
                    '</div>' +
                    '<div class="form-group">' +
                    '  <div class="input-group col-xs-12">' +
                    '       <input class="note-image-input form-control" type="file" name="files" accept="audio/x-m4a,audio/*" id="'+redactor_id+'-upload_audio_local"/>' +
                    '       <input type="hidden" id="'+redactor_id+'-local_audio_url" value="" />' +
                    '  </div>' +
                    '</div>'+
                    '<div id="'+redactor_id+'-progress-bar-wrap" class="form-group" style="display: none">' +
                    '   <div class="col-xs-12">'+
                    '       <div class="reading-status-row" style="margin: 0 0 10px;">'+
                    '           <div class="progress-wrap">'+
                    '               <div class="course-progress-wrap">'+
                    '                   <div class="progress">'+
                    '                       <div class="progress-bar progress-bar-success" role="progressbar"></div>'+
                    '                   </div>'+
                    '               </div>'+
                    '           </div>'+
                    '           <div class="right-wrap">'+
                    '               <a class="next ui-icon-wrap cancel-upload">'+
                    '                   <span class="arrow-label">'+ lang.audioUpload.cancel +'</span>'+
                    '               </a>'+
                    '           </div>'+
                    '       </div>'+
                    '   </div>'+
                    '</div>'+
                    '<div class="form-group">' +
                    '  <div class="col-xs-12 help-block">' + lang.audioUpload.note + '</div>' +
                    '</div>' +
                    '<div class="form-group">' +
                    '  <div class="input-group col-xs-12">' +
                    '    <input type="text" class="note-audio-attributes-href form-control" />' +
                    '  </div>' +
                    '</div>';
                this.$dialog = ui.dialog({
                    title:  lang.audioUpload.dialogTitle + bytesToSize(wp_max_uload_size),
                    body:   body,
                    footer: '<button href="#" class="btn btn-primary note-audio-attributes-btn disabled" disabled id="'+redactor_id+'-insert-custom-audio">' + lang.audioUpload.ok + '</button>'
                }).render().appendTo($container);
            };
            this.destroy = function () {
                ui.hideDialog(this.$dialog);
                this.$dialog.remove();
            };
            this.bindEnterKey = function ($input,$btn) {
                $input.on('keypress', function (e) {
                    if(e.keyCode === 13) $btn.trigger('click');
                });
            };
            this.bindAddBtn = function ($input,$btn) {
                $input.on('input propertychange', function (e) {
                    $(this).val() !== '' ? $btn.removeClass('disabled').removeAttr('disabled') : $btn.addClass('disabled').attr('disabled', 'disabled');
                });
            };
            this.bindLabels = function () {
                self.$dialog.find('.form-control:first').focus().select();
                self.$dialog.find('label').on('click', function() {
                    $(this).parent().find('.form-control:first').focus();
                });
            };
            this.show = function () {
                var $vid = $($editable.data('target'));
                var vidInfo = {
                    vidDom: $vid,
                    href: $vid.attr('href')
                };
                this.showLinkDialog(vidInfo).then(function (vidInfo) {
                    ui.hideDialog(self.$dialog);
                    var $vid            = vidInfo.vidDom,
                        $audioHref      = self.$dialog.find('.note-audio-attributes-href'),
                        url             = $audioHref.val(),
                        $hiddenInput      = self.$dialog.find('#'+redactor_id+'-local_audio_url'),
                        $videoHTML      = $('<div/>');

                    if ($hiddenInput.val() !== ''){
                        url = $hiddenInput.val();
                    }
                    //$videoHTML.addClass('embed-responsive embed-responsive-16by9');
                    var mp3Match   = url.match(/^.+.(mp3)$/);
                    var oggMatch   = url.match(/^.+.(ogg|ogv)$/);
                    var wavMatch   = url.match(/^.+.(wav)$/);
                    var webmMatch  = url.match(/^.+.(webm)$/);
                    var m4aMatch  = url.match(/^.+.(m4a)$/);
                    var $audio;

                    if (mp3Match || oggMatch || wavMatch || webmMatch || m4aMatch) {
                        $audio = $('<audio controls>')
                            .attr('src', url);
                    } else {
                        return false;
                    }

                    //$audio.addClass('note-audio-clip');
                    $videoHTML.html($audio);
                    context.invoke('restoreRange');
                    if ($audio) {
                        // insert audio node
                        context.invoke('editor.insertNode', $videoHTML[0]);
                        $('#'+redactor_id).next('.note-editor').find('.note-editable.panel-body').filter('[contenteditable=true]').find('p').each(function(key, value){
                            if(key === 0 && $(this).length > 0 && $(this)[0].innerHTML === '<br>') {
                                $(value).addClass('fixedPar').css({'position':'fixed'});
                            }
                        });
                        $('#'+redactor_id).summernote('formatPara');
                    }
                });
            };
            this.showLinkDialog = function (vidInfo) {
                return $.Deferred(function (deferred) {
                    var $audioHref = self.$dialog.find('.note-audio-attributes-href');
                    var $uploadInput = self.$dialog.find('#'+redactor_id+'-upload_audio_local');
                    var $responseHiddenInput = self.$dialog.find('#'+redactor_id+'-local_audio_url');
                    var $progressBarWrap = self.$dialog.find('#'+redactor_id+'-progress-bar-wrap');
                    var $progressBar = self.$dialog.find('#'+redactor_id+'-progress-bar-wrap .progress-bar');
                    var $progressCancelBtn = self.$dialog.find('#'+redactor_id+'-progress-bar-wrap .cancel-upload');

                    $editBtn = self.$dialog.find('#'+redactor_id+'-insert-custom-audio');
                    $uploadInput.on('change', function (e) {
                        var val = $(this).val().toLowerCase(),
                            regex = new RegExp("(.*?)\.(mp3|ogv|ogg|wav|webm|m4a)$");
                        if ((regex.test(val))) {
                            if ( e.target.files[0].size > wp_max_uload_size ) {
                                alert(lang.audioUpload.maxUploadSize + bytesToSize(wp_max_uload_size));
                                $responseHiddenInput.attr('value', '');
                                $uploadInput.val('');
                                return false;
                            }
                            $uploadInput.attr('disabled', 'disabled');
                            $editBtn.addClass('disabled').attr('disabled', 'disabled');
                            var fd = new FormData();
                            fd.append('action', 'uploadSummernoteFile');
                            fd.append('file', e.target.files[0]);
                            fd.append('file_type', 'audio');
                            $.ajax({
                                type: 'POST',
                                url: ajaxurl,
                                data: fd,
                                contentType: false,
                                processData: false,
                                xhr: function () {
                                    var xhr = new window.XMLHttpRequest();
                                    xhr.upload.addEventListener("progress", function (evt) {
                                        if (evt.lengthComputable) {
                                            var percentComplete = evt.loaded / evt.total;
                                            percentComplete = parseInt(percentComplete * 100);
                                            $progressBarWrap.fadeIn(200, "linear", function() {
                                                $audioHref.attr('readonly', 'readonly');
                                                $progressBar.css('width', percentComplete + '%').attr('aria-valuenow', percentComplete);
                                            });
                                        }
                                    }, false);
                                    return xhr;
                                },
                                beforeSend: function (xhr) {
                                    $progressCancelBtn.on('click', function (e) {
                                        e.preventDefault();
                                        xhr.abort();
                                        $progressBarWrap.fadeOut(200, "linear", function() {
                                            $progressBar.removeAttr('style aria-valuenow');
                                            $audioHref.removeAttr('readonly');
                                            $uploadInput.val('').removeAttr('disabled');
                                        });

                                    });
                                },
                                success: function (response) {
                                    setTimeout(function () {
                                        $progressBarWrap.fadeOut(200, "linear", function() {
                                            $progressBar.removeAttr('style aria-valuenow');
                                            $audioHref.removeAttr('readonly');
                                            $uploadInput.val('').removeAttr('disabled');
                                        });
                                    }, 1000);
                                    if (response.success) {
                                        $responseHiddenInput.attr('value', response.data);
                                    } else {
                                        alert(lang.audioUpload.somethingWrong);
                                    }
                                    setTimeout(function () {
                                        $editBtn.click();
                                    }, 10);
                                },
                                error: function (jqXHR, textStatus, errorThrown) {
                                    console.log(jqXHR.status, textStatus, errorThrown, 'jqXHR');
                                }
                            });
                        } else {
                            alert(lang.audioUpload.audioFileType);
                            $(this).val('');
                            $uploadInput.val('');
                            return false;
                        }
                    });

                    ui.onDialogShown(self.$dialog, function () {
                        $audioHref.attr('value', '');
                        $responseHiddenInput.attr('value', '');
                        $uploadInput.val('');
                        context.triggerEvent('dialog.shown');
                        $editBtn.click(function (e) {
                            e.preventDefault();
                            deferred.resolve({
                                vidDom: vidInfo.vidDom,
                                href: $audioHref.val()
                            });
                        });
                        $audioHref.val(vidInfo.href).focus;
                        self.bindEnterKey($editBtn);
                        self.bindAddBtn($audioHref, $editBtn);
                        self.bindLabels();
                    });
                    ui.onDialogHidden(self.$dialog, function () {
                        $editBtn.off('click');
                        if(deferred.state() === 'pending') deferred.reject();
                    });
                    ui.showDialog(self.$dialog);
                });
            };
        }
    });
}));