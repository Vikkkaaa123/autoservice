<?php
require_once __DIR__ . '/../config.php';

if (!isset($_GET['id'])) {
    redirect('list.php');
}

$id = (int)$_GET['id'];
$title = "Редактировать клиента";

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = sanitizeInput($_POST['first_name']);
    $lastName = sanitizeInput($_POST['last_name']);
    $phone = sanitizeInput($_POST['phone']);
    $email = sanitizeInput($_POST['email']);
    $address = sanitizeInput($_POST['address']);

    try {
        $pdo->beginTransaction();
        
        // Обновляем данные клиента
        $stmt = $pdo->prepare("UPDATE customers SET 
                              first_name = ?, last_name = ?, phone = ?, email = ?, address = ?
                              WHERE id = ?");
        $stmt->execute([$firstName, $lastName, $phone, $email, $address, $id]);
        
        // Обработка данных об автомобилях
        if (isset($_POST['car_id'])) {
            foreach ($_POST['car_id'] as $index => $carId) {
                $make = sanitizeInput($_POST['car_make'][$index]);
                $model = sanitizeInput($_POST['car_model'][$index]);
                $year = !empty($_POST['car_year'][$index]) ? (int)$_POST['car_year'][$index] : null;
                $license = !empty($_POST['car_license'][$index]) ? sanitizeInput($_POST['car_license'][$index]) : null;
                $vin = !empty($_POST['car_vin'][$index]) ? sanitizeInput($_POST['car_vin'][$index]) : null;
                
                $stmt = $pdo->prepare("UPDATE customer_cars SET 
                                      make = ?, model = ?, year = ?, license_plate = ?, vin = ?
                                      WHERE id = ? AND customer_id = ?");
                $stmt->execute([$make, $model, $year, $license, $vin, $carId, $id]);
            }
        }
        
        $_SESSION['message'] = "Данные клиента и автомобилей обновлены";
        $pdo->commit();
        redirect('view.php?id='.$id);
    } catch (PDOException $e) {
        $pdo->rollBack();
        $error = "Ошибка при обновлении: " . $e->getMessage();
    }
}
?>

<?php include __DIR__ . '/../header.php'; ?>

<div class="section-title">
    <h2>Редактировать клиента</h2>
</div>

<?php if (!empty($error)): ?>
    <div class="alert error"><?= $error ?></div>
<?php endif; ?>

<div class="form-container">
    <form method="post">
        <h3>Основная информация</h3>
        
        <div class="form-group">
            <label for="first_name">Имя:</label>
            <input type="text" id="first_name" name="first_name" 
                   value="<?= htmlspecialchars($customer['first_name']) ?>" required>
        </div>
        
        <div class="form-group">
            <label for="last_name">Фамилия:</label>
            <input type="text" id="last_name" name="last_name" 
                   value="<?= htmlspecialchars($customer['last_name']) ?>" required>
        </div>
        
        <div class="form-group">
            <label for="phone">Телефон:</label>
            <input type="text" id="phone" name="phone" 
                   value="<?= htmlspecialchars($customer['phone']) ?>" required>
        </div>
        
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" 
                   value="<?= htmlspecialchars($customer['email']) ?>">
        </div>
        
        <div class="form-group">
            <label for="address">Адрес:</label>
            <textarea id="address" name="address" rows="3"><?= htmlspecialchars($customer['address']) ?></textarea>
        </div>
        
        <h3>Автомобили клиента</h3>
        
        <?php if (count($cars) > 0): ?>
            <div class="cars-edit-list">
                <?php foreach ($cars as $index => $car): ?>
                <div class="car-edit-group">
                    <input type="hidden" name="car_id[]" value="<?= $car['id'] ?>">
                    
                    <div class="form-group">
                        <label for="car_make_<?= $index ?>">Марка:</label>
                        <input type="text" id="car_make_<?= $index ?>" name="car_make[]" 
                               value="<?= htmlspecialchars($car['make']) ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="car_model_<?= $index ?>">Модель:</label>
                        <input type="text" id="car_model_<?= $index ?>" name="car_model[]" 
                               value="<?= htmlspecialchars($car['model']) ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="car_year_<?= $index ?>">Год выпуска:</label>
                        <input type="number" id="car_year_<?= $index ?>" name="car_year[]" 
                               value="<?= $car['year'] ?>" min="1900" max="<?= date('Y') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="car_license_<?= $index ?>">Гос. номер:</label>
                        <input type="text" id="car_license_<?= $index ?>" name="car_license[]" 
                               value="<?= htmlspecialchars($car['license_plate']) ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="car_vin_<?= $index ?>">VIN:</label>
                        <input type="text" id="car_vin_<?= $index ?>" name="car_vin[]" 
                               value="<?= htmlspecialchars($car['vin']) ?>">
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>У клиента нет автомобилей</p>
        <?php endif; ?>
        
        <div class="form-actions">
            <button type="submit" class="btn-submit">Сохранить</button>
            <a href="view.php?id=<?= $id ?>" class="btn-cancel">Отмена</a>
            <a href="add_car.php?customer_id=<?= $id ?>" class="btn">Добавить автомобиль</a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../footer.php'; ?>