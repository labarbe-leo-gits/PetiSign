<?php
session_start();
include_once '../../database/database.php';
include_once '../../checker.php';

$get_current_admin_id = $pdo->prepare("SELECT id FROM USER WHERE email = :mail");
$get_current_admin_id->bindParam(':mail', $_SESSION['mail'], PDO::PARAM_STR);
$get_current_admin_id->execute();
$current_admin_id = $get_current_admin_id->fetchColumn();

if(isset($_POST['action']) && $_POST['action'] === 'filter') {
    $roles = isset($_POST['roles']) ? json_decode($_POST['roles'], true) : [];
    $banned = isset($_POST['banned']) ? filter_var($_POST['banned'], FILTER_VALIDATE_BOOLEAN) : false;
    $searchType = isset($_POST['searchType']) ? $_POST['searchType'] : '';
    $searchValue = isset($_POST['searchValue']) ? $_POST['searchValue'] : '';
    
    $sql = "SELECT * FROM USER";
    $conditions = [];
    $params = [];
    
    $roleConditions = [];
    if(!empty($roles)) {
        if(in_array('admin', $roles)) {
            $roleConditions[] = "is_admin = 1";
        }
        if(in_array('benevole', $roles)) {
            $roleConditions[] = "is_benevole = 1";
        }
        if(in_array('user', $roles)) {
            $roleConditions[] = "(is_admin = 0 AND is_benevole = 0)";
        }
        
        if(!empty($roleConditions)) {
            $conditions[] = '(' . implode(' OR ', $roleConditions) . ')';
        }
    }
    
    if(!empty($searchValue)) {
        switch($searchType) {
            case 'id':
                $conditions[] = "id LIKE :searchValue";
                $params[':searchValue'] = $searchValue . '%';
                break;
            case 'username':
                $conditions[] = "username LIKE :searchValue";
                $params[':searchValue'] = '%' . $searchValue . '%';
                break;
            case 'email':
                $conditions[] = "email LIKE :searchValue";
                $params[':searchValue'] = '%' . $searchValue . '%';
                break;
        }
    }

    if(!empty($conditions)) {
        $sql .= " WHERE " . implode(' AND ', $conditions);
    }
    
    try {
        $stmt = $pdo->prepare($sql);
        foreach($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo '<tr>
            <th>ID</th>
            <th>Nom d\'utilisateur</th>
            <th>Rôle(s)</th>
            <th>Actions</th>
        </tr>';
        
        foreach($users as $user) {
            $user_ban = $pdo->prepare("SELECT COUNT(*) FROM BAN WHERE id_user = :id");
            $user_ban->bindParam(':id', $user['id'], PDO::PARAM_INT);
            $user_ban->execute();
            $ban = $user_ban->fetchColumn();
            
            if($banned && $ban == 0) {
                continue;
            }
            
            echo "<tr>";
            echo "<td class='id ".($ban > 0 ? "banned" : "")."'>".$user['id']."</td>";
            echo "<td class='content ".($ban > 0 ? "banned" : "")."'>".$user['username']."</td>";
            if($user['is_admin'] == 1){
                if($user['is_benevole'] == 1){
                    echo "<td class='content ".($ban > 0 ? "banned" : "")."'>Administrateur, Bénévole</td>";
                } else {
                    echo "<td class='content".($ban > 0 ? "banned" : "")."'>Administrateur</td>";
                }
            } else if($user['is_benevole'] == 1){
                echo "<td class='content ".($ban > 0 ? "banned" : "")."'>Bénévole</td>";
            } else {
                echo "<td class='content ".($ban > 0 ? "banned" : "")."'>Utilisateur</td>";
            }
            echo "<td class='actions'>";
            echo "<a href='../view_profile.php?id=" . htmlspecialchars($user['id'], ENT_QUOTES, 'UTF-8') . "' class='action'><img src='../../Resources/img/ui_icons/eye.png' alt='Voir'></a>";
            echo "<a href='' class='void'>&nbsp;</a>";
            echo "<a href='modify_user_form.php?id=" . htmlspecialchars($user['id'], ENT_QUOTES, 'UTF-8') . "' class='action'><img src='../../Resources/img/ui_icons/crayon.png' alt='Modify'></a>";
            echo "<a href='' class='void'>&nbsp;</a>";
            
            if($current_admin_id != $user['id']) {
                echo "<a href='delete_user.php?id=" . htmlspecialchars($user['id'], ENT_QUOTES, 'UTF-8') . "' class='action'><img src='../../Resources/img/ui_icons/trash.png' alt='Delete'></a>";
                echo "<a href='' class='void'>&nbsp;</a>";
            }
            
            if($current_admin_id == $user['id']){
                echo "<a href='' class='void'></a>";
            } else {
                if($ban > 0){
                    echo "<a href='unban_user.php?id=" . htmlspecialchars($user['id'], ENT_QUOTES, 'UTF-8') . "' class='action'><img src='../../Resources/img/ui_icons/ban.png' alt='Débannir'></a>";
                } else {
                    echo "<a href='ban_user_form.php?id=" . htmlspecialchars($user['id'], ENT_QUOTES, 'UTF-8') . "' class='action'><img src='../../Resources/img/ui_icons/ban-user.png' alt='Bannir'></a>";
                }
            }
            
            if($current_admin_id != $user['id']){
                echo "<a href='' class='void'>&nbsp;</a>";
                echo "<a href='' class='action'><img src='../../Resources/img/ui_icons/newsletter.png' alt='Modify'></a>";
            }
            
            echo "</td>";
            echo "</tr>";
        }
        
    } catch (PDOException $e) {
        echo "<tr>";
        echo "<td class='id'>N/A</td>";
        echo "<td class='content'>Error</td>";
        echo "<td class='content'>".$e->getMessage()."</td>";
        echo "<td class='actions'></td>";
        echo "</tr>";
    }
}
?>