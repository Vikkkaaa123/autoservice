<?php
require_once __DIR__ . '/../config.php';
$title = "Добавить механика";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = sanitizeInput($_POST['first_name']);
    $lastName = sanitizeInput($_POST['last_name']);
    $specialization = sanitizeInput($_POST['specialization']);
    $phone = sanitizeInput($_POST['phone']);
    $hireDate = $_POST['hire_date'];

    try {
        $stmt = $pdo->prepare("INSERT INTO mechanics (first_name, last_name, specialization, phone, hire_date) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$firstName, $lastName, $specialization, $phone, $hireDate]);
        
        $_SESSION['message'] = "Механик успешно добавлен";
        redirect('list.php');
    } catch (PDOException $e) {
        $error = "Ошибка при добавлении механика: " . $e->getMessage();
    }
}
?>

<?php include __DIR__ . '/../header.php'; ?>

<div class="section-title">
  <h2>Добавить механика</h2>
    </div>

<?php if (!empty($error)): ?>
    <div class="alert error"><?= $error ?></div>
<?php endif; ?>

<div class="form-container">
    <form method="post">
        <div class="form-group">
            <label for="first_name">Имя:</label>
            <input type="text" id="first_name" name="first_name" required>
        </div>
        
        <div class="form-group">
            <label for="last_name">Фамилия:</label>
            <input type="text" id="last_name" name="last_name" required>
        </div>
        
        <div class="form-group">
            <label for="specialization">Специализация:</label>
            <input type="text" id="specialization" name="specialization" required>
        </div>
        
        <div class="form-group">
            <label for="phone">Телефон:</label>
            <input type="text" id="phone" name="phone" required>
        </div>
        
        <div class="form-group">
            <label for="hire_date">Дата приема:</label>
            <input type="date" id="hire_date" name="hire_date" required>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn-submit">Добавить</button>
            <a href="list.php" class="btn-cancel">Отмена</a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../footer.php'; ?>