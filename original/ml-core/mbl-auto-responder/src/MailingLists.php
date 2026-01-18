<?php

namespace Mbl\AutoResponder;

use WP_Term;

class MailingLists
{
    private string $tableName;

    public function __construct()
    {
        $this->tableName = $GLOBALS['wpdb']->get_blog_prefix() . 'memberlux_mailing_list';
    }
}