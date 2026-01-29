Type: Custom Integration Plugin
Dependencies: MemberLux Core, memberlux_term_keys table.
Purpose: Serves as a bridge between external systems (AmoCRM, other CRMs) and MemberLux. Accepts incoming webhooks from external systems and performs canonical access granting using only Core's public functions.

1. ARCHITECTURE AND OPERATING PRINCIPLE
The plugin is a thin wrapper-router. Its sole task is to transform an incoming request from an external system into calls to wpm_insert_one_user_key() and wpm_update_user_key_dates().

text
External System (POST) → REST Endpoint (/mlgb/v1/grant) → ML-Grant-Bridge → MemberLux Core API
2. REST API ENDPOINT
Endpoint: [POST] /wp-json/mlgb/v1/grant

Authentication: Verification via a secret token passed in a header (X-API-Key) or request body.

Request Body (example):

json
{
  "user": {
    "email": "customer@domain.com",
    "first_name": "Ivan",
    "last_name": "Petrov"
  },
  "grant": {
    "term_id": 15,
    "duration": 6,
    "units": "month"
  },
  "external_id": "lead_789",
  "source": "amo_crm"
}
Handler Logic (strictly by Core contract):

php
// 1. VALIDATION AND AUTHENTICATION OF INPUT DATA
// 2. USER SEARCH/CREATION
$user = get_user_by('email', $email);
if (!$user) {
    $user_id = wpm_register_user([
        'user_email' => $email,
        'first_name' => $first_name,
        'last_name'  => $last_name,
        'send_email' => false // Often disabled to let the CRM send its own email
    ]);
} else {
    $user_id = $user->ID;
}

// 3. KEY GENERATION AND ACTIVATION (CANONICAL PATH)
$generated_key = wpm_insert_one_user_key($term_id, $duration, $units);
if ($generated_key) {
    $activation_result = wpm_update_user_key_dates(
        $user_id,
        $generated_key,
        false,
        'grant_bridge_' . $source
    );
}

// 4. LOGGING AND RESPONSE
// Returns JSON with user_id, term_id, key, date_end.
3. CRITICAL IMPLEMENTATION REQUIREMENTS
Idempotency: The endpoint must be idempotent. An identical repeated request (determined by external_id or parameter hash) should not create duplicate keys. Implemented via a custom log table or wp_options storing the external_id -> key_id mapping.

Public API Only: Direct SQL inserts into memberlux_term_keys are forbidden. Use only wpm_insert_one_user_key and wpm_update_user_key_dates.

Security: Mandatory capability or custom token checks. Sanitization of all input data.

Logging: Mandatory detailed logging of all steps (incoming request, user lookup result, key grant success) to a separate table or file for audit.

4. INTEGRATION CONSEQUENCES
Since the plugin uses wpm_update_user_key_dates, its operation triggers the entire MemberLux ecosystem:

The hook do_action('wpm_update_user_key_dates', ...) fires.

The Auto Responder module may subscribe the user to a funnel.

The Certificates module may issue an AUTO-certificate (if automatic issuance is configured for the term_id).

The API & Webhooks module may send an event about access grant to external systems.

The plugin is a reference example of correct external integration with MemberLux without core modification.