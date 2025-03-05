function detect_if_already_voted(){
    if(localStorage.getItem('note')){
        document.querySelector('.right').innerHTML = `
            <div class='note_container'>
                <div class='validated_header'>
                    <img class='validated' src='../Resources/img/ui_icons/validate.png' />
                    <p class='blank_space'>&nbsp;</p>
                    <h1>Merci pour votre avis !</h1>
                </div>
            </div>
            `;
    }
}

detect_if_already_voted();