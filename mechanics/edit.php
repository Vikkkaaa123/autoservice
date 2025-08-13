<?php
require_once __DIR__ . '/../config.php';

if (!isset($_GET['id'])) {
    redirect('list.php');
}

$id = (int)$_GET['id'];
$title = "Редактировать механика";

// Получаем данные механика
$stmt = $pdo->prepare("SELECT * FROM mechanics WHERE id = ?");
$stmt->execute([$id]);
$mechanic = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$mechanic) {
    $_SESSION['error'] = "Механик не найден";
    redirect('list.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = sanitizeInput($_POST['first_name']);
    $lastName = sanitizeInput($_POST['last_name']);
    $specialization = sanitizeInput($_POST['specialization']);
    $phone = sanitizeInput($_POST['phone']);
    $hireDate = $_POST['hire_date'];

    try {
        $stmt = $pdo->prepare("UPDATE mechanics SET first_name = ?, last_name = ?, specialization = ?, phone = ?, hire_date = ? WHERE id = ?");
        $stmt->execute([$firstName, $lastName, $specialization, $phone, $hireDate, $id]);
        
        $_SESSION['message'] = "Данные механика обновлены";
        redirect('list.php');
    } catch (PDOException $e) {
        $error = "Ошибка при обновлении: " . $e->getMessage();
    }
}
?>

<?php include __DIR__ . '/../header.php'; ?>

<div class="section-title">
  <h2>Редактировать механика</h2>
     </div>

<?php if (!empty($error)): ?>
    <div class="alert error"><?= $error ?></div>
<?php endif; ?>

<div class="form-container">
    <form method="post">
        <div class="form-group">
            <label for="first_name">Имя:</label>
            <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($mechanic['first_name']) ?>" required>
        </div>
        
        <div class="form-group">
            <label for="last_name">Фамилия:</label>
            <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($mechanic['last_name']) ?>" required>
        </div>
        
        <div class="form-group">
            <label for="specialization">Специализация:</label>
            <input type="text" id="specialization" name="specialization" value="<?= htmlspecialchars($mechanic['specialization']) ?>" required>
        </div>
        
        <div class="form-group">
            <label for="phone">Телефон:</label>
            <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($mechanic['phone']) ?>" required>
        </div>
        
        <div class="form-group">
            <label for="hire_date">Дата приема:</label>
            <input type="date" id="hire_date" name="hire_date" value="<?= $mechanic['hire_date'] ?>" required>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn-submit">Сохранить</button>
            <a href="list.php" class="btn-cancel">Отмена</a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../footer.php'; ?>