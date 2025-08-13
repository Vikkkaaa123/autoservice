<?php
session_start();

// Настройки базы данных
define('DB_HOST', 'localhost');
define('DB_NAME', 'autoservice');
define('DB_USER', 'root'); 
define('DB_PASS', '');

// Подключение к БД
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
        DB_USER,
        DB_PASS
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}

// Функция для очистки ввода
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Функция для перенаправления
function redirect($location) {
    header("Location: $location");
    exit;
}

function getStatusBadge($status) {
    $statuses = [
        'scheduled' => ['label' => 'Запланировано', 'class' => 'badge-blue'],
        'in_progress' => ['label' => 'В работе', 'class' => 'badge-orange'],
        'completed' => ['label' => 'Завершено', 'class' => 'badge-green'],
        'canceled' => ['label' => 'Отменено', 'class' => 'badge-red']
    ];
    
    return '<span class="badge ' . $statuses[$status]['class'] . '">' 
           . $statuses[$status]['label'] . '</span>';
}

?>