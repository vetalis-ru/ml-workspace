Системные функции WordPress, используемые в MemberLux
Работа с пользователями
•	get_user_by() — Получает пользователя WP по email, ID, login. Возвращает WP_User или null.
•	wp_insert_user() — Создаёт или обновляет пользователя. Возвращает user_id или WP_Error.
•	email_exists() — Проверяет существование пользователя по email.
•	username_exists() — Проверяет занятость логина.
•	is_wp_error() — Проверяет, является ли результат объектом WP_Error.
Работа с базой данных
•	$wpdb — Глобальный объект доступа к базе данных WordPress.
•	$wpdb->get_var() — Возвращает одно значение из запроса.
•	$wpdb->get_row() — Возвращает одну строку результата.
•	$wpdb->insert() — Вставка строки в таблицу.
•	$wpdb->update() — Обновление строки в таблицу.
•	$wpdb->delete() — Удаление строки из таблицы.
•	$wpdb->prepare() — Безопасная подстановка параметров в SQL.
REST API
•	add_action('rest_api_init') — Хук инициализации REST API.
•	register_rest_route() — Регистрация REST endpoint.
•	WP_REST_Request — Объект REST-запроса.
•	WP_REST_Response — Корректный REST-ответ.
Хуки и архитектура
•	add_action() — Подписка на событие WordPress.
•	do_action() — Запуск события.
•	add_filter() — Регистрация фильтра.
•	apply_filters() — Применение фильтров.
Вспомогательные функции
•	current_time() — Текущее время с учётом timezone WordPress.
•	sanitize_user() — Очистка логина пользователя.
•	wp_generate_password() — Генерация пароля или ключа.
•	plugin_dir_path() — Абсолютный путь к директории плагина.
•	defined() — Проверка объявления константы.
Работа с опциями
•	get_option() — Чтение значения из wp_options.
•	add_option() — Добавление новой опции.
•	update_option() — Создание или обновление опции.
