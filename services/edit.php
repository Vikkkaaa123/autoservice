<?php 
require_once '../config.php';

if (!isset($_GET['id'])) {
    redirect('list.php');
}

$id = (int)$_GET['id'];
$title = "Редактировать услугу";

// Получаем данные услуги
$stmt = $pdo->prepare("SELECT * FROM services WHERE id = ?");
$stmt->execute([$id]);
$service = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$service) {
    $_SESSION['error'] = "Услуга не найдена";
    redirect('list.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name']);
    $description = sanitizeInput($_POST['description']);
    $price = (float)$_POST['price'];
    $duration = (int)$_POST['duration'];
    
    try {
        $stmt = $pdo->prepare("UPDATE services SET name = ?, description = ?, price = ?, duration_minutes = ? WHERE id = ?");
        $stmt->execute([$name, $description, $price, $duration, $id]);
        
        $_SESSION['message'] = "Услуга успешно обновлена";
        redirect('list.php');
    } catch (PDOException $e) {
        $error = "Ошибка при обновлении услуги: " . $e->getMessage();
    }
}
?>

<?php include '../header.php'; ?>

<div class="section-title">
  <h2>Редактировать услугу</h2>
    </div>

<?php if (!empty($error)): ?>
    <div class="alert alert-error"><?= $error ?></div>
<?php endif; ?>

<div class="form-container">
    <form method="post">
        <div class="form-group">
            <label for="name">Название услуги:</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($service['name']) ?>" required>
        </div>
        
        <div class="form-group">
            <label for="description">Описание:</label>
            <textarea id="description" name="description"><?= htmlspecialchars($service['description']) ?></textarea>
        </div>
        
        <div class="form-group">
            <label for="price">Цена (руб):</label>
            <input type="number" id="price" name="price" step="0.01" min="0" value="<?= $service['price'] ?>" required>
        </div>
        
        <div class="form-group">
            <label for="duration">Длительность (минут):</label>
            <input type="number" id="duration" name="duration" min="1" value="<?= $service['duration_minutes'] ?>" required>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn-submit">Сохранить</button>
            <a href="list.php" class="btn-cancel">Отмена</a>
        </div>
    </form>
</div>

<?php include '../footer.php'; ?>