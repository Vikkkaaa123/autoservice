<?php 
require_once '../config.php';

if (!isset($_GET['id'])) {
    redirect('list.php');
}

$id = (int)$_GET['id'];

// Проверяем, есть ли связанные записи
$stmt = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE service_id = ?");
$stmt->execute([$id]);
$count = $stmt->fetchColumn();

if ($count > 0) {
    $_SESSION['error'] = "Невозможно удалить услугу, так как есть связанные записи на обслуживание";
    redirect('list.php');
}

try {
    $stmt = $pdo->prepare("DELETE FROM services WHERE id = ?");
    $stmt->execute([$id]);
    
    $_SESSION['message'] = "Услуга успешно удалена";
} catch (PDOException $e) {
    $_SESSION['error'] = "Ошибка при удалении услуги: " . $e->getMessage();
}

redirect('list.php');