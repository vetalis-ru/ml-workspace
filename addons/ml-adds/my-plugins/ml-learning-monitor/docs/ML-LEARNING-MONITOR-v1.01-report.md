# ML Learning Monitor (ЛМ) — Technical Handoff Report (v0.1.0 snapshot)

> **Important note (accuracy):** this report is based strictly on the code/files provided in this chat (`ml-learning-monitor.php`, `assets/mlm-admin.js`, `assets/mlm-admin.css`) and earlier conversation context. It is a **best-effort reverse engineering** and may differ from your live repo if additional files exist.

---

## 1. Общая информация о проекте

- **Название:** ML Learning Monitor (ЛМ)
- **Назначение:** надстройка над MemberLux: добавляет настройки (на странице редактирования УД `wpm-levels`) и AJAX-таблицу «сонь» (пользователи с истёкшим доступом по term_id) с пагинацией/фильтром по диапазону дат.
- **Версия (в заголовке файла):** 0.1.0
- **WP/PHP:** код использует стандартные API WP (в т.ч. `term_meta`, `wp_ajax_*`, `$wpdb`), ориентировочно **WP 5.8+ / PHP 7.4+**.
- **Интеграции:** MemberLux (таблица `wp_memberlux_term_keys`). В текущем код-снимке **нет** обращения к `wp_memberlux_certificate` и хукам `mbl_certificate_issued` (проверка «сертификат выдан/не выдан» пока не реализована в этом варианте).
- **Статус:** UI + сохранение настроек + AJAX выборка/таблица сонь работают; cron/email отправка отсутствуют.

---

## 2. Архитектура проекта

### 2.1 Файловая структура (по предоставленным файлам)

```text
ml-learning-monitor/
  ml-learning-monitor.php
  assets/
    mlm-admin.js
    mlm-admin.css
` ``

> Если в репозитории есть дополнительные файлы/классы (autoload, includes/ и т.д.) — в этот отчёт они не попали, потому что не были переданы в чат.

### 2.2 Назначение файлов

- **`ml-learning-monitor.php`**
  - Единственный PHP-файл плагина в этом снимке.
  - Регистрирует хуки для расширения формы редактирования термина таксономии `wpm-levels`.
  - Рендерит UI настроек писем (вкладки jQuery UI Tabs) и блок «Сони» (кнопка + контейнер таблицы).
  - Сохраняет настройки в `term_meta`.
  - Реализует AJAX endpoint `wp_ajax_mlm_get_sleepers` и SQL-выборку по `wp_memberlux_term_keys`.

- **`assets/mlm-admin.js`**
  - Инициализация вкладок `#mlm_email_tabs`.
  - Показ/скрытие блока настроек при переключении чекбокса `#mlm_enabled`.
  - AJAX запрос «Показать сонь» + обработка пагинации стрелками.

- **`assets/mlm-admin.css`**
  - Лёгкая стилизация вкладок и таблицы/контейнеров ЛМ в админке.

### 2.3 Межфайловые связи / инициализация

- В `ml-learning-monitor.php` в `enqueue_assets()` подключаются `assets/mlm-admin.css` и `assets/mlm-admin.js`, а также:
  - `jquery`, `jquery-ui-tabs`
  - CSS тема jQuery UI Tabs (через CDN)
  - `wp_localize_script('mlm-admin', 'MLM', ...)` передаёт `ajaxUrl`, `nonce`, `perPage`.

---

## 3. База данных

### 3.1 Таблицы WordPress

- `wp_terms`, `wp_term_taxonomy` — термы таксономии `wpm-levels` (УД).
- `wp_termmeta` — хранение настроек ЛМ на уровне term_id.
- `wp_users`, `wp_usermeta` — данные пользователей (в таблице сонь тянутся `first_name`, `last_name`).

### 3.2 Таблица MemberLux `wp_memberlux_term_keys` (используется напрямую)

> В коде таблица берётся как `{prefix}memberlux_term_keys` (через `$wpdb->prefix . 'memberlux_term_keys'`).

Поля, которые реально используются запросом (минимум):
- `term_id`
- `user_id`
- `date_registered`
- `date_end`
- `is_banned`
- `is_unlimited`

⚠️ Поле `status` в предоставленном SQL-снимке **не используется**, поэтому «истёкший/не истёкший» доступ определяется только по датам.

