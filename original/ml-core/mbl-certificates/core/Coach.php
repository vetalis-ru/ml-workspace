<?php


class Coach
{
    public int $id = 0;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function getAccessingWpmLevels(): array
    {
        $userAccess = get_user_meta($this->id, '_mblc_coach_certificate_access', true);
        if (empty($userAccess)) {
            $result = [];
        } else {
            $args = [
                'how_to_issue' => 'employee',
            ];
            if ($userAccess !== 'all') {
                $args['include'] = $userAccess;
            }
            $result = (new MBLC_WpmLevels())->query($args);
        }

        return $result;
    }
}
