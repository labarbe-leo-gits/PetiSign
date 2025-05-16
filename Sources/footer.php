<script src="js/theme.js"></script>
<div id="help-button">
    <a href="ticket.php">
        <span class="question-mark">?</span>
        <span class="help-text">Envoyer un ticket</span>
    </a>
</div>

<style>
    #help-button {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background-color: #FED78B;
        border-radius: 25px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        z-index: 1000;
        transition: width 0.4s ease-in-out, background-color 0.4s ease-in-out, box-shadow 0.3s ease;
        overflow: hidden;
        white-space: nowrap;
        width: 50px;
    }
    
    #help-button:hover {
        background-color: #E0BD78;
        width: 180px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.3);
    }
    
    #help-button a {
        color: #fff;
        text-decoration: none;
        display: flex;
        align-items: center;
        height: 50px;
        padding: 0 10px;
    }
    
    .question-mark {
        font-size: 24px;
        font-weight: bold;
        display: flex;
        justify-content: center;
        align-items: center;
        width: 30px;
        flex-shrink: 0;
    }
    
    .help-text {
        font-size: 16px;
        margin-left: 5px;
        opacity: 0;
        transform: translateX(10px);
        transition: opacity 0.3s ease-in, transform 0.3s ease-in;
    }
    
    #help-button:hover .help-text {
        opacity: 1;
        transform: translateX(0);
        transition-delay: 0.1s; 
    }
</style>
<script>
    <?php if(isset($_SESSION['mail'])): ?>
        let inactivityTime = 600;
        let timeoutId;

        function resetTimer() {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(logoutUser, inactivityTime * 1000);
        }

        function logoutUser() {
            window.location.href = 'logout.php';
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
