<?php

namespace Mbl\AutoResponder;

use wpdb;

class MailingResults
{
    private int $user_id;
    private int $term_id;
    private string $tableName;

    /**
     * @param int $user_id
     * @param int $term_id
     */
    public function __construct(int $user_id, int $term_id)
    {
        $this->user_id = $user_id;
        $this->term_id = $term_id;
        $this->tableName = $GLOBALS['wpdb']->get_blog_prefix() . 'memberlux_mailing_results';;
    }

    public function mail_result(int $mail_order, array $fields = ['*'])
    {
        global $wpdb;
        $_fields = implode(',', $fields);
        $result = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT $_fields FROM $this->tableName WHERE user_id = %d AND term_id = %d AND mail_order = %d",
                $this->user_id, $this->term_id, $mail_order
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
            'user_id' => 'absint',
            'term_id' => 'absint',
            'datetime' => ['date_create', wp_timezone()],
            'm_id' => 'absint',
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

    public function lastMail(array $fields = ['*'])
    {
        global $wpdb;
        $_fields = implode(',', $fields);
        $result = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT $_fields FROM $this->tableName WHERE user_id = %d AND term_id = %d ORDER BY mail_order DESC",
                $this->user_id, $this->term_id
            ),
            ARRAY_A
        );

        return $this->format($result);
    }

    public function nextMail(): int
    {
        $last = $this->lastMail(['mail_order']);
        return is_null($last) ? 0 : $last['mail_order'] + 1;
    }
}