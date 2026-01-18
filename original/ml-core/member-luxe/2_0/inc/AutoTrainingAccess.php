<?php

class AutoTrainingAccess
{
    const META_KEY = '_wpm_auto_training_data';
    const USER_META_KEY = '_wpm_auto_training_post_data_';
    const USER_UPDATE_META_KEY = '_wpm_auto_training_post_data_updated_';

    /**
     * @var int
     */
    private $categoryId;

    /**
     * @var array
     */
    private $categoryMeta;

    /**
     * @var array
     */
    private $userMeta;

    /**
     * @var integer
     */
    private $userId;

    /**
     * AutoTrainingAccess constructor.
     *
     * @param $categoryId
     * @param null $userId
     */
    public function __construct($categoryId, $userId = null)
    {
        $this->categoryId = $categoryId;
        $this->userId = $userId === null ? get_current_user_id() : $userId;

        $this->_initCategoryMeta();
        $this->_initUserMeta();
        $this->_updateMetaStorage();
    }

    private function _initCategoryMeta()
    {
        $meta = get_term_meta($this->categoryId, self::META_KEY, true);

        if (empty($meta)) {
            $meta = self::buildCategoryMeta($this->categoryId);
        }

        $this->categoryMeta = $meta;
    }

    public static function buildCategoryMeta($categoryId)
    {
        $collection = new MBLPageCollection($categoryId);
        $meta = [];

        $k = 0;

        foreach ($collection->getPages() as $MBLPage) {
            $meta[] = [
                'post_id'          => $MBLPage->getId(),
                'order'            => ++$k,
                'shift_is_on'      => $MBLPage->hasShift(),
                'shift_value_type' => $MBLPage->getPostMeta('shift_value_type'),
            ];
        }

        update_term_meta($categoryId, self::META_KEY, $meta);

        return $meta;
    }

    private function _initUserMeta()
    {
        $meta = get_user_meta($this->userId, self::USER_META_KEY . $this->categoryId, true);

        if (empty($meta)) {
            $meta = $this->_buildUserMeta();
        }

        $this->userMeta = $meta;
    }

    private function _buildUserMeta()
    {
        $meta = [];

        foreach ($this->categoryMeta as $item) {
            $meta[$item['post_id']] = [
                'has_access' => false,
            ];
        }

        update_user_meta($this->userId, self::USER_META_KEY . $this->categoryId, $meta);

        return $meta;
    }

    private function _updateMetaStorage()
    {
        if (!$this->userId) return;
        $meta = get_user_meta($this->userId, self::USER_UPDATE_META_KEY . $this->categoryId, true);

        if (empty($meta)) {
            $userCategoryData = wpm_user_cat_data($this->categoryId, $this->userId);

            foreach ($this->categoryMeta as $item) {
                $postId = $item['post_id'];

                if (!$this->getUserPostMeta($postId, 'opened')) {
                    $opened = 0;

                    if (isset($userCategoryData['schedule'][$postId]['opened'])) {
                        $opened = intval($userCategoryData['schedule'][$postId]['opened']);
                    }

                    if (!$opened) {
                        $response = wpm_response($this->userId, $postId);

                        if ($response) {
                            $opened = strtotime($response->response_date);
                        }
                    }

                    if ($opened) {
                        $this->updateUserPostMeta($postId, 'opened', $opened + (get_option('gmt_offset') * HOUR_IN_SECONDS));
                    }
                }
            }

            update_user_meta($this->userId, self::USER_UPDATE_META_KEY . $this->categoryId, true);
        }
    }

    public function getUserPostMeta($postId, $key = null, $default = null)
    {
        $key = $key === null ? $postId : $postId . '.' . $key;
        return wpm_array_get($this->userMeta, $key, $default);
    }

    public function hasUserPostMeta($postId, $key = null)
    {
        return $this->getUserPostMeta($postId, $key) !== null;
    }

    public static function getUserMeta($postId, $categoryId = null, $key = null, $default = null, $userId = null)
    {
        $key = $key === null ? $postId : $postId . '.' . $key;
        $userId = $userId === null ? get_current_user_id() : $userId;
        $categoryId = $categoryId ? $categoryId : wpm_get_autotraining_id_by_post($postId);
        $meta = get_user_meta($userId, self::USER_META_KEY . $categoryId, true);

        return wpm_array_get($meta, $key, $default);
    }

    public function updateUserPostMeta($postId, $key, $value)
    {
        update_user_meta($this->userId, self::USER_META_KEY . $this->categoryId, wpm_array_set($this->userMeta, $postId . '.' . $key, $value));

        $this->_initUserMeta();
    }

