<?php
include_once 'header.php';
include_once '../database/database.php';
include_once 'security.php';

$current_user_id = $pdo->prepare("SELECT id FROM USER WHERE email = :mail");
$current_user_id->bindParam(':mail', $_SESSION['mail'], PDO::PARAM_STR);
$current_user_id->execute();
$current_user_id = $current_user_id->fetchColumn();

$id = $_GET['id'] ?? null;
$filtered_id = filter_var($id, FILTER_VALIDATE_INT);

$get_event_creator = $pdo->prepare("SELECT id_user FROM TEAM_ACTIVITY WHERE id = :id");
$get_event_creator->bindParam(':id', $filtered_id, PDO::PARAM_INT);
$get_event_creator->execute();
$event_creator_id = $get_event_creator->fetchColumn();

if($current_user_id !== $event_creator_id) {
    echo "<script>window.location.href='team.php?id=$filtered_id';</script>";
    exit();
}

$activity_infos = $pdo->prepare("SELECT * FROM TEAM_ACTIVITY WHERE id = :id");
$activity_infos->bindParam(':id', $filtered_id, PDO::PARAM_INT);
$activity_infos->execute();
$activity_infos = $activity_infos->fetch(PDO::FETCH_ASSOC);

?>

<link rel="stylesheet" href="../css/create_petition.css">
<link rel="stylesheet" href="../css/backoffice_addcaptcha.css">
<link rel="stylesheet" href="../css/backoffice_addteam.css">
<link rel="stylesheet" href="../css/benevoles_team.css">

<div class="right_panel">
    <div class="captcha_form" id="captcha_form">
        <h1 id="loginhigh" class="highlighted-text">Modifier l'évènement</h1>
        <hr id="loginhr">
        <form method="post" class="login" action="Processus/update_activity.php">
            <div class="entries">
                <div class="entries">
                    <input name="name" id="name" type="text" onkeyup="count('name_counter',this,60)" value="<?=$activity_infos['name']?>" maxlength=60 required placeholder=" ">
                    <label for="name">Nom</label>
                </div>
                <div class="limit positioned" id="name_counter">
                    <p>Limite de caractères : 0 / 60</p>
                </div>
                <div class="space"></div>
                <div class="entries">
                    <input name="event_date" id="event_date" type="date" value="<?=$activity_infos['event_date']?>" placeholder=" ">
                    <label for="event_date">Date de l'évènement</label>
                </div>
                <div class="space"></div>
                <div class="entries_modify">
                <div class="area">
                    <textarea name="description" id="description" maxlength=150 onkeyup="count('desc_counter',this,150)"><?php echo $activity_infos['description'] ?></textarea>
                    <label for="description" class="textarea_label txt_bis">Description</label>
                </div>
                <div class="limit positioned" id="desc_counter">
                    <p>Limite de caractères : 0 / 150</p>
                </div>
            </div>
                <div class="space"></div>
                <div class="entries">
                    <input name="nb_part" id="nb_part" type="number" value="<?=$activity_infos['max_participants']?>" placeholder=" ">
                    <label for="nb_part">Nombre de participants</label>
                </div>
                <div class="space"></div>
                <div id="address-fields">
                <div class="entries">
                    <input type="text" name="city" id="city" placeholder=" " value="<?php echo $activity_infos['city'] ?>" class="form-input address-field">
                    <label for="city">Ville</label>
                </div>
                
                <div class="space"></div>
                
                <div class="entries">
                    <input type="number" name="pcode" id="pcode" placeholder=" " value="<?php echo $activity_infos['postal_code'] ?>" class="form-input address-field">
                    <label for="pcode">Code postal</label>
                </div>
                
                <div class="space"></div>
                
                <div class="entries">
                    <input type="text" name="road" id="road" placeholder=" " value="<?php echo $activity_infos['rue'] ?>" class="form-input address-field">
                    <label for="road">Rue</label>
                </div>
                
                <div class="space"></div>
                
                <div class="entries">
                    <input type="number" name="num" id="num" placeholder=" " value="<?php echo $activity_infos['num'] ?>" class="form-input address-field">
                    <label for="num">Numéro</label>
                </div>
            </div>

                <div class="space"></div>
                            
            </div>
            <input type="hidden" name="activity_id" value="<?=$filtered_id?>">
            <button class="custom-button" id="add_btn" type="submit">Modifier</button>
        </form>
        <button class="custom-button" onclick="window.location.href='view_activity.php?id=<?=$filtered_id?>'" id="cancel_btn">Annuler</button>
    </div>
</div>
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

document.addEventListener('DOMContentLoaded', validateAddressFields);

</script>

<script src="../js/team_updater.js"></script>

<?php
include_once 'footer.php';
?>