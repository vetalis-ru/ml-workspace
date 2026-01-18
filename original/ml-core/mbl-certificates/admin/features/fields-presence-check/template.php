<?php
/**
 * @global $user_id
 * @global $first_name
 * @global $last_name
 */
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="<?= plugins_url('/member-luxe/2_0/bootstrap/css/bootstrap.css') ?>">
    <link rel="stylesheet" href="<?= plugins_url('/member-luxe/2_0/fonts/icomoon/icomoon.css') ?>">
    <style>
        .result {
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #d3d3d3;
            background: #f9f9f9;
            color: #000;
            height: 74px;
            width: 300px;
            border-radius: 3px;
            box-shadow: 0 0 4px 1px rgb(0 0 0 / 8%);
            -webkit-box-shadow: 0 0 4px 1px rgb(0 0 0 / 8%);
            -moz-box-shadow: 0 0 4px 1px rgba(0,0,0,0.08);
        }
        .result__text {
            margin: 0;
            width: 100%;
            text-align: center;
            font-size: 16px;
            font-weight: 400;
            font-family: "PT Sans", sans-serif;
        }
        .g-recaptcha {
            display: flex;
            justify-content: center;
            margin-bottom: 15px;
        }
        #wpadminbar {
            display: none!important;
        }
        body {
            background: #636364!important;
            display: flex;
            display: -ms-flexbox;
            min-height: 100vh;
            flex-direction: column;
            -ms-flex-direction: column;
            padding: 0;
            margin: 0;
            font-size: 1.6rem;
            font-family: "PT Sans", sans-serif;
            color: #4a4a4a;
        }
        .body {
            height: 100vh;
            font-family: "PT Sans", sans-serif;
            color: #4a4a4a;
        }
        .login-tabs {
            margin: 50px auto;
            max-width: 460px;
        }
        .bordered-tabs .tab-content .tab-pane {
            padding: 30px;
            border-radius: 5px;
        }
        .login-tabs .tab-content .tab-pane {
            padding: 50px;
            color: #8f8f8f;
        }
        .bordered-tabs .tab-content {
            position: relative;
            border-radius: 5px;
            background: #fff;
            border: 1px solid #e3e3e3;
        }
        .mbr-btn.btn-default {
            padding: 15px;
            font-size: 1.4rem;
            text-align: center;
            border: none;
        }
        .mbr-btn.btn-solid {
            border: none;
        }
        .mbr-btn.btn-solid.btn-green {
            background: #a0b0a1;
            color: #fff;
        }
        .mbr-btn.btn-solid.btn-green {
            border-radius: 4px;
            background: #A0B0A1;
            color: #FFFFFF;
        }
        form .mbr-btn {
            display: block;
            width: 100%;
        }
        .form-icon input[type=text], .form-icon input[type=password], .form-icon input[type=email] {
            padding-left: 35px;
        }
        .form-group input.form-control, .form-group textarea.form-control, .form-group input[type=text], .form-group input[type=password], .form-group input[type=email] {
            display: block;
            width: 100%;
            height: auto;
            padding: 10px 10px 10px 40px;
            border: 1px solid #e7e7e7;
            line-height: 1.7rem;
            font-size: 1.6rem;
        }
        form input[type=text], form input[type=email], form input[type=password], form textarea {
            border: 0;
            border-radius: 0;
            background: #fff;
            outline: none;
        }
        form input:focus {
            box-shadow: none!important;
        }
        .mbr-btn.btn-solid.btn-green:hover {
            background: #adbead;
            color: #fff;
        }
        .mbr-btn.btn-solid.btn-green:hover:not(:disabled) {
            background: #ADBEAD;
            color: #FFFFFF;
        }
        .ccontainer {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }
        .result-container {
            max-width: 540px;
            padding-bottom: 200px;
        }
        html {
            margin-top: 0!important;
        }
        .form-icon:before {
            display: block;
            position: absolute;
            left: 12px;
            top: 8px;
            color: #b5b5b5;
            font-family: "icomoon";
        }
        .form-icon.form-icon-user:before {
            content: "";
        }
        form .form-group {
            position: relative;
        }
    </style>
    <script src="https://www.google.com/recaptcha/api.js"></script>

</head>
<body class="body">
<div class="container ccontainer" style="display: none">
    <div class="result-container">
        <div class="result">
            <h1 class="result__text"><?= mblc_get_option_with_default('download_auto') ?></h1>
        </div>
    </div>
</div>
<div class="container form-container">
    <div class="row">
        <div class="col-xs-12">
            <div class="login-tabs bordered-tabs tabs-count-2">
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="wpm-login">
                        <div class="login-form">
                            <form class="login" method="post">
                                <p class="text-center" style="margin-bottom: 30px;font-size: 18px;">
                                    <?= mblc_get_option_with_default('presence_check_msg') ?></p>
                                <div class="form-fields-group">
                                    <?php if (empty($first_name)): ?>
                                    <div class="form-group form-icon form-icon-user">
                                        <input type="text" name="first_name" class="form-control"
                                               placeholder="<?= esc_attr(mblc_get_option_with_default('field_name')) ?>"
                                               required="">
                                    </div>
                                    <?php endif; ?>
                                    <?php if (empty($last_name)): ?>
                                    <div class="form-group form-icon form-icon-user">
                                        <input type="text" name="last_name" class="form-control"
                                               placeholder="<?= esc_attr(mblc_get_option_with_default('field_surname')) ?>"
                                               required="">
                                    </div>
                                    <?php endif; ?>
                                    <input type="hidden" name="user_id" value="<?= esc_attr($user_id) ?>">
                                    <input type="hidden" name="action" value="mblc_form_presence_check">
                                </div>
                                <?php do_action("mblc_form_fields_presence_check"); ?>
                                <button type="submit" <?php if (wpm_option_is('main.enable_captcha', 'on')
                                            && wpm_option_is('main.enable_captcha_certificate', 'on')) {
                                            echo 'disabled';
                                        } ?>
                                        class="mbr-btn btn-default btn-solid btn-green text-uppercase wpm-sign-in-button ">
                                    <?= mblc_get_option_with_default('save_user_name_btn') ?>
                                </button>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function onClick() {
        document.querySelector('.wpm-sign-in-button').removeAttribute('disabled')
    }
    function expiredCallback() {
        document.querySelector('.wpm-sign-in-button').setAttribute('disabled', 'disabled')
    }
    function errorCallback() {
        document.querySelector('.wpm-sign-in-button').setAttribute('disabled', 'disabled')
    }
    document.querySelector('.login').addEventListener('submit', submit)
    function submit(event) {
        event.preventDefault();
        document.querySelector('.form-container').style.display = 'none';
        document.querySelector('.ccontainer').style.display = '';
        document.querySelector('.login').submit()
    }
</script>
<?php wp_footer(); ?>
</body>
</html>