    public static function updatePostCategoryMetas($postId)
    {
        $categoryIds = wp_get_post_terms($postId, 'wpm-category', ["fields" => "ids"]);

        if (is_array($categoryIds)) {
            foreach ($categoryIds as $categoryId) {
                $isAutotraining = wpm_is_autotraining($categoryId);
                if ($isAutotraining) {
                    self::buildCategoryMeta($categoryId);
                }
            }
        }
    }

    public static function clearPostCategoryMetas($postId)
    {
        $categoryIds = wp_get_post_terms($postId, 'wpm-category', ["fields" => "ids"]);

        if (is_array($categoryIds)) {
            foreach ($categoryIds as $categoryId) {
                $isAutotraining = wpm_is_autotraining($categoryId);
                if ($isAutotraining) {
                    self::clearCategoryMeta($categoryId);
                }
            }
        }
    }

    public static function clearCategoryMeta($categoryId)
    {
        delete_term_meta($categoryId, self::META_KEY);
    }

    public static function pageOpened(MBLPage $page, $userId = null)
    {
        $categoryIds = wp_get_post_terms($page->getId(), 'wpm-category', ["fields" => "ids"]);
        if (is_array($categoryIds)) {
            foreach ($categoryIds as $categoryId) {
                $isAutotraining = wpm_is_autotraining($categoryId);
                if ($isAutotraining) {
                    $access = new self($categoryId, $userId);
                    $access->_openPage($page->getId());

                    if(!$page->hasHomework()) {
                        MBLPage::setIsPassed($page->getId(), true);
                    }
                }
            }
        }
    }

    public function _openPage($postId)
    {
        if (!$this->getUserPostMeta($postId, 'opened')) {
            $this->updateUserPostMeta($postId, 'opened', current_time('timestamp'));
        }
    }

    public function checkAccess($postId)
    {
        $result = true;
        $currentMBLPage = new MBLPage(get_post($postId));

        if(!$currentMBLPage->hasAccess()) {
            return false;
        }

        $previousMBLPage = $this->_getPreviousMBLPage($postId);

        if (!$this->hasUserPostMeta($currentMBLPage->getId(), 'release_date')) {
            $result = $this->_hasAccess($currentMBLPage, $previousMBLPage);
        }

        if ($this->hasUserPostMeta($currentMBLPage->getId(), 'release_date')) {
            $result = $this->_checkReleaseDate($currentMBLPage);
        }

        if ($result) {
            $this->storeAccess($postId);
        }

        return $result;
    }

    /**
     * @param MBLPage $currentMBLPage
     * @param MBLPage|null $previousMBLPage
     * @return bool
     */
    private function _hasAccess($currentMBLPage, $previousMBLPage = null)
    {
        if (!$previousMBLPage) {
            return $this->_checkAutoTrainingFirstMaterialAccess($currentMBLPage);
        }

        //if previous page is not accessible - deny
        if (!$this->hasStoredAccess($previousMBLPage->getId())) {
            return false;
        }

        if ($this->getUserPostMeta($currentMBLPage->getId(), 'opened')) {
            return true;
        }

        //if previous page wasn't opened - deny
        if (!$this->getUserPostMeta($previousMBLPage->getId(), 'opened')) {
            return false;
        }

        $result = true;

        if ($previousMBLPage->hasHomework()) {

            $homework = $previousMBLPage->getHomework($this->userId);
            if (!$homework->hasResponse() || !in_array($homework->getRealResponseStatus(), ['accepted', 'approved'])) {
                return false;
            }

            //store end_date according to homework
            if (!$this->hasUserPostMeta($previousMBLPage->getId(), 'end_date') && $previousMBLPage->hasHomeworkResponse()) {
                $endDate = $previousMBLPage->getHomework()->getEndDate();
                $this->updateUserPostMeta($previousMBLPage->getId(), 'end_date', $endDate);
                $this->setHomeworkReminder($previousMBLPage, $endDate);
            }

            if (!$previousMBLPage->isHomeworkDone()) {
                $result = false;
            }
        }

        $releaseDate = $this->getPageReleaseDate($currentMBLPage, $previousMBLPage);

        if ($previousMBLPage->hasHomework() || $currentMBLPage->hasShift()) {
            $this->setPostReleaseDate($currentMBLPage, $releaseDate);
        }

        return $result;
    }

    /**
     * @param MBLPage $mblPage
     * @param int $timestamp
     */
    public function setReminder($mblPage, $timestamp)
    {
        wp_schedule_single_event($timestamp - (get_option('gmt_offset') * HOUR_IN_SECONDS), 'wpm_schedule_user_new_material_access', [$mblPage, $this->userId]);
    }

