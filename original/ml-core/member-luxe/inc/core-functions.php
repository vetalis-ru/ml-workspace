<?php



function not_active_admin_menu()
{
    add_menu_page('MEMBERLUX', 'MEMBERLUX', 'manage_options', 'memberluxe-not-active', 'wpm_not_active_memberluxe_page', '', 3);
}

function wpm_not_active_memberluxe_page()
{
    $top_message = '';

    if (isset($_GET['do-action']) && $_GET['do-action'] == 'register') {
        $codeRaw = wpm_array_get($_GET, 'user_code');
        $data = wpm_activation($codeRaw);

        if (!empty($data['message'])) {
            $top_message = '<div class="updated fade"><p>' . $data['message'] . '</p></div>';
        }
        if (!$data['error']) {
             if ($data['is_trial']) : ?>
                <iframe id="wpm-activation-success"
                        src="https://memberlux.com/activation-success/"
                        frameborder="0"
                        style="width: 1px; height: 1px; position:absolute; top: -99999px; left: -99999px;"></iframe>
                <script type="text/javascript">
                    jQuery(function($){
                        $('#wpm-activation-success').load(function() {
                            setTimeout(function(){window.location = "<?php  echo site_url('/wp-admin/edit.php?post_type=wpm-page&page=wpm-activation'); ?>";}, 1500);
                        });
                    });
                </script>
            <?php else: ?>
                <script type="text/javascript">
                    setTimeout(function(){window.location = "<?php  echo site_url('/wp-admin/edit.php?post_type=wpm-page&page=wpm-activation'); ?>";}, 1500);
                </script>
            <?php endif;
        }
    }

    ?>
    <style type="text/css">
        .content-wrap {
            padding: 30px;
            min-height: 400px;
            background-color: #ffffff;
            -webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, .04);
            box-shadow: 0 1px 1px rgba(0, 0, 0, .04);
        }

        .content-wrap form > .alternate {
            padding: 10px 25px;
        }

        .page-title {
            margin: 0 0 30px;
        }

        .activation-page-wrap {
            max-width: 800px;
            padding-bottom: 30px;
        }

        .main-keys-info {
            margin: 50px 0 30px;
        }

        .section-title {
            margin: 0 0 15px;
        }

        .section-title .desc {
            font-weight: normal;
            font-size: 14px;
        }

        .codes-table {
            text-align: left;
            border-collapse: collapse;
            border-spacing: 0;
            width: 100%;

        }

        .codes-table td, .codes-table th {
            border: 1px solid #e6e6e6;
            padding: 10px;
            white-space: nowrap;
        }

        .codes-table .suspended td {
            background-color: #ffedef;
        }

        .codes-table .expired td {
            background-color: #fff4d1;
        }

        .activation-form-wrap {
            border-radius: 5px;
            padding: 15px 20px;
            margin: 30px 0;
            border: 1px solid #e6e6e6;
            background: #f1f1f1;
        }

        .activation-form-wrap input[type="text"] {
            padding: 10px 15px;
            background: #fff;
            border: 1px solid #cccccc;
        }

        .users-limit-info {
            font-size: 15px;
            margin: 15px 0;
        }

        .users-limit-info .limit {
            margin-right: 20px;
            display: inline-block;
            vertical-align: middle;
        }

        .users-limit-info .count {
            font-weight: bold;
        }

        .users-limit-info .button {
            vertical-align: middle;
            margin-left: 20px;
            background-color: #4db252;
            border-color: #37803a;
            color: #fff;
        }

        .users-limit-info .button:hover, .users-limit-info .button:active, .users-limit-info .button:focus {
            background-color: #37803a;
            border-color: #37803a;
            color: #fff;
        }
    </style>
    <div class="options-wrap key-wrap">
        <div class="wrap wpm-options-page">
            <div id="icon-options-general" class="icon32"></div>
            <?php echo $top_message; ?>
            <div class="options-wrap wpm-ui-wrap" style="margin-top: 0!important;">
                <div class="content-wrap" style="background-color: #f1f1f1!important; padding-top: 0!important;">
                    <div class="activation-page-wrap" style="min-width: 100%!important;">
                        <div class="main-keys-info">
		                    <?php if (wpm_key_is_trial()) : ?>
                                <h3 class="section-title">Пробный период MEMBERLUX активирован!</h3>
		                    <?php elseif (wpm_key_is_registered()) : ?>
                                <h3 class="section-title">MEMBERLUX активирован!</h3>
		                    <?php else : ?>
                                <h3 class="section-title">MEMBERLUX не активирован!</h3>
		                    <?php endif; ?>
                            <div class="users-limit-info">
                                <?php if (wpm_key_is_trial() || wpm_key_is_registered()) : ?>
                                    <span class="limit">
                                        Лимит пользователей:
                                        <span class="count"><?php echo wpm_get_users_limit() == -1 ? 'неограниченно' : wpm_get_users_limit(); ?></span>
                                    </span>
    			                    <?php if (wpm_key_is_trial()) : ?>
                                        <span class="limit">
                                            Действителен до:
                                            <span class="count"><?php echo wpm_get_trial_end_date(); ?></span>
                                        </span>
    			                    <?php elseif (wpm_get_users_limit() != -1) : ?>
                                        <span class="limit">
                                            Использовано:
                                            <span class="count"><?php echo wpm_get_total_users(); ?></span>
                                        </span>
    			                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="activation-form">
                            <form method="get" action="">
		                        <?php if (wpm_key_is_trial() || wpm_key_is_registered()) : ?>
                                    <input type="hidden" name="post_type" value="wpm-page">
                                    <input type="hidden" name="page" value="wpm-activation">
		                        <?php else : ?>
                                    <input type="hidden" name="post_type" value="wpm-page">
                                    <input type="hidden" name="page" value="memberluxe-not-active">
		                        <?php endif; ?>
                                <div class="activation-form-wrap" style="background-color: #ffffff!important;">
                                    <h3 class="section-title">
                                        Код активации
                                        <span class="desc">(«Системы MEMBERLUX», «Карты пополнения клиентов», «Премиум-модулей» или «Пробной версии»)</span>
                                    </h3>
                                    <p>
                                        <input
                                            type="text"
                                            name="user_code"
                                            class="widefat"
                                            value=""
                                            placeholder="Вставьте код сюда">
                                    </p>
                                    <button
                                        type="submit"
                                        name="do-action"
                                        value="register"
                                        class="button button-primary">Активировать</button>
                                </div>
                            </form>
                        </div>
                       <?php if (count(wpm_get_all_activation_codes())) : ?>
                           <div class="codes">
                               <h3 class="section-title">Ваши коды активации</h3>
                               <table class="table codes-table" style="background-color: #fff;">
                                   <thead>
                                   <tr>
                                       <th scope="col">Тип</th>
                                       <th scope="col">Код</th>
                                       <?php /* <th scope="col">Время действия</th> */ ?>
                                       <th scope="col">Статус</th>
                                   </tr>
                                   </thead>
                                   <tbody>
                                   <?php foreach (wpm_get_all_activation_codes() as $key) : ?>
                                       <tr class="<?php echo wpm_array_get($key, 'status'); ?>">
                                           <td><?php echo wpm_array_get($key, 'name'); ?></td>
                                           <td><?php echo wpm_array_get($key, 'code'); ?></td>
                                           <?php /*
                                           <td><?php echo wpm_array_get($key, 'duration') == -1 ? 'неограниченно' : (date('d.m.Y', strtotime(wpm_array_get($key, 'time_start'))) . ' - ' . date('d.m.Y', strtotime(wpm_array_get($key, 'time_end')))); ?></td>
                                           */ ?>
                                           <td><?php echo wpm_get_key_type_name(wpm_array_get($key, 'status')); ?></td>
                                       </tr>
                                   <?php endforeach; ?>
                                   </tbody>
                               </table>
                           </div>
                       <?php endif; ?>
                        <?php $not_installed = mbl_not_installed(); if (count($not_installed)): ?>
                            <h3 style="margin-top: 50px;" class="section-title">Не установленные дополнения</h3>
                            <table class="table codes-table" style="margin-bottom: 20px;">
                                <?php foreach ($not_installed as $item): ?>
                                    <tr><td><?= $item['name'] ?></td></tr>
                                <?php endforeach; ?>
                            </table>
                            <iframe width="560" height="315" src="https://www.youtube.com/embed/t-v0cWrRFhQ" title="Смена Модели Монетизации MEMBERLUX" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                            <h3 style="margin-top: 50px;" class="section-title">
                                🔗 <a href="https://memberlux.com/08" target="_blank">Оставить заявку на персональный набор модулей</a>
                            </h3>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
}

