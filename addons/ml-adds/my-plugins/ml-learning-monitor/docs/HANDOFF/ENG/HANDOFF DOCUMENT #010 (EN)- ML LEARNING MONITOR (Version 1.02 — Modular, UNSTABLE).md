
Type: Custom Add-on Plugin (Refactored, Raw Version)
Status: Unstable, contains known bugs and requires refinement.
Dependencies: WordPress, MemberLux Core (memberlux_term_keys table), MBL Certificates (memberlux_certificate table for correct operation).
Purpose: Monitoring "sleepers" with extended but buggy functionality: certificate checking, table sorting. Architecture refactored from monolith to modular, but the code is convoluted and unoptimized.

1. FILE ARCHITECTURE (MODULAR, WITH CONFUSION)
text
ml-learning-monitor/
├── ml-learning-monitor.php              # Bootstrap. Requires classes (version 1.02)
├── includes/
│   ├── class-mlm-core.php              # Facade, initialization, hooks
│   ├── class-mlm-assets.php            # Enqueues JS/CSS (used)
│   ├── class-mlm-term-fields.php       # Renders and saves main UI (used)
│   ├── class-mlm-email-fields.php      # Renders and saves email fields (used)
│   ├── class-mlm-ajax-handler.php      # AJAX handler for `mlm_get_sleepers` (used)
│   ├── class-mlm-database.php          # SQL queries with JOIN on certificates (used)
│   └── class-mlm-renderer.php          # Generates HTML table with sorting (used)
├── assets/
│   ├── mlm-admin.js                    # Frontend logic v1.02 (works with forms)
│   └── mlm-admin.css                   # Styles (same as v1.01)
└── (NOT USED in this build:)
    └── trait-mlm-admin-ui.php          # TRAIT ARTIFACT. Alternative, unused UI implementation. Contains duplicate methods.
