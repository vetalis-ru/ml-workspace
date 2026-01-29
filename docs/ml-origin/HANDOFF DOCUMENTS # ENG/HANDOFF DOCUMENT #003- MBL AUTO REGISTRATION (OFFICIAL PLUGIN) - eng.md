Type: Official MemberLux Module
Dependencies: MemberLux Core version >= 2.5.9, license key fullpackaccess or autoreg.
Purpose: Providing public one-time links for self-registration and automatic activation of access to a specified access level (term_id).

1. EXPECTED FILE ARCHITECTURE
text
mbl-auto-registration/
├── mbl-auto-registration.php         # Bootstrap
├── includes/
│   ├── class-mbl-ar-core.php         # Main logic, license check
│   ├── class-mbl-ar-public.php       # Public URL handling (entry point)
│   ├── class-mbl-ar-ajax.php         # Admin AJAX (link generation)
│   └── class-mbl-ar-admin.php        # UI in MemberLux settings
└── assets/
    └── (JS/CSS for admin)
2. WORKFLOW AND PUBLIC CONTRACT
1. Public Entry Point (URL):

text
https://site.com/{wpma|join}/{hash}/{email}/
Handled via WordPress rewrite rules or direct parsing of $_GET['mblr_hash'], $_GET['mblr_email'].

Hash — unique identifier for a settings record storing term_id, duration, units.

2. Main Flow (URL handler):

php
// PSEUDOCODE OF THE MAIN ALGORITHM
1.  Extract `$hash` and `$email` from the URL.
2.  Load settings from `$wpdb->options` (`wpm_main_options.mblr_auto_registration.{hash}`).
3.  Check if the link has already been used (status, limits).
4.  Generate an access key: `wpm_insert_one_user_key($term_id, $duration, $units);`
5.  Find the key ID: `wpm_search_key_id($generated_code);`
6.  Register the user (or find an existing one by email) and activate the key:
    wpm_register_user([
        'user_email' => $email,
        'code'       => $generated_code, // Key is activated inside
        'source'     => 'auto_registration'
    ], $send_email = true);
3. Admin and AJAX:

Settings Section: A tab in the MemberLux admin (/wp-admin/admin.php?page=memberlux&tab=...).

AJAX endpoint: wp_ajax_mblr_get_auto_reg_link — generates a new hashed link with given parameters (term_id, duration, units, expires).

3. WORDPRESS HOOKS (INTEGRATION POINTS)
The module is a consumer of the core MemberLux API. It does not provide its own public hooks for extension, but its work triggers standard Core events:

Upon successful registration, wpm_register_user() fires.

Upon key activation, the main hook do_action('wpm_update_user_key_dates', ...) fires.

Standard MemberLux emails are sent.