<?php

include_once 'header.php';
include_once 'database/database.php';
include_once 'Processus/write_logs.php';
include_once 'checker.php';

echo "
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    const toggleBtn = document.createElement('div');
    toggleBtn.className = 'menu-toggle';
    toggleBtn.innerHTML = '<span></span>';
    document.body.appendChild(toggleBtn);
    
    const overlay = document.createElement('div');
    overlay.className = 'overlay';
    document.body.appendChild(overlay);
    
    const sidebar = document.querySelector('.sidebar');
    const sidebarHeader = document.createElement('div');
    sidebarHeader.className = 'sidebar-header';
    sidebarHeader.textContent = 'Conversations';
    sidebarHeader.addEventListener('click', function() {
        window.location.href = 'chat.php';
    });
    sidebarHeader.style.cursor = 'pointer';
    sidebar.insertBefore(sidebarHeader, sidebar.firstChild);
    
    toggleBtn.addEventListener('click', function() {
        sidebar.classList.toggle('active');
        overlay.classList.toggle('active');
        toggleBtn.classList.toggle('active');
    });
    
    overlay.addEventListener('click', function() {
        sidebar.classList.remove('active');
        overlay.classList.remove('active');
        toggleBtn.classList.remove('active');
    });
    
    const chatLinks = document.querySelectorAll('.chat_access');
    chatLinks.forEach(link => {
        link.addEventListener('click', function() {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
            toggleBtn.classList.remove('active');
        });
    });

    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('discussion_id')) {
        sidebar.classList.remove('active');
    }
});

</script>
";

if(!isset($_SESSION['mail'])) {
    //header('Location: login.php');
    echo "<script>window.location.href='login.php';</script>";
    exit();
}

include_once 'Processus/sessionlocked_security.php';

$stmt = $pdo->prepare("SELECT username FROM USER WHERE email = :mail");
$stmt->bindParam(':mail', $_SESSION['mail']);
$stmt->execute();
$user = $stmt->fetchColumn();

$user_ip = $_SERVER['REMOTE_ADDR'];

write_logs('logs/log.txt', 'MSG1NG', $user, $user_ip, 'Visite de la page "Messagerie"');


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
            /* header('Location: error.php?code=403'); */
            echo "<script>window.location.href='error.php?code=403';</script>";
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

$check_if_discussion_exists = $pdo->prepare("SELECT COUNT(*) FROM DISCUSSION WHERE id = :discussion_id");
$check_if_discussion_exists->bindParam(':discussion_id', $discussion_id, PDO::PARAM_INT);
$check_if_discussion_exists->execute();
$discussion_exists = $check_if_discussion_exists->fetchColumn();

if(isset($discussion_id) && $discussion_exists == 0) {
    echo "<script>window.location.href='error.php?code=404';</script>";
    exit();
    
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
            <div class="chat_access" onclick="window.location.href=\'chat.php?discussion_id='. $discussion['id'] .'\'">
                <div class="avatar">
                    <img class="skin" src="../Resources/avatar/skin/skin'. $avatar_skin .'c'. $avatar_skin_color .'.png" alt="">
                    <img src="../Resources/avatar/hat/hat'. $avatar_hat .'c'. $avatar_hat_color .'.png" class="hat" alt="Hat" id="hat">
                    <img src="../Resources/avatar/eyes/eye'. $avatar_eyes .'c'. $avatar_eyes_color .'.png" class="eyes" alt="Eyes" id="eyes">
                    <img src="../Resources/avatar/mouth/smile'. $avatar_mouth .'c'. $avatar_mouth_color .'.png" class="mouth" alt="Mouth" id="mouth">
                </div>
                <p class="name"><a class="link" href="chat.php?discussion_id='. $discussion['id'] .'">'. $chat_username .'</a></p>
                <a class="chat_delete" href="Processus/delete_discussion.php?id='. $discussion['id'] .'"><img src="../Resources/img/ui_icons/trash.png" alt="Supprimer la discussion"></a>
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
                        
                    </ul>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <form id="chat-form" action="Processus/chat.php" method="POST">
                        <div class="entries">
                            <?php
                            $check_if_i_have_been_blocked = $pdo->prepare("SELECT COUNT(*) FROM BLOCKED_USER WHERE id_user = :id_user AND id_blocked_user = :id_blocked_user");
                            $check_if_i_have_been_blocked->bindParam(':id_user', $exchanger_id, PDO::PARAM_INT);
                            $check_if_i_have_been_blocked->bindParam(':id_blocked_user', $id_from_mail, PDO::PARAM_INT);
                            $check_if_i_have_been_blocked->execute();
                            $is_blocked = $check_if_i_have_been_blocked->fetchColumn();

                            $select_blocker_id = $pdo->prepare("SELECT id_user FROM BLOCKED_USER WHERE id_blocked_user = :id_blocked_user AND id_user = :id_user");
                            $select_blocker_id->bindParam(':id_blocked_user', $exchanger_id, PDO::PARAM_INT);
                            $select_blocker_id->bindParam(':id_user', $id_from_mail, PDO::PARAM_INT);
                            $select_blocker_id->execute();
                            $is_blocked_by = $select_blocker_id->fetchColumn();

                            if($is_blocked > 0) {
                                echo '<p class="error_msg">Vous ne pouvez pas envoyer de message à cet utilisateur car vous avez été bloqué.</p>';
                            }
                             if($id_from_mail == $is_blocked_by) {
                                echo '<p class="error_msg">Vous ne pouvez pas envoyer de message à cet utilisateur car vous l\'avez bloqué.</p>';
                             }
                             
                             else{
                                echo "
                                <input type='text' id='message' name='message' placeholder=' ' required class='form-input'>
                                <label for='message'>Votre message</label>";
                            }
                            ?>
                            
                        </div>
                        <input type="hidden" id="discussion_id" name="discussion_id" value="<?php echo $discussion_id; ?>">
                        <input type="hidden" id="sender" name="sender" value="<?php echo $id_from_mail; ?>">
                        <?php
                        if($is_blocked < 0){
                            echo "<button type='submit'><img src='../Resources/img/ui_icons/send.png' alt='></button>";
                        }
                        ?>
                        
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

<script src="js/theme.js"></script>
<script>
    <?php if(isset($_SESSION['mail'])): ?>
        let inactivityTime = 600;
        let timeoutId;

        function resetTimer() {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(logoutUser, inactivityTime * 1000);
        }

        function logoutUser() {
            window.location.href = 'logout.php';
        }

        document.addEventListener('mousemove', resetTimer);
        document.addEventListener('mousedown', resetTimer);
        document.addEventListener('keypress', resetTimer);
        document.addEventListener('touchmove', resetTimer);
        document.addEventListener('scroll', resetTimer);

        resetTimer();
    <?php endif ?>
</script>
</body>
</html>
