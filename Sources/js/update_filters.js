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