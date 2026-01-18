<?php

namespace Mbl\Api;

class SmWebhook implements Webhook
{
    private int $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function toArray(): array
    {
        global $wpdb;
        $sql = "SELECT * 
                FROM {$wpdb->prefix}memberlux_hook AS h 
                LEFT JOIN {$wpdb->prefix}memberlux_hook_action AS a ON h.id = a.hook_id
                WHERE id = %d";
        $data = $wpdb->get_results($wpdb->prepare($sql, $this->id), ARRAY_A);
        $actions = [];
        foreach ($data as $item) {
            $actions[] = $item['action'];
        }

        return [
            'id' => $this->id,
            'destination' => $data[0]['destination'],
            'action' => $actions,
            'sort' => $data[0]['sort'],
            'created_at' => $data[0]['created_at'],
        ];
    }

    public function trigger(array $params)
    {
        $data = $this->toArray();
        $post = [
            'body' => json_encode($params),
            'sslverify' => false,
            'timeout'    => 5,
            'headers' => array(
                'Content-Type' => 'application/json; charset=utf-8',
            ),
        ];
        wp_remote_post($data['destination'], $post);
    }
}