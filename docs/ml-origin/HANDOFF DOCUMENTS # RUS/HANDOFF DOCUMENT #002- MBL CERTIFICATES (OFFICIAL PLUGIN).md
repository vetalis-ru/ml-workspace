Тип: Официальный модуль MemberLux
Зависимости: MemberLux Core версии >= 2.3.4, лицензионный ключ fullpackaccess или cerificates.
Назначение: Выдача сертификатов пользователям как факта завершения уровня доступа. Создает неизменяемые записи и эмитирует каноническое событие для запуска последующих процессов (например, цепочек курсов).

1. ФАЙЛОВАЯ АРХИТЕКТУРА
text
mbl-certificates/
├── mbl-certificates.php              # Bootstrap модуля
├── includes/
│   ├── class-certificate.php         # Главная сущность "Сертификат"
│   ├── class-mblcert-core.php        # Проверка условий и инициализация
│   └── hooks.php                     # Регистрация хуков модуля
└── (доп. файлы админки, шаблонов)
2. СТРУКТУРА БАЗЫ ДАННЫХ
Основная таблица: {prefix}memberlux_certificate

sql
-- ТАБЛИЦА НЕИЗМЕНЯЕМЫХ ФАКТОВ. Наличие записи = сертификат выдан.
CREATE TABLE `wp_memberlux_certificate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT 'ID пользователя, получившего сертификат',
  `wpmlevel_id` int(11) NOT NULL COMMENT 'ID термина wpm-levels (уровень доступа)',
  `date_issue` datetime NOT NULL COMMENT 'Дата и время выдачи',
  `template_id` int(11) DEFAULT NULL COMMENT 'Ссылка на шаблон сертификата',
  -- ... (возможны другие поля meta)
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `wpmlevel_id` (`wpmlevel_id`),
  KEY `date_issue` (`date_issue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ВНИМАНИЕ: В таблице НЕТ поля `status`. Сертификаты не отзываются и не меняют статус.
Вспомогательная таблица: {prefix}memberlux_certificate_templates (для хранения шаблонов).

3. КЛАССЫ И МЕТОДЫ (PUBLIC CONTRACT)
Класс: Certificate (файл includes/class-certificate.php)

php
class Certificate {
    /**
     * СОЗДАНИЕ СЕРТИФИКАТА (ГЛАВНЫЙ ПУБЛИЧНЫЙ МЕТОД)
     * Назначение: Создать запись о выданном сертификате и вызвать глобальное событие.
     * Действие: Вызывает do_action('mbl_certificate_issued', $user_id, $certificate_id);
     * Возвращает: ID созданного сертификата (int) или false.
     */
    public static function create(...): int|false;

    /**
     * ПОЛУЧЕНИЕ ДАННЫХ СЕРТИФИКАТА
     * Назначение: Получить объект сертификата по его ID.
     * Возвращает: Объект со свойствами user_id, wpmlevel_id, date_issue и др. или false.
     */
    public static function getCertificate(int $certificate_id): object|false;
}
4. WORDPRESS HOOKS
Действия (Actions), предоставляемые модулем:

php
// ГЛАВНОЕ СОБЫТИЕ МОДУЛЯ. ЕДИНСТВЕННЫЙ НАДЕЖНЫЙ СИГНАЛ ВЫДАЧИ СЕРТИФИКАТА.
// Вызывается сразу после успешной записи в таблицу memberlux_certificate.
// Используется как триггер для прогресса программ, уведомлений и интеграций.
do_action('mbl_certificate_issued', int $user_id, int $certificate_id);
Действия (Actions), которые модуль слушает:

php
// АВТОМАТИЧЕСКАЯ ВЫДАЧА СЕРТИФИКАТОВ ПРИ АКТИВАЦИИ ДОСТУПА.
// Модуль подписан на главный хук MemberLux Core.
add_action('wpm_update_user_key_dates', 'mblc_certificate_issuance_after_update_user_key_dates', 10, 5);

// ЛОГИКА ОБРАБОТЧИКА (mblc_certificate_issuance_after_update_user_key_dates):
// 1. Проверяет мета-поле термина '_mblc_how_to_issue'.
// 2. Если значение = 'auto', немедленно вызывает Certificate::create().
// 3. Это создает "AUTO-сертификат". Ручная выдача через админку создает "MANUAL-сертификат".
5. ИНТЕГРАЦИОННЫЕ ПРИНЦИПЫ И ANTI-PATTERNS
Принцип: Сертификаты — это неизменяемые факты. Необходимо реагировать на событие их появления (mbl_certificate_issued), а не опрашивать таблицу.

Idempotency: Повторный вызов Certificate::create() для того же пользователя и уровня, вероятно, создаст дублирующую запись. Идемпотентность должна обеспечиваться на стороне потребителя (например, сохранением hash выданного сертификата в user_meta).

Auto vs Manual: Логика различения должна быть реализована потребителем хука (например, проверкой временной близости date_issue и date_start ключа доступа). Сам модуль не помечает тип выдачи в БД.

Не использовать: Предположения о наличии поля status, попытки "отозвать" или "обновить" сертификат.