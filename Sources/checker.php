<?php

if(isset($_SESSION['mail'])){

    try{

        $currentTimestamp = date('Y-m-d H:i:s');

        $user_id_stmt = $pdo->prepare("SELECT id FROM USER WHERE email = :mail");
        $user_id_stmt->bindParam(':mail', $_SESSION['mail']);
        $user_id_stmt->execute();
        $user_id = $user_id_stmt->fetchColumn();

        $update_last_activity = $pdo->prepare("UPDATE USER SET last_activity = :tmp WHERE id = :mail");
        $update_last_activity->bindParam(':tmp', $currentTimestamp);
        $update_last_activity->bindParam(':mail', $user_id);
        $update_last_activity->execute();
    }
    catch (PDOException $e) {
        echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
    }
}

?>