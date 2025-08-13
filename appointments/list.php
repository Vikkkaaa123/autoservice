<?php 
require_once '../config.php';

$title = "Запись на обслуживание";

// Параметры сортировки
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'appointment_date';
$order = isset($_GET['order']) ? $_GET['order'] : 'desc';

// Валидация параметров сортировки
$allowed_sorts = ['client_last_name', 'mechanic_last_name', 'appointment_date'];
$allowed_orders = ['asc', 'desc'];

if (!in_array($sort, $allowed_sorts)) {
    $sort = 'appointment_date';
}
if (!in_array($order, $allowed_orders)) {
    $order = 'desc';
}

// Обработка поискового запроса
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$where = '';
$params = [];

if (!empty($search)) {
    $where = "WHERE cl.last_name LIKE :search OR m.last_name LIKE :search";
    $params[':search'] = "%$search%";
}

// Получаем записи с учетом сортировки
$query = "
    SELECT a.*, 
    c.make as car_make, c.model as car_model, c.license_plate,
    CONCAT(cl.last_name, ' ', cl.first_name) as client_name,
    cl.last_name as client_last_name,
    s.name as service_name,
    CONCAT(m.last_name, ' ', m.first_name) as mechanic_name,
    m.last_name as mechanic_last_name
    FROM appointments a
    JOIN customer_cars c ON a.car_id = c.id
    JOIN customers cl ON c.customer_id = cl.id
    JOIN services s ON a.service_id = s.id
    JOIN mechanics m ON a.mechanic_id = m.id
    $where
    ORDER BY 
        CASE WHEN :sort = 'client_last_name' THEN cl.last_name END $order,
        CASE WHEN :sort = 'mechanic_last_name' THEN m.last_name END $order,
        CASE WHEN :sort = 'appointment_date' THEN a.appointment_date END $order
";

$stmt = $pdo->prepare($query);
$stmt->bindParam(':sort', $sort);

foreach ($params as $key => &$val) {
    $stmt->bindParam($key, $val);
}

$stmt->execute();
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include(__DIR__ . '/../header.php'); ?>

<div class="section-title">
    <h2>Запись на обслуживание</h2>
</div>

<div class="top-actions">
    <div class="add-btn-wrapper">
        <a href="add.php" class="btn-main">Создать запись</a>
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
                    <option value="appointment_date" <?= $sort == 'appointment_date' ? 'selected' : '' ?>>Дате записи</option>
                    <option value="client_last_name" <?= $sort == 'client_last_name' ? 'selected' : '' ?>>Фамилии клиента</option>
                    <option value="mechanic_last_name" <?= $sort == 'mechanic_last_name' ? 'selected' : '' ?>>Фамилии механика</option>
                </select>
                <select name="order" onchange="this.form.submit()">
                    <option value="desc" <?= $order == 'desc' ? 'selected' : '' ?>>По убыванию</option>
                    <option value="asc" <?= $order == 'asc' ? 'selected' : '' ?>>По возрастанию</option>
                </select>
            </form>
        </div>
    </div>
</div>

<div class="data-table-container">
    <table>
        <thead>
            <tr>
                <th>
                    <a href="?search=<?= urlencode($search) ?>&sort=appointment_date&order=<?= $sort == 'appointment_date' && $order == 'desc' ? 'asc' : 'desc' ?>">
                        Дата <?= $sort == 'appointment_date' ? ($order == 'desc' ? '↓' : '↑') : '' ?>
                    </a>
                </th>
                <th>
                    <a href="?search=<?= urlencode($search) ?>&sort=client_last_name&order=<?= $sort == 'client_last_name' && $order == 'desc' ? 'asc' : 'desc' ?>">
                        Клиент <?= $sort == 'client_last_name' ? ($order == 'desc' ? '↓' : '↑') : '' ?>
                    </a>
                </th>
                <th>Автомобиль</th>
                <th>Услуга</th>
                <th>
                    <a href="?search=<?= urlencode($search) ?>&sort=mechanic_last_name&order=<?= $sort == 'mechanic_last_name' && $order == 'desc' ? 'asc' : 'desc' ?>">
                        Механик <?= $sort == 'mechanic_last_name' ? ($order == 'desc' ? '↓' : '↑') : '' ?>
                    </a>
                </th>
                <th>Статус</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($appointments)): ?>
                <tr>
                    <td colspan="7" class="no-results">
                        <?= empty($search) ? 'Нет записей в базе' : 'Ничего не найдено' ?>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($appointments as $app): ?>
                <tr>
                    <td><?= date('d.m.Y H:i', strtotime($app['appointment_date'])) ?></td>
                    <td><?= htmlspecialchars($app['client_name']) ?></td>
                    <td><?= htmlspecialchars($app['car_make']) ?> <?= htmlspecialchars($app['car_model']) ?> (<?= htmlspecialchars($app['license_plate']) ?>)</td>
                    <td><?= htmlspecialchars($app['service_name']) ?></td>
                    <td><?= htmlspecialchars($app['mechanic_name']) ?></td>
                    <td><?= getStatusBadge($app['status']) ?></td>
                    <td class="actions">
                        <a href="edit.php?id=<?= $app['id'] ?>" class="btn-action">Редактировать</a>
                        <a href="delete.php?id=<?= $app['id'] ?>" class="btn-action" onclick="return confirm('Вы уверены?')">Удалить</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include(__DIR__ . '/../footer.php'); ?>