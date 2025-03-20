function hide_popup() {
    const popup = document.querySelector('.popup');
    const filter = document.querySelector('.filter');
    popup.classList.remove('show');
    filter.style.opacity = '0';
    setTimeout(() => {
        filter.style.display = 'none';
        popup.style.display = 'none';
    }, 300);
    return_bg_scroll();
    document.getElementById('subbtn').onclick = show_popup;
}

function show_popup() {
    const popup = document.querySelector('.popup');
    const filter = document.querySelector('.filter');
    filter.style.display = 'block';
    setTimeout(() => {
        filter.style.opacity = '1';
    }, 10);
    popup.style.display = 'grid';
    setTimeout(() => {
        popup.classList.add('show');
    }, 10);
    block_bg_scroll();
    document.getElementById('subbtn').onclick = hide_popup;
}

function updateFilters() {
    let buttons = document.querySelectorAll('.selectable');
    let activeButton = document.querySelector('.active');
    buttons.forEach(button => {
        if (activeButton && !button.classList.contains('active')) {
            button.classList.add('inactive');
        } else {
            button.classList.remove('inactive');
        }
    });
}

let buttons = document.querySelectorAll('.selectable');
let active = null;
buttons.forEach(button => {
    button.addEventListener('click', () => {
        if (active) {
            active.classList.remove('active');
        }
        if(active == button){
            active = null;
            updateFilters();
            return;
        }
        button.classList.add('active');
        active = button;
        updateFilters();
    });
});
document.querySelector('.send > button').addEventListener('click', () => {
    try{
        let note = document.querySelector('.active').value;
        let input = document.getElementById('img_id');
        input.value = note;
        if(note){
            console.log(note);
        }
        hide_popup();
    }
    catch(e){
        console.log(e);
    }
    
});

function block_bg_scroll(){
    document.body.style.overflow = 'hidden';
}

function return_bg_scroll(){
    document.body.style.overflow = 'auto';
}