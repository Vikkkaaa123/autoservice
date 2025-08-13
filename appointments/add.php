<?php
require_once __DIR__ . '/../config.php';
$title = "Создать запись на обслуживание";

// Получаем данные для форм
$cars = $pdo->query("SELECT cc.id, cc.make, cc.model, cc.license_plate, 
                     CONCAT(c.last_name, ' ', c.first_name) as owner
                     FROM customer_cars cc
                     JOIN customers c ON cc.customer_id = c.id
                     ORDER BY c.last_name")->fetchAll();

$services = $pdo->query("SELECT * FROM services ORDER BY name")->fetchAll();
$mechanics = $pdo->query("SELECT * FROM mechanics ORDER BY last_name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $carId = (int)$_POST['car_id'];
    $serviceId = (int)$_POST['service_id'];
    $mechanicId = (int)$_POST['mechanic_id'];
    $date = $_POST['appointment_date'];
    $notes = sanitizeInput($_POST['notes']);

    try {
        // Проверка года (не меньше 2024)
        $year = date('Y', strtotime($date));
        if ($year < 2024) {
            throw new Exception("Год должен быть не меньше 2024");
        }

        // Проверка на занятость механика
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM appointments 
                              WHERE mechanic_id = ? AND appointment_date = ?");
        $stmt->execute([$mechanicId, $date]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            throw new Exception("Этот механик уже занят в указанное время");
        }

        $stmt = $pdo->prepare("INSERT INTO appointments 
                              (car_id, service_id, mechanic_id, appointment_date, notes) 
                              VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$carId, $serviceId, $mechanicId, $date, $notes]);
        
        $_SESSION['message'] = "Запись успешно создана";
        redirect('list.php');
    } catch (PDOException $e) {
        $error = "Ошибка при создании записи: " . $e->getMessage();
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<?php include __DIR__ . '/../header.php'; ?>

<div class="section-title">
  <h2>Создать новую запись</h2>
</div>

<?php if (!empty($error)): ?>
    <div class="alert error"><?= $error ?></div>
<?php endif; ?>

<div class="form-container">
    <form method="post">
        <div class="form-group">
            <label for="car_id">Автомобиль:</label>
            <select id="car_id" name="car_id" required>
                <option value="">Выберите автомобиль</option>
                <?php foreach ($cars as $car): ?>
                <option value="<?= $car['id'] ?>">
                    <?= htmlspecialchars($car['owner']) ?> - 
                    <?= htmlspecialchars($car['make']) ?> 
                    <?= htmlspecialchars($car['model']) ?> 
                    (<?= htmlspecialchars($car['license_plate']) ?>)
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="service_id">Услуга:</label>
            <select id="service_id" name="service_id" required>
                <option value="">Выберите услугу</option>
                <?php foreach ($services as $service): ?>
                <option value="<?= $service['id'] ?>">
                    <?= htmlspecialchars($service['name']) ?> 
                    (<?= $service['price'] ?> руб.)
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="mechanic_id">Механик:</label>
            <select id="mechanic_id" name="mechanic_id" required>
                <option value="">Выберите механика</option>
                <?php foreach ($mechanics as $mechanic): ?>
                <option value="<?= $mechanic['id'] ?>">
                    <?= htmlspecialchars($mechanic['last_name']) ?> 
                    <?= htmlspecialchars($mechanic['first_name']) ?>
                    (<?= htmlspecialchars($mechanic['specialization']) ?>)
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="appointment_date">Дата и время:</label>
            <input type="datetime-local" id="appointment_date" name="appointment_date" required
                   min="<?= date('Y-m-d\TH:i', strtotime('2024-01-01 00:00')) ?>">
        </div>
        
        <div class="form-group">
            <label for="notes">Примечания:</label>
            <textarea id="notes" name="notes" rows="3"></textarea>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn-submit">Создать запись</button>
            <a href="list.php" class="btn-cancel">Отмена</a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../footer.php'; ?>