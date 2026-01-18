<div class="mbl-universal-autoreg-row">
    <div class="wpm-control-row">
        <label>(<?php echo __('Для существующих пользователей', 'mbl_admin'); ?>) <?php echo __('Перенаправлять на URL', 'mbl_admin'); ?>:<br>
            <input type="text"
                   name="mblr_auto_registration[redirect_link]"
                   class="large-text"
                   placeholder="<?php echo wpm_get_start_url(); ?>"
                   value="">
        </label>
    </div>
    <div class="wpm-control-row">
        <label>(<?php echo __('Для новых пользователей', 'mbl_admin'); ?>) <?php echo __('Перенаправлять на URL', 'mbl_admin'); ?>:<br>
            <input type="text"
                   name="mblr_auto_registration[redirect_link_new_users]"
                   class="large-text"
                   placeholder="<?php echo wpm_get_start_url(); ?>"
                   value="">
        </label>
    </div>
</div>