<?php

use Mbl\AutoResponder\Editor;
use Mbl\AutoResponder\MailTemplates;
use Mbl\AutoResponder\Plugin;

add_action('admin_menu', 'mblar_register_main_admin_page');
function mblar_register_main_admin_page()
{
    $plugin = new Plugin();
    $parent_slug = 'mblar_mailing';
    $hook_suffix = add_menu_page(
        'Шаблоны писем MEMBERLUX',
        'Шаблоны писем',
        'manage_options',
        $parent_slug,
        'mblar_render_mailing_page',
        'dashicons-email-alt',
        2
    );
    $hook_suffix_list = add_submenu_page(
        $parent_slug,
        'Все шаблоны',
        'Все шаблоны',
        'manage_options',
        $parent_slug,
        'mblar_render_mailing_page',
    );
    add_action('load-' . $hook_suffix, function () {
        add_action('admin_enqueue_scripts', 'mblar_admin_assets', 100);
    });
    $hook_suffix_create = add_submenu_page(
        $parent_slug,
        'Добавить новый',
        'Добавить новый',
        'manage_options',
        'mblar_add_template',
        'mblar_render_template_page',
    );
    add_action('load-' . $hook_suffix_create, function () {
        add_action('admin_enqueue_scripts', 'mblar_create_template_admin_assets', 100);
    });

    $hook_suffix_settings = add_submenu_page(
        $parent_slug,
        'Настройки',
        'Настройки',
        'manage_options',
        'mblar_settings',
        function () {
            echo '<div id="mblar-root"></div>';
        },
    );
    add_action('load-' . $hook_suffix_settings, function () {
        add_action('admin_enqueue_scripts', 'mblar_settings_admin_assets', 100);
    });
}

function mblar_admin_assets()
{
    global $wpdb;
    wp_enqueue_media();
    $plugin = new Plugin();
    if (isset($_GET['edit'])) {
        require_once dirname(__DIR__) . "/blocks/main.php";
        wp_enqueue_editor();
        wp_localize_script('mblar-template-edit', '__mblar_data__', (new Editor($plugin))->state());
        wp_enqueue_script('mblar-template-edit');
    } else {
        wp_enqueue_style('mblar-bootstrap');
        if (!function_exists('mblar_get_template')) {
            require_once $plugin->path('blocks/main.php');
        }
        $list = (new MailTemplates($wpdb))->list();
        wp_localize_script('mblar-template-list', '__mblar_data__',
            $plugin->js_data([
                'templates' => $list,
                'addPage' => admin_url('admin.php?page=mblar_add_template')
            ])
        );
        wp_enqueue_script('mblar-template-list');
    }
}

function mblar_create_template_admin_assets() {
    require_once dirname(__DIR__) . "/blocks/main.php";
    wp_enqueue_media();
    wp_enqueue_editor();
    wp_localize_script('mblar-template-edit', '__mblar_data__', (new Editor(new Plugin()))->state());
    wp_enqueue_script('mblar-template-edit');
}

function mblar_render_mailing_page()
{
    ob_start();
    wp_editor('', '__editor__');
    ob_end_clean();
    ?>
    <div id="mblar-root"></div>
    <?php
}

function mblar_render_template_page() {
    ob_start();
    wp_editor('', '__editor__');
    ob_end_clean();
    ?>
    <div id="mblar-root"></div>
    <?php
}

function mblar_settings_admin_assets() {
    wp_enqueue_style('mblar-options');
    wp_enqueue_script('mblar-options');
}