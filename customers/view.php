<?php
require_once __DIR__ . '/../config.php';

if (!isset($_GET['id'])) {
    redirect('list.php');
}

$id = (int)$_GET['id'];
$title = "Просмотр клиента";

// Получаем данные клиента
$stmt = $pdo->prepare("SELECT * FROM customers WHERE id = ?");
$stmt->execute([$id]);
$customer = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$customer) {
    $_SESSION['error'] = "Клиент не найден";
    redirect('list.php');
}

// Получаем автомобили клиента
$stmt = $pdo->prepare("SELECT * FROM customer_cars WHERE customer_id = ?");
$stmt->execute([$id]);
$cars = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include __DIR__ . '/../header.php'; ?>

<div class="customer-view client-view-container">
    <h2>Клиент: <?= htmlspecialchars($customer['last_name']) ?> <?= htmlspecialchars($customer['first_name']) ?></h2>
    
    <div class="customer-details">
        <div class="detail">
            <span class="label">Телефон:</span>
            <span class="value"><?= htmlspecialchars($customer['phone']) ?></span>
        </div>
        
        <div class="detail">
            <span class="label">Email:</span>
            <span class="value"><?= htmlspecialchars($customer['email']) ?></span>
        </div>
        
        <div class="detail">
            <span class="label">Адрес:</span>
            <span class="value"><?= htmlspecialchars($customer['address']) ?></span>
        </div>
    </div>
    
    <h3>Автомобили клиента</h3>
    
    <?php if (count($cars) > 0): ?>
        <div class="cars-list">
            <?php foreach ($cars as $index => $car): ?>
            <div class="car-card">
                <div class="car-number">Автомобиль <?= $index + 1 ?></div>
                <div class="car-make-model">
                    <?= htmlspecialchars($car['make']) ?> <?= htmlspecialchars($car['model']) ?>
                </div>
                <div class="car-details">
                    <div>Год: <?= $car['year'] ?: 'не указан' ?></div>
                    <div>Гос. номер: <?= htmlspecialchars($car['license_plate']) ?: 'не указан' ?></div>
                    <div>VIN: <?= htmlspecialchars($car['vin']) ?: 'не указан' ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="no-cars">У клиента нет автомобилей</p>
    <?php endif; ?>
    
    <div class="customer-actions">
        <a href="edit.php?id=<?= $id ?>" class="btn">Редактировать данные</a>
        <a href="list.php" class="btn">Назад к списку</a>
    </div>
</div>

<?php include __DIR__ . '/../footer.php'; ?>