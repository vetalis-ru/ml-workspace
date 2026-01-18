<?php

namespace Mbl\Api;

use WP_User;

class Customer
{
    private string $email;

    /**
     * @param string $email
     */
    public function __construct(string $email)
    {
        $this->email = $email;
    }

    public function wpUser($data = [])
    {
        $user = get_user_by('email', $this->email);

        if (!$user) {
            $login = wpm_email_to_login($this->email);
            $pass = wp_generate_password();
            $user_data = [
                'user_login' => $login,
                'user_pass' => $pass,
                'user_email' => $this->email,
                'role' => 'customer'
            ];
            $user_id = wp_insert_user($user_data);

            $user = new WP_User($user_id);
        }

        $update = [];

        if (!empty($data['first_name'])) {
            $update['first_name'] = $data['first_name'];
        }
        if (!empty($data['last_name'])) {
            $update['last_name'] = $data['last_name'];
        }

        if (!empty($update)) {
            $update['ID'] = $user->ID;
            wp_update_user($update);
        }

        $ad_data = [
            'surname',
            'phone',
            'custom1',
            'custom2',
            'custom3',
            'custom4',
            'custom5',
            'custom6',
            'custom7',
            'custom8',
            'custom9',
            'custom10',
            'custom1textarea',
        ];

        foreach ($ad_data as $key) {
            if (!empty($data[$key])) {
                update_user_meta($user->ID, $key, $data[$key]);
            }
        }

        return $user;
    }
}
