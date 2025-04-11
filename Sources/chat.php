<?php

include_once 'header.php';
include_once 'database/database.php';


if(!isset($_SESSION['mail'])) {
    header('Location: login.php');
    exit();
}

$discussion_id = $_GET['discussion_id'] ?? null;
$discussion_id = filter_input(INPUT_GET, 'discussion_id', FILTER_SANITIZE_NUMBER_INT);


$id_from_mail_stmt = $pdo->prepare("SELECT id FROM USER WHERE email = :mail");
$id_from_mail_stmt->bindParam(':mail', $_SESSION['mail'], PDO::PARAM_STR);
$id_from_mail_stmt->execute();
$id_from_mail = $id_from_mail_stmt->fetchColumn();

$user_to_chat = null;
$exchanger_id = null;
$messages = [];

if(isset($discussion_id) && $discussion_id != null){
    $check_if_user_is_in_discussion = $pdo->prepare("SELECT COUNT(*) FROM DISCUSSION WHERE id = :discussion_id AND id_user = :user_id");
    $check_if_user_is_in_discussion->bindParam(':discussion_id', $discussion_id, PDO::PARAM_INT);
    $check_if_user_is_in_discussion->bindParam(':user_id', $id_from_mail, PDO::PARAM_INT);
    $check_if_user_is_in_discussion->execute();
    $is_in = $check_if_user_is_in_discussion->fetchColumn();

    if($is_in != 1){
        $check_if_user_is_in_discussion = $pdo->prepare("SELECT COUNT(*) FROM DISCUSSION WHERE id = :discussion_id AND id_second_user = :user_id");
        $check_if_user_is_in_discussion->bindParam(':discussion_id', $discussion_id, PDO::PARAM_INT);
        $check_if_user_is_in_discussion->bindParam(':user_id', $id_from_mail, PDO::PARAM_INT);
        $check_if_user_is_in_discussion->execute();
        $is_in = $check_if_user_is_in_discussion->fetchColumn();

        if($is_in == 1){
            $which_id = 2;
            $get_user_to_chat = $pdo->prepare("SELECT username FROM USER WHERE id = (SELECT id_user FROM DISCUSSION WHERE id = :discussion_id)");
            $get_user_to_chat->bindParam(':discussion_id', $discussion_id, PDO::PARAM_INT);
            $get_user_to_chat->execute();
            $user_to_chat = $get_user_to_chat->fetchColumn();

            $exchanger_id = $pdo->prepare("SELECT id_user FROM DISCUSSION WHERE id = :discussion_id");
            $exchanger_id->bindParam(':discussion_id', $discussion_id, PDO::PARAM_INT);
            $exchanger_id->execute();
            $exchanger_id = $exchanger_id->fetchColumn();
        }else{
            header('Location: error.php?code=403');
            exit();
        }

    }else{
        $which_id = 1;
        $get_user_to_chat = $pdo->prepare("SELECT username FROM USER WHERE id = (SELECT id_second_user FROM DISCUSSION WHERE id = :discussion_id)");
        $get_user_to_chat->bindParam(':discussion_id', $discussion_id, PDO::PARAM_INT);
        $get_user_to_chat->execute();
        $user_to_chat = $get_user_to_chat->fetchColumn();

        $exchanger_id = $pdo->prepare("SELECT id_second_user FROM DISCUSSION WHERE id = :discussion_id");
        $exchanger_id->bindParam(':discussion_id', $discussion_id, PDO::PARAM_INT);
        $exchanger_id->execute();
        $exchanger_id = $exchanger_id->fetchColumn();
    }
    
    $get_discussion_details = $pdo->prepare("SELECT * FROM DISCUSSION WHERE id = :discussion_id");
    $get_discussion_details->bindParam(':discussion_id', $discussion_id, PDO::PARAM_INT);
    $get_discussion_details->execute();
    $discussion_details = $get_discussion_details->fetch(PDO::FETCH_ASSOC);

    try{
        $discussion_messages = $pdo->prepare("SELECT * FROM MESSAGE WHERE id_discussion = :discussion_id");
        $discussion_messages->bindParam(':discussion_id', $discussion_id, PDO::PARAM_INT);
        $discussion_messages->execute();
        $messages = $discussion_messages->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

$get_all_user_discussions = $pdo->prepare("SELECT * FROM DISCUSSION WHERE id_user = :user_id OR id_second_user = :user_id");
$get_all_user_discussions->bindParam(':user_id', $id_from_mail, PDO::PARAM_INT);
$get_all_user_discussions->execute();
$all_user_discussions = $get_all_user_discussions->fetchAll(PDO::FETCH_ASSOC);

?>

<link rel="stylesheet" href="css/login_register.css">
<link rel="stylesheet" href="css/login.css">
<link rel="stylesheet" href="css/chat.css">

<div class="main_container">
    <div class="sidebar">
        <?php
        foreach($all_user_discussions as $discussion) {

            if ($discussion['id_user'] == $id_from_mail) {

                $get_username = $pdo->prepare("SELECT username FROM USER WHERE id = :user_id");
                $get_username->bindParam(':user_id', $discussion['id_second_user'], PDO::PARAM_INT);
                $get_username->execute();
                $chat_username = $get_username->fetchColumn();
                $id = $discussion['id_second_user'];
            } else {

                $get_username = $pdo->prepare("SELECT username FROM USER WHERE id = :user_id");
                $get_username->bindParam(':user_id', $discussion['id_user'], PDO::PARAM_INT);
                $get_username->execute();
                $chat_username = $get_username->fetchColumn();
                $id = $discussion['id_user'];
            }

            $get_avatar_hat = $pdo->prepare('SELECT avatar_hat FROM USER WHERE id = :id');
            $get_avatar_hat->bindParam(':id', $id);
            $get_avatar_hat->execute();
            $avatar_hat = $get_avatar_hat->fetchColumn();

            $get_avatar_eyes = $pdo->prepare('SELECT avatar_eyes FROM USER WHERE id = :id');
            $get_avatar_eyes->bindParam(':id', $id);
            $get_avatar_eyes->execute();
            $avatar_eyes = $get_avatar_eyes->fetchColumn();

            $get_avatar_mouth = $pdo->prepare('SELECT avatar_mouth FROM USER WHERE id = :id');
            $get_avatar_mouth->bindParam(':id', $id);
            $get_avatar_mouth->execute();
            $avatar_mouth = $get_avatar_mouth->fetchColumn();

            $get_avatar_skin = $pdo->prepare('SELECT avatar_skin FROM USER WHERE id = :id');
            $get_avatar_skin->bindParam(':id', $id);
            $get_avatar_skin->execute();
            $avatar_skin = $get_avatar_skin->fetchColumn();

            $get_avatar_hat_color = $pdo->prepare('SELECT avatar_hat_color FROM USER WHERE id = :id');
            $get_avatar_hat_color->bindParam(':id', $id);
            $get_avatar_hat_color->execute();
            $avatar_hat_color = $get_avatar_hat_color->fetchColumn();

            $get_avatar_eyes_color = $pdo->prepare('SELECT avatar_eyes_color FROM USER WHERE id = :id');
            $get_avatar_eyes_color->bindParam(':id', $id);
            $get_avatar_eyes_color->execute();
            $avatar_eyes_color = $get_avatar_eyes_color->fetchColumn();

            $get_avatar_mouth_color = $pdo->prepare('SELECT avatar_mouth_color FROM USER WHERE id = :id');
            $get_avatar_mouth_color->bindParam(':id', $id);
            $get_avatar_mouth_color->execute();
            $avatar_mouth_color = $get_avatar_mouth_color->fetchColumn();

            $get_avatar_skin_color = $pdo->prepare('SELECT avatar_skin_color FROM USER WHERE id = :id');
            $get_avatar_skin_color->bindParam(':id', $id);
            $get_avatar_skin_color->execute();
            $avatar_skin_color = $get_avatar_skin_color->fetchColumn();
            
            echo '
            <div class="chat_access">
                <div class="avatar">
                    <img class="skin" src="../Resources/avatar/skin/skin'. $avatar_skin .'c'. $avatar_skin_color .'.png" alt="">
                    <img src="../Resources/avatar/hat/hat'. $avatar_hat .'c'. $avatar_hat_color .'.png" class="hat" alt="Hat" id="hat">
                    <img src="../Resources/avatar/eyes/eye'. $avatar_eyes .'c'. $avatar_eyes_color .'.png" class="eyes" alt="Eyes" id="eyes">
                    <img src="../Resources/avatar/mouth/smile'. $avatar_mouth .'c'. $avatar_mouth_color .'.png" class="mouth" alt="Mouth" id="mouth">
                </div>
                <p class="name"><a class="link" href="chat.php?discussion_id='. $discussion['id'] .'">'. $chat_username .'</a></p>
            </div>
            ';
        }

        if (count($all_user_discussions) == 0) {
            echo '<p class="error_msg">Aucune discussion disponible.</p>';
        }
        ?>
        
    </div>
    <div class="container">
        <?php if(isset($discussion_id) && $discussion_id != null): ?>
            <div class="row">
                <div class="col-md-12">
                    <h1>Discussion avec <a target="_blank" class="link" href="view_profile.php?id=<?=$exchanger_id?>"><?=$user_to_chat?></a></h1>
                    <hr>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <script src="js/chat.js"></script>
                    <ul id="chat-messages">
                        <!-- Chat messages will be loaded here -->
                    </ul>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <form id="chat-form" action="Processus/chat.php" method="POST">
                        <div class="entries">
                            <input type="text" id="message" name="message" placeholder=" " required class="form-input">
                            <label for="message">Votre message</label>
                        </div>
                        <input type="hidden" id="discussion_id" name="discussion_id" value="<?php echo $discussion_id; ?>">
                        <input type="hidden" id="sender" name="sender" value="<?php echo $id_from_mail; ?>">
                        <button type="submit"><img src="../Resources/img/ui_icons/send.png" alt=""></button>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="no-discussion-message">
                        <h3>Bienvenue sur votre espace de discussion !</h3>
                        <p>Sélectionnez une discussion existante sur la gauche ou alors contactez d'autres membres via leurs page de profil !</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>