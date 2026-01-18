<?php

class MBLCache
{
    private static $storage = array();

    public static function get($key, $default = null) {
        if(is_array($key)) {
            $key = implode('_', $key);
        }

        if (array_key_exists($key, self::$storage)) {
            return self::$storage[$key];
        }

        return $default;
    }

    public static function set($key, $value)
    {
        if(is_array($key)) {
            $key = implode('_', $key);
        }

        self::$storage[$key] = $value;
    }
}