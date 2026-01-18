<?php

class WPMBLMailAdapter extends BaseMBLMailAdapter implements IMBLMailAdapter
{
    private static $senderNameStatic = '';
    private static $senderEmailStatic = '';

    public function isAvailable()
    {
        return true;
    }

    public function send($recipient, $message)
    {
        register_shutdown_function(['WPMBLMailAdapter', 'fallback']);

        @ini_set('memory_limit', '4096M');
        @define('WP_MEMORY_LIMIT', '4096M');
        @define('WP_MAX_MEMORY_LIMIT', '4096M');

        self::$senderNameStatic = $this->getSenderName();
        self::$senderEmailStatic = $this->getSenderEmail();

        add_filter('wp_mail_content_type', [$this, 'getContentType']);
        add_action('phpmailer_init', ['WPMBLMailAdapter', 'addReplyTo'], 100);

        do_action('mbl_send_mail', $recipient, $this->getSubject(), $message, $this->getAttachments());

        $header = 'Content-Type: ' . $this->getContentType() . "\r\n";

        $result = wp_mail($recipient, $this->getSubject(), $message, $header, $this->getAttachments());

        remove_action('phpmailer_init', ['WPMBLMailAdapter', 'addReplyTo']);
        remove_filter('wp_mail_content_type', [$this, 'getContentType']);

        return $result;
    }

    public function getContentType()
    {
        return 'text/html';
    }

    public static function fallback()
    {
        $error = error_get_last();
        if ($error['type'] === E_ERROR) {
            wpm_mail_write_log(wpm_array_get($error, 'message'));
        }
    }

    /**
     * @param $phpmailer PHPMailer
     *
     * @throws phpmailerException
     */
    public static function addReplyTo($phpmailer)
    {
        /**
         * @var PHPMailer\PHPMailer\PHPMailer $phpmailer
         */
        global $phpmailer;
        if (!empty($phpmailer->From) && preg_match('/no-reply@/', self::$senderEmailStatic) === 1) {
            $senderEmail = $phpmailer->From;
        } else {
            $senderEmail = self::$senderEmailStatic;
        }

        $phpmailer->addReplyTo($senderEmail, self::$senderNameStatic);

        if ($phpmailer->FromName == 'WordPress') {
            $phpmailer->setFrom(self::$senderEmailStatic, self::$senderNameStatic);
        }
    }
}
