<?php

namespace Mbl\Api;

class Webhooks
{
    private string $table_name;
    private string $action_table_name;

    public function __construct()
    {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'memberlux_hook';
        $this->action_table_name = $wpdb->prefix . 'memberlux_hook_action';
    }

    /**
     * @param int $webhook_id
     * @return bool
     */
    public function delete(int $webhook_id): bool
    {
        global $wpdb;
        $wpdb->delete($this->action_table_name, array('hook_id' => $webhook_id), array('%d'));
        $result = $wpdb->delete($this->table_name, array('id' => $webhook_id), array('%d'));

        return $result !== false;
    }

    /**
     * @param array $actions
     * @return Webhook[]
     */
    public function list(array $actions = []): array
    {
        global $wpdb;
        $sql = "SELECT *
                FROM $this->table_name AS h 
                LEFT JOIN $this->action_table_name AS a ON h.id = a.hook_id";

        if (!empty($actions)) {
            $actions_with_all = [...$actions, 'all'];
            $placeholders = implode(',', array_fill(0, count($actions_with_all), '%s'));
            $sql .= $wpdb->prepare(" WHERE a.action IN ($placeholders)", $actions_with_all);
        }

        $sql .= " ORDER BY h.sort ASC";
        $raw = $wpdb->get_results($sql, ARRAY_A);
        $result = [];
        foreach ($raw as $item) {
            if (!isset($result[$item['id']])) {
                $result[$item['id']] = [
                    'id' => $item['id'],
                    'destination' => $item['destination'],
                    'sort' => $item['sort'],
                    'created_at' => $item['created_at'],
                    'action' => []
                ];
            }
            $result[$item['id']]['action'][] = $item['action'];
        }

        return array_values(array_map(fn($item) => new CachedWebhook(new SmWebhook($item['id']), $item), $result));
    }

    /**
     * @param string $destination
     * @param string[] $action
     * @param string $sort
     * @return int|bool
     */
    public function add(string $destination, array $action, string $sort)
    {
        global $wpdb;
        $data = [
            'destination' => $destination,
            'sort' => $sort
        ];
        $result = $wpdb->insert($this->table_name, $data, ['%s', '%d']);

        if ($result === false) {
            return false;
        }

        $inserted_id = $wpdb->insert_id;

        if (in_array('all', $action)) {
            $wpdb->insert($this->action_table_name, ['hook_id' => $inserted_id, 'action' => 'all'], ['%d', '%s']);
        } else {
            foreach ($action as $item) {
                $wpdb->insert($this->action_table_name, ['hook_id' => $inserted_id, 'action' => $item], ['%d', '%s']);
            }
        }

        return $inserted_id;
    }

    public function update(int $id, string $destination, array $actions, int $sort): bool
    {
        global $wpdb;
        $exists = $wpdb->get_row($wpdb->prepare("SELECT * FROM $this->table_name WHERE id = %d", $id));

        if (!$exists) {
            return false;
        }

        $data = [
            'destination' => $destination,
            'sort' => $sort
        ];

        $wpdb->update($this->table_name, $data, ['id' => $id], ['%s', '%d']);

        $old = $wpdb->get_col($wpdb->prepare(
            "SELECT `action` FROM $this->action_table_name WHERE hook_id = %d", $id
        ));

        $isAll = in_array('all', $actions);
        $oldIsAll = in_array('all', $old);

        if (!$isAll && $oldIsAll) {
            $wpdb->delete($this->action_table_name, ['hook_id' => $id]);
            foreach ($actions as $item) {
                $wpdb->insert($this->action_table_name, ['hook_id' => $id, 'action' => $item], ['%d', '%s']);
            }
        } elseif ($isAll && !$oldIsAll) {
            $wpdb->delete($this->action_table_name, ['hook_id' => $id]);
            $wpdb->insert($this->action_table_name, ['hook_id' => $id, 'action' => 'all'], ['%d', '%s']);
        } else if(!$isAll && !$oldIsAll) {
            foreach ($old as $item) {
                if (!in_array($item, $actions)) {
                    $wpdb->delete($this->action_table_name, ['hook_id' => $id, 'action' => $item]);
                }
            }

            $toAdd = array_diff($actions, $old);
            foreach ($toAdd as $item) {
                $wpdb->insert($this->action_table_name, ['hook_id' => $id, 'action' => $item], ['%d', '%s']);
            }
        }

        return true;
    }
}
