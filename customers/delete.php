<?php
require_once __DIR__ . '/../config.php';

if (!isset($_GET['id'])) {
    redirect('list.php');
}

$id = (int)$_GET['id'];

try {
    $pdo->beginTransaction();
    
    // Удаляем все автомобили клиента
    $stmt = $pdo->prepare("DELETE FROM customer_cars WHERE customer_id = ?");
    $stmt->execute([$id]);
    
    // Удаляем самого клиента
    $stmt = $pdo->prepare("DELETE FROM customers WHERE id = ?");
    $stmt->execute([$id]);
    
    // Проверяем, был ли клиент удален
    if ($stmt->rowCount() > 0) {
        $_SESSION['message'] = "Клиент и его автомобили успешно удалены";
        $pdo->commit();
    } else {
        $_SESSION['error'] = "Клиент с ID $id не найден";
        $pdo->rollBack();
    }
} catch (PDOException $e) {
    $pdo->rollBack();
    $_SESSION['error'] = "Ошибка при удалении: " . $e->getMessage();
}

redirect('list.php');