2. KEY CHANGES AND NEW BUGS (RELATIVE TO 1.01)
Feature Version 1.01 (Monolith) Version 1.02 (Modular)  Issues / Bugs in 1.02
Architecture    Single class (ML_Learning_Monitor)  7 classes + facade. SRP principle.  Code is convoluted. Unused trait artifact present.
Certificate Check   ❌ Not checked   ✅ JOIN on memberlux_certificate Logic unverified. Potential errors due to incorrect c.certificate_id IS NULL condition.
Table Sorting   ❌ None  ✅ Clickable headers, sort/order params  BUG: Sorting by some columns (e.g., "Last Issue Date") does not work or works incorrectly (doesn't toggle ASC/DESC). JS does not handle clicks on new headers from MLM_Renderer.
SQL Query   Basic, without status and date checks   Complex: status='expired', check date_end!='0000-00-00'.    Unreliable: Conditions added but correctness not guaranteed. Using status='expired' may not match data in memberlux_term_keys.
"Sleepers" Output   No filters  With filters and pagination Results may be incorrect. Low confidence in query accuracy.
3. CLASSES AND THEIR ROLES (USED BUILD)
ML_Learning_Monitor (Core)

Constants: TAXONOMY, NONCE_ACTION, PER_PAGE=20.

Role: Facade. Initializes components and registers hooks in __construct.

MLM_Assets

Method: enqueue($hook). Enqueues scripts/styles only on term.php?taxonomy=wpm-levels.

Localization: Passes ajaxUrl, nonce, perPage to JS object MLM.

MLM_Term_Fields

Methods: render($term), save($term_id, $tt_id).

Role: Renders enable checkbox, container with tabs, "sleepers" block. Saves mlm_enabled and delegates email field saving to MLM_Email_Fields.

MLM_Email_Fields

Methods: render_student(), render_admin(), save_student_fields(), save_admin_fields().

Role: Handles email fields (days, subject, body). Displays shortcodes (replacement not implemented).

MLM_Ajax_Handler

Method: get_sleepers().

Role: Validation, security, calls MLM_Database and MLM_Renderer. Added $sort, $order parameters (new in 1.02).

MLM_Database (Most changed class)

Method: query_sleepers($term_id, $page, $per_page, $date_from, $date_to, $sort='user_id', $order='DESC').

Key Changes (and potential bugs):

JOIN on memberlux_certificate: LEFT JOIN ... ON c.user_id = k.user_id AND c.wpmlevel_id = k.term_id. Condition c.certificate_id IS NULL excludes users with certificates.

Status Check: AND k.status = 'expired' — critical logical error. The memberlux_term_keys table does not have an expired status. Access is considered expired by date (date_end). This condition will zero out results.

Date Checks: Added AND k.date_end != '0000-00-00' and AND k.date_end <= %s (with current_time).

Sorting: Implemented via a whitelist of fields ($allowed_sort). But may not work due to the complex subquery s.

MLM_Renderer (With new buggy functionality)

Method: render_sleepers_table(..., $sort, $order).

New: Generates table headers using render_sortable_header(), making them clickable (<a href="#" class="mlm-sort" data-sort="..." data-order="...">).

BUG: The generated sorting links do not have a working JS handler. mlm-admin.js has a stub ($(document).on("click", ".mlm-sort", ...)), but the logic may be disconnected from the new HTML or contain errors. Sorting by clicking headers does not work.

4. KNOWN BUGS AND ISSUES (REQUIRING FIXES)
Table Sorting: Non-functional. Click on headers (mlm-sort) does not change order. Causes: JS not handling events, or sort/order parameters incorrectly passed to AJAX.

SQL Condition status='expired': Critical logical error. The memberlux_term_keys table has no expired status. Access is considered expired by date (date_end). This condition will break the query.

Certificate Check: Logic c.certificate_id IS NULL may be incorrect if the relationship between wpmlevel_id (in certificate) and term_id (in key) is ambiguous. Requires verification.

Code Duplication / Artifacts: Presence of unused trait MLM_Admin_UI indicates sloppy refactoring and a convoluted codebase.

Overall Reliability: No confidence that the query returns correct "sleepers". Requires thorough testing with various data.

Lack of Optimization: Queries are complex, with multiple subqueries and JOINs. No indexes, no caching.

5. WORDPRESS HOOKS (Similar to 1.01, but registered via Core)
php
// Registration in ML_Learning_Monitor::setup_hooks()
add_action('wpm-levels_edit_form_fields', [$this->term_fields, 'render'], 20, 1);
add_action('edited_wpm-levels', [$this->term_fields, 'save'], 20, 2);
add_action('admin_enqueue_scripts', [$this->assets, 'enqueue']);
add_action('wp_ajax_mlm_get_sleepers', [$this->ajax_handler, 'get_sleepers']);
6. RECOMMENDATIONS FOR FURTHER DEVELOPMENT
Fix status='expired': Replace with correct check: AND k.date_end IS NOT NULL AND k.date_end <= CURRENT_DATE().

Fix Sorting:

Verify JS event handler for .mlm-sort.

Ensure sort and order parameters reach MLM_Database::query_sleepers() and are correctly applied in SQL ORDER BY.

Verify Certificate Check: Write a unit test or perform manual checks to ensure JOIN correctly excludes users with issued certificates.

Remove Dead Code: Delete the MLM_Admin_UI trait and other artifacts.

Optimize Query: Run EXPLAIN on the query, add indexes on memberlux_term_keys(term_id, date_end, user_id, is_banned).

Add Logging: Implement logging for AJAX requests for debugging.

Test Phase 2: Before moving to Phase 3 (Cron), ensure stable operation of Phase 2 (correct "sleeper" business logic).

CONCLUSION: Version 1.02 is raw and unstable. It represents an attempt at refactoring and adding new features but contains critical bugs preventing its use. This is not production-ready code, but a development stage requiring significant debugging and refinement.

## ⚠️ Important Note (Baseline)

This version is the first Git-committed snapshot after a local-only iteration.
Some minor fixes from the previous local state may be missing.

All further development MUST be based on this Git baseline.