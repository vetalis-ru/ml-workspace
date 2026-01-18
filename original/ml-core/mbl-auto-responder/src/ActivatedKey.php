<?php

namespace Mbl\AutoResponder;

use DateTime;
use Exception;

class ActivatedKey
{
    private int $key_id;
    private string $table;

    /**
     * @param int $key_id
     */
    public function __construct(int $key_id)
    {
        $this->key_id = $key_id;
        $this->table = $GLOBALS['wpdb']->get_blog_prefix() . 'memberlux_keys_meta';
    }

    /**
     * @throws KeyAlreadyActivated
     */
    public function activate() {
        if ($this->isActivated()) {
            throw new KeyAlreadyActivated("Key id $this->key_id already activated");
        }
        global $wpdb;
        try {
            $now = new DateTime('now', wp_timezone());
        } catch (Exception $e) {
        }
        $wpdb->insert(
            $this->table,
            ['key_id' => $this->key_id, 'activation_datetime' => $now->format('Y-m-d H:i:s')]
        );
    }

    public function isActivated(): bool
    {
        global $wpdb;
        $exist = $wpdb->get_var(
            $wpdb->prepare("SELECT key_id, activation_datetime FROM $this->table WHERE key_id = %d", $this->key_id)
        );
        return !is_null($exist);
    }
}