<?php

class MBLPage implements MBLMetaInterface
{
    /**
     * @var WP_Post
     */
    private $_post;

    private $_postMeta;
    private $_shortDescription;
    private $_hasAccess;
    private $_userId;

    private $_commentsNumber;
    private $_response;

    private $_noAccessMetas;

    private $prevPostLink;
    private $nextPostLink;

    /**
     * @var MBLCategory
     */
    protected $category;

    private $_homeworkResponses = [];
    private $_isHomeworkDone;
    private $_attachments;

    /**
     * @var MBLHomework[]
     */
    private $homeworks;

    /**
     * MBLPage constructor.
     *
     * @param WP_Post $post
     * @param MBLCategory|null $category
     */
    public function __construct($post, $category = null)
    {
        remove_all_actions('the_content');
        remove_all_filters('the_content');
        //add_filter('the_content', 'wpautop'); // убрало удаление отступов во многих местах
        add_filter('the_content', 'do_shortcode');
        //add_filter('the_content', 'wpm_remove_protocol_from_text');

        $this->_post = $post;
        $this->_shortDescription = get_post_meta($post->ID, 'mbl_short_description', true);
        $this->_userId = get_current_user_id();
        $this->_initPostMeta();

        if ($category !== null) {
            $this->category = $category;
        }
    }

    /**
     * @return WP_Post
     */
    public function getPost()
    {
        return $this->_post;
    }

    /**
     * @param string $key
     * @param null $default
     *
     * @return mixed
     */
    public function getPostMeta($key = null, $default = null)
    {
        return wpm_array_get($this->_postMeta, $key, $default);
    }

    public function hasDescription()
    {
        return (bool)$this->getDescription();
    }

    public function getDescription()
    {
        return apply_filters('mbl_material_description_filter', wpm_remove_protocol_from_text($this->_shortDescription));
    }

    public function getTruncatedDescription()
    {
        return wpm_truncate_text($this->getDescription(), 201);
    }

    public function getHomeworkDescription()
    {
        return wpm_remove_protocol_from_text($this->getPostMeta('homework_description')); // убрал wpautop(), поскольку резало отступы в описании к заданию
    }

    public function getHomeworkResponse($key = null, $userId = null)
    {
        if ($userId === null) {
            $userId = $this->_userId;
        }

        return $this->getHomework($userId)->getResponseData($key);
    }

    public function getHomework($userId = null)
    {
        if ($userId === null) {
            $userId = $this->_userId;
        }

        if (!isset($this->homeworks[$userId])) {
            $this->homeworks[$userId] = new MBLHomework($this, $userId);
        }

        return $this->homeworks[$userId];
    }

    public function getHomeworkStatus($userId = null)
    {
        if ($this->hasHomeworkResponse($userId)) {
            return $this->getHomeworkResponse('status', $userId);
        }

        return null;
    }

    /**
     * @param $userId
     *
     * @return array|mixed|null
     */
    public function hasHomeworkResponse($userId = null)
    {
        return (bool)$this->getHomeworkResponse('content', $userId);
    }

    public function getHomeworkResponseContent($userId = null)
    {
        return $this->hasHomeworkResponse($userId)
            ? apply_filters('the_content', wpautop(wpm_remove_protocol_from_text($this->getHomeworkResponse('content', $userId))))
            : null;
    }

    public function getHomeworkResponseAttachmentsHTML($userId = null)
    {
        if ($userId === null) {
            $userId = $this->_userId;
        }

        return $this->hasHomeworkResponse($userId)
            ? UploadHandler::getHomeworkAttachmentsHtml($this->getId(), $userId)
            : null;
    }

    public function getHomeworkResponseAttachmentsAdminHTML($userId = null)
    {
        if ($userId === null) {
            $userId = $this->_userId;
        }

        return $this->hasHomeworkResponse($userId)
            ? UploadHandler::getHomeworkAttachmentsAdminHtml($this->getId(), $userId)
            : null;
    }

    public function getHomeworkResponseDate($userId = null)
    {
        return $this->hasHomeworkResponse($userId)
            ? $this->getHomeworkResponse('date_str', $userId)
            : null;
    }

    public function getHomeworkResponseTime($userId = null)
    {
        return $this->hasHomeworkResponse($userId)
            ? $this->getHomeworkResponse('time_str', $userId)
            : null;
    }

    public function getTitle()
    {
        return $this->_post->post_title;
    }

    public function getTruncatedTitle()
    {
        return wpm_truncate_text($this->_post->post_title, 70);
    }

    public function getContent()
    {
        return wpm_remove_protocol_from_text($this->_post->post_content);
    }

