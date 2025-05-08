<?php
include_once 'header.php';
include_once '../database/database.php';

$id = $_GET['id'] ?? null;
$filtered_id = filter_var($id, FILTER_VALIDATE_INT);

$get_all_benevoles_stmt = $pdo->prepare("SELECT id, username FROM USER WHERE is_benevole != 0");
$get_all_benevoles_stmt->execute();
$all_benevoles = $get_all_benevoles_stmt->fetchAll(PDO::FETCH_ASSOC);

$get_leader_id = $pdo->prepare("SELECT leader FROM TEAM WHERE id = :id");
$get_leader_id->bindParam(':id', $filtered_id, PDO::PARAM_INT);
$get_leader_id->execute();
$leader_id = $get_leader_id->fetchColumn();

$got_all_members_of_team = $pdo->prepare("SELECT id_user FROM TEAM_MEMBER WHERE id_team = :id");
$got_all_members_of_team->bindParam(':id', $filtered_id, PDO::PARAM_INT);
$got_all_members_of_team->execute();
$all_members_of_team = $got_all_members_of_team->fetchAll(PDO::FETCH_COLUMN);

$team_infos = $pdo->prepare("SELECT name, sector, description FROM TEAM WHERE id = :id");
$team_infos->bindParam(':id', $filtered_id, PDO::PARAM_INT);
$team_infos->execute();
$team_infos = $team_infos->fetch(PDO::FETCH_ASSOC);

?>

<link rel="stylesheet" href="../css/backoffice_addcaptcha.css">
<link rel="stylesheet" href="../css/backoffice_addteam.css">

<div class="right_panel">
    <div class="captcha_form" id="captcha_form">
        <h1 id="loginhigh" class="highlighted-text">Modifier l'équipe</h1>
        <hr id="loginhr">
        <form method="post" class="login" action="Processus/update_team.php">
            <div class="entries">
                <div class="entries">
                    <input name="name" id="name" type="text" value="<?=$team_infos['name']?>" required placeholder=" ">
                    <label for="name">Nom</label>
                </div>
                <div class="space"></div>
                <div class="entries">
                    <input name="sector" id="sector" type="text" value="<?=$team_infos['sector']?>" placeholder=" ">
                    <label for="sector">Secteur</label>
                </div>
                <div class="space"></div>
                <h2>Leader de l'équipe</h2>
                <div class="entries benevoles_selector">
                    <?php foreach ($all_benevoles as $benevole): ?>
                        <?php
                        $is_leader = ($benevole['id'] == $leader_id) ? 'selected' : '';
                        ?>
                        <div class="selectable_ <?=$is_leader?>" data-id="<?php echo $benevole['id']; ?>">
                            <?php echo htmlspecialchars($benevole['username'], ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                    <?php endforeach; ?>
                    <input type="hidden" name="selected_leader" id="selected_leader" value="0">
                </div>
                <div class="space"></div>
                <h2>Membres de l'équipe</h2>
                <div class="entries benevoles_selector">
                    <?php foreach ($all_benevoles as $benevole): ?>
                        <?php
                        $is_member = in_array($benevole['id'], $all_members_of_team); 
                        if($is_member) {
                            $selected = 'selected';
                        } else {
                            $selected = '';
                        }
                        ?>
                        <div class="selectable <?=$selected?>" data-id="<?php echo $benevole['id']; ?>">
                            <?php echo htmlspecialchars($benevole['username'], ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                    <?php endforeach; ?>
                    <input type="hidden" name="selected_benevoles" id="selected_benevoles" value="0">
                </div>               
            </div>
            <input type="hidden" name="team_id" id="team_id" value="<?php echo htmlspecialchars($_GET['id'], ENT_QUOTES, 'UTF-8'); ?>">
            <button class="custom-button" id="add_btn" type="submit">Modifier</button>
        </form>
        <button class="custom-button" onclick="window.location.href='teams.php'" id="cancel_btn">Annuler</button>
    </div>
</div>
</div>

<script src="../js/team_updater.js"></script>

<?php
include_once 'footer.php';
?>