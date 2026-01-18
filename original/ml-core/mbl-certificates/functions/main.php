<?php
require_once "plugin.php";
require_once "assets.php";

function getFIO($userId): string
{
    return get_user_meta($userId, 'last_name', true) . ' ' .
        get_user_meta($userId, 'first_name', true) . ' ' .
        get_user_meta($userId, 'surname', true);
}