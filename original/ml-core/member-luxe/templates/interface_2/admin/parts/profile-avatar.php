<?php $avatar = wpm_get_avatar($user->ID, 150, null, false) ?>
<?php $hasAvatar = !empty($avatar) ?>
<table class="form-table">
    <tr>
        <th><label for="avatar"><?php _e('Аватар', 'mbl_admin') ?></label></th>
        <td>
            <input type="hidden" id="avatar" name="avatar"
                   value="<?php echo get_user_meta($user->ID, 'avatar', true); ?>"
                   class="wide">

            <div class="wpm-avatar-preview-wrap">
                <div class="wpm-avatar-preview-box">
                    <?php echo get_avatar($user->ID); ?>
                </div>
            </div>
            <div class="wpm-control-row">
                <p>
                    <button type="button" class="wpm-media-upload-button button"
                            data-id="avatar"><?php _e('Загрузить', 'mbl_admin') ?></button>
                    &nbsp;&nbsp; <a id="delete-wpm-avatar"
                                    class="wpm-delete-media-button button submit-delete"
                                    data-id="avatar"<?php if (!$hasAvatar) echo 'style="display:none"'; ?>><?php _e('Удалить', 'mbl_admin'); ?></a>
                </p>
            </div>
        </td>
    </tr>
</table>