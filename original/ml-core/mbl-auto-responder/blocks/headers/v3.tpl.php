<?php
add_filter(
    'mblar_header_templates',
    function ($variants, Mbl\AutoResponder\Plugin $plugin): array {
        $variants[] = [
            'id' => 3,
            'type' => 'header',
            'name' => 'Вариант №3',
            'preview' => $plugin->assets_uri('images/headers/v3.png'),
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
                    'props' => [
                        'width' => '100%',
                    ],
                    'children' => [
                        'content_top',
                        'content_bottom',
                    ],
                ],
                'content_top' => [
                    'type' => 'tr',
                    'children' => [
                        'content_top_td',
                    ],
                ],
                'content_bottom' => [
                    'type' => 'tr',
                    'children' => [
                        'content_bottom_td',
                    ],
                ],
                'content_top_td' => [
                    'type' => 'td',
                    'props' => [
                        'align' => 'center',
                    ],
                    'children' => [
                        'logo',
                    ],
                ],
                'content_bottom_td' => [
                    'type' => 'td',
                    'children' => [
                        'text',
                    ],
                ],
                'logo' => [
                    'type' => 'img',
                    'props' => [
                        'alt' => '',
                        'src' => $plugin->assets_uri('images/logo-white.png'),
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
                    'label' => 'Отступы текста',
                    'node_id' => 'content_bottom_td',
                    'type' => 'padding',
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
                        'height' => 74,
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
                        'background-color' => '#18AB55',
                        'padding-top' => '25px',
                        'padding-right' => '20px',
                        'padding-bottom' => '10px',
                        'padding-left' => '20px',
                        'border-left-color' => '#db583b',
                        'border-left-style' => 'solid',
                        'border-left-width' => '0',
                        'border-right-color' => '#db583b',
                        'border-right-style' => 'solid',
                        'border-right-width' => '0',
                        'border-top-color' => '#db583b',
                        'border-top-style' => 'solid',
                        'border-top-width' => '0',
                        'border-bottom-color' => '#db583b',
                        'border-bottom-style' => 'solid',
                        'border-bottom-width' => '0',
                    ]
                ],
                'content_bottom_td' => [
                    'style' => [
                        'padding-top' => '20px',
                        'padding-right' => '0',
                        'padding-bottom' => '0',
                        'padding-left' => '0',
                    ]
                ],
                'text' => [
                    'children' => '<p style="text-align: center;"><span style="font-family: tahoma, arial, helvetica, sans-serif;"><strong><span style="color: #ffffff; font-size: 12pt;">Пример текста</span></strong></span></p>',
                ],
                'logo' => [
                    'height' => 40,
                    'width' => NULL,
                    'thumbnail' => 0,
                ]
            ]
        ];

        return $variants;
    },
    10,
    2
);