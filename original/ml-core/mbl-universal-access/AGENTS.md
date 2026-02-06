# AGENTS.md: MBL Universal Access (Official Plugin Context)

## 1. MODULE SCOPE & ROLE
- **Type:** Official MemberLux Module (Read-Only).
- **Purpose:** Providing universal "PIN-code" access. It allows users to gain a specific access level during registration by entering a predefined code.
- **Integration:** Deeply integrates with both native MemberLux registration and WooCommerce checkout/registration flows.
- **Dependencies:** 
    - MemberLux Core version >= 2.9.9.
    - License keys `fullpackaccess` or `uaccess` required.

## 2. OPERATING PRINCIPLE (The Interceptor)
The module acts as a "middleware" during the WordPress user creation process. It intercepts registration data, validates the PIN, and attaches access immediately upon user registration.

### Logic Flow (Internal Handler):
1. **Validation:** Checks if the submitted PIN matches the code configured in `wpm_main_options`.
2. **Key Creation:** If valid, it calls `wpm_insert_one_user_key()` for the linked `term_id`.
3. **Instant Activation:** Calls `wpm_update_user_key_dates()` with `$source = 'universal_access'`.
4. **Outcome:** The user is created and granted access in a single atomic flow from their perspective.

## 3. INTEGRATION POINTS (HOOKS & FILTERS)
The module is a heavy consumer of WordPress and MemberLux filters to "inject" its logic into existing forms.

### Primary Filters & Actions:
| Hook | Role |
| :--- | :--- |
| `wpm_ajax_register_user_form_filter` | Injects the PIN-based access code into the standard MemberLux registration AJAX. |
| `woocommerce_process_registration_errors` | Validates the PIN field during WooCommerce registration/checkout. |
| `user_register` | The main trigger where access is attached after a successful WC registration. |
| `wc_get_template` | Used to override WooCommerce templates to display the PIN input field. |

## 4. SYSTEM OPTIONS & SHORTCODES
- **Storage:** Settings are stored within `wpm_main_options` (PIN code, Redirect URLs, Custom error messages).
- **UI:** Provides shortcodes to manually insert PIN input fields into custom forms or page builders.

## 5. INTEGRATION PRINCIPLES & ANTI-PATTERNS
- **✅ Principle:** Monitor the `wpm_update_user_key_dates` hook. When reacting to this event, check if `$source === 'universal_access'` to identify PIN-based entries.
- **✅ Principle:** If creating custom registration forms, ensure the PIN field data is passed so the module's filters can intercept it.
- **❌ Anti-Pattern:** Manually calculating access expiration dates; always let the module call Core functions to maintain consistency.
- **❌ Anti-Pattern:** Hardcoding PIN validation logic in custom plugins; use the module’s settings via `get_option('wpm_main_options')`.

## 6. SOURCE FILE REFERENCE
**Source Path:** `/Users/semenpetrovich/Documents/GitHub/ml-workspace/docs/ml-origin/HANDOFF DOCUMENTS # ENG/HANDOFF DOCUMENT #004- MBL UNIVERSAL ACCESS (OFFICIAL PLUGIN) - eng.md`
