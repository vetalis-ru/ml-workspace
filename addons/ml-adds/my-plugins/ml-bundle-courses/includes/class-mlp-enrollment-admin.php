<?php // includes/class-mlp-enrollment-admin.php

class MLP_Enrollment_Admin {
    public static function register() {
        add_action('admin_menu', [__CLASS__, 'add_menu']);
    }

    public static function add_menu() {
        add_submenu_page(
            'edit.php?post_type=ml_program',
            'Связать пользователя с программой',
            'Связать пользователя',
            'manage_options',
            'mlp-enrollment',
            [__CLASS__, 'render_page']
        );
        add_submenu_page(
            'edit.php?post_type=ml_program',
            'Участники сборных курсов',
            'Участники программ',
            'manage_options',
            'mlp-enrollments',
            [__CLASS__, 'render_list_page']
        );
    }

    public static function render_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        $message = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mlp_enrollment_nonce'])) {
            if (wp_verify_nonce($_POST['mlp_enrollment_nonce'], 'mlp_enrollment_save')) {
                $user_id = isset($_POST['mlp_user_id']) ? (int)$_POST['mlp_user_id'] : 0;
                $program_id = isset($_POST['mlp_program_id']) ? (int)$_POST['mlp_program_id'] : 0;
                if ($user_id && $program_id) {
                    MLP_Enrollment::set_program_id($user_id, $program_id);
                    MLP_Enrollment::set_current_step($user_id, 0);
                    MLP_Enrollment::set_last_cert_hash($user_id, '');
                    $message = 'Пользователь успешно привязан к программе.';
                } else {
                    $message = 'Выберите пользователя и программу.';
                }
            } else {
                $message = 'Неверный токен формы.';
            }
        }

        $query = isset($_GET['mlp_user_query']) ? sanitize_text_field($_GET['mlp_user_query']) : '';
        $users = [];
        if ($query !== '') {
            $users = self::search_users($query);
        }

        $programs = get_posts([
            'post_type' => 'ml_program',
            'post_status' => 'publish',
            'numberposts' => 100,
            'orderby' => 'title',
            'order' => 'ASC',
        ]);

        ?>
        <div class="wrap">
            <h1>Связать пользователя со сборным курсом</h1>
            <?php if ($message) : ?>
                <div class="notice notice-info"><p><?php echo esc_html($message); ?></p></div>
            <?php endif; ?>

            <form method="get" action="">
                <input type="hidden" name="post_type" value="ml_program">
                <input type="hidden" name="page" value="mlp-enrollment">
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="mlp_user_query">Поиск пользователя</label></th>
                        <td>
                            <input type="text" id="mlp_user_query" name="mlp_user_query" value="<?php echo esc_attr($query); ?>" class="regular-text" placeholder="Email, логин, имя, фамилия или ID">
                            <p class="description">Поиск по email, login/nickname, имени/фамилии или user_id.</p>
                        </td>
                    </tr>
                </table>
                <?php submit_button('Найти', 'secondary', '', false); ?>
            </form>

            <form method="post" action="">
                <?php wp_nonce_field('mlp_enrollment_save', 'mlp_enrollment_nonce'); ?>
                <input type="hidden" name="post_type" value="ml_program">
                <input type="hidden" name="page" value="mlp-enrollment">

                <h2>Результаты поиска</h2>
                <?php if ($query === '') : ?>
                    <p>Введите поисковый запрос, чтобы найти пользователя.</p>
                <?php elseif (empty($users)) : ?>
                    <p>Пользователь не найден.</p>
                <?php else : ?>
                    <table class="widefat striped">
                        <thead>
                            <tr>
                                <th style="width: 40px;"></th>
                                <th>ID</th>
                                <th>Пользователь</th>
                                <th>Email</th>
                                <th>Текущая программа</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user) : ?>
                                <?php $current_program_id = MLP_Enrollment::get_program_id($user->ID); ?>
                                <tr>
                                    <td>
                                        <input type="radio" name="mlp_user_id" value="<?php echo esc_attr($user->ID); ?>">
                                    </td>
                                    <td><?php echo esc_html($user->ID); ?></td>
                                    <td>
                                        <?php echo esc_html($user->display_name ?: $user->user_login); ?>
                                        <br>
                                        <span class="description"><?php echo esc_html($user->user_login); ?></span>
                                    </td>
                                    <td><?php echo esc_html($user->user_email); ?></td>
                                    <td>
                                        <?php echo $current_program_id ? esc_html(get_the_title($current_program_id)) . ' (#' . esc_html($current_program_id) . ')' : '—'; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>

                <h2>Программа</h2>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="mlp_program_id">Сборный курс</label></th>
                        <td>
                            <select name="mlp_program_id" id="mlp_program_id">
                                <option value="">— выберите программу —</option>
                                <?php foreach ($programs as $program) : ?>
                                    <option value="<?php echo esc_attr($program->ID); ?>">
                                        <?php echo esc_html($program->post_title); ?> (#<?php echo esc_html($program->ID); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                </table>

                <?php submit_button('Привязать пользователя', 'primary'); ?>
            </form>
        </div>
        <?php
    }

    public static function render_list_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        $step_input = isset($_GET['mlp_step']) ? trim((string)$_GET['mlp_step']) : '';
        $filters = [
            'program_id' => isset($_GET['mlp_program_id']) ? (int)$_GET['mlp_program_id'] : 0,
            'step' => $step_input === '' ? null : (int)$step_input,
            'orderby' => isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : 'user_id',
            'order' => isset($_GET['order']) ? sanitize_text_field($_GET['order']) : 'asc',
        ];

        $programs = get_posts([
            'post_type' => 'ml_program',
            'post_status' => 'publish',
            'numberposts' => 200,
            'orderby' => 'title',
            'order' => 'ASC',
        ]);
        $program_map = [];
        foreach ($programs as $program) {
            $program_map[$program->ID] = $program->post_title;
        }

        $users = self::get_enrolled_users($filters);
        $users = self::sort_enrolled_users($users, $filters);
        $base_url = admin_url('edit.php?post_type=ml_program&page=mlp-enrollments');

        ?>
        <div class="wrap">
            <h1>Участники сборных курсов</h1>

            <form method="get" action="">
                <input type="hidden" name="post_type" value="ml_program">
                <input type="hidden" name="page" value="mlp-enrollments">
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="mlp_program_id">Сборный курс</label></th>
                        <td>
                            <select name="mlp_program_id" id="mlp_program_id">
                                <option value="0">Все программы</option>
                                <?php foreach ($programs as $program) : ?>
                                    <option value="<?php echo esc_attr($program->ID); ?>" <?php selected($filters['program_id'], $program->ID); ?>>
                                        <?php echo esc_html($program->post_title); ?> (#<?php echo esc_html($program->ID); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="mlp_step">Текущий шаг</label></th>
                        <td>
                            <input type="number" min="0" name="mlp_step" id="mlp_step" value="<?php echo esc_attr($step_input); ?>" class="small-text">
                            <p class="description">0 — первый шаг. Оставьте пустым для всех.</p>
                        </td>
                    </tr>
                </table>
                <?php submit_button('Применить фильтры', 'secondary', '', false); ?>
            </form>

            <table class="widefat striped">
                <thead>
                    <tr>
                        <?php echo self::render_sortable_header('user_id', 'ID', $filters, $base_url); ?>
                        <?php echo self::render_sortable_header('user_login', 'Логин', $filters, $base_url); ?>
                        <?php echo self::render_sortable_header('display_name', 'Имя', $filters, $base_url); ?>
                        <?php echo self::render_sortable_header('user_email', 'Email', $filters, $base_url); ?>
                        <?php echo self::render_sortable_header('program', 'Программа', $filters, $base_url); ?>
                        <?php echo self::render_sortable_header('current_step', 'Текущий шаг', $filters, $base_url); ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)) : ?>
                        <tr>
                            <td colspan="6">Нет привязанных пользователей.</td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($users as $row) : ?>
                            <tr>
                                <td><?php echo esc_html($row['user_id']); ?></td>
                                <td><?php echo esc_html($row['user_login']); ?></td>
                                <td><?php echo esc_html($row['display_name']); ?></td>
                                <td><?php echo esc_html($row['user_email']); ?></td>
                                <td>
                                    <?php
                                    $program_title = isset($program_map[$row['program_id']]) ? $program_map[$row['program_id']] : '—';
                                    echo esc_html($program_title);
                                    echo $row['program_id'] ? ' (#' . esc_html($row['program_id']) . ')' : '';
                                    ?>
                                </td>
                                <td><?php echo esc_html($row['current_step']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php
    }

    private static function get_enrolled_users(array $filters) {
        $meta_query = [
            [
                'key' => 'mlp_program_id',
                'compare' => 'EXISTS',
            ],
        ];
        if ($filters['program_id']) {
            $meta_query[] = [
                'key' => 'mlp_program_id',
                'value' => $filters['program_id'],
                'compare' => '=',
            ];
        }
        if ($filters['step'] !== null) {
            $meta_query[] = [
                'key' => 'mlp_current_step',
                'value' => $filters['step'],
                'compare' => '=',
            ];
        }

        $user_query = new WP_User_Query([
            'number' => 500,
            'meta_query' => $meta_query,
            'fields' => ['ID', 'user_login', 'display_name', 'user_email'],
        ]);

        $rows = [];
        foreach ($user_query->get_results() as $user) {
            $program_id = (int)get_user_meta($user->ID, 'mlp_program_id', true);
            $rows[] = [
                'user_id' => $user->ID,
                'user_login' => $user->user_login,
                'display_name' => $user->display_name,
                'user_email' => $user->user_email,
                'program_id' => $program_id,
                'program_title' => $program_id ? (string)get_the_title($program_id) : '',
                'current_step' => (int)get_user_meta($user->ID, 'mlp_current_step', true),
            ];
        }

        return $rows;
    }

    private static function sort_enrolled_users(array $rows, array $filters) {
        $orderby = in_array($filters['orderby'], ['user_id', 'user_login', 'display_name', 'user_email', 'program', 'current_step'], true)
            ? $filters['orderby']
            : 'user_id';
        $order = strtolower($filters['order']) === 'desc' ? 'desc' : 'asc';

        usort($rows, function ($a, $b) use ($orderby, $order) {
            $value_a = $orderby === 'program' ? $a['program_title'] : $a[$orderby];
            $value_b = $orderby === 'program' ? $b['program_title'] : $b[$orderby];
            if ($value_a == $value_b) {
                return 0;
            }
            $result = ($value_a < $value_b) ? -1 : 1;
            return $order === 'desc' ? -$result : $result;
        });

        return $rows;
    }

    private static function render_sortable_header($key, $label, array $filters, $base_url) {
        $current = $filters['orderby'] === $key;
        $next_order = ($current && strtolower($filters['order']) === 'asc') ? 'desc' : 'asc';
        $url = add_query_arg([
            'orderby' => $key,
            'order' => $next_order,
            'mlp_program_id' => $filters['program_id'],
            'mlp_step' => $filters['step'],
        ], $base_url);
        $indicator = $current ? (strtolower($filters['order']) === 'asc' ? ' ▲' : ' ▼') : '';

        return '<th><a href="' . esc_url($url) . '">' . esc_html($label) . $indicator . '</a></th>';
    }

    private static function search_users($query) {
        $users = [];
        if (ctype_digit($query)) {
            $user = get_user_by('id', (int)$query);
            if ($user) {
                $users[] = $user;
                return $users;
            }
        }

        $search = '*' . $query . '*';
        $user_query = new WP_User_Query([
            'search' => $search,
            'search_columns' => ['user_login', 'user_nicename', 'user_email', 'display_name'],
            'number' => 20,
            'orderby' => 'display_name',
            'order' => 'ASC',
        ]);
        foreach ($user_query->get_results() as $user) {
            $users[$user->ID] = $user;
        }

        $meta_query = new WP_User_Query([
            'meta_query' => [
                'relation' => 'OR',
                [
                    'key' => 'first_name',
                    'value' => $query,
                    'compare' => 'LIKE',
                ],
                [
                    'key' => 'last_name',
                    'value' => $query,
                    'compare' => 'LIKE',
                ],
                [
                    'key' => 'nickname',
                    'value' => $query,
                    'compare' => 'LIKE',
                ],
            ],
            'number' => 20,
            'orderby' => 'display_name',
            'order' => 'ASC',
        ]);
        foreach ($meta_query->get_results() as $user) {
            $users[$user->ID] = $user;
        }

        return array_values($users);
    }
}
