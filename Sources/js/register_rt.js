document.addEventListener('DOMContentLoaded', function() {
    const emailInput = document.getElementById('mail');
    const usernameInput = document.getElementById('username');
    const emailStatus = document.getElementById('email-status');
    const usernameStatus = document.getElementById('username-status');
    const submitBtn = document.getElementById('submit-btn');
    let emailTimeout, usernameTimeout;
    let emailValid = false, usernameValid = false;

    function updateSubmitButton() {
        submitBtn.disabled = !(emailValid && usernameValid);
    }

    function checkAvailability(type, value, statusElement) {
        fetch(`Processus/fetch_mail.php?type=${type}&value=${encodeURIComponent(value)}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'available') {
                    statusElement.innerHTML = `✓ ${type === 'email' ? 'Email' : 'Nom d\'utilisateur'} disponible`;
                    statusElement.className = 'status-message status-available';
                    if (type === 'email') emailValid = true;
                    if (type === 'username') usernameValid = true;
                } else if (data.status === 'taken') {
                    statusElement.innerHTML = `✗ ${type === 'email' ? 'Email' : 'Nom d\'utilisateur'} déjà utilisé`;
                    statusElement.className = 'status-message status-taken';
                    if (type === 'email') emailValid = false;
                    if (type === 'username') usernameValid = false;
                } else {
                    statusElement.innerHTML = 'Erreur de vérification';
                    statusElement.className = 'status-message status-taken';
                    if (type === 'email') emailValid = false;
                    if (type === 'username') usernameValid = false;
                }
                updateSubmitButton();
            })
            .catch(error => {
                statusElement.innerHTML = 'Erreur de connexion';
                statusElement.className = 'status-message status-taken';
                if (type === 'email') emailValid = false;
                if (type === 'username') usernameValid = false;
                updateSubmitButton();
            });
    }

    emailInput.addEventListener('input', function() {
        const email = this.value.trim();
        
        clearTimeout(emailTimeout);
        
        if (!email) {
            emailStatus.innerHTML = '';
            emailStatus.className = 'status-message';
            emailValid = false;
            updateSubmitButton();
            return;
        }
        
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            emailStatus.innerHTML = 'Format d\'email invalide';
            emailStatus.className = 'status-message status-taken';
            emailValid = false;
            updateSubmitButton();
            return;
        }
        
        emailStatus.innerHTML = 'Vérification...';
        emailStatus.className = 'status-message status-checking';
        
        emailTimeout = setTimeout(() => {
            checkAvailability('email', email, emailStatus);
        }, 500);
    });

    usernameInput.addEventListener('input', function() {
        const username = this.value.trim();
        
        clearTimeout(usernameTimeout);
        
        if (!username) {
            usernameStatus.innerHTML = '';
            usernameStatus.className = 'status-message';
            usernameValid = false;
            updateSubmitButton();
            return;
        }
        
        if (username.length < 3) {
            usernameStatus.innerHTML = 'Minimum 3 caractères';
            usernameStatus.className = 'status-message status-taken';
            usernameValid = false;
            updateSubmitButton();
            return;
        }

        if (username.length > 30) {
            usernameStatus.innerHTML = 'Maximum 30 caractères';
            usernameStatus.className = 'status-message status-taken';
            usernameValid = false;
            updateSubmitButton();
            return;
        }
        
        usernameStatus.innerHTML = 'Vérification...';
        usernameStatus.className = 'status-message status-checking';
        
        usernameTimeout = setTimeout(() => {
            checkAvailability('username', username, usernameStatus);
        }, 500);
    });
});