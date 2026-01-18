<?php

use Mbl\AutoResponder\Editor;
use Mbl\AutoResponder\Plugin;

function mblar_default_template(Plugin $plugin): array
{

    $editor = new Editor($plugin);

    return [
        'id' => null,
        'name' => 'Новый шаблон письма',
        'common' => mblar_template_with_props($editor->common(), [
            'root' => [
                'style' => [
                    'background-color' => '#F7F7F7'
                ]
            ]
        ]),
        'header' => mblar_template_with_props($editor->header(1), [
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
        ]),
        'content' => mblar_template_with_props($editor->content(), []),
        'footer' => mblar_template_with_props($editor->footer(1), [
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
        ]),
    ];
}