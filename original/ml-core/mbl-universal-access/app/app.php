<?php
include_once(MBL_ACCESS_DIR . '/src/MBL_ACCESS_View.php');
include_once(MBL_ACCESS_DIR . '/src/MBL_ACCESS_Admin.php');
include_once(MBL_ACCESS_DIR . '/src/MBL_ACCESS_Public.php');
include_once(MBL_ACCESS_DIR . '/src/MBL_ACCESS_Core.php');

include_once(MBL_ACCESS_DIR . '/app/functions.php');

add_action('init', 'mbl_access_init', 1);

function mbl_access_init()
{
    new MBL_ACCESS_Core();
}
