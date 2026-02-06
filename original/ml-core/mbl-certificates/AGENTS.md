# AGENTS.md: MBL Certificates (Official Plugin Context)
**Source File Reference:** `/Users/semenpetrovich/Documents/GitHub/ml-workspace/docs/ml-origin/HANDOFF DOCUMENT #002- MBL CERTIFICATES (OFFICIAL PLUGIN) - eng.md`


## 1. MODULE SCOPE & ROLE
- **Type:** Official MemberLux Module (Read-Only).
- **Purpose:** Issuing certificates to users as proof of access level completion. It creates immutable records (facts) and emits a canonical event to trigger downstream logic (e.g., course chains).
- **Dependencies:** 
    - MemberLux Core version >= 2.3.4.
    - License keys `fullpackaccess` or `certificates` required for activation.
- **Developer Model:** Treat this module as a "State Change Provider". It signals when a user has officially "passed" a level.

## 2. DATABASE STRUCTURE (Immutable Facts)
**Main Table:** `{prefix}memberlux_certificate`  
This table represents the definitive history of user achievements.

| Field | Type | Description |
| :--- | :--- | :--- |
| **id** | `int(11)` | Primary Key. |
| **user_id** | `int(11)` | ID of the user who received the certificate. |
| **wpmlevel_id** | `int(11)` | ID of the access level term (`wpm-levels` taxonomy). |
| **date_issue** | `datetime` | Date and time of issuance (Source of Truth for "When"). |
| **template_id** | `int(11)` | Link to the certificate visual template. |

> [!CAUTION]
> **CRITICAL:** The table **DOES NOT** have a `status` column. Certificates are never revoked or changed after creation. The presence of a row = the immutable fact of issuance.

## 3. CLASSES & PUBLIC API
**Class:** `Certificate` (Path: `includes/class-certificate.php`)

| Method | Purpose | Action & Returns |
| :--- | :--- | :--- |
| `Certificate::create(...)` | Creates a certificate record | Returns `int` (ID) or `false`. Triggers `mbl_certificate_issued`. |
| `Certificate::getCertificate(int $id)` | Retrieves data by ID | Returns `object` (`user_id`, `wpmlevel_id`, etc.) or `false`. |

## 4. CRITICAL HOOKS AND AUTOMATION LOGIC

### A. Event we EMIT (The Primary Trigger)
This is the only reliable signal of certificate issuance. Used by other plugins for program progression and external integrations.
```php
// Location: Certificate::create()
do_action('mbl_certificate_issued', int $user_id, int $certificate_id);
Используйте код с осторожностью.

B. Auto-Issuance Logic (Module LISTENS to Core)
The module subscribes to the main MemberLux Core hook to automate issuance:
php
add_action('wpm_update_user_key_dates', 'mblc_certificate_issuance_after_update_user_key_dates', 10, 5);
Используйте код с осторожностью.

Handler Logic (mblc_certificate_issuance_after_update_user_key_dates):
Checks the wpm-levels term meta for the field _mblc_how_to_issue.
If the value is 'auto', it immediately calls Certificate::create().
Result: This creates an "AUTO-certificate". Manual issuance via the admin UI creates a "MANUAL-certificate", but both trigger the same hook.
5. INTEGRATION PRINCIPLES & ANTI-PATTERNS
✅ Principle: Always react to the mbl_certificate_issued event. Do not poll the database table manually for changes.
✅ Idempotency: Repeated calls to Certificate::create() will create duplicate records. Consumer plugins (like your custom modules) must verify if a certificate already exists for a user_id/wpmlevel_id before requesting creation.
❌ Anti-Pattern: Searching for a status field in the DB (it doesn't exist).
❌ Anti-Pattern: Attempting to "revoke" or "update" a certificate record programmatically—it is designed to be a permanent log.
❌ Anti-Pattern: Modifying the issuance logic within the module's core files.
6. TERMINOLOGY
wpmlevel_id: The specific ID from the wpm-levels taxonomy representing the course or access level.
mblc: Internal prefix used for functions, meta-keys, and options within this module.
