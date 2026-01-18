<?php

use Mbl\AutoResponder\Options;
use Mbl\AutoResponder\Plugin;
use Mbl\AutoResponder\UnsubscribeUrl;
use Mbl\AutoResponder\UnsubscribeUrlCrypt;

add_shortcode('m_unsubscribe_page', 'm_unsubscribe_page');
add_action('wp_enqueue_scripts', 'm_unsubscribe_page_assets');
function m_unsubscribe_page(): string
{
    return '<div id="mblar-unsubscribe-root"></div>';
}

function m_unsubscribe_page_assets()
{
    global $post;
    if ($post instanceof WP_Post && has_shortcode($post->post_content, 'm_unsubscribe_page')) {
        $plugin = new Plugin();
        $version = $plugin->assets_version();
        wp_register_script('mblar-unsubscribe', $plugin->assets_uri('js/unsubscribe.js?v=' . $version));
        wp_localize_script('mblar-unsubscribe', '__mblar_data__', $plugin->js_data());
        wp_enqueue_script('mblar-unsubscribe');
    }
}
/*
add_shortcode('m_unsubscribe_url', 'm_unsubscribe_url');

function m_unsubscribe_url(): string
{
    return (new Options())->byId('unsubscribe_url');
}
*/
add_shortcode('m_unsubscribe_all', 'm_unsubscribe_all');
function m_unsubscribe_all($attributes): string
{
    if (empty($attributes)) {
        $render = '[m_unsubscribe_all]';
    } else {
        $url = new UnsubscribeUrl(new UnsubscribeUrlCrypt());
        $link = $url->url($attributes['user_id']);
        $text = (new Options())->byId('unsubscribe_all');
        $render = "<a href=\"$link\">$text</a>";
    }
    return $render;
}

add_shortcode('m_unsubscribe_current', 'm_unsubscribe_current');
function m_unsubscribe_current($attributes): string
{
    if (empty($attributes)) {
        $render = '[m_unsubscribe_all]';
    } else {
        $url = new UnsubscribeUrl(new UnsubscribeUrlCrypt());
        $link = $url->url(
            $attributes['user_id'],
            ['term_id' => $attributes['term_id'], 'mailing_datetime' => $attributes['mailing_datetime']]
        );
        $text = (new Options())->byId('unsubscribe_current');
        $render = "<a href=\"$link\">$text</a>";
    }
    return $render;
}

add_shortcode('m_unsubscribe_form_url', 'm_unsubscribe_form_url');
function m_unsubscribe_form_url($attributes): string
{
    if (empty($attributes)) {
        $render = '[m_unsubscribe_all]';
    } else {
        $url = new UnsubscribeUrl(new UnsubscribeUrlCrypt());
        $render = $url->formUrl($attributes['user_id'], $attributes['term_id'], $attributes['mailing_datetime']);
    }
    return $render;
}

add_filter('mblar_mail_content', 'mblar_mail_content_shortcodes', 10, 2);
function mblar_mail_content_shortcodes($message, $mailing_info)
{
    if (has_shortcode($message, 'm_unsubscribe_all')) {
        if (!empty($mailing_info)) {
            $attributes = " user_id=\"{$mailing_info['user_id']}\"";
            $message = preg_replace('/\[m_unsubscribe_all]/', "[m_unsubscribe_all$attributes]", $message);
        }
    }
    if (has_shortcode($message, 'm_unsubscribe_current')) {
        if (!empty($mailing_info)) {
            $attributes = " user_id=\"{$mailing_info['user_id']}\"";
            $attributes .= " term_id=\"{$mailing_info['term_id']}\"";
            $attributes .= " mailing_datetime=\"{$mailing_info['mailing_datetime']}\"";
            $message = preg_replace('/\[m_unsubscribe_current]/', "[m_unsubscribe_current$attributes]", $message);
        }
    }
    if (has_shortcode($message, 'm_unsubscribe_form_url')) {
        if (!empty($mailing_info)) {
            $attributes = " user_id=\"{$mailing_info['user_id']}\"";
            $attributes .= " term_id=\"{$mailing_info['term_id']}\"";
            $attributes .= " mailing_datetime=\"{$mailing_info['mailing_datetime']}\"";
            $message = preg_replace('/\[m_unsubscribe_form_url]/', "[m_unsubscribe_form_url$attributes]", $message);
        }
    }

    return $message;
}
