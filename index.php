<?php require_once 'config.php'; ?>
<?php $title = "Главная"; ?>
<?php include 'header.php'; ?>

<div class="dashboard">
    <h1>Система управления автосервисом</h1>
    
    <div class="stats-grid">
        <div class="stat-card">
            <h3>Клиенты</h3>
            <?php
                $count = $pdo->query("SELECT COUNT(*) FROM customers")->fetchColumn();
                echo "<p>$count</p>";
            ?>
            <a href="customers/list.php" class="btn">Перейти</a>
        </div>
        
        <div class="stat-card">
            <h3>Механики</h3>
            <?php
                $count = $pdo->query("SELECT COUNT(*) FROM mechanics")->fetchColumn();
                echo "<p>$count</p>";
            ?>
            <a href="mechanics/list.php" class="btn">Перейти</a>
        </div>
        
        <div class="stat-card">
            <h3>Услуги</h3>
            <?php
                $count = $pdo->query("SELECT COUNT(*) FROM services")->fetchColumn();
                echo "<p>$count</p>";
            ?>
            <a href="services/list.php" class="btn">Перейти</a>
        </div>
        
        <div class="stat-card">
            <h3>Запись</h3>
            <?php
                $count = $pdo->query("SELECT COUNT(*) FROM appointments")->fetchColumn();
                echo "<p>$count</p>";
            ?>
            <a href="appointments/list.php" class="btn">Перейти</a>
        </div>
    </div>

    <div class="slider-wrapper">
        <button class="slider-btn prev-btn">
            <img src="images/arrow.jpg" alt="Предыдущее" class="arrow-icon">
        </button>
        
        <div class="slider-container">
            <div class="slider">
                <img src="images/slider1.jpg" alt="Фото 1" class="slide active">
                <img src="images/slider2.jpg" alt="Фото 2" class="slide">
                <img src="images/slider3.jpg" alt="Фото 3" class="slide">
                <img src="images/slider4.jpg" alt="Фото 4" class="slide">
                <img src="images/slider5.jpg" alt="Фото 5" class="slide">
            </div>
        </div>
        
        <button class="slider-btn next-btn">
            <img src="images/arrow.jpg" alt="Следующее" class="arrow-icon">
        </button>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const slides = document.querySelectorAll('.slide');
        const prevBtn = document.querySelector('.prev-btn');
        const nextBtn = document.querySelector('.next-btn');
        let currentSlide = 0;
        let slideInterval;
        
        // Функция показа текущего слайда
        function showSlide(index) {
            slides.forEach(slide => slide.classList.remove('active'));
            slides[index].classList.add('active');
            currentSlide = index;
        }
        
        // Функция следующего слайда
        function nextSlide() {
            currentSlide = (currentSlide + 1) % slides.length;
            showSlide(currentSlide);
        }
        
        // Функция предыдущего слайда
        function prevSlide() {
            currentSlide = (currentSlide - 1 + slides.length) % slides.length;
            showSlide(currentSlide);
        }
        
        // Автоматическое перелистывание-6сек
        function startSlider() {
            slideInterval = setInterval(nextSlide, 6000);
        }
        
        // Остановка при наведении
        function stopSlider() {
            clearInterval(slideInterval);
        }
        
        // Инициализация
        showSlide(0);
        startSlider();
        
        // Обработчики событий
        nextBtn.addEventListener('click', function() {
            stopSlider();
            nextSlide();
            startSlider();
        });
        
        prevBtn.addEventListener('click', function() {
            stopSlider();
            prevSlide();
            startSlider();
        });
        
        // Пауза при наведении
        document.querySelector('.slider-container').addEventListener('mouseenter', stopSlider);
        document.querySelector('.slider-container').addEventListener('mouseleave', startSlider);
    });
</script>

<?php include 'footer.php'; ?>