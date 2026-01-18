<?php

use Mbl\AutoResponder\Editor;
use Mbl\AutoResponder\MailTemplates;
use Mbl\AutoResponder\Options;
use Mbl\AutoResponder\Plugin;
use Mbl\AutoResponder\Texts;

function mblar_permission_callback(): bool
{
    return true;
}

add_action('rest_api_init', function () {
    $plugin = new Plugin();
    $plugin->register_rest_route(
        '/variant/header/(?P<id>\d+)',
        [
            'methods' => 'GET',
            'callback' => function (WP_REST_Request $request) {
                $id = (int)$request['id'];

                return (new Editor(new Plugin()))->header($id);
            },
            'permission_callback' => 'mblar_permission_callback'
        ]
    );
    $plugin->register_rest_route(
        '/variant/footer/(?P<id>\d+)',
        [
            'methods' => 'GET',
            'callback' => function (WP_REST_Request $request) {
                $id = (int)$request['id'];

                return (new Editor(new Plugin()))->footer($id);
            },
            'permission_callback' => 'mblar_permission_callback'
        ]
    );
    $plugin->register_rest_route(
        '/editor/create',
        [
            'methods' => 'POST',
            'callback' => function (WP_REST_Request $request) {
                global $wpdb;
                $editor = new Editor(new Plugin());
                $state = json_decode(stripslashes($_POST['template']), true);
                $mailTemplates = new MailTemplates($wpdb);
                $id = $mailTemplates->add($state['name'], mblar_setting_to_array($editor, $state));

                return ['ok' => true, 'id' => $id];
            },
            'permission_callback' => 'mblar_permission_callback'
        ]
    );
    $plugin->register_rest_route(
        '/editor/save',
        [
            'methods' => 'POST',
            'callback' => function (WP_REST_Request $request) {
                global $wpdb;
                $editor = new Editor(new Plugin());
                $state = json_decode(stripslashes($_POST['template']), true);
                $mailTemplates = new MailTemplates($wpdb);
                $res = $mailTemplates->save((int)$state['id'], $state['name'], mblar_setting_to_array($editor, $state));

                return ['ok' => true, 'message' => $wpdb->last_error];
            },
            'permission_callback' => 'mblar_permission_callback'
        ]
    );
    $plugin->register_rest_route(
        '/editor/copy/(?P<id>\d+)',
        [
            'methods' => 'POST',
            'callback' => function (WP_REST_Request $request) {
                global $wpdb;
                $id = (int)$request['id'];
                $mailTemplates = new MailTemplates($wpdb);
                $template_id = $mailTemplates->copy($id);
                $template = $mailTemplates->byId($template_id);

                return ['ok' => true, 'template' => ['id' => $template['id'], 'name' => $template['name']]];
            },
            'permission_callback' => 'mblar_permission_callback'
        ]
    );
    $plugin->register_rest_route(
        '/editor/remove/(?P<id>\d+)',
        [
            'methods' => 'POST',
            'callback' => function (WP_REST_Request $request) {
                global $wpdb;
                $id = (int)$request['id'];
                $mailTemplates = new MailTemplates($wpdb);

                return ['ok' => true, 'message' => $mailTemplates->remove($id)];
            },
            'permission_callback' => 'mblar_permission_callback'
        ]
    );
    $plugin->register_rest_route(
        'options',
        [
            'methods' => 'GET',
            'callback' => function (WP_REST_Request $request) {
                return ['ok' => true, 'options' => (new Options())->list()];
            },
            'permission_callback' => 'mblar_permission_callback'
        ]
    );
    $plugin->register_rest_route(
        'options/save',
        [
            'methods' => 'POST',
            'callback' => function (WP_REST_Request $request) {
                $options = new Options();
                $options->save($_POST);

                return ['ok' => true, 'options' => $options->list()];
            },
            'permission_callback' => 'mblar_permission_callback'
        ]
    );
});