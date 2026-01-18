<?php $category = new MBLCategory(get_term(get_queried_object()->term_id), true, true) ?>
<?php $showPage = is_user_logged_in() || wpm_get_option('main.opened'); ?>
<?php wpm_render_partial('head', 'base', compact('post')) ?>
<?php wpm_render_partial('navbar') ?>
<div class="site-content">
    <?php if (post_password_required()) : ?>
        <?php wpm_render_partial('header-cover'); ?>
        <div class="wpm-protected">
            <?php echo get_the_password_form(); ?>
        </div>
    <?php elseif ($showPage) : ?>
        <?php wpm_render_partial('header-cover'); ?>
        <?php wpm_render_partial('breadcrumbs', 'base', compact('category')); ?>
        <?php wpm_render_partial('category-description', 'base', compact('category')); ?>
        <?php if ($category->hasChildren()) : ?>
            <?php wpm_render_partial('categories', 'base', array('categoryCollection' => $category->getChildrenCollection(), 'category' => $category)) ?>
        <?php else : ?>
            <?php wpm_render_partial('materials', 'base', compact('category')) ?>
        <?php endif; ?>
    <?php else : ?>
        <?php wpm_render_partial('header-cover', 'base', array('alias' => 'login')); ?>
        <?php wpm_render_partial('restricted') ?>
    <?php endif; ?>
</div>
<?php wpm_render_partial('footer') ?>
<?php wpm_render_partial('footer-scripts') ?>