if (wpm_key_is_registered() || wpm_key_is_trial()) {
    add_action('plugins_loaded', 'wpm_check_users_limit_notice_callback');

    add_action('init', 'wpm_page_post_type');
    add_action('init', 'wpm_taxonomies', 0);
    add_action('init', 'wpm_user_level_taxonomies', 5);

    add_action('admin_menu', 'wpm_admin_menu');

    add_action('init', 'wpm_rewrite_init');
    add_action('init', 'wpm_remove_all_tinymce_ext_plugins', 10000);
    add_filter('tiny_mce_before_init', 'wpm_tinymce_config', 9998);
    add_filter('template_include', 'wpm_get_template', 100);
    add_action('template_redirect', 'wpm_template_redirect', 100);
    add_action('admin_bar_menu', 'wpm_admin_nav_bar', 999);
    add_action('admin_menu', 'wpm_admin_menu_customer', 999);

    add_action('pre_get_posts', 'wpm_custom_number_of_posts', 1);
    add_filter('pre_get_posts', 'wpm_custom_get_posts');

} else {
    add_action('admin_menu', 'not_active_admin_menu', 999);
}

function wpm_check_users_limit_notice_callback() {
	wpm_check_users_limit_notice();
}

function wpm_check_users_limit_notice()
{
    if (!is_admin()) return;
    $usersTotal = wpm_get_total_users();
    $limit = wpm_get_users_limit();

    if($limit != -1 && ($usersTotal >= $limit || $usersTotal/$limit >= .9)) {
	    add_action('admin_notices', 'wpm_add_users_limit_notice');
    }
}

