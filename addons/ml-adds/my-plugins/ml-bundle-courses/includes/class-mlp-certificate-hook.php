<?php // includes/class-mlp-certificate-hook.php 


class MLP_Certificate_Hook {
    public static function handle($user_id, $cert_id) {
        // 1) Идемпотентность
        $hash = md5($user_id . ':' . $cert_id);
        if (MLP_Enrollment::get_last_cert_hash($user_id) === $hash) {
            return;
        }

        // 2) Получаем сертификат и term_id
        $certificate = Certificate::getCertificate((int)$cert_id);
        $current_term_id = (int)$certificate->wpmlevel_id;

        // 3) Если сертификат авто — критическая ошибка, стоп
        $how = get_term_meta($current_term_id, '_mblc_how_to_issue', true);
        if ($how === 'auto') {
            MLP_Logger::error("AUTO CERTIFICATE DETECTED", [
                'user_id' => $user_id,
                'cert_id' => $cert_id,
                'term_id' => $current_term_id,
            ]);
            return;
        }

        // 4) Проверяем, есть ли программа
        $program_id = MLP_Enrollment::get_program_id($user_id);
        if (!$program_id) {
            return;
        }

        // 5) Получаем steps
        $steps = get_post_meta($program_id, 'mlp_steps', true);
        if (!is_array($steps) || empty($steps)) {
            return;
        }
        $total_steps = count($steps);

        $current_index = array_search($current_term_id, array_column($steps, 'term_id'), true);
        if ($current_index === false) {
            return;
        }

        $next_index = $current_index + 1;
        if (!isset($steps[$next_index])) {
            MLP_Notifier::notify($program_id, $user_id, $current_term_id, 0, [
                'current_step' => $current_index + 1,
                'total_steps' => $total_steps,
            ]);
            MLP_Enrollment::set_last_cert_hash($user_id, $hash);
            return;
        }

        $next_step = $steps[$next_index];
        $next_term_id = (int)$next_step['term_id'];
        $duration = (int)$next_step['duration'];
        $units = $next_step['units'] ?? 'months';

        // 7) Выдача следующего УД (канонический путь ML)
        $code = wpm_insert_one_user_key($next_term_id, $duration, $units, false);
        if (!$code) {
            MLP_Logger::error("KEY GENERATION FAILED", [
                'user_id' => $user_id,
                'term_id' => $next_term_id,
            ]);
            return;
        }

        wpm_update_user_key_dates($user_id, $code, false, 'program_flow');
        self::sync_key_dates_with_certificate($code, $certificate);

        $user = get_user_by('ID', $user_id);
        if ($user) {
            wpm_send_email_about_new_key($user, $code, $next_term_id);
        }

        // 8) Уведомления
        MLP_Notifier::notify($program_id, $user_id, $current_term_id, $next_term_id, [
            'current_step' => $current_index + 1,
            'next_step' => $next_index + 1,
            'total_steps' => $total_steps,
            'duration' => $duration,
            'units' => $units,
        ]);

        // 9) Сохранение состояния + event
        MLP_Enrollment::set_current_step($user_id, $next_index);
        MLP_Enrollment::set_last_cert_hash($user_id, $hash);

        do_action('mlp_program_step_granted', $user_id, $program_id, $current_term_id, $next_term_id);
    }

    private static function sync_key_dates_with_certificate($code, $certificate): void {
        $issue_date = $certificate->date_issue ?? '';
        if (!$issue_date) {
            return;
        }

        $timestamp = strtotime($issue_date);
        if (!$timestamp) {
            return;
        }

        $key = wpm_search_key_id($code);
        if (empty($key['key_info'])) {
            return;
        }

        $duration = $key['key_info']['duration'] ?: 0;
        $units = wpm_array_get($key, 'key_info.units', 'months');
        $key['key_info']['date_start'] = date('d-m-Y', $timestamp);
        $key['key_info']['date_end'] = date('d-m-Y', strtotime("+$duration " . $units, $timestamp));

        MBLTermKeysQuery::updateKey($key['key_info']);
    }
}
