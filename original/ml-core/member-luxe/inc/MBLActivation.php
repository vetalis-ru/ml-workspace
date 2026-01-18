<?php

class MBLActivation
{
    /**
     * @var null
     */
    private $userId;

    /**
     * @var MBLActivationRow[]
     */
    private $rows = [];

    private $activationResult = null;


    /**
     * MBLActivation constructor.
     *
     * @param null $userId
     * @param bool $withoutUpdate
     */
    public function __construct($userId = null, $withoutUpdate = false)
    {
        if ($userId === null) {
            $userId = get_current_user_id();
        }

        if(!$userId) {
            return;
        }

        $this->userId = $userId;

        if (isset($_GET['code']) && !$withoutUpdate) {
            $this->activationResult = wpm_add_key_to_user($this->userId, wpm_array_get($_GET, 'code'), true, 'activation_page');
        }

        $userKeys = MBLTermKeysQuery::find(
            [
                'user_id'   => $userId,
                'is_banned' => 0,
                'key_type'  => 'wpm_term_keys',
            ],
            null,
            ['date_start' => 'desc']
        );

        foreach ($userKeys as $key) {
            $this->rows[] = new MBLActivationRow($key);
        }
    }

    public function getBreadcrumbs()
    {
        return [
            [
                'name' => __('Активация', 'mbl'),
                'link' => wpm_activation_link(),
                'icon' => 'key',
            ],
        ];
    }

    /**
     * @return MBLActivationRow[]
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * @return null
     */
    public function getActivationResult()
    {
        return $this->activationResult;
    }
}
