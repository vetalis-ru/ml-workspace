<?php // includes/class-mlp-program-cpt.php

class MLP_Program_CPT {
    public static function register() {
        register_post_type('ml_program', [
            'labels' => [
                'name' => 'Сборные курсы',
                'singular_name' => 'Сборный курс',
            ],
            'public' => false,
            'show_ui' => true,
            'supports' => ['title'],
        ]); 

        add_action('add_meta_boxes', [__CLASS__, 'add_meta_boxes']);
        add_action('save_post_ml_program', [__CLASS__, 'save_meta']);
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_admin_assets']);
    }

    public static function enqueue_admin_assets($hook) {
        if (!in_array($hook, ['post.php', 'post-new.php'], true)) {
            return;
        }

        $post_type = isset($_GET['post_type']) ? sanitize_text_field($_GET['post_type']) : '';
        if ($hook === 'post.php') {
            $post_id = isset($_GET['post']) ? (int)$_GET['post'] : 0;
            if ($post_id) {
                $post_type = get_post_type($post_id);
            }
        }

        if ($post_type !== 'ml_program') {
            return;
        }

        wp_enqueue_style('mlp-select2', plugins_url('/member-luxe/js/select2/css/select2.min.css'), [], '4.0.12');
        wp_enqueue_script('mlp-select2', plugins_url('/member-luxe/js/select2/js/select2.full.min.js'), ['jquery'], '4.0.12', false);
        if (strpos(get_locale(), 'ru') === 0) {
            wp_enqueue_script('mlp-select2-ru', plugins_url('/member-luxe/js/select2/js/i18n/ru.js'), ['mlp-select2'], '4.0.12', false);
        }
    }

    public static function add_meta_boxes() {
        add_meta_box(
            'mlp_steps',
            'Шаги программы (term_id)',
            [__CLASS__, 'render_steps_box'],
            'ml_program',
            'normal',
            'default'
        );
    }

