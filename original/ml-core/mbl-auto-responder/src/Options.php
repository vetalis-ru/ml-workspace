<?php

namespace Mbl\AutoResponder;

class Options
{
    private string $name;
    private array $map;

    public function __construct()
    {
        $this->name = 'mblar';
        $this->map = [
            [
                'id' => 'unsubscribe_success_url',
                'value' => '',
                'label' => 'Url страницы "Вы успешно отписались"',
                'group' => 'common',
            ],
            [
                'id' => 'unsubscribe_all',
                'value' => 'Отписаться от всех рассылок',
                'label' => 'Текст ссылки "отписаться от всех рассылок"',
                'group' => 'text',
            ],
            [
                'id' => 'unsubscribe_current',
                'value' => 'Отписаться',
                'label' => 'Текст ссылки "отписаться от текущей рассылки"',
                'group' => 'text',
            ],
        ];
    }

    public function byId($id) {
        return $this->toArray()[$id];
    }

    public function toArray(): array
    {
        return array_reduce($this->list(), function ($carry, $i) {
            $carry[$i['id']] = $i['value'];
            return $carry;
        });
    }

    public function list(): array
    {
        $withDefault = array_merge(
            array_reduce($this->map, function ($carry, $i) {
                $carry[$i['id']] = $i['value'];
                return $carry;
            }),
            json_decode(get_option($this->name), true) ?? []
        );

        return array_map(function ($item) use ($withDefault) {
            $item['value'] = $withDefault[$item['id']];
            return $item;
        }, $this->map);
    }

    public function save(array $data)
    {
        $keys = array_map(fn($i) => $i['id'], $this->map);
        update_option(
            $this->name,
            json_encode(
                array_merge(
                    json_decode(get_option($this->name), true) ?: [],
                    array_filter($data, fn($k) => in_array($k, $keys), ARRAY_FILTER_USE_KEY)
                )
            )
        );
    }
}
