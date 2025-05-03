<?php

session_start();

$jsonFilePath = '../Sources/json/banned_username.json';

function getBannedUsernames() {
    global $jsonFilePath;
    if (file_exists($jsonFilePath)) {
        $jsonContent = file_get_contents($jsonFilePath);
        $data = json_decode($jsonContent, true);
        return isset($data['banned_usernames']) ? $data['banned_usernames'] : [];
    }
    return [];
}

// Save banned usernames to file
function saveBannedUsernames($usernames) {
    global $jsonFilePath;
    $data = ['banned_usernames' => $usernames];
    $jsonContent = json_encode($data, JSON_PRETTY_PRINT);
    file_put_contents($jsonFilePath, $jsonContent);
}

$message = '';
$messageType = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bannedUsernames = getBannedUsernames();
    
    if (isset($_POST['add_username']) && !empty($_POST['new_username'])) {
        $newUsername = trim($_POST['new_username']);
        
        if (!in_array($newUsername, $bannedUsernames)) {
            $bannedUsernames[] = $newUsername;
            sort($bannedUsernames); // Keep the list alphabetically sorted
            saveBannedUsernames($bannedUsernames);
            $message = "Username '{$newUsername}' has been banned.";
            $messageType = 'success';
        } else {
            $message = "Username '{$newUsername}' is already banned.";
            $messageType = 'warning';
        }
    }
    
    if (isset($_POST['delete_username']) && isset($_POST['username'])) {
        $usernameToDelete = $_POST['username'];
        $key = array_search($usernameToDelete, $bannedUsernames);
        
        if ($key !== false) {
            unset($bannedUsernames[$key]);
            $bannedUsernames = array_values($bannedUsernames); // Reindex array
            saveBannedUsernames($bannedUsernames);
            $message = "Username '{$usernameToDelete}' has been unbanned.";
            $messageType = 'success';
        }
    }
}

$bannedUsernames = getBannedUsernames();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Banned Usernames</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            line-height: 1.6;
        }
        .container {
            display: flex;
            gap: 30px;
        }
        .left-panel {
            flex: 1;
        }
        .right-panel {
            flex: 2;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input[type="text"] {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }
        .alert {
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }
        .alert-warning {
            background-color: #fff3cd;
            color: #856404;
        }
        .btn {
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-primary {
            background-color: #007bff;
            color: white;
        }
        .btn-danger {
            background-color: #dc3545;
            color: white;
        }
        .search-box {
            padding: 8px;
            width: 100%;
            margin-bottom: 10px;
            box-sizing: border-box;
        }
        .count-badge {
            background-color: #6c757d;
            color: white;
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 0.8em;
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <h1>Manage Banned Usernames <span class="count-badge"><?php echo count($bannedUsernames); ?></span></h1>
    
    <?php if (!empty($message)): ?>
        <div class="alert alert-<?php echo $messageType; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>
    
    <div class="container">
        <div class="left-panel">
            <h2>Add New Banned Username</h2>
            <form method="post" action="">
                <div class="form-group">
                    <label for="new_username">Username:</label>
                    <input type="text" id="new_username" name="new_username" required>
                </div>
                <button type="submit" name="add_username" class="btn btn-primary">Ban Username</button>
            </form>
        </div>
        
        <div class="right-panel">
            <h2>Current Banned Usernames</h2>
            
            <input type="text" id="usernameSearch" class="search-box" placeholder="Search usernames..." onkeyup="filterUsernames()">
            
            <?php if (empty($bannedUsernames)): ?>
                <p>No banned usernames found.</p>
            <?php else: ?>
                <table id="usernamesTable">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th width="100">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bannedUsernames as $username): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($username); ?></td>
                                <td>
                                    <form method="post" action="" style="display: inline;">
                                        <input type="hidden" name="username" value="<?php echo htmlspecialchars($username); ?>">
                                        <button type="submit" name="delete_username" class="btn btn-danger"
                                         onclick="return confirm('Are you sure you want to unban this username?')">
                                            Unban
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        function filterUsernames() {
            const input = document.getElementById('usernameSearch');
            const filter = input.value.toLowerCase();
            const table = document.getElementById('usernamesTable');
            const tr = table.getElementsByTagName('tr');
            
            for (let i = 1; i < tr.length; i++) {
                const td = tr[i].getElementsByTagName('td')[0];
                if (td) {
                    const txtValue = td.textContent || td.innerText;
                    if (txtValue.toLowerCase().indexOf(filter) > -1) {
                        tr[i].style.display = '';
                    } else {
                        tr[i].style.display = 'none';
                    }
                }
            }
        }
    </script>
</body>
</html>