<?php

session_start();

include_once 'database/database.php';
include_once 'checker.php';

if(!isset($_GET['id']) || empty($_GET['id'])){
    header('Location: index.php');
    exit();
}

$pet_id = $_GET['id'];

$count_stmt = $pdo->prepare('SELECT COUNT(*) FROM PETITION WHERE id = :id');
$count_stmt->bindParam(':id', $pet_id, PDO::PARAM_INT);
$count_stmt->execute();
$count = $count_stmt->fetchColumn();

if($count <= 0){
    header('Location: discover.php');
    exit();
}

if(isset($_SESSION['mail'])){
    $is_admin = $pdo->prepare('SELECT is_admin FROM USER WHERE email = :mail');
    $is_admin->bindParam(':mail', $_SESSION['mail']);
    $is_admin->execute();
    $is_admin = $is_admin->fetchColumn();
}

$validating_petition = $pdo->prepare('SELECT * FROM PETITION WHERE id = :id');
$validating_petition->bindParam(':id', $_GET['id']);
$validating_petition->execute();
$validating_petition = $validating_petition->fetch();

if(!$validating_petition){
    header('Location: index.php');
    exit();
}

$count_pet_with_this_id = $pdo->prepare('SELECT COUNT(id) FROM PETITION WHERE id = :id');
$count_pet_with_this_id->bindParam(':id', $_GET['id']);
$count_pet_with_this_id->execute();
$count_pet_with_this_id = $count_pet_with_this_id->fetchColumn();

if($count_pet_with_this_id <= 0){
    header('Location: index.php');
    exit();
}

include_once 'header.php';

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

$pet_description = html_entity_decode($pet_description, ENT_QUOTES, 'UTF-8');
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

        $set_pet_status_to_closed_stmt = $pdo->prepare('UPDATE PETITION SET statut = :statut WHERE id = :id');
        $set_pet_status_to_closed_stmt->bindParam(':statut', 'CLOSED');
        $set_pet_status_to_closed_stmt->bindParam(':id', $_GET['id']);
        $set_pet_status_to_closed_stmt->execute();

    }

    if($pet_author_id == $get_user_id){
        echo '
        <div class="warning">
            <div class="warning_text"><p>&nbsp;&nbsp;&nbsp;Vous êtes le créateur de cette pétition. Vous pouvez la modifier et la supprimer via votre espace "<a href="my_petitions.php">Mes Pétitions</a>" ou via les boutons ci-dessous.</p></div>
        </div>
        ';
    }

    ?>

    <div class="main_container">
        <div class="petition_header">
            <p>&nbsp;</p>
            <div class="text">
                <h1 class="petition_name highlighted-text" id="lmm"><?= html_entity_decode($pet_name)?></h1>
                <a class="pet_category" href="search.php?category_id=<?=$pet_category?>"><?=$pet_category_name?></a>
            </div>
        </div>

        <div class="description">
            <p><?=$pet_description?></p>
        </div>

        <div class="test">

            <?php

            $check_if_already_reported_stmt = $pdo->prepare('SELECT COUNT(*) FROM REPORT WHERE id_user = :user_id AND id_target = :petition_id AND report_type = 2');
            $check_if_already_reported_stmt->bindParam(':user_id', $get_user_id);
            $check_if_already_reported_stmt->bindParam(':petition_id', $_GET['id']);
            $check_if_already_reported_stmt->execute();
            $already_reported = $check_if_already_reported_stmt->fetchColumn();

            if($get_user_id == $pet_author_id){
                echo '<a href="modify_petition.php?id='.$_GET['id'].'" class="quick">
                    <img src="../Resources/img/ui_icons/crayon.png" alt="Modifier">
                    &nbsp;&nbsp;Modifier la pétition
                </a>';
                echo '<a href="Processus/delete_petition.php?id='. $_GET['id'] .'" class="quick">
                    <img src="../Resources/img/ui_icons/trash.png" alt="Supprimer">
                    &nbsp;&nbsp;Supprimer la pétition
                </a>';
                echo '<a href="modify_petition.php?id='.$_GET['id'].'" class="quick mobile_ver">
                    <img src="../Resources/img/ui_icons/crayon.png" alt="Modifier">
                    
                </a>';
                echo '<a href="Processus/delete_petition.php?id='. $_GET['id'] .'" class="quick mobile_ver">
                    <img src="../Resources/img/ui_icons/trash.png" alt="Supprimer">
                    
                </a>';

            }

            if($get_user_id != $pet_author_id){

                if($already_reported == 0){
                    echo '<a href="Processus/report.php?id='.$_GET['id'].'&type=2" class="quick">
                    <img src="../Resources/img/ui_icons/red-flag.png" alt="Signaler">
                    &nbsp;Signaler un abus
                </a>';
                echo '<a href="Processus/report.php?id='. $_GET['id'] .'&type=2" class="quick mobile_ver">
                    <img src="../Resources/img/ui_icons/red-flag.png" alt="Signaler">
                    
                </a>';
                }else{
                    echo '<p class="quick disabled">
                    <img src="../Resources/img/ui_icons/red-flag.png" alt="">
                    &nbsp;Signalement déjà effectué
                </p>';
                echo '<p class="quick disabled mobile_ver">
                    <img src="../Resources/img/ui_icons/red-flag.png" alt="">
                    &nbsp;Signalement déjà effectué
                </p>';
                }
            }

            ?>

            <?php
            if($is_admin == 1 && $pet_author_id != $get_user_id){
                echo '<a href="Processus/admin_delete.php?id='.$_GET['id'].'" class="quick">
                <img src="../Resources/img/ui_icons/trash.png" alt="">&nbsp;Supprimer la pétition (Admin)
            </a>';
            echo '<a href="Processus/admin_delete.php?id='.$_GET['id'].'" class="quick mobile_ver">
                <img src="../Resources/img/ui_icons/trash.png" alt="">
            </a>';
            }

            
            echo "<a class='show_qr quick qr_code_action_link' id='show_qr'><img src='../Resources/img/ui_icons/qr-code.png' alt=''></a>";
            echo '<a class="quick mobile_ver" id="show_qr2" onclick="openQRPopup()">
                <img src="../Resources/img/ui_icons/qr-code.png" alt="">
            </a>';
            ?>
        </div>
    </div>

    <?php
    $get_all_signatures_stmt = $pdo->prepare('SELECT * FROM SIGNATURE WHERE id_petition = :petition_id');
    $get_all_signatures_stmt->bindParam(':petition_id', $_GET['id']);
    $get_all_signatures_stmt->execute();
    $all_signatures = $get_all_signatures_stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <div class="grid">
