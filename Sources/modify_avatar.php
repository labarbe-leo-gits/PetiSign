<?php
include_once 'header.php';
include_once 'database/database.php';

if(!isset($_SESSION['mail'])){
    header('Location: login.php');
    exit();
}

try{

    $get_avatar_hat = $pdo->prepare('SELECT avatar_hat FROM USER WHERE email = :mail');
    $get_avatar_hat->bindParam(':mail', $mail);
    $get_avatar_hat->execute();
    $avatar_hat = $get_avatar_hat->fetchColumn();

    $get_avatar_eyes = $pdo->prepare('SELECT avatar_eyes FROM USER WHERE email = :mail');
    $get_avatar_eyes->bindParam(':mail', $mail);
    $get_avatar_eyes->execute();
    $avatar_eyes = $get_avatar_eyes->fetchColumn();

    $get_avatar_mouth = $pdo->prepare('SELECT avatar_mouth FROM USER WHERE email = :mail');
    $get_avatar_mouth->bindParam(':mail', $mail);
    $get_avatar_mouth->execute();
    $avatar_mouth = $get_avatar_mouth->fetchColumn();

    $hat_id = isset($_POST['hat']) ? $_POST['hat'] : $avatar_hat;
    $eyes_id = isset($_POST['eyes']) ? $_POST['eyes'] : $avatar_eyes;
    $mouth_id = isset($_POST['mouth']) ? $_POST['mouth'] : $avatar_mouth;
    $skin_id = isset($_POST['skin']) ? $_POST['skin'] : 1;
} catch (PDOException $e) {
    echo "\n\n\n\n\n\nError: " . $e->getMessage();
    exit();
}

?>

<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="css/modify_avatar.css">

<div class="container">
    <div class="prev_container">
        <div class="preview">
            <img class="skin" src="../Resources/avatar/skin.png" alt="">
            <img class="hat" src="../Resources/avatar/hat<?php echo $hat_id; ?>.png" alt="">
            <img class="eyes" src="../Resources/avatar/eyes<?php echo $eyes_id; ?>.png" alt="">
            <img class="mouth" src="../Resources/avatar/smile<?php echo $mouth_id; ?>.png" alt="">
        </div>
        </div>

    <div class="creator">
        <form method="post">

            <input type="hidden" name="hat" value="<?php echo $hat_id; ?>">
            <input type="hidden" name="eyes" value="<?php echo $eyes_id; ?>">
            <input type="hidden" name="mouth" value="<?php echo $mouth_id; ?>">

            <div class="title_">
                <div class="title">Yeux</div>
                <hr>
            </div>
            
            
            <div class="container_">
                <div class="column black-squares">
                    <div><button type="submit" name="eyes" value="1"><img src="../Resources/avatar/eyes1.png" alt=""></button></div> <div><button type="submit" name="eyes" value="2"><img src="../Resources/avatar/eyes2.png" alt=""></button></div> 
                    <div><button type="submit" name="eyes" value="3"><img src="../Resources/avatar/eyes3.png" alt=""></button></div> <div><button type="submit" name="eyes" value="4"><img src="../Resources/avatar/eyes4.png" alt=""></button></div>
                </div>

                <div class="divider">&nbsp;</div>

                <div class="column colored-squares">
                    <div></div> <div></div> <div></div>
                    <div></div> <div></div> <div></div>
                </div>
            </div>

            <div class="title_">
                <div class="title">Bouches</div>
                <hr>
            </div>
            
            
            <div class="container_">
                <div class="column black-squares">
                    <div><button type="submit" name="mouth" value="1"><img src="../Resources/avatar/smile1.png" alt=""></button></div> <div><button type="submit" name="mouth" value="2"><img src="../Resources/avatar/smile2.png" alt=""></button></div> 
                    <div><button type="submit" name="mouth" value="3"><img src="../Resources/avatar/smile3.png" alt=""></button></div> <div><button type="submit" name="mouth" value="4"><img src="../Resources/avatar/smile4.png" alt=""></button></div>
                </div>

                <div class="divider">&nbsp;</div>

                <div class="column colored-squares colored-squares_2">
                    <div></div> <div></div> <div></div>
                    <div></div> <div></div> <div></div>
                </div>
            </div>

            <div class="title_">
                <div class="title">Chapeaux</div>
                <hr>
            </div>
            
            <div class="container_">
                <div class="column black-squares">
                    <div><button type="submit" name="hat" value="1"><img src="../Resources/avatar/hat1.png" alt=""></button></div> <div><button type="submit" name="hat" value="2"><img src="../Resources/avatar/hat2.png" alt=""></button></div> 
                    <div><button type="submit" name="hat" value="3"><img src="../Resources/avatar/hat3.png" alt=""></button></div> <div><button type="submit" name="hat" value="4"><img src="../Resources/avatar/hat4.png" alt=""></button></div>
                </div>

                <div class="divider">&nbsp;</div>

                <div class="column colored-squares colored-squares_3">
                    <div></div> <div></div> <div></div>
                    <div></div> <div></div> <div></div>
                </div>
            </div>

            <div class="title_">
                <div class="title">Teint</div>
                <hr>
            </div>
            
            <div class="container_">

                <div class="column colored-squares colored-squares_4">
                    <div></div> <div></div> <div></div>
                    <div></div> <div></div> <div></div>
                </div>
            </div>

        </form>

        <form action="Processus/save_avatar.php" method="post">
            <input type="hidden" name="hat" value="<?php echo $hat_id; ?>">
            <input type="hidden" name="eyes" value="<?php echo $eyes_id; ?>">
            <input type="hidden" name="mouth" value="<?php echo $mouth_id; ?>">
            <button type="submit" class="custom-button">Sauvegarder</button>
            <button type="button" class="custom-button" onclick="window.history.back();">Annuler</button>
        </form>
    </div>
</div>