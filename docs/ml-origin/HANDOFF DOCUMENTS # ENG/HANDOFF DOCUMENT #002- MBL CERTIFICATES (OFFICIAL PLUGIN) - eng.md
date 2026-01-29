Type: Official MemberLux Module
Dependencies: MemberLux Core version >= 2.3.4, license key fullpackaccess or cerificates.
Purpose: Issuing certificates to users as proof of access level completion. Creates immutable records and emits a canonical event to trigger downstream processes (e.g., course chains).

1. FILE ARCHITECTURE
text
mbl-certificates/
├── mbl-certificates.php              # Module bootstrap
├── includes/
│   ├── class-certificate.php         # Main "Certificate" entity
│   ├── class-mblcert-core.php        # License check and initialization
│   └── hooks.php                     # Module hook registration
└── (additional admin/template files)
2. DATABASE STRUCTURE
Main Table: {prefix}memberlux_certificate

sql
-- TABLE OF IMMUTABLE FACTS. A record's presence = certificate issued.
CREATE TABLE `wp_memberlux_certificate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT 'ID of the user who received the certificate',
  `wpmlevel_id` int(11) NOT NULL COMMENT 'ID of the wpm-levels term (access level)',
  `date_issue` datetime NOT NULL COMMENT 'Date and time of issuance',
  `template_id` int(11) DEFAULT NULL COMMENT 'Link to certificate template',
  -- ... (other meta fields possible)
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `wpmlevel_id` (`wpmlevel_id`),
  KEY `date_issue` (`date_issue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- IMPORTANT: The table does NOT have a `status` column. Certificates are not revoked or changed.
Auxiliary Table: {prefix}memberlux_certificate_templates (for template storage).

3. CLASSES AND METHODS (PUBLIC CONTRACT)
Class: Certificate (file includes/class-certificate.php)

php
class Certificate {
    /**
     * CREATE CERTIFICATE (MAIN PUBLIC METHOD)
     * Purpose: Create a certificate record and fire the global event.
     * Action: Calls do_action('mbl_certificate_issued', $user_id, $certificate_id);
     * Returns: ID of the created certificate (int) or false.
     */
    public static function create(...): int|false;

    /**
     * RETRIEVE CERTIFICATE DATA
     * Purpose: Get a certificate object by its ID.
     * Returns: Object with properties user_id, wpmlevel_id, date_issue, etc. or false.
     */
    public static function getCertificate(int $certificate_id): object|false;
}
4. WORDPRESS HOOKS
Actions provided by the module:

php
// MAIN MODULE EVENT. THE ONLY RELIABLE SIGNAL OF CERTIFICATE ISSUANCE.
// Fired immediately after a successful insert into the memberlux_certificate table.
// Used as a trigger for program progression, notifications, and integrations.
do_action('mbl_certificate_issued', int $user_id, int $certificate_id);
Actions listened to by the module:

php
// AUTOMATIC CERTIFICATE ISSUANCE UPON ACCESS ACTIVATION.
// The module subscribes to the main MemberLux Core hook.
add_action('wpm_update_user_key_dates', 'mblc_certificate_issuance_after_update_user_key_dates', 10, 5);

// HANDLER LOGIC (mblc_certificate_issuance_after_update_user_key_dates):
// 1. Checks the term meta field '_mblc_how_to_issue'.
// 2. If the value = 'auto', immediately calls Certificate::create().
// 3. This creates an "AUTO-certificate". Manual issuance via admin creates a "MANUAL-certificate".
5. INTEGRATION PRINCIPLES AND ANTI-PATTERNS
Principle: Certificates are immutable facts. React to their appearance event (mbl_certificate_issued), do not poll the table.

Idempotency: Repeated calls to Certificate::create() for the same user and level will likely create duplicate records. Idempotency must be ensured by the consumer (e.g., by storing a hash of the issued certificate in user_meta).

Auto vs Manual: Distinction logic must be implemented by the hook consumer (e.g., checking temporal proximity of date_issue and key's date_start). The module itself does not mark the issuance type in the DB.

Do NOT use: Assumptions about a status field, attempts to "revoke" or "update" a certificate.