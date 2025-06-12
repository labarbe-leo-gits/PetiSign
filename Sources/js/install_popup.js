function show_popup_install() {
    const popup = document.querySelector('.install-popup');
    const filter = document.querySelector('.filter');
    
    popup.style.display = 'block';
    filter.style.display = 'block';

    document.body.style.overflow = 'hidden';
    
    setTimeout(() => {
        popup.classList.add('show');
        filter.classList.add('show');
    }, 10);
}

function hide_popup_install() {
    const popup = document.querySelector('.install-popup');
    const filter = document.querySelector('.filter');
    
    popup.classList.remove('show');
    filter.classList.remove('show');

    document.body.style.overflow = 'auto';
    
    setTimeout(() => {
        popup.style.display = 'none';
        filter.style.display = 'none';
    }, 300);
}

document.querySelector('.filter').addEventListener('click', function() {
    hide_popup_install();
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        hide_popup_install();
    }
});