<?php

namespace Mbl\Api;

use WP_REST_Request;
use WP_REST_Response;

class RtOrder
{
    public function response(WP_REST_Request $request): WP_REST_Response
    {

        $email = $request->get_param('email');
        $product_id = (array)$request->get_param('product_id');

        $products = [];
        foreach ($product_id as $id) {
            $product = wc_get_product($id);
            if (!$product) {
                return new WP_REST_Response(['error' => "Product product_id: $id not found"], '404');
            }
            $products[] = $product;
        }

        $user = (new Customer($email))->wpUser([
            'first_name' => $request->get_param('first_name'),
            'last_name' => $request->get_param('last_name'),
            'surname' => $request->get_param('patronymic'),
            'phone' => $request->get_param('phone'),
            'custom1' => $request->get_param('custom1'),
            'custom2' => $request->get_param('custom2'),
            'custom3' => $request->get_param('custom3'),
            'custom4' => $request->get_param('custom4'),
            'custom5' => $request->get_param('custom5'),
            'custom6' => $request->get_param('custom6'),
            'custom7' => $request->get_param('custom7'),
            'custom8' => $request->get_param('custom8'),
            'custom9' => $request->get_param('custom9'),
            'custom10' => $request->get_param('custom10'),
            'custom1textarea' => $request->get_param('custom1textarea'),
        ]);
        $user_id = $user->ID;

        $order = wc_create_order(['customer_id' => $user_id]);
        foreach ($products as $product) {
            $order->add_product($product);
        }
        $order->calculate_totals();
        $order->update_status('completed');

        return new WP_REST_Response(['order' => $order->get_id()]);
    }
}
