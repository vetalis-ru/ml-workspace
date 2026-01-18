<?php // includes/class-mlp-notifier.php

class MLP_Notifier {
    public static function notify($program_id, $user_id, $current_term_id, $next_term_id) {
        $email = get_post_meta($program_id, 'mlp_notify_email', true);
        $webhook = get_post_meta($program_id, 'mlp_webhook_url', true);

        $payload = [
            'user_id' => $user_id,
            'program_id' => $program_id,
            'current_term_id' => $current_term_id,
            'next_term_id' => $next_term_id,
        ];

        if ($email) {
            wp_mail($email, 'ML Program Step Granted', json_encode($payload, JSON_UNESCAPED_UNICODE));
        }

        if ($webhook) {
            wp_remote_post($webhook, [
                'headers' => ['Content-Type' => 'application/json'],
                'body' => wp_json_encode($payload),
                'timeout' => 5,
            ]);
        }
    }
}
