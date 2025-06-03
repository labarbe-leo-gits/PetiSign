<script>
    <?php if(isset($_SESSION['mail'])): ?>
        let inactivityTime = 600;
        let timeoutId;

        function resetTimer() {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(logoutUser, inactivityTime * 1000);
        }

        function logoutUser() {
            window.location.href = '/Sources/logout.php';
        }

        document.addEventListener('mousemove', resetTimer);
        document.addEventListener('mousedown', resetTimer);
        document.addEventListener('keypress', resetTimer);
        document.addEventListener('touchmove', resetTimer);
        document.addEventListener('scroll', resetTimer);

        resetTimer();
    <?php endif ?>
</script>
</body>
</html>