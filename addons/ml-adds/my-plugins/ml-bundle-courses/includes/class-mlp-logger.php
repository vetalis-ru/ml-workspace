<?php // includes/class-mlp-logger.php

class MLP_Logger {
    private static function log_path(): string {
        return WP_PLUGIN_DIR . '/ml-bundle-cources/ml-bundle-cources.log';
    }

    public static function error($message, array $context = []) {
        $line = '[' . date('Y-m-d H:i:s') . '] ' . $message;
        if ($context) {
            $line .= ' | ' . wp_json_encode($context);
        }
        $line .= PHP_EOL;

        file_put_contents(self::log_path(), $line, FILE_APPEND);
    }
}

