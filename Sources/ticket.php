<?php

session_start();
if(isset($_SESSION['ban'])){
    header("Location: ban.php");
    exit();
}

include_once 'header.php';

?>

<div id="success-message" class="success">
    <p>Votre demande a bien été prise en compte. Notre équipe reviendra vers vous prochainement.</p>
</div>

<link rel="stylesheet" href="css/login_register.css">
<link rel="stylesheet" href="css/login.css">
<link rel="stylesheet" href="css/create_petition.css">
<link rel="stylesheet" href="css/ticket.css">

<div class="login_form" id="login_form">
    <h1 id="loginhigh" class="highlighted-text">Demande de support</h1>
    <hr id="loginhr">
    <form method="POST" action="create_ticket.php" class="login" id="ticket-form">
        <div class="entries">
            <div class="entries">
                <input name="name" id="name" type="text" required placeholder=" " class="form-input">
                <label for="name">Nom *</label>
            </div>
            <div class="space"></div>
            <div class="entries">
                <input name="firstname" id="firstname" type="text" required placeholder=" " class="form-input">
                <label for="firstname">Prénom *</label>
            </div>
            <div class="space"></div>
            <div class="entries">
                <input name="email" id="email" type="email" required placeholder=" " class="form-input">
                <label for="email">Adresse e-mail *</label>
            </div>
            <div class="space"></div>
            <div class="entries">
                <input name="phone" id="phone" type="text" placeholder=" " class="form-input">
                <label for="phone">Téléphone</label>
            </div>
            <div class="space"></div>
            <hr id="loginhr">
            <div class="space"></div>
            <div class="entries">
                <div class="category-select">
                    <select name="category" id="category">
                        <option value="">Sélectionnez une catégorie</option>
                        <option value="1">Problème technique</option>
                        <option value="2">Demande d'assistance</option>
                        <option value="3">Incident</option>
                        <option value="4">Demande de service</option>
                    </select>
                </div>
            </div>
            <div class="space"></div>
            <div class="entries">
                <div class="category-select">
                    <select name="urgency" id="urgency">
                        <option value="">Sélectionnez un niveau d'urgence</option>
                        <option value="1">Très basse</option>
                        <option value="2">Basse</option>
                        <option value="3">Moyenne</option>
                        <option value="4">Haute</option>
                        <option value="5">Très haute</option>
                    </select>
                </div>
            </div>
            <div class="space"></div>
            <div class="entries">
                <input name="title" id="title" type="text" required placeholder=" " onkeyup="count('name_counter',this,60)" maxlength=60 class="form-input">
                <label for="title">Titre *</label>
            </div>
            <div class="limit positioned" id="name_counter">
                    <p>Limite de caractères : 0 / 60</p>
            </div>
            <div class="space"></div>
            <div class="entries_modify">
                <div class="area">
                    <textarea name="description" id="description" maxlength=600 onkeyup="count('desc_counter',this,600)"></textarea>
                    <label for="description" class="textarea_label txt_bis"></label>
                </div>
                <div class="limit positioned" id="desc_counter">
                    <p>Limite de caractères : 0 / 600</p>
                </div>
            </div>
            <div class="space"></div>
            <hr id="loginhr">
            <div class="space"></div>
            <button type="submit">Soumettre le ticket</button>
        </div>
    </form>
</div>

<script>
document.getElementById('ticket-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    let form = this;
    let isValid = true;
    
    if (isValid) {
        const formData = {
            name: document.getElementById('name').value,
            firstname: document.getElementById('firstname').value,
            email: document.getElementById('email').value,
            phone: document.getElementById('phone').value,
            category: document.getElementById('category').value,
            urgency: document.getElementById('urgency').value,
            title: document.getElementById('title').value,
            description: document.getElementById('description').value
        };
        
        fetch('create_ticket.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('success-message').style.display = 'block';
                form.reset();
                count('desc_counter', document.getElementById('description'), 600);
                count('name_counter', document.getElementById('title'), 60);
            } else {
                alert('Erreur: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Une erreur est survenue lors de la création du ticket. Veuillez réessayer plus tard.');
        });
    }
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    count('desc_counter', document.getElementById('description'), 600);
    count('name_counter', document.getElementById('title'), 60);
});
</script>

<script src="js/count_characters.js"></script>

<?php
include_once 'footer.php';
?>