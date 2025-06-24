<?php
include_once 'header.php';
include_once '../checker.php';

$banned_usernames_json_path = '../json/banned_username.json';

function getBannedUsernames(){
    global $banned_usernames_json_path;
    if(file_exists($banned_usernames_json_path)){
        $banned_usernames_json = file_get_contents($banned_usernames_json_path);
        $data = json_decode($banned_usernames_json, true);
        return isset($data['banned_usernames']) ? $data['banned_usernames'] : [];
    } else {
        return [];
    }
}

function saveToFile($usernames){
    global $banned_usernames_json_path;
    $data = ['banned_usernames' => $usernames];
    $json_data = json_encode($data, JSON_PRETTY_PRINT);
    file_put_contents($banned_usernames_json_path, $json_data);
}

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $banned = getBannedUsernames();
    if(isset($_POST['add_username']) && !empty($_POST['new_username'])){
        $new_username = trim($_POST['new_username']);
        $username = filter_var($new_username, FILTER_SANITIZE_STRING);
        if(!in_array($username, $banned)){
            $banned[] = $username;
            saveToFile($banned);

            $check_if_a_user_have_this_username = $pdo->prepare("SELECT id FROM USER WHERE username = :usrname");
            $check_if_a_user_have_this_username->bindParam(':usrname', $username);
            $check_if_a_user_have_this_username->execute();
            $existing_user = $check_if_a_user_have_this_username->fetchColumn();

            $random_seed = bin2hex(random_bytes(5));

            if($existing_user){
                $rename_said_user = $pdo->prepare("UPDATE USER SET username = CONCAT(CONCAT(id, '__banned_username__'), $random_seed) WHERE id = :id");
                $rename_said_user->bindParam(':id', $existing_user);
                $rename_said_user->execute();
            }

            echo "<script>alert('Ajout réussi');</script>";
        } else {
            echo "<script>alert('Nom d\'utilisateur déjà banni !');</script>";
        }
    }

    if(isset($_POST['delete_username']) && isset($_POST['username'])){
        $username_to_delete = trim($_POST['username']);
        $username = filter_var($username_to_delete, FILTER_SANITIZE_STRING);
        $key = array_search($username, $banned);

        if($key !== false){
            unset($banned[$key]);
            $banned = array_values($banned);
            saveToFile($banned);
            echo "<script>alert('Pseudo supprimé avec succès');</script>";
        }
    }
}

$banned = getBannedUsernames();

?>

<link rel="stylesheet" href="../css/backoffice_tablepages.css">
<link rel="stylesheet" href="../css/backoffice_addcaptcha.css">
<link rel="stylesheet" href="../css/backoffice_banned_usernames.css">

<div class="right_panel">

    <div class="container">
        <h2 class="highlighted-text" id="page_title">Gestion des noms d'utilisateur interdits</h2>
        <form method="POST" action="">
            <div class="entries">
                <input type="text" id="new_username" name="new_username" required placeholder="">
                <label for="new_username">Nom d'utilisateur</label>
                <button type="submit" name="add_username" class="custom-button smaller add_btn"><img src="/Resources/img/ui_icons/plus.png" alt=""></button>
            </div>
        </form>
        <div class="current">
            <div class="entries">
                <input type="text" name="" id="usernameSearch" class="search-box" placeholder="" onkeyup="filterUsernames()">
                <label for="usernameSearch">Recherche</label>
            </div>
            
            <?php

            if(empty($banned)){
                echo "<p>Aucun nom d'utilisateur interdit trouvé.</p>";
            } else {
                echo "<table class='banned_usernames_table'>";
                echo "<thead><tr><th>Nom d'utilisateur</th><th>Action</th></tr></thead>";
                echo "<tbody id='usernameTable'>";
                foreach($banned as $username){
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($username) . "</td>";
                    echo "<td>
                            <form method='POST' action=''>
                                <input type='hidden' name='username' value='" . htmlspecialchars($username) . "'>
                                <button type='submit' name='delete_username' class='delete_btn'>Supprimer</button>
                            </form>
                          </td>";
                    echo "</tr>";
                }
                echo "</tbody></table>";
            }

            ?>

        </div>
    </div>

    <script>
        function filterUsernames(){
            const input = document.getElementById('usernameSearch');
            const filter = input.value.toLowerCase();
            const table = document.getElementById('usernameTable');
            const rows = table.getElementsByTagName('tr');

            for(let i = 0; i < rows.length; i++){
                const td = rows[i].getElementsByTagName('td')[0];
                if(td){
                    const txtValue = td.textContent || td.innerText;
                    if(txtValue.toLowerCase().indexOf(filter) > -1){
                        rows[i].style.display = '';
                    } else {
                        rows[i].style.display = 'none';
                    }
                }
            }
        }
    </script>

</div>

<?php
include_once 'footer.php';
?>