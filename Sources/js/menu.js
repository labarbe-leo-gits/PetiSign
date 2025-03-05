function show_popup() {
    const popup = document.querySelector('.menu_container');
    const filter = document.querySelector('.mobile_menu_popup');
    popup.style.display = 'flex';
    filter.style.display = 'block';
    setTimeout(() => {
        popup.style.opacity = '1';
        filter.style.opacity = '1';
        filter.style.transform = 'translate(-50%, -50%) scale(1)';
    }, 10);
    document.getElementById('excep').href = 'javascript:hide_popup()';
}

function hide_popup() {
    const popup = document.querySelector('.menu_container');
    const filter = document.querySelector('.mobile_menu_popup');
    popup.style.opacity = '0';
    filter.style.opacity = '0';
    filter.style.transform = 'translate(-50%, -50%) scale(0.9)';
    setTimeout(() => {
        popup.style.display = 'none';
        filter.style.display = 'none';
    }, 300);
    document.getElementById('excep').href = 'javascript:show_popup()';
}