<?php

class MBLTermKeysQuery
{
    const NEW_SCHEMA_VERSION = '0.7.9.7';

    public static $table;
    public static $fields;

    public static $dates = [
        'date_start',
        'date_end',
        'date_registered',
    ];

    public static $units = [
        'months',
        'days',
    ];

    private static $userKeysCache = [];

    private static function _init()
    {
        global $wpdb;

        if (!isset(self::$table)) {
            self::$table = $wpdb->prefix . "memberlux_term_keys";
            self::$fields = self::getFields();
        }
    }

    /**
     * @return string
     */
    public static function getTable()
    {
        if (!isset(self::$table)) {
            self::_init();
        }

        return self::$table;
    }

    public static function getFields()
    {
        return [
            'id'              => '%d',
            'term_id'         => '%d',
            'user_id'         => '%d',
            'key'             => '%s',
            'key_type'        => '%s',
            'status'          => '%s',
            'sent'            => '%d',
            'is_banned'       => '%d',
            'duration'        => '%d',
            'units'           => '%s',
            'date_start'      => '%s',
            'date_end'        => '%s',
            'date_registered' => '%s',
            'info'            => '%s',
            'is_unlimited'    => '%d',
        ];
    }

    public static function getSelectFieldsString($fields = null)
    {
        $tableFields = array_keys(self::getFields());

        if (is_null($fields)) {
            $fields = '`' . implode('`,`', $tableFields) . '`';
        } elseif (is_array($fields)) {
            $fields = '`' . implode('`,`', $fields) . '`';
        }

        return $fields;
    }

    public static function getOrderString($order = null)
    {
        if (is_array($order)) {
            $order = array_intersect_key($order, self::$fields);
            $orderConditions = [];

            foreach ($order as $field => $value) {
                $orderConditions[] = "`{$field}` {$value}";
            }

            if (count($orderConditions)) {
                $order = 'ORDER BY ' . implode(', ', $orderConditions);
            } else {
                $order = '';
            }
        } elseif (is_null($order)) {
            $order = '';
        }

        return $order;
    }

    public static function getLimitString($limit = null)
    {
        $limitStr = '';

        if (!is_null($limit)) {
            $limitStr = 'LIMIT ' . intval($limit);
        }

        return $limitStr;
    }

    public static function delete($where)
    {
        global $wpdb;
        self::_init();

        return $wpdb->delete(self::$table, $where);
    }

    public static function getUserKeys($userId)
    {
        return wpm_array_pluck(self::findByUserId($userId, ['key']), 'key');
    }

    public static function getUserTermIds($userId)
    {
        return wpm_array_pluck(self::findByUserId($userId, ['term_id']), 'term_id');
    }

    public static function getUserByTermKey($key)
    {
        $userId = wpm_array_get(self::findOne(['key' => $key], ['user_id']), 'user_id');

        return $userId ? get_userdata($userId) : null;
    }

    public static function getTermIdByKey($key)
    {
        return wpm_array_get(MBLTermKeysQuery::findOne(['key' => $key], ['term_id']), 'term_id');
    }

    public static function findByUserId($userId, $fields = null, $order = null, $limit = null)
    {
        if ($fields !== null || $order !== null || $limit !== null) {
            return self::find(['user_id' => $userId], $fields, $order, $limit);
        }

        if (!isset(self::$userKeysCache[$userId])) {
            self::$userKeysCache[$userId] = self::find(['user_id' => $userId], $fields, $order, $limit);
        }

        return self::$userKeysCache[$userId];
    }

    public static function find($where = [], $fields = null, $order = null, $limit = null)
    {
        global $wpdb;
        self::_init();

        $table = self::$table;
        $select = self::getSelectFieldsString($fields);
        $whereClause = self::_performWhere($where);
        $order = self::getOrderString($order);
        $limit = self::getLimitString($limit);

        $query = "SELECT {$select} FROM {$table} {$whereClause} {$order} {$limit}";

        $result = $wpdb->get_results($query, ARRAY_A);

        if ($result) {
            $result = array_map([__CLASS__, '_datesFromSql'], $result);
        }

        if ($result && wpm_array_get($where, 'key_type') == 'wpm_term_keys' && (is_null($fields) || isset($fields['id']))) {
            $performedResult = [];

            foreach ($result as $row) {
                $performedResult[$row['id']] = $row;
            }

            $result = $performedResult;
        } elseif ($result && wpm_array_get($where, 'key_type') == 'wpm_keys_basket' && (is_null($fields) || isset($fields['key']))) {
            $performedResult = [];

            foreach ($result as $row) {
                $performedResult[$row['key']] = $row;
            }

            $result = $performedResult;
        }

        return $result;
    }

