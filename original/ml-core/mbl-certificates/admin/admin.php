<?php
require_once 'fields-config.php';
require_once 'register-admin-pages.php';
require_once 'features/issuance/main.php';
require_once 'features/certificate-templates/main.php';
require_once 'features/certificates-table/main.php';
require_once 'features/options/main.php';
require_once 'features/wpm-levels-certificate-options/main.php';
require_once 'features/view-certificates-in-profiles/main.php';
require_once 'features/certificate-download/main.php';
require_once 'features/fields-presence-check/main.php';
require_once 'features/recaptcha/main.php';
require_once 'features/coach-access/main.php';
require_once 'customer.php';
if (wp_doing_ajax()) {
    require_once 'ajax.php';
}

add_action('admin_notices', [new MLC_AdminNotice(), 'displayAdminNotice']);
