<?php
include_once '../../loading.php';
include_once '../../database/database.php';
include_once 'security.php';

if($is_admin != 0){

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
        $admin = filter_input(INPUT_POST, 'administrator', FILTER_SANITIZE_NUMBER_INT);
        $benevole = filter_input(INPUT_POST, 'benevole', FILTER_SANITIZE_NUMBER_INT);
        $new_pswd = filter_input(INPUT_POST, 'new_pswd', FILTER_SANITIZE_STRING);
        $newmail = filter_input(INPUT_POST, 'emailaddress', FILTER_SANITIZE_EMAIL);
        $newmail = filter_var($newmail, FILTER_VALIDATE_EMAIL);

        $get_username_before = $pdo->prepare("SELECT username FROM USER WHERE id = :id");
        $get_username_before->bindParam(':id', $id, PDO::PARAM_INT);
        $get_username_before->execute();
        $username_before = $get_username_before->fetchColumn();

        if ($username_before != $username) {
            $check_if_username_exists = $pdo->prepare("SELECT COUNT(id) FROM USER WHERE username = :username");
            $check_if_username_exists->bindParam(':username', $username);
            $check_if_username_exists->execute();
            $username_exists = $check_if_username_exists->fetchColumn();

            if ($username_exists > 0) {
                header("Location: ../modify_user_form.php?id=$id&error=username_exists");
                exit();
            }
        }

        $get_mail_before = $pdo->prepare("SELECT email FROM USER WHERE id = :id");
        $get_mail_before->bindParam(':id', $id, PDO::PARAM_INT);
        $get_mail_before->execute();
        $mail_before = $get_mail_before->fetchColumn();

        if($mail_before != $newmail){
            $check_if_mail_exists = $pdo->prepare("SELECT COUNT(id) FROM USER WHERE email = :email");   
            $check_if_mail_exists->bindParam(':email', $newmail);
            $check_if_mail_exists->execute();
            $mail_exists = $check_if_mail_exists->fetchColumn();
            if ($mail_exists > 0) {
                header("Location: ../modify_user_form.php?id=$id&error=mail_exists");
                exit();
            }
        }

        if ($new_pswd != "" or $new_pswd != null) {
            $new_pswd = password_hash($new_pswd, PASSWORD_DEFAULT);
            try {
                $stmt = $pdo->prepare("UPDATE USER SET password = :password WHERE id = :id");
                $stmt->bindParam(':password', $new_pswd);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
            } catch (PDOException $e) {
                echo "Error: " . $e->POSTMessage();
            }
        }

        if (isset($id, $username, $admin, $benevole, $newmail)) {
            try {
                $stmt = $pdo->prepare("UPDATE USER SET is_admin = :is_admin, is_benevole = :is_benevole, username = :username, email = :email WHERE id = :id");
                $stmt->bindParam(':is_admin', $admin);
                $stmt->bindParam(':is_benevole', $benevole);
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':email', $newmail);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                header("Location: ../users.php");
                exit();
            } catch (PDOException $e) {
                echo "Error: " . $e->POSTMessage();
            }
        } else {
            header("Location: ../modify_user_form.php?id=$id");
            exit();
        }
    } else {
        header("Location: ../users.php");
        exit();
    }
} else {
    header('Location: /Sources/error.php?code=403');
    exit();

}

?>