function wpm_add_users_limit_notice()
{
	$message = 'Количество пользователей MEMBERLUX: <b>' . wpm_get_total_users() . '</b>. ';
	$message .= 'Доступный лимит: <b>' . wpm_get_users_limit() . '</b>.';

	echo "<div class='notice notice-warning is-dismissible'><p>{$message}</p></div>";
}

function wpm_get_users_limit()
{
    return wpm_array_get(wpm_get_key_data(), 'total_users', 0);
}

function wpm_is_users_overflow()
{
	$limit = wpm_get_users_limit();

	return $limit > -1 && $limit <= wpm_get_total_users();
}

function wpm_get_total_users()
{
    $users_count = count_users();

    $blocked = get_users(array(
        'meta_key' => 'wpm_status',
        'meta_value' => 'inactive',
        'role__in' => array('customer','coach'),
        'role__not_in' => array('administrator'),
        'fields' => 'ids'
    ));


    return wpm_array_get($users_count, 'avail_roles.customer', 0)
        + wpm_array_get($users_count, 'avail_roles.coach', 0)
        - count($blocked);
}

function wpm_get_key_data()
{
    $data = get_option('wpm_key_data', wpm_get_empty_key_data());

    if(!is_array($data)) {
	    $data = wpm_get_empty_key_data();
    }

    return $data;
}

function wpm_get_empty_key_data()
{
    return [
        'response_status' => '',
        'response_code'   => '',
        'keys'            => [],
        'keys_info'       => [],
        'total_users'     => 0,
        'last_check'      => 0,
        'offer'           => []
    ];
}

function wpm_register_key()
{
    wpm_activation();
}

