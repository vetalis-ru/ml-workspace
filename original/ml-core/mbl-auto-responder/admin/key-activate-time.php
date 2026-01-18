<?php

use Mbl\AutoResponder\ActivatedKey;
use Mbl\AutoResponder\MailingList;
use Mbl\AutoResponder\UserSubscriptions;

add_action('wpm_mbl_term_key_updated', 'mblar_mbl_term_key_updated', 10, 1);
function mblar_mbl_term_key_updated($key)
{
    if (empty($key['user_id'])) {
        return;
    }
    if ($key['status'] !== 'used' || $key['key_type'] !== 'wpm_term_keys' || (int)$key['is_banned'] !== 0) {
        return;
    }

    mblar_activate_key($key['user_id'], $key['key']);
}

add_action('wpm_mbl_term_keys_query_updated', 'mblar_mbl_term_keys_query_updated', 10, 2);
function mblar_mbl_term_keys_query_updated($data, $where)
{
    if (!isset($data['user_id']) || !isset($where['key'])) {
        return;
    }
    mblar_activate_key($data['user_id'], $where['key']);
}

function mblar_activate_key($user_id, $code)
{
    $key = wpm_search_key_id($code);
    $activatedKey = new ActivatedKey(absint($key['item_id']));
    try {
        $activatedKey->activate();
        $userSubscriptions = new UserSubscriptions($user_id);
        $userSubscriptions->subscribe(new MailingList(absint($key['term_id'])));
    } catch (Exception $e) {
    }
}