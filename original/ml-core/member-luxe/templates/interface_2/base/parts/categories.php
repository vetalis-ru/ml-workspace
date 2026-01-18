<?php /** @var MBLCategoryCollection $categoryCollection */ ?>
<?php $baseCategory = isset($category) ? $category : null; ?>
<section class="folders-row clearfix">
    <?php if (wpm_user_is_active()) : ?>
        <div class="container">
            <div class="row">
                <?php foreach ($categoryCollection->getCategories() as $category) : ?>
                    <?php if(apply_filters('mbl_show_category', true, $category, $baseCategory)): ?>
                    <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3 ">
                        <?php wpm_render_partial('folder', 'base', compact('category')) ?>
                    </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</section>
<?php if (wpm_user_is_active() && $baseCategory !== null) : ?>
    <?php wpm_render_partial('pagination', 'base', array('pager' => $baseCategory->getChildrenCollection())) ?>
<?php endif; ?>
