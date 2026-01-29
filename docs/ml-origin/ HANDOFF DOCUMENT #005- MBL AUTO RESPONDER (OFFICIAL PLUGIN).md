Тип: Официальный модуль MemberLux
Зависимости: MemberLux Core версии >= 2.3.4, лицензионный ключ fullpackaccess или auto-responder.
Назначение: Создание автоматических цепочек писем (автоворонок), привязанных к активации конкретного уровня доступа (term_id). Рассылка происходит по расписанию относительно даты активации ключа пользователем (date_registered).

1. СТРУКТУРА БАЗЫ ДАННЫХ
Модуль расширяет схему БД несколькими служебными таблицами:

sql
-- 1. ШАБЛОНЫ РАССЫЛОК (воронки)
CREATE TABLE `wp_memberlux_mailing_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `term_id` int(11) NOT NULL COMMENT 'ID уровня доступа, к которому привязана воронка',
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `term_id` (`term_id`)
);

-- 2. ПИСЬМА ВНУТРИ ВОРОНКИ
CREATE TABLE `wp_memberlux_mailing` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `template_id` int(11) NOT NULL,
  `send_after_days` int(11) NOT NULL COMMENT 'Дней относительно активации доступа',
  `subject` text,
  `body` longtext,
  `sort_order` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `template_id` (`template_id`)
);

-- 3. РЕЗУЛЬТАТЫ ОТПРАВКИ (лог)
CREATE TABLE `wp_memberlux_mailing_results` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `mailing_id` int(11) NOT NULL COMMENT 'Ссылка на wp_memberlux_mailing.id',
  `scheduled_at` datetime DEFAULT NULL,
  `sent_at` datetime DEFAULT NULL,
  `status` varchar(50) DEFAULT 'pending' COMMENT 'pending, sent, failed',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `mailing_id` (`mailing_id`),
  KEY `status_scheduled` (`status`, `scheduled_at`)
);

-- 4. МЕТА-ДАННЫЕ КЛЮЧЕЙ (для хранения данных подписки)
CREATE TABLE `wp_memberlux_keys_meta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `term_key_id` int(11) NOT NULL COMMENT 'Ссылка на memberlux_term_keys.id',
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext,
  PRIMARY KEY (`id`),
  KEY `term_key_id` (`term_key_id`),
  KEY `meta_key` (`meta_key`)
);

-- 5. ОБЩИЕ СПИСКИ РАССЫЛОК И ПРИВЯЗКА ПОЛЬЗОВАТЕЛЕЙ
CREATE TABLE `wp_memberlux_mailing_list` (...);
CREATE TABLE `wp_memberlux_user_mailing_list` (...);
2. МЕХАНИЗМ АКТИВАЦИИ: ОТ КЛЮЧА ДО ПОДПИСКИ
Модуль подписывается на главное событие MemberLux Core для запуска логики подписки.

php
// ГЛАВНЫЕ ВХОДНЫЕ ТОЧКИ (хуки Core)
add_action('wpm_update_user_key_dates', 'mblar_activate_key_subscription', 10, 5);
add_action('wpm_mbl_term_key_updated', 'mblar_activate_key_subscription', 10, 2);
add_action('wpm_mbl_term_keys_query_updated', 'mblar_activate_key_subscription', 10, 2);

// ЛОГИКА ОБРАБОТЧИКА (mblar_activate_key_subscription):
// 1. Получает $term_id и $user_id из данных хука.
// 2. Проверяет, существует ли активная воронка (mailing template) для этого $term_id.
// 3. Если да, создает записи-задачи в wp_memberlux_mailing_results для КАЖДОГО письма в воронке.
//    - calculated_send_date = date_registered + send_after_days
//    - status = 'pending'
// 4. Записывает факт подписки в wp_memberlux_keys_meta.
3. CRON-СИСТЕМА РАССЫЛКИ
php
// СОБЫТИЕ И ИНТЕРВАЛ
add_action('mbl_auto_responder_event', 'mbl_auto_responder_cron_handler');
add_filter('cron_schedules', 'mblar_add_cron_interval'); // Добавляет интервал, напр. каждые 60 сек.

// ЛОГИКА CRON-ОБРАБОТЧИКА (mbl_auto_responder_cron_handler):
// 1. Выбирает из wp_memberlux_mailing_results задачи со status='pending' AND scheduled_at <= current_time().
// 2. Для каждой задачи:
//    a) Загружает шаблон письма (subject, body).
//    b) Заменяет шорткоды (через фильтр wpm_user_mail_shortcode_replacement).
//    c) Отправляет письмо через MBL_Mailer (стандартный класс MemberLux).
//    d) Обновляет запись: status='sent', sent_at=current_time().
//    e) Логирует ошибку при неудаче.
4. ИНТЕГРАЦИОННЫЕ ПРИНЦИПЫ
Триггер: Основан на wpm_update_user_key_dates. Любое расширение, активирующее ключ через Core API, автоматически получает функционал автоответчика (если для term_id настроена воронка).

Шорткоды писем: Использует общий фильтр MemberLux wpm_user_mail_shortcode_replacement для рендеринга писем, обеспечивая консистентность.

Idempotency: Обработчик хука wpm_update_user_key_dates должен проверять через memberlux_keys_meta, не был ли пользователь уже подписан на воронку, чтобы избежать дублирования задач.

Отписка: Может реализовываться через установку флага в memberlux_keys_meta или is_banned в основном ключе, что должно обрабатываться cron-задачей.