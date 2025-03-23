document.addEventListener("DOMContentLoaded", function() {
    const button = document.getElementById('loginbtn');
    const inputs = document.querySelectorAll('.editable');
    const usernameField = document.getElementById('username');
    const descriptionField = document.getElementById('description');
    const emailField = document.getElementById('mail');
    const profileUsername = document.getElementById('nomdp');
    const profileDescription = document.getElementById('description_profile');
    const save_btn = document.getElementById('save_btn');
    let emailError = document.createElement('p');
    const gender_field = document.getElementById('gender');
    const date_field = document.getElementById('anniv');
    const pswd_btn = document.getElementById('pswd_btn');
    const avatar_btn = document.getElementById('avatar_btn');
    emailError.style.color = 'red';
    emailError.style.fontSize = '0.8em';
    emailError.style.display = 'none';
    emailError.textContent = 'Adresse email invalide';
    emailField.parentNode.appendChild(emailError);

    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    button.addEventListener('click', function() {
        let isDisabled = inputs[0].disabled;

        if (!isDisabled) {
            if (!isValidEmail(emailField.value)) {
                emailError.style.display = 'block';
                return;
            } else {
                emailError.style.display = 'none';
            }

            profileUsername.textContent = usernameField.value || 'Nom d\'utilisateur';
            profileDescription.textContent = descriptionField.value.trim() !== '' ? descriptionField.value : " Vous n'avez pas de desciption. Ajoutez-en une !";
        }

        inputs.forEach(input => {
            input.disabled = !input.disabled;
            input.style.cursor = input.disabled ? 'not-allowed' : 'text';
        });
        gender_field.style.cursor = inputs[0].disabled ? 'not-allowed' : 'pointer';
        date_field.style.cursor = inputs[0].disabled ? 'not-allowed' : 'pointer';
        button.textContent = inputs[0].disabled ? 'Modifier' : 'Enregistrer';

        if(button.textContent === 'Enregistrer') {
            save_btn.style.display = 'block';
            pswd_btn.style.display = 'none';
            avatar_btn.style.display = 'none';
            button.style.display = 'none';
        }

        
    });

    descriptionField.addEventListener('input', function() {
        profileDescription.textContent = descriptionField.value.trim() !== '' ? descriptionField.value : " Vous n'avez pas de desciption. Ajoutez-en une !";
    });
    usernameField.addEventListener('input', function() {
        profileUsername.textContent = usernameField.value || 'Nom d\'utilisateur';
    });
    emailField.addEventListener('input', function() {
        if (!isValidEmail(emailField.value)) {
            emailError.style.display = 'block';
        } else {
            emailError.style.display = 'none';
        }
    });
});