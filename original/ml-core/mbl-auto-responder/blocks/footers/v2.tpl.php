<?php
add_filter(
    'mblar_footer_templates',
    function ($variants, Mbl\AutoResponder\Plugin $plugin): array {
        $variants[] = [
            'id' => 2,
            'name' => 'Вариант №2',
            'preview' => $plugin->assets_uri('images/footers/v2.png'),
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
                        'src' => $plugin->assets_uri('images/logo.png'),
                    ],

                ],
                'text' => [
                    'type' => 'text',
                ],
            ],
            'settings' => [
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
                        'height' => 40,
                        'width' => 100,
                    ],
                ],
                [
                    'label' => 'Контент',
                    'node_id' => 'text',
                    'type' => 'text',
                    'bg_node' => 'td',
                ],
                [
                    'label' => 'Отступы текста',
                    'node_id' => 'content_bottom_td',
                    'type' => 'padding',
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
                'logo' => [
                    'height' => 40,
                    'width' => NULL,
                    'thumbnail' => 0,
                ],
                'td' => [
                    'style' => [
                        'border-top-left-radius' => '0px',
                        'border-top-right-radius' => '0px',
                        'border-bottom-left-radius' => '10px',
                        'border-bottom-right-radius' => '10px',
                        'background-color' => '#efefef',
                        'padding-top' => '30px',
                        'padding-right' => '0px',
                        'padding-bottom' => '30px',
                        'padding-left' => '0px',
                        'border-left-color' => '#efefef',
                        'border-left-style' => 'solid',
                        'border-left-width' => '0px',
                        'border-right-color' => '#efefef',
                        'border-right-style' => 'solid',
                        'border-right-width' => '0px',
                        'border-top-color' => '#efefef',
                        'border-top-style' => 'solid',
                        'border-top-width' => '0px',
                        'border-bottom-color' => '#efefef',
                        'border-bottom-style' => 'solid',
                        'border-bottom-width' => '0px',
                    ],
                ],
                'text' => [
                    'children' => '<p style="text-align: center;"><span style="font-size: 10pt; line-height: 14px;">Вы получили это письмо, так как ранее интересовались обучением</span></p>',
                ],
                'content_bottom_td' => [
                    'style' => [
                        'padding-top' => '20px',
                        'padding-right' => '0',
                        'padding-bottom' => '0',
                        'padding-left' => '0',
                    ]
                ],
            ],
        ];

        return $variants;
    },
    10,
    2
);