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
