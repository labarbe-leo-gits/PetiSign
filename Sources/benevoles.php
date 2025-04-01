<?php include_once "header_benevoles.php";?>

<link rel="stylesheet" href="css/benevoles.css">
<link rel="stylesheet" href="css/dark.css">

<div class="title_box">
    <h1>Mon équipe bénévole</h1>
</div>
<div class="team_box">
    <h2>Nom de l'équipe</h2>
</div>

<div class="container">
    <div class="left_panel">
        <div class="members_box">
            <div class="members_stats">
                <div class="stat">
                    <h3>Membres :</h3>
                </div>
                <hr class="equip_separator">
                <div class="stat">
                    <h4>Thomas</h4>
                </div>
                <h4>Léo</h4>
                <h4>Louis</h4>
            </div>
        </div>
    </div>
    
    <div class="right_panel">
        <div class="sector_box">
            <div class="stats">
                <h3>Secteur :</h3>
                <h4>Paris XV</h4>
                <hr class="sector_separator">
                <label for="leave"></label>    
                <button type="submit">Quitter l'équipe</button>
            </div>
        </div>
    </div>

    <div class="right_panel2">
        <div class="event_box">
            <div class="stats">
                <h3>Actualités et évènements :</h3>
                <hr class="event_separator">
                <h4>Prochain évènement : 12/12/2023</h4>
                <h4>Evènement passé : 01/01/2023</h4>
                <h4>Evènement passé : 01/02/2023</h4>
            </div>
        </div>
    </div>
</div>

<div class="mail">
    <label for="email"></label>
    <button type="submit">Envoyer un mail à mon équipe</button>
</div>

<?php include_once 'footer.php'; ?>