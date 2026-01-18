<?php

namespace Mbl\AutoResponder;

use Exception;

class Subscriptions
{
    private string $table;

    public function __construct()
    {
        $this->table = $GLOBALS['wpdb']->get_blog_prefix() . 'memberlux_mailing_list';
    }

    /**
     * @throws Exception
     */
    public function byTerm($term_id): array
    {
        global $wpdb;
        $sql = $wpdb->prepare("SELECT * FROM $this->table WHERE term_id = %d", $term_id);
        $subscription = $wpdb->get_row($sql, ARRAY_A);
        if (!is_array($subscription)) {
            throw new Exception("Subscription wpm-level $term_id not found");
        }

        return $subscription;
    }
}