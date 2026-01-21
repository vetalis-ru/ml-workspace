<?php
/**
 * Plugin Name: ML Learning Monitor
 * Description: MemberLux add-on: monitors expired access without manually issued certificate and sends reminder emails. Adds settings to wpm-levels (term.php) without modifying core.
 * Version: 0.1.0
 * Author: vetalis-ru
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * ============================================================================
 * ML Learning Monitor — Phase 1 foundation
 * ============================================================================
 * ЗАДАЧА PHASE 1:
 *  - Не вмешиваться в core MemberLux и official plugins
 *  - Встроиться в экран редактирования УД (taxonomy wpm-levels) через стандартные WP hooks
 *  - Сохранять настройки напоминаний как term meta (или совместимо с тем, как ML хранит term meta)
 *  - Добавить WP-Cron скелет (пока только логирование "tick")
 *
 * ВАЖНО:
 *  - На Phase 1 НИЧЕГО не отправляем и не меняем доступы — только UI + storage + cron skeleton.
 */

/* =============================================================================
 * Константы / ключи хранения
 * ========================================================================== */

/**
 * Префикс мета-ключей терма (УД).
 * Храним в term meta, но делаем ключи уникальными и "наши".
 */
define('ML_LM_TERM_META_PREFIX', 'ml_lm_');

/**
 * Идентификатор cron-hook события.
 */
define('ML_LM_CRON_HOOK', 'ml_lm_cron_tick');

/**
 * (Опционально) путь к лог-файлу.
 * В repo лучше игнорировать *.log, но на сервере это удобно для дебага.
 * Можно выключить логирование, если файл/папка недоступны.
 */
define('ML_LM_LOG_FILE', WP_CONTENT_DIR . '/uploads/ml-learning-monitor.log');


/* =============================================================================
 * Логгер (простая версия Phase 1)
 * ========================================================================== */

/**
 * Пишет строки лога в файл (если доступно).
 * В Phase 2/3 можно заменить на более продвинутый логгер/настройки.
 *
 * @param string $msg
 * @param array  $ctx
 * @return void
 */
