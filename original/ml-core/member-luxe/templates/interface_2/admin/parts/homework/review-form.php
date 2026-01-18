<?php /** @var MBLPage $mblPage */ ?>
<div class="discussion-form-wrap">
    <form class="discussion-form"
          id="wpm_response_review_form_<?php echo $response->id; ?>"
          action="" method="post"
          data-id="<?php echo $mblPage->getHomeworkResponse('id', $response->user_id); ?>_new"
          data-response-id="<?php echo $mblPage->getHomeworkResponse('id', $response->user_id); ?>"
          data-name="wpm_review">
        <div class="comment-label">
            <i class="fa fa-commenting" aria-hidden="true"></i>
            <?php _e('Написать комментарий', 'mbl_admin'); ?>
        </div>
        <div class="author">
            <i class="fa fa-user-circle-o" aria-hidden="true"></i>
            <?php echo wpm_get_current_user('display_name'); ?>
        </div>

        <div class="form">
            <?php wpm_editor_admin('', 'wpm_response_review_content_' . $response->id, array(), true) ?>
            <div class="flex-row flex-wrap">
                <div class="form-group file-upload-row flex-col-9">
                    <?php wpm_render_partial('file-upload', 'admin', array('id' => ($response->id.'_new'), 'name' => 'wpm_review')); ?>
                </div>
                <div class="submit-row flex-col-3">
                    <button
                        type="submit"
                        name="button"
                        class="button button-primary"
                    ><?php _e('Отправить', 'mbl_admin'); ?></button>
                </div>
            </div>
        </div>
    </form>
</div>
<script type="text/javascript">
    jQuery(function ($) {
        var reviewForm = $('form#wpm_response_review_form_<?php echo $response->id; ?>');

        reviewForm.on('submit', function (e) {
            var button = reviewForm.find('[type="submit"]');

            button.text('<?php _e('Загрузка...', 'mbl_admin'); ?>');
            button.addClass('progress-button-active');
            button.prop('disabled', true);

            $.post(
                ajaxurl,
                {
                    action : 'wpm_add_response_review_action',
                    response_id : reviewForm.data('response-id'),
                    review_content : $('#wpm_response_review_content_<?php echo $response->id; ?>').summernote('code'),
                    last_user_id : '<?php echo $lastUserId; ?>',
                    tpl : 'hw'
                },
                function ($html) {
                    $('#wpm_homework_reviews_<?php echo $response->id; ?>').append($html);
                    button.text('<?php _e('Отправить', 'mbl'); ?>');
                    $('#wpm_response_review_content_<?php echo $response->id; ?>').summernote('code', '');
                    reviewForm.find('.wpm-fileupload').trigger('reload_files');
                    button.removeClass('progress-button-active');
                    button.prop('disabled', false);
                }
            );

            e.preventDefault();
            return false;
        });

        //------------------------- radio, checkbox, select

        $('.wpm_radio input:checked').each(function () {
            $(this).parent().addClass('wpm_checked');
        });

        $(document).on('change', '.wpm_radio input', function () {
            var name = $(this).attr('name');
            $('input[name = ' + name + ']').each(function () {
                $(this).parent().removeClass('wpm_checked');
            });
            $(this).parent().addClass('wpm_checked');
        });

        $(document).on('change', '.wpm_subsc_thumb input', function () {
            if ($(this).hasClass('trial')) return false;
            var name = $(this).attr('name');
            $('input[name = ' + name + ']').each(function () {
                $(this).parent().removeClass('wpm_checked');
            });
            $(this).parent().addClass('wpm_checked');
        });
        $(document).on('change', '.ps_bullets_form input, .p_cbutton input, .ps_satisfaction input, .ps_arrows input, .ps_bonus input, .ps_cbutton input, .ps_timer_image input, .wpp_header input', function () {
            var name = $(this).attr('name');
            $('input[name = ' + name + ']').each(function () {
                $(this).parent().removeClass('wpm_checked');
            });
            $(this).parent().addClass('wpm_checked');
        });


        $('.wpm_checkbox input:checked, .p_cbutton input:checked').each(function () {
            $(this).parent().addClass('wpm_checked');
        });

        $(document).on('change', '.wpm_checkbox input:checkbox', function () {
            $(this).parent().toggleClass('wpm_checked');
        });

        $(document).on('change', '.wpm_checkbox input:radio', function () {
            $('input[name=' + $(this).attr("name") + ']').each(function () {
                $(this).parent().removeClass('wpm_checked');
            });
            $(this).parent().toggleClass('wpm_checked');
        });
        $(document).on('click', '.wpm_checkbox input:disabled, .wpm_radio input:disabled', function () {
            return false;
        });


//----------------------------- video border style  -------

        $('.wpm_radio_V input:checked').each(function () {
            $(this).parent().addClass('wpm_checked');
        });

        $(document).on('change', '.wpm_radio_v input', function () {
            if ($('input[name=video_border]:checked').val() == 'yes') {
                var name = $(this).attr('name');
                $('input[name = ' + name + ']').each(function () {
                    $(this).parent().removeClass('wpm_checked');
                });
                $(this).parent().addClass('wpm_checked');
            }
        });

        $(document).on('change', 'input[name=video_border]', function () {
            if ($(this).val() == 'yes') {
                $('#video-width, #video-height').attr('disabled', 'disabled');
                $('.video_border_560 input').click();
                $('.video_styles label:first-child input').click();
            } else {
                $('#video-width, #video-height').removeAttr('disabled');
                $('.video_border_sizes label, .video_styles > label').removeClass('wpm_checked');
                $('.video_border_sizes input:checked, .video_styles input:checked').removeAttr('checked');
            }
        });
        $(document).on('click', 'input[name=video_border_size]', function () {

            if ($('input[name=video_border]:checked').val() == 'yes') {
                if ($(this).val() == '480x270') {
                    $('#video-width').val('480');
                    $('#video-height').val('270');
                }
                if ($(this).val() == '560x315') {
                    $('#video-width').val('560');
                    $('#video-height').val('315');
                }
                if ($(this).val() == '640x360') {
                    $('#video-width').val('640');
                    $('#video-height').val('360');
                }
                if ($(this).val() == '720x405') {
                    $('#video-width').val('720');
                    $('#video-height').val('405');
                }
            }
        });
    });
</script>