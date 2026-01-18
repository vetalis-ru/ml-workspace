<?php
add_action(
    'edited_wpm-levels',
    function ($term_id) {
        global $wpdb;
        $mailing_list = $wpdb->get_blog_prefix() . 'memberlux_mailing_list';
        $mailing = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM $mailing_list WHERE term_id = %d", [$term_id]), ARRAY_A
        );
        if (!isset($_POST['mblar']) && !$mailing) {
            return;
        }
        if (!isset($_POST['mblar'])) {
            $toSave = [
                'is_on' => 0,
            ];
            $wpdb->update(
                "{$wpdb->prefix}memberlux_user_mailing_list",
                ['status' => 'disabled'],
                ['term_id' => $term_id, 'mailing_datetime' => $mailing['datetime'], 'status' => 'processing']
            );
        } else {
            $l_data = $_POST['mblar']['list'];
            $toSave = [
                'is_on' => (int)$l_data['is_on'],
                'template_id' => (int)$l_data['template_id'],
                'unsubscribe' => isset($l_data['unsubscribe']) ? (int)$l_data['unsubscribe'] : 0,
            ];
        }
        if (!$mailing) {
            $wpdb->insert(
                $mailing_list,
                array_merge(
                    $toSave,
                    [
                        'term_id' => $term_id,
                        'datetime' => (new DateTime('now', wp_timezone()))->format('Y-m-d H:i:s')
                    ]
                )
            );
        } else {
            $mailing = [
                'term_id' => (int)$mailing['term_id'],
                'is_on' => (int)$mailing['is_on'],
                'datetime' => $mailing['is_on'],
                'template_id' => (int)$mailing['template_id'],
                'unsubscribe' => (int)$mailing['unsubscribe'],
            ];
            if (!$mailing['is_on'] && $toSave['is_on']) {
                $toSave['datetime'] = (new DateTime('now', wp_timezone()))->format('Y-m-d H:i:s');
            }
            $wpdb->update($mailing_list, $toSave, ['term_id' => $term_id]);
        }
        if (isset($_POST['mblar'])) {
            $table_name = $wpdb->get_blog_prefix() . 'memberlux_mailing';
            $mails = array_map(
                function ($mail) use ($term_id) {
                    return array_merge($mail, [
                        'term_id' => (int)$term_id,
                        'mail_order' => (int)$mail['mail_order'],
                        'days' => (int)$mail['days'],
                        'hour' => (int)$mail['hour'],
                        'minute' => (int)$mail['minute'],
                    ]);
                },
                $_POST['mblar']['mail']
            );
            foreach ($mails as $mail) {
                $id = $wpdb->get_var(
                    $wpdb->prepare(
                        "SELECT id FROM $table_name WHERE term_id = %d AND mail_order = %d",
                        $term_id, $mail['mail_order']
                    )
                );
                if (!$id) {
                    $wpdb->insert($table_name, $mail);
                } else {
                    $wpdb->update($table_name, $mail, ['id' => (int)$id]);
                }
            }
            $toDelete = array_map(
                fn($id) => (int)$id,
                $wpdb->get_col($wpdb->prepare(
                    "SELECT id FROM $table_name WHERE term_id = %d AND mail_order > %d",
                    [$term_id, $mails[count($mails) - 1]['mail_order']]
                ))
            );
            foreach ($toDelete as $id) {
                $wpdb->delete($table_name, ['id' => $id]);
            }
        }
    },
    10,
    1
);