<?php
add_filter(
    'mblar_header_templates',
    function ($variants, Mbl\AutoResponder\Plugin $plugin): array {
        $variants[] = [
            'id' => 2,
            'name' => 'Вариант №2',
            'preview' => $plugin->assets_uri('images/headers/v2.png'),
            'tree' => [
                'root' => [
                    'type' => 'table',
                    'props' => [
                        'style' => [
                            'border-spacing' => '0px',
                            'width' => '100%'
                        ],
                    ],
                    'children' => [
                        'tr',
                    ],
                ],
                'tr' => [
                    'type' => 'tr',
                    'children' => [
                        'td',
                    ],
                ],
                'td' => [
                    'type' => 'td',
                    'children' => [
                        'content',
                    ],
                ],
                'content' => [
                    'type' => 'table',
                    'children' => [
                        'content_tr',
                    ],
                ],
                'content_tr' => [
                    'type' => 'tr',
                    'children' => [
                        'left_td',
                        'right_td',
                    ],
                ],
                'left_td' => [
                    'type' => 'td',
                    'props' => [
                        'width' => '300',
                    ],
                    'children' => [
                        'logo',
                    ],
                ],
                'right_td' => [
                    'type' => 'td',
                    'props' => [
                        'width' => '300',
                    ],
                    'children' => [
                        'text',
                    ],
                ],
                'logo' => [
                    'type' => 'img',
                    'props' => [
                        'alt' => '',
                        'src' => $plugin->assets_uri('images/logo.png'),
                    ],
                ],
                'text' => [
                    'type' => 'text',
                ],
            ],
            'settings' => [
                [
                    'label' => 'Текст',
                    'node_id' => 'text',
                    'type' => 'text',
                    'bg_node' => 'td'
                ],
                [
                    'label' => 'Картинка',
                    'node_id' => 'logo',
                    'type' => 'image',
                ],
                [
                    'label' => 'Размеры картинка',
                    'node_id' => 'logo',
                    'type' => 'image_size',
                    'default' => [
                        'height' => 24,
                        'width' => 100,
                    ],
                ],
                [
                    'label' => 'Цвет фона',
                    'node_id' => 'td',
                    'type' => 'bg_color',
                ],
                [
                    'label' => 'Обводка контента',
                    'node_id' => 'td',
                    'type' => 'borders',
                ],
                [
                    'label' => 'Закругления углов',
                    'node_id' => 'td',
                    'type' => 'border_radius',
                ],
                [
                    'label' => 'Отступы',
                    'node_id' => 'td',
                    'type' => 'padding',
                ],
            ],
            'default_props' => [
                'td' => [
                    'style' => [
                        'border-top-left-radius' => '15px',
                        'border-top-right-radius' => '15px',
                        'border-bottom-left-radius' => '0px',
                        'border-bottom-right-radius' => '0px',
                        'background-color' => '#ffffff',
                        'padding-top' => '30px',
                        'padding-right' => '20px',
                        'padding-bottom' => '30px',
                        'padding-left' => '20px',
                        'border-left-color' => '#18AB55',
                        'border-left-style' => 'solid',
                        'border-left-width' => '1px',
                        'border-right-color' => '#18AB55',
                        'border-right-style' => 'solid',
                        'border-right-width' => '1px',
                        'border-top-color' => '#18AB55',
                        'border-top-style' => 'solid',
                        'border-top-width' => '1px',
                        'border-bottom-color' => '#ffffff',
                        'border-bottom-style' => 'solid',
                        'border-bottom-width' => '0',
                    ]
                ],
                'text' => [
                    'children' => '<p style="text-align: right;"><strong>Пример текста</strong></p>'
                ],
                'logo' => [
                    'thumbnail' => 0,
                    'height' => 24,
                    'width' => null,
                ]
            ]
        ];

        return $variants;
    },
    10,
    2
);