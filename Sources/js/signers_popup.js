// Updated JavaScript to match your popup pattern
function showSignersPopup() {
    const overlay = document.getElementById('signersPopupOverlay');
    const body = document.body;
    body.style.overflow = 'hidden';
    overlay.style.display = 'flex';
    overlay.offsetHeight;
    overlay.classList.add('show');
}

function hideSignersPopup() {
    const overlay = document.getElementById('signersPopupOverlay');
    const body = document.body;
    body.style.overflow = '';
    overlay.classList.remove('show');
    setTimeout(() => {
        overlay.style.display = 'none';
    }, 300);
}