<?php

if(!isset($_GET['id']) || empty($_GET['id'])){
    header('Location: index.php');
    exit();
}

if(isset($_SESSION['mail'])){
    $is_admin = $pdo->prepare('SELECT is_admin FROM USER WHERE mail = :mail');
    $is_admin->bindParam(':mail', $_SESSION['mail']);
    $is_admin->execute();
    $is_admin = $is_admin->fetchColumn();
}

include_once 'header.php';
include_once 'database/database.php';

$validating_petition = $pdo->prepare('SELECT * FROM PETITION WHERE id = :id');
$validating_petition->bindParam(':id', $_GET['id']);
$validating_petition->execute();
$validating_petition = $validating_petition->fetch();

if(!$validating_petition){
    header('Location: index.php');
    exit();
}

$pet_name_stmt = $pdo->prepare('SELECT title FROM PETITION WHERE id = :id');
$pet_name_stmt->bindParam(':id', $_GET['id']);
$pet_name_stmt->execute();
$pet_name = $pet_name_stmt->fetchColumn();

$pet_category_stmt = $pdo->prepare('SELECT category FROM PETITION WHERE id = :id');
$pet_category_stmt->bindParam(':id', $_GET['id']);
$pet_category_stmt->execute();
$pet_category = $pet_category_stmt->fetchColumn();

$pet_category_name = $pdo->prepare('SELECT name FROM CATEGORY WHERE id = :id');
$pet_category_name->bindParam(':id', $pet_category);
$pet_category_name->execute();
$pet_category_name = $pet_category_name->fetchColumn();

$pet_description_stmt = $pdo->prepare('SELECT description FROM PETITION WHERE id = :id');
$pet_description_stmt->bindParam(':id', $_GET['id']);
$pet_description_stmt->execute();
$pet_description = $pet_description_stmt->fetchColumn();

$pet_description = nl2br($pet_description);

$pet_signature_goal_stmt = $pdo->prepare('SELECT signature_goal FROM PETITION WHERE id = :id');
$pet_signature_goal_stmt->bindParam(':id', $_GET['id']);
$pet_signature_goal_stmt->execute();
$pet_signature_goal = $pet_signature_goal_stmt->fetchColumn();

$pet_signature_count = $pdo->prepare('SELECT signature_count FROM PETITION WHERE id = :id');
$pet_signature_count->bindParam(':id', $_GET['id']);
$pet_signature_count->execute();
$pet_signature_count = $pet_signature_count->fetchColumn();

$pet_author_stmt = $pdo->prepare('SELECT user FROM PETITION WHERE id = :id');
$pet_author_stmt->bindParam(':id', $_GET['id']);
$pet_author_stmt->execute();
$pet_author = $pet_author_stmt->fetchColumn();

$pet_author_username = $pdo->prepare('SELECT username FROM USER WHERE id = :id');
$pet_author_username->bindParam(':id', $pet_author);
$pet_author_username->execute();
$pet_author_username = $pet_author_username->fetchColumn();

$pet_author_id_stmt = $pdo->prepare('SELECT id FROM USER WHERE username = :username');
$pet_author_id_stmt->bindParam(':username', $pet_author_username);
$pet_author_id_stmt->execute();
$pet_author_id = $pet_author_id_stmt->fetchColumn();

$pet_date = $pdo->prepare('SELECT DATE_FORMAT(date, "%d/%m/%Y") FROM PETITION WHERE id = :id');
$pet_date->bindParam(':id', $_GET['id']);
$pet_date->execute();
$pet_date = $pet_date->fetchColumn();

$pet_image_id_stmt = $pdo->prepare('SELECT image_id FROM PETITION WHERE id = :id');
$pet_image_id_stmt->bindParam(':id', $_GET['id']);
$pet_image_id_stmt->execute();
$pet_image_id = $pet_image_id_stmt->fetchColumn();

