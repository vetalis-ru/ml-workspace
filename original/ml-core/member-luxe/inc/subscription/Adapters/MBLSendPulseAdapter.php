<?php

class MBLSendPulseAdapter extends BaseMBLSubscriptionAdapter implements IMBLSubscriptionAdapter
{

    private $apiUrl = 'https://api.sendpulse.com';

    private $userIdSendPulse;
    private $secret;
    private $token;

    private $refreshToken = 0;

    protected $configKey = 'sendpulse';

    /**
     * Sendpulse API constructor
     *
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct();
        try {
            $userIdSendPulse = $this->getApiUserId();
            $secret = $this->getApiSecret();

            if (empty($userIdSendPulse) || empty($secret)) {
                throw new Exception('Empty ID or SECRET');
            }

            $this->userId = $userIdSendPulse;
            $this->secret = $secret;

            $hashName = md5($userIdSendPulse . '::' . $secret);


            if (empty($this->token) && !$this->getToken()) {
                throw new Exception('Could not connect to api, check your ID and SECRET');
            }
        } catch (Exception $e) {
            self::$error = $e->getMessage();
        }
    }

    /**
     * Get token and store it
     *
     * @return bool
     */
    private function getToken()
    {
        $data = array(
            'grant_type' => 'client_credentials',
            'client_id' => $this->userId,
            'client_secret' => $this->secret,
        );

        $requestResult = $this->sendRequest('oauth/access_token', 'POST', $data, false);

        if ($requestResult->http_code !== 200) {
            return false;
        }

        $this->refreshToken = 0;
        $this->token = $requestResult->data->access_token;

        $hashName = md5($this->userId . '::' . $this->secret);
        /** Save token to storage */

        return true;
    }

    /**
     * Form and send request to API service
     *
     * @param        $path
     * @param string $method
     * @param array $data
     * @param bool $useToken
     *
     * @return stdClass
     */
    protected function sendRequest($path, $method = 'GET', $data = array(), $useToken = true)
    {
        $url = $this->apiUrl . '/' . $path;
        $method = strtoupper($method);
        $curl = curl_init();

        if ($useToken && !empty($this->token)) {
            $headers = array('Authorization: Bearer ' . $this->token);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        }

        switch ($method) {
            case 'POST':
                curl_setopt($curl, CURLOPT_POST, count($data));
                curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
                break;
            case 'PUT':
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
                break;
            case 'DELETE':
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
                curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
                break;
            default:
                if (!empty($data)) {
                    $url .= '?' . http_build_query($data);
                }
        }

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 15);
        curl_setopt($curl, CURLOPT_TIMEOUT, 15);

        $response = curl_exec($curl);
        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $headerCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $responseBody = substr($response, $header_size);

        curl_close($curl);

        if ($headerCode === 401 && $this->refreshToken === 0) {
            ++$this->refreshToken;
            $this->getToken();
            $retval = $this->sendRequest($path, $method, $data);
        } else {
            $retval = new stdClass();
            $retval->data = json_decode($responseBody);
            $retval->http_code = $headerCode;
        }

        return $retval;
    }

    /**
     * Process results
     *
     * @param $data
     *
     * @return stdClass
     */
    protected function handleResult($data)
    {
        if (empty($data->data)) {
            $data->data = new stdClass();
        }
        if ($data->http_code !== 200) {
            $data->data->is_error = true;
            $data->data->http_code = $data->http_code;
        }

        return $data->data;
    }

    /**
     * Process errors
     *
     * @param null $customMessage
     *
     * @return stdClass
     */
    protected function handleError($customMessage = null)
    {
        $message = new stdClass();
        $message->is_error = true;
        if (null !== $customMessage) {
            $message->message = $customMessage;
        }

        return $message;
    }

    /**
     * Get list of address books
     *
     * @param null $limit
     * @param null $offset
     *
     * @return mixed
     */
    public function listAddressBooks($limit = null, $offset = null)
    {
        $data = array();
        if (null !== $limit) {
            $data['limit'] = $limit;
        }
        if (null !== $offset) {
            $data['offset'] = $offset;
        }

        $requestResult = $this->sendRequest('addressbooks', 'GET', $data);

        return $this->handleResult($requestResult);
    }

    /**
     * Add new emails to address book
     *
     * @param $bookID
     * @param $emails
     * @param $additionalParams
     *
     * @return stdClass
     */
    public function addEmails($bookID, $emails, $additionalParams = [])
    {
        if (empty($bookID) || empty($emails)) {
            return $this->handleError('Empty book id or emails');
        }

        $data = array(
            'emails' => json_encode($emails),
        );

        if ($additionalParams) {
            $data = array_merge($data, $additionalParams);
        }

        $requestResult = $this->sendRequest('addressbooks/' . $bookID . '/emails', 'POST', $data);

        return $this->handleResult($requestResult);
    }

    /**
     * Remove email addresses from book
     *
     * @param $bookID
     * @param $emails
     *
     * @return stdClass
     */
    public function removeEmails($bookID, $emails)
    {
        if (empty($bookID) || empty($emails)) {
            return $this->handleError('Empty book id or emails');
        }

        $data = array(
            'emails' => serialize($emails),
        );

        $requestResult = $this->sendRequest('addressbooks/' . $bookID . '/emails', 'DELETE', $data);

        return $this->handleResult($requestResult);
    }

    /**
     * Remove email from all books
     *
     * @param $email
     *
     * @return stdClass
     */
    public function removeEmailFromAllBooks($email)
    {
        if (empty($email)) {
            return $this->handleError('Empty email');
        }

        $requestResult = $this->sendRequest('emails/' . $email, 'DELETE');

        return $this->handleResult($requestResult);
    }


    private function getApiUserId()
    {
        return trim($this->getConfig('apiUserId'));
    }

    private function getApiSecret()
    {
        return trim($this->getConfig('apiSecret'));
    }

    public function credentialsFilled()
    {
        return $this->getApiUserId() != '' && $this->getApiSecret() != '';
    }

    public function addUser()
    {
        $addTo = $this->getConfig('rid');
        $deleteFromAll = $this->getConfig('del_rid');

        if ($deleteFromAll) {
            $this->removeEmailFromAllBooks($this->userInfo->user_email);
        }

        $emails = array(
            array(
                'email' => $this->userInfo->user_email,
                'variables' => array(
                    'phone' => $this->userInfo->phone,
                    'Имя' => $this->userInfo->first_name,
                    'Фамилия' => $this->userInfo->last_name
                )
            )
        );

        $result = $this->addEmails($addTo, $emails);

        return $result;
    }

    public function unsubscribe()
    {
        if ($this->getConfig('auto_disable') == 'on') {
            $this->removeEmails($this->getConfig('rid'), array($this->userInfo->user_email));
        }
    }

    public function updateUser()
    {
    }

}