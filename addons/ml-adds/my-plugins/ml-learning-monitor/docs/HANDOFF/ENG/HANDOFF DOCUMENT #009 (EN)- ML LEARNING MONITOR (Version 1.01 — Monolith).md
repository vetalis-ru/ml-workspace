
Type: Custom Add-on Plugin
Dependencies: WordPress, MemberLux Core (memberlux_term_keys table).
Purpose: Monitoring users with expired access ("sleepers") at the wpm-levels taxonomy term level. Provides an interface to configure reminder email templates (3 to student, 1 to admin) and an AJAX table to view "sleepers".

1. FILE ARCHITECTURE (MONOLITH)
text
ml-learning-monitor/
├── ml-learning-monitor.php       # SINGLE PHP plugin file (472 lines). Contains all code.
├── assets/
│   ├── mlm-admin.js             # Frontend logic: tabs, visibility, AJAX (83 lines)
│   └── mlm-admin.css            # Admin interface styles (64 lines)
2. CLASS AND METHODS (ML_LEARNING_MONITOR)
Main and only class: ML_Learning_Monitor

Constants:

TAXONOMY = 'wpm-levels'

NONCE_ACTION = 'mlm_ajax_nonce'

NONCE_NAME = 'mlm_nonce' (declared but not used in form nonce field)

PER_PAGE = 20

Methods (public):

__construct() – Registers hooks.

enqueue_assets($hook) – Enqueues JS/CSS only on term.php?taxonomy=wpm-levels.

render_term_fields($term) – Renders the entire plugin UI block on the term edit page.

save_term_fields($term_id, $tt_id) – Saves all settings from $_POST to term_meta.

ajax_get_sleepers() – Handler for AJAX wp_ajax_mlm_get_sleepers.

Methods (private):

render_student_email_fields($term_id, $index) – Renders fields for a student email.

render_admin_email_fields($term_id) – Renders fields for the admin email.

query_sleepers($term_id, $page, $per_page, $date_from, $date_to) – SQL query to find "sleepers".

render_sleepers_table_html($rows, $total, $page, $per_page, $term_id) – Generates HTML table with pagination.

3. WORDPRESS HOOKS
php
// HOOKS REGISTERED BY THE PLUGIN
add_action('wpm-levels_edit_form_fields', [$this, 'render_term_fields'], 20, 1);
add_action('edited_wpm-levels', [$this, 'save_term_fields'], 20, 2);
add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
add_action('wp_ajax_mlm_get_sleepers', [$this, 'ajax_get_sleepers']);
4. TERM META-DATA (wp_termmeta)
All settings are stored at the term level (term_id). Keys:

Activation: mlm_enabled (0/1)

Student Emails (1-3):

mlm_email_{1|2|3}_days (int) – Days after access expiry to send.

mlm_email_{1|2|3}_subject (string) – Email subject.

mlm_email_{1|2|3}_body (text) – Email body (supports HTML, wp_kses_post).

Admin Email:

mlm_admin_days_after_last (int) – Days after the last student email.

mlm_admin_email (string) – Recipient email.

mlm_admin_subject (string) – Subject.

mlm_admin_body (text) – Body.

Displayed Shortcodes (replacement NOT implemented in this version):
[user_email], [user_login], [course_name], [expired_date], [is_bundle_course], [bundle_name].

5. AJAX ENDPOINT
Action: wp_ajax_mlm_get_sleepers

Method: POST

Checks: manage_options, nonce (mlm_ajax_nonce).

Parameters:

term_id (required) – ID of the wpm-levels term.

page – Pagination page number.

date_from, date_to – Range for access expiry date (date_end). Defaults to current date.

Response (JSON): {success: true, data: {html: string, total: int, page: int}}

6. SQL QUERY FOR "SLEEPERS" (query_sleepers)
Algorithm (pseudocode):

For each user (user_id), find their latest by end date (date_end) access key for the given term_id.

Filter records where:

is_banned = 0

is_unlimited = 0

date_end falls within the requested [date_from, date_to] range.

Exclude users who have another active key for the same term_id (i.e., is_unlimited=1 or date_end >= date_to).

Return user_id, user_email, names, issue/expiry dates of the last key. The reminders_sent field is always 0 (sending logic not implemented).

Critical Limitation (Phase 1): The query does NOT check for issued certificates (memberlux_certificate). A user with expired access but a received certificate will still be displayed as a "sleeper".

7. SECURITY AND SANITIZATION
Capabilities: All operations require manage_options.

Nonce: For saving settings, a unique mlm_save_nonce per term is used. For AJAX – a general mlm_ajax_nonce.

Sanitization:

Text fields: sanitize_text_field()

Email: sanitize_email()

HTML email body: wp_kses_post()

Numbers: (int)

Dates: Regex /^\d{4}-\d{2}-\d{2}$/

Output Escaping: Uses esc_html(), esc_attr(), esc_textarea().

8. FRONTEND (JS/CSS)
jQuery UI Tabs: For switching between email settings.

Show/Hide Block: The entire monitor UI is hidden if the mlm_enabled checkbox is unchecked.

AJAX Table Loading: Triggered by the "Show Sleepers" button with date filtering.

Pagination: "Next/Previous" buttons in the table.

9. DEVELOPMENT STATUS (PHASE 1)
✅ Implemented:

UI integration with wpm-levels.

Saving email template settings.

SQL query and AJAX table for "sleepers" with pagination.

Basic security.

⚠️ NOT Implemented (expected in later Phases):

Sending real emails.

Cron tasks for automatic sending.

Sending logs.

Checking for certificates among "sleepers".

Shortcode replacement in emails.

Tracking the number of reminders sent (reminders_sent is always 0).