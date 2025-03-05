function hide_popup_trancho() {
    const popup = document.querySelector('.popup');
    const filter = document.querySelector('.filter');
    popup.classList.remove('show');
    filter.style.opacity = '0';
    setTimeout(() => {
        filter.style.display = 'none';
        popup.style.display = 'none';
    }, 300);
    document.getElementById('subbtn').onclick = show_popup_trancho;
}

function show_popup_trancho() {
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
    document.getElementById('subbtn').onclick = hide_popup_trancho;
}