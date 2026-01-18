<?php

class AutoTrainingView
{
    public $mainOptions;
    public $designOptions;
    public $categoryId;
    /**
     * @var WP_User
     */
    public $user;

    /**
     * @var array
     */
    public $accessibleLevels;

    /**
     * @var bool
     */
    public $isAutoTraining;

    /**
     * @var bool
     */
    public $previousVisible = true;

    /**
     * @var int
     */
    public $postId;

    /**
     * @var bool
     */
    public $isAuthor;

    /**
     * @var bool
     */
    public $hasAccess = false;

    /**
     * @var bool
     */
    public $hasDirectAccess = false;

    /**
     * @var bool
     */
    public $isPostponedDueToHomework = false;

    /**
     * @var array
     */
    public $pageMeta;

    /**
     * @var int
     */
    public $prevPostId = null;

    /**
     * @var array
     */
    public $prevPostMeta = null;

    /**
     * @var int
     */
    public $postIterator;

    /**
     * @var bool
     */
    public $onlyPreview = false;
    /**
     * @var bool
     */
    public $isFirstPreview = null;

    private $_hasNextPosts = null;
    private $_countPosts;
    /**
     * @var bool
     */
    private $paginate;

    /**
     * @var AutoTrainingAccess
     */
    private $autoTrainingAccess;

    /**
     * AutoTrainingView constructor.
     * @param $categoryId
     * @param bool $paginate
     */
    public function __construct($categoryId, $paginate = true)
    {
        global $paged, $wp_query;

        $page = $paged;

        $this->categoryId = $categoryId;

        $this->init();

        if (!$page || !$paginate) {
            $page = 1;
        }

        if ($page > 1) {
            $this->_setPrevPostData();
        }

        $this->autoTrainingAccess = new AutoTrainingAccess($categoryId);

        $this->mainOptions = get_option('wpm_main_options');
        $this->designOptions = get_option('wpm_design_options');
        $this->user = wp_get_current_user();
        $this->accessibleLevels = wpm_get_all_user_accesible_levels($this->user->ID);
        $this->isAutoTraining = wpm_is_autotraining($this->categoryId);
        $this->postIterator = $this->mainOptions['main']['posts_per_page'] * ($page - 1) + 1;
        $this->_countPosts = count($wp_query->posts);
        $this->paginate = $paginate;
    }

    public function init()
    {
        global $wp_query;
        if (!empty($wp_query->posts)) {
            $chain = array();
            foreach ($wp_query->posts as $item) {
                array_push($chain, $item->ID);
            }
        }

        return $this;
    }

    private function _setPrevPostData()
    {
        $prevPost = get_previous_post();

        $collection = new MBLPageCollection($this->categoryId);
        $pages = array_values($collection->getPages());
        foreach ($pages as $k => $page) {
            /** @var MBLPage $page */
            if($page->getId() == get_the_ID()) {
                $prevPage = wpm_array_get($pages, $k-1);
                if($prevPage) {
                    $prevPost = $prevPage->getPost();
                }
            }
        }

        if ($prevPost) {
            $this->prevPostId = $prevPost->ID;
            $this->prevPostMeta = get_post_meta($this->prevPostId, '_wpm_page_meta', true);
        }
    }

    public function iterate($id = null)
    {
        $this->postId = $id ?: get_the_ID();
        $this->isAuthor = wpm_is_author($this->user->ID, get_the_author_meta('ID'));
        $this->hasAccess = wpm_check_access($this->postId, $this->accessibleLevels);
        $this->pageMeta = get_post_meta($this->postId, '_wpm_page_meta', true);
    }

    public function showPost()
    {
        return (is_user_logged_in() || $this->mainOptions['main']['opened'])
        && $this->hasLevelAccess()
        && $this->hasAccess();
    }

    public function hasLevelAccess()
    {
        $showPost = false;
        $levelsList = wpm_get_post_level_ids($this->postId);
        foreach ($levelsList AS $termId) {
            $termMeta = get_option("taxonomy_term_{$termId}");
            if (!isset($termMeta['hide_for_no_access']) || $termMeta['hide_for_no_access'] != 'hide') {
                $showPost = true;
            }
        }

        return $showPost || $this->hasAccess || $this->isAuthor;
    }

    public function hasToCheckAccess()
    {
        return !$this->isAuthor && $this->isAutoTraining && !in_array('administrator', $this->user->roles);
    }

    public function checkAccess()
    {
        if($this->autoTrainingAccess->hasStoredAccess($this->postId)) {
            return true;
        }

        $this->hasDirectAccess = wpm_has_direct_access($this->postId);

        if($this->hasDirectAccess) {
            $this->autoTrainingAccess->storeAccess($this->postId);
            return true;
        }

        return $this->autoTrainingAccess->checkAccess($this->postId);
    }

    public function hasAccess()
    {
        $result = true;
        if ($this->hasToCheckAccess()) {
            if (!$this->checkAccess()) {
                $result = false;
            }
        }

        return $result;
    }

    public function updatePostIterator()
    {
        $this->postIterator++;
    }

