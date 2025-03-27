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
    gap: 20px; /* Add some space between the preview and creator */
}

.preview {
    background-color: brown;
    width: 200px;
    height: 200px;
    position: relative;
    display: flex; /* Use flexbox for centering */
    justify-content: center; /* Center horizontally */
    align-items: center; /* Center vertically */
}

.preview img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}

.creator {
    background-color: aqua;
    padding: 20px; /* Add padding for better spacing */
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
    display: grid;
    grid-template-columns: 1fr 3fr;
    width: 100%; /* Make header full width */
    margin-bottom: 10px; /* Add space below header */
}

.sep hr {
    width: 100%; /* Make separator line full width */
}

.grid {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 10px; /* Add gap between grid items */
    width: 100%; /* Make grid full width */
}

.left_items, .right_items {
    display: flex;
    flex-wrap: wrap; /* Allow buttons to wrap to next line if needed */
    gap: 5px; /* Add gap between buttons */
}

.color_btn {
    width: 30px;
    height: 30px;
    border: 1px solid #ccc;
    cursor: pointer;
}

/* Example color assignments */
#e1 { background-color: black; }
#e2 { background-color: #808080; }
#e3 { background-color: #9370DB; }
#e4 { background-color: #6495ED; }
#e5 { background-color: #8FBC8F; }
#e6 { background-color: #20B2AA; }

/* Add spacing to form buttons */
form button {
    margin: 5px;
    padding: 8px 12px;
}
</style>

<div class="container">
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

            <div class="eyes_selector">
                <div class="header">
                    <div class="text"><p>Yeux</p></div>
                    <div class="sep"><hr></div>
                </div>
                <div class="grid">
                    <div class="left_items">
                        <button type="submit">h</button>
                        <button type="submit">h</button>
                        <button type="submit">h</button>
                        <button type="submit">h</button>
                    </div>
                    <div class="right_items">
                        <button type="submit" class="color_btn" id="e1">&nbsp;</button>
                        <button type="submit" class="color_btn" id="e2">&nbsp;</button>
                        <button type="submit" class="color_btn" id="e3">&nbsp;</button>
                        <button type="submit" class="color_btn" id="e4">&nbsp;</button>
                        <button type="submit" class="color_btn" id="e5">&nbsp;</button>
                        <button type="submit" class="color_btn" id="e6">&nbsp;</button>
                    </div>
                </div>
            </div>
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