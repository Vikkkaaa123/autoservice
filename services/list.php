<?php 
require_once '../config.php';

$title = "Список услуг";

// Обработка поискового запроса
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$where = '';
$params = [];

if (!empty($search)) {
    $where = "WHERE name LIKE :search";
    $params[':search'] = "%$search%";
}

// Получаем услуги, отсортированные по дате добавления (по возрастанию)
$query = "SELECT * FROM services $where ORDER BY created_at ASC";
$stmt = $pdo->prepare($query);

foreach ($params as $key => &$val) {
    $stmt->bindParam($key, $val);
}

$stmt->execute();
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include(__DIR__ . '/../header.php'); ?>

<div class="section-title">
    <h2>Список услуг</h2>
</div>

<div class="top-actions">
    <div class="add-btn-wrapper">
        <a href="add.php" class="btn-main">Добавить услугу</a>
    </div>
    
    <div class="search-box">
        <form method="get" action="">
            <input type="text" name="search" placeholder="Поиск по названию..." 
                   value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="btn-main">Поиск</button>
            <?php if (!empty($search)): ?>
                <a href="list.php" class="btn-main">Сбросить</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<div class="data-table-container">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Название</th>
                <th>Описание</th>
                <th>Цена</th>
                <th>Длительность</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($services)): ?>
                <tr>
                    <td colspan="6" class="no-results">
                        <?= empty($search) ? 'Нет услуг в базе' : 'Ничего не найдено' ?>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($services as $service): ?>
                <tr>
                    <td><?= $service['id'] ?></td>
                    <td><?= htmlspecialchars($service['name']) ?></td>
                    <td><?= htmlspecialchars($service['description']) ?></td>
                    <td><?= number_format($service['price'], 2) ?> ₽</td>
                    <td><?= $service['duration_minutes'] ?> мин</td>
                    <td class="actions">
                        <a href="edit.php?id=<?= $service['id'] ?>" class="btn-action">Редактировать</a>
                        <a href="delete.php?id=<?= $service['id'] ?>" class="btn-action" onclick="return confirm('Вы уверены?')">Удалить</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include(__DIR__ . '/../footer.php'); ?>