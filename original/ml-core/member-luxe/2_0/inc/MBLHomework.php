<?php

class MBLHomework
{
    /**
     * @var MBLPage
     */
    private $page;

    /**
     * @var integer
     */
    private $userId;

    private $response;

    private $responseData;

    /**
     * MBLHomework constructor.
     * @param MBLPage $page
     * @param integer $userId
     */
    public function __construct(MBLPage $page, $userId = null)
    {
        $this->page = $page;
        $this->userId = $userId === null ? get_current_user_id() : $userId;
    }

    public function getInfo()
    {
        if ($this->isCompleted()) {
            return [
                'done' => true,
                'time' => strtotime(wpm_array_get($this->_getResponse(), 'date')),
            ];
        }

        return [
            'done' => false,
        ];
    }

    public function isCompleted()
    {
        return $this->hasResponse() && $this->responseStatusDone();
    }

    public function getResponseContent()
    {
        return $this->_getResponse()
            ? apply_filters('the_content', stripslashes($this->_getResponse()->response_content))
            : null;
    }

    private function _getResponse()
    {
        if (!isset($this->response)) {
            $this->response = wpm_response($this->userId, $this->page->getId());
        }

        return $this->response;
    }

    public function getResponseObject()
    {
        return $this->_getResponse();
    }

    public function responseStatusDone()
    {
        return in_array($this->getResponseStatus(), ['accepted', 'approved']);
    }

    public function getResponseStatus()
    {
        return $this->_isOnHoldByShift()
            ? 'opened'
            : $this->_getResponse()->response_status;
    }

    public function getRealResponseStatus()
    {
        return $this->_getResponse()->response_status;
    }

    public function getEndDate()
    {
        $approvalDate = mbl_mysql_date($this->_getResponse()->approval_date);
        return $approvalDate
            ? $approvalDate
            : mbl_mysql_date($this->_getResponse()->response_date);
    }

    private function _isOnHoldByShift()
    {
        $result = false;

        if ($this->_hasShift()) {
            $result = $this->_getResponse()->approval_date && mbl_mysql_date($this->_getResponse()->approval_date) > current_time('timestamp');
        }

        return $result;
    }

    private function _hasShift()
    {
        return $this->_getResponse()->response_status == 'accepted' && $this->page->getPostMeta('confirmation_method') == 'auto_with_shift';
    }

    public function getResponseData($key = null)
    {
        $this->_initResponseData();

        return wpm_array_get($this->responseData, $key);
    }

    private function _initResponseData()
    {
        if (!isset($this->responseData)) {
            $data = [
                'id'          => null,
                'date'        => '',
                'status'      => '',
                'content'     => '',
                'reviews'     => [],
                'date_object' => null,
            ];

            if ($this->_getResponse()) {
                $data['id'] = $this->_getResponse()->id;
                $data['date'] = date_format(date_create($this->_getResponse()->response_date), 'H:i d/m/Y');
                $data['date_str'] = date_format(date_create($this->_getResponse()->response_date), 'd.m.Y');
                $data['time_str'] = date_format(date_create($this->_getResponse()->response_date), 'H:i');
                $data['date_object'] = date_create($this->_getResponse()->response_date);
                $data['status'] = $this->getResponseStatus();
                $data['real_status'] = $this->_getResponse()->response_status;
                $data['content'] = apply_filters('the_content', stripslashes($this->_getResponse()->response_content));
                $data['status_msg'] = wpm_get_response_status_message($this->_getResponse()->response_status);
                $data['status_class'] = wpm_get_response_status_class($this->_getResponse()->response_status);
                $data['reviews'] = wpm_get_response_reviews($this->_getResponse()->id);
            }

            $this->responseData = $data;
        }
    }

    public function hasResponse()
    {
        return !empty($this->getResponseContent());
    }

}
