Ключевые файлы MemberLux core (must-know)
1. Управление доступами (TERM KEYS)
includes/class-wpm-term-keys.php
includes/class-wpm-term-keys-query.php
includes/functions/term-keys.php
Что внутри:
•	структура и логика таблицы memberlux_term_keys
•	генерация ключей
•	статусы, даты, is_unlimited / is_banned
•	вспомогательные SQL-запросы
➡️ Основа всего, что связано с УД, сроками, продлениями, «сонями».
 
2. Активация ключей и жизненный цикл УД
includes/functions/wpm-user.php
includes/functions/wpm-register.php
Что внутри:
•	wpm_update_user_key_dates()
•	wpm_register_user()
•	вызов do_action('wpm_update_user_key_dates', …)
•	автоматические побочные эффекты
➡️ Критично для:
•	понимания, когда даты ставятся как current_time()
•	где можно/нельзя вмешиваться
•	источника auto-сертификатов
 
3. Email и уведомления
includes/class-mbl-mailer.php
includes/functions/send-user-email.php
includes/functions/send-registration-email.php
Что внутри:
•	wpm_send_email_about_new_key
•	wpm_send_user_email
•	фильтр wpm_user_mail_shortcode_replacement
•	шаблоны, параметры, шорткоды
➡️ Используется напрямую или косвенно всеми плагинами (включая твои).
 
4. Cron и фоновые проверки
includes/cron/wpm-cron.php
(или аналогичный файл, где регистрируются cron-задачи)
Что внутри:
•	напоминания об окончании УД
•	периодические проверки ключей
•	паттерн безопасного cron-запуска
➡️ Эталон для логики ML Learning Monitor (Phase 2–3).
 
5. Taxonomy уровней доступа
includes/taxonomies/wpm-levels.php
Что внутри:
•	регистрация wpm-levels
•	UI терма (edit_form_fields)
•	использование term_meta / option("taxonomy_term_$id")
➡️ Критично для твоего решения «надстройка поверх core».
 
6. Сертификаты (official plugin)
mbl-certificates/includes/class-certificate.php
mbl-certificates/includes/hooks.php (или аналог)
Что внутри:
•	Certificate::create()
•	таблица memberlux_certificate
•	do_action('mbl_certificate_issued', $user_id, $cert_id)
•	auto-логика через wpm_update_user_key_dates
➡️ Единственный канонический источник истины по факту выдачи сертификата.
 
7. Логирование и утилиты
includes/class-mbl-logger.php
includes/helpers/*.php
Что внутри:
•	формат логов
•	куда писать
•	как логируют official-модули
➡️ Важно для консистентности твоих плагинов.
 
Минимальный набор, если времени мало (ТОП-5)
Если читать только самое необходимое:
1.	class-wpm-term-keys.php
2.	functions/wpm-user.php
3.	functions/send-user-email.php
4.	taxonomies/wpm-levels.php
5.	mbl-certificates/class-certificate.php
 
Что НЕ нужно анализировать для надстроек
•	UI админки курсов (кроме wpm-levels)
•	импорты / экспорты
•	платёжные интеграции
•	legacy-модули, не вызывающие public hooks

