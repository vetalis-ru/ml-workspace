Тип: Кастомный плагин-надстройка
Зависимости: WordPress, MemberLux Core (таблица memberlux_term_keys).
Назначение: Мониторинг пользователей с истекшим доступом ("сонь") на уровне термина таксономии wpm-levels. Предоставляет интерфейс для настройки шаблонов напоминающих писем (3 студенту, 1 администратору) и AJAX-таблицу для просмотра "сонь".

1. ФАЙЛОВАЯ АРХИТЕКТУРА (МОНОЛИТ)
text
ml-learning-monitor/
├── ml-learning-monitor.php       # ЕДИНСТВЕННЫЙ PHP-файл плагина (472 строки). Содержит весь код.
├── assets/
│   ├── mlm-admin.js             # Фронтенд-логика: вкладки, видимость, AJAX (83 строки)
│   └── mlm-admin.css            # Стили админ-интерфейса (64 строки)
2. КЛАСС И МЕТОДЫ (ML_LEARNING_MONITOR)
Основной и единственный класс: ML_Learning_Monitor

Константы:

TAXONOMY = 'wpm-levels'

NONCE_ACTION = 'mlm_ajax_nonce'

NONCE_NAME = 'mlm_nonce' (объявлена, но не используется в nonce-поле формы)

PER_PAGE = 20

Методы (публичные):

__construct() – Регистрирует хуки.

enqueue_assets($hook) – Подключает JS/CSS только на term.php?taxonomy=wpm-levels.

render_term_fields($term) – Рендерит весь UI блока плагина на странице редактирования термина.

save_term_fields($term_id, $tt_id) – Сохраняет все настройки из $_POST в term_meta.

ajax_get_sleepers() – Обработчик AJAX wp_ajax_mlm_get_sleepers.

Методы (приватные):

render_student_email_fields($term_id, $index) – Рендерит поля для студенческого письма.

render_admin_email_fields($term_id) – Рендерит поля для письма администратору.

query_sleepers($term_id, $page, $per_page, $date_from, $date_to) – SQL-запрос для поиска "сонь".

render_sleepers_table_html($rows, $total, $page, $per_page, $term_id) – Генерирует HTML таблицы с пагинацией.

3. WORDPRESS HOOKS
php
// ХУКИ, РЕГИСТРИРУЕМЫЕ ПЛАГИНОМ
add_action('wpm-levels_edit_form_fields', [$this, 'render_term_fields'], 20, 1);
add_action('edited_wpm-levels', [$this, 'save_term_fields'], 20, 2);
add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
add_action('wp_ajax_mlm_get_sleepers', [$this, 'ajax_get_sleepers']);
4. META-DATA ТЕРМИНОВ (wp_termmeta)
Все настройки хранятся на уровне термина (term_id). Ключи:

Активация: mlm_enabled (0/1)

Письма студенту (1-3):

mlm_email_{1|2|3}_days (int) – Дней после окончания доступа для отправки.

mlm_email_{1|2|3}_subject (string) – Тема письма.

mlm_email_{1|2|3}_body (text) – Тело письма (поддерживает HTML, wp_kses_post).

Письмо администратору:

mlm_admin_days_after_last (int) – Дней после последнего студенческого письма.

mlm_admin_email (string) – Email адресата.

mlm_admin_subject (string) – Тема.

mlm_admin_body (text) – Тело.

Отображаемые шорткоды (замена НЕ реализована в этой версии):
[user_email], [user_login], [course_name], [expired_date], [is_bundle_course], [bundle_name].

5. AJAX ENDPOINT
Действие: wp_ajax_mlm_get_sleepers

Метод: POST

Проверки: manage_options, nonce (mlm_ajax_nonce).

Параметры:

term_id (обязательный) – ID термина wpm-levels.

page – Номер страницы пагинации.

date_from, date_to – Диапазон дат истечения доступа (date_end). По умолчанию — текущая дата.

Ответ (JSON): {success: true, data: {html: string, total: int, page: int}}

6. SQL-ЗАПРОС ДЛЯ ПОИСКА "СОНЬ" (query_sleepers)
Алгоритм (псевдокод):

Найти для каждого пользователя (user_id) его последний по дате окончания (date_end) ключ доступа для заданного term_id.

Отфильтровать записи, где:

is_banned = 0

is_unlimited = 0

date_end попадает в запрошенный диапазон [date_from, date_to].

Исключить пользователей, у которых есть другой активный ключ для этого же term_id (т.е. is_unlimited=1 или date_end >= date_to).

Вернуть user_id, user_email, имена, даты выдачи/окончания последнего ключа. Поле reminders_sent всегда равно 0 (логика отправки не реализована).

Критическое ограничение (Phase 1): Запрос НЕ проверяет наличие выданных сертификатов (memberlux_certificate). Пользователь с истекшим доступом, но получивший сертификат, все равно будет отображен как "соня".

7. БЕЗОПАСНОСТЬ И САНИТАЦИЯ
Права: Все операции требуют manage_options.

Nonce: Для сохранения настроек используется уникальный mlm_save_nonce на термин. Для AJAX – общий mlm_ajax_nonce.

Санитизация:

Текстовые поля: sanitize_text_field()

Email: sanitize_email()

HTML-тело письма: wp_kses_post()

Числа: (int)

Даты: Регулярное выражение /^\d{4}-\d{2}-\d{2}$/

Экранирование вывода: Используется esc_html(), esc_attr(), esc_textarea().

8. ФРОНТЕНД (JS/CSS)
jQuery UI Tabs: Для переключения между настройками писем.

Показать/Скрыть блок: Весь UI монитора скрыт, если чекбокс mlm_enabled выключен.

AJAX-загрузка таблицы: По кнопке "Показать сонь" с фильтрацией по датам.

Пагинация: Кнопки "Вперед/Назад" в таблице.

9. СОСТОЯНИЕ РАЗРАБОТКИ (PHASE 1)
✅ Реализовано:

UI интеграция с wpm-levels.

Сохранение настроек писем.

SQL-запрос и AJAX-таблица "сонь" с пагинацией.

Базовая безопасность.

⚠️ НЕ реализовано (ожидается в следующих Phase):

Отправка реальных писем.

Cron-задачи для автоматической отправки.

Логирование отправок.

Проверка наличия сертификатов у "сонь".

Замена шорткодов в письмах.

Отслеживание количества отправленных напоминаний (reminders_sent всегда 0).