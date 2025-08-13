<?php
require_once __DIR__ . '/../config.php';
$title = "Добавить клиента";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = sanitizeInput($_POST['first_name']);
    $lastName = sanitizeInput($_POST['last_name']);
    $phone = sanitizeInput($_POST['phone']);
    $email = sanitizeInput($_POST['email']);
    $address = sanitizeInput($_POST['address']);
    
    $make = !empty($_POST['car_make']) ? sanitizeInput($_POST['car_make']) : null;
    $model = !empty($_POST['car_model']) ? sanitizeInput($_POST['car_model']) : null;
    $year = !empty($_POST['car_year']) ? (int)$_POST['car_year'] : null;
    $license = !empty($_POST['car_license']) ? sanitizeInput($_POST['car_license']) : null;
    $vin = !empty($_POST['car_vin']) ? sanitizeInput($_POST['car_vin']) : null;

    try {
        $pdo->beginTransaction();
        
        // Добавляем клиента
        $stmt = $pdo->prepare("INSERT INTO customers (first_name, last_name, phone, email, address) 
                              VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$firstName, $lastName, $phone, $email, $address]);
        $customerId = $pdo->lastInsertId();
        
        // Добавляем автомобиль, если указаны данные
        if ($make && $model) {
            $stmt = $pdo->prepare("INSERT INTO customer_cars 
                                  (customer_id, make, model, year, license_plate, vin) 
                                  VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$customerId, $make, $model, $year, $license, $vin]);
        }
        
        $_SESSION['message'] = "Клиент успешно добавлен" . ($make ? " с автомобилем" : "");
        $pdo->commit();
        redirect('list.php');
    } catch (PDOException $e) {
        $pdo->rollBack();
        $error = "Ошибка при добавлении клиента: " . $e->getMessage();
    }
}
?>

<?php include __DIR__ . '/../header.php'; ?>

<div class="form-container">
    <h2>Добавить нового клиента</h2>

    <?php if (!empty($error)): ?>
        <div class="alert error"><?= $error ?></div>
    <?php endif; ?>

    <form method="post">
        <h3>Основная информация</h3>
        
        <div class="form-group">
            <label for="first_name">Имя:</label>
            <input type="text" id="first_name" name="first_name" required>
        </div>
        
        <div class="form-group">
            <label for="last_name">Фамилия:</label>
            <input type="text" id="last_name" name="last_name" required>
        </div>
        
        <div class="form-group">
            <label for="phone">Телефон:</label>
            <input type="text" id="phone" name="phone" required>
        </div>
        
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email">
        </div>
        
        <div class="form-group">
            <label for="address">Адрес:</label>
            <textarea id="address" name="address" rows="3"></textarea>
        </div>
        
        <h3>Информация об автомобиле</h3>
        
        <div class="form-group">
            <label for="car_make">Марка:</label>
            <input type="text" id="car_make" name="car_make">
        </div>
        
        <div class="form-group">
            <label for="car_model">Модель:</label>
            <input type="text" id="car_model" name="car_model">
        </div>
        
        <div class="form-group">
            <label for="car_year">Год выпуска:</label>
            <input type="number" id="car_year" name="car_year" min="1900" max="<?= date('Y') ?>">
        </div>
        
        <div class="form-group">
            <label for="car_license">Гос. номер:</label>
            <input type="text" id="car_license" name="car_license">
        </div>
        
        <div class="form-group">
            <label for="car_vin">VIN:</label>
            <input type="text" id="car_vin" name="car_vin">
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn-submit">Добавить клиента</button>
            <a href="list.php" class="btn-cancel">Отмена</a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../footer.php'; ?>