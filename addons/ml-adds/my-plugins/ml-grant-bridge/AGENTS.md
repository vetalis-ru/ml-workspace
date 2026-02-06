# AGENTS.md: ML-GRANT-BRIDGE (Custom Integration Plugin)

## 1. MODULE SCOPE & ROLE
- **Type:** Custom Integration Plugin (Your Development).
- **Purpose:** Acts as a specialized "Bridge" or "Router" between external CRMs (like AmoCRM) and MemberLux.
- **Core Function:** Converts incoming webhooks into canonical MemberLux access records using ONLY public Core API functions.
- **Developer Model:** This is a "Thin Wrapper" designed for reliability, idempotency, and security.

## 2. ARCHITECTURE & DATA FLOW
The plugin follows a strict routing pattern:
`External System (POST) → MLGB REST Endpoint → MemberLux Core API`

### REST API Configuration
- **Namespace:** `mlgb/v1`
- **Endpoint:** `[POST] /grant`
- **Auth:** Mandatory verification via `X-API-Key` header or request token.
- **Source Tagging:** Every activation is tagged as `grant_bridge_{source}` (e.g., `grant_bridge_amo_crm`).

## 3. EXECUTION LOGIC (The Canonical Path)
The handler follows these exact steps to ensure system integrity:
1.  **User Management:** Uses `get_user_by('email')`. If not found, calls `wpm_register_user` (usually with `send_email => false` to avoid duplicate notifications if the CRM handles emails).
2.  **Key Generation:** Calls `wpm_insert_one_user_key($term_id, $duration, $units)`.
3.  **Key Activation:** Calls `wpm_update_user_key_dates($user_id, $generated_key, false, $source)`.
4.  **Logging:** Records the entire transaction (Payload -> Action -> Result) for audit purposes.

## 4. CRITICAL REQUIREMENTS & CONSTRAINTS
- **✅ Idempotency:** Must prevent duplicate access grants for the same `external_id`. It must check a mapping table (external_id -> key_id) before processing.
- **✅ Public API Only:** **STRICT RULE:** No direct SQL `INSERT` into `memberlux_term_keys`. Only use Core functions to ensure all downstream hooks are triggered.
- **✅ Downstream Compatibility:** By using `wpm_update_user_key_dates`, this plugin automatically triggers:
    - **Certificates:** Auto-issuance of certificates.
    - **Auto Responder:** Scheduling of email funnels.
    - **Webhooks:** Notifying other external systems.

## 5. INTEGRATION PRINCIPLES & ANTI-PATTERNS
- **✅ Principle:** Use this plugin as the primary entry point for any CRM that requires more complex logic than the standard MemberLux API module provides.
- **✅ Principle:** Always sanitize and validate the `term_id` and `duration` before passing them to Core functions.
- **❌ Anti-Pattern:** Allowing the plugin to proceed without a valid `external_id` (this breaks idempotency).
- **❌ Anti-Pattern:** Modifying MemberLux Core constants or global variables within this bridge.

## 6. SOURCE FILE REFERENCE
**Source Path:** `/Users/semenpetrovich/Documents/GitHub/ml-workspace/docs/ml-origin/HANDOFF DOCUMENTS # ENG/HANDOFF DOCUMENT #007- ML-GRANT-BRIDGE (CUSTOM PLUGIN) - eng.md`
