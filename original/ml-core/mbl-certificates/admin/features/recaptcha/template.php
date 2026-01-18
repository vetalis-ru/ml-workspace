<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <title>Проверка</title>
    <script src="https://www.google.com/recaptcha/api.js"></script>
    <style>
        .body {
            font-family: "PT Sans", sans-serif;
            color: #4a4a4a;
            background:#636364;
            height: 100vh;
        }
        .container {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .form {
            max-width: 540px;
            padding-bottom: 200px;
        }

        .g-recaptcha div {
            display: inline-block;
        }
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
        }
    </style>
    <link rel="stylesheet" href="<?= plugins_url('/member-luxe/2_0/bootstrap/css/bootstrap.css') ?>">
</head>
<body class="body">
<div class="container">
    <form action="" method="POST" class="form" id="form">
        <div class="g-recaptcha" data-callback="onClick"
             data-expired-callback="expiredCallback"
             data-error-callback="errorCallback"
             data-sitekey="<?= wpm_get_option('main.captcha_key') ?>"></div>
        <div class="result" style="display: none">
            <h1 class="result__text"><?= mblc_get_option_with_default('download_auto') ?></h1>
        </div>
    </form>
</div>
<script>
    function onClick() {
        document.getElementById('form').submit()
        document.querySelector('.g-recaptcha').style.display = 'none';
        document.querySelector('.result').style.display = '';
    }
</script>
</body>
</html>
