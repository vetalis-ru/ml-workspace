Тип: Базовый плагин (Read-Only Engine)
Версия API: 2.x (совместимость с модулями от 2.3.4)
Назначение: Низкоуровневый движок управления доступом к контенту на основе терминов (wpm-levels) и ключей доступа (Term Keys).

1. ФАЙЛОВАЯ АРХИТЕКТУРА (КЛЮЧЕВЫЕ ФАЙЛЫ)
text
memberlux.php                          # Бутстрап плагина, объявление констант
includes/
├── class-wpm-term-keys.php           # Основная модель работы с ключами доступа
├── class-wpm-term-keys-query.php     # Построитель SQL-запросов для ключей
├── functions/
│   ├── term-keys.php                 # Функции генерации и управления ключами
│   ├── wpm-user.php                  # Функции активации ключа пользователем
│   ├── wpm-register.php              # Функция регистрации пользователя
│   ├── send-user-email.php           # Отправка писем пользователям
│   └── send-registration-email.php   # Отправка писем о регистрации
├── class-mbl-mailer.php              # Унифицированная система отправки email
├── cron/wpm-cron.php                 # Регистрация и логика cron-задач
├── taxonomies/wpm-levels.php         # Регистрация таксономии уровней доступа
└── class-mbl-logger.php              # Канонический класс для логирования
2. СТРУКТУРА БАЗЫ ДАННЫХ
Центральная таблица: {prefix}memberlux_term_keys

sql
-- ЕДИНСТВЕННЫЙ ИСТОЧНИК ИСТИНЫ О ДОСТУПЕ
CREATE TABLE `wp_memberlux_term_keys` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `term_id` int(11) NOT NULL COMMENT 'ID термина таксономии wpm-levels',
  `user_id` int(11) DEFAULT NULL COMMENT 'ID пользователя WP (NULL до активации)',
  `key` varchar(255) NOT NULL COMMENT 'Уникальный ключ доступа (код)',
  `status` varchar(50) DEFAULT 'NEW' COMMENT 'Статус: NEW, USED',
  `date_registered` datetime DEFAULT NULL COMMENT 'Время активации ключа (current_time())',
  `date_start` datetime DEFAULT NULL COMMENT 'Начало доступа',
  `date_end` datetime DEFAULT NULL COMMENT 'Окончание доступа',
  `duration` int(11) DEFAULT NULL COMMENT 'Длительность доступа',
  `units` varchar(20) DEFAULT NULL COMMENT 'Единицы длительности (day, month, year)',
  `is_unlimited` tinyint(1) DEFAULT 0 COMMENT 'Признак бессрочного доступа',
  `is_banned` tinyint(1) DEFAULT 0 COMMENT 'Признак блокировки ключа',
  PRIMARY KEY (`id`),
  KEY `term_id` (`term_id`),
  KEY `user_id` (`user_id`),
  KEY `key` (`key`),
  KEY `date_end` (`date_end`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
3. ОСНОВНЫЕ PHP-ФУНКЦИИ (PUBLIC CONTRACT)
php
// 1. ГЕНЕРАЦИЯ КЛЮЧА (без активации)
// Назначение: Создать ключ доступа для последующей активации.
// Возвращает: Код ключа (string) или false.
function wpm_insert_one_user_key(
    int $term_id,
    int $duration,
    string $units,
    bool $is_unlimited = false
): string|false;

// 2. ПОИСК КЛЮЧА
// Назначение: Получить информацию о ключе по его коду.
// Возвращает: Ассоциативный массив с полями записи, включая 'term_id'.
function wpm_search_key_id(string $code): array;

// 3. АКТИВАЦИЯ КЛЮЧА (ГЛАВНЫЙ ТРИГГЕР)
// Назначение: Привязать ключ к пользователю, установить даты доступа.
// Логика: Устанавливает date_start = current_time(), рассчитывает date_end,
//         проставляет user_id, status='USED'.
// ХУК: Вызывает do_action('wpm_update_user_key_dates', ...).
// Возвращает: bool (успех/неудача).
function wpm_update_user_key_dates(
    int $user_id,
    string $code,
    bool $isBanned = false,
    string $source = ''
): bool;

// 4. РЕГИСТРАЦИЯ ПОЛЬЗОВАТЕЛЯ
// Назначение: Каноническая функция создания пользователя с возможностью активации ключа.
// Логика: Если $user_data['code'] передан, вызывает wpm_update_user_key_dates().
function wpm_register_user(
    array $user_data,
    bool $send_email = true
): int|WP_Error;

// 5. ОТПРАВКА EMAIL
function wpm_send_email_about_new_key(WP_User $user, string $code, int $term_id): bool;
function wpm_send_user_email(int $user_id, string $key, string $email, array $params, array $files = []): bool;
function wpm_send_registration_email(string $recipient_type, ...): bool;
4. WORDPRESS HOOKS (PUBLIC API)
Действия (Actions), предоставляемые ядром:

php
// ГЛАВНЫЙ ТРИГГЕР ДЛЯ ИНТЕГРАЦИЙ.
// Вызывается сразу после успешной активации ключа через wpm_update_user_key_dates().
// Параметры: ID пользователя, ID термина доступа, код ключа, объект ключа, источник активации.
do_action('wpm_update_user_key_dates', int $user_id, int $term_id, string $code, object $key, string $source);
Фильтры (Filters), предоставляемые ядром:

php
// ЕДИНАЯ ТОЧКА ДЛЯ РАБОТЫ С ШОРТКОДАМИ В ПИСЬМАХ.
// Позволяет добавлять или модифицировать замену шорткодов во всех письмах MemberLux.
apply_filters('wpm_user_mail_shortcode_replacement', string $message, int $user_id, array $params);
5. ТАКСОНОМИЯ И МЕТА-ДАННЫЕ
Таксономия: wpm-levels. Все уровни доступа — термины этой таксономии.

Страница редактирования: term.php?taxonomy=wpm-levels&tag_ID={term_id}

Способы хранения мета-данных термина:

Стандартный WP: get_term_meta($term_id, '_meta_key', true);

Через опции (используется в core): get_option("taxonomy_term_{$term_id}");

Важность: Это официальная и единственная точка расширения UI для добавления настроек, связанных с уровнем доступа.

6. CRON-ЗАДАЧИ (ПАТТЕРН)
Файл: includes/cron/wpm-cron.php

Назначение: Эталон реализации периодических фоновых задач (напоминания, проверки статусов).

Принцип: Все cron-процессы должны быть идемпотентны.

7. ЗАВИСИМОСТИ И ОГРАНИЧЕНИЯ
Предоставляет: Низкоуровневый API (ключи, даты, статусы, события). Не содержит логики автоматизаций, напоминаний, multi-step программ.

Не предоставляет:

Встроенный планировщик напоминаний.

Автоматизацию продлений.

State machine для сложных программ.

Защиту от повторных внешних триггеров (идемпотентность — ответственность модуля).

Модель для разработчика: MemberLux Core — read-only low-level access engine. Вся бизнес-логика высшего порядка должна быть реализована в кастомных плагинах, использующих публичные хуки и функции.