<div class="signatures_container">
    <div class="objectif clickable-signature-count" onclick="showSignersPopup()"><?=$pet_signature_count?> / <?=$pet_signature_goal?> Signatures récoltées</div>
    <div class="sign">
                <form method="post" action="Processus/sign.php" class="sign_petition_form">
                    <input type="hidden" name="petition_id" value="<?=$_GET['id']?>">
                    <?php
                    if($is_banned > 0){
                        echo '<button type="button" class="sign_petition_btn disabled" disabled>Pétition suspendue</button>';
                    }else{
                    if($signature_count > 0){
                        //echo '<button type="button" class="sign_petition_btn disabled" disabled><img src="../Resources/img/ui_icons/validate.png" alt="">&nbsp;&nbspDéjà signé</button>';
                        echo '<button type="button" class="sign_petition_btn mobile_sign_button" onclick="window.location.href=\'Processus/delete_sign.php?id='. $_GET['id'] .'\'">Retirer ma signature</button>';
                        echo '<button type="button" class="sign_petition_btn" onclick="window.location.href=\'Processus/delete_sign.php?id='. $_GET['id'] .'\'">Retirer ma signature</button>';

                        echo '<a class="view_signature_btn" onclick="showSignaturePopup()"><img src="/Resources/img/ui_icons/eye.png" alt="Voir"></a>';

                    }else{
                        if($pet_statut != 'OPEN'){
                            echo '<button type="button" class="sign_petition_btn mobile_sign_button disabled" disabled><img src="../Resources/img/ui_icons/validate.png" alt="">&nbsp;Pétition fermée</button>';
                        }else{

                        if($get_user_id != $pet_author_id){
                            echo '<button type="button" class="sign_petition_btn" onclick="show_popup_trancho()">Je Signe !</button>';
                            echo '<button type="button" class="sign_petition_btn mobile_sign_button" onclick="showMobileSignaturePopup()">Je Signe !</button>';
                        }
                        else{
                            echo '<button type="button" class="sign_petition_btn disabled mobile_sign_button" disabled title="Vous êtes le créateur de cette pétition">Impossible de signer</button>';
                            echo '<button type="button" class="sign_petition_btn disabled" disabled title="Vous êtes le créateur de cette pétition">Impossible de signer</button>';
                        }
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
    <div class="action_btn_viewpetition">
    <div class="action_btn">
        <button class="custom-button" onclick="window.history.back()" id="back">Revenir en arrière</button>
    </div>
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
                <textarea required name="comment" id="comment" maxlength=200  onkeyup="count(\'desc_counter\',this,200)"></textarea>
                <div class="limit positioned" id="desc_counter">
                        <p>Limite de caractères : 0 / 200</p>
                </div>

                <button type="submit" class="comment_btn custom-button"><img src="../Resources/img/ui_icons/send.png" alt="Envoyer"><p>&nbsp; Publier</p></button>
            </form>
        </div>';
    }

    ?>

    <?php

    $comments_stmt = $pdo->prepare('SELECT * FROM COMMENT WHERE id_target = :id AND target_type = 1 ORDER BY date DESC');
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

                    if($is_admin == 1 || $comment['id_user'] == $get_user_id){
                        echo '<a href="Processus/delete_com.php?id='.$comment['id'].'" class="quick2">
                        <img src="../Resources/img/ui_icons/trash.png" alt=""></a>';
                    }

                    if($comment['id_user'] != $get_user_id){
                        echo '
                    <a href="Processus/report.php?id='.$comment['id'].'&type=3" class="quick2">
                    <img src="../Resources/img/ui_icons/red-flag.png" alt=""></a>';
                    }

                    echo '</p>
                    <div class="comment_text">'.html_entity_decode(nl2br(htmlspecialchars($comment['content']))).'</div>
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

<div class="signers-popup-overlay" id="signersPopupOverlay">
    <div class="signers-popup" onclick="event.stopPropagation()">
        <div class="signers-popup-header">
            <h2 class="pop_title">Signatures</h2>
            <button class="close-popup" onclick="hideSignersPopup()" title="Fermer">
                <img src="../Resources/img/ui_icons/plus.png" alt="Fermer">
            </button>
        </div>
        <div class="signers-list">
            <?php if (empty($all_signatures)): ?>
                <div style="text-align: center; padding: 20px; color: #666;">Aucune signature pour le moment.</div>
            <?php else: ?>
                <?php foreach($all_signatures as $signature): ?>
                    <div class="signer-item">
                        <?php
                        $check_if_public = $pdo->prepare('SELECT user_public FROM USER WHERE id = :id');
                        $check_if_public->bindParam(':id', $signature['id_user']);
                        $check_if_public->execute();
                        $is_public = $check_if_public->fetchColumn();

                        if($is_public == 1){
                            $id_to_username_stmt = $pdo->prepare('SELECT username FROM USER WHERE id = :id');
                            $id_to_username_stmt->bindParam(':id', $signature['id_user']);
                            $id_to_username_stmt->execute();
                            $signature['username'] = $id_to_username_stmt->fetchColumn();
                            $user_id = $signature['id_user'];
                            $link = "view_profile.php?id=".$user_id;
                        }else{
                            $signature['username'] = 'PétiSigner Anonyme';
                            $user_id = "AnonymousUsr";
                            $link = "javascript:void(0);";
                        }
                        ?>
                        <div class="signer_username">
                            <img src="/Resources/img/ui_icons/unlogged_user.png" alt="">
                        </div>
                        <div class="signer-info" onclick="window.location.href='<?=$link?>'">
                            <a href="<?= $link ?>" class="signer-name"><?=$signature['username']?></a>
                            <div class="signer-date"><?=date('d/m/Y à H:i', strtotime($signature['date']))?> UTC</div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="mobile-signature-overlay" id="mobileSignatureOverlay">
    <div class="mobile-signature-popup">
        <div class="mobile-signature-header">
            <h2>Signature numérique</h2>
            <button class="close-mobile-signature" onclick="hideMobileSignaturePopup()" title="Fermer">
                <img src="../Resources/img/ui_icons/plus.png" alt="Fermer">
            </button>
        </div>
        
        <div class="signature-instructions">
            <p>Dessinez votre signature dans la zone ci-dessous :</p>
        </div>
        
        <div class="canvas-container">
            <canvas id="signatureCanvas" width="400" height="200"></canvas>
        </div>
        
        <div class="signature-actions">
            <button type="button" class="clear-signature-btn" onclick="clearCanvas()">
                <img src="../Resources/img/ui_icons/refresh.png" alt="Effacer">
                Effacer
            </button>
            <button type="button" class="submit-signature-btn" onclick="submitMobileSignature()">
                <img src="../Resources/img/ui_icons/validate.png" alt="Valider">
                Valider ma signature
            </button>
        </div>
        
        <div class="signature-terms">
            <input type="checkbox" id="mobileCheck1" required>
            <label for="mobileCheck1">J'accepte les conditions d'utilisation de PétiSign</label><br>
            <input type="checkbox" id="mobileCheck2" required>
            <label for="mobileCheck2">J'affirme avoir lu le contenu de la pétition</label>
        </div>
    </div>
</div>

<div class="view_sign_overlay">
<div class="view_signature_popup">
    <div class="popup_sign_header">
        <h2>Ma signature</h2>
        <button class="close-popup" onclick="hideSignaturePopup()" title="Fermer">
            <img src="/Resources/img/ui_icons/plus.png" alt="Fermer">
        </button>
    </div>
    <div class="popup_sign_body">
        <?php
        $check_if_mobile_signature_filename = $pdo->prepare('SELECT mobile_signature_filename FROM SIGNATURE WHERE id_petition = :petition_id AND id_user = :user_id');
        $check_if_mobile_signature_filename->bindParam(':petition_id', $_GET['id']);
        $check_if_mobile_signature_filename->bindParam(':user_id', $get_user_id);
        $check_if_mobile_signature_filename->execute();
        $mobile_or_not = $check_if_mobile_signature_filename->fetchColumn();

        if($mobile_or_not != null){

            $file_exists = file_exists('../Resources/signatures/' . $mobile_or_not);

            if($file_exists){
                echo "
            
                <div class='signature_image'>
                    <canvas title='Signature effectuée sur mobile' id='signatureCanvasView' width='400' height='200'></canvas>
                    <script>
                        const canvas = document.getElementById('signatureCanvasView');
                        const ctx = canvas.getContext('2d');
                        const image = new Image();
                        image.src = '/Resources/signatures/{$mobile_or_not}';
                        image.onload = function() {
                            ctx.drawImage(image, 0, 0, canvas.width, canvas.height);
                        };
                    </script>
                </div>
                
                ";
            }else{
                echo "
                <div class='signature_image'>
                    <p class='error_message'>Signature numérique non trouvée.</p>
                </div>
                ";
            }

        }        

        ?>
        <div class="signature_info">
            <p class="signature_text">Signature numérique pour la pétition : <strong><?=$pet_name?></strong></p>
            <p class="signature_text">Signée le : <strong><?=date('d/m/Y à H:i', strtotime($validating_petition['date']))?> UTC</strong></p>
        </div>
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
<script src="js/signers_popup.js"></script>
<script src="js/draw.js"></script>

<script>
    function showSignaturePopup() {
    const popup = document.querySelector('.view_signature_popup');
    const overlay = document.querySelector('.view_sign_overlay');
    if (popup && overlay) {
        overlay.style.display = 'flex';
        overlay.offsetHeight;
        overlay.classList.add('show');
        popup.classList.add('show');
        
        document.body.style.overflow = 'hidden';
    }
}

function hideSignaturePopup() {
    const popup = document.querySelector('.view_signature_popup');
    const overlay = document.querySelector('.view_sign_overlay');
    if (popup && overlay) {
        popup.classList.remove('show');
        overlay.classList.remove('show');
        
        setTimeout(() => {
            overlay.style.display = 'none';
            document.body.style.overflow = '';
        }, 300);
    }
}
</script>

<?php

$qrcode_filename = 'qr_code_petition_id' . $pet_id . '.png';
$qrcode_path = '../Resources/qrcode/' . $qrcode_filename;
$file_exists = file_exists($qrcode_path);

$generate_qr_script = '';

if($file_exists == false){
    $generate_qr_script = "
    <script>
    document.addEventListener('DOMContentLoaded', async function() {
        
        try {
            const qrImage = document.getElementById('qr-code');
            if (!qrImage) {
                console.error('QR code image element not found');
                return;
            }
            
            const data = encodeURIComponent('https://petisign.cloud/Sources/view_petition.php?id=" . $pet_id . "');
            const apiUrl = `https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=\${data}&format=png`;

            const response = await fetch(apiUrl);
            
            if (response.ok) {
                const blob = await response.blob();
                const imageUrl = URL.createObjectURL(blob);
                
                qrImage.src = imageUrl;
                
                const reader = new FileReader();
                reader.onloadend = async function() {
                    const base64data = reader.result;
                    const qrcode_filename = 'qr_code_petition_id" . $pet_id . ".png';
                    
                    try {
                        const saveResponse = await fetch('Processus/save_qr_code.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({ 
                                filename: qrcode_filename, 
                                imageData: base64data 
                            })
                        });
                        
                        if (saveResponse.ok) {
                            const saveResult = await saveResponse.json();
                        } else {
                            console.error('Server responded with error:', saveResponse.status);
                        }
                    } catch (saveError) {
                        console.error('Error saving QR code:', saveError);
                    }
                };
                reader.readAsDataURL(blob);
                
            } else {
                console.error('Failed to generate QR code, status:', response.status);
            }
        } catch (error) {
            console.error('Error calling QR API:', error);
        }
    });
    </script>";
} else {
    $generate_qr_script = "
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const qrImage = document.getElementById('qr-code');
        if (qrImage) {
            qrImage.src = '../Resources/qrcode/" . $qrcode_filename . "';
        }
    });
    </script>";
}

