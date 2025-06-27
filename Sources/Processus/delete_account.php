<?php

session_start();

include_once '../loading.php';
include_once '../database/database.php';
use PHPMailer\PHPMailer\PHPMailer;
require_once '../../send_notif.php';

if(!isset($_SESSION['mail'])) {
    echo "You must be logged in to delete your account.";
    echo "<script>window.location.href = '../login.php';</script>";
    exit();
}

if(isset($_GET['user_action']) && $_GET['user_action'] == 'delete_account') {

    if (isset($_GET['id'])) {

        $id = intval($_GET['id']);

        $get_current_user_id = $pdo->prepare("SELECT id FROM USER WHERE email = :mail");
        $get_current_user_id->bindParam(':mail', $_SESSION['mail'], PDO::PARAM_STR);
        $get_current_user_id->execute();
        $current_user_id = $get_current_user_id->fetchColumn();

        $filtered_username_stmt = $pdo->prepare("SELECT username FROM USER WHERE id = :id");
        $filtered_username_stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $filtered_username_stmt->execute();
        $filtered_username = $filtered_username_stmt->fetchColumn();

        if($current_user_id != $id) {
            echo "You can only delete your own account.";
            echo "<script>window.location.href = '../profile.php';</script>";
            exit();
        }

        try {
            $pdo->beginTransaction();
            
            $checkLeaderStmt = $pdo->prepare("SELECT id FROM TEAM WHERE leader = :id");
            $checkLeaderStmt->bindParam(':id', $id, PDO::PARAM_INT);
            $checkLeaderStmt->execute();
            $teamsLed = $checkLeaderStmt->fetchAll(PDO::FETCH_COLUMN);
            
            foreach ($teamsLed as $teamId) {
                $findMemberStmt = $pdo->prepare("
                    SELECT id_user FROM TEAM_MEMBER 
                    WHERE id_team = :team_id AND id_user != :user_id
                    LIMIT 1
                ");
                $findMemberStmt->bindParam(':team_id', $teamId, PDO::PARAM_INT);
                $findMemberStmt->bindParam(':user_id', $id, PDO::PARAM_INT);
                $findMemberStmt->execute();
                
                $newLeader = $findMemberStmt->fetchColumn();
                
                if ($newLeader) {
                    $updateLeaderStmt = $pdo->prepare("UPDATE TEAM SET leader = :new_leader WHERE id = :team_id");
                    $updateLeaderStmt->bindParam(':new_leader', $newLeader, PDO::PARAM_INT);
                    $updateLeaderStmt->bindParam(':team_id', $teamId, PDO::PARAM_INT);
                    $updateLeaderStmt->execute();
                } else {
                    $getTeamActivitiesStmt = $pdo->prepare("SELECT id FROM TEAM_ACTIVITY WHERE id_team = :team_id");
                    $getTeamActivitiesStmt->bindParam(':team_id', $teamId, PDO::PARAM_INT);
                    $getTeamActivitiesStmt->execute();
                    $teamActivities = $getTeamActivitiesStmt->fetchAll(PDO::FETCH_COLUMN);
                    
                    foreach ($teamActivities as $activityId) {
                        $deleteActivityInscriptionsStmt = $pdo->prepare("DELETE FROM ACTIVITY_INSCRIPTION WHERE id_activity = :activity_id");
                        $deleteActivityInscriptionsStmt->bindParam(':activity_id', $activityId, PDO::PARAM_INT);
                        $deleteActivityInscriptionsStmt->execute();
                    }
                    
                    $deleteTeamActivitiesStmt = $pdo->prepare("DELETE FROM TEAM_ACTIVITY WHERE id_team = :team_id");
                    $deleteTeamActivitiesStmt->bindParam(':team_id', $teamId, PDO::PARAM_INT);
                    $deleteTeamActivitiesStmt->execute();
                    
                    $deleteTeamStmt = $pdo->prepare("DELETE FROM TEAM WHERE id = :team_id");
                    $deleteTeamStmt->bindParam(':team_id', $teamId, PDO::PARAM_INT);
                    $deleteTeamStmt->execute();
                }
            }

            $deleteActivityInscriptionStmt = $pdo->prepare("DELETE FROM ACTIVITY_INSCRIPTION WHERE id_user = :id");
            $deleteActivityInscriptionStmt->bindParam(':id', $id, PDO::PARAM_INT);
            $deleteActivityInscriptionStmt->execute();
            
            $deleteIndividualActivitiesStmt = $pdo->prepare("DELETE FROM TEAM_ACTIVITY WHERE id_user = :id AND id_team IS NULL");
            $deleteIndividualActivitiesStmt->bindParam(':id', $id, PDO::PARAM_INT);
            $deleteIndividualActivitiesStmt->execute();
            
            $deleteTeamMemberStmt = $pdo->prepare("DELETE FROM TEAM_MEMBER WHERE id_user = :id");
            $deleteTeamMemberStmt->bindParam(':id', $id, PDO::PARAM_INT);
            $deleteTeamMemberStmt->execute();

            $get_all_signatures_mobile_filename = $pdo->prepare("SELECT mobile_signature_filename FROM SIGNATURE WHERE id_user = :id");
            $get_all_signatures_mobile_filename->bindParam(':id', $id, PDO::PARAM_INT);
            $get_all_signatures_mobile_filename->execute();
            $signatures = $get_all_signatures_mobile_filename->fetchAll(PDO::FETCH_ASSOC);

            foreach ($signatures as $signature) {
                if (!empty($signature['mobile_signature_filename'])) {
                    $file_path = '../../../Resources/signatures/' . $signature['mobile_signature_filename'];
                    if (file_exists($file_path)) {
                        unlink($file_path);
                    }
                }
            }
            
            $deleteSignatureStmt = $pdo->prepare("DELETE FROM SIGNATURE WHERE id_user = :id");
            $deleteSignatureStmt->bindParam(':id', $id, PDO::PARAM_INT);
            $deleteSignatureStmt->execute();
            
            $deletePetitionStmt = $pdo->prepare("DELETE FROM PETITION WHERE user = :id");
            $deletePetitionStmt->bindParam(':id', $id, PDO::PARAM_INT);
            $deletePetitionStmt->execute();
            
            $getDiscussionsStmt = $pdo->prepare("SELECT id FROM DISCUSSION WHERE id_user = :id OR id_second_user = :id");
            $getDiscussionsStmt->bindParam(':id', $id, PDO::PARAM_INT);
            $getDiscussionsStmt->execute();
            $discussions = $getDiscussionsStmt->fetchAll(PDO::FETCH_COLUMN);
            
            foreach ($discussions as $discussionId) {
                $deleteMessagesStmt = $pdo->prepare("DELETE FROM MESSAGE WHERE id_discussion = :discussion_id");
                $deleteMessagesStmt->bindParam(':discussion_id', $discussionId, PDO::PARAM_INT);
                $deleteMessagesStmt->execute();
            }
            
            $deleteDiscussionsStmt = $pdo->prepare("DELETE FROM DISCUSSION WHERE id_user = :id OR id_second_user = :id");
            $deleteDiscussionsStmt->bindParam(':id', $id, PDO::PARAM_INT);
            $deleteDiscussionsStmt->execute();
            
            $deleteCommentsStmt = $pdo->prepare("DELETE FROM COMMENT WHERE id_user = :id");
            $deleteCommentsStmt->bindParam(':id', $id, PDO::PARAM_INT);
            $deleteCommentsStmt->execute();

            $deleteNewsletterSubStmt = $pdo->prepare("DELETE FROM ABONNEMENT WHERE id_user = :id");
            $deleteNewsletterSubStmt->bindParam(':id', $id, PDO::PARAM_INT);
            $deleteNewsletterSubStmt->execute();

            $deleteReportsStmt = $pdo->prepare("DELETE FROM REPORT WHERE id_user = :id OR (report_type = 0 AND id_target = :id)");
            $deleteReportsStmt->bindParam(':id', $id, PDO::PARAM_INT);
            $deleteReportsStmt->execute();

            $deleteDonationsStmt = $pdo->prepare("DELETE FROM DON WHERE id_user = :id");
            $deleteDonationsStmt->bindParam(':id', $id, PDO::PARAM_INT);
            $deleteDonationsStmt->execute();
            
            $deleteBansStmt = $pdo->prepare("DELETE FROM BAN WHERE id_user = :id");
            $deleteBansStmt->bindParam(':id', $id, PDO::PARAM_INT);
            $deleteBansStmt->execute();
            
            $delete_candidate_stmt = $pdo->prepare("DELETE FROM USER_CANDIDATE WHERE id_user = :id OR target_user = :id");
            $delete_candidate_stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $delete_candidate_stmt->execute();
            
            $delete_friendship_stmt = $pdo->prepare("DELETE FROM FRIEND WHERE id_user = :id OR id_friend = :id");
            $delete_friendship_stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $delete_friendship_stmt->execute();
            
            $delete_blocked_users_stmt = $pdo->prepare("DELETE FROM BLOCKED_USER WHERE id_user = :id OR id_blocked_user = :id");
            $delete_blocked_users_stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $delete_blocked_users_stmt->execute();
            
            $deleteUserStmt = $pdo->prepare("DELETE FROM USER WHERE id = :id");
            $deleteUserStmt->bindParam(':id', $id, PDO::PARAM_INT);
            $deleteUserStmt->execute();

            $pdo->commit();

            $mail_content = "
            Conformément à votre demande, votre compte PétiSign a été supprimé.
            <br>
            Si vous avez changé d'avis, vous pouvez toujours créer un nouveau compte.
            <br>
            Nous vous remercions pour votre participation et espérons vous revoir bientôt.
            <br>
            Si vous avez des questions ou des préoccupations, n'hésitez pas à nous contacter à l'adresse suivante :
            <a href='https://petisign.cloud/Sources/ticket.php'>https://petisign.cloud/Sources/ticket.php</a>
            <br>
            Cordialement,
            <br>
            L'équipe PétiSign";

            $mail_sent = new PHPMailer(true);
            EnvoieMail($mail_sent, $_SESSION['mail'], $filtered_username, 'Confirmation de suppression', nl2br(html_entity_decode($mail_content)));

            echo "Account deleted successfully.";
            session_destroy();
            header("Location: ../register.php?error=AccDelSucs&refere=delete_account");
            exit();
        } catch (PDOException $e) {
            $pdo->rollBack();
            echo "Error: " . $e->getMessage();
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