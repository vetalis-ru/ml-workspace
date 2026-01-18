<?php

class MBLRView
{
    public static function includePartial($view, $domain = 'public')
    {
        include(self::_partialPath($view, $domain));
    }

    public static function getPartial($view, $domain = 'public', $variables = array())
    {
        return self::_partial(self::_partialPath($view, $domain), $variables);
    }

    private static function _partial($path, $variables = array())
    {
        extract($variables);

        ob_start();
        require($path);

        return ob_get_clean();
    }

    private static function _partialPath($view, $domain = 'public')
    {
        $path = "/templates/{$domain}/{$view}.php";

        return MBLR_DIR . $path;
    }

}