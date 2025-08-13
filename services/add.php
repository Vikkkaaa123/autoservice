<?php 
require_once '../config.php';

$title = "Добавить услугу";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name']);
    $description = sanitizeInput($_POST['description']);
    $price = (float)$_POST['price'];
    $duration = (int)$_POST['duration'];
    
    try {
        $stmt = $pdo->prepare("INSERT INTO services (name, description, price, duration_minutes) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $description, $price, $duration]);
        
        $_SESSION['message'] = "Услуга успешно добавлена";
        redirect('list.php');
    } catch (PDOException $e) {
        $error = "Ошибка при добавлении услуги: " . $e->getMessage();
    }
}
?>

<?php include '../header.php'; ?>

<div class="section-title">
  <h2>Добавить новую услугу</h2>
     </div>

<?php if (!empty($error)): ?>
    <div class="alert alert-error"><?= $error ?></div>
<?php endif; ?>

<div class="form-container">
    <form method="post">
        <div class="form-group">
            <label for="name">Название услуги:</label>
            <input type="text" id="name" name="name" required>
        </div>
        
        <div class="form-group">
            <label for="description">Описание:</label>
            <textarea id="description" name="description"></textarea>
        </div>
        
        <div class="form-group">
            <label for="price">Цена (руб):</label>
            <input type="number" id="price" name="price" step="0.01" min="0" required>
        </div>
        
        <div class="form-group">
            <label for="duration">Длительность (минут):</label>
            <input type="number" id="duration" name="duration" min="1" required>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn-submit">Добавить</button>
            <a href="list.php" class="btn-cancel">Отмена</a>
        </div>
    </form>
</div>

<?php include '../footer.php'; ?>