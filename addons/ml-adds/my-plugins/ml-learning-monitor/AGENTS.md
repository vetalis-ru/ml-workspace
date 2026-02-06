ML Learning Monitor (MemberLux Ecosystem)
1. PROJECT OVERVIEW
Name

ML Learning Monitor (ЛМ)

Domain

WordPress plugin for the MemberLux ecosystem.

Core Purpose

ML Learning Monitor is a monitoring and analytics plugin that operates on the level of MemberLux access terms (wpm-levels) and is designed to identify and manage so-called “sleepers” — users whose access has expired and who are no longer active under a given access level.

The plugin currently focuses on:

Detection of sleepers

Configuration of reminder email templates

Administrative visibility via AJAX-based tables

Project maturity note:
ML Learning Monitor is at a very early stage of development.
All described functionality, architectural decisions, development plans, and priorities should be considered preliminary and subject to change as the project evolves and new requirements emerge.

Future purpose (explicitly planned in multiple documents):

Automated reminder emails

Cron-based processing

Idempotent notification chains

Audit and statistics

2. CURRENT STATUS & MATURITY
Implemented (Baseline State)

Term-level configuration UI

Storage of monitoring and email settings per access level

AJAX-based sleepers table

SQL logic to identify expired users

Security layer (nonces, caps, sanitization, escaping)

Not Implemented (Explicitly Stated)

Actual email sending

Cron jobs

Email delivery logs

Certificate-aware filtering

Integration with mbl-certificates

Statistics dashboard

This plugin is explicitly described as functional but incomplete, with UI and data-layer groundwork in place.

3. ARCHITECTURAL POSITION
Ecosystem Context

Runs on top of MemberLux

Does not modify MemberLux core

Reads from MemberLux canonical table memberlux_term_keys

Stores its own configuration in wp_termmeta

Level of Operation

Per-term (per access level) — not per user globally

Integrated into the term edit screen of taxonomy wpm-levels

4. FILE & DIRECTORY STRUCTURE (CANONICAL)
Class-based Architecture
ml-learning-monitor/
├── ml-learning-monitor.php             # Plugin bootstrap
├── includes/
│   ├── class-mlm-core.php              # Initialization, hooks
│   ├── class-mlm-assets.php            # Enqueue scripts/styles
│   ├── class-mlm-term-fields.php       # Term-level UI fields
│   ├── class-mlm-email-fields.php      # Email template UI
│   ├── class-mlm-ajax-handler.php      # AJAX endpoints
│   ├── class-mlm-database.php          # SQL logic
│   └── class-mlm-renderer.php          # HTML rendering
├── assets/
│   ├── mlm-admin.css
│   └── mlm-admin.js
└── docs/


Each class has a single responsibility, reflecting a clean separation between UI, storage, queries, and rendering.

5. KEY COMPONENTS (LOGICAL MAP)
Component	Responsibility
ML_Learning_Monitor	Main orchestrator, hooks registration
MLM_Core	Plugin initialization
MLM_Term_Fields	Rendering fields on term edit screen
MLM_Email_Fields	Email template configuration
MLM_Ajax_Handler	AJAX requests processing
MLM_Database	SQL queries for sleepers
MLM_Renderer	HTML generation
MLM_Assets	CSS/JS enqueue
6. DATA MODEL & STORAGE
6.1 MemberLux Table (Read-only)

Table: {wp}memberlux_term_keys

Fields used across documents:

id

term_id

user_id

date_registered

date_end

is_unlimited

is_banned

Notes:

Field status is not used in the described SQL snapshot

Expiration is determined purely by dates

6.2 WordPress Tables

wp_termmeta — all ML Learning Monitor settings

wp_users, wp_usermeta — user identity data (email, first_name, last_name)

6.3 Term Meta Configuration (Per wpm-levels Term)
mlm_enabled = 0|1

# Student emails (1–3)
mlm_email_1_days
mlm_email_1_subject
mlm_email_1_body

mlm_email_2_days
mlm_email_2_subject
mlm_email_2_body

