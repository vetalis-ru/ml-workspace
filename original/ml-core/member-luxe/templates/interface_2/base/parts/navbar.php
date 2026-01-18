<!-- header -->
<?php if (is_user_logged_in() || wpm_get_option('main.opened')) : ?>
    <?php wpm_render_partial('navbar-full'); ?>
    <?php wpm_render_partial('navbar-mobile'); ?>
    <script type="text/javascript">
        jQuery(function ($) {
            var hash = window.location.hash,
                $loginFull = $('#login-dropdown:visible'),
                $registerFull = $('#registration-dropdown:visible'),
                $mobileMenuButton = $('.mobile-menu-button:first'),
                $mobileLoginButton = $('#mobile-login-button'),
                $mobileRegisterButton = $('#mobile-register-button');
            if (hash === '#registration') {
                if($registerFull.length) {
                    $registerFull.click();
                } else {
                    $mobileMenuButton.click();
                    setTimeout(function () {
                        $mobileRegisterButton.click();
                    }, 0);
                }
            } else if (hash === '#login') {
                if($loginFull.length) {
                    $loginFull.click();
                } else {
                    $mobileMenuButton.click();
                    setTimeout(function () {
                        $mobileLoginButton.click();
                    }, 0);
                }
            }
        });
    </script>
    <?php endif; ?>
<!-- // header -->
