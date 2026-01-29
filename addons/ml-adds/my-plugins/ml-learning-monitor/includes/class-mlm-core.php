<?php
/** class-mlm-core.php
 * Основной класс плагина ML Learning Monitor
 * 
 * @package ML_Learning_Monitor
 */

if (!defined('ABSPATH')) {
    exit;
}

final class ML_Learning_Monitor {
    const TAXONOMY = 'wpm-levels';
    const NONCE_ACTION = 'mlm_ajax_nonce';
    const NONCE_NAME = 'mlm_nonce';
    const PER_PAGE = 20;

    /**
     * @var MLM_Assets
     */
    private $assets;

    /**
     * @var MLM_Term_Fields
     */
    private $term_fields;

    /**
     * @var MLM_Email_Fields
     */
    private $email_fields;

    /**
     * @var MLM_Ajax_Handler
     */
    private $ajax_handler;

    /**
     * Конструктор
     */
    public function __construct() {
        $this->init_components();
        $this->setup_hooks();
    }

    /**
     * Инициализация компонентов
     */
    private function init_components() {
        $this->assets = new MLM_Assets();
        $this->term_fields = new MLM_Term_Fields();
        $this->email_fields = new MLM_Email_Fields();
        $this->ajax_handler = new MLM_Ajax_Handler();
    }

    /**
     * Настройка хуков WordPress
     */
    private function setup_hooks() {
        // Хуки для полей терма
        add_action(self::TAXONOMY . '_edit_form_fields', [$this->term_fields, 'render'], 20, 1);
        add_action('edited_' . self::TAXONOMY, [$this->term_fields, 'save'], 20, 2);

        // Хуки для ассетов
        add_action('admin_enqueue_scripts', [$this->assets, 'enqueue']);

        // Хуки для AJAX
        add_action('wp_ajax_mlm_get_sleepers', [$this->ajax_handler, 'get_sleepers']);
    }
}