АРХИТЕКТУРА MEMBERLUX CORE

1) Ключевая модель доступа (Term Keys):
- Таблица `memberlux_term_keys` (term_id, user_id, key, status, date_start, date_end, is_unlimited, is_banned и т.д.).
- Генерация ключа: `wpm_insert_one_user_key($term_id, $duration, $units, $is_unlimited=false)` — создаёт ключ со статусом `new`.
- Получение ключа/ID: `wpm_search_key_id($code)` → возвращает `key_info` и `term_id`.
- Активация ключа (присвоение пользователю): `wpm_update_user_key_dates($user_id, $code, $isBanned=false, $source='')`.
  * Эта функция проставляет `date_start/end`, `status=used`, `user_id`.
  * После этого вызывает action `do_action('wpm_update_user_key_dates', $user_id, $term_id, $code, $key, $source)`.

2) Регистрация пользователя:
- Канонический путь: `wpm_register_user(...)`.
- Важно: ключ активируется внутри `wpm_register_user` ТОЛЬКО если передан `user_data['code']`.
- `wpm_register_user(send_email=true)` отправляет письмо регистрации админу + пользователю.

3) Email:
- `wpm_send_email_about_new_key($user, $code, $term_id)` — письмо о выдаче УД (new key).
- `wpm_send_registration_email('user'|'admin', ...)` — письмо регистрации.
- Фильтр: `wpm_user_mail_shortcode_replacement` применяется при отправке.

4) Лог событий:
- `wpm_update_user_key_dates` action используется downstream (например, подписки, ключевая мета `memberlux_keys_meta`).

=========================
МОДУЛЬ "MBL CERTIFICATES"
=========================

1) Подключение модуля
- Включается через MBLCERTCore, если:
  * установлен MemberLux;
  * версия core >= 2.3.4;
  * активен ключ `fullpackaccess` или `cerificates`.

2) Таблицы БД:
- `wp_memberlux_certificate_templates` — шаблоны сертификатов.
- `wp_memberlux_certificate` — выданные сертификаты.

3) Главная сущность:
- `Certificate::create(...)` — создаёт сертификат, пишет в БД и вызывает:
  `do_action("mbl_certificate_issued", $user_id, $cert_id)`.

4) Важный hook для интеграции:
- `mbl_certificate_issued($user_id, $cert_id)` — главный event выдачи сертификата.
  * Используется для отправки письма (внутри модуля).
  * Идеальная точка для внешнего плагина.

5) Получение данных сертификата:
- `Certificate::getCertificate($cert_id)` возвращает объект, где есть:
  * `user_id`
  * `wpmlevel_id` — ID уровня доступа, за который выдан сертификат.

6) Автоматическая выдача сертификатов:
- В модуле есть hook:
  `add_action('wpm_update_user_key_dates', 'mblc_certificate_issuance_after_update_user_key_dates', ...)`
  * Если у уровня доступа meta `_mblc_how_to_issue` = 'auto', сертификат выдаётся автоматически при активации доступа.

