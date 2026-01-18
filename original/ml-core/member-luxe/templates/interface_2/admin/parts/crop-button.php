<a
    href="#"
    <?php echo $key ? '' : 'style="display:none;"'; ?>
    class="wpm-crop-media-button button"
    data-action="wpm_crop_action"
    data-croppable="wpm_<?php echo $id; ?>"
    data-orig="wpm_<?php echo $id; ?>_original"
    data-image="wpm-<?php echo $id; ?>-preview"
    data-width="<?php echo $w; ?>"
    data-height="<?php echo $h; ?>"
    data-save-title="<?php _e('Сохранить', 'mbl_admin') ?>"
    data-cancel-title="<?php _e('Отменить', 'mbl_admin') ?>"
    data-preview-title="<?php _e('Предпросмотр', 'mbl_admin') ?>"
    data-id="<?php echo $id; ?>"><?php _e('Редактировать', 'mbl_admin') ?></a>