    public function isProtected()
    {
        $mainProtection = wpm_option_is('protection.text_protected', 'on');
        $exceptions = wpm_get_option('protection.text_protected_exceptions', []);

        return $mainProtection && !in_array($this->getId(), $exceptions);
    }

    public function getId()
    {
        return $this->_post->ID;
    }

    public function hasAccess()
    {
        if (!isset($this->_hasAccess)) {
            $this->_hasAccess = self::checkAccess($this->_userId, $this->getId());

            if ($this->category) {
                $this->_hasAccess = $this->_hasAccess && $this->category->hasAccess();
            }
        }

        return $this->_hasAccess;
    }

    public function hasMaterialAccess()
    {
        return self::checkAccess($this->_userId, $this->getId());
    }

    public static function checkAccess($userId, $postId)
    {
        $accessible_levels = $userId ? wpm_get_all_user_accesible_levels($userId) : [];

        return !wpm_is_blocked($userId) && wpm_check_access($postId, $accessible_levels);
    }

    private function _checkIsHomeworkDone()
    {
        return $this->hasHomework() && $this->getHomework()->isCompleted();
    }

    public function isHomeworkDone()
    {
        if (!isset($this->_isHomeworkDone)) {
            $this->_isHomeworkDone = $this->_checkIsHomeworkDone();
        }

        return $this->_isHomeworkDone;
    }

    /**
     * @return bool
     */
    public function hasHomework()
    {
        return wpm_array_get($this->_postMeta, 'is_homework') == 'on';
    }

    public function getCommentsNumber()
    {
        if (!isset($this->_commentsNumber)) {
            $this->_commentsNumber = @get_comments([
                'post_id' => $this->getId(),
                'fields'  => 'ids',
                'status'  => 'approve',
            ]);
        }

        return count($this->_commentsNumber);
    }

    public function getViewsNumber()
    {
        return intval(get_term_meta($this->getId(), 'wpm_page_views', true));
    }

