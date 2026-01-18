<?php

namespace Mbl\AutoResponder;

class Mailings
{
    private string $tableName;

    public function __construct()
    {
        $this->tableName = $GLOBALS['wpdb']->get_blog_prefix() . 'memberlux_mailing';
    }

    public function byOrder(int $term_id, int $mail_order)

    {
        global $wpdb;
        $result =  $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $this->tableName WHERE term_id = %d AND mail_order = %d",
                $term_id, $mail_order
            ),
            ARRAY_A
        );
        return $this->format($result);
    }

    public function format($raw)
    {
        $format = [
            'id' => 'absint',
            'mail_order' => 'absint',
            'days' => 'absint',
            'hour' => 'absint',
            'minute' => 'absint',
            'interval_type' => fn($v) => $v,
            'subject' => fn($v) => $v,
            'message' => fn($v) => $v,
        ];
        if (!empty($raw)) {
            foreach ($raw as $key => $value) {
                if (!isset($format[$key])) {
                    continue;
                }
                if (is_array($format[$key])) {
                    $args = array_slice($format['key'], 1);
                    $raw[$key] = $format[$key][0]($value, ...$args);
                } else {
                    $raw[$key] = $format[$key]($value);
                }
            }
        }

        return $raw;
    }
}