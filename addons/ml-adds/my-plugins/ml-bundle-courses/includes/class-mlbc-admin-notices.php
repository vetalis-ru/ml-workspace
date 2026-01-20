<?php // includes/class-mlbc-admin-notices.php

class MLBC_Admin_Notices {
    private const OPTION_KEY = 'mlbc_admin_notices';

    public static function register(): void {
        add_action('admin_notices', [__CLASS__, 'render_notices']);
    }

    public static function add_notice(string $type, string $message, bool $dismissible = true): void {
        if (!is_admin()) {
            return;
        }

        $allowed = ['success', 'warning', 'error', 'info'];
        $type = in_array($type, $allowed, true) ? $type : 'info';

        $notices = get_option(self::OPTION_KEY, []);
        if (!is_array($notices)) {
            $notices = [];
        }

        $notices[] = [
            'type' => $type,
            'message' => $message,
            'dismissible' => $dismissible,
        ];

        update_option(self::OPTION_KEY, $notices, false);
    }

    public static function render_notices(): void {
        if (!is_admin() || !current_user_can('manage_options')) {
            return;
        }

        $notices = get_option(self::OPTION_KEY, []);
        if (empty($notices) || !is_array($notices)) {
            return;
        }

        delete_option(self::OPTION_KEY);

        foreach ($notices as $notice) {
            $type = isset($notice['type']) ? $notice['type'] : 'info';
            $message = isset($notice['message']) ? $notice['message'] : '';
            $dismissible = !empty($notice['dismissible']);
            $class = 'notice notice-' . esc_attr($type);
            if ($dismissible) {
                $class .= ' is-dismissible';
            }
            echo '<div class="' . $class . '"><p>' . esc_html($message) . '</p></div>';
        }
    }
}

function mlbc_add_admin_notice(string $type, string $message, bool $dismissible = true): void {
    MLBC_Admin_Notices::add_notice($type, $message, $dismissible);
}
