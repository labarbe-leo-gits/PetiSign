<?php include_once "header_benevoles.php";?>

<link rel="stylesheet" href="css/benevoles.css">

<div class="title">
    <h1>Mon équipe bénévole</h1>
</div>


<div class="container">
    <div class="left_panel">
        <h2>Mon équipe bénévole</h2>
        <div class="team_name">
            <h3>Team name</h3>
        </div>
        <div class="members_stats">
            <div class="stat">
                <h4>Membres :</h4>
                <p><?=$users?></p>
            </div>
            <div class="stat">
                <h4>Dirigeant :</h4>
            </div>
            <h4>Membres :</h4>
        </div>
    </div>

    <div class="right_panel">
        <h2>Secteurs</h2>
        <div class="actions">
            <h3>Secteur :</h3>
            <hr id="stats_sep_bo">
            <div class="stat">
                <h4>Paris XIV</h4>
                <p><?=$actions?></p>
            </div>
        </div>
    </div>
</div>

<?php include_once 'footer.php'; ?>