    public static function findWhere($where, $fields = null, $order = null, $limit = null)
    {
        global $wpdb;
        self::_init();

        $table = self::$table;
        $select = self::getSelectFieldsString($fields);
        $order = self::getOrderString($order);
        $limit = self::getLimitString($limit);

        $query = "SELECT {$select} FROM {$table} {$where} {$order} {$limit}";

        $result = $wpdb->get_results($query, ARRAY_A);

        if ($result) {
            $result = array_map([__CLASS__, '_datesFromSql'], $result);
        }

        if ($result && wpm_array_get($where, 'key_type') == 'wpm_term_keys' && (is_null($fields) || isset($fields['id']))) {
            $performedResult = [];

            foreach ($result as $row) {
                $performedResult[$row['id']] = $row;
            }

            $result = $performedResult;
        } elseif ($result && wpm_array_get($where, 'key_type') == 'wpm_keys_basket' && (is_null($fields) || isset($fields['key']))) {
            $performedResult = [];

            foreach ($result as $row) {
                $performedResult[$row['key']] = $row;
            }

            $result = $performedResult;
        }

        return $result;
    }

    public static function findOne($where = [], $fields = null, $order = null)
    {
        global $wpdb;
        self::_init();

        $table = self::$table;
        $fields = self::getSelectFieldsString($fields);
        $where = self::_performWhere($where);
        $order = self::getOrderString($order);
        $limit = self::getLimitString(1);

        $query = "SELECT {$fields} FROM {$table} {$where} {$order} {$limit}";

        return self::_datesFromSql($wpdb->get_row($query, ARRAY_A));
    }

    public static function getKeysCountByPeriods($termIds = [])
    {
        global $wpdb;
        self::_init();

        $table = self::$table;
        $ids = implode(',', $termIds);

        $where = "term_id IN ({$ids}) AND status='new' AND key_type='wpm_term_keys' AND sent=0 AND is_banned=0";
        $query = "SELECT term_id, duration, units, is_unlimited, COUNT(id) as nb FROM {$table} WHERE {$where} GROUP BY term_id, duration, units, is_unlimited";

        return $wpdb->get_results($query, ARRAY_A);
    }

    private static function _performWhere($where = [])
    {
        global $wpdb;
        self::_init();

        if (is_array($where)) {
            if (!array_key_exists('is_banned', $where)) {
                $where['is_banned'] = 0;
            } elseif ($where['is_banned'] === 'all') {
                unset($where['is_banned']);
            }

            $where = array_intersect_key($where, self::$fields);
            $whereConditions = [];

            foreach ($where as $field => $value) {
                $whereConditions[] = $wpdb->prepare("(`{$field}`=" . self::$fields[$field] . ")", $value);
            }

            if (count($whereConditions)) {
                $where = 'WHERE ' . implode(' AND ', $whereConditions);
            } else {
                $where = '';
            }
        }

        return $where;
    }

    public static function transformKeysToInfo($keys)
    {
        $info = [];

        if (is_array($keys)) {
            foreach ($keys as $key) {
                $info[] = self::transformKeyToInfo($key);
            }
        }

        return $info;
    }

    public static function transformKeyToInfo($key)
    {
        $info = null;

        if ($key && is_array($key)) {
            $info = [
                'term_id'    => $key['term_id'],
                'key_info'   => $key,
                'item_id'    => $key['id'],
                'is_deleted' => ($key['key_type'] == 'wpm_keys_basket'),
            ];
        }

        return $info;
    }


