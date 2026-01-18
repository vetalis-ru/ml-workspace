<?php
if (!function_exists('wpm_tree_post_ids')) {
    function wpm_tree_post_ids($termIds)
    {
        return get_posts(array(
                'post_type' => 'wpm-page',
                'fields' => 'ids',
                'posts_per_page' => -1,
                'tax_query' => array(
                    array(
                        'taxonomy' => 'wpm-category',
                        'field' => 'term_id',
                        'terms' => implode(',', $termIds)
                    )
                ),
            )
        );
    }
}

if (!function_exists('wpm_get_comments_count')) {
    function wpm_get_comments_count($mBLCategory)
    {
        if (!isset($mBLCategory->_treeCommentIds)) {
            $postIds = $mBLCategory->getTreePostIds();
            if (!empty($postIds)) {
                $mBLCategory->_treeCommentIds = get_comments(array(
                    'post__in' => implode(',', $postIds),
                    'fields' => 'ids',
                    'status' => 'approve'
                ));
            } else {
                $mBLCategory->_treeCommentIds = array();
            }
        }

        return count($mBLCategory->_treeCommentIds);
    }
}

if (!function_exists('wpm_get_post_level_ids')) {
    function wpm_get_post_level_ids($postId)
    {
        return wp_get_post_terms($postId, 'wpm-levels', ["fields" => "ids"]);
    }
}

if (!function_exists('wpm_training_schedule_posts')) {
    function wpm_training_schedule_posts($term_id)
    {
        return get_posts(array(
                'post_type' => 'wpm-page',
                'posts_per_page' => -1,
                'tax_query' => array(
                    array(
                        'taxonomy' => 'wpm-category',
                        'field' => 'term_id',
                        'terms' => $term_id
                    )
                ),
                'orderby' => 'menu_order post_date',
                'order' => 'ASC'
            )
        );
    }
}

if (!function_exists('wpm_add_breadcrumb_ancestor')) {
    function wpm_add_breadcrumb_ancestor(&$breadcrumbs, $ancestor)
    {
        $term = new MBLCategory(get_term($ancestor));
        array_unshift($breadcrumbs, array(
            'name' => $term->getName(),
            'link' => $term->getLink(),
            'icon' => 'folder-open-o'
        ));
    }
}
