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
        videoUpload: {
            icon: '<i class="fa fa-video-camera"></i>'
        }
    });
    $.extend($.summernote.plugins, {
        'videoUpload': function (context) {
            var redactor_id = context.layoutInfo.note[0].getAttribute('id');
            var self      = this,
                ui        = $.summernote.ui,
                $editor   = context.layoutInfo.editor,
                $editable = context.layoutInfo.editable,
                options   = context.options,
                lang      = options.langInfo;
            context.memo('button.videoUpload', function() {
                var button = ui.button({
                    contents: options.videoUpload.icon,
                    tooltip:  lang.videoUpload.tooltip,
                    click:    function (e) {
                        context.invoke('saveRange');
                        context.invoke('videoUpload.show');
                    }
                });
                return button.render();
            });
            this.initialize = function () {
                var $container = options.dialogsInBody ? $(document.body) : $editor;
                var body =
                    '<div class="form-group">' +
                    '  <div class="col-xs-12 help-block">' + lang.videoUpload.selectFromFiles + '</div>' +
                    '</div>' +
                    '<div class="form-group">' +
                    '  <div class="input-group col-xs-12">' +
                    '       <input class="note-image-input form-control" type="file" name="files" accept="video/mp4,video/x-m4v,video/*" id="'+redactor_id+'-upload_video_local" />' +
                    '       <input type="hidden" id="'+redactor_id+'-local_video_url" value="" />' +
                    '  </div>' +
                    '</div>'+
                    '<div id="'+redactor_id+'-progress-bar-wrap" class="form-group" style="display: none">' +
                        '<div class="col-xs-12">'+
                            '<div class="reading-status-row" style="margin: 0 0 10px;">'+
                                '<div class="progress-wrap">'+
                                    '<div class="course-progress-wrap">'+
                                        '<div class="progress">'+
                                            '<div class="progress-bar progress-bar-success" role="progressbar"></div>'+
                                        '</div>'+
                                '</div>'+
                                '</div>'+
                                '<div class="right-wrap">'+
                                    '<a class="next ui-icon-wrap cancel-upload">'+
                                        '<span class="arrow-label">'+ lang.videoUpload.cancel +'</span>'+
                                    '</a>'+
                                '</div>'+
                            '</div>'+
                        '</div>'+
                    '</div>'+
                    '<div class="form-group">' +
                    '  <div class="col-xs-12 help-block">' + lang.videoUpload.note + '</div>' +
                    '</div>' +
                    '<div class="form-group">' +
                    '  <div class="input-group col-xs-12">' +
                    '    <input type="text" class="note-video-attributes-href form-control" />' +
                    '  </div>' +
                    '</div>';
                this.$dialog = ui.dialog({
                    title:  lang.videoUpload.dialogTitle + bytesToSize(wp_max_uload_size),
                    body:   body,
                    footer: '<button href="#" class="btn btn-primary note-video-attributes-btn disabled" disabled id="'+redactor_id+'-insert-custom-video">' + lang.videoUpload.ok + '</button>'
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
                        $videoHref      = self.$dialog.find('.note-video-attributes-href'),
                        url             = $videoHref.val(),
                        $hiddenInput      = self.$dialog.find('#'+redactor_id+'-local_video_url'),
                        $videoHTML      = $('<div/>');

                    if ($hiddenInput.val() !== ''){
                        url = $hiddenInput.val();
                    }

                   //$videoHTML.addClass('embed-responsive embed-responsive-16by9 video-popup');

                    var ytMatch    = url.match(/^(?:https?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((\w|-){11})(?:\S+)?$/);
                    var igMatch    = url.match(/(?:www\.|\/\/)instagram\.com\/p\/(.[a-zA-Z0-9_-]*)/);
                    var vMatch     = url.match(/\/\/vine\.co\/v\/([a-zA-Z0-9]+)/);
                    var vimMatch   = url.match(/\/\/(player\.)?vimeo\.com\/([a-z]*\/)*([0-9]{6,11})[?]?.*/);
                    var dmMatch    = url.match(/.+dailymotion.com\/(video|hub)\/([^_]+)[^#]*(#video=([^_&]+))?/);
                    var youkuMatch = url.match(/\/\/v\.youku\.com\/v_show\/id_(\w+)=*\.html/);
                    var mp4Match   = url.match(/^.+.(mp4|m4v)$/);
                    var movMatch   = url.match(/^.+.(mov)$/);
                    var oggMatch   = url.match(/^.+.(ogg|ogv)$/);
                    var webmMatch  = url.match(/^.+.(webm)$/);

                    var $video;

                    if (ytMatch && ytMatch[1].length === 11) {
                        var youtubeId = ytMatch[1];
                        $video = $('<iframe>')
                            .attr('frameborder', 0)
                            .attr('src', '//www.youtube.com/embed/' + youtubeId)
                            .attr('width', '640').attr('height', '360');
                    } else if (igMatch && igMatch[0].length) {
                        $video = $('<iframe>')
                            .attr('frameborder', 0)
                            .attr('src', 'https://instagram.com/p/' + igMatch[1] + '/embed/')
                            .attr('width', '612').attr('height', '710')
                            .attr('scrolling', 'no')
                            .attr('allowtransparency', 'true');
                    } else if (vMatch && vMatch[0].length) {
                        $video = $('<iframe>')
                            .attr('frameborder', 0)
                            .attr('src', vMatch[0] + '/embed/simple')
                            .attr('width', '600').attr('height', '600')
                            .attr('class', 'vine-embed');
                    } else if (vimMatch && vimMatch[3].length) {
                        $video = $('<iframe webkitallowfullscreen mozallowfullscreen allowfullscreen>')
                            .attr('frameborder', 0)
                            .attr('src', '//player.vimeo.com/video/' + vimMatch[3])
                            .attr('width', '640').attr('height', '360');
                    } else if (dmMatch && dmMatch[2].length) {
                        $video = $('<iframe>')
                            .attr('frameborder', 0)
                            .attr('src', '//www.dailymotion.com/embed/video/' + dmMatch[2])
                            .attr('width', '640').attr('height', '360');
                    } else if (youkuMatch && youkuMatch[1].length) {
                        $video = $('<iframe webkitallowfullscreen mozallowfullscreen allowfullscreen>')
                            .attr('frameborder', 0)
                            .attr('height', '498')
                            .attr('width', '510')
                            .attr('src', '//player.youku.com/embed/' + youkuMatch[1]);
                    } else if (mp4Match || oggMatch || webmMatch || movMatch) {
                        $video = $('<video controls>')
                            .attr('src', url)
                            .attr('width', '640').attr('height', '360');
                    } else {
                        // this is not a known video link. Now what, Cat? Now what?
                        return false;
                    }

                    $video.addClass('note-video-clip inner-video');
                    //$video.css({'width':'100%','max-width':'640px', 'height':'auto'});
                    //$videoHTML.css({"width": "80%"});
                    $videoHTML.css({"margin": "0 0 5px"});
                    $videoHTML.html($video);
                    context.invoke('restoreRange');
                    if ($video) {
                        // insert video node
                        if ($hiddenInput.val() !== '') {
                            $videoHTML.addClass('video-wrap-from-local');
                            context.invoke('editor.insertNode', $videoHTML[0]);
                            $('#'+redactor_id).next('.note-editor').find('.note-editable.panel-body').filter('[contenteditable=true]').find('p').each(function(key, value){
                                if(key === 0 && $(this).length > 0 && $(this)[0].innerHTML === '<br>') {
                                    $(value).addClass('fixedPar').css({'position':'fixed'});
                                }
                            });
                            $('#' + redactor_id).summernote('formatPara');
                        } else {
                            context.invoke('editor.pasteHTML', $video);
                        }
                    }
                });
            };
            this.showLinkDialog = function (vidInfo) {
                return $.Deferred(function (deferred) {
                    var $videoHref = self.$dialog.find('.note-video-attributes-href');
                    var $uploadInput = self.$dialog.find('#'+redactor_id+'-upload_video_local');
                    var $responseHiddenInput = self.$dialog.find('#'+redactor_id+'-local_video_url');
                    var $progressBarWrap = self.$dialog.find('#'+redactor_id+'-progress-bar-wrap');
                    var $progressBar = self.$dialog.find('#'+redactor_id+'-progress-bar-wrap .progress-bar');
                    var $progressCancelBtn = self.$dialog.find('#'+redactor_id+'-progress-bar-wrap .cancel-upload');

                    $editBtn = self.$dialog.find('#'+redactor_id+'-insert-custom-video');
                    $uploadInput.on('change', function (e) {
                        var val = $(this).val().toLowerCase(),
                            regex = new RegExp("(.*?)\.(webm|mp4|ogv|ogg|mov)$");
                        if ((regex.test(val))) {
                            if ( e.target.files[0].size > wp_max_uload_size ) {
                                alert(lang.videoUpload.maxUploadSize + bytesToSize(wp_max_uload_size));
                                $responseHiddenInput.attr('value', '');
                                $uploadInput.val('');
                                return false;
                            }
                            $uploadInput.attr('disabled', 'disabled');
                            $editBtn.addClass('disabled').attr('disabled', 'disabled');
                            var fd = new FormData();
                            fd.append('action', 'uploadSummernoteFile');
                            fd.append('file', e.target.files[0]);
                            fd.append('file_type', 'video');
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
                                                $videoHref.attr('readonly', 'readonly');
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
                                            $videoHref.removeAttr('readonly');
                                            $uploadInput.val('').removeAttr('disabled');
                                        });
                                    });
                                },
                                success: function (response) {
                                    setTimeout(function () {
                                        $progressBarWrap.fadeOut(200, "linear", function() {
                                            $progressBar.removeAttr('style aria-valuenow');
                                            $videoHref.removeAttr('readonly');
                                            $uploadInput.val('').removeAttr('disabled');
                                        });
                                    }, 1000);

                                    if (response.success) {
                                        $responseHiddenInput.attr('value', response.data);
                                    } else {
                                        //console.log(response);
                                        alert(response.data);
                                    }
                                    setTimeout(function () {
                                        $editBtn.click();
                                    }, 10);
                                },
                                error: function (jqXHR, textStatus, errorThrown) {
                                    console.log(jqXHR.status, textStatus, errorThrown);
                                }
                            });
                        } else {
                            alert(lang.videoUpload.videoFileType);
                            $(this).val('');
                            $uploadInput.val('');
                            return false;
                        }
                    });

                    ui.onDialogShown(self.$dialog, function () {
                        $videoHref.attr('value', '');
                        $responseHiddenInput.attr('value', '');
                        $uploadInput.val('');
                        context.triggerEvent('dialog.shown');
                        $editBtn.click(function (e) {
                            e.preventDefault();
                            deferred.resolve({
                                vidDom: vidInfo.vidDom,
                                href: $videoHref.val()
                            });
                        });
                        $videoHref.val(vidInfo.href).focus;
                        self.bindEnterKey($editBtn);
                        self.bindAddBtn($videoHref, $editBtn);
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