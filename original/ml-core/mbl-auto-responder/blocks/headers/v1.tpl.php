<?php
add_filter(
    'mblar_header_templates',
    function ($variants, Mbl\AutoResponder\Plugin $plugin): array {
        $variants[] = [
            'id' => 1,
            'type' => 'header',
            'name' => 'Вариант №1',
            'preview' => $plugin->assets_uri('images/headers/v1.png'),
            'tree' => [
                'root' => [
                    'type' => 'table',
                    'props' => [
                        'style' => [
                            'border-spacing' => '0px',
                            'width' => '100%'
                        ],
                    ],
                    'children' => ['tr']
                ],
                'tr' => [
                    'type' => 'tr',
                    'children' => ['img_container'],
                ],
                'img_container' => [
                    'type' => 'td',
                    'children' => ['img'],
                ],
                'img' => [
                    'type' => 'img',
                    'props' => [
                        'alt' => '',
                        'src' => $plugin->assets_uri('images/logo-white.png'),
                    ],
                ]
            ],
            'settings' => [
                [
                    'label' => 'Цвет фона',
                    'node_id' => 'img_container',
                    'type' => 'bg_color',
                ],
                [
                    'label' => 'Картинка',
                    'node_id' => 'img',
                    'type' => 'image',
                ],
                [
                    'label' => 'Размеры картинка',
                    'node_id' => 'img',
                    'type' => 'image_size',
                    'default' => [
                        'height' => 40,
                        'width' => 162,
                    ],
                ],
                [
                    'label' => 'Выравнивание',
                    'node_id' => 'img_container',
                    'type' => 'align',
                ],
                [
                    'label' => 'Обводка контента',
                    'node_id' => 'img_container',
                    'type' => 'borders',
                ],
                [
                    'label' => 'Закругления углов',
                    'node_id' => 'img_container',
                    'type' => 'border_radius',
                ],
                [
                    'label' => 'Отступы',
                    'node_id' => 'img_container',
                    'type' => 'padding',
                ]
            ],
            'default_props' => [
                'img_container' => [
                    'align' => 'right',
                    'style' => [
                        'border-top-left-radius' => '10px',
                        'border-top-right-radius' => '10px',
                        'border-bottom-left-radius' => '0px',
                        'border-bottom-right-radius' => '0px',
                        'background-color' => '#18AB55',
                        'padding-top' => '20px',
                        'padding-right' => '10px',
                        'padding-bottom' => '20px',
                        'padding-left' => '10px',
                        'border-left-color' => '#18AB55',
                        'border-left-style' => 'solid',
                        'border-left-width' => '1px',
                        'border-right-color' => '#18AB55',
                        'border-right-style' => 'solid',
                        'border-right-width' => '1px',
                        'border-top-color' => '#18AB55',
                        'border-top-style' => 'solid',
                        'border-top-width' => '1px',
                        'border-bottom-color' => '#18AB55',
                        'border-bottom-style' => 'solid',
                        'border-bottom-width' => '1px'
                    ]
                ],
                'img' => [
                    'height' => 40,
                    'width' => null,
                    'thumbnail' => 0,
                ]
            ],
        ];

        return $variants;
    },
    10,
    2
);