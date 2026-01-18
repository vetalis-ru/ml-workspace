<?php
/**
 * Plugin Name: ML Grant Bridge
 * Description: Выдача учебных доступов MemberLux из amoCRM
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/includes/import.php';
require_once __DIR__ . '/includes/duplicate-check.php';


/* =====================    Регистрация REST endpoint =====================  */

add_action('rest_api_init', function () {
    register_rest_route('mlgb/v1', '/grant', [
        'methods'             => 'POST',
        'callback'            => 'mlgb_handle_grant',
        'permission_callback' => '__return_true',
    ]);

    mlgb_log_safe('BOOT: REST endpoint registered', [
        'result' => 'ok',
    ]);
});
   
/* =====================    Основной обработчик =====================  */

function mlgb_handle_grant(WP_REST_Request $request)
{

    mlgb_log_safe('REST ENTRYPOINT HIT', [
        'result' => 'called',
    ]);

    /* ---------- Проверка HMAC SIGNATURE (timestamp + body) ---------- */
    $hmac_result = mlgb_verify_hmac_request();
    if ($hmac_result instanceof WP_REST_Response) {
        return $hmac_result;
    }

    mlgb_log_safe('STEP 1: REQUEST RECEIVED', [
        'result' => 'ok',
    ]);


// ---------- STEP 1: input ----------    

    $email = trim($request->get_param('email'));
    $key   = $request->get_param('key');

    if (!$email || !is_array($key)) {
    mlgb_log_safe('INVALID INPUT', [
        'error' => 'invalid_input',
    ]);
        return new WP_REST_Response(['error' => 'invalid_input'], 400);
    }

    $term_id  = (int)($key['term_id'] ?? 0);
    $duration = (int)($key['duration'] ?? 0);
    $units    = $key['units'] ?? '';

    mlgb_log_safe('STEP 1: INPUT PARSED', [
        'term_id' => (int)$term_id,
        'result'  => 'parsed',
    ]);


// ----------   STEP 2: ВЫДАЧА УД НОВОМУ ИЛИ СУЩЕСТВУЮЩЕМУ ПОЛЬЗОВАТЕЛЮ    ----------  

    $user = get_user_by('email', $email);
    if ($user) {

    mlgb_log_safe('EXISTING USER FLOW START', [
        'user_id' => $user->ID,
        'term_id' => $term_id,
        'result'  => 'start',
    ]);

    $result = mlgb_grant_existing_user(
        $email,
        $term_id,
        $duration,
        $units,
        'bulk_operations_add'
    );
    return $result;
    } 

    else {
        $result = mlgb_create_user_and_grant(
        $email,
        $term_id,
        $duration,
        $units,
        'bulk_operations_add'
        );

        switch ($result['status']) {
            case 'created':
                return ['ok' => true];

            case 'duplicate':
                return ['ok' => true, 'duplicate' => true];

            default:
                return ['ok' => false, 'error' => $result];
        }
    }

}