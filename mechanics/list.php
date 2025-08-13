<?php 
require_once '../config.php';

$title = "Список механиков";

// Параметры сортировки
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'last_name';
$order = isset($_GET['order']) ? $_GET['order'] : 'asc';

// Валидация параметров сортировки
$allowed_sorts = ['last_name', 'created_at'];
$allowed_orders = ['asc', 'desc'];

if (!in_array($sort, $allowed_sorts)) {
    $sort = 'last_name';
}
if (!in_array($order, $allowed_orders)) {
    $order = 'asc';
}

// Обработка поискового запроса (только по фамилии)
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$where = '';
$params = [];

if (!empty($search)) {
    $where = "WHERE last_name LIKE :search";
    $params[':search'] = "%$search%";
}

// Получаем механиков с учетом сортировки и поиска
$query = "SELECT * FROM mechanics $where ORDER BY $sort $order";
$stmt = $pdo->prepare($query);

foreach ($params as $key => &$val) {
    $stmt->bindParam($key, $val);
}

$stmt->execute();
$mechanics = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include(__DIR__ . '/../header.php'); ?>

<div class="section-title">
    <h2>Список механиков</h2>
</div>

<div class="top-actions">
    <div class="add-btn-wrapper">
        <a href="add.php" class="btn-main">Добавить механика</a>
    </div>
    
    <div class="search-sort-container">
        <div class="search-box">
            <form method="get" action="">
                <input type="text" name="search" placeholder="Поиск по фамилии..." 
                       value="<?= htmlspecialchars($search) ?>">
                <button type="submit" class="btn-main">Поиск</button>
                <?php if (!empty($search)): ?>
                    <a href="list.php" class="btn-main">Сбросить</a>
                <?php endif; ?>
            </form>
        </div>
        
        <div class="sort-box">
            <form method="get" action="">
                <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                <label for="sort">Сортировать по:</label>
                <select name="sort" id="sort" onchange="this.form.submit()">
                    <option value="last_name" <?= $sort == 'last_name' ? 'selected' : '' ?>>Фамилии</option>
                    <option value="created_at" <?= $sort == 'created_at' ? 'selected' : '' ?>>Дате добавления</option>
                </select>
                <select name="order" onchange="this.form.submit()">
                    <option value="asc" <?= $order == 'asc' ? 'selected' : '' ?>>По возрастанию</option>
                    <option value="desc" <?= $order == 'desc' ? 'selected' : '' ?>>По убыванию</option>
                </select>
            </form>
        </div>
    </div>
</div>

<div class="data-table-container">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>
                    <a href="?search=<?= urlencode($search) ?>&sort=last_name&order=<?= $sort == 'last_name' && $order == 'asc' ? 'desc' : 'asc' ?>">
                        Фамилия <?= $sort == 'last_name' ? ($order == 'asc' ? '↑' : '↓') : '' ?>
                    </a>
                </th>
                <th>Имя</th>
                <th>Специализация</th>
                <th>Телефон</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($mechanics)): ?>
                <tr>
                    <td colspan="6" class="no-results">
                        <?= empty($search) ? 'Нет механиков в базе' : 'Ничего не найдено' ?>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($mechanics as $mechanic): ?>
                <tr>
                    <td><?= $mechanic['id'] ?></td>
                    <td><?= htmlspecialchars($mechanic['last_name']) ?></td>
                    <td><?= htmlspecialchars($mechanic['first_name']) ?></td>
                    <td><?= htmlspecialchars($mechanic['specialization']) ?></td>
                    <td><?= htmlspecialchars($mechanic['phone']) ?></td>
                    <td class="actions">
                        <a href="edit.php?id=<?= $mechanic['id'] ?>" class="btn-action">Редактировать</a>
                        <a href="delete.php?id=<?= $mechanic['id'] ?>" class="btn-action" onclick="return confirm('Вы уверены?')">Удалить</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include(__DIR__ . '/../footer.php'); ?>