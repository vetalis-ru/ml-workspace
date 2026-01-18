<?php
require_once 'functions.php';
require_once 'admin-page.php';
function mblc_render_certificate_templates()
{
    require_once 'routing.php';
}

add_action('pre_get_terms', function (WP_Term_Query $query) {
    global $pagenow;
    if ($pagenow === 'edit-tags.php'
        && isset($_GET['taxonomy']) && $_GET['taxonomy'] === 'wpm-levels'
        && isset($_GET['include']) && !empty($_GET['include'])
    ) {
        $query->query_vars['include'] = $_GET['include'];
    }
}, 10, 1);