    public static function render_steps_box($post) {
        $steps = get_post_meta($post->ID, 'mlp_steps', true);
        $steps = is_array($steps) ? $steps : [];
        $levels = get_terms([
            'taxonomy' => 'wpm-levels',
            'hide_empty' => false,
        ]);
        if (!is_array($levels)) {
            $levels = [];
        }
        ?>
        <?php wp_nonce_field('mlp_steps_save', 'mlp_steps_nonce'); ?>
        <p>Шаги программы (УД, срок, единицы):</p>
        <table class="widefat striped" id="mlp-steps-table">
            <thead>
                <tr>
                    <th style="width: 55%;">Уровень доступа</th>
                    <th style="width: 20%;">Длительность</th>
                    <th style="width: 20%;">Ед. измерения</th>
                    <th style="width: 5%;"></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($steps)) : ?>
                    <tr class="mlp-step-row">
                        <td>
                            <select name="mlp_steps[0][term_id]" class="mlp-term-select" style="width: 100%;">
                                <option value="">— выберите УД —</option>
                                <?php foreach ($levels as $level) : ?>
                                    <option value="<?php echo esc_attr($level->term_id); ?>">
                                        <?php echo esc_html($level->name . ' (#' . $level->term_id . ')'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td>
                            <input type="number" min="1" name="mlp_steps[0][duration]" value="1" style="width: 100%;">
                        </td>
                        <td>
                            <select name="mlp_steps[0][units]">
                                <option value="days">days</option>
                                <option value="months" selected>months</option>
                            </select>
                        </td>
                        <td>
                            <button type="button" class="button mlp-remove-step">×</button>
                        </td>
                    </tr>
                <?php else : ?>
                    <?php foreach ($steps as $index => $step) :
                        $term_id = isset($step['term_id']) ? (int)$step['term_id'] : 0;
                        $duration = isset($step['duration']) ? (int)$step['duration'] : 1;
                        $units = isset($step['units']) && in_array($step['units'], ['days', 'months'], true)
                            ? $step['units']
                            : 'months';
                        ?>
                        <tr class="mlp-step-row">
                            <td>
                                <select name="mlp_steps[<?php echo esc_attr($index); ?>][term_id]" class="mlp-term-select" style="width: 100%;">
                                    <option value="">— выберите УД —</option>
                                    <?php foreach ($levels as $level) : ?>
                                        <option value="<?php echo esc_attr($level->term_id); ?>" <?php selected($term_id, $level->term_id); ?>>
                                            <?php echo esc_html($level->name . ' (#' . $level->term_id . ')'); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <input type="number" min="1" name="mlp_steps[<?php echo esc_attr($index); ?>][duration]" value="<?php echo esc_attr($duration); ?>" style="width: 100%;">
                            </td>
                            <td>
                                <select name="mlp_steps[<?php echo esc_attr($index); ?>][units]">
                                    <option value="days" <?php selected($units, 'days'); ?>>days</option>
                                    <option value="months" <?php selected($units, 'months'); ?>>months</option>
                                </select>
                            </td>
                            <td>
                                <button type="button" class="button mlp-remove-step">×</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        <p>
            <button type="button" class="button button-secondary" id="mlp-add-step">Добавить шаг</button>
        </p>
        <p class="description">Быстрый поиск: используйте поле поиска внутри выпадающего списка.</p>
        <script>
            (function() {
                const table = document.getElementById('mlp-steps-table');
                const addButton = document.getElementById('mlp-add-step');

                function cleanupSelect2(row) {
                    row.querySelectorAll('.select2').forEach((element) => element.remove());
                    row.querySelectorAll('select').forEach((select) => {
                        select.classList.remove('select2-hidden-accessible');
                        select.removeAttribute('data-select2-id');
                        select.removeAttribute('aria-hidden');
                        select.removeAttribute('tabindex');
                        select.style.display = '';
                    });
                }

                function renumberRows() {
                    const rows = table.querySelectorAll('tbody .mlp-step-row');
                    rows.forEach((row, index) => {
                        row.querySelectorAll('select, input').forEach((input) => {
                            input.name = input.name.replace(/mlp_steps\[\d+]/, 'mlp_steps[' + index + ']');
                        });
                    });
                }

                function cloneRow() {
                    const rows = table.querySelectorAll('tbody .mlp-step-row');
                    const lastRow = rows[rows.length - 1];
                    const newRow = lastRow.cloneNode(true);
                    cleanupSelect2(newRow);
                    newRow.querySelectorAll('select').forEach((select) => {
                        select.selectedIndex = 0;
                    });
                    newRow.querySelectorAll('input[type="number"]').forEach((input) => {
                        input.value = 1;
                    });
                    table.querySelector('tbody').appendChild(newRow);
                    renumberRows();
                }

                function initSelect2(select) {
                    if (!select || typeof jQuery === 'undefined' || typeof jQuery.fn.select2 === 'undefined') {
                        return;
                    }
                    jQuery(select).select2({
                        width: '100%',
                    });
                }

                function removeRow(button) {
                    const rows = table.querySelectorAll('tbody .mlp-step-row');
                    if (rows.length <= 1) {
                        return;
                    }
                    button.closest('.mlp-step-row').remove();
                    renumberRows();
                }

                table.querySelectorAll('.mlp-term-select').forEach((select) => {
                    initSelect2(select);
                });

                table.addEventListener('click', function(event) {
                    if (event.target.classList.contains('mlp-remove-step')) {
                        removeRow(event.target);
                    }
                });

                addButton.addEventListener('click', function() {
                    cloneRow();
                    const newSelect = table.querySelector('tbody .mlp-step-row:last-child .mlp-term-select');
                    initSelect2(newSelect);
                });
            })();
        </script>
        <?php
    }


    public static function save_meta($post_id) {
        MLP_Logger::error('save_meta hit', ['post_id' => $post_id]);

        if (!isset($_POST['mlp_steps_nonce']) || !wp_verify_nonce($_POST['mlp_steps_nonce'], 'mlp_steps_save')) {
            return;
        }

        if (isset($_POST['mlp_steps']) && is_array($_POST['mlp_steps'])) {
            $steps = [];
            foreach ($_POST['mlp_steps'] as $step) {
                $term_id = isset($step['term_id']) ? (int)$step['term_id'] : 0;
                $duration = isset($step['duration']) ? (int)$step['duration'] : 0;
                $units = isset($step['units']) && in_array($step['units'], ['days', 'months'], true)
                    ? $step['units']
                    : 'months';

                if ($term_id > 0 && $duration > 0) {
                    $steps[] = [
                        'term_id' => $term_id,
                        'duration' => $duration,
                        'units' => $units,
                    ];
                }
            }

            update_post_meta($post_id, 'mlp_steps', $steps);
            return;
        }

        if (isset($_POST['mlp_steps_json'])) {
            $raw = wp_unslash($_POST['mlp_steps_json']);
            $decoded = json_decode($raw, true);
            MLP_Logger::error('steps json', ['raw' => $raw, 'decoded' => is_array($decoded)]);
            if (is_array($decoded)) {
                update_post_meta($post_id, 'mlp_steps', $decoded);
            }
        }
    }

}
