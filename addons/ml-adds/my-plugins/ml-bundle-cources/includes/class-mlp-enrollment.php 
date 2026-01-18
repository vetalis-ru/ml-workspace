<?php // includes/class-mlp-enrollment.php

class MLP_Enrollment {
    public static function get_program_id($user_id) {
        return (int)get_user_meta($user_id, 'mlp_program_id', true);
    }

    public static function set_program_id($user_id, $program_id) {
        update_user_meta($user_id, 'mlp_program_id', (int)$program_id);
    }

    public static function get_current_step($user_id) {
        return (int)get_user_meta($user_id, 'mlp_current_step', true);
    }

    public static function set_current_step($user_id, $step_index) {
        update_user_meta($user_id, 'mlp_current_step', (int)$step_index);
    }

    public static function get_last_cert_hash($user_id) {
        return get_user_meta($user_id, 'mlp_last_certificate_hash', true);
    }

    public static function set_last_cert_hash($user_id, $hash) {
        update_user_meta($user_id, 'mlp_last_certificate_hash', $hash);
    }
}
