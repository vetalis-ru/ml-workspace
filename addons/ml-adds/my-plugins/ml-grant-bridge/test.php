<?php

echo 'HEEEY!!!!';
// Жёстко грузим WordPress, если файл открывается напрямую
require_once dirname(__DIR__, 3) . '/wp-load.php';

// Путь, который ты используешь в плагине
$log_dir  = plugin_dir_path(__DIR__) . 'logs';
$log_file = $log_dir . '/mlgb.log';

echo '<pre>';

// Проверка путей
echo "plugin_dir_path(__DIR__):\n";
echo plugin_dir_path(__DIR__) . "\n\n";

echo "log_dir:\n";
echo $log_dir . "\n";

echo "log_file:\n";
echo $log_file . "\n\n";

// Проверка существования
echo "is_dir(log_dir): ";
var_dump(is_dir($log_dir));

echo "is_writable(log_dir): ";
var_dump(is_writable($log_dir));

// Пробуем создать лог
$result = file_put_contents(
    $log_file,
    '[' . date('Y-m-d H:i:s') . "] TEST LOG WRITE\n",
    FILE_APPEND
);

echo "file_put_contents result: ";
var_dump($result);

echo '</pre>';
