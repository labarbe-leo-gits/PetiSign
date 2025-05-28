let pollingInterval = 1000;
let pollingTimer;

function updateUI(users) {
    const table = document.getElementById('active-users-table');

    const headerRow = table.rows[0];

    while (table.rows.length > 1) {
        table.deleteRow(1);
    }
    
    if (users.length > 0) {

        users.forEach(function(user) {
            const row = table.insertRow(-1);
            
            const idCell = row.insertCell(0);
            idCell.className = 'id';
            idCell.textContent = user.id;
            
            const usernameCell = row.insertCell(1);
            usernameCell.className = 'content';
            usernameCell.textContent = user.username;
            
            const activityCell = row.insertCell(2);
            activityCell.className = 'content';
            const date = new Date(user.last_activity);
            const day = date.getDate().toString().padStart(2, '0');
            const month = (date.getMonth() + 1).toString().padStart(2, '0');
            const year = date.getFullYear().toString().slice(-2);
            const hours = date.getHours().toString().padStart(2, '0');
            const minutes = date.getMinutes().toString().padStart(2, '0');
            activityCell.textContent = `${day}/${month}/${year} ${hours}:${minutes}`;
        });
    } else {
        const row = table.insertRow(-1);
        const cell = row.insertCell(0);
        cell.colSpan = 3;
        cell.className = 'content';
        cell.textContent = 'Aucun utilisateur actif ces 10 dernières minutes.';
    }
    
    document.getElementById('last-updated').textContent = 
        'Dernière mise à jour: ' + new Date().toLocaleTimeString();
}

function fetchData() {
    fetch('users.php?fetch_data=true')
        .then(response => response.json())
        .then(data => {
            updateUI(data);
        })
        .catch(error => {
            console.error('Error fetching data:', error);
        })
        .finally(() => {
            pollingTimer = setTimeout(fetchData, pollingInterval);
        });
}

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('last-updated').textContent = 
        'Dernière mise à jour: ' + new Date().toLocaleTimeString();
    fetchData();
});