<script>
    jQuery(function ($) {

        $('.wpm-options-page .wpm-settings-form').show()
        $('.wpm-options-page .mbl-options-preloader').hide()

        $('.wpm-inner-accordion').accordion({
            heightStyle : 'content'
        });

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
                if ($('input#wpm_' + image_id + '_original').length) {
                    $('input#wpm_' + image_id + '_original').val(attachment.url);
                }
                $('#wpm-' + image_id + '-preview').attr('src', attachment.url).show();
                $('#delete-wpm-' + image_id).show();
                $('.wpm-crop-media-button[data-id="' + image_id + '"]').show();
            });
            // Finally, open the modal.
            wpm_file_frame.open();
        });
        $(document).on('click', '.wpm-delete-media-button', function () {
            image_id = $(this).attr('data-id');
            $('input#wpm_' + image_id).val('');
            if ($('input#wpm_' + image_id + '_original').length) {
                $('input#wpm_' + image_id + '_original').val('');
            }
            $('#delete-wpm-' + image_id).hide();
            $('#wpm-' + image_id + '-preview').hide();
            $('.wpm-crop-media-button[data-id="' + image_id + '"]').hide();
        });

        //--------
        $('.reset-options').click(function () {
            var notification = $(this).next('.message');
            notification.html('');
            var do_reset = confirm("<?php _e('Вы действительно хотите сбросить настройки?', 'mbl_admin'); ?>");
            if (do_reset) {
                $.ajax({
                    type    : 'POST',
                    url     : ajaxurl,
                    data    : {
                        action      : "wpm_reset_options_to_default_action",
                        option_type : $(this).attr('data-type')
                    },
                    success : function (data) {
                        if (!data.error) {
                            notification.html(data.message);
                            setTimeout(function () {
                                location.reload()
                            }, 1000);
                        } else {
                            notification.html('<?php _e('Настройки не сброшены', 'mbl_admin') ?>');
                        }

                    }
                });
            }
        });

        $(document).on('click', '#wpm_text_protection_chbx', function () {
            $('.wpm-protection-exceptions').fadeToggle('fast');
        });

        changeLinkedList('#send_term_key_lvl', '#send_term_key');

        $("#wpm-border-radius-slider").slider({
            value : <?php echo wpm_get_design_option('main.border-radius', 5); ?>,
            min   : 0,
            max   : 30,
            step  : 1,
            slide : function (event, ui) {
                $("#wpm-border-radius").val(ui.value);
            }
        });

        // add new users

        var emails = [];
        var filtered_emails = [];
        var import_emails = $('#bulk-import-users #import-emails');
        $(document).on('click', '#bulk-import-users #import-add', function (e) {
            $('#bulk-import-users .wpm-ajax-overlay').fadeIn('fast', function () {
                $.ajax({
                    type     : 'POST',
                    url      : ajaxurl,
                    dataType : 'json',
                    data     : {
                        'action' : 'wpm_parse_emails_action',
                        'emails' : $('#bulk-import-users #users-emails-str').val()
                    },
                    success  : function (data) {
                        //console.log(data);

                        if (data.count != 0) {
                            $('#bulk-import-users .wpm-ajax-tab').hide();
                            $('#bulk-import-users .wpm-import-confirm').show();

                            $('#bulk-import-users .wpm-emails-count').html('<?php _e('Найдено емейлов:', 'mbl_admin') ?><b> ' + data.count + '</b>');
                            filtered_emails = data.emails;
                            for (var i = 0; i < Object.keys(data.emails).length; i++) {
                                import_emails.append('<li>' + data.emails[i] + '</li>');
                            }
                        } else {
                            $('#bulk-import-users .wpm-ajax-tab').hide();
                            $('#bulk-import-users .wpm-no-emails').show();
                        }

                        $('#bulk-import-users .wpm-ajax-overlay').fadeOut('fast');
                    }
                });
            });
            e.preventDefault();
        });
        $(document).on('click', '#bulk-import-users .import-new-emails', function () {
            $('#bulk-import-users .wpm-ajax-overlay').fadeIn('fast', function () {
                $('#bulk-import-users .wpm-ajax-tab').hide();
                $('#bulk-import-users .wpm-import-new').show();
                import_emails.html('');
                $('#bulk-import-users #users-emails-str').val('');
                $('#bulk-import-users .wpm-ajax-overlay').fadeOut('fast');
            });
        });
        $(document).on('click', '#bulk-import-users #import-send', function (e) {
            $('#bulk-import-users .wpm-ajax-overlay').fadeIn('fast', function () {

                $('#bulk-import-users .wpm-ajax-tab').hide();
                $('#bulk-import-users .wpm-import-result').show();

                $.ajax({
                    type     : 'POST',
                    url      : ajaxurl,
                    dataType : 'json',
                    async    : true,
                    data     : {
                        'action'       : 'wpm_import_users_action',
                        'emails'       : filtered_emails,
                        'term_id'      : $('#bulk-import-users #users-level').val(),
                        'duration'     : $('#bulk-import-users #users-level-duration').val(),
                        'units'        : $('#bulk-import-users #users-units').val(),
                        'is_unlimited' : ($('#bulk-import-users #mbl-users-is-unlimited').prop('checked') ? 1 : 0)

                    },
                    success  : function (data) {
                        //console.log(data);
                        if (data.count_fails == 0) {
                            var html = '<p><b><?php _e('Все пользователи успешно зарегистрированы!', 'mbl_admin') ?></b></p>';
                        }
                        if (data.count == 0) {
                            var html = '<p><b><?php _e('Ни один пользователь не зарегистрирован!', 'mbl_admin') ?></b></p>';
                        } else {

                        }
                        if (data.count != 0 && data.count_fails != 0) {
                            var html = '<p><b><?php _e('Не все пользователи зарегистрированы!', 'mbl_admin') ?></b></p>';
                        }

                        if (data.count != 0) {
                            var html = html + '<p><b><?php _e('Зарегистрировано:', 'mbl_admin') ?> ' + data.count + '</b></p>';
                            for (var i = 0; i < data.count; i++) {
                                html = html + '<p class="success">' + data.emails[i].email + ' : <span>' + data.emails[i].message + '</span></p>';
                            }
                        }

                        if (data.count_fails != 0) {
                            var html = html + '<p><b><?php _e('Не зарегистрировано:', 'mbl_admin') ?> ' + data.count_fails + '</b></p>';
                            for (var i = 0; i < data.count_fails; i++) {
                                html = html + '<p class="fail">' + data.fails[i].email + ' : <span>' + data.fails[i].message + '</span></p>';

                            }
                        }

                        $('#bulk-import-users .wpm-ajax-import-result').html(html);
                        $('#bulk-import-users .wpm-ajax-overlay').fadeOut('fast');
                    }
                });
            });
            e.preventDefault();
        });

        // add keys to users
        var addkeys_emails = [];
        var addkeys_filtered_emails = [];

        $(document).on('click', '#bulk-addkeys-users #import-add', function (e) {
            $('#bulk-addkeys-users .wpm-ajax-overlay').fadeIn('fast', function () {
                $.ajax({
                    type     : 'POST',
                    url      : ajaxurl,
                    dataType : 'json',
                    data     : {
                        'action' : 'wpm_parse_emails_and_check_users_action',
                        'emails' : $('#bulk-addkeys-users #users-emails-str').val()
                    },
                    success  : function (data) {
                        //console.log(data);

                        var html = '';
                        var email_list = '';

                        // in no emails found
                        if (data.count_registered == 0 && data.count_not_registered == 0) {
                            $('#bulk-addkeys-users .wpm-ajax-tab').hide();
                            $('#bulk-addkeys-users .message').html('<p><?php _e('Не найдено корректных емейлов.', 'mbl_admin') ?></p>');
                            $('#bulk-addkeys-users .wpm-no-emails').show();

                        } else {
                            $('#bulk-addkeys-users .wpm-ajax-tab').hide();
                            $('#bulk-addkeys-users .wpm-import-confirm').show();

                            if (data.count_registered == 0) {
                                $('#bulk-addkeys-users .wpm-ajax-tab').hide();
                                $('#bulk-addkeys-users .message').html('<p><?php _e('Нет пользователей с такими емейлами', 'mbl_admin') ?></p>');
                                $('#bulk-addkeys-users .wpm-no-emails').show();

                            } else {

                                addkeys_filtered_emails = data.email_registered;

                                html = html + '<b><?php _e('Зарегистрированные:', 'mbl_admin') ?> ' + data.count_registered + '</b>';
                                email_list = '';
                                for (var i = 0; i < Object.keys(data.email_registered).length; i++) {
                                    email_list = email_list + '<li>' + data.email_registered[i] + '</li>';
                                }
                                html = html + '<ul>' + email_list + '</ul>';
                            }

                            if (data.count_not_registered != 0) {

                                html = html + '<b class="red"><?php _e('Не зарегистрированные: ', 'mbl_admin') ?>' + data.count_not_registered + '</b>';
                                email_list = '';
                                for (var i = 0; i < Object.keys(data.email_not_registered).length; i++) {
                                    email_list = email_list + '<li>' + data.email_not_registered[i] + '</li>';
                                }
                                html = html + '<ul>' + email_list + '</ul>';

                            }

                        }
                        $('#bulk-addkeys-users .wpm-users-check-result').html(html);
                        $('#bulk-addkeys-users .wpm-ajax-overlay').fadeOut('fast');


                    }
                });
            });
            e.preventDefault();
        });
        $(document).on('click', '#bulk-addkeys-users .import-new-emails', function () {
            $('#bulk-addkeys-users .wpm-ajax-overlay').fadeIn('fast', function () {
                $('#bulk-addkeys-users .wpm-ajax-tab').hide();
                $('#bulk-addkeys-users .wpm-import-new').show();
                $('#bulk-addkeys-users #users-emails-str').val('');
                $('#bulk-addkeys-users .wpm-ajax-overlay').fadeOut('fast');
            });
        });
        $(document).on('click', '#bulk-addkeys-users #import-send', function (e) {
            $('#bulk-addkeys-users .wpm-ajax-overlay').fadeIn('fast', function () {

                $('#bulk-addkeys-users .wpm-ajax-tab').hide();
                $('#bulk-addkeys-users .wpm-import-result').show();

                $.ajax({
                    type     : 'POST',
                    url      : ajaxurl,
                    dataType : 'json',
                    async    : true,
                    data     : {
                        'action'       : 'wpm_bulk_add_key_to_user_action',
                        'emails'       : addkeys_filtered_emails,
                        'term_id'      : $('#bulk-addkeys-users .users-level').val(),
                        'duration'     : $('#bulk-addkeys-users #users-level-duration').val(),
                        'units'        : $('#bulk-addkeys-users #users-units').val(),
                        'is_unlimited' : ($('#bulk-addkeys-users #mbl-users-is-unlimited').prop('checked') ? 1 : 0)

                    },
                    success  : function (data) {
                        // console.log(data);
                        if (data.count_fails == 0) {
                            var html = '<p><b><?php _e('Коды доступа успешно добавлены!', 'mbl_admin') ?></b></p>';
                        }
                        if (data.count == 0) {
                            var html = '<p><b><?php _e('Коды доступа не добавлены!', 'mbl_admin') ?></b></p>';
                        } else {

                        }
                        if (data.count != 0 && data.count_fails != 0) {
                            var html = '<p><b><?php _e('Не всем пользователям добавлены коды доступа!', 'mbl_admin') ?></b></p>';
                        }

                        if (data.count != 0) {
                            var html = html + '<p><b><?php _e('Добавлено:', 'mbl_admin') ?> ' + data.count + '</b></p>';
                            for (var i = 0; i < data.count; i++) {
                                html = html + '<p class="success">' + data.emails[i].email + ' : <span>' + data.emails[i].message + '</span></p>';
                            }
                        }

                        if (data.count_fails != 0) {
                            var html = html + '<p><b><?php _e('Не добавлено:', 'mbl_admin') ?> ' + data.count_fails + '</b></p>';
                            for (var i = 0; i < data.count_fails; i++) {
                                html = html + '<p class="fail">' + data.fails[i].email + ' : <span>' + data.fails[i].message + '</span></p>';

                            }
                        }

                        $('#bulk-addkeys-users .wpm-ajax-import-result').html(html);
                        $('#bulk-addkeys-users .wpm-ajax-overlay').fadeOut('fast');
                    }
                });
            });
            e.preventDefault();
        });

        var $justclickGroups = $('#justclick_groups');
        if ($justclickGroups.length) {
            $.post(ajaxurl, {
                'action' : 'wpm_justclick_groups_select'
            }, function (html) {
                $justclickGroups.html(html);
            })
        }

        var $sendpulseGroups = $('#sendpulse_groups');
        if ($sendpulseGroups.length) {
            $.post(ajaxurl, {
                'action' : 'wpm_sendpulse_get_groups'
            }, function (html) {
                $sendpulseGroups.html(html);
            })
        }

        var $autowebGroups = $('#autoweb_groups');
        if ($autowebGroups.length) {
            $.post(ajaxurl, {
                'action' : 'wpm_autoweboffice_get_groups'
            }, function (html) {
                $autowebGroups.html(html);
            })
        }

        $(".active_checkbox").on('click', function(e){
            if($(this).prop('checked') == true && $(this).attr('class')=='active_checkbox'){
                $(this).closest(".wpm-row").find("input[type='checkbox']").each(function(){
                    $(this).closest('.active_checkbox').prop('checked',false);
                });
                $(this).prop('checked',true);
            }
        });

        $(document).on('change', '#wpm-show-progress', function() {
            $('#wpm-progress-options').slideToggle('fast');
        });

    });

    function changeLinkedList(main, linked) {
        var $ = jQuery,
            val = $(main).val(),
            linkedSrc,
            options;

        linked = $(linked);

        if (linked.length) {
            linkedSrc = $('#' + linked.attr('id') + '_src');
            options = linkedSrc.find('option');

            if (val != '') {
                linked.prop('disabled', false);
                if (linked.data('empty') == '1') {
                    linked.html('<option value=""></option>');
                } else {
                    linked.html('');
                }
                options
                    .filter(function () {
                        return $(this).data('main') == val;
                    })
                    .clone()
                    .appendTo(linked);
            } else {
                linked.prop('disabled', true);
            }
        }
    }

</script>