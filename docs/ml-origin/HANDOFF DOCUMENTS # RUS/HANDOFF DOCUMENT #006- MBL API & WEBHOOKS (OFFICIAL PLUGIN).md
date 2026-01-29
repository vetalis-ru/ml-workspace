Тип: Официальный модуль MemberLux
Зависимости: MemberLux Core версии >= 2.73, лицензионный ключ fullpackaccess или api.
Назначение: Предоставление REST API для управления доступами и система вебхуков (Webhooks) для отправки событий MemberLux во внешние системы (AmoCRM, Telegram и др.).

1. REST API ENDPOINTS
Модуль регистрирует собственный REST namespace (/mbl/v1/).

Endpoint: [POST] /wp-json/mbl/v1/order/

Назначение: Канонический endpoint для интеграции с внешними CRM/платежками для выдачи доступа.

Аутентификация: По токену (передается в заголовке Authorization).

Тело запроса (JSON):

json
{
  "customer": {
    "email": "user@example.com",
    "first_name": "John",
    "last_name": "Doe"
  },
  "product": {
    "term_id": 123,
    "duration": 1,
    "units": "month"
  },
  "meta": {
    "order_id": "external_order_456",
    "source": "crm_system"
  }
}
Логика обработки (псевдокод):

php
1.  Валидация и аутентификация.
2.  Поиск пользователя по email или создание нового (через wpm_register_user).
3.  Генерация ключа: wpm_insert_one_user_key($term_id, $duration, $units).
4.  Активация ключа для пользователя: wpm_update_user_key_dates($user_id, $code, false, 'api_order').
5.  Возврат JSON-ответа с данными пользователя и ключа.
Важно: Этот endpoint является официальной точкой интеграции для автоматической выдачи доступа, аналогично внутреннему workflow.

2. СИСТЕМА WEBHOOKS
Модуль добавляет механизм подписки на события MemberLux и их отправки во внешние системы.

2.1. Таблицы БД для управления вебхуками:

sql
-- СПИСОК ЗАРЕГИСТРИРОВАННЫХ WEBHOOK
CREATE TABLE `wp_memberlux_hook` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `url` text NOT NULL COMMENT 'Endpoint внешней системы',
  `event` varchar(100) NOT NULL COMMENT 'Событие MemberLux (напр., wpm_update_user_key_dates)',
  `is_active` tinyint(1) DEFAULT 1,
  `secret` varchar(255) DEFAULT NULL COMMENT 'Секрет для подписи payload',
  PRIMARY KEY (`id`),
  KEY `event` (`event`)
);

-- ЛОГ ОТПРАВОК (ИСТОРИЯ)
CREATE TABLE `wp_memberlux_hook_action` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hook_id` int(11) NOT NULL,
  `payload` longtext COMMENT 'JSON данные, отправленные в webhook',
  `response_code` int(11) DEFAULT NULL,
  `response_body` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `hook_id` (`hook_id`),
  KEY `created_at` (`created_at`)
);
2.2. Механизм работы:

Регистрация события: Администратор в настройках MemberLux указывает URL внешней системы и выбирает событие (например, wpm_update_user_key_dates).

Триггер: При срабатывании выбранного события в коде MemberLux, модуль API перехватывает его:

php
// ПОДПИСКА НА СОБЫТИЕ CORE
add_action('wpm_update_user_key_dates', 'mbl_api_dispatch_webhook', 20, 5);

function mbl_api_dispatch_webhook($user_id, $term_id, $code, $key, $source) {
    // 1. Ищет в wp_memberlux_hook все активные хуки для события 'wpm_update_user_key_dates'.
    // 2. Для каждого хука формирует payload (JSON) с данными: {user_id, term_id, code, source, timestamp}.
    // 3. Отправляет асинхронный POST-запрос (wp_remote_post) на указанный URL.
    // 4. Добавляет запись в лог (wp_memberlux_hook_action) с payload и ответом.
}
Админка: Управление вебхуками через вкладку в настройках MemberLux.

3. ИНТЕГРАЦИОННЫЕ ПРИНЦИПЫ
Расширяемость: Модуль является мостом между внутренними событиями MemberLux и внешним миром. Он не изменяет ядро, а только реагирует на его публичные хуки.

Отказоустойчивость: Отправка вебхуков должна быть асинхронной и устойчивой к ошибкам сети (повторные попытки, логирование).

Безопасность: Payload может подписываться секретным ключом (secret) для проверки целостности на стороне приемника.