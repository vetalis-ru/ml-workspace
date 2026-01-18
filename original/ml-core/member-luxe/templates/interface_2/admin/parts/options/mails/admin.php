<div id="header-tab-mails-admin">
    <div class="wpm-control-row wpm-inner-tab-content">
        <h4>
            <?php wpm_render_partial('options/checkbox-row', 'admin', array('label' => __('Уведомление об ответе на домашнее задание', 'mbl_admin'), 'key' => 'letters.response_admin.enabled', 'default' => 'on')) ?>
        </h4>
        <div class="wpm-row">
            <label>
                <?php _e('Заголовок письма', 'mbl_admin') ?><br>
                <input type="text" name="main_options[letters][response_admin][title]"
                       value="<?php echo wpm_get_option('letters.response_admin.title') ?>"
                       class="large-text">
            </label>

        </div>
        <div class="wpm-control-row">
            <?php wp_editor(stripslashes(wpm_get_option('letters.response_admin.content')), 'wpm_letter_response_admin', array('textarea_name' => 'main_options[letters][response_admin][content]', 'editor_height' => 250));?>
        </div>
        <div class="wpm-help-wrap">
            <div class="flex-row flex-wrap">
                <div class="flex-col-4 pr-10">
                    <span class="code-string">[material_name]</span> - <?php _e('название материала', 'mbl_admin') ?><br>
                    <span class="code-string">[user_name]</span> - <?php _e('имя пользователя', 'mbl_admin') ?><br>
                    <span class="code-string">[user_email]</span> - <?php _e('email пользователя', 'mbl_admin') ?>
                </div>
                <div class="flex-col-4 pr-10">
                    <span class="code-string">[user_response]</span> - <?php _e('текст ответа', 'mbl_admin') ?><br>
                    <span class="code-string">[material_url]</span> - <?php _e('ссылка на материал', 'mbl_admin') ?><br>
                    <span class="code-string">[admin_url]</span> - <?php _e('ссылка на панель управления', 'mbl_admin') ?>
                </div>
            </div>
        </div>
    </div>

    <div class="wpm-control-row wpm-inner-tab-content">
        <h4>
            <?php wpm_render_partial('options/checkbox-row', 'admin', array('label' => __('Уведомление о комментарии к ответу на домашнее задание', 'mbl_admin'), 'key' => 'letters.response_review_admin.enabled', 'default' => 'on')) ?>
        </h4>

        <div class="wpm-row">
            <label>
                <?php _e('Заголовок письма', 'mbl_admin') ?><br>
                <input type="text" name="main_options[letters][response_review_admin][title]"
                       value="<?php echo wpm_get_option('letters.response_review_admin.title') ?>"
                       class="large-text">
            </label>

        </div>
        <div class="wpm-control-row">
            <?php wp_editor(stripslashes(wpm_get_option('letters.response_review_admin.content')), 'wpm_letter_response_review_admin', array('textarea_name' => 'main_options[letters][response_review_admin][content]', 'editor_height' => 250));?>
        </div>
        <div class="wpm-help-wrap">
            <div class="flex-row flex-wrap">
                <div class="flex-col-4 pr-10">
                    <span class="code-string">[material_name]</span> - <?php _e('название материала', 'mbl_admin') ?><br>
                    <span class="code-string">[user_name]</span> - <?php _e('имя пользователя', 'mbl_admin') ?><br>
                    <span class="code-string">[user_email]</span> - <?php _e('email пользователя', 'mbl_admin') ?><br>
                    <span class="code-string">[user_response]</span> - <?php _e('текст ответа', 'mbl_admin') ?>
                </div>
                <div class="flex-col-4 pr-10">
                    <span class="code-string">[author]</span> - <?php _e('автор комментария', 'mbl_admin') ?><br>
                    <span class="code-string">[response_review]</span> - <?php _e('текст комментария', 'mbl_admin') ?><br>
                    <span class="code-string">[material_url]</span> - <?php _e('ссылка на материал', 'mbl_admin') ?><br>
                    <span class="code-string">[admin_url]</span> - <?php _e('ссылка на панель управления', 'mbl_admin') ?>
                </div>
            </div>
        </div>
    </div>
</div>