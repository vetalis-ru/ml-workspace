<?php
add_action( 'pre_get_posts', 'mblc_add_event_table_filters_handler' );
function mblc_add_event_table_filters_handler( $query ){

    $cs = function_exists('get_current_screen') ? get_current_screen() : null;

    // убедимся что мы на нужной странице админки
    if( ! is_admin() || empty($cs->post_type) || $cs->post_type != 'product' || $cs->id != 'edit-product' )
        return;


    if(isset($_GET['product_ids']) && !empty($_GET['product_ids'])){
        $product_ids = array_map('intval', explode(',', $_GET['product_ids']));
        $query->set( 'post__in', $product_ids);
    }
}
