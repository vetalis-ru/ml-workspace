<?php // includes/class-mlp-notifier.php

class MLP_Notifier {
    public static function notify($program_id, $user_id, $current_term_id, $next_term_id, array $context = []) {
        $notify_enabled = (bool)get_post_meta($program_id, 'mlp_notify_enabled', true);
        $email = get_post_meta($program_id, 'mlp_notify_email', true);
        $webhook = get_post_meta($program_id, 'mlp_webhook_url', true);

        $payload = [
            'user_id' => $user_id,
            'program_id' => $program_id,
            'current_term_id' => $current_term_id,
            'next_term_id' => $next_term_id,
            'event' => $next_term_id ? 'step_granted' : 'program_completed',
            'context' => $context,            
        ];

        if ($notify_enabled && $email) {
            $subject = $next_term_id
                ? get_post_meta($program_id, 'mlp_notify_step_subject', true)
                : get_post_meta($program_id, 'mlp_notify_complete_subject', true);
            $body = $next_term_id
                ? get_post_meta($program_id, 'mlp_notify_step_body', true)
                : get_post_meta($program_id, 'mlp_notify_complete_body', true);

            $subject = self::replace_tokens($subject ?: 'ML Program Update', $payload);
            $body = self::replace_tokens($body ?: wp_json_encode($payload, JSON_UNESCAPED_UNICODE), $payload);

            wp_mail($email, $subject, $body);
        }

        if ($webhook) {
            wp_remote_post($webhook, [
                'headers' => ['Content-Type' => 'application/json'],
                'body' => wp_json_encode($payload),
                'timeout' => 5,
            ]);
        }
    }

    private static function replace_tokens($text, array $payload) {
        $user = get_user_by('ID', (int)$payload['user_id']);
        $program_title = (string)get_the_title((int)$payload['program_id']);
        $current_course = $payload['current_term_id'] ? (string)term_description((int)$payload['current_term_id'], 'wpm-levels') : '';
        $next_course = $payload['next_term_id'] ? (string)term_description((int)$payload['next_term_id'], 'wpm-levels') : '';

        if ($payload['current_term_id']) {
            $current_term = get_term((int)$payload['current_term_id'], 'wpm-levels');
            if ($current_term && !is_wp_error($current_term)) {
                $current_course = $current_term->name;
            }
        }
        if ($payload['next_term_id']) {
            $next_term = get_term((int)$payload['next_term_id'], 'wpm-levels');
            if ($next_term && !is_wp_error($next_term)) {
                $next_course = $next_term->name;
            }
        }

        $replacements = [
            '[user_id]' => (string)$payload['user_id'],
            '[user_email]' => $user ? $user->user_email : '',
            '[user_login]' => $user ? $user->user_login : '',
            '[display_name]' => $user ? $user->display_name : '',
            '[program_id]' => (string)$payload['program_id'],
            '[program_title]' => $program_title,
            '[current_term_id]' => (string)$payload['current_term_id'],
            '[current_course]' => $current_course,
            '[current_step]' => isset($payload['context']['current_step']) ? (string)$payload['context']['current_step'] : '',
            '[total_steps]' => isset($payload['context']['total_steps']) ? (string)$payload['context']['total_steps'] : '',
            '[next_term_id]' => (string)$payload['next_term_id'],
            '[next_course]' => $next_course,
            '[next_step]' => isset($payload['context']['next_step']) ? (string)$payload['context']['next_step'] : '',
            '[duration]' => isset($payload['context']['duration']) ? (string)$payload['context']['duration'] : '',
            '[units]' => isset($payload['context']['units']) ? (string)$payload['context']['units'] : '',
        ];

        return strtr((string)$text, $replacements);
    }    
}
