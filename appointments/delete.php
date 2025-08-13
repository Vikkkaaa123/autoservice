<?php
require_once __DIR__ . '/../config.php';

if (!isset($_GET['id'])) {
    redirect('list.php');
}

$id = (int)$_GET['id'];

try {
    $stmt = $pdo->prepare("DELETE FROM appointments WHERE id = ?");
    $stmt->execute([$id]);
    
    $_SESSION['message'] = "Запись успешно удалена";
} catch (PDOException $e) {
    $_SESSION['error'] = "Ошибка при удалении записи: " . $e->getMessage();
}

redirect('list.php');