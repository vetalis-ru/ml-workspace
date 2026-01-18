<?php /** @var string $key  */?>
<?php /** @var MBLPage $page */?>
<?php /** @var MBLCategory $category */ ?>

<?php if ($category->getMeta('autotraining_first_material') == 'shift') : ?>
    <a
        href="<?php echo admin_url('/edit-tags.php?action=edit&taxonomy=wpm-category&tag_ID=' . $category->getTermId() . '&post_type=wpm-page'); ?>#mbl-autotraining-settings"
        class="mbl-autotraining-map-category-shift"
        data-mbl-tooltip
        title="<?php _e('Смещение автотренинга в настройках рубрики', 'mbl_admin'); ?>"
        target="_blank"
    >
        <?php wpm_render_partial('autotraining/shift', 'admin', array('page' => $category, 'key' => 'first_material_shift', 'disableTooltip' => true)) ?>
    </a>
<?php elseif($page->getMeta('shift_is_on')) : ?>
    <span
        data-mbl-tooltip
        title="<?php _e('Смещение первого урока автотренинга не учитывается', 'mbl_admin'); ?>"
        class="dimmed"
    >
        <?php wpm_render_partial('autotraining/shift', 'admin', array('page' => $page, 'key' => 'shift_value', 'disableTooltip' => true)) ?>
    </span>
<?php endif; ?>
