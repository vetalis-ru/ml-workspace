
Project Owner: Vetalis
Stack: WordPress / PHP / MemberLux
Status: Active development
Scope: Custom plugins extending MemberLux core (read-only)
 
0. Базовые принципы (обязательно)
•	MemberLux core и official-плагины — read-only.
Любые изменения реализуются ТОЛЬКО через кастомные плагины.
•	Используются только публичные контракты: функции, хуки, структуры данных.
•	Никаких правок core, никаких monkey-patch.
•	Все кастомные плагины проектируются как надстройки, безопасные при обновлении ML.
 
1. MemberLux Core — архитектура (концентрированно)
1.1 Модель доступа (TERM KEYS)
•	Таблица: {$wpdb->prefix}memberlux_term_keys
•	Ключевые поля:
term_id, user_id, key, status,
date_start, date_end, date_registered,
is_unlimited, is_banned, duration, units
Канонический поток:
1.	wpm_insert_one_user_key() — генерирует ключ (без дат)
2.	wpm_update_user_key_dates() — активирует ключ:
o	выставляет date_start = current_time()
o	считает date_end
o	пишет user_id, status=USED
o	вызывает do_action('wpm_update_user_key_dates', ...)
⚠️ Важно: даты всегда ставятся в момент активации, не генерации.
 
1.2 Регистрация пользователя
•	Каноническая функция: wpm_register_user()
•	Ключ активируется только если передан USER_DATA['CODE']
•	При send_email = true отправляет:
o	письмо пользователю
o	письмо администратору
 
1.3 Email-система
Основные функции:
•	wpm_send_email_about_new_key()
•	wpm_send_user_email()
•	wpm_send_registration_email()
Фильтр:
•	wpm_user_mail_shortcode_replacement — единая точка замены шорткодов.
 
1.4 Cron в core
•	В core есть cron-логика напоминаний об окончании УД.
•	Используется как эталон паттерна (без копирования кода).
•	Все cron-процессы должны быть идемпотентны.
 
1.5 Taxonomy уровней доступа
•	УД = терм таксономии wpm-levels
•	Страница редактирования: term.php?taxonomy=wpm-levels&tag_ID=…
•	Core уже использует:
o	*_edit_form_fields
o	term_meta / option("taxonomy_term_$id")
➡️ Это официальная точка расширения UI.
 
2. Official plugin: mbl-certificates
2.1 Таблицы
•	{$wpdb->prefix}memberlux_certificate
•	{$wpdb->prefix}memberlux_certificate_templates
2.2 Ключевая логика
•	Certificate::create()
→ do_action('mbl_certificate_issued', $user_id, $cert_id)
2.3 Получение данных сертификата
•	Certificate::getCertificate($cert_id)
o	user_id
o	wpmlevel_id (term_id)
2.4 AUTO vs MANUAL
•	Auto-сертификаты:
o	триггерятся из wpm_update_user_key_dates
o	meta _MBLC_HOW_TO_ISSUE = 'AUTO'
•	Для кастомной логики учитываются только MANUAL.
 
3. Кастомные плагины проекта
 
3.1 ml-grant-bridge
Назначение:
Выдача УД из внешних систем (amoCRM и др.).
Архитектура:
•	REST endpoint /mlgb/v1/grant
•	Принимает webhook → активирует ключ
•	Использует:
o	wpm_insert_one_user_key
o	wpm_update_user_key_dates
Особенности:
•	Строгая идемпотентность
•	Без правок core
•	Логирование собственное
 
3.2 ml-bundle-courses
Назначение:
Пошаговые «сборные курсы» (programs).
Модель данных:
•	post_type: ml_program
•	post_meta: mlp_steps — цепочка term_id + duration
•	user_meta:
o	mlp_program_id
o	mlp_current_step
o	mlp_last_certificate_hash
Ключевой триггер:
•	mbl_certificate_issued
•	Проверка шага → выдача следующего УД
Custom hook:
•	mlp_program_step_granted($user_id, $program_id, $current_term_id, $next_term_id)
 
3.3 ML Learning Monitor (текущий проект)
Назначение:
Мониторинг «сонь» — пользователей, у которых:
•	УД истёк
•	Сертификат не был выдан вручную
•	Продление не оформлено
 
4. ML Learning Monitor — текущая реализация
4.1 UI-подход
•	Надстройка над core-UI, не отдельное меню
•	Расширяет страницу редактирования wpm-levels
•	Активируется чекбоксом:
o	mlm_enabled (term_meta)
4.2 Настройки (term_meta)
•	3 письма пользователю:
o	mlm_email_{1..3}_days
o	mlm_email_{1..3}_subject
o	mlm_email_{1..3}_body
•	Письмо администратору:
o	mlm_admin_days_after_last
o	mlm_admin_email
o	mlm_admin_subject
o	mlm_admin_body
4.3 Просмотр «сонь»
•	Кнопка «Показать сонь»
•	AJAX-запрос
•	Пагинация (20 на страницу)
•	Таблица формируется только по клику
4.4 Текущая проблема (известная)
•	Выборка «сонь» не исключает пользователей с вручную выданным сертификатом
•	Требуется:
o	проверка через memberlux_certificate
o	фильтрация по term_id
o	игнор AUTO-сертификатов
 
5. Правила разработки (обязательные)
1.	Не менять код, не относящийся к текущей задаче.
2.	Любые доп. улучшения:
o	сначала план
o	затем согласие владельца
3.	Правки:
o	формат было / стало
o	либо полный файл, если >2 блоков
4.	Принятие:
o	после «ok ok» изменения считаются базой
5.	Никаких GitHub-операций без прямого запроса.
 
6. Что должен сделать следующий разработчик / Codex
1.	Исправить SQL-логику выборки «сонь»:
o	исключить пользователей с MANUAL-сертификатом по тому же term_id
2.	Проверить корректность:
o	продлений
o	AUTO-сертификатов
3.	Предложить оптимизацию:
o	SQL
o	индексы
o	lazy-загрузку
4.	Подготовить Phase 2:
o	cron-логика
o	отправка писем
o	хранение истории напоминаний
 
7. Ключевая мысль
MemberLux = state machine на term keys.
Все надстройки должны уважать этот контракт.

