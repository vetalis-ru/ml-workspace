<?php
add_filter(
    'mblar_footer_templates',
    function ($variants, Mbl\AutoResponder\Plugin $plugin): array {
        $variants[] = [
            'id' => 1,
            'name' => 'Вариант №1',
            'preview' => $plugin->assets_uri('images/footers/v1.png'),
            'tree' => [
                'root' => [
                    'type' => 'table',
                    'props' => [
                        'style' => [
                            'width' => '100%',
                            'mso-table-lspace' => '0pt',
                            'mso-table-rspace' => '0pt',
                            'border-spacing' => '0px',
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
                        'text',
                    ],
                ],
                'text' => [
                    'type' => 'text',
                ],
            ],
            'settings' => [
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
                [
                    'label' => 'Контент',
                    'node_id' => 'text',
                    'type' => 'text',
                    'bg_node' => 'td'
                ],
            ],
            'default_props' => [
                'td' => [
                    'style' => [
                        'border-top-left-radius' => '0px',
                        'border-top-right-radius' => '0px',
                        'border-bottom-left-radius' => '10px',
                        'border-bottom-right-radius' => '10px',
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
                        'border-bottom-width' => '1px',
                    ],
                ],
                'text' => [
                    'children' => '<p style="text-align: right;"><span style="font-family: tahoma, arial, helvetica, sans-serif;"><strong><span style="color: #ffffff; font-size: 12pt;">С уважением, команда нашей школы</span></strong></span></p>',
                ]
            ],
        ];

        return $variants;
    },
    10,
    2
);