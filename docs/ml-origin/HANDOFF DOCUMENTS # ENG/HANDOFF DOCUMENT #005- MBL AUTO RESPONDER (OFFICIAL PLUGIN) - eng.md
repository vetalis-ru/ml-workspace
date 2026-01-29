Type: Official MemberLux Module
Dependencies: MemberLux Core version >= 2.3.4, license key fullpackaccess or auto-responder.
Purpose: Creating automatic email sequences (autoresponders) tied to the activation of a specific access level (term_id). Emails are sent on a schedule relative to the user's key activation date (date_registered).

1. DATABASE STRUCTURE
The module extends the DB schema with several service tables:

sql
-- 1. MAILING TEMPLATES (funnels)
CREATE TABLE `wp_memberlux_mailing_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `term_id` int(11) NOT NULL COMMENT 'Access level ID the funnel is tied to',
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `term_id` (`term_id`)
);

-- 2. EMAILS WITHIN A FUNNEL
CREATE TABLE `wp_memberlux_mailing` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `template_id` int(11) NOT NULL,
  `send_after_days` int(11) NOT NULL COMMENT 'Days relative to access activation',
  `subject` text,
  `body` longtext,
  `sort_order` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `template_id` (`template_id`)
);

-- 3. SENDING RESULTS (log)
CREATE TABLE `wp_memberlux_mailing_results` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `mailing_id` int(11) NOT NULL COMMENT 'Reference to wp_memberlux_mailing.id',
  `scheduled_at` datetime DEFAULT NULL,
  `sent_at` datetime DEFAULT NULL,
  `status` varchar(50) DEFAULT 'pending' COMMENT 'pending, sent, failed',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `mailing_id` (`mailing_id`),
  KEY `status_scheduled` (`status`, `scheduled_at`)
);

-- 4. KEY META-DATA (for storing subscription info)
CREATE TABLE `wp_memberlux_keys_meta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `term_key_id` int(11) NOT NULL COMMENT 'Reference to memberlux_term_keys.id',
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext,
  PRIMARY KEY (`id`),
  KEY `term_key_id` (`term_key_id`),
  KEY `meta_key` (`meta_key`)
);

-- 5. MAILING LISTS AND USER SUBSCRIPTIONS
CREATE TABLE `wp_memberlux_mailing_list` (...);
CREATE TABLE `wp_memberlux_user_mailing_list` (...);
2. ACTIVATION MECHANISM: FROM KEY TO SUBSCRIPTION
The module subscribes to the main MemberLux Core event to trigger subscription logic.

php
// MAIN ENTRY POINTS (Core hooks)
add_action('wpm_update_user_key_dates', 'mblar_activate_key_subscription', 10, 5);
add_action('wpm_mbl_term_key_updated', 'mblar_activate_key_subscription', 10, 2);
add_action('wpm_mbl_term_keys_query_updated', 'mblar_activate_key_subscription', 10, 2);

// HANDLER LOGIC (mblar_activate_key_subscription):
// 1. Gets $term_id and $user_id from the hook data.
// 2. Checks if an active mailing template (funnel) exists for this $term_id.
// 3. If yes, creates task records in wp_memberlux_mailing_results for EACH email in the funnel.
//    - calculated_send_date = date_registered + send_after_days
//    - status = 'pending'
// 4. Records the subscription fact in wp_memberlux_keys_meta.
3. CRON MAILING SYSTEM
php
// EVENT AND INTERVAL
add_action('mbl_auto_responder_event', 'mbl_auto_responder_cron_handler');
add_filter('cron_schedules', 'mblar_add_cron_interval'); // Adds interval, e.g., every 60 sec.

// CRON HANDLER LOGIC (mbl_auto_responder_cron_handler):
// 1. Selects tasks from wp_memberlux_mailing_results with status='pending' AND scheduled_at <= current_time().
// 2. For each task:
//    a) Loads the email template (subject, body).
//    b) Replaces shortcodes (via the wpm_user_mail_shortcode_replacement filter).
//    c) Sends the email via MBL_Mailer (standard MemberLux class).
//    d) Updates the record: status='sent', sent_at=current_time().
//    e) Logs error on failure.
4. INTEGRATION PRINCIPLES
Trigger: Based on wpm_update_user_key_dates. Any extension that activates a key via the Core API automatically gets the autoresponder functionality (if a funnel is configured for that term_id).

Email Shortcodes: Uses the common MemberLux filter wpm_user_mail_shortcode_replacement for email rendering, ensuring consistency.

Idempotency: The wpm_update_user_key_dates hook handler must check via memberlux_keys_meta if the user is already subscribed to a funnel to avoid duplicate tasks.

Unsubscribe: Can be implemented by setting a flag in memberlux_keys_meta or is_banned on the main key, which should be handled by the cron task.