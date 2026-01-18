<?php
function mblc_certificate_fields(): array
{
    $settings = mblc_default_field_settings();
    $fields = [
        new Field(
            'ФИО',
            'name',
            [
                'hide' => 0,
                'position' => [
                    'top' => $settings['name']['vertical']['position']['top'],
                    'left' => $settings['name']['vertical']['position']['left'],
                ],
                'size' => [
                    'width' => $settings['name']['vertical']['size']['width']
                ],
                'text_align' => 'center',
                'font_size' => 22,
                'font_weight' => 'bold',
                'color' => '#000000',
                'font_family' => 'opensans',
                'line_height' => 28,
                'example_text' => 'Фамилия Имя Отчество',
            ]
        ),
        new Field(
            'Дата выдачи сертификата',
            'date',
            [
                'hide' => 0,
                'position' => [
                    'top' => $settings['date']['vertical']['position']['top'],
                    'left' => $settings['date']['vertical']['position']['left'],
                ],
                'size' => [
                    'width' => $settings['date']['vertical']['size']['width']
                ],
                'text_align' => 'center',
                'font_size' => 14,
                'font_weight' => 'normal',
                'color' => '#000000',
                'font_family' => 'opensans',
                'line_height' => 24,
                'example_text' => '18.05.2020',
            ]
        ),
        new Field(
            'Серия',
            'series',
            [
                'hide' => 0,
                'position' => [
                    'top' => $settings['series']['vertical']['position']['top'],
                    'left' => $settings['series']['vertical']['position']['left'],
                ],
                'size' => [
                    'width' => $settings['series']['vertical']['size']['width']
                ],
                'text_align' => 'center',
                'font_size' => 14,
                'font_weight' => 'normal',
                'color' => '#000000',
                'font_family' => 'opensans',
                'line_height' => 24,
                'example_text' => 'CRT',
            ]
        ),
        new Field(
            'Номер',
            'number',
            [
                'hide' => 0,
                'position' => [
                    'top' => $settings['number']['vertical']['position']['top'],
                    'left' => $settings['number']['vertical']['position']['left'],
                ],
                'size' => [
                    'width' => $settings['number']['vertical']['size']['width']
                ],
                'text_align' => 'center',
                'font_size' => 14,
                'font_weight' => 'normal',
                'color' => '#000000',
                'font_family' => 'opensans',
                'line_height' => 24,
                'example_text' => '00009',
            ]
        ),
        new Field(
            'Название курса',
            'course',
            [
                'hide' => 0,
                'position' => [
                    'top' => $settings['course']['vertical']['position']['top'],
                    'left' => $settings['course']['vertical']['position']['left'],
                ],
                'size' => [
                    'width' => $settings['course']['vertical']['size']['width']
                ],
                'text_align' => 'center',
                'font_size' => 20,
                'font_weight' => 'bold',
                'color' => '#000000',
                'font_family' => 'opensans',
                'line_height' => 24,
                'example_text' => 'Название курса',
            ]
        ),
    ];
    if ( mblc_get_option_with_default( 'custom_fields_enabled' ) === 'on' ) {
        for ( $i = 1; $i <= mblc_certificate_custom_fields_count(); $i++ ) {
            $fields[] = new Field(
                "Дополнительное поле $i",
                "field$i",
                [
                    'hide' => 1,
                    'position' => [
                        'top' => $settings["field$i"]['vertical']['position']['top'],
                        'left' => $settings["field$i"]['vertical']['position']['left'],
                    ],
                    'size' => [
                        'width' => $settings["field$i"]['vertical']['size']['width']
                    ],
                    'text_align' => 'center',
                    'font_size' => 20,
                    'font_weight' => 'normal',
                    'color' => '#000000',
                    'font_family' => 'opensans',
                    'line_height' => 24,
                    'example_text' => "Дополнительное поле $i",
                ]
            );
        }
    }

    return apply_filters( 'mblc_certificate_template_fields', $fields );
}

function mblc_certificate_custom_fields_count()
{
    return apply_filters( 'mblc_certificate_template_custom_fields_count', 2 );
}

function mblc_default_field_settings()
{
    $settings = [
        'name' => [
            'vertical' => [
                'position' => [
                    'top' => 357,
                    'left' => 0,
                ],
                'size' => [
                    'width' => 793
                ],
            ],
            'horizontal' => [
                'position' => [
                    'top' => 440,
                    'left' => 0,
                ],
                'size' => [
                    'width' => 1121
                ],
            ],
        ],
        'date' => [
            'vertical' => [
                'position' => [
                    'top' => 969,
                    'left' => 382,
                ],
                'size' => [
                    'width' => 85
                ],
            ],
            'horizontal' => [
                'position' => [
                    'top' => 600,
                    'left' => 235,
                ],
                'size' => [
                    'width' => 85
                ],
            ],
        ],
        'series' => [
            'vertical' => [
                'position' => [
                    'top' => 970,
                    'left' => 593,
                ],
                'size' => [
                    'width' => 36
                ],
            ],
            'horizontal' => [
                'position' => [
                    'top' => 120,
                    'left' => 845,
                ],
                'size' => [
                    'width' => 36
                ],
            ],
        ],
        'number' => [
            'vertical' => [
                'position' => [
                    'top' => 969,
                    'left' => 657,
                ],
                'size' => [
                    'width' => 56
                ],
            ],
            'horizontal' => [
                'position' => [
                    'top' => 120,
                    'left' => 910,
                ],
                'size' => [
                    'width' => 56
                ],
            ],
        ],
        'course' => [
            'vertical' => [
                'position' => [
                    'top' => 323,
                    'left' => 0,
                ],
                'size' => [
                    'width' => 793
                ],
            ],
            'horizontal' => [
                'position' => [
                    'top' => 340,
                    'left' => 0,
                ],
                'size' => [
                    'width' => 1121
                ],
            ],
        ],
    ];

    if ( mblc_get_option_with_default( 'custom_fields_enabled' ) === 'on' ) {
        for ( $i = 1; $i <= mblc_certificate_custom_fields_count(); $i++ ) {
            $settings["field$i"] = [
                'vertical' => [
                    'position' => [
                        'top' => 464 + 24 * ($i - 1),
                        'left' => 0,
                    ],
                    'size' => [
                        'width' => 793
                    ]
                ],
                'horizontal' => [
                    'position' => [
                        'top' => 423 + 24 * ($i - 1),
                        'left' => 0,
                    ],
                    'size' => [
                        'width' => 1121
                    ]
                ]
            ];
        }
    }

    return apply_filters( 'mblc_certificate_template_fields_default_settings', $settings );
}
