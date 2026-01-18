<?php
add_action('wp_ajax_wpm_justclick_groups_select', 'wpm_justclick_groups_select');

function wpm_justclick_groups_select()
{
    $groups = MBLSubscription::direct('justclick', 'getAllGroups', array());
    $error = MBLSubscription::direct('justclick', 'getError');

    if (count($groups)) {
        $html = '<label>' . __('Группа контактов', 'wpm') . '<br>';
        $html .= '<select name="main_options[auto_subscriptions][justclick][rid]">'
            . '<option value=""></option>';
        foreach ($groups as $group) {
            $html .= '<option'
                . ' value="' . $group['rass_name'] . '"'
                . (wpm_option_is('auto_subscriptions.justclick.rid', $group['rass_name']) ? ' selected="selected"' : '')
                . '>' . $group['rass_title'] . '</option>';
        }
        $html .= '</select>';
        $html .= '</label>';
    } else {
        $html = sprintf('<span class="wpm_error">%s: %s</span>', __('Ошибка', 'wpm'), $error);
    }

    echo $html;
    die();
}

add_action('wp_ajax_wpm_sendpulse_get_groups', 'wpm_sendpulse_get_groups');

function wpm_sendpulse_get_groups()
{
    $groups = MBLSubscription::direct('sendpulse', 'listAddressBooks', array());
    $error = MBLSubscription::direct('sendpulse', 'getError');
    if (is_array($groups)) {
        $html = '<label>' . __('Адресные книги', 'wpm') . '<br>';
        $html .= '<select name="main_options[auto_subscriptions][sendpulse][rid]">';
        $html .= '<option value=""></option>';
        foreach ($groups as $group) {
            $html .= '<option value="'.$group->id.'"'
                . (wpm_option_is('auto_subscriptions.sendpulse.rid', $group->id) ? ' selected="selected"' : '')
                .'>'. $group->name.'</option>';
        }
        $html .= '</select>';
        $html .= '</label>';
    } else {
        $html = sprintf('<span class="wpm_error">%s: %s</span>', __('Ошибка', 'wpm'), $error);
    }
    echo $html;
    die();
}

add_action('wp_ajax_wpm_autoweboffice_get_groups', 'wpm_autoweboffice_get_groups');

function wpm_autoweboffice_get_groups()
{
    $groups = MBLSubscription::direct('autoweb', 'getAllGroups');
    $error = MBLSubscription::direct('autoweb', 'getErrors');

    if (is_array($groups) && $groups[0] != new stdClass()) {
        $html = '<label>' . __('Группа подписчиков', 'wpm') . '<br>';
        $html .= '<select name="main_options[auto_subscriptions][autoweb][rid]">';
        $html .= '<option value=""></option>';
        foreach ($groups as $group) {
            $html .= '<option value="'.$group->id_newsletter.'"'
             . (wpm_option_is('auto_subscriptions.autoweb.rid', $group->id_newsletter) ? ' selected="selected"' : '')
            .'>'. $group->newsletter.'</option>';
        }
        $html .= '</select>';
        $html .= '</label>';
    } else {
        if (wpm_option_is('auto_subscriptions.autoweb.subdomain', '')){

            $errorAutoweb = 'Не введен идентификатор магазина ';

            if (wpm_option_is('auto_subscriptions.autoweb.apiKeyRead', '')){
                $errorAutoweb .= 'и Секретный ключ API KEY GET';
            }
            if (wpm_option_is('auto_subscriptions.autoweb.apiKeyWrite', '')){
                $errorAutoweb .= 'и Секретный ключ API KEY SET';
            }

            $html = sprintf('<span class="wpm_error">%s: %s</span>', __('Ошибка', 'wpm'), $errorAutoweb);

        } else {
            $html = sprintf('<span class="wpm_error">%s: %s</span>', __('Ошибка', 'wpm'), $groups);
        }
    }

    echo $html;
    die();
}

add_action('wp_ajax_wpm_justclick_groups_level_select', 'wpm_justclick_groups_level_select');

function wpm_justclick_groups_level_select()
{
    $termId = intval(wpm_array_get($_POST, 'term_id'));
    $html = '';

    if ($termId) {
        $options = get_option("taxonomy_term_{$termId}");
        $groups = MBLSubscription::direct('justclick', 'getAllGroups', array());
        $error = MBLSubscription::direct('justclick', 'getError');

        if (count($groups)) {
            $html .= '<label>' . __('Выбор группы', 'wpm');
            $html .= '<p><select name="term_meta[auto_subscriptions][justclick][rid]">'
                . '<option value=""></option>';
            foreach ($groups as $group) {
                $html .= '<option'
                    . ' value="' . $group['rass_name'] . '"'
                    . (wpm_array_get($options, 'auto_subscriptions.justclick.rid') == $group['rass_name'] ? ' selected="selected"' : '')
                    . '>' . $group['rass_title'] . '</option>';
            }
            $html .= '</select></p>';
            $html .= '</label>';
        } else {
            $html = sprintf('<span class="wpm_error">%s: %s</span>', __('Ошибка', 'wpm'), $error);
        }
    }

    echo $html;
    die();
}

add_action('wp_ajax_wpm_sendpulse_groups_level_select', 'wpm_sendpulse_groups_level_select');

