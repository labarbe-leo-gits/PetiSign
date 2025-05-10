<?php
include_once 'header.php';

$hat_id = isset($_POST['hat']) ? $_POST['hat'] : 1;
$eyes_id = isset($_POST['eyes']) ? $_POST['eyes'] : 1;
$mouth_id = isset($_POST['mouth']) ? $_POST['mouth'] : 1;
$skin_id = isset($_POST['skin']) ? $_POST['skin'] : 1;
$hair_id = isset($_POST['hair']) ? $_POST['hair'] : 1;
?>

<h1>Tests : Avatars custom</h1>

<style>
    .container {
        display: grid;
        grid-template-columns: 1fr 2fr;
        gap: 20px;
    }

    .preview {
        background-color: brown;
        width: 200px;
        height: 200px;
        position: relative;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .preview img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }

    .creator {
        background-color: aqua;
        padding: 20px;
    }

    .skin, .hat, .eyes, .mouth {
        position: absolute;
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .hat {
        width: 150px;
        left: 50%;
        transform: translate(-50%, -50%);
    }

    .eyes {
        top: 50%;
        left: 50%;
        width: 100px;
        transform: translate(-50%, -50%);
        margin-top: -10px;
    }

    .mouth {
        top: 80%;
        left: 50%;
        width: 100px;
        transform: translate(-50%, -50%);
    }

    .eyes_selector {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .header {
        display: flex;
        width: 100%;
        margin-bottom: 10px;
        align-items: center;
        background-color: #f0f0f0;
        padding: 5px;
    }

    .sep hr {
        width: 50vw;
        margin: 0 auto;
    }

    .grid {
        display: grid;
        grid-template-columns: 1fr 2fr;
        gap: 10px;
        width: 100%;
    }

    .left_items, .right_items {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
    }

    .color_btn {
        width: 30px;
        height: 30px;
        border: 1px solid #ccc;
        cursor: pointer;
    }

    #e1 { background-color: #827397; }
    #e2 { background-color: #59372A; }
    #e3 { background-color: #6495ED; }
    #e4 { background-color: #00CED1; }
    #e5 { background-color: #483D8B; }
    #e6 { background-color: #8FBC8F; }

    form button {
        margin: 5px;
        padding: 8px 12px;
    }
</style>

<div class="container">
    <!-- Avatar Preview -->
    <div class="preview">
        <img class="skin" src="../Resources/avatar/skin.png" alt="">
        <img class="hat" src="../Resources/avatar/hat<?php echo $hat_id; ?>.png" alt="">
        <img class="eyes" src="../Resources/avatar/eyes<?php echo $eyes_id; ?>.png" alt="">
        <img class="mouth" src="../Resources/avatar/smile<?php echo $mouth_id; ?>.png" alt="">
    </div>

    <!-- Avatar Creator -->
    <div class="creator">
        <form action="" method="post">
            <input type="hidden" name="hat" value="<?php echo $hat_id; ?>">
            <input type="hidden" name="mouth" value="<?php echo $mouth_id; ?>">

            <!-- Eye Selector -->
            <div class="eyes_selector">
                <div class="header">
                    <div class="text"><p>Yeux</p></div>
                    <div class="sep"><hr></div>
                </div>
                <div class="grid">
                    <div class="left_items">
                        <button type="submit" name="eyes" value="1">Eyes1</button>
                        <button type="submit" name="eyes" value="2">Eyes2</button>
                        <button type="submit" name="eyes" value="3">Eyes3</button>
                        <button type="submit" name="eyes" value="4">Eyes4</button>
                    </div>
                    <div class="right_items">
                        <button type="submit" name="eyes" value="1" class="color_btn" id="e1">&nbsp;</button>
                        <button type="submit" name="eyes" value="2" class="color_btn" id="e2">&nbsp;</button>
                        <button type="submit" name="eyes" value="3" class="color_btn" id="e3">&nbsp;</button>
                        <button type="submit" name="eyes" value="4" class="color_btn" id="e4">&nbsp;</button>
                        <button type="submit" name="eyes" value="5" class="color_btn" id="e5">&nbsp;</button>
                        <button type="submit" name="eyes" value="6" class="color_btn" id="e6">&nbsp;</button>
                    </div>
                </div>
            </div>

            <!-- Hat Selector -->
            <button type="submit" name="hat" value="1">Hat1</button>
            <button type="submit" name="hat" value="2">Hat2</button>
            <button type="submit" name="hat" value="3">Hat3</button>

            <!-- Mouth Selector -->
            <button type="submit" name="mouth" value="1">Mouth1</button>
            <button type="submit" name="mouth" value="2">Mouth2</button>
            <button type="submit" name="mouth" value="3">Mouth3</button>
            <button type="submit" name="mouth" value="4">Mouth4</button>
        </form>

        <!-- Save Avatar -->
        <form action="Processus/save_avatar.php" method="post">
            <input type="hidden" name="hat" value="<?php echo $hat_id; ?>">
            <input type="hidden" name="eyes" value="<?php echo $eyes_id; ?>">
            <input type="hidden" name="mouth" value="<?php echo $mouth_id; ?>">
            <button type="submit">Save</button>
        </form>
    </div>
</div>
