<?php

namespace Mbl\AutoResponder;

use wpdb;

class MailTemplates
{
    private string $table_name;
    private wpdb $db;

    /**
     * @param wpdb $db
     */
    public function __construct(wpdb $db)
    {
        $this->table_name = $db->get_blog_prefix() . 'memberlux_mailing_templates';
        $this->db = $db;
    }

    public function byId(int $id)
    {
        return $this->db->get_row(
            $this->db->prepare("SELECT * FROM $this->table_name WHERE id = %d", $id),
            ARRAY_A
        );
    }

    public function list($args = ['fields' => ['id', 'name']])
    {
        $fields = implode(',', $args['fields']);
        return array_map([$this, 'format'], $this->db->get_results(
            "SELECT $fields FROM $this->table_name",
            ARRAY_A
        ));
    }

    public function add($name, $template_data): int
    {
        $this->db->insert($this->table_name, [
            'name' => $name,
            'data' => json_encode($template_data),
        ]);

        return $this->db->insert_id;
    }

    public function save($id, $name, $template_data): int
    {
        return $this->db->update($this->table_name, [
            'name' => $name,
            'data' => json_encode($template_data),
        ], ['id' => $id]);
    }

    public function remove($id)
    {
        return $this->db->delete($this->table_name, ['id' => $id]);
    }

    public function copy($id): int
    {
        $template = $this->byId($id);
        $this->db->insert($this->table_name, [
            'name' => "{$template['name']} (копия)",
            'data' => $template['data'],
        ]);

        return $this->db->insert_id;
    }

    public function format($raw)
    {
        $format = [
            'id' => 'absint',
            'name' => fn($v) => $v,
            'data' => fn($v) => $v,
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
