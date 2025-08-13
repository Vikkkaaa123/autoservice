<?php
require_once __DIR__ . '/../config.php';

if (!isset($_GET['id'])) {
    redirect('list.php');
}

$id = (int)$_GET['id'];

// Проверяем, есть ли связанные записи
$stmt = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE mechanic_id = ?");
$stmt->execute([$id]);
$count = $stmt->fetchColumn();

if ($count > 0) {
    $_SESSION['error'] = "Невозможно удалить механика, так как есть связанные записи";
    redirect('list.php');
}

try {
    $stmt = $pdo->prepare("DELETE FROM mechanics WHERE id = ?");
    $stmt->execute([$id]);
    
    $_SESSION['message'] = "Механик успешно удален";
} catch (PDOException $e) {
    $_SESSION['error'] = "Ошибка при удалении: " . $e->getMessage();
}

redirect('list.php');