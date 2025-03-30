<?php
include_once 'header.php';

$hat_id = isset($_POST['hat']) ? $_POST['hat'] : 1;
$eyes_id = isset($_POST['eyes']) ? $_POST['eyes'] : 1;
$mouth_id = isset($_POST['mouth']) ? $_POST['mouth'] : 1;
$skin_id = isset($_POST['skin']) ? $_POST['skin'] : 1;


?>

<style>
body {
    overflow: hidden;
}
.container {
    display: grid;
    grid-template-columns: 1fr 1.5fr;
    gap: 20px;
    height: 100vh;
    overflow: hidden;
    align-items: center;
    justify-content: center;
}

.preview {
    border-radius: 10px;
    width: 200px;
    height: 200px;
    position: sticky;
    top: 20px;
    display: flex;
    justify-content: center;
    align-items: center;
}

.preview img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}

.prev_container {
    display: flex;
    justify-content: center;
    align-items: center;
    box-shadow: 0px 10px 14px rgba(0, 0, 0, 0.1);
    text-align: center;
    padding: 20px;
    padding-top: 70px;
    width: 300px;
    border-radius: 10px;
    margin: 0 auto;
}

.creator {
    padding: 40px;
    border-radius: 10px;
    box-shadow: 0px 10px 14px rgba(0, 0, 0, 0.1);
    margin-right: 30px;
    height: 70vh;
    margin-top:-70px;
    overflow-y: auto;
    overflow-x: hidden;
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

form button {
    margin: 5px;
    padding: 8px 12px;
}

.container_ {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-top: 50px;
    justify-content: center;
}

.column {
    display: grid;
    gap: 10px;
}

.black-squares {
    grid-template-columns: repeat(2, 75px);
}

.colored-squares {
    grid-template-columns: repeat(3, 75px);
}

.black-squares div,
.colored-squares div {
    width: 75px;
    height: 75px;
    border-radius: 5px;
}

.black-squares div {
    background-color: white;
    box-shadow: 0px 10px 14px rgba(0, 0, 0, 0.1);
}

.black-squares button {
    width: 100%;
    height: 100%;
    border: none;
    cursor: pointer;
    margin: 0;
    padding: 0;
    background-color: transparent;
}

.black-squares button img {
    width: 60%;
    height: 60%;
    object-fit: cover;
    margin: 0 auto;
}

.colored-squares div {
    box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2);
}

.colored-squares div:nth-child(1) { background-color: #827397; }
.colored-squares div:nth-child(2) { background-color: #6495ED; }
.colored-squares div:nth-child(3) { background-color: #483D8B; }
.colored-squares div:nth-child(4) { background-color: #59372A; }
.colored-squares div:nth-child(5) { background-color: #00CED1; }
.colored-squares div:nth-child(6) { background-color: #8FBC8F; }

.colored-squares_2 div:nth-child(1) { background-color: #000000; }
.colored-squares_2 div:nth-child(2) { background-color: #FFB6C1; }
.colored-squares_2 div:nth-child(3) { background-color: #800000; }
.colored-squares_2 div:nth-child(4) { background-color: #D1603D; }
.colored-squares_2 div:nth-child(5) { background-color: #E0BBE4; }
.colored-squares_2 div:nth-child(6) { background-color: #FA8072; }

.colored-squares_3 div:nth-child(1) { background-color: #000000; }
.colored-squares_3 div:nth-child(2) { background-color: #87CEEB; }
.colored-squares_3 div:nth-child(3) { background-color: #2F4F4F; }
.colored-squares_3 div:nth-child(4) { background-color: #8B4513; }
.colored-squares_3 div:nth-child(5) { background-color: #957DAD; }
.colored-squares_3 div:nth-child(6) { background-color: #DA70D6; }

.colored-squares_4 div:nth-child(1) { background-color: #F5E0CC; }
.colored-squares_4 div:nth-child(2) { background-color: #CB9A6F; }
.colored-squares_4 div:nth-child(3) { background-color: #673E19; }
.colored-squares_4 div:nth-child(4) { background-color: #E2BC9B; }
.colored-squares_4 div:nth-child(5) { background-color: #9E724C; }
.colored-squares_4 div:nth-child(6) { background-color: #FED78B; }

.line {
    width: 100%;
    height: 2px;
    background-color: black;
    margin-bottom: 10px;
}

.title_ {
    display: flex;
    align-items: center;
    font-size: 20px;
}

.title_ hr {
    margin: 0 auto;
    width: 90%;
}

.divider {
    margin-left: 10px;
    border-left: 2px solid black;
    height: 70px;
}

.title_ {
    margin-top: 50px;
}

.title {
    margin-right: 10px;
}

.creator form:last-child {
    position: sticky;
    bottom: 0;
    background-color: white;
    padding: 10px 0;
    margin-top: 20px;
    width: 100%;
    border-radius:8px;
    text-align: center;
    justify-content:center;
    box-shadow: 0 -5px 10px rgba(0,0,0,0.1);
}


@media (max-height: 800px) {
    .creator {
        height: 80vh;
    }
}

@media (max-width: 768px) {
    .container {
        grid-template-columns: 1fr;
    }
    
    .preview {
        margin: 0 auto;
    }
    
    .creator {
        margin-right: 0;
        margin-top: 20px;
    }
}
</style>

<link rel="stylesheet" href="css/style.css">

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