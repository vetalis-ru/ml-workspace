<?php

namespace Mbl\AutoResponder;

use DateTime;
use Exception;

class UserSubscriptions
{
    private int $user_id;
    private string $table;

    public function __construct($user_id)
    {
        $this->user_id = $user_id;
        $this->table = $GLOBALS['wpdb']->get_blog_prefix() . 'memberlux_user_mailing_list';
    }

    /**
     * @return void
     */
    public function unsubscribe()
    {
        global $wpdb;
        $fields = ['status' => 'unsubscribe'];
        $where = [
            'user_id' => $this->user_id,
            'status' => 'processing'
        ];
        $wpdb->update($this->table, $fields, $where);
    }

    public function unsubscribeMailing($term_id, $mailing_datetime = '')
    {
        global $wpdb;
        $fields = ['status' => 'unsubscribe'];
        $where = [
            'user_id' => $this->user_id,
            'status' => 'processing',
            'term_id' => $term_id,
        ];
        if (!empty($mailing_datetime)) {
            $where['mailing_datetime'] = $mailing_datetime;
        }
        $wpdb->update($this->table, $fields, $where);
    }

    /**
     * @throws SubscriptionNotExist
     * @throws MailingListNotExist
     */
    public function subscription(MailingList $mailingList): array
    {
        global $wpdb;
        $mailing = $mailingList->toArray();
        $term_id = $mailing['term_id'];
        $mailing_datetime = $mailing['datetime'];
        $sql = $wpdb->prepare(
            "SELECT mailing_datetime FROM $this->table 
                        WHERE user_id = %d AND term_id = %d AND mailing_datetime = %s",
            [$this->user_id, $term_id, $mailing_datetime]
        );
        $row = $wpdb->get_row($sql, ARRAY_A);
        if (is_null($row)) {
            throw new SubscriptionNotExist(
                "User $this->user_id subscription wpm level $term_id and datetime $mailing_datetime not exist"
            );
        }
        return [
            'user_id' => absint($row['user_id']),
            'term_id ' => absint($row['term_id']),
            'mailing_datetime' => $row['mailing_datetime'],
            'datetime_start' => $row['datetime_start'],
            'status' => $row['status'],
        ];
    }

    /**
     * @throws MailingListNotExist
     * @throws MailingListOff
     * @throws SubscriptionAlreadyExists
     */
    public function subscribe(MailingList $mailingList)
    {
        global $wpdb;
        $mailing = $mailingList->toArray();
        if (!$mailing['is_on']) {
            throw new MailingListOff("Mailing List wpm level {$mailing['term_id']} is off");
        }
        try {
            $subscription = $this->subscription($mailingList);
            throw new SubscriptionAlreadyExists(
                "Subscription user $this->user_id wpm level {$subscription['term_id']} "
                . "{$subscription['datetime']}  already exists"
            );
        } catch (SubscriptionNotExist $e) {
        }
        if ($mailing['unsubscribe']) {
            $wpdb->update(
                $this->table,
                ['status' => 'disabled'],
                ['user_id' => $this->user_id, 'status' => 'processing']
            );
        }
        $wpdb->insert(
            $this->table,
            [
                'user_id' => $this->user_id,
                'term_id' => $mailing['term_id'],
                'mailing_datetime' => $mailing['datetime'],
                'datetime_start' => (new DateTime('now', wp_timezone()))->format('Y-m-d H:i:s'),
                'status' => 'processing',
            ],
        );
    }
}