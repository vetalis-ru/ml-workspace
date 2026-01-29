Тип: Кастомный плагин, State Manager
Зависимости: MemberLux Core, MBL Certificates (Official Plugin).
Назначение: Управление пошаговыми программами ("сборные курсы"). Автоматически выдает следующий уровень доступа (term_id) в цепочке после того, как пользователь получил сертификат за предыдущий шаг.

1. МОДЕЛЬ ДАННЫХ
Плагин вводит собственную структуру данных поверх WordPress.

1.1. Custom Post Type: ml_program

Назначение: Объект "Программа", содержащий цепочку шагов.

Мета-поле mlp_steps: Сериализованный массив шагов.

php
// СТРУКТУРА МАССИВА mlp_steps
$steps = [
    [
        'term_id'  => 25,   // ID уровня доступа (wpm-levels term)
        'duration' => 30,   // Длительность доступа для этого шага
        'units'    => 'day' // Единицы измерения (day, month, year)
    ],
    [
        'term_id'  => 26,
        'duration' => 1,
        'units'    => 'month'
    ],
    // ... следующие шаги
];
1.2. User Meta (Прогресс пользователя):
Для отслеживания прогресса каждого пользователя по программе используются ключевые мета-поля:

mlp_program_id - (int) ID программы (ml_program CPT), в которой участвует пользователь.

mlp_current_step - (int) Текущий индекс шага в массиве mlp_steps (начинается с 0).

mlp_last_certificate_hash - (string) Уникальный хеш последнего обработанного сертификата. Критично для идемпотентности.

2. АРХИТЕКТУРА И БУТСТРАП
php
// ГЛАВНЫЙ ФАЙЛ ПЛАГИНА: ml-bundle-courses.php
require_once __DIR__ . '/includes/class-mlp-program-cpt.php';      // Регистрация CPT `ml_program`
require_once __DIR__ . '/includes/class-mlp-enrollment.php';       // Логика привязки пользователя к программе
require_once __DIR__ . '/includes/class-mlp-enrollment-admin.php'; // UI для ручного управления
require_once __DIR__ . '/includes/class-mlp-certificate-hook.php'; // ГЛАВНЫЙ ОБРАБОТЧИК - реагирует на сертификаты
require_once __DIR__ . '/includes/class-mlp-notifier.php';         // Уведомления
require_once __DIR__ . '/includes/class-mlp-logger.php';           // Логирование
require_once __DIR__ . '/includes/class-mlbc-admin-notices.php';   // Админ-уведомления

// ВАЖНО: Инициализация ключевых обработчиков ПРИВЯЗАНА К СОБЫТИЮ ВЫДАЧИ СЕРТИФИКАТА.
// Это lazy-load подход: логика плагина активируется только когда в системе выдают первый сертификат.
add_action('mbl_certificate_issued', ['MLP_Program_CPT', 'register']);
add_action('mbl_certificate_issued', ['MLP_Enrollment_Admin', 'register']);
add_action('mbl_certificate_issued', ['MLBC_Admin_Notices', 'register']);
// ГЛАВНЫЙ ТРИГГЕР: Обработчик, который продвигает пользователя по программе.
add_action('mbl_certificate_issued', ['MLP_Certificate_Hook', 'handle'], 10, 2);
3. АЛГОРИТМ РАБОТЫ ГЛАВНОГО ОБРАБОТЧИКА
Класс: MLP_Certificate_Hook, метод handle(int $user_id, int $certificate_id)

php
// ПСЕВДОКОД АЛГОРИТМА
1.  ПОЛУЧИТЬ ДАННЫЕ СЕРТИФИКАТА:
    $cert = Certificate::getCertificate($certificate_id);
    $cert_term_id = $cert->wpmlevel_id; // Уровень, за который выдан сертификат
    $cert_hash = md5($user_id . '_' . $certificate_id . '_' . $cert_term_id);

2.  ПРОВЕРИТЬ ИДЕМПОТЕНТНОСТЬ:
    if (get_user_meta($user_id, 'mlp_last_certificate_hash', true) === $cert_hash) {
        return; // Этот сертификат уже обрабатывался.
    }

3.  НАЙТИ АКТИВНУЮ ПРОГРАММУ ПОЛЬЗОВАТЕЛЯ:
    $program_id = get_user_meta($user_id, 'mlp_program_id', true);
    $current_step_index = get_user_meta($user_id, 'mlp_current_step', true);

4.  ВАЛИДАЦИЯ ШАГА:
    $program_steps = get_post_meta($program_id, 'mlp_steps', true);
    $current_step_term_id = $program_steps[$current_step_index]['term_id'] ?? null;

    if ($current_step_term_id != $cert_term_id) {
        // Выданный сертификат не соответствует текущему ожидаемому шагу программы.
        // Возможно, это manual сертификат или ошибка. Залогировать и выйти.
        return;
    }

5.  ВЫДАТЬ СЛЕДУЮЩИЙ УРОВЕНЬ ДОСТУПА:
    $next_step_index = $current_step_index + 1;
    if (isset($program_steps[$next_step_index])) {
        $next_step = $program_steps[$next_step_index];
        // КАНОНИЧЕСКАЯ ВЫДАЧА ЧЕРЕЗ CORE:
        $new_key = wpm_insert_one_user_key($next_step['term_id'], $next_step['duration'], $next_step['units']);
        wpm_update_user_key_dates($user_id, $new_key, false, 'bundle_courses_progression');

        // ОБНОВИТЬ ПРОГРЕСС ПОЛЬЗОВАТЕЛЯ
        update_user_meta($user_id, 'mlp_current_step', $next_step_index);
    } else {
        // ПРОГРАММА ЗАВЕРШЕНА
        delete_user_meta($user_id, 'mlp_program_id');
        delete_user_meta($user_id, 'mlp_current_step');
    }

6.  ОБНОВИТЬ ХЕШ И ВЫЗВАТЬ КАСТОМНЫЙ ХУК:
    update_user_meta($user_id, 'mlp_last_certificate_hash', $cert_hash);
    do_action('mlp_program_step_granted', $user_id, $program_id, $cert_term_id, $next_step['term_id'] ?? null);
4. WORDPRESS HOOKS
Действия (Actions), предоставляемые плагином:

php
// КАСТОМНЫЙ ХУК ДЛЯ ИНТЕГРАЦИИ.
// Вызывается после успешной выдачи следующего шага в программе.
// Параметры: ID пользователя, ID программы, term_id завершенного шага, term_id выданного шага (или null, если программа завершена).
do_action('mlp_program_step_granted', int $user_id, int $program_id, int $completed_term_id, ?int $granted_term_id);
5. КРИТИЧЕСКИЕ ПРИНЦИПЫ РАЗРАБОТКИ
Идемпотентность: Обеспечивается хешированием сертификата (mlp_last_certificate_hash). Один сертификат не должен запускать выдачу доступа дважды.

Валидация шага: Плагин проверяет, что выданный сертификат соответствует текущему ожидаемому шагу пользователя в программе. Это защищает от случайных manual-сертификатов.

Auto vs Manual: Алгоритм не различает AUTO и MANUAL сертификаты. Если администратор вручную выдал сертификат за текущий шаг — плагин воспримет это как завершение шага и выдаст следующий уровень. Это особенность дизайна.

Зависимость от Certificates Module: Плагин не работает без активного модуля MBL Certificates, так как целиком построен на его хуке mbl_certificate_issued.