function wpm_activation($code = null)
{
    if ( !defined('MBL_URL') ) {
        define('MBL_URL', '');
    }

    $successCodes = array(
	    400,
	    500,
	    501,
	    502,
	    503,
	    504,
	    505,
	    506,
	    507,
	    508,
	    509,
	    550,
	    571,
	    572,
	    573,
	    574,
	    575,
	    576,
	    577,
	    578,
	    579,
        580,
        581,
        582,
        583,
        584,
	    585,
	    586,
	    587,
        588,
        589,
        590,
        591,
        592,
        593,
        594,
        595,
        596,
    );

	$keyData = wpm_get_key_data();

	if (null !== $code) {
		$codes = array(trim($code));
		$action = 'activation';
	} else {
		$codes = array();
		$action = 'check';
	}

	$base_url = 'aHR0cHM6Ly9hcGl2Mi5tZW1iZXJsdXguY29tL2FwaS92Mg==';

	$current_user = wp_get_current_user();

	preg_match('%[^/]+\.[^/:]+%m', get_bloginfo('wpurl'), $matches);

	$site = !empty($matches) ? $matches[0] : MBL_URL;

	$data = wp_remote_post(base64_decode($base_url), array(
			'method'      => 'POST',
			'timeout'     => 45,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking'    => true,
			'headers'     => array(),
			'body'        => array(
				'action'     => $action,
				'codes'      => $codes,
				'site'       => $site,
				'user_email' => $current_user->user_email,
				'user_name'  => $current_user->user_firstname,
			),
			'cookies'     => array(),
		)
	);

    $result = array(
		'message' => '',
		'error'   => false,
		'data'    => $data,
        'is_trial' => false,
	);

	if (is_wp_error($data)) {
        $error_message = $data->get_error_message();
        $error_code = $data->get_error_code();

        if ($error_code === 'http_request_failed' && str_contains($error_message, 'timed out')) {
            $keyData['last_check'] = current_time('mysql');
            update_option('wpm_key_data', $keyData);
        }

		/*
		 * Request failed
		 */
		$result['message'] = 'Запрос не удался.';
		$result['error'] = true;
	} else {
		if ((int)$data['response']['code'] !== 200) {
			/*
			 * API key issues
			 */
			$body = json_decode($data['body'], true);
			$result['message'] = $body['message'];
			$result['error'] = true;
		} else {
			/*
			 * Success
			 */
			$body = json_decode($data['body'], true);

            if (!array_key_exists('response_code', $body) || !array_key_exists('response_status', $body)) {
                $result['message'] = 'Запрос не удался.';
                $result['error'] = true;
            } else {
                if ($action === 'activation') {
                    if(in_array($body['response_code'], $successCodes)) {

                        $keyInfo = wpm_array_get($body, 'activated_code', array());
                        if (!empty($keyInfo)) {
                            $keyData['keys_info'][] = $keyInfo;
                        }
                    }
                    if($body['response_status'] == 'trial_activated') {
                        $result['is_trial'] = true;
                    }
                    $keyData['last_check'] = '';
                } else {
                    $keyData['last_check'] = current_time('mysql');
                    $keyData['response_status'] = $body['response_status'];
                    $keyData['response_code'] = $body['response_code'];
                    $keyData['message'] = $body['message'];
                }

                $keys = wpm_array_get($body, 'keys', array());
                if (!empty($keys)) {
                    $keyData['keys'] = $keys;
                }

                if(isset($body['total_users'])) {
                    $keyData['total_users'] = intval($body['total_users']);
                }

                $notifications = wpm_get_notifications();
                $notificationIds = wpm_array_pluck($notifications, 'id');

                foreach (wpm_array_get($body, 'notifications', array()) as $notification) {
                    if(!in_array(wpm_array_get($notification, 'id'), $notificationIds)) {
                        $notifications[] = $notification;
                    }
                }

                $keyData['offer'] = wpm_array_get($body, 'offer', array());

                $result['message'] = $body['message'];
                $result['error'] = !in_array($body['response_code'], $successCodes);

                if (isset($keyData['notifications'])) {
                    unset($keyData['notifications']);
                }

                update_option('wpm_key_data', $keyData);
                update_option('wpm_core_notifications', $notifications);
            }
		}
	}

	if(!$result['error'] && $action === 'activation') {
	    wpm_activation();
    }

	return $result;
}

function wpm_get_notifications()
{
    $notifications = get_option('wpm_core_notifications');

    if (!is_array($notifications)) {
        $notifications = wpm_array_get(wpm_get_key_data(), 'notifications', []);
    }

    return $notifications;
}

function wpm_key_is_registered()
{
    $result = wpm_array_get(wpm_get_key_data(), 'response_status') == 'has_active_codes';

    return !wpm_key_is_trial() && $result;
}

function wpm_key_is_trial()
{
    $keysData = wpm_array_get(wpm_get_key_data(), 'keys.active', array());

    $types = wpm_array_pluck($keysData, 'type');

    return count($types) && !count(array_diff($types, array('trial')));
}

function wpm_get_trial_end_date()
{
    $date = 0;

	foreach (wpm_array_get(wpm_get_key_data(), 'keys.active', array()) as $activeKey) {
        if($activeKey['type'] == 'trial') {
            $endDate = strtotime($activeKey['time_end']);

            if($endDate > $date) {
                $date = $endDate;
            }
        }
    }

	return date('d.m.Y', $date);
}

