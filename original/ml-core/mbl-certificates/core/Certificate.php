<?php


class Certificate
{
    const TABLE_NAME = 'memberlux_certificate';
    public int $id = 0;
    public int $user_id = 0;
    public int $certificate_template_id = 0;
    public int $wpmlevel_id = 0;
    public string $certificate_name = '';
    public string $graduate_first_name = '';
    public string $graduate_last_name = '';
    public string $graduate_surname = '';
    public string $date_issue = '';
    public string $series = '';
    public string $number = '';
    public string $responsible_person = '';
    public string $create_date = '';
    public string $course_name = '';
    public array $custom_fields = [];

    public function __construct(
        int    $id,
        string $certificate_name,
        int    $user_id,
        int    $certificate_template_id,
        int    $wpmlevel_id,
        string $graduate_first_name,
        string $graduate_last_name,
        string $graduate_surname,
        string $date_issue,
        string $series,
        string $number,
        string $responsible_person,
        string $create_date,
        string $course_name,
        array $custom_fields = []
    )
    {
        $this->id = $id;
        $this->certificate_name = $certificate_name;
        $this->user_id = $user_id;
        $this->certificate_template_id = $certificate_template_id;
        $this->wpmlevel_id = $wpmlevel_id;
        $this->graduate_first_name = $graduate_first_name;
        $this->graduate_last_name = $graduate_last_name;
        $this->graduate_surname = $graduate_surname;
        $this->date_issue = $date_issue;
        $this->series = $series;
        $this->number = $number;
        $this->responsible_person = $responsible_person;
        $this->create_date = $create_date;
        $this->course_name = $course_name;
        $this->custom_fields = $custom_fields;
    }

    public function getAdditionFields(): array
    {
        return $this->custom_fields;
    }

    public function getCertificateName(): string
    {
        return $this->certificate_name;
    }

    public function getDateIssue(): string
    {
        return $this->date_issue;
    }

    public static function create(
        int    $user_id,
        string $certificate_name,
        int    $certificate_template_id,
        int    $wpmLevel_id,
        string $graduate_first_name,
        string $graduate_last_name,
        string $graduate_surname,
        string $date_issue,
        string $series,
        string $responsible_person,
        string $create_date,
        string $course_name
    ): int
    {
        global $wpdb;
        $customFields = [];
        for ( $i = 1; $i <= mblc_certificate_custom_fields_count(); $i++ ) {
            $field_value = get_term_meta($wpmLevel_id, "_mblc_field$i", true) ?: '';
            if (!empty($field_value)) {
                $customFields["field$i"] = $field_value;
            }
        }
        $number = Certificate::generateCertificateNumber($user_id);
        $sql = "INSERT INTO `$wpdb->prefix" . self::TABLE_NAME . "`
         (`certificate_name`, `user_id`, `certificate_template_id`, `wpmlevel_id`, `graduate_first_name`,
          `graduate_last_name`, `graduate_surname`, `date_issue`, `series`, `number`, `responsible_person`,
          `create_date`, `course_name`, `custom_fields`)
          VALUES (%s, %d, %d, %d, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)";
        $prepare = $wpdb->prepare($sql, [
            $certificate_name,
            $user_id,
            $certificate_template_id,
            $wpmLevel_id,
            $graduate_first_name,
            $graduate_last_name,
            $graduate_surname,
            $date_issue,
            $series,
            $number,
            $responsible_person,
            $create_date,
            $course_name,
            serialize($customFields)
        ]);
        $wpdb->query($prepare);
        $cert_id = $wpdb->insert_id;
        do_action("mbl_certificate_issued", $user_id, $cert_id);

        return $cert_id;
    }

    public static function update(int $certificate_id, array $fields = [])
    {
        global $wpdb;
        $table_name = self::tableName();
        $setData = Data::dataToString(self::cleanKey($fields));
        $wpdb->query("UPDATE $table_name SET $setData WHERE `certificate_id` = $certificate_id");
    }

    public static function delete(int $certificate_id)
    {
        global $wpdb;

        return $wpdb->delete(self::tableName(), ['certificate_id' => $certificate_id]);
    }

    public static function tableName(): string
    {
        global $wpdb;
        return $wpdb->prefix . self::TABLE_NAME;
    }