    public static function updateKey($key)
    {
        global $wpdb;
        self::_init();

        if (!wpm_array_get($key, 'user_id')) {
            $user = self::_searchUserByMetaKey($key['key']);
            if (!is_null($user) && $user->ID) {
                $key['user_id'] = $user->ID;
            }
        }
        if (!wpm_array_get($key, 'is_banned')) {
            if (!is_null(self::_searchUserByMetaKey($key['key'], 'user_banned_key'))) {
                $key['is_banned'] = 1;
            }
        }

        $key = self::_datesToSql($key);

        if (wpm_array_get($key, 'units') != 'days') {
            $key['units'] = 'months';
        }

        $fields = self::getFields();
        $key = array_intersect_key($key, $fields);
        $formats = array_intersect_key($fields, $key);

        $info = wpm_array_get($key, 'info');
        if (!is_null($info)) {
            $key['info'] = maybe_serialize($info);
        }

        $duration = wpm_array_get($key, 'duration', 0);
        if ($duration === null) {
            $key['duration'] = 0;
        }

        $id = wpm_array_get($key, 'id');
        ksort($key);
        ksort($formats);

        if (is_null($id)) {
            $check = self::findOne(['key' => $key['key']], ['id']);
            if ($check) {
                $id = $check['id'];
            }
        }

        if (is_null($id)) {
            $result = $wpdb->insert(self::$table, $key, $formats);
        } else {
            unset(
                $key['id'],
                $formats['id']
            );
            $result = $wpdb->update(self::$table, $key, ['id' => $id], $formats);
        }
        do_action('wpm_mbl_term_key_updated', $key);

        return $result;
    }

    public static function insertKey($key)
    {
        global $wpdb;
        self::_init();

        $key = self::_datesToSql($key);

        if (wpm_array_get($key, 'units') != 'days') {
            $key['units'] = 'months';
        }

        $fields = self::getFields();
        $key = array_intersect_key($key, $fields);
        $formats = array_intersect_key($fields, $key);

        $info = wpm_array_get($key, 'info');
        if (!is_null($info)) {
            $key['info'] = maybe_serialize($info);
        }

        $duration = wpm_array_get($key, 'duration', 0);
        if ($duration === null) {
            $key['duration'] = 0;
        }

        ksort($key);
        ksort($formats);

        return $wpdb->insert(self::$table, $key, $formats);
    }

    public static function update($data, $where)
    {
        global $wpdb;
        self::_init();

        $data = self::_datesToSql($data);
        $fields = self::getFields();
        $data = array_intersect_key($data, $fields);
        $formats = array_intersect_key($fields, $data);

        $info = wpm_array_get($data, 'info');
        if (!is_null($info)) {
            $data['info'] = maybe_serialize($info);
        }

        ksort($data);
        ksort($formats);

        $result = $wpdb->update(self::$table, $data, $where, $formats);
        do_action('wpm_mbl_term_keys_query_updated', $data, $where);

        return $result;
    }

    private static function _datesToSql($key)
    {
        if ($key) {
            foreach (self::$dates as $field) {
                $date = wpm_array_get($key, $field);
                if ($date && $date != '') {
                    $formattedDate = date("Y-m-d", strtotime($date));

                    $key[$field] = self::_checkDate($formattedDate)
                        ? $formattedDate
                        : self::_fixDate($date);
                }
            }
        }

        return $key;
    }

    private static function _fixDate($dateString)
    {
        if (!is_string($dateString)) {
            $dateString = '';
        }

        if (!preg_match('/(\d{2})\-(\d{2})\-(\d{4,})/', $dateString)) {
            $dateString = '';
        }

        $dateParts = explode('-', $dateString, 3);
        $year = max(min(intval(wpm_array_get($dateParts, 2, intval(date('Y')) + 10)), intval(date('Y')) + 10), intval(date('Y')) - 10);
        $month = max(min(intval(wpm_array_get($dateParts, 1, 1)), 12), 1);
        $day = max(min(intval(wpm_array_get($dateParts, 0, 1)), 31), 1);

        $date = sprintf("%04d-%02d-%02d", $year, $month, $day);

        return $date;
    }

    private static function _checkDate($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);

        if (!$d || $d->format($format) !== $date) {
            return false;
        }

        if (intval($d->format('Y')) <= 1970) {
            return false;
        }