$get_user_id_stmt = $pdo->prepare('SELECT id FROM USER WHERE email = :mail');
$get_user_id_stmt->bindParam(':mail', $_SESSION['mail']);
$get_user_id_stmt->execute();
$get_user_id = $get_user_id_stmt->fetchColumn();

$signature_stmt = $pdo->prepare("SELECT COUNT(*) FROM SIGNATURE WHERE id_user = :user_id AND id_petition = :petition_id");
$signature_stmt->bindParam(':user_id', $get_user_id);
$signature_stmt->bindParam(':petition_id', $_GET['id']);
$signature_stmt->execute();
$signature_count = $signature_stmt->fetchColumn();

$pet_statut_stmt = $pdo->prepare('SELECT statut FROM PETITION WHERE id = :id');
$pet_statut_stmt->bindParam(':id', $_GET['id']);
$pet_statut_stmt->execute();
$pet_statut = $pet_statut_stmt->fetchColumn();

$check_if_creator_is_banned_stmt = $pdo->prepare('SELECT COUNT(*) FROM BAN WHERE id_user = :id');
$check_if_creator_is_banned_stmt->bindParam(':id', $pet_author_id);
$check_if_creator_is_banned_stmt->execute();
$is_banned = $check_if_creator_is_banned_stmt->fetchColumn();

?>

<link rel="stylesheet" href="css/view_petition.css">

<script src="js/dynamic_underline_view.js"></script>

<div class="page_container">

    <?php

    if($is_banned > 0){
        echo '
        <div class="warning">
            <div class="warning_icon"><img src="../Resources/img/ui_icons/warning.png" alt=""></div>
            <div class="warning_text"><p>&nbsp;&nbsp;&nbsp;Le créateur de cette pétition a été banni de la plateforme. Cette pétition est donc suspendue et les commentaires ont été désactivés.</p></div>
        </div>
        ';
    }

    ?>

    <div class="main_container">
        <div class="petition_header">
            <p>&nbsp;</p>
            <div class="text">
                <h1 class="petition_name highlighted-text" id="lmm"><?=$pet_name?></h1>
                <a class="pet_category" href=""><?=$pet_category_name?></a>
            </div>
        </div>

        <div class="description">
            <p><?=$pet_description?></p>
        </div>

        <div class="test">
            <a href="Processus/report.php?id=<?= $_GET['id'] ?>&type=2" class="quick">
                <img src="../Resources/img/ui_icons/red-flag.png" alt="">
                &nbsp;Signaler un abus
            </a>

            <?php
            if($is_admin == 1){
                echo '<a href="Processus/admin_delete.php?id='.$_GET['id'].'" class="quick">
                <img src="../Resources/img/ui_icons/trash.png" alt="">&nbsp;Supprimer la pétition (Admin)
            </a>';
            }
            ?>
        </div>
    </div>

    <div class="grid">
        <div class="signatures_container">
            <div class="objectif"><?=$pet_signature_count?> / <?=$pet_signature_goal?> Signatures récoltées</div>
            <div class="sign">
                <form method="post" action="Processus/sign.php">
                    <input type="hidden" name="petition_id" value="<?=$_GET['id']?>">
                    <?php
                    if($is_banned > 0){
                        echo '<button type="button" class="sign_petition_btn disabled" disabled>Pétition suspendue</button>';
                    }else{
                    if($signature_count > 0){
                        echo '<button type="button" class="sign_petition_btn disabled" disabled><img src="../Resources/img/ui_icons/validate.png" alt="">&nbsp;Déjà signé</button>';
                    }else{
                        if($pet_statut != 'OPEN'){
                            echo '<button type="button" class="sign_petition_btn disabled" disabled><img src="../Resources/img/ui_icons/validate.png" alt="">&nbsp;Pétition fermée</button>';
                        }else{
                            echo '<button type="button" class="sign_petition_btn" onclick="show_popup_trancho()">Je Signe !</button>';
                        }
                    }
                    }
                    ?>    
                    
                </form></div>
        </div>

        <div class="petition_information">
            <p class="author">Pétition de <a href="view_profile.php?id=<?=$pet_author?>" class="profile_link"><?=$pet_author_username?></a></p>
            <p class="creation_date">Publiée le <?=$pet_date?></p>
        </div>
    </div>

    <div class="action_btn">
        <button class="custom-button" onclick="window.history.back()" id="back">Revenir en arrière</button>
    </div>
