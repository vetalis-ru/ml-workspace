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
        $json = $steps ? wp_json_encode($steps, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) : "[]";
        ?>
        <p>JSON-массив шагов (term_id, duration, units):</p>
        <textarea name="mlp_steps_json" style="width:100%;height:150px;"><?php echo esc_textarea($json); ?></textarea>
        <?php
    }


    public static function save_meta($post_id) {
        MLP_Logger::error('save_meta hit', ['post_id' => $post_id]);

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
