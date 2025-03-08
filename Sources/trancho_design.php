<?php
include_once 'header.php';
?>

<style>
    .container {
        display: grid;
        grid-template-columns: 1fr 3fr;
        width: fit-content;
        margin-top: 5vh;
        margin-left: 1vw;
        border-radius: 10px;
        box-shadow: 0px 10px 14px rgba(0, 0, 0, 0.1);
        background-color: #f8f8f8;
    }
    .right {
        width: fit-content;
        height: 100%;
        margin: auto;
        margin-top: 8%;
    }
    .loop {
        margin: 5px;
    }
    .left {
        width: fit-content;
    }
    .left > img {
        border-top-left-radius: 10px;
        border-bottom-left-radius: 10px;
        height: 100%;
    }
    button {
        width: 5vw;
        height: 8vh;
        background-color: #1A414E;
        border-radius: 10px;
        border: 0px;
        color: white;
        cursor: pointer;
        transition: background-color 0.3s ease;
        font-size: 16px;
    }
    button:hover {
        background-color: #173642;
    }
    .send {
        text-align: center;
    }
    .send > button {
        width: 100%;
    }
    .active {
        background-color: #FED78B;
        color: #1A414E;
    }
    .active:hover {
        background-color: #E0BD78;
    }
    .popup {
        display: none;
        opacity: 0;
        transform: translateY(-20px);
        transition: opacity 0.3s ease, transform 0.3s ease;
    }
    .popup.show {
        display: grid;
        opacity: 1;
        transform: translateY(0);
    }
    .validated {
        width: 30px;
        filter: invert(20%) sepia(16%) saturate(1711%) hue-rotate(149deg) brightness(93%) contrast(90%);
    }
    .validated_header {
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .blank_space{
        margin: 5px;
    }
    .given_note {
        text-align: center;
    }
    .note_container {
        height: 100%;
        margin-top: 10vh;
    }
    hr{
        text-align: center;
        margin-bottom: 35px;
    }
    .exit{
        text-align: center;
    }
    .exit > button{
        width: 80%;
        margin-top: 5px;
    }
</style>

<button class="custom-button" id="showhide" onclick="show_popup()">Noter PétiSign</button>

<div class="container popup">
    <div class="left">
        <img id="trancho" src="../Resources/img/trancho/trancho.png" alt="Kevin Trancho">
    </div>
    <div class="right">
        <h1>Noter PétiSign</h1>
        <h2>Quelle est la probabilité que vous recommendiez PétiSign à un ami ?</h2>
        <button class="selectable" id="0" value="0">0</button>
        <?php
            for($i=1;$i<10;$i++){
                print "<button id='$i' value='0.$i' class='loop selectable'>0.$i</button>";
            }
        ?>
        <button id="10" class="selectable" value="1">1</button>
        <div class="send"><button>Envoyer</button></div>
    </div>
</div>

<script>
    function hide_popup(){
        const popup = document.querySelector('.popup');
        popup.classList.remove('show');
        setTimeout(() => {
            popup.style.display = 'none';
        }, 300);
        document.getElementById('showhide').onclick = show_popup;
    }

    function show_popup(){
        const popup = document.querySelector('.popup');
        popup.style.display = 'grid';
        popup.style.zIndex = '100';
        setTimeout(() => {
            popup.classList.add('show');
        }, 10);
        document.getElementById('showhide').onclick = hide_popup;
    }
</script>

<script>
    let buttons = document.querySelectorAll('.selectable');
    let active = document.querySelector('.active');
    buttons.forEach(button => {
        button.addEventListener('click', () => {
            if (active) {
                active.classList.remove('active');
            }
            if(active == button){
                active = null;
                return;
            }
            button.classList.add('active');
            active = button;
        });
    });
    document.querySelector('.send > button').addEventListener('click', () => {
        try{
            let note = document.querySelector('.active').value;
            if(note){
                console.log(note);
                active.classList.remove('active');
                document.querySelector('.right').innerHTML = `
                <div class='note_container'>
                    <div class='validated_header'>
                        <img class='validated' src='../Resources/img/ui_icons/validate.png' />
                        <p class='blank_space'>&nbsp;</p>
                        <h1>Merci pour votre note !</h1>
                    </div>
                    <hr>
                    <div class='given_note'>
                        <h2>Vous avez noté PétiSign ${note}/1</h2>
                    </div>
                </div>
                `;
                return;
            }
        }
        catch(e){
            console.log('Veuillez choisir une note');
        }
        
    });
</script>