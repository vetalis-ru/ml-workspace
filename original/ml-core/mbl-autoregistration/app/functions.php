<?php

function mblr_include_partial($view, $domain = 'public')
{
    MBLRView::includePartial($view, $domain);
}

function mblr_render_partial($view, $domain = 'public', $variables = array(), $return = false)
{
    $result = MBLRView::getPartial($view, $domain, $variables);

    if ($return) {
        return $result;
    } else {
        echo $result;
    }
}

function mblr_update_option($key, $value)
{
    $main_options = get_option('wpm_main_options');

    if(!isset($main_options) || !is_array($main_options)) {
        $main_options = array();
    }

    update_option('wpm_main_options', wpm_array_set($main_options, $key, $value));
}


