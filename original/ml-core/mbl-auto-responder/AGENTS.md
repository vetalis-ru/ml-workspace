# AGENTS.md: MBL Auto Responder (Official Plugin Context)

## 1. MODULE SCOPE & ROLE
- **Type:** Official MemberLux Module (Read-Only).
- **Purpose:** Creating automatic email sequences (funnels) triggered by access level activation. 
- **Core Mechanism:** Schedules emails relative to the `date_registered` of a Term Key and processes them via a dedicated Cron task.
- **Dependencies:** 
    - MemberLux Core version >= 2.3.4.
    - License keys `fullpackaccess` or `auto-responder` required.

## 2. DATABASE STRUCTURE (The Queue System)
The module uses a multi-table schema to manage templates and sending status.

| Table | Purpose | Key Fields |
| :--- | :--- | :--- |
| `{prefix}memberlux_mailing_templates` | Funnel definitions | `term_id` (Link to access level) |
| `{prefix}memberlux_mailing` | Individual emails in a funnel | `send_after_days`, `subject`, `body` |
| `{prefix}memberlux_mailing_results` | **The Sending Queue** (Log) | `scheduled_at`, `sent_at`, `status` (pending/sent) |
| `{prefix}memberlux_keys_meta` | Subscription tracking | `term_key_id`, `meta_key`, `meta_value` |

## 3. ACTIVATION & SCHEDULING
The module transforms a Core "Access Granted" event into a series of "Scheduled Emails".

### Subscription Flow:
1. **Trigger:** Listens to `wpm_update_user_key_dates`.
2. **Detection:** Finds all emails in `wp_memberlux_mailing` for the given `term_id`.
3. **Queueing:** For each email, it inserts a row into `wp_memberlux_mailing_results`:
   - `scheduled_at` = `key.date_registered` + `email.send_after_days`.
   - `status` = `pending`.
4. **Marking:** Saves the subscription status in `wp_memberlux_keys_meta` to prevent duplicate scheduling (Idempotency).

## 4. THE CRON ENGINE
- **Action:** `mbl_auto_responder_event` (runs on a high-frequency interval, e.g., every 60s).
- **Logic:** 
    - Queries `mailing_results` where `status='pending'` and `scheduled_at <= now`.
    - Renders content using `wpm_user_mail_shortcode_replacement` filter.
    - Sends via `MBL_Mailer` class.
    - Updates status to `sent` and sets `sent_at`.

## 5. INTEGRATION PRINCIPLES & ANTI-PATTERNS
- **✅ Principle:** To identifying users currently in a funnel, query `wp_memberlux_mailing_results` by `user_id` and `status='pending'`.
- **✅ Principle:** Use the standard Core activation methods (`wpm_update_user_key_dates`) to ensure the autoresponder triggers correctly.
- **❌ Anti-Pattern:** Manually inserting rows into `mailing_results` without a corresponding `term_key_id` in `keys_meta`.
- **❌ Anti-Pattern:** Bypassing `MBL_Mailer` for sending emails; this breaks shortcode processing and logging.

## 6. SOURCE FILE REFERENCE
**Source Path:** `/Users/semenpetrovich/Documents/GitHub/ml-workspace/docs/ml-origin/HANDOFF DOCUMENTS # ENG/HANDOFF DOCUMENT #005- MBL AUTO RESPONDER (OFFICIAL PLUGIN) - eng.md`
