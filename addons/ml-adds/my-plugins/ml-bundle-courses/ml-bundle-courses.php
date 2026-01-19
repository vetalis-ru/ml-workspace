<?php
/**
 * Plugin Name: ML Bundle Courses
 * Description: Пошаговая выдача УД по программам (Сборный курс) на основании сертификатов.
 * Version: 0.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/includes/class-mlp-program-cpt.php';
require_once __DIR__ . '/includes/class-mlp-enrollment.php';
require_once __DIR__ . '/includes/class-mlp-enrollment-admin.php';
require_once __DIR__ . '/includes/class-mlp-certificate-hook.php';
require_once __DIR__ . '/includes/class-mlp-notifier.php';
require_once __DIR__ . '/includes/class-mlp-logger.php';

add_action('init', ['MLP_Program_CPT', 'register']);
add_action('init', ['MLP_Enrollment_Admin', 'register']);
add_action('mbl_certificate_issued', ['MLP_Certificate_Hook', 'handle'], 10, 2);
