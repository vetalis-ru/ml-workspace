<?php

namespace Mbl\AutoResponder;

class Plugin
{
    public function path($path = ''): string
    {
        return MBL_AUTO_RESPONDER_PATH . '/' . $path;
    }

    public function uri($path = ''): string
    {
        return MBL_AUTO_RESPONDER_URI . '/' . $path;
    }

    public function assets_uri($uri = ''): string
    {
        return MBL_AUTO_RESPONDER_URI . 'assets/' . $uri;
    }

    public function version(): string
    {
        return MBL_AUTO_RESPONDER_VERSION;
    }

    public function assets_version(): string
    {
        return $this->is_debug() ? time() : $this->version();
    }

    public function prefix(): string
    {
        return 'mblar_mailing';
    }

    public function rest_namespace(): string
    {
        return "{$this->prefix()}/v1";
    }

    public function register_rest_route($route, $args = array(), $override = false): string
    {
        return register_rest_route($this->rest_namespace(), $route, $args, $override);
    }

    public function rest_url($route = ''): string
    {
        return rest_url("{$this->prefix()}/v1/{$route}");
    }

    public function js_data($addition = []): array
    {
        return [
                'plugin_uri' => admin_url( 'admin.php?page=' . $this->prefix() ),
                'assets_uri' => $this->assets_uri(),
                'rest' => $this->rest_url(),
                'wpm_auto_login_shortcodes_tips' => [
                    [ 'tag' => '[auth_link]', 'label' => __( 'ссылка для входа', 'mbl_admin' ) ],
                    [
                        'tag' => '[auth_link redirect="' . site_url() . '"]',
                        'label' => __( 'ссылка для входа с перенаправлением', 'mbl_admin' )
                    ],
                ]
            ] + $addition;
    }

    public function is_debug() {
        return MBL_AUTO_RESPONDER_DEBUG;
    }
}