function wpm_sendpulse_groups_level_select()
{
    $termId = intval(wpm_array_get($_POST, 'term_id'));
    $html = '';

    if ($termId) {
        $options = get_option("taxonomy_term_{$termId}");
        $groups = MBLSubscription::direct('sendpulse', 'listAddressBooks', array());
        $error = MBLSubscription::direct('sendpulse', 'getError');

        if (is_array($groups)) {
            $html = '<label>' . __('Адресные книги', 'wpm');
            $html .= '<p><select name="term_meta[auto_subscriptions][sendpulse][rid]">';
            $html .= '<option value=""></option>';
            foreach ($groups as $group) {
                $html .= '<option value="'.$group->id.'"'
                    . (wpm_array_get($options, 'auto_subscriptions.sendpulse.rid') == $group->id ? ' selected="selected"' : '')
                    .'>'. $group->name.'</option>';
            }
            $html .= '</select></p>';
            $html .= '</label>';
        } else {
            $html = sprintf('<span class="wpm_error">%s: %s</span>', __('Ошибка', 'wpm'), $error);
        }
    }

    echo $html;
    die();
}

add_action('wp_ajax_wpm_autoweb_groups_level_select', 'wpm_autoweb_groups_level_select');

function wpm_autoweb_groups_level_select()
{
    $termId = intval(wpm_array_get($_POST, 'term_id'));
    $html = '';

    if ($termId) {
        $options = get_option("taxonomy_term_{$termId}");
        $groups = MBLSubscription::direct('autoweb', 'getAllGroups', array());
        $error = MBLSubscription::direct('autoweb', 'getErrors');

        if (is_array($groups) && $groups[0] != new stdClass()) {
            $html .= '<label>' . __('Выбор группы', 'wpm');
            $html .= '<p><select name="term_meta[auto_subscriptions][autoweb][rid]">';
            $html .= '<option value=""></option>';
            foreach ($groups as $group) {
                $html .= '<option value="'.$group->id_newsletter.'"'
                    . (wpm_array_get($options, 'auto_subscriptions.autoweb.rid') == $group->id_newsletter ? ' selected="selected"' : '')
                    .'>'. $group->newsletter.'</option>';
            }
            $html .= '</select></p>';
            $html .= '</label>';
        } else {
            if (wpm_option_is('auto_subscriptions.autoweb.subdomain', '')){

                $errorAutoweb = 'Не введен идентификатор магазина ';

                if (wpm_option_is('auto_subscriptions.autoweb.apiKeyRead', '')){
                    $errorAutoweb .= 'и Секретный ключ API KEY GET ';
                }
                if (wpm_option_is('auto_subscriptions.autoweb.apiKeyWrite', '')){
                    $errorAutoweb .= 'и Секретный ключ API KEY SET ';
                }

                $html = sprintf('<span class="wpm_error">%s: %s</span>', __('Ошибка', 'wpm'), $errorAutoweb);

            } else {
                $html = sprintf('<span class="wpm_error">%s: %s</span>', __('Ошибка', 'wpm'), $groups);
            }
        }
    }

    echo $html;
    die();
}

add_action('wp_ajax_wpm_justclick_groups_del_level_select', 'wpm_justclick_groups_del_level_select');

function wpm_justclick_groups_del_level_select()
{
    $termId = intval(wpm_array_get($_POST, 'term_id'));
    $html = '';

    if ($termId) {
        $options = get_option("taxonomy_term_{$termId}");
        $groups = MBLSubscription::direct('justclick', 'getAllGroups', array());
        $error = MBLSubscription::direct('justclick', 'getError');

        if (count($groups)) {
            $del_rid = wpm_array_get($options, 'auto_subscriptions.justclick.del_rid', array());
            $html .= '<select name="term_meta[auto_subscriptions][justclick][del_rid][]" multiple="multiple" size="' . min(count($groups), 5) . '">';
            foreach ($groups as $group) {
                $isSelected = is_array($del_rid) && in_array($group['rass_name'], $del_rid);
                $html .= '<option'
                    . ' value="' . $group['rass_name'] . '"'
                    . ($isSelected ? ' selected="selected"' : '')
                    . '>' . $group['rass_title'] . '</option>';
            }
            $html .= '</select>';
        } else {
            $html = sprintf('<span class="wpm_error">%s: %s</span>', __('Ошибка', 'wpm'), $error);
        }
    }

    echo $html;
    die();
}

function wpm_levels_show_subscriptions()
{
    return (bool)count(wpm_array_filter(wpm_array_pluck(wpm_get_option('auto_subscriptions'), 'active'), 'on'));
}

//add_action('user_register', 'mbl_subscription_user_register', 100, 1);

function mbl_subscription_user_register($userId)
{
    $user = get_userdata($userId);

    if (in_array('customer', $user->roles)) {
        $levelId = wpm_array_get(MBLTermKeysQuery::getUserTermIds($userId), 0);
        if ($levelId) {
            MBLSubscription::add($userId, $levelId);
        }
    }
}

add_action('profile_update', 'mbl_subscription_user_update', 1, 1);

function mbl_subscription_user_update($userId)
{
    $user = get_userdata($userId);

    if (in_array('customer', $user->roles)) {
        MBLSubscription::update($userId);
    }
}