        return true;
    }


    private static function _datesFromSql($key)
    {
        if ($key) {
            foreach (self::$dates as $field) {
                $date = wpm_array_get($key, $field);
                if ($date && $date != '') {
                    $key[$field] = date('d-m-Y', strtotime($date));
                }
            }
        }

        return $key;
    }

    private static function _searchUserByMetaKey($key, $metaKey = 'user_key')
    {
        $value = ':"' . $key . '";';
        $uq = new WP_User_Query([
            'meta_key'     => $metaKey,
            'meta_value'   => $value,
            'meta_compare' => 'LIKE',
        ]);
        $user = $uq->get_results();

        return count($user) ? $user[0] : null;
    }

    public static function moveKeys($termFrom, $durationFrom, $unitsFrom, $termTo, $durationTo, $unitsTo, $isUnlimitedFrom = 0, $isUnlimitedTo = 0)
    {
        global $wpdb;
        self::_init();

        $termKeysTable = self::$table;

        $newKeys = "status='new' AND key_type='wpm_term_keys' AND sent=0 AND is_banned=0";

        if($isUnlimitedFrom) {
            $where = "`term_id`={$termFrom} AND `is_unlimited`=1 AND {$newKeys}";
        } else {
            $where = "`term_id`={$termFrom} AND `duration`={$durationFrom} AND `units`='{$unitsFrom}' AND `is_unlimited`=0 AND {$newKeys}";
        }

        if($isUnlimitedTo) {
            $mysql = "UPDATE {$termKeysTable} SET `term_id`={$termTo}, `is_unlimited`=1, `units`='{$unitsTo}' WHERE {$where}";
        } else {
            $mysql = "UPDATE {$termKeysTable} SET `term_id`={$termTo}, `duration`={$durationTo}, `units`='{$unitsTo}', `is_unlimited`=0 WHERE {$where}";
        }


        return $wpdb->query($mysql);
    }

    public static function migrate()
    {
        global $wpdb;
        self::_init();

        if (!get_option('wpm_term_key_migrate_0797_schema')) {
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

            $termKeysTable = self::$table;

            $sqlTermKeysTable = "CREATE TABLE IF NOT EXISTS {$termKeysTable} (
              `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
              `term_id` BIGINT(11) DEFAULT NULL,
              `user_id` BIGINT(11) DEFAULT NULL,
              `key` VARCHAR(255) NOT NULL,
              `key_type` ENUM('wpm_term_keys', 'wpm_keys_basket') DEFAULT 'wpm_term_keys',
              `status` ENUM('new', 'used', 'expired') DEFAULT 'new',
              `sent` TINYINT(1) DEFAULT 0,
              `is_banned` TINYINT(1) DEFAULT 0,
              `duration` BIGINT(11) NOT NULL,
              `units` VARCHAR(50) NOT NULL DEFAULT 'months',
              `date_start` DATE DEFAULT '0000-00-00' NOT NULL,
              `date_end` DATE DEFAULT '0000-00-00' NOT NULL,
              `date_registered` DATE,
              `info` LONGTEXT,
              `created_at` TIMESTAMP,
              `is_unlimited` TINYINT(1) NOT NULL DEFAULT 0,
              PRIMARY KEY (`id`),
              INDEX `memberlux_term_keys_key_idx` (`key`),
              INDEX `memberlux_term_keys_key_type_idx` (`key_type`),
              INDEX `memberlux_term_keys_date_start_idx` (`date_start`),
              INDEX `memberlux_term_keys_date_end_idx` (`date_end`),
              INDEX `memberlux_term_keys_duration_idx` (`duration`),
              INDEX `memberlux_term_keys_user_id_idx` (`user_id`),
              INDEX `memberlux_term_keys_term_id_idx` (`term_id`),
              INDEX `memberlux_term_keys_status_idx` (`status`),
              INDEX `memberlux_term_keys_is_banned_idx` (`is_banned`)
                )
                DEFAULT CHARACTER SET utf8
                DEFAULT COLLATE utf8_unicode_ci
                ENGINE=InnoDB;";

            $wpdb->query($sqlTermKeysTable);
            update_option("memberlux_db_version", self::NEW_SCHEMA_VERSION);

            $optionsTable = $wpdb->prefix . 'options';

            $wpdb->query("UPDATE {$optionsTable} SET autoload='no' WHERE option_name LIKE 'wpm_term_keys%' OR option_name LIKE 'wpm_keys_basket%'");

            update_option('wpm_term_key_migrate_0797_schema', true);
        }
    }

    public static function dropTable()
    {
        global $wpdb;
        self::_init();

        delete_option('wpm_term_key_migrate_0797_schema');
        delete_option('wpm_term_key_migrate_073_stats');

        $termKeysTable = self::$table;

        $mysql = "DROP TABLE IF EXISTS {$termKeysTable}";

        $wpdb->query($mysql);
    }
}
