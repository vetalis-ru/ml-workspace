<?php

class MBLDBUpdates
{
    private static $updates = [
        '0.7.9.7.7'   => [
            'ALTER TABLE %prefix%memberlux_responses ADD `is_archived` tinyint(1) DEFAULT 0;',
            'CREATE INDEX `%prefix%memberlux_responses_is_archived_idx` ON %prefix%memberlux_responses (`is_archived`);',
        ],
        '0.7.9.9.4'   => [
            'CREATE TABLE %prefix%memberlux_logins (
		    `id` int(11) NOT NULL AUTO_INCREMENT,
            `user_id` int(11) ,
            `logged_in_at` datetime,
            `logged_out_at` datetime,
            `last_seen_at` datetime,
            `ip` varchar(20),
            `browser` varchar(100),
            `os` varchar(100),
            `country_name` varchar(100),
            `country_code` varchar(20),
            `device` varchar(255),
            `brandname` varchar(255),
            `model` varchar(255),
            `user_agent` text,
            PRIMARY KEY (`id`));',
            'CREATE INDEX `%prefix%memberlux_logins_user_id_idx` ON %prefix%memberlux_logins (`user_id`);',
            'CREATE INDEX `%prefix%memberlux_logins_logged_in_at_idx` ON %prefix%memberlux_logins (`logged_in_at`);',
            'CREATE INDEX `%prefix%memberlux_logins_logged_out_at_idx` ON %prefix%memberlux_logins (`logged_out_at`);',
        ],
        '0.7.9.9.7'   => [
            'CREATE INDEX `%prefix%memberlux_logins_ip_idx` ON %prefix%memberlux_logins (`ip`);',
        ],
        '1.3'         => [
            'CREATE TABLE %prefix%memberlux_translations (
		    `id` INT(11) NOT NULL AUTO_INCREMENT,
            `language_code` VARCHAR(20),
            `hash` VARCHAR(255),
            `msgid` TEXT,
            `msgstr` TEXT,
            PRIMARY KEY (`id`),
            INDEX `%prefix%memberlux_translations_language_code_idx` (`language_code`),
            UNIQUE KEY `%prefix%memberlux_translations_hash_idx` (`hash`)
            )
            DEFAULT CHARACTER SET utf8
            DEFAULT COLLATE utf8_unicode_ci
            ENGINE=InnoDB;',
        ],
        '2.4.1'       => [
            'ALTER TABLE %prefix%memberlux_responses ADD (`reviewed_by` bigint(11) DEFAULT NULL, `version` int(11) DEFAULT NULL);',
            'CREATE INDEX `%prefix%memberlux_responses_v_idx` ON %prefix%memberlux_responses (`version`);',
            'CREATE INDEX `%prefix%memberlux_responses_reviewed_by_idx` ON %prefix%memberlux_responses (`reviewed_by`);',
        ],
        //SET sql_mode = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';
        '2.9.9.9.5'   => [
            'ALTER TABLE %prefix%memberlux_term_keys ADD `is_unlimited` TINYINT(1) DEFAULT 0;',
        ],
        '2.9.9.9.9.6' => [
            'UPDATE `%prefix%memberlux_term_keys` SET `is_unlimited` = 0 WHERE `is_unlimited` IS NULL;',
            'ALTER TABLE `%prefix%memberlux_term_keys` CHANGE `is_unlimited` `is_unlimited` TINYINT(1) NOT NULL DEFAULT 0;',
        ],
    ];

    private static $checks = [
        '2.9.9.9.9.9.8' => [
            ['MBLDBUpdates', 'checkUnlimited'],
        ],
    ];

    public static function update()
    {
        global $wpdb;

        foreach (self::$updates as $version => $updates) {
            if (version_compare(get_option('wpm_version'), $version, '<')) {
                foreach ($updates as $update) {
                    $wpdb->query(str_replace('%prefix%', $wpdb->prefix, $update));
                }
                update_option('wpm_last_db_update', $version);
            }
        }
    }

    public static function check()
    {
        $lastCheck = get_option('wpm_last_db_check', '0.1');

        foreach (self::$checks as $version => $updates) {
            if (version_compare(get_option('wpm_version'), $version, '>') && version_compare($lastCheck, $version, '<')) {
                foreach ($updates as $update) {
                    if (is_callable($update)) {
                        try {
                            call_user_func($update);
                        } catch (Exception $exception) {
                            self::log($exception->getMessage());
                        }
                    }
                }
                update_option('wpm_last_db_check', $version);
            }
        }
    }

    public static function checkUnlimited()
    {
        global $wpdb;
        $table = $wpdb->prefix . 'memberlux_term_keys';

        $existingColumns = $wpdb->get_col("DESC {$table}", 0);

        if (is_array($existingColumns) && in_array('term_id', $existingColumns) && !in_array('is_unlimited', $existingColumns)) {
            $wpdb->query(sprintf('ALTER TABLE `%s` ADD `is_unlimited` TINYINT(1) NOT NULL DEFAULT 0;', $table));
        }
    }

    public static function log($message)
    {
        $logDir = WP_MEMBERSHIP_DIR . '/log';
        $logFile = $logDir . '/update.log';

        if (is_array($message)) {
            $message = print_r($message, true);
        }

        if (!file_exists($logDir)) {
            mkdir($logDir);
        }

        if (is_dir($logDir) && is_writable($logDir)) {
            file_put_contents($logFile, $message . "\n", FILE_APPEND);
        }
    }
}