<?php
function mbapi_custom_wpm_levels_taxonomy_columns($columns) {
    $new_columns = array();
    $new_columns['cb'] = $columns['cb'];
    $new_columns['term_id'] = 'ID';
    unset($columns['cb']);
    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;
    }
    return $new_columns;
}
add_filter("manage_edit-wpm-levels_columns", "mbapi_custom_wpm_levels_taxonomy_columns");

function mbapi_custom_wpm_levels_taxonomy_column_content($content, $column_name, $term_id) {
    if ($column_name === 'term_id') {
        return $term_id;
    }
    return $content;
}
add_action("manage_wpm-levels_custom_column", "mbapi_custom_wpm_levels_taxonomy_column_content", 10, 3);

function mbapi_custom_wpm_levels_taxonomy_column_style() {
    global $pagenow;
    if ($pagenow == 'edit-tags.php' && isset($_GET['taxonomy']) && $_GET['taxonomy'] == 'wpm-levels') {
        echo '<style>.column-term_id { width: 3em; }</style>';
    }
}
add_action('admin_head', 'mbapi_custom_wpm_levels_taxonomy_column_style');

