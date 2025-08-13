<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Автосервис' ?></title>
    <!-- Важно: этот путь должен быть правильным -->
    <link rel="stylesheet" href="/autoservice/styles.css">
</head>
<body>
    <!-- Меню -->
    <button class="menu-button" onclick="toggleMenu()">
        <img src="/autoservice/images/menu.jpg" alt="Меню" class="menu-icon">
    </button>
    
    <nav class="sidebar" id="sidebar">
        <ul>
            <li><a href="/autoservice/index.php">Главная</a></li>
            <li><a href="/autoservice/customers/list.php">Клиенты</a></li>
            <li><a href="/autoservice/mechanics/list.php">Механики</a></li>
            <li><a href="/autoservice/services/list.php">Услуги</a></li>
            <li><a href="/autoservice/appointments/list.php">Запись</a></li>
        </ul>
    </nav>

    <main>
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert success"><?= $_SESSION['message'] ?></div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>