    /**
     * @param MBLPage $mblPage
     * @param int $timestamp
     */
    public function setHomeworkReminder($mblPage, $timestamp)
    {
        $MBLHomework = $mblPage->getHomework($this->userId);
        if($MBLHomework->getRealResponseStatus() === 'accepted') {
            wp_schedule_single_event($timestamp - (get_option('gmt_offset') * HOUR_IN_SECONDS), 'wpm_schedule_user_homework_status', [$MBLHomework->getRealResponseStatus(), $MBLHomework->getResponseObject()]);
        }
    }

    /**
     * @param $postId
     * @return MBLPage|null
     */
    private function _getPreviousMBLPage($postId)
    {
        $previousId = null;

        foreach ($this->categoryMeta as $item) {
            if ($postId == $item['post_id']) {
                break;
            }

            $previousId = $item['post_id'];
        }

        return $previousId ? new MBLPage(get_post($previousId)) : null;
    }

    /**
     * @param MBLPage $currentMBLPage
     * @return bool
     */
    private function _checkAutoTrainingFirstMaterialAccess($currentMBLPage)
    {
        $mblCategory = new MBLCategory(get_term($this->categoryId), false, false, true);

        if ($mblCategory->getMeta('autotraining_first_material') == 'shift') {
            if (!is_user_logged_in()) {
                return false;
            }

            $user = wp_get_current_user();
            $registeredAt = strtotime($user->user_registered) + (get_option('gmt_offset') * HOUR_IN_SECONDS);

            $shift = new MBLShift($mblCategory, $registeredAt, 'first_material_shift');

            $this->setPostReleaseDate($currentMBLPage, $shift->getEndDate()->getTimestamp());

            return false;

        } elseif ($mblCategory->getMeta('autotraining_first_material') == 'shift_activation') {
            if (!is_user_logged_in()) {
                return false;
            }

            $activationStartDate = $this->_getActivationStartDate();

            if ($activationStartDate === null) {
                return false;
            }

            $shift = new MBLShift($mblCategory, $activationStartDate, 'first_material_activation_shift');

            $this->setPostReleaseDate($currentMBLPage, $shift->getEndDate()->getTimestamp());
            return false;
        }

        return true;
    }

    private function _getActivationStartDate()
    {
        $activation = new MBLActivation($this->userId, true);
        $startDate = null;

        foreach ($activation->getRows() as $keyRow) {
            foreach ($keyRow->getCategories() as $MBLCategory) {
                if ($MBLCategory->getTermId() == $this->categoryId && ($startDate === null || $startDate > $keyRow->getDateStartTimestamp())) {
                    $startDate = $keyRow->getDateStartTimestamp();
                }
            }
        }

        return $startDate
            ? ($startDate + (60 * 60 * 24))
            : null;
    }

    public function storeAccess($postId, $hasAccess = true)
    {
        $this->updateUserPostMeta($postId, 'has_access', $hasAccess);
    }

    public function hasStoredAccess($postId)
    {
        $previousHasAccess = true;
        $hasAccess = wpm_array_get($this->userMeta, $postId . '.has_access', false);

        foreach ($this->categoryMeta as $item) {
            if ($postId == $item['post_id']) {
                break;
            }

            if (!wpm_array_get($this->userMeta, $item['post_id'] . '.has_access', false)) {
                $previousHasAccess = false;
                break;
            }
        }

        return $previousHasAccess && $hasAccess;

    }

    public function getPageReleaseDate(MBLPage $currentMBLPage, MBLPage $previousMBLPage)
    {
        $startDate = $this->getUserPostMeta($previousMBLPage->getId(), 'end_date', $this->getUserPostMeta($previousMBLPage->getId(), 'opened'));

        if ($currentMBLPage->hasShift()) {
            $shift = new MBLShift($currentMBLPage, $startDate, 'shift_value');
            $releaseDate = $shift->getEndDate()->getTimestamp();
        } else {
            $releaseDate = $startDate;
        }

        return $releaseDate;
    }

    private function setPostReleaseDate($currentMBLPage, $releaseDate)
    {
        if (!$this->hasUserPostMeta($currentMBLPage->getId(), 'release_date')) {
            $this->updateUserPostMeta($currentMBLPage->getId(), 'release_date', $releaseDate);
            if ($releaseDate) {
                $this->setReminder($currentMBLPage, $releaseDate);
            }
        }
    }

    private function _checkReleaseDate(MBLPage $currentMBLPage)
    {
        return $this->getUserPostMeta($currentMBLPage->getId(), 'release_date') <= current_time('timestamp');
    }

    public static function log($message)
    {
        $logDir = WP_PLUGIN_DIR . '/member-luxe/log';
        $logFile = $logDir . '/access.log';

        if (is_array($message)) {
            $message = print_r($message, true);
        }

        if (!file_exists($logDir)) {
            mkdir($logDir);
        }

        if (is_dir($logDir) && is_writable($logDir)) {
            file_put_contents($logFile, $message . "\n", FILE_APPEND);
        }
    }
}