    public function incrementView()
    {
        global $wpdb;
        $views = $this->getViewsNumber() + 1;
        $user_id = get_current_user_id();
        if ($user_id) {
            $datetime = $wpdb->get_var($wpdb->prepare("SELECT datetime FROM {$wpdb->prefix}memberlux_lesson_view
                WHERE post_id = %d AND user_id = %d", $this->getId(), $user_id));
            if (is_null($datetime)) {
                $wpdb->insert($wpdb->prefix . "memberlux_lesson_view", [
                    'user_id' => $user_id,
                    'post_id' => $this->getId(),
                    'datetime' => date('Y-m-d H:i:s', time())
                ]);
            }
        }
        update_term_meta($this->getId(), 'wpm_page_views', $views);
    }

    public function hasAttachments()
    {
        return (bool)count($this->getAttachments());
    }

    public function getAttachments()
    {
        if (!isset($this->_attachments)) {
            $this->_attachments = wpm_array_get(UploadHandler::getMaterialAttachments($this->getId()), 'files', []);
        }

        return $this->_attachments;
    }

    public function getTabsCount()
    {
        $count = 1;

        if (!$this->hasAccess()) {
            return $count;
        }

        if ($this->hasHomework() && is_user_logged_in()) {
            ++$count;
        }

        if ($this->hasAttachments()) {
            ++$count;
        }

        return $count;
    }

    public function getHomeworkStatusClass()
    {
        return $this->hasHomeworkResponse()
            ? wpm_get_response_status_class($this->getHomeworkStatus())
            : null;
    }

    public function getHomeworkStatusText()
    {
        return $this->hasHomeworkResponse()
            ? wpm_get_response_status_message($this->getHomeworkStatus())
            : __('не начат', 'mbl');
    }

    public function hasHomeworkResponseReviews()
    {
        return (bool)count($this->getHomeworkResponse('reviews'));
    }

    public function hasVideoContent()
    {
        if (!is_array($this->getPostMeta('content_types'))) {
            $this->detectContentTypes();
        }

        return $this->getPostMeta('content_types.video');
    }

    public function hasAudioContent()
    {
        if (!is_array($this->getPostMeta('content_types'))) {
            $this->detectContentTypes();
        }

        return $this->getPostMeta('content_types.audio');
    }

    public function hasTextContent()
    {
        if (!is_array($this->getPostMeta('content_types'))) {
            $this->detectContentTypes();
        }

        return $this->getPostMeta('content_types.text');
    }

    public function detectContentTypes()
    {
        if (has_shortcode($this->getContent(), 'wpm_video') || strpos($this->getContent(), '<video') !== false) {
            $this->updatePostMeta('content_types.video', true);
        }

        if (has_shortcode($this->getContent(), 'wpm_uppod') || has_shortcode($this->getContent(), 'wpm_audio') || strpos($this->getContent(), '<audio') !== false) {
            $this->updatePostMeta('content_types.audio', true);
        }

        if (strlen(trim(strip_tags(strip_shortcodes($this->getContent())))) >= 240) {
            $this->updatePostMeta('content_types.text', true);
        }
    }

    public function updatePostMeta($key, $value)
    {
        update_post_meta($this->getId(), '_wpm_page_meta', wpm_array_set($this->getPostMeta(), $key, $value));
        $this->_initPostMeta();
    }

    public function open()
    {
        AutoTrainingAccess::pageOpened($this);
        remove_all_actions('the_content');
        remove_all_filters('the_content');
        //add_filter('the_content', 'wpautop'); убрал wpautop. Поскольку резало описание в тестах
        add_filter('the_content', 'do_shortcode');
        add_filter('the_content', 'wpm_add_infoprotector_key_to_url');
        //add_filter('the_content', 'wpm_remove_protocol_from_text');
    }

    public function getNoAccessContent()
    {
        $termMetas = $this->getLevelMetasWithNoAccessContent();

        $noAccessContent = '';

        if (count($termMetas) > 1) {
            $noAccess = __('Материал доступен для следующих тарифных планов:', 'mbl');
            $noAccessContent .= ('<h2 class="accordion-title">' . $noAccess . '</h2>');
            $noAccessContent .= '<div id="no-access-content">';

            foreach ($termMetas as $termId => $meta) {
                $term = get_term($termId, 'wpm-levels');

                $noAccessContent .= '<div data-term-id="' . $termId . '">' .
                                    '<h3>' . $term->name . '</h3>' .
                                    '<div class="term-content">' . wpautop(stripslashes($meta['no_access_content'])) . '</div>' .
                                    '</div>';
            }

            $noAccessContent .= '</div>';

        } else {
            foreach ($termMetas as $termId => $meta) {
                $noAccessContent .= $this->_getNoAccessForSingleTerm($termId);
            }
        }

        return apply_filters('the_content', $noAccessContent);
    }

    private function _getNoAccessForSingleTerm($termId)
    {
        $meta = get_option("taxonomy_term_$termId");

        if (defined('MBLP_VERSION') && class_exists('woocommerce') && wpm_array_get($meta, 'no_access_redirect_cart') == 'on') {
            $product = wc_get_product(wpm_array_get($meta, 'no_access_product_id'));

            if ($product && is_callable([$product, 'get_add_to_cart_sample_url'])) {
                return $this->_getNoAccessRedirect($product->get_add_to_cart_sample_url());
            }
        }

        if(wpm_array_get($meta, 'no_access_redirect_url') == 'on') {
            return $this->_getNoAccessRedirect(wpm_array_get($meta, 'no_access_redirect_url_value'));
        }

        return wpautop(stripslashes($meta['no_access_content']));
    }

    private function _getNoAccessRedirect($url)
    {
        return wpm_render_partial('noaccess_redirect', 'base', compact('url'), true);
    }

    /**
     * @return array
     */
    public function getLevelMetasWithNoAccessContent()
    {
        if (!isset($this->_noAccessMetas)) {
            $termIds = wp_get_post_terms($this->getId(), 'wpm-levels', ["fields" => "ids"]);

            if (!count($termIds)) {
                $termIds = get_terms('wpm-levels', ["fields" => "ids"]);
            }

            $termMetas = [];

            foreach ($termIds as $termId) {
                $meta = get_option("taxonomy_term_$termId");
                if ($meta && !empty($meta['no_access_content'])) {
                    $termMetas[$termId] = $meta;
                }
            }
            $this->_noAccessMetas = $termMetas;
        }

        return $this->_noAccessMetas;
    }

    public function isPassed()
    {
        return $this->hasHomework()
            ? $this->isHomeworkDone()
            : $this->isPassedByUser();
    }

    public function isPassedByUser()
    {
        $passed = self::_getPassedByUser();

        return wpm_array_get($passed, $this->getId(), false);
    }

    public static function setIsPassed($postId, $isPassed)
    {
        $passed = self::_getPassedByUser();

        $passed[$postId] = !!$isPassed;

        update_user_meta(get_current_user_id(), 'passed_materials', $passed);
        do_action('mbl_material_passed', $postId, !!$isPassed);
    }

    private static function _getPassedByUser()
    {
        $passed = get_user_meta(get_current_user_id(), 'passed_materials', true);

        if (!is_array($passed)) {
            $passed = [];
        }

        return $passed;
    }

    /**
     * @return mixed
     */
    public function getPrevPostLink()
    {
        if (!isset($this->prevPostLink) && isset($this->category)) {
            $prevMblPage = $this->category->getPrevMBLPage();
            $this->prevPostLink = $prevMblPage && $prevMblPage->hasAccess()
                ? wpm_material_link($this->category ? $this->category->getWpCategory() : null, $prevMblPage->getPost())
                : null;
        }

        return $this->prevPostLink;
    }

    public function getLink()
    {
        return wpm_material_link($this->category ? $this->category->getWpCategory() : null, $this->getPost());
    }

    public function getPrevMBLPage()
    {
        return $this->category
            ? $this->category->getPrevMBLPage()
            : null;
    }

    /**
     * @return mixed
     */
    public function getNextPostLink()
    {
        if (!isset($this->nextPostLink)) {
            $nextMblPage = $this->getNextMBLPage();

            $this->nextPostLink = $nextMblPage /*&& $nextMblPage->hasAccess()*/
                ? wpm_material_link($this->category ? $this->category->getWpCategory() : null, $nextMblPage->getPost())
                : null;
        }

        return $this->nextPostLink;
    }

    public function getNextMBLPage()
    {
        return $this->category
            ? $this->category->getNextMBLPage()
            : null;
    }

    public function hasNextPostLink()
    {
        $link = $this->getNextPostLink();

        return $link && $link !== '';
    }

    public function hasPrevPostLink()
    {
        $link = $this->getPrevPostLink();

        return $link && $link !== '';
    }

    private function _initPostMeta()
    {
        $this->_postMeta = get_post_meta($this->getId(), '_wpm_page_meta', true);
    }

    public function hasBackgroundImage()
    {
        return (bool)$this->getBackgroundImage();
    }

    public function getBackgroundImage()
    {
        $image = $this->getPostMeta('bg_url');

        return $image ? $image : wpm_get_option('materials.bg_url');
    }

    public function showNumber()
    {
        return $this->showIndicator('number_show');
    }

    public function showContentType()
    {
        return $this->showIndicator('content_type_show');
    }

    public function showHomeworkStatus()
    {
        return $this->showIndicator('homework_status_show');
    }

    public function showDate()
    {
        return $this->showIndicator('date_show');
    }

    public function showComments()
    {
        return $this->showIndicator('comments_show') && !wpm_option_is('main.comments_mode', 'cackle');
    }

    public function showViews()
    {
        return $this->showIndicator('views_show');
    }

    public function showAccess()
    {
        return $this->showIndicator('access_show');
    }

    private function showIndicator($alias)
    {
        return $this->getPostMeta('indicators.individual_indicators', 0)
            ? $this->getPostMeta('indicators.' . $alias)
            : wpm_get_option('materials.' . $alias, 1);
    }

    public function hasShift()
    {
        return (bool)$this->getPostMeta('shift_is_on');
    }

    public function getMeta($key = null, $default = null)
    {
        return $this->getPostMeta($key, $default);
    }

    public function getAccessLevels()
    {
        return wp_get_post_terms($this->getId(), 'wpm-levels');
    }

    public function getAccessLevelNames()
    {
        $levels = $this->getAccessLevels();

        if (is_wp_error($levels) || !is_array($levels)) {
            return [];
        }

        return array_map(function ($level) {
            return $level->name;
        }, $levels);
    }

    public function getEditLink()
    {
        return apply_filters('edit_post_link', esc_url(get_edit_post_link($this->getId())), $this->getId(), '');
    }

    public function getStoredReleaseDate($utc = true, $default = null)
    {
        $categoryId = $this->category
            ? $this->category->getTermId()
            : null;

        $releaseDate = AutoTrainingAccess::getUserMeta($this->getId(), $categoryId, 'release_date', $default, $this->_userId);

        if ($releaseDate !== $default && $utc) {
            $releaseDate -= (get_option('gmt_offset') * HOUR_IN_SECONDS);
        }

        return $releaseDate;
    }

    public function getAutotraining($withFallback = true)
    {
        if (!$this->category) {
            $id = wpm_get_autotraining_id_by_post($this->getId());

            if (!$id && $withFallback) {
                $terms = wp_get_post_terms($this->getId(), 'wpm-category');

                if (count($terms)) {
                    $id = $terms[0]->term_id;
                }
            }

            if ($id) {
                $this->category = new MBLCategory(get_term($id, 'wpm-category'), false, false, true);
            }
        }

        return $this->category;
    }
}
