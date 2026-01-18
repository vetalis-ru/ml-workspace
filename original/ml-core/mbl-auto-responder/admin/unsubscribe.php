<?php

use Mbl\AutoResponder\Options;
use Mbl\AutoResponder\UnsubscribeUrl;
use Mbl\AutoResponder\UnsubscribeUrlCrypt;
use Mbl\AutoResponder\UserSubscriptions;

$mblarPageName = 'mblar-unsubscribe';
add_action('init', 'mblar_unsubscribe_rewrite_rule');
function mblar_unsubscribe_rewrite_rule()
{
    global $mblarPageName;
    $query = "index.php?pagename=$mblarPageName" . '&s_type=$matches[1]&message_id=$matches[2]';
    add_rewrite_rule("^$mblarPageName/(s)/([^/]*)?", $query, 'top');
    add_rewrite_rule("^$mblarPageName/(a)/([^/]*)?", $query, 'top');
    add_filter('query_vars', 'mblar_unsubscribe_query_vars', 10, 1);
}

function mblar_unsubscribe_query_vars($vars)
{
    $vars[] = 'message_id';
    $vars[] = 's_type';
    return $vars;
}

add_action('set_404', 'mblar_unsubscribe_page_not404', 1, 10);
function mblar_unsubscribe_page_not404(WP_Query $wp_query)
{
    global $mblarPageName;
    if ($wp_query->get('pagename') === $mblarPageName) {
        $wp_query->is_404 = false;
        status_header(200);
    }
}

add_action('template_redirect', 'mblar_unsubscribe_page_redirect', 10);
function mblar_unsubscribe_page_redirect()
{
    global $mblarPageName;
    if (get_query_var('pagename') === $mblarPageName) {
        try {
            list($user_id, $term_id, $mailing_datetime) = (new UnsubscribeUrl(new UnsubscribeUrlCrypt()))->parseUrl();
            $user = get_user_by('id', $user_id);
            if ($user === false) {
                throw new Exception('User not found');
            }
            $subscribe = new UserSubscriptions($user_id);
            if (is_null($term_id) || is_null($mailing_datetime)) {
                $subscribe->unsubscribe();
            } else {
                $subscribe->unsubscribeMailing($term_id, $mailing_datetime);
            }
            wp_redirect((new Options())->byId('unsubscribe_success_url'));
        } catch (Exception $exception) {
            print_r($exception->getMessage());
        }
        die();
    }
}