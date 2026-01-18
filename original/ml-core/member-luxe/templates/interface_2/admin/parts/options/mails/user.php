<div id="header-tab-mails-user">
    <div class="wpm-control-row wpm-inner-tab-content">
        <h4>
            <?php wpm_render_partial('options/checkbox-row', 'admin', array('label' => __('Уведомление о статусе домашнего задания', 'mbl_admin'), 'key' => 'letters.response_status.enabled', 'default' => 'on')) ?>
        </h4>
        <div class="wpm-row">
            <label>
                <?php _e('Заголовок письма', 'mbl_admin') ?><br>
                <input type="text" name="main_options[letters][response_status][title]"
                       value="<?php echo wpm_get_option('letters.response_status.title') ?>"
                       class="large-text">
            </label>

        </div>
        <div class="wpm-control-row">
            <?php wp_editor(stripslashes(wpm_get_option('letters.response_status.content')), 'wpm_letter_response_status', array('textarea_name' => 'main_options[letters][response_status][content]', 'editor_height' => 250));?>
        </div>
        <div class="wpm-help-wrap">
            <div class="flex-row flex-wrap">
                <div class="flex-col-4 pr-10">
                    <span class="code-string">[material_name]</span> - <?php _e('название материала', 'mbl_admin') ?><br>
                    <span class="code-string">[user_name]</span> - <?php _e('имя пользователя', 'mbl_admin') ?>
                </div>
                <div class="flex-col-4 pr-10">
                    <span class="code-string">[status]</span> - <?php _e('статус', 'mbl_admin') ?><br>
                    <span class="code-string">[material_url]</span> - <?php _e('ссылка на материал', 'mbl_admin') ?>
                </div>
                <div>
                    <?php wpm_auto_login_shortcodes_tips() ?>
                </div>
            </div>
        </div>
    </div>

    <div class="wpm-control-row wpm-inner-tab-content">
        <h4>
            <?php wpm_render_partial('options/checkbox-row', 'admin', array('label' => __('Уведомление о комментарии к ответу на домашнее задание', 'mbl_admin'), 'key' => 'letters.response_review.enabled', 'default' => 'on')) ?>
        </h4>
        <div class="wpm-row">
            <label>
                <?php _e('Заголовок письма', 'mbl_admin') ?><br>
                <input type="text" name="main_options[letters][response_review][title]"
                       value="<?php echo wpm_get_option('letters.response_review.title') ?>"
                       class="large-text">
            </label>

        </div>
        <div class="wpm-control-row">
            <?php wp_editor(stripslashes(wpm_get_option('letters.response_review.content')), 'wpm_letter_response_review', array('textarea_name' => 'main_options[letters][response_review][content]', 'editor_height' => 250));?>
        </div>
        <div class="wpm-help-wrap">
            <div class="flex-row flex-wrap">
                <div class="flex-col-4 pr-10">
                    <span class="code-string">[user_name]</span> - <?php _e('имя пользователя', 'mbl_admin') ?><br>
                    <span class="code-string">[material_name]</span> - <?php _e('название материала', 'mbl_admin') ?><br>
                    <span class="code-string">[material_url]</span> - <?php _e('ссылка на материал', 'mbl_admin') ?>
                </div>
                <div class="flex-col-4 pr-10">
                    <span class="code-string">[user_response]</span> - <?php _e('текст ответа', 'mbl_admin') ?><br>
                    <span class="code-string">[response_review]</span> - <?php _e('текст комментария', 'mbl_admin') ?>
                </div>
                <div>
                    <?php wpm_auto_login_shortcodes_tips() ?>
                </div>
            </div>
        </div>
    </div>

    <div class="wpm-control-row wpm-inner-tab-content">
        <h4>
            <?php wpm_render_partial('options/checkbox-row', 'admin', array('label' => __('Уведомление о доступном новом материале', 'mbl_admin'), 'key' => 'letters.new_material_access.enabled', 'default' => 'on')) ?>
        </h4>
        <div class="wpm-row">
            <label>
                <?php _e('Заголовок письма', 'mbl_admin') ?><br>
                <input type="text" name="main_options[letters][new_material_access][title]"
                       value="<?php echo wpm_get_option('letters.new_material_access.title') ?>"
                       class="large-text">
            </label>

        </div>
        <div class="wpm-control-row">
            <?php wp_editor(stripslashes(wpm_get_option('letters.new_material_access.content')), 'wpm_letter_new_material_access', array('textarea_name' => 'main_options[letters][new_material_access][content]', 'editor_height' => 250));?>
        </div>
        <div class="wpm-help-wrap">
            <div class="flex-row flex-wrap">
                <div class="flex-col-4 pr-10">
                    <span class="code-string">[material_name]</span> - <?php _e('название материала', 'mbl_admin') ?><br>
                    <span class="code-string">[material_url]</span> - <?php _e('ссылка на материал', 'mbl_admin') ?>
                </div>
                <div class="flex-col-4 pr-10">
                    <span class="code-string">[user_name]</span> - <?php _e('имя пользователя', 'mbl_admin') ?><br>
                    <span class="code-string">[user_email]</span> - <?php _e('email пользователя', 'mbl_admin') ?>
                </div>
                <div>
                    <?php wpm_auto_login_shortcodes_tips() ?>
                </div>
            </div>
        </div>
    </div>
</div>
