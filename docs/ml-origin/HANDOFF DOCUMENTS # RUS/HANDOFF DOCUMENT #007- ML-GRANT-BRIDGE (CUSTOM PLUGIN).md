Тип: Кастомный плагин-интегратор
Зависимости: MemberLux Core, memberlux_term_keys table.
Назначение: Служит мостом (bridge) между внешними системами (AmoCRM, иные CRM) и MemberLux. Принимает входящие вебхуки из внешних систем и выполняет каноническую выдачу доступа пользователю, используя только публичные функции Core.

1. АРХИТЕКТУРА И ПРИНЦИП РАБОТЫ
Плагин является тонкой оберткой-маршрутизатором. Его единственная задача — трансформировать входящий запрос от внешней системы в вызовы wpm_insert_one_user_key() и wpm_update_user_key_dates().

text
Внешняя система (POST) → REST Endpoint (/mlgb/v1/grant) → ML-Grant-Bridge → MemberLux Core API
2. REST API ENDPOINT
Endpoint: [POST] /wp-json/mlgb/v1/grant

Аутентификация: Проверка по секретному токену, переданному в заголовке (X-API-Key) или в теле запроса.

Тело запроса (пример):

json
{
  "user": {
    "email": "customer@domain.com",
    "first_name": "Иван",
    "last_name": "Петров"
  },
  "grant": {
    "term_id": 15,
    "duration": 6,
    "units": "month"
  },
  "external_id": "lead_789",
  "source": "amo_crm"
}
Логика обработчика (строго по контракту Core):

php
// 1. ВАЛИДАЦИЯ И АУТЕНТИФИКАЦИЯ ВХОДЯЩИХ ДАННЫХ
// 2. ПОИСК/СОЗДАНИЕ ПОЛЬЗОВАТЕЛЯ
$user = get_user_by('email', $email);
if (!$user) {
    $user_id = wpm_register_user([
        'user_email' => $email,
        'first_name' => $first_name,
        'last_name'  => $last_name,
        'send_email' => false // Часто отключают, чтобы CRM отправил свое письмо
    ]);
} else {
    $user_id = $user->ID;
}

// 3. ГЕНЕРАЦИЯ И АКТИВАЦИЯ КЛЮЧА (КАНОНИЧЕСКИЙ ПУТЬ)
$generated_key = wpm_insert_one_user_key($term_id, $duration, $units);
if ($generated_key) {
    $activation_result = wpm_update_user_key_dates(
        $user_id,
        $generated_key,
        false,
        'grant_bridge_' . $source
    );
}

// 4. ЛОГИРОВАНИЕ И ОТВЕТ
// В ответе JSON возвращает user_id, term_id, key, date_end.
3. КРИТИЧЕСКИЕ ТРЕБОВАНИЯ К РЕАЛИЗАЦИИ
Идемпотентность (Idempotency): Endpoint должен быть идемпотентным. Повторный идентичный запрос (определяемый по external_id или хешу параметров) не должен создавать дублирующих ключей. Реализуется через собственную таблицу логов или wp_options, где сохраняется связь external_id -> key_id.

Только Public API: Запрещены прямые SQL-запросы на вставку в memberlux_term_keys. Используются только wpm_insert_one_user_key и wpm_update_user_key_dates.

Безопасность: Обязательная проверка прав (capability) или кастомного токена. Санитизация всех входящих данных.

Логирование: Обязательное детальное логирование всех шагов (входящий запрос, результат поиска пользователя, успешность выдачи ключа) в отдельную таблицу или файл для аудита.

4. ИНТЕГРАЦИОННЫЕ ПОСЛЕДСТВИЯ
Поскольку плагин использует wpm_update_user_key_dates, его работа приводит к каскадному срабатыванию всей экосистемы MemberLux:

Срабатывает хук do_action('wpm_update_user_key_dates', ...).

Модуль Auto Responder может подписать пользователя на воронку.

Модуль Certificates может выдать AUTO-сертификат (если для term_id настроена автоматическая выдача).

Модуль API & Webhooks может отправить событие о выдаче доступа во внешние системы.

Плагин является эталонным примером корректной внешней интеграции с MemberLux без модификации ядра.