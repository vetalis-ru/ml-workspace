<?php

use Mbl\AutoResponder\Editor;
use Mbl\AutoResponder\Plugin;

function mblar_createElement($type, $props = [], $children = []): string
{
    $attributes = '';
    foreach ($props as $name => $value) {
        if ($name === 'style') {
            $styles = [];
            foreach ($value as $k => $v) {
                $styles[] = "$k:$v";
            }
            $styles = implode(';', $styles);
            $attributes .= " $name=\"$styles\"";
        } else {
            $attributes .= " $name=\"$value\"";
        }
    }
    if (!empty($children)) {
        $_children = implode('', $children);
        if ($type === 'text') {
            $result = $_children;
        } else {
            $result = "<$type{$attributes}>$_children</$type>";
        }
    } else {
        $result = "<$type{$attributes}/>";
    }

    return $result;
}

function mblar_renderTreeV2($tree, $id = 'root'): string
{
    $props = $tree[$id]['props'] ?? [];
    $_children = $tree[$id]['children'] ?? [];
    if (!empty($_children)) {
        if (is_array($_children)) {
            foreach ($_children as $_id) {
                $children[] = mblar_renderTreeV2($tree, $_id);
            }
        } else {
            $children = [$_children];
        }
    }

    return mblar_createElement($tree[$id]['type'], $props, $children ?? []);
}

function mblar_render_email($data, $message, $mailing_info = []): string
{
    $props = json_decode($data, true);
    $editor = new Editor(new Plugin());
    $common = mblar_template_with_props($editor->common(), $props['common']['props']);
    $header = mblar_renderTreeV2(
        mblar_template_with_props($editor->header($props['header']['id']),
            $props['header']['props'])['tree']
    );
    $content_with_message = mblar_template_with_props($editor->content(), $props['content']['props']);
    $content_with_message['tree']['content']['children'] = do_shortcode(apply_filters(
        'mblar_mail_content',
        wpautop($message),
        $mailing_info
    ));
    $content = mblar_renderTreeV2($content_with_message['tree']);
    $footer = mblar_renderTreeV2(
        mblar_template_with_props($editor->footer($props['footer']['id']),
            $props['footer']['props'])['tree']
    );
    return "<!doctype html>
    <html>
        <head>
        <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\"/>
        <meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />
        <style>.mblar-content img{max-width: 100%;height: auto;}</style>
        </head>
        <body style='background-color: {$common['tree']['root']['props']['style']['background-color']}'>
        <div style='padding-top: {$common['tree']['root']['props']['style']['padding-top']};
            padding-bottom: {$common['tree']['root']['props']['style']['padding-bottom']};
            background-color: {$common['tree']['root']['props']['style']['background-color']}'> 
        <table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\"
           style=\"mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;margin:0;
           width:100%;height:100%;
           background-position:center top;background-repeat: no-repeat;background-size: cover;
           background-color: {$common['tree']['root']['props']['style']['background-color']};\">
            <tr>
                <td valign=\"top\" align=\"center\" style=\"padding: 0;margin:0;\">
                <table style=\"width:100%;height:100%;border:0;max-width: 600px;\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
                <tr>
                    <td>
                         $header
                         $content
                         $footer
                    </td>
                </tr>                
                </table>
                </td>
            </tr>
        </table>
    </div></body></html>";
}

function mblar_render_email_without_template($message, $mailing_info = []): string
{
    return do_shortcode(apply_filters('mblar_mail_content', wpautop($message), $mailing_info));
}