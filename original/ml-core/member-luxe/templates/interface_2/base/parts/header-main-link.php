<?php if (wpm_user_is_active()) : ?>
    <a class="nav-item hidden-md hidden-xs hidden-sm"
       href="<?php echo get_permalink(apply_filters('mbl_home_id', wpm_get_option('home_id'))); ?>">
        <span class="iconmoon icon-home"></span>
        <?php _e('Главная', 'mbl'); ?>
    </a>
<?php endif; ?>
