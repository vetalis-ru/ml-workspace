<?php
function mblar_common(): array
{
    return [
        'id' => 1,
        'type' => 'common',
        'name' => 'Общие',
        'tree' => [
            'root' => [
                'type' => 'table',
            ]
        ],
        'settings' => [
            [
                'label' => 'Цвет фона письма',
                'node_id' => 'root',
                'type' => 'bg_color'
            ],
            [
                'label' => 'Отступы',
                'node_id' => 'root',
                'type' => 'padding_tb',
            ]
        ],
        'default_props' => [
            'root' => [
                'style' => [
                    'padding-top' => '150px',
                    'padding-bottom' => '25px',
                    'background-color' => '#F7F7F7',
                ]
            ]
        ]
    ];
}

function mblar_content(): array
{
    return [
        'id' => 1,
        'type' => 'content',
        'name' => 'Контент',
        'tree' => [
            'root' => [
                'type' => 'table',
                'props' => [
                    'class' => 'mblar-content',
                    'style' => [
                        'width' => '100%',
                        'mso-table-lspace' => '0pt',
                        'mso-table-rspac' => '0pt',
                        'border-spacing' => '0px',
                        'background-repeat' => 'no-repeat',
                        'background-size' => 'cover',
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
                'type' => 'text',
                'children' => '
                        <p style="color: #023047">
                            Здравствуйте, Клиент!
                        </p>
                        <p style="color: #023047">
                            Здесь вы можете спросить всё, что вас интересует о платформе и курсах. Если у вас есть какая-то конкретная проблема или вы хотите сообщить об ошибке, пишите нам об этом на
                        </p>
                        <p style="color: #023047">
                            Обратите внимание на следующие моменты:
                        </p>
                        <ol style="color: #023047">
                        <li>Обязательно подтвердите свой email, чтобы гарантированно получать бесплатные обучающие материалы на почту. Письмо подтверждения уже выслано на ваш email.</li><li>Проверьте правильно ли заполнены ваши личные данные.</li>
                        <li>Корректно заполните контактные данные.</li>
                        <li>Для более простого входа в систему вы можете авторизоваться через одну из соц. сетей.</li></ol>
                    ',
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
                'label' => 'Отступы',
                'node_id' => 'td',
                'type' => 'padding',
            ],
            [
                'label' => 'Закругления углов',
                'node_id' => 'td',
                'type' => 'border_radius',
            ],
        ],
        'default_props' => [
            'td' => [
                'valign' => 'top',
                'style' => [
                    'background-color' => '#ffffff',
                    'padding-top' => '20px',
                    'padding-right' => '40px',
                    'padding-bottom' => '20px',
                    'padding-left' => '40px',
                    'border-left-color' => '#18AB55',
                    'border-left-style' => 'solid',
                    'border-left-width' => '1px',
                    'border-right-color' => '#18AB55',
                    'border-right-style' => 'solid',
                    'border-right-width' => '1px',
                    'border-top-color' => '#18AB55',
                    'border-top-style' => 'solid',
                    'border-top-width' => '0',
                    'border-bottom-color' => '#18AB55',
                    'border-bottom-style' => 'solid',
                    'border-bottom-width' => '0',
                    'border-top-left-radius' => '0px',
                    'border-top-right-radius' => '0px',
                    'border-bottom-left-radius' => '0px',
                    'border-bottom-right-radius' => '0px',
                ]
            ]
        ]
    ];
}