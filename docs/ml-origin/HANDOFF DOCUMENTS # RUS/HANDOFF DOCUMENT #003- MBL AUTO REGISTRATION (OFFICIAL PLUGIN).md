Тип: Официальный модуль MemberLux
Зависимости: MemberLux Core версии >= 2.5.9, лицензионный ключ fullpackaccess или autoreg.
Назначение: Предоставление публичных одноразовых ссылок для самостоятельной регистрации и автоматической активации доступа к выбранному уровню (term_id).

1. ФАЙЛОВАЯ АРХИТЕКТУРА (ОЖИДАЕМАЯ)
text
mbl-auto-registration/
├── mbl-auto-registration.php         # Bootstrap
├── includes/
│   ├── class-mbl-ar-core.php         # Основная логика, проверка лицензии
│   ├── class-mbl-ar-public.php       # Обработка публичных URL (entry point)
│   ├── class-mbl-ar-ajax.php         # AJAX для админки (генерация ссылок)
│   └── class-mbl-ar-admin.php        # UI в настройках MemberLux
└── assets/
    └── (JS/CSS для админки)
2. МЕХАНИЗМ РАБОТЫ И PUBLIC CONTRACT
1. Публичный Entry Point (URL):

text
https://site.com/{wpma|join}/{hash}/{email}/
Обрабатывается через WordPress rewrite rules или прямой парсинг $_GET['mblr_hash'], $_GET['mblr_email'].

Hash — уникальный идентификатор настройки, хранящей term_id, duration, units.

2. Основной Flow (обработчик URL):

php
// ПСЕВДОКОД ГЛАВНОГО АЛГОРИТМА
1.  Извлечь `$hash` и `$email` из URL.
2.  Загрузить настройки из `$wpdb->options` (`wpm_main_options.mblr_auto_registration.{hash}`).
3.  Проверить, не использована ли уже ссылка (статус, лимиты).
4.  Сгенерировать ключ доступа: `wpm_insert_one_user_key($term_id, $duration, $units);`
5.  Найти ID ключа: `wpm_search_key_id($generated_code);`
6.  Зарегистрировать пользователя (или найти существующего по email) и активировать ключ:
    wpm_register_user([
        'user_email' => $email,
        'code'       => $generated_code, // Ключ активируется внутри
        'source'     => 'auto_registration'
    ], $send_email = true);
3. Админка и AJAX:

Раздел настроек: Вкладка в админке MemberLux (/wp-admin/admin.php?page=memberlux&tab=...).

AJAX endpoint: wp_ajax_mblr_get_auto_reg_link — генерирует новую хешированную ссылку с заданными параметрами (term_id, duration, units, expires).

3. WORDPRESS HOOKS (INTEGRATION POINTS)
Модуль является потребителем основного API MemberLux. Он не предоставляет собственных публичных хуков для расширения, но его работа порождает стандартные события Core:

При успешной регистрации срабатывает wpm_register_user().

При активации ключа срабатывает главный хук do_action('wpm_update_user_key_dates', ...).

Отправляются стандартные письма MemberLux.