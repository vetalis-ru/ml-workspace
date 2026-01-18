<?php
add_action('init', 'wpm_utm_cookie');
function wpm_utm_cookie()
{
    if (wpm_has_request_utm()) {
        wpm_set_utm(wpm_request_utm());
    }
}

function wpm_set_utm($utm_items)
{
    $time = time() + intval(wpm_get_option('utm_expiration_days', 7)) * DAY_IN_SECONDS;
    setcookie('wpm_has_utm', '1', $time, COOKIEPATH, COOKIE_DOMAIN);
    foreach ($utm_items as $utm_key => $utm_value) {
        setcookie(wpm_utm_prefix($utm_key), $utm_value, $time, COOKIEPATH, COOKIE_DOMAIN);
    }
}

function wpm_get_utm(): array
{
    $result = [];
    if (wpm_has_utm()) {
        foreach (wpm_utm_keys() as $utm_key) {
            $key = wpm_utm_prefix($utm_key);
            $result[$utm_key] = $_GET[$utm_key] ?? $_COOKIE[$key] ?? '';
        }
    }
    return $result;
}

function wpm_utm_prefix($utm_key): string
{
    return "wpm_$utm_key";
}

function wpm_utm_keys(): array
{
    return ['utm_source', 'utm_medium', 'utm_campaign', 'utm_id', 'utm_term', 'utm_content'];
}

function wpm_has_utm(): bool
{
    return wpm_has_request_utm() || !empty($_COOKIE['wpm_has_utm']);
}

function wpm_request_utm(): array
{
    $utm_items = [];
    $utm_keys = wpm_utm_keys();
    foreach ($utm_keys as $utm_key) {
        $utm_items[$utm_key] = trim($_GET[$utm_key] ?? '');
    }
    return $utm_items;
}

function wpm_has_request_utm(): bool
{
    return !empty(array_filter(wpm_request_utm()));
}

function wpm_clean_utm()
{
    unset($_COOKIE['wpm_has_utm']);
    setcookie('wpm_has_utm', '', time() - 15 * 60);
    foreach (wpm_utm_keys() as $utm_key) {
        $key = wpm_utm_prefix($utm_key);
        unset($_COOKIE[$key]);
        setcookie($key, '', time() - 15 * 60);
    }
    add_filter('wpm_clear_utm', 'wpm_clean_utm_field', 10, 1);
}

function wpm_clean_utm_field(): bool
{
    return true;
}

function wpm_clean_utm_script(): string
{
    $utm_keys = [];
    foreach (wpm_utm_keys() as $utm_key) {
        $utm_keys[] = wpm_utm_prefix($utm_key);
    }
    $keys = json_encode($utm_keys);
    return "window.wpmClearUtmCookie = () => {
        const keys = $keys;
        document.cookie = 'wpm_has_utm=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
        keys.forEach(name => {
          document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;'
        });
    };
    ";
}
