<?php

namespace Mbl\AutoResponder;

use Exception;

class UnsubscribeUrlCrypt
{
    public function encode($user_id, $data = null): string
    {
        if (strlen((string)$user_id) < 10) {
            $str = (string)$user_id;
            $end = 9 - strlen((string)$user_id);
            if ($end !== 0) {
                foreach (range(1, $end) as $s) {
                    $str .= '0';
                }

            }
            $str .= "$end";
            $num = base_convert((int)$str, 10, 34) . 'z';
        } else {
            $num = base_convert($user_id, 10, 34) . 'y';
        }
        if (!empty($data)) {
            $num .= base_convert($data['term_id'] + 123, 10, 34)
                . '_' . base_convert(strtotime($data['mailing_datetime']), 10, 34);
        } else {
            $num .= base_convert($user_id, 10, 34);
        }

        return $num;
    }

    /**
     * @throws Exception
     */
    public function decode($url, bool $all = true): array
    {
        if (stripos($url, 'z') !== false) {
            $delimiter = 'z';
            $h0 = true;
        } elseif(stripos($url, 'y') !== false) {
            $delimiter = 'y';
            $h0 = false;
        } else {
            throw new Exception('Incorrect url');
        }
        $n = explode($delimiter, $url);
        if ($h0) {
            $number = base_convert($n[0], 34, 10);
            $count = (int)substr($number, -1, 1);
            $id = substr($number, 0, strlen($number) - $count - 1);
        } else {
            $id = base_convert($n[0], 34, 10);
        }

        if ($all) {
            return [(int)$id, null, null];
        } else {
            $t = explode('_', $n[1]);
            return [
                (int)$id,
                (int)base_convert($t[0], 34, 10) - 123,
                date('Y-m-d H:i:s', (int)base_convert($t[1], 34, 10))
            ];
        }
    }
}