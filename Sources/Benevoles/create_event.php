<?php

include_once 'header.php';
include_once 'security.php';
?>

<link rel="stylesheet" href="../css/create_petition.css">
<link rel="stylesheet" href="/Sources/css/benevoles_form.css">

<div class="login_form" id="login_form">
    <h1 id="loginhigh" class="highlighted-text">Créer un événement</h1>
    <hr id="loginhr">
    
    <form action="Processus/event_creation.php" method="post" class="login">
        <div class="entries">
            <div class="entries">
                <input type="text" name="title" id="title" placeholder=" " maxlength=60 required class="form-input" onkeyup="count('name_counter',this,60)">
                <label for="title">Titre de l'événement</label>
            </div>

            <div class="limit positioned" id="name_counter">
                <p>Limite de caractères : 0 / 60</p>
            </div>

            <div class="space"></div>

            <div class="entries_modify">
                <div class="area">
                    <textarea name="description" id="description" maxlength=150 onkeyup="count('desc_counter',this,150)"></textarea>
                    <label for="description" class="textarea_label txt_bis">Description</label>
                </div>
                <div class="limit positioned" id="desc_counter">
                    <p>Limite de caractères : 0 / 150</p>
                </div>
            </div>
            
            <div class="space"></div>
            
            <div class="entries">
                <input type="date" name="date" id="date" placeholder=" " required class="form-input">
                <label for="date">Date de l'événement</label>
            </div>
            
            <div class="space"></div>
            
            <div class="entries">
                <input type="number" name="max_participants" id="max_participants" placeholder=" " class="form-input">
                <label for="max_participants">Participants (Max)</label>
            </div>
            
            <div class="space"></div>
            
            <p class="smallTxt">Informations optionnelles de localisation</p>
            
            <div id="address-fields">
                <div class="entries">
                    <input type="text" name="city" id="city" placeholder=" " class="form-input address-field">
                    <label for="city">Ville</label>
                </div>
                
                <div class="space"></div>
                
                <div class="entries">
                    <input type="number" name="pcode" id="pcode" placeholder=" " class="form-input address-field">
                    <label for="pcode">Code postal</label>
                </div>
                
                <div class="space"></div>
                
                <div class="entries">
                    <input type="text" name="road" id="road" placeholder=" " class="form-input address-field">
                    <label for="road">Rue</label>
                </div>
                
                <div class="space"></div>
                
                <div class="entries">
                    <input type="number" name="num" id="num" placeholder=" " class="form-input address-field">
                    <label for="num">Numéro</label>
                </div>
            </div>
            
            <input type="hidden" name="id" id="id" value="<?php echo $_GET['id']; ?>">
        </div>
        
        <button type="submit" class="custom-button loginbtn">Créer</button>
    </form>
</div>

<script>

  document.addEventListener('DOMContentLoaded', function() {
    count('desc_counter', document.getElementById('description'), 150);
    count('name_counter', document.getElementById('name'), 60);
  });

</script>

<script src="../js/count_characters.js"></script>

<script>
    const addressFields = document.querySelectorAll('.address-field');
    
    addressFields.forEach(field => {
        field.addEventListener('input', validateAddressFields);
    });
    
    function validateAddressFields() {
        let hasValue = false;
        addressFields.forEach(field => {
            if (field.value.trim() !== '') {
                hasValue = true;
            }
        });
        
        addressFields.forEach(field => {
            if (hasValue) {
                field.required = true;
                const label = field.parentElement.querySelector('label');
                if (!label.innerHTML.includes('*')) {
                    label.innerHTML = label.innerHTML + ' *';
                }
            } else {
                field.required = false;
                const label = field.parentElement.querySelector('label');
                label.innerHTML = label.innerHTML.replace(' *', '');
            }
        });
    }
</script>

<?php
include_once 'footer.php';
?>