?>

<div class="qr-overlay">
    <div id="qr-container">
        <h2>QR Code</h2>
        <button type="button"><img src="/Resources/img/ui_icons/cross.png" class="close-cross" alt="Fermer"></button>
        <img id="qr-code" alt="QR Code" />
        <div class="action">
            <button type="button"><img src="/Resources/img/ui_icons/download.png" alt="Télécharger">&nbsp;&nbsp;Télécharger le QR Code</button>

            <a href="Processus/download_petition.php?id=<?= $pet_id ?>" target="_blank">
                <button type="button" class="affiche">
                    <img src="/Resources/img/ui_icons/download.png" alt="Télécharger">
                    &nbsp;&nbsp;Télécharger l'affiche</button>
            </a>
        </div>
    </div>
</div>





<?php
echo $generate_qr_script;
?>

<script>
    document.getElementById('show_qr').addEventListener('click', function() {
        const qrOverlay = document.querySelector('.qr-overlay');
        const qrContainer = document.getElementById('qr-container');
    
        qrOverlay.style.display = 'flex';
        setTimeout(() => {
            qrOverlay.classList.add('show');
        }, 10);
        
        document.body.style.overflow = 'hidden';
    });

    document.querySelector('#qr-container > button:first-of-type').addEventListener('click', function() {
        closeQRPopup();
    });

    document.querySelector('#qr-container .action button').addEventListener('click', function() {
        const qrImage = document.getElementById('qr-code');
        const link = document.createElement('a');
        link.href = qrImage.src;
        link.download = 'qr-code.png';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });

    function openQRPopup() {
        const qrOverlay = document.querySelector('.qr-overlay');
        const qrContainer = document.getElementById('qr-container');
        
        qrOverlay.style.display = 'flex';
        setTimeout(() => {
            qrOverlay.classList.add('show');
        }, 10);
        
        document.body.style.overflow = 'hidden';
    }

    function closeQRPopup() {
        const qrOverlay = document.querySelector('.qr-overlay');
        
        qrOverlay.classList.remove('show');
        
        setTimeout(() => {
            qrOverlay.style.display = 'none';
            document.body.style.overflow = '';
        }, 300);
    }
</script>

<?php
include_once 'footer.php';
?>
