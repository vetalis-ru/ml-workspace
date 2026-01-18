<?php
add_filter(
    'mblar_header_templates',
    function ($variants, Mbl\AutoResponder\Plugin $plugin): array
    {
        $variants[] = [
            'id' => 4,
            'type' => 'header',
            'name' => 'Вариант №4',
            'preview' => $plugin->assets_uri('images/headers/v4.png'),
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
                    'props' => [
                        'style' => [
                            'overflow' => 'hidden',
                            'padding' => '0px',
                        ],
                    ],
                    'children' => [
                        'img',
                    ],
                ],
                'img' => [
                    'type' => 'img',
                    'props' => [
                        'height' => null,
                        'width' => '600',
                        'alt' => '',
                        'src' => $plugin->assets_uri('images/email-img-back.png'),
                        'style' => [
                            'width' => '100%',
                            'display' => 'block',
                        ],
                    ],
                ],
            ],
            'settings' => [
                [
                    'label' => 'Картинка',
                    'node_id' => 'img',
                    'type' => 'image',
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
            ],
            'default_props' => [
                'img' => [
                    'thumbnail' => 0,
                ],
                'td' => [
                    'style' => [
                        'border-top-left-radius' => '0px',
                        'border-top-right-radius' => '0px',
                        'border-bottom-left-radius' => '0px',
                        'border-bottom-right-radius' => '0px',
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
                ]
            ]
        ];

        return $variants;
    },
    10,
    2
);