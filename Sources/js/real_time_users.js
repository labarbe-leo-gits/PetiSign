let pollingInterval = 1000; // Poll every 10 seconds by default
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
            activityCell.textContent = user.last_activity;
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