### 3.3 Term Meta (настройки ЛМ)

| meta_key | Тип | Default | Назначение |
|---|---|---:|---|
| `mlm_enabled` | int(0/1) | `0` | Enable/disable monitoring UI for this access level (term). |
| `mlm_email_1_days` | int | `''` | Days after access expiry to send student email #1. |
| `mlm_email_1_subject` | string | `''` | Subject for student email #1. |
| `mlm_email_1_body` | text | `''` | Body for student email #1. |
| `mlm_email_2_days` | int | `''` | Days after access expiry to send student email #2. |
| `mlm_email_2_subject` | string | `''` | Subject for student email #2. |
| `mlm_email_2_body` | text | `''` | Body for student email #2. |
| `mlm_email_3_days` | int | `''` | Days after access expiry to send student email #3. |
| `mlm_email_3_subject` | string | `''` | Subject for student email #3. |
| `mlm_email_3_body` | text | `''` | Body for student email #3. |
| `mlm_admin_days_after_last` | int | `''` | Days after last student email to notify admin. |
| `mlm_admin_email` | string(email) | `''` | Admin email recipient. |
| `mlm_admin_subject` | string | `''` | Admin email subject. |
| `mlm_admin_body` | text | `''` | Admin email body. |


---

## 4. Классы и методы

### 4.1 `ML_Learning_Monitor` (единый класс в снимке)

**Константы**
- `TAXONOMY = 'wpm-levels'`
- `NONCE_ACTION = 'mlm_ajax_nonce'`
- `NONCE_NAME = 'mlm_nonce'` *(в текущем файле как имя поля не используется; nonce для AJAX передаётся через `wp_localize_script` и читается из `$_POST['nonce']`)*
- `PER_PAGE = 20`

**Методы (фактически присутствуют):**
- `__construct()` — регистрирует хуки:
  - `wpm-levels_edit_form_fields` → `render_term_fields()`
  - `edited_wpm-levels` → `save_term_fields()`
  - `admin_enqueue_scripts` → `enqueue_assets()`
  - `wp_ajax_mlm_get_sleepers` → `ajax_get_sleepers()`
- `enqueue_assets($hook)` — подключает ассеты только на `term.php?taxonomy=wpm-levels`.
- `render_term_fields($term)` — выводит чекбокс, tabs писем, блок «Сони».
- `render_student_email_fields($term_id, $index)` — days/subject/body для письма студента.
- `render_admin_email_fields($term_id)` — days_after_last/admin_email/subject/body.
- `save_term_fields($term_id, $tt_id)` — сохраняет term_meta.
- `ajax_get_sleepers()` — JSON ответ с HTML таблицей.
- `query_sleepers(...)` — SQL выборка сонь.
- `render_sleepers_table_html(...)` — рендер таблицы/пагинации.

---

## 5. WordPress hooks

- `wpm-levels_edit_form_fields` → `render_term_fields`
- `edited_wpm-levels` → `save_term_fields`
- `admin_enqueue_scripts` → `enqueue_assets`
- `wp_ajax_mlm_get_sleepers` → `ajax_get_sleepers`

---

## 6. AJAX endpoint `mlm_get_sleepers`

- **Method:** POST
- **Auth:** `manage_options`
- **Nonce:** `NONCE_ACTION`
- **Params:** `term_id` (required), `page` (default 1), `date_from`, `date_to`
- **Success payload:** `{ html, total, page }`

---

## 7. Admin assets summary

- JS: tabs init, toggle monitor block, AJAX fetch + pagination.
- CSS: minimal layout styling.

---

## 8. Security summary

- Cap checks + nonces on all write/AJAX operations.
- Sanitize all inputs; escape all outputs.

---

## 9. SQL summary (sleepers)

`query_sleepers()`:
- selects latest key per user by `MAX(date_end)` for given term_id
- filters `date_end` in `[date_from, date_to]`
- excludes users with another non-banned key that is unlimited or has `date_end >= date_to`.

---

## 10. What is NOT implemented in this snapshot (but implied by requirements)

- certificate-aware filtering (manual vs AUTO issuance)
- scheduled email sending (cron)
- delivery/audit logs

---

## Appendix: stats

- `ml-learning-monitor.php`: **472 lines**
- `assets/mlm-admin.js`: **83 lines**
- `assets/mlm-admin.css`: **64 lines**
