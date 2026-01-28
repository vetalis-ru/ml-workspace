# ML Learning Monitor — Handoff Summary

## Overview
ML Learning Monitor is a WordPress plugin that adds term-level monitoring for MemberLux terms (`wpm-levels`). It provides per-term reminder email settings (3 student reminders + 1 admin reminder) and an admin UI to query “sleepers” (users whose latest term key has expired and who do not have an active or unlimited key).​:codex-file-citation[codex-file-citation]{line_range_start=1 line_range_end=449 path=addons/ml-adds/my-plugins/ml-learning-monitor/ml-learning-monitor.php git_url="https://github.com/vetalis-ru/ml-workspace/blob/main/addons/ml-adds/my-plugins/ml-learning-monitor/ml-learning-monitor.php#L1-L449"}​

## Scope & Entry Points
- **Plugin bootstrap**: `addons/ml-adds/my-plugins/ml-learning-monitor/ml-learning-monitor.php`.
- **Admin assets**:
  - JS: `addons/ml-adds/my-plugins/ml-learning-monitor/assets/mlm-admin.js`.
  - CSS: `addons/ml-adds/my-plugins/ml-learning-monitor/assets/css/mlm-admin.css`.

## Admin UI (Term Edit Screen)
- Hooked into `wpm-levels` term edit form to render a monitor panel with:
  - Enable checkbox for per-term monitoring.
  - jQuery UI tabs for **3 student emails** + **1 admin email**.
  - “Sleepers” section with date filters and a button to request a paginated sleepers table via AJAX.​:codex-file-citation[codex-file-citation]{line_range_start=23 line_range_end=185 path=addons/ml-adds/my-plugins/ml-learning-monitor/ml-learning-monitor.php git_url="https://github.com/vetalis-ru/ml-workspace/blob/main/addons/ml-adds/my-plugins/ml-learning-monitor/ml-learning-monitor.php#L23-L185"}​
- Uses `jquery-ui-tabs`; the script initializes tabs and toggles UI visibility based on the enable checkbox.​:codex-file-citation[codex-file-citation]{line_range_start=1 line_range_end=83 path=addons/ml-adds/my-plugins/ml-learning-monitor/assets/mlm-admin.js git_url="https://github.com/vetalis-ru/ml-workspace/blob/main/addons/ml-adds/my-plugins/ml-learning-monitor/assets/mlm-admin.js#L1-L83"}​

## Email Templates & Tokens
- **Student emails** (1–3): fields for “days after expiration,” subject, and body.
- **Admin email**: fields for “days after last student email,” admin email address, subject, and body.
- Shortcode tokens displayed in UI: `[user_email]`, `[user_login]`, `[course_name]`, `[expired_date]`, `[is_bundle_course]`, `[bundle_name]` (display only; replacement logic is external).​:codex-file-citation[codex-file-citation]{line_range_start=119 line_range_end=256 path=addons/ml-adds/my-plugins/ml-learning-monitor/ml-learning-monitor.php git_url="https://github.com/vetalis-ru/ml-workspace/blob/main/addons/ml-adds/my-plugins/ml-learning-monitor/ml-learning-monitor.php#L119-L256"}​

## Saving Behavior
- Term meta is saved on `edited_wpm-levels` with nonce and capability checks.
- Meta keys include:
  - `mlm_enabled`
  - `mlm_email_{1..3}_days`, `mlm_email_{1..3}_subject`, `mlm_email_{1..3}_body`
  - `mlm_admin_days_after_last`, `mlm_admin_email`, `mlm_admin_subject`, `mlm_admin_body`​:codex-file-citation[codex-file-citation]{line_range_start=258 line_range_end=309 path=addons/ml-adds/my-plugins/ml-learning-monitor/ml-learning-monitor.php git_url="https://github.com/vetalis-ru/ml-workspace/blob/main/addons/ml-adds/my-plugins/ml-learning-monitor/ml-learning-monitor.php#L258-L309"}​

## Sleepers Query (AJAX)
- AJAX endpoint: `wp_ajax_mlm_get_sleepers`.
- Input: `term_id`, `page`, `date_from`, `date_to`.
- Uses SQL against `{$wpdb->prefix}memberlux_term_keys` to find users whose latest key for the term expired within the date range and who do **not** have an active/unlimited key. Results are paginated (20/page).​:codex-file-citation[codex-file-citation]{line_range_start=311 line_range_end=449 path=addons/ml-adds/my-plugins/ml-learning-monitor/ml-learning-monitor.php git_url="https://github.com/vetalis-ru/ml-workspace/blob/main/addons/ml-adds/my-plugins/ml-learning-monitor/ml-learning-monitor.php#L311-L449"}​
- Rendering: returns HTML table with user ID/email/name, last issue date, last end date, and reminders sent (currently fixed at `0`).​:codex-file-citation[codex-file-citation]{line_range_start=370 line_range_end=449 path=addons/ml-adds/my-plugins/ml-learning-monitor/ml-learning-monitor.php git_url="https://github.com/vetalis-ru/ml-workspace/blob/main/addons/ml-adds/my-plugins/ml-learning-monitor/ml-learning-monitor.php#L370-L449"}​

## Assets Behavior
- **JS** (`mlm-admin.js`):
  - Initializes tabs, toggles the monitor block, requests sleepers via AJAX, handles pagination, and renders returned HTML into the container.​:codex-file-citation[codex-file-citation]{line_range_start=1 line_range_end=83 path=addons/ml-adds/my-plugins/ml-learning-monitor/assets/mlm-admin.js git_url="https://github.com/vetalis-ru/ml-workspace/blob/main/addons/ml-adds/my-plugins/ml-learning-monitor/assets/mlm-admin.js#L1-L83"}​
- **CSS** (`mlm-admin.css`):
  - Minor layout styling for tabs, sleepers list, and field widths for day inputs.​:codex-file-citation[codex-file-citation]{line_range_start=1 line_range_end=64 path=addons/ml-adds/my-plugins/ml-learning-monitor/assets/css/mlm-admin.css git_url="https://github.com/vetalis-ru/ml-workspace/blob/main/addons/ml-adds/my-plugins/ml-learning-monitor/assets/css/mlm-admin.css#L1-L64"}​

## Dependencies
- WordPress admin environment with `wpm-levels` taxonomy and `memberlux_term_keys` table.
- jQuery + jQuery UI Tabs (enqueued on term edit screen).

## Notes / Known Limitations
- “Reminders sent” is currently static (`0`) in sleepers table output and is not calculated from history.​:codex-file-citation[codex-file-citation]{line_range_start=338 line_range_end=368 path=addons/ml-adds/my-plugins/ml-learning-monitor/ml-learning-monitor.php git_url="https://github.com/vetalis-ru/ml-workspace/blob/main/addons/ml-adds/my-plugins/ml-learning-monitor/ml-learning-monitor.php#L338-L368"}​
- Token replacement for email bodies is not implemented in this plugin; tokens are only displayed in the UI for reference.

## Suggested Next Steps
- Implement token replacement and actual reminder sending logic (cron or background job).
- Track reminders sent per user/term to populate the sleepers table.
- Consider moving inline styles to CSS for consistency.