    /**
     * @throws Exception
     */
    public static function getCertificate($id): Certificate
    {
        global $wpdb;
        $sql = $wpdb->prepare("SELECT * FROM " . self::tableName() . " WHERE certificate_id = %d", $id);
        $data = $wpdb->get_row($sql);
        if (is_null($data)) throw new Exception('Сертификат не найден');
        $custom_fields = maybe_unserialize($data->custom_fields);
        return new Certificate(
            (int)$data->certificate_id,
            $data->certificate_name ?: 'Название курса',
            (int)$data->user_id,
            (int)$data->certificate_template_id,
            (int)$data->wpmlevel_id,
            $data->graduate_first_name ?: '',
            $data->graduate_last_name ?: '',
            $data->graduate_surname ?: '',
            $data->date_issue,
            $data->series ?: '',
            $data->number,
            $data->responsible_person,
            $data->create_date,
            $data->course_name ?: '',
            is_array($custom_fields) ? $custom_fields : []
        );
    }

    public static function cleanKey(array $fields): array
    {
        global $wpdb;
        $tableName = self::tableName();
        $keys = $wpdb->get_col("SHOW COLUMNS FROM $tableName");
        return array_filter($fields, function ($key) use ($keys) {
            return in_array($key, $keys);
        }, ARRAY_FILTER_USE_KEY);
    }

    public static function generateCertificateNumber(int $user_id): string
    {
        return self::numberFormat($user_id, 5);
    }

    public static function numberFormat($digit, $width): string
    {
        while (strlen($digit) < $width)
            $digit = '0' . $digit;
        return $digit;
    }

    /**
     * @throws Exception
     */
    public static function getCustomerCertificates($userId): array
    {
        global $wpdb;
        $table_name = self::tableName();
        $sql = $wpdb->prepare("SELECT certificate_id FROM $table_name WHERE user_id = %d", $userId);
        foreach ($wpdb->get_col($sql) as $id) {
            $certificates[] = Certificate::getCertificate($id);
        }

        return $certificates ?? [];
    }

    /**
     * @throws Exception
     */
    public static function getCertificatesByWpmLevelId(int $wpmLevelId, $flag = 'object'): array
    {
        global $wpdb;
        $tableName = $wpdb->prefix . self::TABLE_NAME;
        $certificateIds = $wpdb->get_col(
            $wpdb->prepare("SELECT `certificate_id` FROM $tableName WHERE `wpmlevel_id` = %d", $wpmLevelId)
        );
        if ($flag === 'ids') {
            $result = array_map('intval', $certificateIds);
        } else {
            $result = array_map(fn($id) => self::getCertificate($id), $certificateIds);
        }

        return $result;
    }

    public static function query($params): array
    {
        global $wpdb;
        $tableName = self::tableName();
        $perPage = $params['per_page'] ?? 10;
        $orderBy = $params['order_by'] ?? 'create_date';
        $order = isset($params['order']) ? strtoupper($params['order']) : 'DESC';
        $filter = isset($params['filter']) ? (array)$params['filter'] : [];
        $email = $params['email'] ?? "";

        $whereConditions = [];
        foreach ($filter as $key => $value) {
            if ((is_string($value) || is_numeric($value)) && !empty($value)) {
                $whereConditions[] = " `$key` = " . esc_sql($value);
            } elseif (is_array($value)) {
                if (!empty($value['from']) && !empty($value['to'])) {
                    $whereConditions[] = " ( `$key` BETWEEN '" . esc_sql($value['from']) . "' AND '" . esc_sql($value['to']) . "')";
                } elseif (!empty($value['from'])) {
                    $whereConditions[] = " `$key` = '" . esc_sql($value['from']) . "'";
                } elseif (!empty($value['to'])) {
                    $whereConditions[] = " `$key` = '" . esc_sql($value['to']) . "'";
                }
            } elseif (is_object($value)) {
                if (!empty($value->from) && !empty($value->to)) {
                    $whereConditions[] = " ( `$key` BETWEEN '" . esc_sql($value->from) . "' AND '" . esc_sql($value->to) . "')";
                } elseif (!empty($value->from)) {
                    $whereConditions[] = " `$key` = '" . esc_sql($value->from) . "'";
                } elseif (!empty($value->to)) {
                    $whereConditions[] = " `$key` = '" . esc_sql($value->to) . "'";
                }
            }
        }

        //construct query
        $count_query = 'SELECT COUNT(*)';
        $base_query = "SELECT certificate.*, users.`user_login`, users.`user_email`";
        $inner_query = " FROM $tableName AS certificate
            LEFT JOIN $wpdb->users AS users ON certificate.user_id = users.ID";
        if (!empty($email)) {
            $whereConditions = [];
            $whereConditions[] = " users.`user_email` LIKE '%$email%'";
        }
        $where = empty($whereConditions) ? '' : ' WHERE' . implode(' AND', $whereConditions);
        if (in_array($orderBy, ['create_date', 'date_issue'])) {
            $order = " ORDER BY $orderBy $order, certificate_id DESC";
        } else {
            $order = " ORDER BY $orderBy $order";
        }
        $sql_count_query = $count_query . $inner_query . $where . $order;
        $total = $wpdb->get_var($sql_count_query);
        $totalPages = (int)ceil($total / $perPage);
        if (empty($params['page_num'])) {
            $pageNum = 1;
        } else {
            $pageNum = max(min($params['page_num'], $totalPages), 1);
        }
        $start = $perPage * ($pageNum - 1);
        $limit = " LIMIT {$start}, {$perPage}";
        $sql_query = $base_query . $inner_query . $where . $order . $limit;
        $result = $wpdb->get_results($sql_query);
        $wpmlevel_ids = array_unique(array_values(array_map(fn($c) => (int)$c->wpmlevel_id, $result)));
        $wpm_levels = (new MBLC_WpmLevels())->query(['include' => $wpmlevel_ids]);
        return [
            'result' => array_map(function ($r) use ($wpm_levels) {
                $wpm_level = current(array_filter($wpm_levels, fn($l) => $l->term_id === (int)$r->wpmlevel_id));
                $r->wpmlevel_name = $wpm_level->name;
                return $r;
            }, $result),
            'page_num' => $pageNum,
            'per_page' => $perPage,
            'total_pages' => $totalPages,
            'total' => intval($total),
            'sql' => $sql_query
        ];
    }

