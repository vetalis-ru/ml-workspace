<?php
$user = get_user_by('ID', get_current_user_id());
$isCoach = in_array('coach', $user->roles);
if ($isCoach) {
    $coach = new Coach($user->ID);
    $wpmLevelsOptions = $coach->getAccessingWpmLevels();
} else {
    $wpmLevelsOptions = (new MBLC_WpmLevels())->query();
}
include 'view.php';