    public function hasNextPosts($curMblPage = null)
    {
        if (is_null($this->_hasNextPosts)) {

            if($curMblPage !== null) {
                $this->postIterator = 0;
                $this->postId = $curMblPage->getId();
                $this->prevPostMeta = $curMblPage->getPostMeta();
                $nextPage = $curMblPage->getNextMBLPage();
                $nextPost = $nextPage ? $nextPage->getPost() : get_next_post();
            } else {
                $nextPost = get_next_post();
            }

            $this->_hasNextPosts = (bool)$nextPost;

            if ($nextPost && $this->isAutoTraining) {
                $this->prevPostId = $this->postId;
                $this->postId = $nextPost->ID;
                $this->isAuthor = wpm_is_author($this->user->ID, $nextPost->post_author);
                $this->hasAccess = wpm_check_access($this->postId, $this->accessibleLevels);
                $this->pageMeta = get_post_meta($this->postId, '_wpm_page_meta', true);

                $this->_hasNextPosts = ($this->showPost() && (!$this->hasToCheckAccess() || $this->checkAccess($curMblPage)))
                    || ($nextPage && !$nextPage->hasAccess());
            }
        }

        return $this->_hasNextPosts;
    }

    public function showAll()
    {
        $showAll = wpm_get_design_option('page.show_all') == 'on';

        if ($showAll) {
            $this->onlyPreview = !is_user_logged_in();
        }
        $this->isFirstPreview = is_null($this->isFirstPreview);

        return $showAll;
    }

    public function isLastRow()
    {
        return $this->_countPosts == $this->postIterator;
    }

    public function getShowButtonText()
    {
        return array_key_exists('text', $this->designOptions['buttons']['show'])
            ? $this->designOptions['buttons']['show']['text']
            : 'Показать';
    }

    public function getNoAccessButtonText()
    {
        return array_key_exists('text', $this->designOptions['buttons']['no_access'])
            ? $this->designOptions['buttons']['no_access']['text']
            : 'Нет доступа';
    }

    public function transliterate($string)
    {
        $iso = array(
            "Є" => "YE", "І" => "I", "Ѓ" => "G", "і" => "i", "№" => "", "є" => "ye", "ѓ" => "g",
            "А" => "A", "Б" => "B", "В" => "V", "Г" => "G", "Д" => "D",
            "Е" => "E", "Ё" => "YO", "Ж" => "ZH",
            "З" => "Z", "И" => "I", "Й" => "J", "К" => "K", "Л" => "L",
            "М" => "M", "Н" => "N", "О" => "O", "П" => "P", "Р" => "R",
            "С" => "S", "Т" => "T", "У" => "U", "Ф" => "F", "Х" => "H",
            "Ц" => "C", "Ч" => "CH", "Ш" => "SH", "Щ" => "SHH", "Ъ" => "'",
            "Ы" => "Y", "Ь" => "", "Э" => "E", "Ю" => "YU", "Я" => "YA",
            "а" => "a", "б" => "b", "в" => "v", "г" => "g", "д" => "d",
            "е" => "e", "ё" => "yo", "ж" => "zh",
            "з" => "z", "и" => "i", "й" => "j", "к" => "k", "л" => "l",
            "м" => "m", "н" => "n", "о" => "o", "п" => "p", "р" => "r",
            "с" => "s", "т" => "t", "у" => "u", "ф" => "f", "х" => "h",
            "ц" => "c", "ч" => "ch", "ш" => "sh", "щ" => "shh", "ъ" => "",
            "ы" => "y", "ь" => "", "э" => "e", "ю" => "yu", "я" => "ya",
            "—" => "-", "«" => "", "»" => "", "…" => ""
        );

        foreach ($iso AS $cyr => $lat) {
            $string = str_replace($cyr, $lat, $string);
        }

        return $string;
    }

    public function updateAccessMeta($hasAccess = null)
    {
        if($hasAccess === null) {
            $hasAccess = !$this->onlyPreview;
        }

        $meta = get_post_meta($this->postId, 'wmp_material_access', true);

        if (!$meta || !is_array($meta)) {
            $meta = array();
        }

        if(!$hasAccess && isset($meta[$this->user->ID])) {
            unset($meta[$this->user->ID]);
        } elseif ($hasAccess) {
            $meta[$this->user->ID] = true;
        }

        update_post_meta($this->postId, 'wmp_material_access', $meta);
    }

    public function updateProgressMeta($termId, $isPassed)
    {
        $meta = get_term_meta($termId, 'wmp_term_progress', true);

        if (!$meta) {
            $meta = array();
        }

        if (!isset($meta[$this->user->ID])) {
            $meta[$this->user->ID] = array();
        }

        if (isset($meta[$this->user->ID][$this->postId]) && ($this->onlyPreview || !$isPassed)) {
            unset($meta[$this->user->ID][$this->postId]);
        } elseif (!$this->onlyPreview && $isPassed) {
            $meta[$this->user->ID][$this->postId] = true;
        }

        update_term_meta($termId, 'wmp_term_progress', $meta);
    }
}
