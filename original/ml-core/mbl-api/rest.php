<?php

namespace Mbl\Api;

use WP_Error;
use WP_REST_Request;

add_action('rest_api_init', 'Mbl\Api\rest_api');

function rest_api()
{
    register_rest_route('mbl/v1', '/order/', [
        'methods' => 'POST',
        'callback' => [new RtOrder(), 'response'],
        'permission_callback' => fn() => current_user_can('manage_options'),
        'args' => [
            'email' => [
                'required' => true,
                'validate_callback' => 'is_email',
                'sanitize_callback' => 'sanitize_email'
            ],
            'product_id' => [
                'required' => true,
                'validate_callback' => function ($param) {
                    $code = 'rest_invalid_param';
                    $message = __('Invalid product_id format');
                    if (is_array($param)) {
                        foreach ($param as $id) {
                            if (is_numeric($id)) {
                                continue;
                            }
                            return new WP_Error($code, $message, ['status' => 400]);
                        }
                        return true;
                    } elseif (!is_numeric($param)) {
                        return new WP_Error($code, $message, ['status' => 400]);
                    }

                    return true;
                },
                'sanitize_callback' => function ($param) {
                    if (is_array($param)) {
                        return array_map('absint', $param);
                    } else {
                        return absint($param);
                    }
                }
            ],
            "first_name" => [
                'required' => false,
                'sanitize_callback' => 'sanitize_text_field'
            ],
            "last_name" => [
                'required' => false,
                'sanitize_callback' => 'sanitize_text_field'
            ],
            "patronymic" => [
                'required' => false,
                'sanitize_callback' => 'sanitize_text_field'
            ],
            "phone" => [
                'required' => false,
                'sanitize_callback' => 'sanitize_text_field'
            ],
            "custom1" => [
                'required' => false,
                'sanitize_callback' => 'sanitize_text_field'
            ],
            "custom2" => [
                'required' => false,
                'sanitize_callback' => 'sanitize_text_field'
            ],
            "custom3" => [
                'required' => false,
                'sanitize_callback' => 'sanitize_text_field'
            ],
            "custom4" => [
                'required' => false,
                'sanitize_callback' => 'sanitize_text_field'
            ],
            "custom5" => [
                'required' => false,
                'sanitize_callback' => 'sanitize_text_field'
            ],
            "custom6" => [
                'required' => false,
                'sanitize_callback' => 'sanitize_text_field'
            ],
            "custom7" => [
                'required' => false,
                'sanitize_callback' => 'sanitize_text_field'
            ],
            "custom8" => [
                'required' => false,
                'sanitize_callback' => 'sanitize_text_field'
            ],
            "custom9" => [
                'required' => false,
                'sanitize_callback' => 'sanitize_text_field'
            ],
            "custom10" => [
                'required' => false,
                'sanitize_callback' => 'sanitize_text_field'
            ],
            "custom1textarea" => [
                'required' => false,
                'sanitize_callback' => 'sanitize_text_field'
            ],
        ]
    ]);
    register_rest_route('mbl/v1', '/webhook/form/', [
        'methods' => 'POST',
        'callback' => function (WP_REST_Request $request) {
            $webhooks = new Webhooks();
            $sort = 1;

            foreach ($_POST['hook'] as $hook) {
                if (empty($hook['destination'])) {
                    return new WP_Error('invalid_destination', 'Webhook url обязательное поле', array('status' => 400));
                }
                if (empty($hook['action'])) {
                    return new WP_Error('invalid_action', 'Webhook событие обязательное поле', array('status' => 400));
                }
            }

            foreach ($_POST['hook'] as $hook) {
                $id = is_numeric($hook['id']) ? (int)$hook['id'] : $hook['id'];
                if (is_int($id)) {
                    $webhooks->update($hook['id'], $hook['destination'], $hook['action'], $sort);
                } else {
                    $webhooks->add($hook['destination'], $hook['action'], $sort);
                }
                $sort++;
            }

            return ['ok' => true];
        },
        'permission_callback' => fn() => current_user_can('manage_options'),
    ]);
    register_rest_route('mbl/v1', '/webhook/(?P<id>\d+)', [
        'methods' => 'DELETE',
        'callback' => function (WP_REST_Request $request) {
            $webhooks = new Webhooks();
            $webhooks->delete($request->get_param('id'));
            return ['ok' => true];
        },
        'permission_callback' => function () {
            return current_user_can('manage_options');
        },
    ]);
    register_rest_route('mbl/v1', '/webhook/action', [
        'methods' => 'POST',
        'permission_callback' => fn() => true,
        'callback' => function (WP_REST_Request $request) {
            set_time_limit(0);
            ignore_user_abort(1);
            $json_data = file_get_contents("php://input");
            $data = json_decode($json_data, true);

            if (!isset($data['actions'])) {
                return '';
            }

            foreach ($data['actions'] as $action) {
                $webhooks = new Webhooks();
                $list = $webhooks->list([$action['type']]);
                foreach ($list as $item) {
                    $item->trigger($action);
                }
            }

            return 'ok';
        }
    ]);
}
