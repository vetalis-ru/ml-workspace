<?php

namespace Mbl\AutoResponder;

use Exception;

class UnsubscribeUrl
{
    private UnsubscribeUrlCrypt $crypt;

    /**
     * @param UnsubscribeUrlCrypt $crypt
     */
    public function __construct(UnsubscribeUrlCrypt $crypt)
    {
        $this->crypt = $crypt;
    }

    public function url($user_id, $data = null): string
    {
        $type = !empty($data) ? 's' : 'a';
        return home_url() . "/mblar-unsubscribe/$type/" . $this->crypt->encode($user_id, $data);
    }

    public function formUrl($user_id, $term_id, $mailing_datetime): string
    {
        $query = $this->crypt->encode($user_id, [
            'term_id' => $term_id, 'mailing_datetime' => $mailing_datetime
        ]);
        return (new Options())->byId('unsubscribe_form_url') . '?' . http_build_query(['q' => $query]);
    }

    /**
     * @throws Exception
     */
    public function parseUrl(): array
    {
        return $this->crypt->decode(get_query_var('message_id'), get_query_var('s_type') === 'a');
    }
}