<?php

use Mbl\AutoResponder\Editor;

foreach (glob(__DIR__ . '/**/*.tpl.php') as $file) {
    if (is_file($file)) {
        require_once $file;
    }
}
require_once __DIR__ . '/content.tpl.php';
require_once __DIR__ . '/mblar_default_template.php';

function mblar_get_template($template): array
{
    return mblar_template_with_props($template, $template['default_props']);
}

function mblar_header_templates($plugin): array
{
    return array_map('mblar_get_template', apply_filters('mblar_header_templates', [], $plugin));
}

function mblar_footer_templates($plugin): array
{
    return array_map('mblar_get_template', apply_filters('mblar_footer_templates', [], $plugin));
}

function mblar_template_with_props($template, $options): array
{
    foreach ($options as $node_id => $props) {
        if ($template['tree'][$node_id]['type'] === 'img' && !empty($props['thumbnail'])) {
            $template['tree'][$node_id]['props']['src'] = wp_get_attachment_image_url($props['thumbnail'], 'full');
        }
        foreach ($props as $name => $prop) {
            if ($name === 'children') {
                $template['tree'][$node_id]['children'] = $prop;
            } else {
                if (is_array($prop)) {
                    $template['tree'][$node_id]['props'][$name] = array_merge(
                        $template['tree'][$node_id]['props'][$name] ?? [],
                        $prop
                    );
                } else {
                    $template['tree'][$node_id]['props'][$name] = $prop;
                }
            }
        }
    }

    return $template;
}

/**
 * @param Editor $editor
 * @param $editor_state
 * @return array
 */
function mblar_setting_to_array(Editor $editor, $editor_state): array
{
    $result = [];
    foreach (['common', 'content', 'header', 'footer'] as $block_name) {
        $block_state = $editor_state[$block_name];
        $block = $editor->$block_name($block_state['id']);
        $result[$block_name] = [
            'id' => $block['id'],
            'props' => mblar_get_settings($block, $block_state)
        ];
    }

    return $result;
}

function mblar_get_settings($block, $block_state): array
{
    $result = [];
    foreach ($block['settings'] as $setting) {
        $result = array_merge_recursive($result, mblar_get_setting_props($setting, $block_state));
    }

    return $result;
}

function mblar_get_setting_props($setting, $block): array
{
    $tree = $block['tree'];
    $node_id = $setting['node_id'];
    $style = $tree[$node_id]['props']['style'];
    switch ($setting['type']) {
        case 'border_radius':
            $props = [
                'style' => [
                    'border-top-left-radius' => $style['border-top-left-radius'],
                    'border-top-right-radius' => $style['border-top-right-radius'],
                    'border-bottom-left-radius' => $style['border-bottom-left-radius'],
                    'border-bottom-right-radius' => $style['border-bottom-right-radius'],
                ]
            ];
            break;
        case 'bg_color':
            $props = [
                'style' => [
                    'background-color' => $style['background-color']
                ]
            ];
            break;
        case 'padding':
            $props = [
                'style' => [
                    'padding-top' => $style['padding-top'],
                    'padding-right' => $style['padding-right'],
                    'padding-bottom' => $style['padding-bottom'],
                    'padding-left' => $style['padding-left'],
                ]
            ];
            break;
        case 'padding_tb':
            $props = [
                'style' => [
                    'padding-top' => $style['padding-top'],
                    'padding-bottom' => $style['padding-bottom'],
                ]
            ];
            break;
        case 'borders':
            $props = [
                'style' => [
                    'border-left-color' => $style['border-left-color'],
                    'border-left-style' => $style['border-left-style'],
                    'border-left-width' => $style['border-left-width'],
                    'border-right-color' => $style['border-right-color'],
                    'border-right-style' => $style['border-right-style'],
                    'border-right-width' => $style['border-right-width'],
                    'border-top-color' => $style['border-top-color'],
                    'border-top-style' => $style['border-top-style'],
                    'border-top-width' => $style['border-top-width'],
                    'border-bottom-color' => $style['border-bottom-color'],
                    'border-bottom-style' => $style['border-bottom-style'],
                    'border-bottom-width' => $style['border-bottom-width'],
                ]
            ];
            break;
        case 'image':
            $props = [
                'thumbnail' => $tree[$setting['node_id']]['props']['thumbnail']
            ];
            break;
        case 'image_size':
            $props = [
                'height' => $tree[$setting['node_id']]['props']['height'],
                'width' => $tree[$setting['node_id']]['props']['width'],
            ];
            break;
        case 'align':
            $props = [
                'align' => $tree[$setting['node_id']]['props']['align']
            ];
            break;
        case 'text':
            $props = [
                'children' => $tree[$setting['node_id']]['children']
            ];
            break;
        default:
            $props = [];
    }

    return [$node_id => $props];
}