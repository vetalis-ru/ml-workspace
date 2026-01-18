<?php
namespace Mbl\Api;
add_action( 'wpm_update_user_key_dates', 'Mbl\Api\update_user_key_dates_hook', 10, 5 );

function update_user_key_dates_hook( $user_id, $term_id, $code, $key, $source )
{
    $user = get_user_by('ID', $user_id);
    $term = get_term($key['term_id']);
    $is_unlimited = (int)$key['key_info']['is_unlimited'];

    $action = [
        'type' => $source,
        'email' => $user->user_email,
        'first_name' => $user->first_name,
        'last_name' => $user->last_name,
        'patronymic' => get_user_meta( $user->ID, 'surname', true ),
        'phone' => get_user_meta( $user->ID, 'phone', true ),
        'custom1' => get_user_meta( $user->ID, 'custom1', true ),
        'custom2' => get_user_meta( $user->ID, 'custom2', true ),
        'custom3' => get_user_meta( $user->ID, 'custom3', true ),
        'custom4' => get_user_meta( $user->ID, 'custom4', true ),
        'custom5' => get_user_meta( $user->ID, 'custom5', true ),
        'custom6' => get_user_meta( $user->ID, 'custom6', true ),
        'custom7' => get_user_meta( $user->ID, 'custom7', true ),
        'custom8' => get_user_meta( $user->ID, 'custom8', true ),
        'custom9' => get_user_meta( $user->ID, 'custom9', true ),
        'custom10' => get_user_meta( $user->ID, 'custom10', true ),
        'custom1textarea' => get_user_meta( $user->ID, 'custom1textarea', true ),
        'key' => [
            'term_id' => $key['term_id'],
            'name' => $term->name,
            'is_unlimited' => $is_unlimited,
            'duration' => $is_unlimited ? null : $key['key_info']['duration'],
            'units' => $is_unlimited ? null : $key['key_info']['units'],
            'date_start' => $key['key_info']['date_start'],
            'date_end' => $is_unlimited ? null : $key['key_info']['date_end'],
        ]
    ];
    $body['actions'] = [$action];


    $webhooks = new Webhooks();
    $list = $webhooks->list([$source]);
    if (empty($list)) {
        return;
    }

    wp_remote_post(rest_url('mbl/v1/webhook/action'), [
        'body' => json_encode($body),
        'sslverify' => false,
        'timeout'    => 0,
        'headers' => array(
            'Content-Type' => 'application/json; charset=utf-8',
        ),
    ]);
}


