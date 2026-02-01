MemberLux — Core и официальные модули (полная архитектура)

АРХИТЕКТУРА MEMBERLUX CORE

1) КЛЮЧЕВАЯ МОДЕЛЬ ДОСТУПА (TERM KEYS):
•	ТАБЛИЦА `MEMBERLUX_TERM_KEYS` (TERM_ID, USER_ID, KEY, STATUS, DATE_START, DATE_END, IS_UNLIMITED, IS_BANNED И Т.Д.).
•	ГЕНЕРАЦИЯ КЛЮЧА: `WPM_INSERT_ONE_USER_KEY($TERM_ID, $DURATION, $UNITS, $IS_UNLIMITED=FALSE)` — СОЗДАЁТ КЛЮЧ СО СТАТУСОМ `NEW`.
•	ПОЛУЧЕНИЕ КЛЮЧА/ID: `WPM_SEARCH_KEY_ID($CODE)` → ВОЗВРАЩАЕТ `KEY_INFO` И `TERM_ID`.
•	АКТИВАЦИЯ КЛЮЧА (ПРИСВОЕНИЕ ПОЛЬЗОВАТЕЛЮ): `WPM_UPDATE_USER_KEY_DATES($USER_ID, $CODE, $ISBANNED=FALSE, $SOURCE='')`.
* ЭТА ФУНКЦИЯ ПРОСТАВЛЯЕТ `DATE_START/END`, `STATUS=USED`, `USER_ID`.
* ПОСЛЕ ЭТОГО ВЫЗЫВАЕТ ACTION `DO_ACTION('WPM_UPDATE_USER_KEY_DATES', $USER_ID, $TERM_ID, $CODE, $KEY, $SOURCE)`.

2) РЕГИСТРАЦИЯ ПОЛЬЗОВАТЕЛЯ:
•	КАНОНИЧЕСКИЙ ПУТЬ: `WPM_REGISTER_USER(...)`.
•	ВАЖНО: КЛЮЧ АКТИВИРУЕТСЯ ВНУТРИ `WPM_REGISTER_USER` ТОЛЬКО ЕСЛИ ПЕРЕДАН `USER_DATA['CODE']`.
•	`WPM_REGISTER_USER(SEND_EMAIL=TRUE)` ОТПРАВЛЯЕТ ПИСЬМО РЕГИСТРАЦИИ АДМИНУ + ПОЛЬЗОВАТЕЛЮ.

3) EMAIL:
•	`WPM_SEND_EMAIL_ABOUT_NEW_KEY($USER, $CODE, $TERM_ID)` — ПИСЬМО О ВЫДАЧЕ УД (NEW KEY).
•	`WPM_SEND_REGISTRATION_EMAIL('USER'|'ADMIN', ...)` — ПИСЬМО РЕГИСТРАЦИИ.
•	ФИЛЬТР: `WPM_USER_MAIL_SHORTCODE_REPLACEMENT` ПРИМЕНЯЕТСЯ ПРИ ОТПРАВКЕ.

4) ЛОГ СОБЫТИЙ:
•	`WPM_UPDATE_USER_KEY_DATES` ACTION ИСПОЛЬЗУЕТСЯ DOWNSTREAM (НАПРИМЕР, ПОДПИСКИ, КЛЮЧЕВАЯ МЕТА `MEMBERLUX_KEYS_META`).

МОДУЛЬ "MBL CERTIFICATES"

1) ПОДКЛЮЧЕНИЕ МОДУЛЯ
- ВКЛЮЧАЕТСЯ ЧЕРЕЗ MBLCERTCORE, ЕСЛИ:
* УСТАНОВЛЕН MEMBERLUX;
* ВЕРСИЯ CORE >= 2.3.4;
* АКТИВЕН КЛЮЧ `FULLPACKACCESS` ИЛИ `CERIFICATES`.

2) ТАБЛИЦЫ БД:
•	`WP_MEMBERLUX_CERTIFICATE_TEMPLATES`
•	`WP_MEMBERLUX_CERTIFICATE`

3) ГЛАВНАЯ СУЩНОСТЬ:
•	`CERTIFICATE::CREATE(...)`
•	ВЫЗЫВАЕТ: `DO_ACTION("MBL_CERTIFICATE_ISSUED", $USER_ID, $CERT_ID)`.

4) ВАЖНЫЙ HOOK ДЛЯ ИНТЕГРАЦИИ:
•	`MBL_CERTIFICATE_ISSUED($USER_ID, $CERT_ID)`.

5) ПОЛУЧЕНИЕ ДАННЫХ СЕРТИФИКАТА:
•	`CERTIFICATE::GETCERTIFICATE($CERT_ID)`
* `USER_ID`
* `WPMLEVEL_ID`

