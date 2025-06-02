<?php
include_once '../loading.php';
use PHPMailer\PHPMailer\PHPMailer;
include_once 'write_logs.php';

try{

    session_start();

    if (!isset($_SESSION['mail'])) {
        header('Location: ../login.php');
        exit();
    }

    include_once '../database/database.php';
    require_once '../SendNewsletterFunction.php';


    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $petition_id = htmlspecialchars(filter_input(INPUT_POST, 'petition_id', FILTER_SANITIZE_NUMBER_INT));

        if (!isset($_POST['check'])) {
            header('Location: ../view_petition.php?id=' . $petition_id);
            exit();
        }

        if (!isset($_POST['check2'])) {
            header('Location: ../view_petition.php?id=' . $petition_id);
            exit();
        }

        $user_id_stmt = $pdo->prepare("SELECT id FROM USER WHERE email = :mail");
        $user_id_stmt->bindParam(':mail', $_SESSION['mail']);
        $user_id_stmt->execute();
        $user_id = $user_id_stmt->fetchColumn();

        $petition_goal_stmt = $pdo->prepare("SELECT signature_goal FROM PETITION WHERE id = :id");
        $petition_goal_stmt->bindParam(':id', $petition_id);
        $petition_goal_stmt->execute();
        $petition_goal = $petition_goal_stmt->fetchColumn();

        $signature_stmt = $pdo->prepare("SELECT COUNT(*) FROM SIGNATURE WHERE id_user = :user_id AND id_petition = :petition_id");
        $signature_stmt->bindParam(':user_id', $user_id);
        $signature_stmt->bindParam(':petition_id', $petition_id);
        $signature_stmt->execute();
        $signature_count = $signature_stmt->fetchColumn();

        if ($signature_count > 0) {
            header('Location: '. $_SERVER['HTTP_REFERER']);
            exit();
        }

        $check_goal_stmt = $pdo->prepare("SELECT signature_count FROM PETITION WHERE id = :id");
        $check_goal_stmt->bindParam(':id', $petition_id);
        $check_goal_stmt->execute();
        $check_goal = $check_goal_stmt->fetchColumn();

        if($check_goal >= $petition_goal) {
            echo "Error: The petition has already reached its goal.";
            exit();
        }

        $petition_signature_stmt = $pdo->prepare("SELECT COUNT(*) FROM SIGNATURE WHERE id_petition = :id");
        $petition_signature_stmt->bindParam(':id', $petition_id);
        $petition_signature_stmt->execute();
        $petition_signature = $petition_signature_stmt->fetchColumn();

        $insert_stmt = $pdo->prepare("INSERT INTO SIGNATURE (id_user, id_petition) VALUES (:user_id, :petition_id)");
        $insert_stmt->bindParam(':user_id', $user_id);
        $insert_stmt->bindParam(':petition_id', $petition_id);
        $insert_stmt->execute();

        $update_stmt = $pdo->prepare("UPDATE PETITION SET signature_count = signature_count + 1 WHERE id = :id");
        $update_stmt->bindParam(':id', $petition_id);
        $update_stmt->execute();

        $get_updated_count_stmt = $pdo->prepare("SELECT signature_count FROM PETITION WHERE id = :id");
        $get_updated_count_stmt->bindParam(':id', $petition_id);
        $get_updated_count_stmt->execute();
        $updated_count = $get_updated_count_stmt->fetchColumn();

        if($updated_count >= $petition_goal) {
            $update_stmt2 = $pdo->prepare("UPDATE PETITION SET statut = 'CLOSED' WHERE id = :id");
            $update_stmt2->bindParam(':id', $petition_id);
            $update_stmt2->execute();
        }

        $get_mails = $pdo->prepare("SELECT u.id, u.username, u.email FROM USER u JOIN SIGNATURE s ON u.id = s.id_user WHERE u.newsletter = 1 AND s.id_petition = 36 ORDER BY s.date DESC");
        $get_mails->execute();
        $mails = $get_mails->fetchAll(PDO::FETCH_ASSOC);

        $stages_stmt = $pdo->prepare("SELECT signature_stage_one, signature_stage_two, signature_stage_three, signature_stage_four FROM PETITION WHERE id = :id");
        $stages_stmt->bindParam(':id', $petition_id);
        $stages_stmt->execute();
        $stages = $stages_stmt->fetch(PDO::FETCH_ASSOC);

        $signature_stage_one = $stages['signature_stage_one'];
        $signature_stage_two = $stages['signature_stage_two'];
        $signature_stage_three = $stages['signature_stage_three'];
        $signature_stage_four = $stages['signature_stage_four'];

        if($updated_count == $signature_stage_one || $updated_count == $signature_stage_two || $updated_count == $signature_stage_three || $updated_count == $signature_stage_four) {
            echo "You have reached a milestone in the petition. Congratulations!";
            $mail_sent = new PHPMailer(true);

            $pet_title = $pdo->prepare("SELECT title FROM PETITION WHERE id = :id");
            $pet_title->bindParam(':id', $petition_id);
            $pet_title->execute();
            $title_pet = $pet_title->fetchColumn();

            foreach ($mails as $mail) {
                $title = "Suivi de signatures";
                EnvoieMail($mail_sent, $mail['email'], $mail['username'], $title, "La pétition ". $title_pet . " a atteint " . $updated_count . " signatures sur " . $petition_goal . " !<br /><br />C'est un pas de plus vers votre cause !<br/><br />L'équipe de PétiSign vous félicite pour votre engagement !", "abonné à notre newsletter.");
            }

        }

        $user = $pdo->prepare("SELECT username FROM USER WHERE id = :id");
        $user->bindParam(':id', $user_id);
        $user->execute();
        $user = $user->fetchColumn();
        $ip = $_SERVER['REMOTE_ADDR'];

        $pet_title = $pdo->prepare("SELECT title FROM PETITION WHERE id = :id");
        $pet_title->bindParam(':id', $petition_id);
        $pet_title->execute();
        $title_pet = $pet_title->fetchColumn();

        write_logs('../logs/log.txt', 'N3WS1N', $user, $ip, 'Pétition signée');

        header('Location: ../view_petition.php?id=' . $petition_id);
        exit();


    } else {
        header('Location: ../index.php');
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}
?>