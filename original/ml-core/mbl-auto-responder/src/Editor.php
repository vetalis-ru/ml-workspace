<?php

namespace Mbl\AutoResponder;

class Editor
{
    private Plugin $plugin;

    /**
     * @param Plugin $plugin
     */
    public function __construct(Plugin $plugin)
    {
        if (!function_exists('mblar_header_templates')) {
            require_once $plugin->path('blocks/main.php');
        }
        $this->plugin = $plugin;
    }

    public function header(int $id): array
    {
        $headers = mblar_header_templates($this->plugin);

        return current(array_filter($headers, fn($header) => $header['id'] === $id));
    }

    public function footer(int $id): array
    {
        $footers = mblar_footer_templates($this->plugin);

        return current(array_filter($footers, fn($footer) => $footer['id'] === $id));
    }

    public function common(int $id = 1): array
    {
        return mblar_get_template(mblar_common());
    }

    public function content(int $id = 1): array
    {
        return mblar_get_template(mblar_content());
    }

    public function state(): array
    {
        if (!empty($_GET['edit'])) {
            global $wpdb;
            $mailTemplates = new MailTemplates($wpdb);
            $template_data = $mailTemplates->byId((int)$_GET['edit']);
            $props = json_decode($template_data['data'], true);
            $template = [
                'id' => (int)$template_data['id'],
                'name' => $template_data['name'],
                'common' => mblar_template_with_props($this->common(), $props['common']['props']),
                'header' => mblar_template_with_props($this->header($props['header']['id']), $props['header']['props']),
                'content' => mblar_template_with_props($this->content(), $props['content']['props']),
                'footer' => mblar_template_with_props($this->footer($props['footer']['id']), $props['footer']['props']),
            ];
        } else {
            $template = mblar_default_template($this->plugin);
        }
        return $this->plugin->js_data([
            'template' => $template,
            'variants' => [
                'headers' => array_map(
                    fn($header) => ['id' => $header['id'], 'name' => $header['name'], 'preview' => $header['preview']],
                    mblar_header_templates($this->plugin)
                ),
                'footers' => array_map(
                    fn($footer) => ['id' => $footer['id'], 'name' => $footer['name'], 'preview' => $footer['preview']],
                    mblar_footer_templates($this->plugin)
                ),
            ]
        ]);
    }
}