<?php
include_once(MBLR_DIR . '/src/MBLRView.php');
include_once(MBLR_DIR . '/src/MBLRAdmin.php');
include_once(MBLR_DIR . '/src/MBLRPublic.php');
include_once(MBLR_DIR . '/src/MBLRCore.php');

include_once(MBLR_DIR . '/app/functions.php');

add_action('init', 'mblr_init', 1);

function mblr_init()
{
    new MBLRCore();
}
