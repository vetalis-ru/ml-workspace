<?php
function mblc_plugin_path($relative_path = ''): string
{
    return dirname(__DIR__) . '/' . $relative_path;
}