</div>

<div class="comment_section">
    <h2 class="title">Commentaires</h2>

    <?php

    if($is_banned <= 0){
        echo '
        <div class="new">
            <form method="POST" action="Processus/add_comment.php">
                <input type="hidden" name="petition_id" value="
        ';
        echo $_GET['id'];
        echo '">
                <input type="hidden" name="user_id" value="';
        echo $get_user_id;
        echo '">
                <textarea name="comment" id="comment" maxlength=800  onkeyup="count(\'desc_counter\',this,800)"></textarea>
                <div class="limit positioned" id="desc_counter">
                        <p>Limite de caractères : 0 / 800</p>
                </div>

                <button type="submit" class="comment_btn custom-button"><img src="../Resources/img/ui_icons/send.png" alt="Envoyer"><p>&nbsp; Publier</p></button>
            </form>
        </div>';
    }

    ?>

    <?php

    $comments_stmt = $pdo->prepare('SELECT * FROM COMMENT WHERE id_petition = :id ORDER BY date DESC');
    $comments_stmt->bindParam(':id', $_GET['id']);
    $comments_stmt->execute();
    $comments = $comments_stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach($comments as $comment){
        $comment_user_stmt = $pdo->prepare('SELECT username FROM USER WHERE id = :id');
        $comment_user_stmt->bindParam(':id', $comment['id_user']);
        $comment_user_stmt->execute();
        $comment_user = $comment_user_stmt->fetchColumn();

        $get_avatar_hat = $pdo->prepare('SELECT avatar_hat FROM USER WHERE id = :id');
        $get_avatar_hat->bindParam(':id', $comment['id_user']);
        $get_avatar_hat->execute();
        $avatar_hat = $get_avatar_hat->fetchColumn();

        $get_avatar_eyes = $pdo->prepare('SELECT avatar_eyes FROM USER WHERE id = :id');
        $get_avatar_eyes->bindParam(':id', $comment['id_user']);
        $get_avatar_eyes->execute();
        $avatar_eyes = $get_avatar_eyes->fetchColumn();

        $get_avatar_mouth = $pdo->prepare('SELECT avatar_mouth FROM USER WHERE id = :id');
        $get_avatar_mouth->bindParam(':id', $comment['id_user']);
        $get_avatar_mouth->execute();
        $avatar_mouth = $get_avatar_mouth->fetchColumn();

        $get_avatar_skin = $pdo->prepare('SELECT avatar_skin FROM USER WHERE id = :id');
        $get_avatar_skin->bindParam(':id', $comment['id_user']);
        $get_avatar_skin->execute();
        $avatar_skin = $get_avatar_skin->fetchColumn();

        $get_avatar_hat_color = $pdo->prepare('SELECT avatar_hat_color FROM USER WHERE id = :id');
        $get_avatar_hat_color->bindParam(':id', $comment['id_user']);
        $get_avatar_hat_color->execute();
        $avatar_hat_color = $get_avatar_hat_color->fetchColumn();

        $get_avatar_eyes_color = $pdo->prepare('SELECT avatar_eyes_color FROM USER WHERE id = :id');
        $get_avatar_eyes_color->bindParam(':id', $comment['id_user']);
        $get_avatar_eyes_color->execute();
        $avatar_eyes_color = $get_avatar_eyes_color->fetchColumn();

        $get_avatar_mouth_color = $pdo->prepare('SELECT avatar_mouth_color FROM USER WHERE id = :id');
        $get_avatar_mouth_color->bindParam(':id', $comment['id_user']);
        $get_avatar_mouth_color->execute();
        $avatar_mouth_color = $get_avatar_mouth_color->fetchColumn();

        $get_avatar_skin_color = $pdo->prepare('SELECT avatar_skin_color FROM USER WHERE id = :id');
        $get_avatar_skin_color->bindParam(':id', $comment['id_user']);
        $get_avatar_skin_color->execute();
        $avatar_skin_color = $get_avatar_skin_color->fetchColumn();

        $formatted_date = date('d/m/Y', strtotime($comment['date']));
        $formated_time = date('H:i', strtotime($comment['date']));

        echo '<div class="comment">
                <div class="user_info">
                    <div class="avatar">
                        <img class="skin" src="../Resources/avatar/skin/skin'.$avatar_skin.'c'.$avatar_skin_color.'.png" alt="">
                        <img src="../Resources/avatar/hat/hat'.$avatar_hat.'c'.$avatar_hat_color.'.png" class="hat" alt="Hat" id="hat">
                        <img src="../Resources/avatar/eyes/eye'.$avatar_eyes.'c'.$avatar_eyes_color.'.png" class="eyes" alt="Eyes" id="eyes">
                        <img src="../Resources/avatar/mouth/smile'.$avatar_mouth.'c'.$avatar_mouth_color.'.png" class="mouth" alt="Mouth" id="mouth">
                    </div>
                    <a href="view_profile.php?id='. $comment['id_user'] .'" class="profile_link_comment">'.$comment_user.'</a>
                </div>
                <div class="comment_content">
                    <p class="comment_date">'.$formatted_date.' &#x25CF; '.$formated_time.' &#x25CF ';

                    if($is_admin == 1){
                        echo '<a href="Processus/admin_delete_com.php?id='.$comment['id'].'" class="quick2">
                        <img src="../Resources/img/ui_icons/trash.png" alt=""></a>';
                    }

                    echo '
                    <a href="Processus/report.php?id='.$comment['id'].'&type=3" class="quick2">
                    <img src="../Resources/img/ui_icons/red-flag.png" alt=""></a>';

                    echo '</p>
                    <div class="comment_text">'.$comment['content'].'</div>
                </div>
            </div>';
    }

    ?>
