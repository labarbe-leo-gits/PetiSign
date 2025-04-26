<script>
    document.addEventListener('DOMContentLoaded', function() {
        const menuToggle = document.getElementById('menuToggle');
        const leftPanel = document.querySelector('.left_panel');
        
        menuToggle.addEventListener('click', function() {
            leftPanel.classList.toggle('active');
        });
        
        const menuItems = document.querySelectorAll('.left_panel .item a');
        menuItems.forEach(item => {
            item.addEventListener('click', function() {
                leftPanel.classList.remove('active');
            });
        });
    });
</script>
</body>
</html>