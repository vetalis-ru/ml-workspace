<?php

class AutoTrainingAdmin
{
    /**
     * @var MBLCategory
     */
    private $category = null;

    /**
     * @var MBLPage[]
     */
    private $pages;

    /**
     * AutoTrainingAdmin constructor.
     * @param $categoryId
     */
    public function __construct($categoryId)
    {
        $wpCategory = get_term($categoryId);
        if ($wpCategory) {
            $this->category = new MBLCategory($wpCategory, false, false, true);

            $collection = new MBLPageCollection($categoryId);
            $this->pages = $collection->getPages();
        }
    }

    public function render()
    {
        if (!$this->category) {
            wpm_render_partial('autotraining/not-found', 'admin');
        } else {
            wpm_render_partial('autotraining/map', 'admin', array('category' => $this->category, 'pages' => $this->pages));
        }
    }
}