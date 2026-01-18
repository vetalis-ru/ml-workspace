<?php

namespace Mbl\AutoResponder;

class Mailing
{
    private int $term_id;
    private string $tableName;

    /**
     * @param int $term_id
     */
    public function __construct(int $term_id)
    {
        $this->term_id = $term_id;
        $this->tableName = $GLOBALS['wpdb']->get_blog_prefix() . 'memberlux_mailing';
    }

    public function toArray(): array
    {
        global $wpdb;
        return array_map(
            function ($mail) {
                return array_merge($mail, [
                    'id' => (int)$mail['id'],
                    'term_id' => (int)$mail['term_id'],
                    'mail_order' => (int)$mail['mail_order'],
                    'days' => (int)$mail['days'],
                    'hour' => (int)$mail['hour'],
                    'minute' => (int)$mail['minute'],
                    'message' => wp_unslash($mail['message']),
                ]);
            },
            $wpdb->get_results(
                $wpdb->prepare("SELECT * FROM $this->tableName WHERE term_id = %d", $this->term_id),
                ARRAY_A
            )
        ) ?? [];
    }
}