</div>

<div class="space">&nbsp;</div>

<link rel="stylesheet" href="css/sign_popup.css">

<div class="filter">&nbsp;</div>

    <div class="container_ popup">
        <div class="close"><img onclick="hide_popup_trancho()" src="../Resources/img/ui_icons/plus.png" alt="Fermer la Popup"></div>
        <div class="right">
            <form action="Processus/sign.php" method="post">
                <input type="hidden" name="petition_id" value="<?=$_GET['id']?>">
                <h1>Signer "<?=$pet_name?>"</h1>
                <p class="slogan">Signer, c'est changer le monde</p>
                <hr>
                <p>En cliquant sur signer, j'affirme avoir pris conscience des conditions d'utilisation de PétiSign.</p>
                <p>De plus, vos données personelles seront utilisées à des fins de statistiques pour PétiSign et ses filliales.</p>
                <input type="checkbox" name="check" id="check" required>
                <label for="check">J'accepte les conditions d'utilisation de PétiSign</label><br>
                <input type="checkbox" name="check2" id="check2" required>
                <label for="check2">J'affirme avoir pris connaissance des éléments mentionnés ci-dessus</label>
                <hr class="bottom_hr">
                <div class="send"><button class="button_" type="submit">Signer</button></div>
            </form>
        </div>
    </div>
</div>

<style>
    .petition_header {
        background-image: url("../../Resources/img/petition_selection/<?=$pet_image_id?>.jpg");
    }
</style>
<script src="js/count_characters.js"></script>
<script src="js/trancho_popup.js"></script>

<?php
include_once 'footer.php';
?>
