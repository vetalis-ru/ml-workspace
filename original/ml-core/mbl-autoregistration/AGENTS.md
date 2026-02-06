# AGENTS.md: MBL Auto Registration (Official Plugin Context)
**Source File Reference:** `/Users/semenpetrovich/Documents/GitHub/ml-workspace/docs/ml-origin/HANDOFF DOCUMENTS # ENG/HANDOFF DOCUMENT #003- MBL AUTO REGISTRATION (OFFICIAL PLUGIN) - eng.md`


## 1. MODULE SCOPE & ROLE
- **Type:** Official MemberLux Module (Read-Only).
- **Purpose:** Automating user self-registration and instant access granting via public "one-time" hashed links. 
- **Core Function:** Bridges external traffic (URLs) with Core internal methods (`wpm_register_user`).
- **Dependencies:** 
    - MemberLux Core version >= 2.5.9.
    - License keys `fullpackaccess` or `autoreg` required.

## 2. WORKFLOW & ARCHITECTURE (The Bridge)
The module operates as a handler for specific URL patterns, converting a `hash` and `email` into a valid WordPress user session with active access.

### Public Entry Point
URL Pattern: `https://site.com/{wpma|join}/{hash}/{email}/`
- Handled via WP Rewrite rules or direct parsing of `mblr_hash` and `mblr_email` GET parameters.

### Execution Algorithm (Internal)
1. **Validation:** Extracts `$hash` and `$email`, then verifies settings in `wpm_main_options`.
2. **Key Generation:** Calls `wpm_insert_one_user_key($term_id, $duration, $units)` to create a `NEW` key.
3. **Execution:** Triggers `wpm_register_user()` passing the `code` and setting the **Source** to `auto_registration`.
4. **Result:** User is created/found, key is marked `USED`, and access dates are calculated.

## 3. DATA & STORAGE
- **Settings Storage:** All link configurations (hashes, terms, durations) are stored in the `wp_options` table under the serialized key: `wpm_main_options.mblr_auto_registration.{hash}`.
- **State:** The module tracks if a link has been "used" based on the email/hash combination to prevent multi-use if limits are set.

## 4. INTEGRATION POINTS (HOOKS)
This module acts primarily as a **Consumer** of Core API. It does not emit unique hooks, but its execution is the primary cause for the following events:

| Triggered Hook | Role in this Module |
| :--- | :--- |
| `wpm_update_user_key_dates` | Fired when the auto-reg link is clicked and processed. |
| `wpm_user_mail_shortcode_replacement` | Used during the registration email dispatch. |

> [!TIP]
> **Developer Note:** When building extensions (like `ml-learning-monitor`), check the `$source` parameter in `wpm_update_user_key_dates`. If it equals `auto_registration`, you know the access was granted via this module.

## 5. INTEGRATION PRINCIPLES & ANTI-PATTERNS
- **✅ Principle:** Monitor Core hooks. Since this module uses standard Core functions, any logic attached to `wpm_register_user` or key activation will work automatically.
- **✅ Principle:** Use the AJAX endpoint `wp_ajax_mblr_get_auto_reg_link` if you need to programmatically generate registration links for users.
- **❌ Anti-Pattern:** Trying to bypass `wpm_register_user` and manually inserting rows into `memberlux_term_keys` during auto-registration.
- **❌ Anti-Pattern:** Hardcoding URL structures instead of using the module's hash-generation logic.

## 6. FILE STRUCTURE (Must-Know)
- `class-mbl-ar-public.php`: The "Front-end" controller. Logic for URL parsing.
- `class-mbl-ar-core.php`: The "Engine". License checks and internal logic.
- `class-mbl-ar-ajax.php`: Handler for link generation in the Admin UI.
