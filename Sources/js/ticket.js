document.getElementById('ticket-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    let form = this;
    let isValid = true;

    if (isValid) {
        const formData = {
            name: document.getElementById('name').value,
            firstname: document.getElementById('firstname').value,
            email: document.getElementById('email').value,
            phone: document.getElementById('phone').value,
            category: document.getElementById('category').value,
            urgency: document.getElementById('urgency').value,
            title: document.getElementById('title').value,
            description: document.getElementById('description').value
        };
        
        fetch('create_ticket.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('success-message').style.display = 'block';
                form.reset();
                
            } else {
                alert('Erreur: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Une erreur est survenue lors de la création du ticket. Veuillez réessayer plus tard.');
        });
    }
});