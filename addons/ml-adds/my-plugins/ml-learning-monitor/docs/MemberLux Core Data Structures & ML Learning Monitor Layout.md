1) memberlux_term_keys (access keys)

Purpose: canonical access model in MemberLux.

Table: wp_memberlux_term_keys
Key fields (relevant):

term_id — wpm-levels term (access level / course)

user_id — WP user ID (NULL until activation)

key — access code

status — NEW / USED

date_registered — activation timestamp

date_start — access start (usually equals activation time)

date_end — access end (NULL if unlimited)

duration, units — original duration params

is_unlimited — unlimited access flag

is_banned — ban flag

Notes:

Keys are generated first, activated later.

wpm_update_user_key_dates() sets dates and status=USED.

Expired access = date_end < NOW() and is_unlimited = 0.

2) memberlux_certificate (certificates)

Purpose: completion proof for a course (term).

Table: wp_memberlux_certificate
Key fields (relevant):

id — certificate ID

user_id — WP user ID

wpmlevel_id — term_id (course/access level)

date_issue — issue timestamp

template_id — certificate template

Notes:

Certificates are created via Certificate::create().

Immediately triggers:
do_action('mbl_certificate_issued', $user_id, $cert_id).

Auto-issued certificates exist; manual issuance must be distinguished by logic, not UI.

3) ML Learning Monitor (LM) – File Structure

Plugin root: ml-learning-monitor/

ml-learning-monitor.php      // main plugin file:
                             // - term UI extension (wpm-levels edit)
                             // - settings storage (term_meta)
                             // - sleepers query logic
                             // - AJAX endpoint (admin-only)

assets/
  mlm-admin.js               // tabs, toggle, AJAX loaders, pagination
  mlm-admin.css              // admin UI styles


Responsibilities:

Read-only analysis of term keys + certificates.

Identify “sleepers”: expired access + no (manual) certificate.

No core mutation, no cron yet (Phase 1).

Admin-only UI, loaded only on term.php?taxonomy=wpm-levels.