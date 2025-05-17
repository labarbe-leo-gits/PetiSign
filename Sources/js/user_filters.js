document.addEventListener('DOMContentLoaded', function() {

    const roleFilters = document.querySelectorAll('.filter_section input[type="checkbox"][name="benevole"], .filter_section input[type="checkbox"][name="admin"], .filter_section input[type="checkbox"][name="user"]');
    const bannedFilter = document.getElementById('banned');
    const searchType = document.getElementById('type');
    const searchInput = document.getElementById('search');
    
    roleFilters.forEach(filter => {
        filter.addEventListener('change', applyFilters);
    });
    
    bannedFilter.addEventListener('change', applyFilters);
    searchType.addEventListener('change', applyFilters);
    searchInput.addEventListener('input', applyFilters);
    
    function applyFilters() {
        const roles = [];
        if(document.getElementById('benevole').checked) roles.push('benevole');
        if(document.getElementById('admin').checked) roles.push('admin');
        if(document.getElementById('user').checked) roles.push('user');
        
        const banned = document.getElementById('banned').checked;
        const searchType = document.getElementById('type').value;
        const searchValue = document.getElementById('search').value;
        
        const formData = new FormData();
        formData.append('action', 'filter');
        formData.append('roles', JSON.stringify(roles));
        formData.append('banned', banned);
        formData.append('searchType', searchType);
        formData.append('searchValue', searchValue);
        
        fetch('Processus/filter_users.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(html => {
            document.querySelector('.tableau table tbody').innerHTML = html;
        })
        .catch(error => {
            console.error('Error fetching filtered users:', error);
        });
    }
});