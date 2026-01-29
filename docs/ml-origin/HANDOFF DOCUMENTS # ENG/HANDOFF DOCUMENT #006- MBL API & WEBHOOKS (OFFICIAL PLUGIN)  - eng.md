Type: Official MemberLux Module
Dependencies: MemberLux Core version >= 2.73, license key fullpackaccess or api.
Purpose: Providing a REST API for access management and a webhook system for sending MemberLux events to external systems (AmoCRM, Telegram, etc.).

1. REST API ENDPOINTS
The module registers its own REST namespace (/mbl/v1/).

Endpoint: [POST] /wp-json/mbl/v1/order/

Purpose: Canonical endpoint for integration with external CRMs/payment systems for granting access.

Authentication: Via token (passed in the Authorization header).

Request Body (JSON):

json
{
  "customer": {
    "email": "user@example.com",
    "first_name": "John",
    "last_name": "Doe"
  },
  "product": {
    "term_id": 123,
    "duration": 1,
    "units": "month"
  },
  "meta": {
    "order_id": "external_order_456",
    "source": "crm_system"
  }
}
Handler Logic (pseudocode):

php
1.  Validation and authentication.
2.  Find user by email or create a new one (via wpm_register_user).
3.  Generate a key: wpm_insert_one_user_key($term_id, $duration, $units).
4.  Activate the key for the user: wpm_update_user_key_dates($user_id, $code, false, 'api_order').
5.  Return JSON response with user and key data.
Important: This endpoint is the official integration point for automatic access granting, similar to the internal workflow.

2. WEBHOOK SYSTEM
The module adds a mechanism to subscribe to MemberLux events and send them to external systems.

2.1. Database Tables for Webhook Management:

sql
-- LIST OF REGISTERED WEBHOOKS
CREATE TABLE `wp_memberlux_hook` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `url` text NOT NULL COMMENT 'External system endpoint',
  `event` varchar(100) NOT NULL COMMENT 'MemberLux event (e.g., wpm_update_user_key_dates)',
  `is_active` tinyint(1) DEFAULT 1,
  `secret` varchar(255) DEFAULT NULL COMMENT 'Secret for payload signing',
  PRIMARY KEY (`id`),
  KEY `event` (`event`)
);

-- SENDING LOG (HISTORY)
CREATE TABLE `wp_memberlux_hook_action` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hook_id` int(11) NOT NULL,
  `payload` longtext COMMENT 'JSON data sent to webhook',
  `response_code` int(11) DEFAULT NULL,
  `response_body` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `hook_id` (`hook_id`),
  KEY `created_at` (`created_at`)
);
2.2. Operation Mechanism:

Event Registration: Administrator in MemberLux settings specifies the external system's URL and selects an event (e.g., wpm_update_user_key_dates).

Trigger: When the selected event fires in MemberLux code, the API module intercepts it:

php
// SUBSCRIBING TO CORE EVENT
add_action('wpm_update_user_key_dates', 'mbl_api_dispatch_webhook', 20, 5);

function mbl_api_dispatch_webhook($user_id, $term_id, $code, $key, $source) {
    // 1. Finds all active hooks in wp_memberlux_hook for the 'wpm_update_user_key_dates' event.
    // 2. For each hook, forms a payload (JSON) with data: {user_id, term_id, code, source, timestamp}.
    // 3. Sends an asynchronous POST request (wp_remote_post) to the specified URL.
    // 4. Adds a record to the log (wp_memberlux_hook_action) with payload and response.
}
Admin: Webhook management via a tab in MemberLux settings.

3. INTEGRATION PRINCIPLES
Extensibility: The module is a bridge between internal MemberLux events and the external world. It does not modify the core, only reacts to its public hooks.

Fault Tolerance: Webhook dispatch should be asynchronous and resilient to network errors (retries, logging).

Security: Payload can be signed with a secret key (secret) for integrity verification on the receiver's side.