setInterval(() => {
    fetch('fetch_logs.php')
        .then(response => response.text())
        .then(data => {
            document.getElementById('logs_div').innerHTML = data;
            updateCount('AUTH01', 'connection-count');
            updateCount('AUTH03', 'new-account-count');
            updateCount('N3WC0M', 'new-coms-count');
            updateCount('N3WR3P', 'new-report-count');
            updateCount('N3WS1N', 'new-sign-count');
            updateUniqueCount('D1SC0V', 'discover-count');
            updateUniqueCount('PROF1L', 'profile-count');
            updateUniqueCount('MYS1GN', 'mysign-count');
            updateUniqueCount('MYP3TS', 'mypet-count');
            updateUniqueCount('MSG1NG', 'msg-count');
            updateUniqueCount('AC3UIL', 'index-count');
            updateUniqueCount('N3WP3T', 'new-pet-count');
            updateUniqueCount('D0WNLD', 'dl_count');
            //updateUniqueCount('N3WC0M', 'new-coms-count');
            //updateUniqueCount('N3WREP', 'new-report-count');
            //updateUniqueCount('N3WS1N', 'new-sign-count');

        })
        .catch(error => console.error('Error fetching logs:', error));
}, 1000);

function updateCount(keyword, elementId) {
    let logContent = document.getElementById('logs_div').innerHTML;
    let count = (logContent.match(new RegExp(keyword, 'g')) || []).length;
    document.getElementById(elementId).innerHTML = count;
}

function updateUniqueCount(keyword, elementId) {
    let logContent = document.getElementById('logs_div').innerHTML;
    let lines = logContent.split('<br>');
    let uniqueIPs = new Set();

    lines.forEach(line => {
        if (line.includes(keyword)) {
            let ipMatch = line.match(/\b(?:\d{1,3}\.){3}\d{1,3}\b/);
            if (ipMatch) {
                uniqueIPs.add(ipMatch[0]);
            }
        }
    });

    document.getElementById(elementId).innerHTML = uniqueIPs.size;
}