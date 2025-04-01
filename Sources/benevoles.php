<?php include_once "header_benevoles.php";?>

<link rel="stylesheet" href="css/benevoles.css">

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
                <label for="leave"></label>    
                <button type="submit">Quitter l'équipe</button>
            </div>
        </div>
    </div>
</div>

<div class="mail">
    <label for="email"></label>
    <button type="submit">Envoyer un mail à mon équipe</button>
</div>

<?php include_once 'footer.php'; ?>