<?php

function mbl_access_include_partial($view, $domain = 'public')
{
    MBL_ACCESS_View::includePartial($view, $domain);
}

function mbl_access_render_partial($view, $domain = 'public', $variables = array(), $return = false)
{
    $result = MBL_ACCESS_View::getPartial($view, $domain, $variables);

    if ($return) {
        return $result;
    } else {
        echo $result;
    }
}

function mbl_access_update_option($key, $value)
{
    $main_options = get_option('wpm_main_options');

    if(!isset($main_options) || !is_array($main_options)) {
        $main_options = array();
    }

    update_option('wpm_main_options', wpm_array_set($main_options, $key, $value));
}


