<?php
include_once 'header.php';
include_once '../database/database.php';

try{
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM USER");
    $stmt->execute();
    $users = $stmt->fetchColumn();
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

?>

<link rel="stylesheet" href="../css/backoffice_index.css">

<div class="right_panel">
    <h2>Bienvenue sur le Back Office !</h2>
    <div class="stats">
        <h3>Statistiques de PétiSign</h3>
        <hr id="stats_sep_bo">
        <div class="stat">
            <h4>Nombre d'utilisateurs :</h4>
            <p><?=$users?></p>
        </div>
        <div class="stat">
            <h4>Nombre de pétitions :</h4>
            <p> 0</p>
        </div>
        <div class="stat">
            <h4>Somme total des dons récoltés :</h4>
            <p>0</p>
            <p>€</p>
        </div>
    </div>
</div>
</div>

<?php
include_once 'footer.php';
?>