mlm_email_3_days
mlm_email_3_subject
mlm_email_3_body

# Admin email
mlm_admin_days_after_last
mlm_admin_email
mlm_admin_subject
mlm_admin_body


These keys are stored but not executed in the current version.

7. ADMIN UI — TERM EDIT SCREEN
Location

WordPress Admin → Edit term of taxonomy wpm-levels

Rendered UI Blocks

Enable Monitoring Checkbox

Email Configuration Tabs

Student Email #1

Student Email #2

Student Email #3

Admin Email

Sleepers Section

Date range filters

“Show sleepers” button

AJAX-loaded table

UI Technology

jQuery

jQuery UI Tabs

Custom JS/CSS

8. EMAIL TEMPLATES & TOKENS
Tokens Shown in UI (Display Only)
[user_email]
[user_login]
[course_name]
[expired_date]
[is_bundle_course]
[bundle_name]


Important:

Tokens are not replaced

No templating engine exists in this plugin

Token processing is explicitly described as external or future work

9. SLEEPERS DEFINITION
Conceptual Definition

A “sleeper” is a user who:

Had access to a specific term (term_id)

Whose latest access has expired

Has no active or unlimited key

Is not banned

Algorithm (As Implemented)

Select latest access key per user (MAX(date_end))

Filter by:

term_id

date_end within [date_from, date_to]

Exclude users who:

Have another key where is_unlimited = 1

Have another key where date_end >= today / date_to

Exclude banned keys:

is_banned = 0

Pagination:

20 users per page

10. AJAX INTERFACE
Endpoint

wp_ajax_mlm_get_sleepers

Parameters

term_id (required)

page

date_from

date_to

Response

HTML table

Pagination controls

Total count

Current page

11. ASSETS BEHAVIOR
JavaScript (mlm-admin.js)

Initializes jQuery UI Tabs

Toggles monitoring block visibility

Sends AJAX requests

Handles pagination

Injects returned HTML

CSS (mlm-admin.css)

Minimal admin styling

Tabs layout

Table spacing

Input widths

12. SECURITY MODEL
Capability Checks
current_user_can('manage_options')

Nonces

mlm_save_nonce

mlm_ajax_nonce

Sanitization

sanitize_text_field

sanitize_email

wp_kses_post

Escaping

esc_html

esc_attr

esc_textarea

13. DEVELOPMENT PLAN (PRELIMINARY)

Important:
Because ML Learning Monitor is at an early stage, the plan below is not fixed and may be significantly revised as new requirements, integrations, or constraints arise during development.

Phase 1 — Baseline Audit

Freeze current UX

Audit SQL & security

Confirm sleepers logic

Phase 2 — Canonical Sleeper Logic

Add certificate checks

Add renewal checks

Introduce hooks without touching core

Phase 3 — Cron Logic

Notification history

Dry-run mode

Idempotency

Stop chain on renewal/certificate

Phase 4 — Email Rendering

Token replacement engine

Secure mail helper

Phase 5 — Technical Hardening

Clear class separation

Optional WP_List_Table

Admin notices

14. PRIORITY TASKS (SUBJECT TO CHANGE)
High Priority

Cron email processing

Email logs table

Hooks:

mlm_before_email_send

mlm_after_email_send

Medium Priority

Statistics admin page

Test email sending

SQL optimization (indexes on term_id, date_end)

Low Priority

WYSIWYG email editor

CSV export

Integration with mbl-certificates

15. SOURCE FILE REFERENCES

All information in this AGENTS.md was aggregated from the following source documents:

docs/ML Learning Monitor Handoff.md

docs/ML Learning Monitor Plan (from Codex).md

docs/ML-LEARNING-MONITOR-HANDOFF-1.md

docs/ml-learning-monitor-Project file structure.md

docs/ML-LEARNING-MONITOR-v1.01-report.md

Source path:

/Users/semenpetrovich/Documents/GitHub/ml-workspace/addons/ml-adds/my-plugins/ml-learning-monitor/docs/
