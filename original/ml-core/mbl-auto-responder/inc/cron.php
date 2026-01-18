<?php
add_filter('cron_schedules', 'mblar_cron_interval');
function mblar_cron_interval($schedules)
{
    $schedules['mblar_interval'] = array(
        'interval' => MBLAR_CRON_INTERVAL,
        'display' => 'Интервал проверки рассылки'
    );
    return $schedules;
}

/**
 * @throws Exception
 */
function mblar_calc_time($next_order, $mailing, $datetime_start): DateTime
{
    $months = ['', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
    $datetime = new DateTime($datetime_start, wp_timezone());
    foreach ($mailing as $order => $item) {
        if ($order > $next_order) break;
        $prev = new DateTime($datetime->format('Y-m-d H:i:s'), wp_timezone());
        if ($item['interval_type'] === 'interval') {
            $datetime->modify("+ {$item['days']} day + {$item['hour']} hour + {$item['minute']} minute");
        } else {
            $prev_day = (int)$prev->format('N');
            $datetime->setTime($item['hour'], $item['minute']);
            if (($prev_day === $item['days'] && $prev > $datetime) || $prev_day !== $item['days']) {
                $datetime->modify("next {$months[$item['days']]}");
                $datetime->setTime($item['hour'], $item['minute']);
            }
        }
    }
    return $datetime;
}

add_action('mbl_auto_responder_event', 'mbl_auto_responder_cron');
function mbl_auto_responder_cron() {
    if (empty($_GET['doing_wp_cron'])) {
        $doing_wp_cron = $GLOBALS['doing_wp_cron'];
    } else {
        $doing_wp_cron = $_GET['doing_wp_cron'];
    }
    $now = DateTime::createFromFormat('U.u', (float)$doing_wp_cron, wp_timezone());
    global $wpdb;
    $sql = "       
        SELECT uml.user_id, u.user_email AS email, uml.term_id, uml.datetime_start, status, ml.template_id,
            uml.mailing_datetime, COALESCE(MAX(mr.mail_order), -1) AS last_mail_order, MAX(mm.mail_order) AS max_order
        FROM {$wpdb->prefix}memberlux_user_mailing_list AS uml
        JOIN $wpdb->users AS u ON u.ID = uml.user_id
        JOIN {$wpdb->prefix}memberlux_mailing_list AS ml ON ml.term_id = uml.term_id
        JOIN {$wpdb->prefix}memberlux_mailing AS mm ON mm.term_id = uml.term_id
        LEFT JOIN {$wpdb->prefix}memberlux_mailing_results AS mr
            ON mr.user_id = uml.user_id AND mr.term_id = uml.term_id AND mr.mailing_datetime = uml.mailing_datetime
        WHERE uml.status = 'processing'
        GROUP BY uml.user_id, uml.term_id, uml.mailing_datetime;
        ";
    $items = array_map(
        function ($item) {
            return array_merge($item,
                [
                    'user_id' => (int)$item['user_id'],
                    'term_id' => (int)$item['term_id'],
                    'last_mail_order' => (int)$item['last_mail_order'],
                    'max_order' => (int)$item['max_order'],
                    'template_id' => (int)$item['template_id'],
                ]);
        },
        $wpdb->get_results($sql, ARRAY_A)
    );

    $term_ids = implode(',', array_values(array_unique(array_map(fn($i) => $i['term_id'], $items))));
    $mailings = array_reduce(
        $wpdb->get_results(
            "SELECT * FROM {$wpdb->prefix}memberlux_mailing
                WHERE term_id IN ($term_ids)
                ORDER BY term_id, mail_order",
            ARRAY_A
        ),
        function ($carry, $item) {
            $carry[$item['term_id']][$item['mail_order']] = array_merge($item, [
                'id' => (int)$item['id'],
                'term_id' => (int)$item['term_id'],
                'mail_order' => (int)$item['mail_order'],
                'days' => (int)$item['days'],
                'hour' => (int)$item['hour'],
                'minute' => (int)$item['minute'],
                'message' => wp_unslash($item['message']),
            ]);
            return $carry;
        }
    );
    $template_ids = implode(
        ',',
        array_values(array_unique(array_map(fn($i) => $i['template_id'], $items)))
    );
    $templates = array_reduce(
        $wpdb->get_results(
            "SELECT id, data FROM {$wpdb->prefix}memberlux_mailing_templates WHERE id IN ($template_ids)",
            ARRAY_A
        ),
        function ($carry, $item) {
            $carry[(int)$item['id']] = $item['data'];
            return $carry;
        }
    );
    foreach ($items as $item) {
        $last_order = $item['last_mail_order'];
        $max_order = $item['max_order'];
        $next_order = $last_order + 1;
        if ($next_order > $max_order) {
            $wpdb->update(
                "{$wpdb->prefix}memberlux_user_mailing_list",
                ['status' => 'finish'],
                [
                    'user_id' => $item['user_id'],
                    'term_id' => $item['term_id'],
                    'mailing_datetime' => $item['mailing_datetime']
                ]
            );
        } else {
            $mailing = $mailings[$item['term_id']];
            ob_start();
            $next_datetime = mblar_calc_time($next_order, $mailing, $item['datetime_start']);
            if ($now >= $next_datetime) {
                $mailer = new MBLMailer();
                $mail = $mailing[$next_order];
                $mailer->addRecipient($item['email'])
                    ->setSubject($mail['subject']);
                $mailing_info = [
                    'user_id' => $item['user_id'],
                    'term_id' => $item['term_id'],
                    'mailing_datetime' => $item['mailing_datetime']
                ];
                $message_text = apply_filters('wpm_user_mail_shortcode_replacement', $mail['message'], $item['user_id'], []);
                if (isset($templates[$item['template_id']])) {
                    $message = mblar_render_email($templates[$item['template_id']], $message_text, $mailing_info);
                } else {
                    $message = mblar_render_email_without_template($message_text, $mailing_info);
                }
                $mailer->setMessageRaw($message);
                $mailer->send();
                $wpdb->insert(
                    "{$wpdb->prefix}memberlux_mailing_results",
                    [
                        'user_id' => $item['user_id'],
                        'datetime' => date('Y-m-d H:i:s'),
                        'term_id' => $item['term_id'],
                        'm_id' => $mail['id'],
                        'mail_order' => $next_order,
                        'mailing_datetime' => $item['mailing_datetime'],
                        'subject' => $mail['subject'],
                        'message' => $mail['message'],
                    ]
                );
                if ($next_order === $max_order) {
                    $wpdb->update(
                        "{$wpdb->prefix}memberlux_user_mailing_list",
                        ['status' => 'finish'],
                        [
                            'user_id' => $item['user_id'],
                            'term_id' => $item['term_id'],
                            'mailing_datetime' => $item['mailing_datetime']
                        ]
                    );
                }
            }
        }
        if (_get_cron_lock() !== $doing_wp_cron) {
            break;
        }
    }
}