    /**
     * @throws Exception
     */
    public static function getGroupingCertificateByFIO(
        string $graduate_last_name,
        string $graduate_first_name,
        string $graduate_surname = ''
    ): array
    {
        $certificates = [];
        $members = Member::getMembersByFio(
            $graduate_last_name,
            $graduate_first_name,
            $graduate_surname
        );
        foreach ($members as $member) {
            $res = Certificate::getCustomerCertificates($member->ID);
            if (!empty($res)) {
                $certificates[$member->ID]['fio'] = trim("$member->last_name $member->first_name "
                    . $member->surname);
                $certificates[$member->ID]['certificates'] = $res;
            }
        }
        return $certificates;
    }

    public static function getCertificateBySeriesNumber(string $series, string $number): array
    {
        global $wpdb;
        $table_name = self::tableName();
        $ids = $wpdb->get_col(
            $wpdb->prepare(
                "SELECT `certificate_id` FROM $table_name WHERE `series` = %s AND `number` = %s",
                $series, $number
            )
        );

        return array_map(['Certificate', 'getCertificate'], $ids);
    }

    public function getFIO(): string
    {
        return "$this->graduate_last_name $this->graduate_first_name $this->graduate_surname";
    }

    public static function insertCertificate(array $params): int
    {
        global $wpdb;
        $tableName = $wpdb->prefix . self::TABLE_NAME;
        $sql = "INSERT INTO `$tableName`
         (
          `user_id`, `wpmlevel_id`, `certificate_name`, `certificate_template_id`,
          `graduate_first_name`, `graduate_last_name`, `graduate_surname`,
          `date_issue`, `series`, `number`, `responsible_person`,
          `create_date`, `course_name`)
          VALUES (
            %d, %d, %s, %d,
            %s, %s, %s,
            %s, %s, %s, %d,
            %s, %s
          )";
        $prepare = $wpdb->prepare($sql, [
            $params['user_id'], $params['wpmlevel_id'], $params['certificate_name'], $params['certificate_template_id'],
            $params['graduate_first_name'], $params['graduate_last_name'], $params['graduate_surname'],
            $params['date_issue'], $params['series'], $params['number'], $params['responsible_person'],
            $params['create_date'], $params['course_name'],
        ]);
        $wpdb->query($prepare);
        return $wpdb->insert_id;
    }

    public static function isCustomerCertificate(int $userId, int $certificateId): bool
    {
        global $wpdb;
        $table_name = self::tableName();
        $sql = $wpdb->prepare(
            "SELECT certificate_id FROM $table_name WHERE user_id = %d AND certificate_id = %d",
            $userId, $certificateId
        );
        return !empty($wpdb->get_var($sql));
    }
}
