<?php

include_once 'header.php';
include_once '../checker.php';

if(isset($_GET['code'])){
    $json_file = file_get_contents('../json/success_register.json');
    $error_manager = json_decode($json_file, true);
    if(array_key_exists($_GET['code'], $error_manager)){
        $insertVal = $_GET['code'];
    }
}

$error_details = $error_manager[$insertVal];

?>

<link rel="stylesheet" href="../css/backoffice_tablepages.css">
<link rel="stylesheet" href="../css/backoffice_image_management.css">

<div class="right_panel">
    <div class="title">
        <h2 class="highlighted-text" id="page_title">Gestion de la Base de Données</h2>
    </div>
    <div class="title">
        <h3 class="highlighted-text" id="page_title">Catégories</h3>
    </div>
    <div class="database_actions_container">
        <a class="captcha_database_action" href="add_category.php"><img src="../../Resources/img/ui_icons/plus.png" alt="Nouveau Captcha"> Nouvelle Catégorie</a>
    </div>
    <div class="tableau">
        <table>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Actions</th>
            </tr>
            <?php
            try {
                $stmt = $pdo->prepare("SELECT * FROM CATEGORY ORDER BY id ASC");
                $stmt->execute();
                $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($categories as $category) {
                    echo "<tr>";
                    echo "<td class='id'>" . htmlspecialchars($category['id'], ENT_QUOTES, 'UTF-8') . "</td>";
                    echo "<td class='content'>" . htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8') . "</td>";
                    echo "<td class='actions'>";
                    echo "<a href='modify_category_form.php?id=" . htmlspecialchars($category['id'], ENT_QUOTES, 'UTF-8') . "' class='action'><img src='../../Resources/img/ui_icons/crayon.png' alt='Modify'></a>";
                    echo "<a href='' class='void'>&nbsp;</a>";
                    echo "<a href='Processus/delete_category.php?id=" . htmlspecialchars($category['id'], ENT_QUOTES, 'UTF-8') . "' class='action'><img src='../../Resources/img/ui_icons/trash.png' alt='Delete'></a>";
                    echo "</td>";
                    echo "</tr>";
                }
            } catch (PDOException $e) {
                echo "<tr>";
                echo "<td class='id'>N/A</td>";
                echo "<td class='content'>Error</td>";
                echo "<td class='actions'></td>";
                echo "</tr>";
            }
            ?>
        </table>
    </div>
    <div class="title">
        <h3 class="highlighted-text" id="page_title">Gestion des images</h3>
    </div>
    <div class="database_actions_container">
        <a class="captcha_database_action" href="upload_image.php"><img src="../../Resources/img/ui_icons/upload.png" alt="Nouveau Captcha">&nbsp;&nbsp;Upload</a>
    </div>
    
    <?php

    
    if(isset($_GET['code']) && $_GET['code'] != ''){
        echo '
        <div class="message">
            <div class="success">
                <p class="error_text">' . $error_details .'</p>
            </div>
        </div>
        ';
    }

    ?>
    <div class="image-gallery">
    <?php
    $imageDir = "../../Resources/img/petition_selection";
    $allFiles = scandir($imageDir);
    $imageFiles = [];
    
    foreach ($allFiles as $file) {
        if ($file !== '.' && $file !== '..' && preg_match('/\.(jpg)$/i', $file)) {
            $imageFiles[] = $file;
        }
    }
    natsort($imageFiles);
    
    foreach ($imageFiles as $file) {
    $imagePath = $imageDir . '/' . $file;
    echo '<div class="image-container">';
    echo '<img src="' . $imagePath . '" alt="' . $file . '" class="petition-image">';
    echo '<div class="image-info">';
    echo '<span class="image-name">' . $file . '</span>';
    echo '<div class="action-buttons">';
    echo '<a href="/Resources/img/petition_selection/'. $file .'" target="_blank" class="download">';
    echo '<img src="../../Resources/img/ui_icons/download.png" alt="Download" class="delete-icon">';
    echo '</a>';
    echo '<a href="Processus/delete_image.php?image=' . urlencode($file) . '" class="delete-image" onclick="return confirm(\'Êtes-vous sûr de vouloir supprimer cette image?\');">';
    echo '<img src="../../Resources/img/ui_icons/trash.png" alt="Delete" class="delete-icon">';
    echo '</a>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
}
    ?>
</div>
</div>
</div>
<script src="/Sources/js/message_hider.js"></script>

<?php
include_once 'footer.php';
?>