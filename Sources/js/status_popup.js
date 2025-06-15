function openStatusPopup() {
    const popup = document.getElementById('statusUpdatePopup');
    popup.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    
    setTimeout(() => {
        popup.classList.add('show');
    }, 10);
    
    setTimeout(() => {
        document.getElementById('status').focus();
    }, 300);
}

function closeStatusPopup() {
    const popup = document.getElementById('statusUpdatePopup');
    popup.classList.remove('show');
    
    setTimeout(() => {
        popup.style.display = 'none';
        document.body.style.overflow = 'auto';
    }, 300);
}

document.getElementById('statusUpdatePopup').addEventListener('click', function(event) {
    if (event.target === this) {
        closeStatusPopup();
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const textarea = document.getElementById('status');
    const maxLength = 60;
    
    if (textarea) {
        const counter = document.createElement('div');
        counter.className = 'character-counter';
        counter.innerHTML = `0/${maxLength}`;
        textarea.parentNode.insertBefore(counter, textarea.nextSibling);
        
        function updateCounter() {
            const currentLength = textarea.value.length;
            counter.innerHTML = `${currentLength}/${maxLength}`;
            
            if (currentLength > maxLength * 0.9) {
                counter.classList.add('warning');
            } else {
                counter.classList.remove('warning');
            }
            
            if (currentLength > maxLength) {
                textarea.value = textarea.value.substring(0, maxLength);
                counter.innerHTML = `${maxLength}/${maxLength}`;
            }
        }
        
        textarea.addEventListener('input', updateCounter);
        
        updateCounter();
    }
});

document.querySelector('#statusUpdatePopup form').addEventListener('submit', function(event) {
    const textarea = document.getElementById('status');
    if (textarea.value.trim() === '') {
        event.preventDefault();
        alert('Veuillez entrer un statut avant de soumettre.');
        textarea.focus();
        return false;
    }
    
    const submitBtn = this.querySelector('.custom-button');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Mise à jour...';
    submitBtn.disabled = true;
    
    setTimeout(() => {
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    }, 5000);
});