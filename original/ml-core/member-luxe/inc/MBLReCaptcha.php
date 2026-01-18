<?php

class MBLReCaptcha
{
    private static $result = false;

    public static function check($page = 'login', $request = null)
    {
        if(!wpm_option_is('main.enable_captcha', 'on') || !wpm_option_is('main.enable_captcha_' . $page, 'on')) {
            return true;
        }

        $params = self::_getParams($request);
        $url = 'https://www.google.com/recaptcha/api/siteverify';

        if ($params['response'] !== false) {
            if (!self::$result) {
                $url = "{$url}?secret={$params['secret']}&response={$params['response']}&remoteip={$params['ip']}";
                $response = wp_remote_get($url);
                if (!is_wp_error($response)) {
                    self::$result = json_decode(wp_remote_retrieve_body($response));
                } else {
                    self::$result = (object)array('success' => false, 'wp_error' => $response);
                }
            }

            return self::$result->success;
        }

        return false;
    }

    private static function _getParams($request = null)
    {
        $secret = wpm_get_option('main.captcha_secret');
        $response = is_array($request) && isset($request['g-recaptcha-response'])
            ? $request['g-recaptcha-response']
            : (isset($_REQUEST['g-recaptcha-response']) ? $_REQUEST['g-recaptcha-response'] : false);
        $ip = MBLStats::getIP();

        return compact('secret', 'response', 'ip');
    }
}