function ml_lm_log(string $msg, array $ctx = []): void
{
    // Чтобы не ломать сайт, если нет прав/папки — максимально "тихо".
    $line = '[' . date('Y-m-d H:i:s') . '] ' . $msg;

    if (!empty($ctx)) {
        $line .= ' | ' . wp_json_encode($ctx, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    $line .= PHP_EOL;

    // suppress errors intentionally (Phase 1)
    @file_put_contents(ML_LM_LOG_FILE, $line, FILE_APPEND);
}


/* =============================================================================
 * Активация/деактивация: планирование cron
 * ========================================================================== */

/**
 * Планируем WP-Cron задачу.
 * Частота: hourly (стандартная), чтобы Phase 1 не спамил и не грузил.
 */
function ml_lm_activate(): void
{
    // Если уже запланировано — не дублируем.
    if (!wp_next_scheduled(ML_LM_CRON_HOOK)) {
        wp_schedule_event(time() + 60, 'hourly', ML_LM_CRON_HOOK);
    }

    ml_lm_log('PLUGIN ACTIVATED', [
        'cron_scheduled' => wp_next_scheduled(ML_LM_CRON_HOOK) ? 'yes' : 'no',
    ]);
}

/**
 * Снимаем WP-Cron задачу.
 */
function ml_lm_deactivate(): void
{
    $ts = wp_next_scheduled(ML_LM_CRON_HOOK);
    if ($ts) {
        wp_unschedule_event($ts, ML_LM_CRON_HOOK);
    }

    ml_lm_log('PLUGIN DEACTIVATED', [
        'cron_unscheduled' => $ts ? 'yes' : 'no',
    ]);
}

register_activation_hook(__FILE__, 'ml_lm_activate');
register_deactivation_hook(__FILE__, 'ml_lm_deactivate');


/* =============================================================================
 * Cron entrypoint (Phase 1 = "tick" only)
 * ========================================================================== */

/**
 * Cron-таск (Phase 1): пока просто логируем факт запуска.
 * В Phase 2 сюда добавится:
 *  - поиск "сонь"
 *  - расчёт расписания уведомлений
 *  - отправка писем и запись состояния
 */
add_action(ML_LM_CRON_HOOK, function (): void {
    ml_lm_log('CRON TICK', [
        'hook' => ML_LM_CRON_HOOK,
        'result' => 'ok',
    ]);
});


/* =============================================================================
 * Admin UI: расширение страницы терма wpm-levels (term.php)
 * ========================================================================== */

/**
 * Рендерит нашу секцию на странице редактирования УД (терма таксономии wpm-levels).
 *
 * Хук: {$taxonomy}_edit_form_fields
 * Для wpm-levels: wpm-levels_edit_form_fields
 *
 * @param WP_Term $term
 * @return void
 */
function ml_lm_render_term_fields($term): void
{
    if (!($term instanceof WP_Term)) {
        return;
    }

    // Только админам
    if (!current_user_can('manage_options')) {
        return;
    }

    // Подтянем значения из term meta (если пусто — дефолты).
    $enabled = (string) get_term_meta($term->term_id, ML_LM_TERM_META_PREFIX . 'enabled', true);
    $enabled = ($enabled === '') ? '0' : $enabled;

    // 3 письма студенту (в Phase 1 — только поля, без реальной отправки)
    $s1_days = (string) get_term_meta($term->term_id, ML_LM_TERM_META_PREFIX . 'student_1_days', true);
    $s1_subj = (string) get_term_meta($term->term_id, ML_LM_TERM_META_PREFIX . 'student_1_subject', true);
    $s1_body = (string) get_term_meta($term->term_id, ML_LM_TERM_META_PREFIX . 'student_1_body', true);

    $s2_days = (string) get_term_meta($term->term_id, ML_LM_TERM_META_PREFIX . 'student_2_days', true);
    $s2_subj = (string) get_term_meta($term->term_id, ML_LM_TERM_META_PREFIX . 'student_2_subject', true);
    $s2_body = (string) get_term_meta($term->term_id, ML_LM_TERM_META_PREFIX . 'student_2_body', true);

    $s3_days = (string) get_term_meta($term->term_id, ML_LM_TERM_META_PREFIX . 'student_3_days', true);
    $s3_subj = (string) get_term_meta($term->term_id, ML_LM_TERM_META_PREFIX . 'student_3_subject', true);
    $s3_body = (string) get_term_meta($term->term_id, ML_LM_TERM_META_PREFIX . 'student_3_body', true);

    // письмо админу
    $admin_days_after_last = (string) get_term_meta($term->term_id, ML_LM_TERM_META_PREFIX . 'admin_days_after_last', true);
    $admin_email           = (string) get_term_meta($term->term_id, ML_LM_TERM_META_PREFIX . 'admin_email', true);
    $admin_subject         = (string) get_term_meta($term->term_id, ML_LM_TERM_META_PREFIX . 'admin_subject', true);
    $admin_body            = (string) get_term_meta($term->term_id, ML_LM_TERM_META_PREFIX . 'admin_body', true);

    // Nonce для сохранения
    $nonce = wp_create_nonce('ml_lm_save_term_' . $term->term_id);

    ?>
    <tr class="form-field">
        <th scope="row" colspan="2">
            <h2><?php echo esc_html__('ML Learning Monitor', 'ml-learning-monitor'); ?></h2>
            <p style="margin: 4px 0 0; color:#555;">
                <?php echo esc_html__('Настройки напоминаний “соням” (Phase 1: только хранение настроек, без отправки).', 'ml-learning-monitor'); ?>
            </p>
        </th>
    </tr>

    <tr class="form-field">
        <th scope="row">
            <label for="ml_lm_enabled"><?php echo esc_html__('Включить мониторинг для этого УД', 'ml-learning-monitor'); ?></label>
        </th>
        <td>
            <input type="hidden" name="ml_lm_nonce" value="<?php echo esc_attr($nonce); ?>" />
            <label>
                <input type="checkbox" id="ml_lm_enabled" name="ml_lm_enabled" value="1" <?php checked($enabled, '1'); ?> />
                <?php echo esc_html__('Да, включить', 'ml-learning-monitor'); ?>
            </label>
        </td>
    </tr>

    <tr class="form-field">
        <th scope="row" colspan="2">
            <h3 style="margin:10px 0 0;"><?php echo esc_html__('Письма студенту (3 шага)', 'ml-learning-monitor'); ?></h3>
        </th>
    </tr>

    <?php
    // Утилита вывода одного блока письма (Phase 1)
    $render_student = function (int $n, $days, $subj, $body) {
        $n_id = (string) $n;
        ?>
        <tr class="form-field">
            <th scope="row"><?php echo esc_html(sprintf('Письмо %d: через N дней после окончания УД', $n)); ?></th>
            <td>
                <input type="number"
                       name="<?php echo esc_attr('ml_lm_student_' . $n_id . '_days'); ?>"
                       value="<?php echo esc_attr($days); ?>"
                       min="0"
                       step="1"
                       style="width:120px;" />
                <p class="description"><?php echo esc_html__('0 = отключено', 'ml-learning-monitor'); ?></p>
            </td>
        </tr>
        <tr class="form-field">
            <th scope="row"><?php echo esc_html(sprintf('Письмо %d: тема', $n)); ?></th>
            <td>
                <input type="text"
                       name="<?php echo esc_attr('ml_lm_student_' . $n_id . '_subject'); ?>"
                       value="<?php echo esc_attr($subj); ?>"
                       class="regular-text" />
            </td>
        </tr>
        <tr class="form-field">
            <th scope="row"><?php echo esc_html(sprintf('Письмо %d: текст', $n)); ?></th>
            <td>
                <textarea name="<?php echo esc_attr('ml_lm_student_' . $n_id . '_body'); ?>"
                          rows="6"
                          class="large-text code"><?php echo esc_textarea($body); ?></textarea>
            </td>
        </tr>
        <?php
    };

    $render_student(1, $s1_days, $s1_subj, $s1_body);
    $render_student(2, $s2_days, $s2_subj, $s2_body);
    $render_student(3, $s3_days, $s3_subj, $s3_body);
    ?>

    <tr class="form-field">
        <th scope="row" colspan="2">
            <h3 style="margin:10px 0 0;"><?php echo esc_html__('Письмо администратору', 'ml-learning-monitor'); ?></h3>
        </th>
    </tr>

    <tr class="form-field">
        <th scope="row"><?php echo esc_html__('Через N дней после последнего письма студенту', 'ml-learning-monitor'); ?></th>
        <td>
            <input type="number"
                   name="ml_lm_admin_days_after_last"
                   value="<?php echo esc_attr($admin_days_after_last); ?>"
                   min="0"
                   step="1"
                   style="width:120px;" />
            <p class="description"><?php echo esc_html__('0 = отключено', 'ml-learning-monitor'); ?></p>
        </td>
    </tr>

    <tr class="form-field">
        <th scope="row"><?php echo esc_html__('Email администратора для уведомлений', 'ml-learning-monitor'); ?></th>
        <td>
            <input type="email"
                   name="ml_lm_admin_email"
                   value="<?php echo esc_attr($admin_email); ?>"
                   class="regular-text" />
        </td>
    </tr>

    <tr class="form-field">
        <th scope="row"><?php echo esc_html__('Тема письма админу', 'ml-learning-monitor'); ?></th>
        <td>
            <input type="text"
                   name="ml_lm_admin_subject"
                   value="<?php echo esc_attr($admin_subject); ?>"
                   class="regular-text" />
        </td>
    </tr>

    <tr class="form-field">
        <th scope="row"><?php echo esc_html__('Текст письма админу', 'ml-learning-monitor'); ?></th>
        <td>
            <textarea name="ml_lm_admin_body"
                      rows="6"
                      class="large-text code"><?php echo esc_textarea($admin_body); ?></textarea>
        </td>
    </tr>

    <tr class="form-field">
        <th scope="row" colspan="2">
            <h4 style="margin:10px 0 0;"><?php echo esc_html__('Шорткоды (Phase 1: список)', 'ml-learning-monitor'); ?></h4>
            <p class="description">
                [user_email], [user_login], [course_name], [term_id], [expired_date], [is_bundle_course], [bundle_name]
            </p>
        </th>
    </tr>
    <?php
}

/**
 * Подключаем рендер на страницу терма wpm-levels.
 */
add_action('wpm-levels_edit_form_fields', 'ml_lm_render_term_fields', 10, 1);


/* =============================================================================
 * Сохранение term settings
 * ========================================================================== */

/**
 * Сохраняет наши поля при сохранении терма wpm-levels.
 *
 * WP hook: edited_{$taxonomy}
 * Для wpm-levels: edited_wpm-levels
 *
 * @param int $term_id
 * @return void
 */
function ml_lm_save_term_fields(int $term_id): void
{
    // Только админам
    if (!current_user_can('manage_options')) {
        return;
    }

    // Проверяем nonce, который мы вставили на форму
    $nonce = isset($_POST['ml_lm_nonce']) ? (string) $_POST['ml_lm_nonce'] : '';
    if (!$nonce || !wp_verify_nonce($nonce, 'ml_lm_save_term_' . $term_id)) {
        return;
    }

    // 1) enabled
    $enabled = isset($_POST['ml_lm_enabled']) ? '1' : '0';
    update_term_meta($term_id, ML_LM_TERM_META_PREFIX . 'enabled', $enabled);

    // 2) Student emails 1..3
    for ($i = 1; $i <= 3; $i++) {
        $days_key = 'ml_lm_student_' . $i . '_days';
        $subj_key = 'ml_lm_student_' . $i . '_subject';
        $body_key = 'ml_lm_student_' . $i . '_body';

        $days = isset($_POST[$days_key]) ? (int) $_POST[$days_key] : 0;
        $subj = isset($_POST[$subj_key]) ? (string) wp_unslash($_POST[$subj_key]) : '';
        $body = isset($_POST[$body_key]) ? (string) wp_unslash($_POST[$body_key]) : '';

        update_term_meta($term_id, ML_LM_TERM_META_PREFIX . 'student_' . $i . '_days', (string) max(0, $days));
        update_term_meta($term_id, ML_LM_TERM_META_PREFIX . 'student_' . $i . '_subject', sanitize_text_field($subj));
        update_term_meta($term_id, ML_LM_TERM_META_PREFIX . 'student_' . $i . '_body', $body);
    }

    // 3) Admin notification
    $admin_days_after_last = isset($_POST['ml_lm_admin_days_after_last']) ? (int) $_POST['ml_lm_admin_days_after_last'] : 0;
    $admin_email           = isset($_POST['ml_lm_admin_email']) ? (string) wp_unslash($_POST['ml_lm_admin_email']) : '';
    $admin_subject         = isset($_POST['ml_lm_admin_subject']) ? (string) wp_unslash($_POST['ml_lm_admin_subject']) : '';
    $admin_body            = isset($_POST['ml_lm_admin_body']) ? (string) wp_unslash($_POST['ml_lm_admin_body']) : '';

    update_term_meta($term_id, ML_LM_TERM_META_PREFIX . 'admin_days_after_last', (string) max(0, $admin_days_after_last));
    update_term_meta($term_id, ML_LM_TERM_META_PREFIX . 'admin_email', sanitize_email($admin_email));
    update_term_meta($term_id, ML_LM_TERM_META_PREFIX . 'admin_subject', sanitize_text_field($admin_subject));
    update_term_meta($term_id, ML_LM_TERM_META_PREFIX . 'admin_body', $admin_body);

    ml_lm_log('TERM SETTINGS SAVED', [
        'term_id' => $term_id,
        'enabled' => $enabled,
    ]);
}

add_action('edited_wpm-levels', 'ml_lm_save_term_fields', 10, 1);


/* =============================================================================
 * (Phase 1) Safety: show basic admin notice after save (optional)
 * ========================================================================== */

/**
 * В Phase 1 не внедряем полноценный admin notice framework,
 * но дадим минимальный след, что данные сохранились (через log).
 * Полноценные success/warn/error/info notices — в Phase 2.
 */
