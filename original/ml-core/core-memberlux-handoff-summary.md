# Core MemberLux — Handoff Summary

## Scope and Boundaries
MemberLux core is treated as **read-only**. Extensions must rely strictly on **public functions, hooks, filters, REST endpoints, and database contracts**. Core files must never be modified.

This summary reflects **verified behavior and contracts**, sufficient for building custom plugins and integrations.

---

## Core Access Model (Term Keys)

### Primary Table
`{wp_prefix}memberlux_term_keys`

Key fields:
- `term_id`
- `user_id`
- `key`
- `status`
- `date_start`
- `date_end`
- `date_registered`
- `duration`
- `units`
- `is_unlimited`
- `is_banned`

This table is the **single source of truth** for access control.

---

## Key Lifecycle

### Key Generation
```php
wpm_insert_one_user_key($term_id, $duration, $units, $is_unlimited = false)
```
- Creates a key with status `NEW`
- Does **not** activate access
- Does **not** set `date_start` / `date_end`
- Returns the generated key code

### Key Activation
```php
wpm_update_user_key_dates($user_id, $code, $isBanned = false, $source = '')
```
- Assigns key to user
- Sets `date_start`, `date_end`, `date_registered`
- Sets status to `USED`
- Triggers:
```php
do_action('wpm_update_user_key_dates', $user_id, $term_id, $code, $key, $source)
```

This action is the **canonical downstream trigger** for integrations.

---

## User Registration

```php
wpm_register_user($user_data, $send_email = true)
```
- Creates WordPress user
- If `$user_data['code']` is provided:
  - Activates the key internally
- Sends registration emails to user and admin when `$send_email = true`

---

## Email System

### Key Issuance Email
```php
wpm_send_email_about_new_key($user, $code, $term_id)
```

### Generic Email Sender
```php
wpm_send_user_email($user_id, $key, $email, $params, $files = [])
```

### Shortcode Processing Filter
```php
apply_filters('wpm_user_mail_shortcode_replacement', $message, $user_id, $params)
```
Used globally by MemberLux emails (keys, registration, certificates).

---

## Certificates Module (Official)

### Event
```php
do_action('mbl_certificate_issued', $user_id, $cert_id)
```
Triggered immediately after certificate creation.

Used as:
- Canonical trigger for program progression
- Entry point for chained access logic (e.g. bundle courses)

Certificates are stored in:
`{wp_prefix}memberlux_certificate`

---

## REST API Usage

MemberLux core does not expose extensive REST APIs by default.

Custom integrations (e.g. `ml-grant-bridge`) register endpoints via:
```php
add_action('rest_api_init', ...)
```

REST usage is considered **extension responsibility**, not core.

---

## Confirmed WordPress Dependencies

- User management: `get_user_by`, `wp_insert_user`, `email_exists`
- Database: `$wpdb`, `prepare`, `get_var`, `get_results`
- Time: `current_time()`
- Hooks: `add_action`, `do_action`, `add_filter`, `apply_filters`
- Options/meta: `get_option`, `update_option`, `get_user_meta`, `get_term_meta`

---

## Recommended Extension Patterns

- Treat `term_id` (`wpm-levels`) as the access domain
- Issue access **only** via:
  - `wpm_insert_one_user_key`
  - `wpm_update_user_key_dates`
- React to:
  - `wpm_update_user_key_dates` (access granted)
  - `mbl_certificate_issued` (program completion)
- Ensure idempotency at plugin level (core does not guard against duplicate external triggers)
- Never write directly to `memberlux_term_keys`

---

## What Core Does NOT Provide

- No built-in scheduler for reminders
- No bundled automation for renewals
- No state machine for multi-step programs
- No protection from repeated external triggers

All higher-level logic must live in **custom plugins**.

---

## Mental Model for Developers

MemberLux core = **low-level access engine**

It provides:
- Keys
- Dates
- Status
- Emails
- Events

Everything else (flows, logic, monitoring, automation) is intentionally external.


---

## Core MemberLux — Key Files Reference

Scope: **read-only**. Files listed below contain canonical logic and public contracts used by extensions.  
Goal: fast navigation for future code analysis.

### memberlux.php
Plugin bootstrap. Defines constants, loads core modules, controls execution order.

### core/term-keys.php (or class-mbl-term-keys*.php)
Canonical access model. Implements term key lifecycle.
Key APIs: `wpm_insert_one_user_key`, `wpm_update_user_key_dates`.

### core/term-keys-query.php (MBLTermKeysQuery)
Low-level SQL access to `memberlux_term_keys`. Reference for correct queries.

### core/register-user.php
User registration flow. Activates key only if `USER_DATA['CODE']` is present.

### core/send-user-email.php
All user/admin email dispatch.
Key APIs: `wpm_send_email_about_new_key`, `wpm_send_user_email`.
Defines `wpm_user_mail_shortcode_replacement` filter.

### core/mailer/*
Low-level mail transport, templates, attachments.

### core/hooks.php
Central place for MemberLux actions and filters.
Critical hooks: `wpm_update_user_key_dates`, `wpm_user_mail_shortcode_replacement`.

### core/options.php
Global MemberLux settings and feature flags.

### core/db-schema.php
Database schema and versioning. Ground truth for tables.

### core/utils/*
Shared helpers (dates, formatting, validation). Use instead of reimplementation.

