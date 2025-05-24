<?php

include_once '../../loading.php';
include_once '../../database/database.php';
include_once 'security.php';

if($is_admin != 0){
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        $get_current_user_id = $pdo->prepare("SELECT id FROM USER WHERE email = :mail");
        $get_current_user_id->bindParam(':mail', $_SESSION['mail'], PDO::PARAM_STR);
        $get_current_user_id->execute();
        $current_user_id = $get_current_user_id->fetchColumn();

        if ($current_user_id == $id) {
            echo "cannot delete your own account";
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

                    $deleteTeamStmt = $pdo->prepare("DELETE FROM TEAM WHERE id = :team_id");
                    $deleteTeamStmt->bindParam(':team_id', $teamId, PDO::PARAM_INT);
                    $deleteTeamStmt->execute();
                }
            }
            
            $deleteTeamMemberStmt = $pdo->prepare("DELETE FROM TEAM_MEMBER WHERE id_user = :id");
            $deleteTeamMemberStmt->bindParam(':id', $id, PDO::PARAM_INT);
            $deleteTeamMemberStmt->execute();
            
            $deleteActivityInscriptionStmt = $pdo->prepare("DELETE FROM ACTIVITY_INSCRIPTION WHERE id_user = :id");
            $deleteActivityInscriptionStmt->bindParam(':id', $id, PDO::PARAM_INT);
            $deleteActivityInscriptionStmt->execute();
            
            $deleteActivitiesStmt = $pdo->prepare("DELETE FROM TEAM_ACTIVITY WHERE id_user = :id AND id_team IS NULL");
            $deleteActivitiesStmt->bindParam(':id', $id, PDO::PARAM_INT);
            $deleteActivitiesStmt->execute();
            
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
            
            $deleteUserStmt = $pdo->prepare("DELETE FROM USER WHERE id = :id");
            $deleteUserStmt->bindParam(':id', $id, PDO::PARAM_INT);
            $deleteUserStmt->execute();

            $pdo->commit();

            header("Location: ../users.php");
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