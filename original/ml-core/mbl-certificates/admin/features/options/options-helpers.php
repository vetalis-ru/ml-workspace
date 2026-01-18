<?php
function mblc_mail_text_with_variables($userId, $cert_id, $auto = false)
{
    $message = mblc_get_option_with_default('cert_mailer_text');
    $replace = $auto
        ? "[m_certificate_pdf ext=\"pdf\" certificate=\"$cert_id\" user_id=\"$userId \"]"
        : "[m_certificate_pdf ext=\"pdf\" certificate=\"$cert_id\"]";
    $return = str_replace(
        "[m_certificate_pdf]",
        do_shortcode($replace),
        str_replace(
            "[user_surname]",
            get_user_meta($userId, 'last_name', true),
            str_replace("[user_name]", get_user_meta($userId, 'first_name', true), $message)
        )
    );

    if (mblc_jpg_enabled()) {
        $replace = m_certificate_pdf(['ext' => 'jpg', 'certificate' => $cert_id]);
        $return = str_replace( "[m_certificate_jpg]", $replace, $return);
    }

    return $return;
}

function mblc_get_option($key, $default = false)
{
    return get_option('mblc_options')[$key] ?? $default;
}

function mblc_update_option($key, $value)
{
    $all_options = get_option('mblc_options');
    $all_options[$key] = $value;
    update_option('mblc_options', $all_options);
}

function mblc_delete_option($key)
{
    $all_options = get_option('mblc_options');
    unset($all_options[$key]);
    update_option('mblc_options', $all_options);
}

function mblc_get_default_text() {
    $text = [
        'download_text' => 'Скачать в PDF',
        'check_text' => 'Проверить',
        'download_auto' => 'Скачивание начнется автоматически',
        'by_fio' => 'По ФИО',
        'field_surname' => 'Фамилия',
        'field_name' => 'Имя',
        'by_serial_number' => 'По серии и номеру',
        'field_serial' => 'Серия',
        'field_number' => 'Номер',
        'not_found' => 'По вашему запросу не найдено ни одного результата. Проверьте пожалуйста правильность введенный'
            .' данных',
        'cert_fio' => 'ФИО',
        'cert_course' => 'Название курса',
        'cert_serial' => 'Серия',
        'cert_number' => 'Номер',
        'cert_date_issuance' => 'Дата выдачи',
        'all_certificates' => 'Список доступных сертификатов',
    ];

    if (mblc_get_option('jpg_enabled', 'off') === 'on') {
        $text['download_text_jpg'] = 'Скачать в JPG';
    }

    return apply_filters('mblc_get_default_text', $text);
}

function mblc_get_option_with_default($key, $default = false)
{
    $mapping = apply_filters('mblc_default_option_values', [
        'btn_text_color' => 'FFFFFF',
        'btn_bg' => 'A0B0A1',
        'cert_mailer_topic' => 'Ваш сертификат доступен для скачивания',
        'cert_mailer_text' => 'Здравствуйте!' . PHP_EOL . PHP_EOL
            . 'Поздравляем с успешным прохождением курса!' . PHP_EOL . PHP_EOL
            . 'Скачать сертификат:' . PHP_EOL
            . '[m_certificate_pdf]',
        'custom_fields_enabled' => 'off',
        'jpg_enabled' => 'off',
    ] + mblc_get_default_text());

    return mblc_get_option($key, $mapping[$key] ?? $default);
}

function mblc_jpg_enabled(): bool {
    return mblc_get_option_with_default( 'jpg_enabled' ) === 'on' && extension_loaded('imagick');
}
