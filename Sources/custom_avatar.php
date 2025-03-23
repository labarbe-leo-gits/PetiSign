<?php
include_once 'header.php';

$hat_id = isset($_POST['hat']) ? $_POST['hat'] : 1;
$eyes_id = isset($_POST['eyes']) ? $_POST['eyes'] : 1;
$mouth_id = isset($_POST['mouth']) ? $_POST['mouth'] : 1;

?>

<h1>Tests : Avatars custom</h1>

<style>
    .container{
        display: grid;
        grid-template-columns: 1fr 1fr;
    }
    .preview{
        background-color:brown;
        width:200px;
        height:200px;
        position: relative;
    }
    .creator{
        background-color:aqua;
    }
    .skin, .hat, .eyes, .mouth{
        position: absolute;
        width: 100%;
        height: 100%;
        object-fit: contain;
    }
    .hat{
        width:150px;
        left:50%;
        transform: translate(-50%, -50%);
    }
    .eyes{
        top: 50%;
        left: 50%;
        width: 100px;
        transform: translate(-50%, -50%);
        margin-top:-10px;
    }
    .mouth{
        top: 80%;
        left: 50%;
        width: 100px;
        transform: translate(-50%, -50%);
    }
</style>

<div class="container">
    <?php
    echo $hat_id;
    ?>
    <div class="preview">
        <img class="skin" src="../Resources/avatar/skin.png" alt="">
        <img class="hat" src="../Resources/avatar/hat<?php echo $hat_id; ?>.png" alt="">
        <img src="../Resources/avatar/eyes<?php echo $eyes_id; ?>.png" alt="" class="eyes">
        <img src="../Resources/avatar/smile<?php echo $mouth_id; ?>.png" alt="" class="mouth">
    </div>
    <div class="creator">
        <form action="" method="post">
            <input type="hidden" name="hat" value="<?php echo $hat_id; ?>">
            <input type="hidden" name="eyes" value="<?php echo $eyes_id; ?>">
            <input type="hidden" name="mouth" value="<?php echo $mouth_id; ?>">
            <button type="submit" name="hat" value="1">Hat</button>
            <button type="submit" name="hat" value="2">Hat2</button>
            <button type="submit" name="hat" value="3">Hat3</button>
            <button type="submit" name="eyes" value="1">Eyes</button>
            <button type="submit" name="eyes" value="2">Eyes2</button>
            <button type="submit" name="eyes" value="3">Eyes3</button>
            <button type="submit" name="eyes" value="4">Eyes4</button>
            <button type="submit" name="mouth" value="1">Mouth</button>
            <button type="submit" name="mouth" value="2">Mouth2</button>
            <button type="submit" name="mouth" value="4">Mouth4</button>
        </form>
        <form action="Processus/save_avatar.php" method="post">
            <input type="hidden" name="hat" value="<?php echo $hat_id; ?>">
            <input type="hidden" name="eyes" value="<?php echo $eyes_id; ?>">
            <input type="hidden" name="mouth" value="<?php echo $mouth_id; ?>">
            <button type="submit">Save</button>
        </form>
    </div>
</div>