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
                        <h1>Merci pour votre avis !</h1>
                    </div>
                </div>
                `;
                localStorage.setItem('note', 'true');
                return;
            }
        }
        catch(e){
            console.log('Veuillez choisir une note');
        }
        
    });