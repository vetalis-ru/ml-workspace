# AGENTS.md: MBL API & Webhooks (Official Plugin Context)

## 1. MODULE SCOPE & ROLE
- **Type:** Official MemberLux Module (Read-Only).
- **Purpose:** Bridging MemberLux with external ecosystems via a REST API (Inbound) and a Webhook system (Outbound).
- **Core Function:** Automating access granting from external CRMs and notifying external systems about internal MemberLux events.
- **Dependencies:** 
    - MemberLux Core version >= 2.73.
    - License keys `fullpackaccess` or `api` required.

## 2. INBOUND: REST API (`/mbl/v1/`)
The module registers a canonical namespace for external integrations.

### Primary Endpoint: `[POST] /wp-json/mbl/v1/order/`
- **Logic:** Validates the token → Finds/Creates user via `wpm_register_user` → Generates key via `wpm_insert_one_user_key` → Activates via `wpm_update_user_key_dates`.
- **Source Tag:** Activations via this endpoint are marked with `$source = 'api_order'`.
- **Developer Note:** Use this endpoint as the standard for any 3rd-party payment or CRM integration.

## 3. OUTBOUND: WEBHOOK SYSTEM
A notification engine that transforms internal WordPress hooks into external HTTP POST requests.

### Database Tables (The Event Log)
| Table | Purpose | Key Fields |
| :--- | :--- | :--- |
| `{prefix}memberlux_hook` | Webhook subscriptions | `url`, `event` (e.g., `wpm_update_user_key_dates`), `secret` |
| `{prefix}memberlux_hook_action` | Delivery History/Log | `payload` (JSON), `response_code`, `created_at` |

### Dispatch Mechanism
1. **Trigger:** Listens to Core events (e.g., `add_action('wpm_update_user_key_dates', ...)`).
2. **Payload:** Constructs a JSON object containing `user_id`, `term_id`, `code`, and `source`.
3. **Transport:** Sends data asynchronously via `wp_remote_post()`.
4. **Logging:** Records the attempt and the external system's response in `hook_action`.

## 4. INTEGRATION PRINCIPLES & ANTI-PATTERNS
- **✅ Principle:** To notify an external service about a custom event, ensure your custom plugin triggers a standard hook that the Webhook module is configured to watch.
- **✅ Principle:** Always verify the `secret` signature on the receiving end to ensure the payload originated from MemberLux.
- **❌ Anti-Pattern:** Creating redundant HTTP dispatchers in custom plugins. Use the existing Webhook UI and table to manage outbound data.
- **❌ Anti-Pattern:** Blocking the main PHP process waiting for a webhook response; the module is designed for "fire and forget" or async-style logging.

## 5. SOURCE FILE REFERENCE
**Source Path:** `/Users/semenpetrovich/Documents/GitHub/ml-workspace/docs/ml-origin/HANDOFF DOCUMENTS # ENG/HANDOFF DOCUMENT #006- MBL API & WEBHOOKS (OFFICIAL PLUGIN)  - eng.md`
