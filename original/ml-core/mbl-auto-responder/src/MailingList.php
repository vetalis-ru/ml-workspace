<?php

namespace Mbl\AutoResponder;

use WP_Term;

class MailingList
{
    private string $tableName;
    private WP_Term $term;
    private array $cache;

    /**
     * @param WP_Term | int $term
     */
    public function __construct($term, $cache = [])
    {
        $this->tableName = $GLOBALS['wpdb']->get_blog_prefix() . 'memberlux_mailing_list';
        if (is_int($term)) {
            $this->term = get_term_by('id', $term, 'wpm-levels');
        } else {
            $this->term = $term;
        }
        $this->cache = $cache;
    }

    /**
     * @throws MailingListNotExist
     */
    public function toArray()
    {
        if (empty($this->cache)) {
            global $wpdb;
            $sql = $wpdb->prepare("SELECT * FROM $this->tableName WHERE term_id = %d", $this->term->term_id);
            $row = $wpdb->get_row($sql, ARRAY_A);
            if (is_null($row)) {
                throw new MailingListNotExist("MailingList wpm level {$this->term->term_id} not found");
            }
            $this->cache = $this->format($row);
        }

        return $this->cache;
    }

    public function format($raw)
    {
        $format = [
            'term_id' => 'absint',
            'template_id' => 'absint',
            'is_on' => fn($v) => (int)$v === 1,
            'unsubscribe' => fn($v) => (int)$v === 1,
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