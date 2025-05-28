<?php
include_once '../../loading.php';
include_once '../../database/database.php';
include_once 'security.php';
use PHPMailer\PHPMailer\PHPMailer;
require_once '../../send_notif.php';

if($is_admin != 0){
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $id_user = intval($_GET['user_id']);

        try {
            $stmt = $pdo->prepare("UPDATE USER_CANDIDATE SET current_status = 'Accepté' WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $set_benevole_to_user = $pdo->prepare("UPDATE USER SET is_benevole = 1 WHERE id = :id");
            $set_benevole_to_user->bindParam(':id', $id_user, PDO::PARAM_INT);
            $set_benevole_to_user->execute();

            $filtered_mail_stmt = $pdo->prepare("SELECT email, username FROM USER WHERE id = :id");
            $filtered_mail_stmt->bindParam(':id', $id_user, PDO::PARAM_INT);
            $filtered_mail_stmt->execute();
            $filtered_mail = $filtered_mail_stmt->fetch(PDO::FETCH_ASSOC);
            $mail = $filtered_mail['email'];

            $filtered_username = filter_var($filtered_mail['username'], FILTER_SANITIZE_STRING);

            $mail_sent = new PHPMailer(true);
            EnvoieMail($mail_sent, $mail, $filtered_username, "Votre candidature Bénévole PétiSign", "Votre candidature en tant que bénévole a été acceptée. Vous pouvez dès à présent vous connecter à votre compte pour participer aux activités bénévole, créer votre équipe et vous inscrire aux événements.");

            header("Location: ../candidates.php");
            exit();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        header("Location: ../candidates.php");
        exit();
    }
} else {
    header('Location: /Sources/error.php?code=403');
    exit();

}
?>