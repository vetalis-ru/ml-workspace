Type: Base Plugin (Read-Only Engine)
API Version: 2.x (compatible with modules from 2.3.4)
Purpose: Low-level access control engine for WordPress content, based on terms (wpm-levels) and access keys (Term Keys).

1. FILE ARCHITECTURE (KEY FILES)
text
memberlux.php                          # Plugin bootstrap, constants declaration
includes/
├── class-wpm-term-keys.php           # Core model for term key operations
├── class-wpm-term-keys-query.php     # SQL query builder for keys
├── functions/
│   ├── term-keys.php                 # Key generation and management functions
│   ├── wpm-user.php                  # Key activation functions
│   ├── wpm-register.php              # User registration function
│   ├── send-user-email.php           # User email dispatch
│   └── send-registration-email.php   # Registration email dispatch
├── class-mbl-mailer.php              # Unified email sending system
├── cron/wpm-cron.php                 # Cron task registration and logic
├── taxonomies/wpm-levels.php         # Registration of the access level taxonomy
└── class-mbl-logger.php              # Canonical logging class
2. DATABASE STRUCTURE
Central Table: {prefix}memberlux_term_keys

sql
-- SINGLE SOURCE OF TRUTH FOR ACCESS.
CREATE TABLE `wp_memberlux_term_keys` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `term_id` int(11) NOT NULL COMMENT 'ID of the wpm-levels taxonomy term',
  `user_id` int(11) DEFAULT NULL COMMENT 'WP User ID (NULL until activation)',
  `key` varchar(255) NOT NULL COMMENT 'Unique access key (code)',
  `status` varchar(50) DEFAULT 'NEW' COMMENT 'Status: NEW, USED',
  `date_registered` datetime DEFAULT NULL COMMENT 'Key activation time (current_time())',
  `date_start` datetime DEFAULT NULL COMMENT 'Access start date',
  `date_end` datetime DEFAULT NULL COMMENT 'Access end date',
  `duration` int(11) DEFAULT NULL COMMENT 'Access duration',
  `units` varchar(20) DEFAULT NULL COMMENT 'Duration units (day, month, year)',
  `is_unlimited` tinyint(1) DEFAULT 0 COMMENT 'Flag for unlimited access',
  `is_banned` tinyint(1) DEFAULT 0 COMMENT 'Key ban flag',
  PRIMARY KEY (`id`),
  KEY `term_id` (`term_id`),
  KEY `user_id` (`user_id`),
  KEY `key` (`key`),
  KEY `date_end` (`date_end`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
3. MAIN PHP FUNCTIONS (PUBLIC CONTRACT)
php
// 1. KEY GENERATION (without activation)
// Purpose: Create an access key for later activation.
// Returns: Key code (string) or false.
function wpm_insert_one_user_key(
    int $term_id,
    int $duration,
    string $units,
    bool $is_unlimited = false
): string|false;

// 2. KEY LOOKUP
// Purpose: Retrieve key information by its code.
// Returns: Associative array with record fields, including 'term_id'.
function wpm_search_key_id(string $code): array;

// 3. KEY ACTIVATION (MAIN TRIGGER)
// Purpose: Assign a key to a user, set access dates.
// Logic: Sets date_start = current_time(), calculates date_end,
//        sets user_id, status='USED'.
// HOOK: Calls do_action('wpm_update_user_key_dates', ...).
// Returns: bool (success/failure).
function wpm_update_user_key_dates(
    int $user_id,
    string $code,
    bool $isBanned = false,
    string $source = ''
): bool;

// 4. USER REGISTRATION
// Purpose: Canonical function for creating a user with optional key activation.
// Logic: If $user_data['code'] is provided, calls wpm_update_user_key_dates().
function wpm_register_user(
    array $user_data,
    bool $send_email = true
): int|WP_Error;

// 5. EMAIL DISPATCH
function wpm_send_email_about_new_key(WP_User $user, string $code, int $term_id): bool;
function wpm_send_user_email(int $user_id, string $key, string $email, array $params, array $files = []): bool;
function wpm_send_registration_email(string $recipient_type, ...): bool;
4. WORDPRESS HOOKS (PUBLIC API)
Actions provided by the core:

php
// MAIN TRIGGER FOR INTEGRATIONS.
// Fired immediately after successful key activation via wpm_update_user_key_dates().
// Parameters: User ID, Access term ID, Key code, Key object, Activation source.
do_action('wpm_update_user_key_dates', int $user_id, int $term_id, string $code, object $key, string $source);
Filters provided by the core:

php
// UNIFIED POINT FOR EMAIL SHORTCODE PROCESSING.
// Allows adding or modifying shortcode replacement in all MemberLux emails.
apply_filters('wpm_user_mail_shortcode_replacement', string $message, int $user_id, array $params);
5. TAXONOMY AND META-DATA
Taxonomy: wpm-levels. All access levels are terms of this taxonomy.

Edit Screen: term.php?taxonomy=wpm-levels&tag_ID={term_id}

Term Meta Storage Methods:

Standard WP: get_term_meta($term_id, '_meta_key', true);

Via Options (used in core): get_option("taxonomy_term_{$term_id}");

Importance: This is the official and only UI extension point for adding level-specific settings.

6. CRON TASKS (PATTERN)
File: includes/cron/wpm-cron.php

Purpose: Reference implementation for periodic background tasks (reminders, status checks).

Principle: All cron processes must be idempotent.

7. DEPENDENCIES AND LIMITATIONS
Provides: Low-level API (keys, dates, statuses, events). Does not contain higher-order automation, reminder, or multi-step program logic.

Does NOT provide:

Built-in reminder scheduler.

Renewal automation.

State machine for complex programs.

Protection against repeated external triggers (idempotency is the module's responsibility).

Developer Model: MemberLux Core is a read-only low-level access engine. All higher-order business logic must be implemented in custom plugins using public hooks and functions.