Type: Custom Plugin, State Manager
Dependencies: MemberLux Core, MBL Certificates (Official Plugin).
Purpose: Management of step-by-step programs ("bundle courses"). Automatically grants the next access level (term_id) in a chain after the user receives a certificate for the previous step.

1. DATA MODEL
The plugin introduces its own data structure on top of WordPress.

1.1. Custom Post Type: ml_program

Purpose: "Program" object containing a chain of steps.

Meta Field mlp_steps: Serialized array of steps.

php
// STRUCTURE OF THE mlp_steps ARRAY
$steps = [
    [
        'term_id'  => 25,   // Access level ID (wpm-levels term)
        'duration' => 30,   // Access duration for this step
        'units'    => 'day' // Units (day, month, year)
    ],
    [
        'term_id'  => 26,
        'duration' => 1,
        'units'    => 'month'
    ],
    // ... next steps
];
1.2. User Meta (User Progress):
To track each user's progress in a program, key meta fields are used:

mlp_program_id - (int) ID of the program (ml_program CPT) the user is enrolled in.

mlp_current_step - (int) Current index in the mlp_steps array (starts at 0).

mlp_last_certificate_hash - (string) Unique hash of the last processed certificate. Critical for idempotency.

2. ARCHITECTURE AND BOOTSTRAP
php
// MAIN PLUGIN FILE: ml-bundle-courses.php
require_once __DIR__ . '/includes/class-mlp-program-cpt.php';      // Registers CPT `ml_program`
require_once __DIR__ . '/includes/class-mlp-enrollment.php';       // Logic for user enrollment
require_once __DIR__ . '/includes/class-mlp-enrollment-admin.php'; // UI for manual management
require_once __DIR__ . '/includes/class-mlp-certificate-hook.php'; // MAIN HANDLER - reacts to certificates
require_once __DIR__ . '/includes/class-mlp-notifier.php';         // Notifications
require_once __DIR__ . '/includes/class-mlp-logger.php';           // Logging
require_once __DIR__ . '/includes/class-mlbc-admin-notices.php';   // Admin notices

// IMPORTANT: Initialization of key handlers is TIED TO THE CERTIFICATE ISSUANCE EVENT.
// This is a lazy-load approach: plugin logic activates only when the first certificate is issued in the system.
add_action('mbl_certificate_issued', ['MLP_Program_CPT', 'register']);
add_action('mbl_certificate_issued', ['MLP_Enrollment_Admin', 'register']);
add_action('mbl_certificate_issued', ['MLBC_Admin_Notices', 'register']);
// MAIN TRIGGER: Handler that advances the user through the program.
add_action('mbl_certificate_issued', ['MLP_Certificate_Hook', 'handle'], 10, 2);
3. MAIN HANDLER ALGORITHM
Class: MLP_Certificate_Hook, method handle(int $user_id, int $certificate_id)

php
// PSEUDOCODE ALGORITHM
1.  GET CERTIFICATE DATA:
    $cert = Certificate::getCertificate($certificate_id);
    $cert_term_id = $cert->wpmlevel_id; // Level for which the certificate was issued
    $cert_hash = md5($user_id . '_' . $certificate_id . '_' . $cert_term_id);

2.  CHECK IDEMPOTENCY:
    if (get_user_meta($user_id, 'mlp_last_certificate_hash', true) === $cert_hash) {
        return; // This certificate has already been processed.
    }

3.  FIND USER'S ACTIVE PROGRAM:
    $program_id = get_user_meta($user_id, 'mlp_program_id', true);
    $current_step_index = get_user_meta($user_id, 'mlp_current_step', true);

4.  VALIDATE STEP:
    $program_steps = get_post_meta($program_id, 'mlp_steps', true);
    $current_step_term_id = $program_steps[$current_step_index]['term_id'] ?? null;

    if ($current_step_term_id != $cert_term_id) {
        // The issued certificate does not match the current expected step in the program.
        // Possibly a manual certificate or an error. Log and exit.
        return;
    }

5.  GRANT THE NEXT ACCESS LEVEL:
    $next_step_index = $current_step_index + 1;
    if (isset($program_steps[$next_step_index])) {
        $next_step = $program_steps[$next_step_index];
        // CANONICAL GRANT VIA CORE:
        $new_key = wpm_insert_one_user_key($next_step['term_id'], $next_step['duration'], $next_step['units']);
        wpm_update_user_key_dates($user_id, $new_key, false, 'bundle_courses_progression');

        // UPDATE USER PROGRESS
        update_user_meta($user_id, 'mlp_current_step', $next_step_index);
    } else {
        // PROGRAM COMPLETED
        delete_user_meta($user_id, 'mlp_program_id');
        delete_user_meta($user_id, 'mlp_current_step');
    }

6.  UPDATE HASH AND FIRE CUSTOM HOOK:
    update_user_meta($user_id, 'mlp_last_certificate_hash', $cert_hash);
    do_action('mlp_program_step_granted', $user_id, $program_id, $cert_term_id, $next_step['term_id'] ?? null);
4. WORDPRESS HOOKS
Actions provided by the plugin:

php
// CUSTOM HOOK FOR INTEGRATION.
// Fired after successfully granting the next step in a program.
// Parameters: User ID, Program ID, term_id of the completed step, term_id of the granted step (or null if program ended).
do_action('mlp_program_step_granted', int $user_id, int $program_id, int $completed_term_id, ?int $granted_term_id);
5. CRITICAL DEVELOPMENT PRINCIPLES
Idempotency: Ensured by hashing the certificate (mlp_last_certificate_hash). One certificate must not trigger access grant twice.

Step Validation: The plugin verifies that the issued certificate corresponds to the user's current expected step in the program. This protects against accidental manual certificates.

Auto vs Manual: The algorithm does not distinguish between AUTO and MANUAL certificates. If an admin manually issues a certificate for the current step, the plugin will treat it as step completion and grant the next level. This is a design feature.

Dependency on Certificates Module: The plugin does not work without the active MBL Certificates module, as it is entirely built on its mbl_certificate_issued hook.