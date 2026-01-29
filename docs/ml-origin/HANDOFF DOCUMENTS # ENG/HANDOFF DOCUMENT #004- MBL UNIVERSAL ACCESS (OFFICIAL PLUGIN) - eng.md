Type: Official MemberLux Module
Dependencies: MemberLux Core version >= 2.9.9, license key fullpackaccess or uaccess.
Purpose: Providing universal ("pin-code") access to the system, integrated with registration forms (including WooCommerce), to automatically grant a specified access level to new users.

1. MAIN SYSTEM OPTIONS (wpm_main_options)
The module adds global settings to the MemberLux system:

PIN Code: A unique code entered by the user in a form field.

Redirects: URLs for redirection after successful/unsuccessful access linking.

Texts: Error messages, form hints.

Shortcodes: Shortcodes for inserting the PIN code input field into forms.

2. WORDPRESS HOOKS (INTEGRATION POINTS)
The module actively integrates into registration processes by intercepting and modifying data via filters.

Filters used by the module for integration:

php
// 1. FILTERING MEMBERLUX REGISTRATION FORM DATA
// Allows the module to "inject" the access code from the PIN field into the registration process.
add_filter('wpm_ajax_register_user_form_filter', 'mbl_uaccess_filter_registration_data', 10, 2);

// 2. INTERCEPTING WOOCOMMERCE REGISTRATION
// Integrates with WooCommerce forms to add PIN code validation logic.
add_filter('woocommerce_process_registration_errors', 'mbl_uaccess_wc_registration_check', 10, 1);
add_action('user_register', 'mbl_uaccess_attach_access_after_wc_registration', 10, 1);

// 3. OVERRIDING WOOCOMMERCE TEMPLATES
// To display the custom PIN code field on WooCommerce pages.
add_filter('wc_get_template', 'mbl_uaccess_override_wc_template', 10, 5);
Access generation (internal handler logic):

php
// PSEUDOCODE LOGIC (e.g., in the 'user_register' handler)
1.  Check if a valid PIN code was passed in the request.
2.  Find the target `term_id` and access parameters (`duration`, `units`) by the PIN.
3.  Generate a key: `wpm_insert_one_user_key($term_id, $duration, $units);`
4.  Immediately activate it for the new user:
    wpm_update_user_key_dates($user_id, $generated_code, false, 'universal_access');
    // This triggers the standard 'wpm_update_user_key_dates' hook.
3. OPERATING PRINCIPLE
Administrator configures a PIN code and links it to a specific access level (term_id) via the MemberLux admin.

A field for PIN code input is added to the site (in MemberLux or WooCommerce registration forms) via shortcode or template modification.

The user enters the PIN during registration.

The module intercepts the process, validates the PIN, and if correct, after user creation automatically generates and activates the corresponding access key for them.

All standard MemberLux hooks (wpm_update_user_key_dates) fire, allowing other modules (Certificates, Bridge) to react.

Important: The module does not create its own hooks for extension but is a prime example of a consumer and modifier of the standard MemberLux data flow via WordPress filters.