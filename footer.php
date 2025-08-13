    </main>

    <footer>
        <div class="footer-content">
            <p>&copy; <?= date('Y') ?> Автосервис. Все права защищены.</p>
        </div>
    </footer>

    <script>
    // Функция для управления выдвижным меню
    function toggleMenu() {
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        
        sidebar.classList.toggle('active');
        
        // Добавляем/убираем класс для основного содержимого
        if (sidebar.classList.contains('active')) {
            mainContent.style.marginLeft = '250px';
            document.addEventListener('click', closeMenuOutside);
        } else {
            mainContent.style.marginLeft = '0';
            document.removeEventListener('click', closeMenuOutside);
        }
    }

    // Закрытие меню при клике вне его области
    function closeMenuOutside(event) {
        const sidebar = document.getElementById('sidebar');
        const menuButton = document.querySelector('.menu-button');
        
        if (!sidebar.contains(event.target) && 
            event.target !== menuButton && 
            !menuButton.contains(event.target)) {
            toggleMenu();
        }
    }

    // Закрытие меню при изменении размера окна
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.remove('active');
            document.getElementById('mainContent').style.marginLeft = '0';
        }
    });
    </script>
</body>
</html>