function wpm_get_all_activation_codes()
{
    $data = wpm_get_key_data();

    return array_merge(
        wpm_array_get($data, 'keys.active', array()),
        wpm_array_get($data, 'keys.suspended', array()),
        wpm_array_get($data, 'keys.expired', array())
    );
}

function wpm_get_key_type_name($type)
{
	$result = '';

	switch ($type) {
		case 'used':
		case 'active':
			$result = 'активирован';
			break;
		case 'suspended':
			$result = 'заблокирован';
			break;
		case 'expired':
			$result = 'завершен';
			break;
	}

	return $result;
}

function wpm_check_registration($code)
{
    $code = trim($code);
    $base_url = 'aHR0cDovL2FwaS53cHBhZ2UucnUvYXBpL3JlZ2lzdGVyLw==';

    global $current_user;
    get_currentuserinfo();

    $args = array(
        'timeout' => '50',
        'headers' => array(
            'site' => get_bloginfo('wpurl')
        )
    );
    preg_match('%[^/]+\.[^/:]{2,3}%m', get_bloginfo('wpurl'), $matches);

    $site = !empty($matches) ? $matches[0] : '';

    $request_info = '';
    $request_info .= 'code=' . $code;
    $request_info .= '&site=' . $site;
    $request_info .= '&user_email=' . $current_user->user_email;
    $request_info .= '&user_name=' . $current_user->user_firstname;

    $data = wp_remote_get(base64_decode($base_url) . '?' . $request_info, $args);

    $result = array(
        'message' => '',
        'error'   => false,
        'data'    => $data
    );

    if (is_wp_error($data)) {
        /*
         * Request faild
         */
        $result['message'] = 'Запрос не удался.';
        $result['error'] = true;
    } else if ((int)$data['response']['code'] !== 200) {
        /*
         * API key issues
         */
        $body = json_decode($data['body']);
        $result['message'] = $body->message;
        $result['error'] = true;
    } else {
        /*
         * Success
         */
        $body = json_decode($data['body']);

        $units = isset($body->code->units) ? $body->code->units : 'months';

        if ($body->status == 'registered') {
            $key_args = array(
                'code'       => $code,
                'status'     => $body->status,
                'duration'   => $body->code->duration,
                'units'      => $units,
                'time_start' => $body->code->time_start,
                'time_end'   => $body->code->time_end,
                'last_check' => current_time('mysql')
            );
            update_option('wpm_key', $key_args);
            $result['message'] = $body->message;
            $result['error'] = false;

        } elseif ($body->status == 'used') {
            $result['message'] = $body->message;
            $result['error'] = true;

        } elseif ($body->status == 'suspended') {

            $key_args = array(
                'code'       => $code,
                'status'     => $body->status,
                'duration'   => $body->code->duration,
                'units'      => $units,
                'time_start' => $body->code->time_start,
                'time_end'   => $body->code->time_end,
                'last_check' => current_time('mysql')
            );
            update_option('wpm_key', $key_args);

            $result['message'] = $body->message;
            $result['error'] = true;
        } else {
            $result['message'] = $body->message;
            $result['error'] = false;
        }

    }
    return $result;

}

add_action('init', 'wpm_check_key_authentication');
function wpm_check_key_authentication()
{
	if(wpm_array_get($_GET, 'page') == 'wpm-info-panel') { //TODO::REMOVE
	    wpm_activation();
    }

	$currentTime = current_time('mysql');

	$keyData = wpm_get_key_data();
	$lastCheck = wpm_array_get($keyData, 'last_check', '');

	if ($lastCheck == '' || strtotime($currentTime) > strtotime($lastCheck . '+ 1 day')) {
		wpm_activation();
	}
}

function wpm_is_key_2_0_method()
{
    $userKey = get_option('wpm_key_data');
    return !empty($userKey) && is_array($userKey);
}

add_filter('tag_row_actions','wpm_post_row_actions_filter', 10, 2);

function wpm_post_row_actions_filter($actions, $tag)
{
   if ($tag->taxonomy =="wpm-levels"){
       unset($actions['view']);
   }
   return $actions;
}

function wpm_notification_is_read($id)
{
    $read_notifications = get_option('wpm_read_notifications', array());

    return in_array($id, $read_notifications);
}

function wpm_read_notifications()
{
    $notifications = wpm_get_notifications();
    $ids = wpm_array_pluck($notifications, 'id');
    update_option('wpm_read_notifications', $ids);
}

