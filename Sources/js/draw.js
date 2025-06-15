let canvas, ctx;
let isDrawing = false;
let lastX = 0;
let lastY = 0;

document.addEventListener('DOMContentLoaded', function() {
    initializeCanvas();
});

function initializeCanvas() {
    canvas = document.getElementById('signatureCanvas');
    if (!canvas) return;
    
    ctx = canvas.getContext('2d');
    
    ctx.strokeStyle = '#000000';
    ctx.lineWidth = 2;
    ctx.lineCap = 'round';
    ctx.lineJoin = 'round';
    
    ctx.fillStyle = '#ffffff';
    ctx.fillRect(0, 0, canvas.width, canvas.height);
    
    canvas.addEventListener('mousedown', startDrawing);
    canvas.addEventListener('mousemove', draw);
    canvas.addEventListener('mouseup', stopDrawing);
    canvas.addEventListener('mouseout', stopDrawing);
    
    canvas.addEventListener('touchstart', handleTouch);
    canvas.addEventListener('touchmove', handleTouch);
    canvas.addEventListener('touchend', stopDrawing);
    
    canvas.addEventListener('touchstart', preventDefault);
    canvas.addEventListener('touchmove', preventDefault);
}

function startDrawing(e) {
    isDrawing = true;
    const rect = canvas.getBoundingClientRect();
    lastX = e.clientX - rect.left;
    lastY = e.clientY - rect.top;
}

function draw(e) {
    if (!isDrawing) return;
    
    const rect = canvas.getBoundingClientRect();
    const currentX = e.clientX - rect.left;
    const currentY = e.clientY - rect.top;
    
    ctx.beginPath();
    ctx.moveTo(lastX, lastY);
    ctx.lineTo(currentX, currentY);
    ctx.stroke();
    
    lastX = currentX;
    lastY = currentY;
}

function stopDrawing() {
    isDrawing = false;
}

function handleTouch(e) {
    e.preventDefault();
    const touch = e.touches[0];
    const mouseEvent = new MouseEvent(e.type === 'touchstart' ? 'mousedown' : 
                                     e.type === 'touchmove' ? 'mousemove' : 'mouseup', {
        clientX: touch.clientX,
        clientY: touch.clientY
    });
    canvas.dispatchEvent(mouseEvent);
}

function preventDefault(e) {
    e.preventDefault();
}

function clearCanvas() {
    if (!ctx) return;
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    ctx.fillStyle = '#ffffff';
    ctx.fillRect(0, 0, canvas.width, canvas.height);
}

function submitMobileSignature() {
    const check1 = document.getElementById('mobileCheck1');
    const check2 = document.getElementById('mobileCheck2');
    
    if (!check1.checked || !check2.checked) {
        alert('Veuillez accepter toutes les conditions avant de valider votre signature.');
        return;
    }
    
    if (isCanvasEmpty()) {
        alert('Veuillez dessiner votre signature avant de valider.');
        return;
    }
    
    const signatureData = canvas.toDataURL('image/png');
    
    const petitionId = getPetitionId();
    
    saveSignatureToServer(signatureData, petitionId);
}

function getPetitionId() {
    const urlParams = new URLSearchParams(window.location.search);
    const petitionId = urlParams.get('id');
    return petitionId;
}

function saveSignatureToServer(signatureData, petitionId) {
    const submitBtn = document.querySelector('.submit-signature-btn');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<img src="../Resources/img/ui_icons/loading.gif" alt="Chargement"> Envoi en cours...';
    submitBtn.disabled = true;
    
    const formData = new FormData();
    formData.append('petition_id', petitionId);
    formData.append('signature_data', signatureData);
    formData.append('check', '1');
    formData.append('check2', '1');
    
    fetch('Processus/sign.php?mobile_signature=true', {
        method: 'POST',
        body: formData
    })
    
    .then(response => {
        window.location.reload();
    })
}

function isCanvasEmpty() {
    const emptyCanvas = document.createElement('canvas');
    emptyCanvas.width = canvas.width;
    emptyCanvas.height = canvas.height;
    const emptyCtx = emptyCanvas.getContext('2d');
    emptyCtx.fillStyle = '#ffffff';
    emptyCtx.fillRect(0, 0, emptyCanvas.width, emptyCanvas.height);
    
    return canvas.toDataURL() === emptyCanvas.toDataURL();
}

function hideMobileSignaturePopup() {
    const overlay = document.getElementById('mobileSignatureOverlay');
    if (overlay) {
        overlay.classList.add('closing');
        
        setTimeout(() => {
            overlay.style.display = 'none';
            overlay.classList.remove('closing');
        }, 300);
    }
}

function showMobileSignaturePopup() {
    const overlay = document.getElementById('mobileSignatureOverlay');
    if (overlay) {
        overlay.classList.remove('closing');
        overlay.style.display = 'flex';
        setTimeout(() => {
            initializeCanvas();
        }, 100);
    }
}
