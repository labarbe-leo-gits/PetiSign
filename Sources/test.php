<?php

include_once 'database/database.php';

function getActiveUsers($pdo) {

    $tenMinutesAgo = date('Y-m-d H:i:s', time() - (10 * 60));

    $query = "SELECT * FROM USER WHERE last_activity >= ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$tenMinutesAgo]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$activeUsers = getActiveUsers($pdo);

if (isset($_GET['fetch_data']) && $_GET['fetch_data'] === 'true') {
    header('Content-Type: application/json');
    echo json_encode($activeUsers);
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Active Users</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        #polling-status {
            font-size: 12px;
            color: #888;
            margin-top: 10px;
        }
        #polling-interval {
            width: 60px;
        }
    </style>
</head>
<body>
    <div id="data-container">
        <h2>Users active in the last 10 minutes:</h2>
        <?php if (count($activeUsers) > 0): ?>
            <ul>
                <?php foreach ($activeUsers as $user): ?>
                    <li><?= $user['username'] ?> - Last active: <?= $user['last_activity'] ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No users were active in the last 10 minutes.</p>
        <?php endif; ?>
    </div>

    <div>
        <label for="polling-interval">Refresh interval (ms):</label>
        <input type="number" id="polling-interval" value="1000" min="500" max="10000">
        <button id="update-interval">Update</button>
    </div>
    <div id="polling-status">Polling every 1000ms</div>
    <div id="last-updated"></div>

    <script>
        let pollingInterval = 100;
        let pollingTimer;

        function updateUI(users) {
            const container = document.getElementById('data-container');
            
            let html = '<h2>Users active in the last 10 minutes:</h2>';
            
            if (users.length > 0) {
                html += '<ul>';
                users.forEach(function(user) {
                    html += `<li>${user.username} - Last active: ${user.last_activity}</li>`;
                });
                html += '</ul>';
            } else {
                html += '<p>No users were active in the last 10 minutes.</p>';
            }
            
            container.innerHTML = html;
            
            document.getElementById('last-updated').textContent = 
                'Last updated: ' + new Date().toLocaleTimeString();
        }

        function fetchData() {
            fetch('test.php?fetch_data=true')
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
                'Last updated: ' + new Date().toLocaleTimeString();
                

            fetchData();

            document.getElementById('update-interval').addEventListener('click', function() {
                const newInterval = parseInt(document.getElementById('polling-interval').value);
                if (newInterval >= 500) {
                    pollingInterval = newInterval;
                    document.getElementById('polling-status').textContent = 
                        'Polling every ' + pollingInterval + 'ms';
                    
                    clearTimeout(pollingTimer);
                    fetchData();
                } else {
                    alert('Minimum interval is 500ms');
                    document.getElementById('polling-interval').value = 500;
                }
            });
        });
    </script>
</body>
</html>