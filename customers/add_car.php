<?php
require_once __DIR__ . '/../config.php';

if (!isset($_GET['customer_id'])) {
    redirect('list.php');
}

$customerId = (int)$_GET['customer_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $make = sanitizeInput($_POST['make']);
    $model = sanitizeInput($_POST['model']);
    $year = !empty($_POST['year']) ? (int)$_POST['year'] : null;
    $license = !empty($_POST['license']) ? sanitizeInput($_POST['license']) : null;
    $vin = !empty($_POST['vin']) ? sanitizeInput($_POST['vin']) : null;

    try {
        $stmt = $pdo->prepare("INSERT INTO customer_cars (customer_id, make, model, year, license_plate, vin) 
                              VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$customerId, $make, $model, $year, $license, $vin]);
        
        $_SESSION['message'] = "Автомобиль успешно добавлен";
        redirect("view.php?id=$customerId");
    } catch (PDOException $e) {
        $error = "Ошибка при добавлении автомобиля: " . $e->getMessage();
    }
}

// Получаем данные клиента
$stmt = $pdo->prepare("SELECT * FROM customers WHERE id = ?");
$stmt->execute([$customerId]);
$customer = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$customer) {
    $_SESSION['error'] = "Клиент не найден";
    redirect('list.php');
}

$title = "Добавить автомобиль для " . $customer['last_name'] . ' ' . $customer['first_name'];
?>

<?php include __DIR__ . '/../header.php'; ?>

<div class="section-title">
 <h2>Добавить автомобиль для: <?= htmlspecialchars($customer['last_name']) ?> <?=      htmlspecialchars($customer['first_name']) ?></h2>
  </div>

<?php if (!empty($error)): ?>
    <div class="alert error"><?= $error ?></div>
<?php endif; ?>

<div class="form-container">
    <form method="post">
        <div class="form-group">
            <label for="make">Марка:</label>
            <input type="text" id="make" name="make" required>
        </div>
        
        <div class="form-group">
            <label for="model">Модель:</label>
            <input type="text" id="model" name="model" required>
        </div>
        
        <div class="form-group">
            <label for="year">Год выпуска:</label>
            <input type="number" id="year" name="year" min="1900" max="<?= date('Y') ?>">
        </div>
        
        <div class="form-group">
            <label for="license">Гос. номер:</label>
            <input type="text" id="license" name="license">
        </div>
        
        <div class="form-group">
            <label for="vin">VIN-код:</label>
            <input type="text" id="vin" name="vin">
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn-submit">Добавить</button>
            <a href="view.php?id=<?= $customerId ?>" class="btn-cancel">Отмена</a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../footer.php'; ?>