6) АВТОМАТИЧЕСКАЯ ВЫДАЧА СЕРТИФИКАТОВ:
•	HOOK `ADD_ACTION('WPM_UPDATE_USER_KEY_DATES', 'MBLC_CERTIFICATE_ISSUANCE_AFTER_UPDATE_USER_KEY_DATES', ...)`
* META `_MBLC_HOW_TO_ISSUE` = 'AUTO'.

МОДУЛЬ MBL AUTO REGISTRATION

1) УСЛОВИЯ ВКЛЮЧЕНИЯ И ЛИЦЕНЗИЯ
•	MEMBERLUX CORE ≥ 2.5.9
•	КЛЮЧ FULLPACKACCESS ИЛИ AUTOREG

2) ПУБЛИЧНЫЙ ENTRY POINT
•	URL: WPMA/JOIN/:HASH/{EMAIL}
•	QUERY VARS: MBLR_HASH, MBLR_EMAIL

3) ОСНОВНОЙ FLOW (AJAX)
•	ЧТЕНИЕ НАСТРОЕК ИЗ `WPM_MAIN_OPTIONS.MBLR_AUTO_REGISTRATION.{HASH}`
•	`WPM_INSERT_ONE_USER_KEY`
•	`WPM_SEARCH_KEY_ID`
•	`WPM_REGISTER_USER(SOURCE=AUTO_REGISTRATION, SEND_EMAIL=TRUE)`

4) ADMIN UI
•	ВКЛАДКА НАСТРОЕК
•	AJAX: `WP_AJAX_MBLR_GET_AUTO_REG_LINK`

МОДУЛЬ MBL UNIVERSAL ACCESS

1) УСЛОВИЯ ВКЛЮЧЕНИЯ
•	MEMBERLUX ≥ 2.9.9
•	КЛЮЧ FULLPACKACCESS ИЛИ UACCESS

2) СИСТЕМНЫЕ ОПЦИИ
•	PIN КОД
•	РЕДИРЕКТЫ
•	ТЕКСТЫ
•	SHORTCODES

3) ОСНОВНЫЕ HOOKS
•	`WPM_AJAX_REGISTER_USER_FORM_FILTER`
•	`USER_REGISTER`
•	`WC_GET_TEMPLATE`

4) ГЕНЕРАЦИЯ ДОСТУПА
•	`WPM_INSERT_ONE_USER_KEY`
•	`WPM_SEARCH_KEY_ID`
•	`WPM_REGISTER_USER(SOURCE=AUTO_REGISTRATION)`

МОДУЛЬ MBL AUTO RESPONDER

1) УСЛОВИЯ ВКЛЮЧЕНИЯ
•	MEMBERLUX ≥ 2.3.4
•	КЛЮЧ FULLPACKACCESS ИЛИ AUTO-RESPONDER

2) ТАБЛИЦЫ И CRON
•	MEMBERLUX_MAILING
•	MEMBERLUX_MAILING_TEMPLATES
•	MEMBERLUX_MAILING_RESULTS
•	MEMBERLUX_KEYS_META
•	MEMBERLUX_MAILING_LIST
•	MEMBERLUX_USER_MAILING_LIST
•	CRON: MBL_AUTO_RESPONDER_EVENT (60 сек)

3) АКТИВАЦИЯ КЛЮЧА → РАССЫЛКА
•	`WPM_MBL_TERM_KEY_UPDATED`
•	`WPM_MBL_TERM_KEYS_QUERY_UPDATED`
•	`MBLAR_ACTIVATE_KEY`
•	`ACTIVATEDKEY::ACTIVATE`
•	`USERSUBSCRIPTIONS::SUBSCRIBE`

4) CRON ОТПРАВКА
•	`MBL_AUTO_RESPONDER_CRON`
•	`MBLAR_CALC_TIME`
•	`MBLMAILER`

МОДУЛЬ MBL API

1) УСЛОВИЯ ВКЛЮЧЕНИЯ
•	MEMBERLUX ≥ 2.73
•	КЛЮЧ FULLPACKACCESS ИЛИ API

2) ТАБЛИЦЫ WEBHOOKS
•	MEMBERLUX_HOOK
•	MEMBERLUX_HOOK_ACTION

3) REST /ORDER
•	`/WP-JSON/MBL/V1/ORDER/`
•	CUSTOMER::WPUSER

4) WEBHOOK ПОДСИСТЕМА
•	ОСНОВА: `WPM_UPDATE_USER_KEY_DATES`
•	`WP_REMOTE_POST`

5) REST УПРАВЛЕНИЕ
•	`/WEBHOOK/FORM/`
•	`/WEBHOOK/{ID}`
•	`/WEBHOOK/ACTION`

6) ADMIN UI
•	ВКЛАДКА WEBHOOKS (MBL_OPTIONS)

