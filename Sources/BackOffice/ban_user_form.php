<?php
include_once 'header.php';
include_once '../../database/database.php';

$id = $_GET['id'];

$usernamestmt = $pdo->prepare("SELECT username FROM USER WHERE id = :id");
$usernamestmt->bindParam(':id', $id, PDO::PARAM_INT);
$usernamestmt->execute();
$username = $usernamestmt->fetchColumn();

$get_adminstmt = $pdo->prepare("SELECT username FROM USER WHERE email = :mail");
$get_adminstmt->bindParam(':mail', $_SESSION['mail'], PDO::PARAM_STR);
$get_adminstmt->execute();
$admin = $get_adminstmt->fetchColumn();

$admin_id_stmt = $pdo->prepare("SELECT id FROM USER WHERE email = :mail");
$admin_id_stmt->bindParam(':mail', $_SESSION['mail'], PDO::PARAM_STR);
$admin_id_stmt->execute();
$admin_id = $admin_id_stmt->fetchColumn();

if ($username === false || $is_admin === false) {
    echo "Error: Unable to fetch data for ID $id";
    exit();
}
?>

<link rel="stylesheet" href="../css/backoffice_ban_user.css">
<link rel="stylesheet" href="../css/role_selector.css">

<div class="right_panel">
    <div class="captcha_form" id="captcha_form">
        <h1 id="loginhigh" class="highlighted-text">Bannissement</h1>
        <hr id="loginhr">
        <form method="post" class="login" action="Processus/ban.php">
            <div class="entries">
                <div class="entries">
                    <p class="role_selector_text">Utilisateur à bannir</p>
                    <div class="readonly-field"><a id="user" href="/Sources/view_profile.php?id=<?php echo $id ?>"><?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?></a></div>
                </div>
                <div class="space"></div>

                <div class="entries">
                    <p class="role_selector_text">Administrateur</p>
                    <div class="readonly-field"><a id="user" href="/Sources/view_profile.php?id=<?php echo $admin_id ?>"><?= htmlspecialchars($admin, ENT_QUOTES, 'UTF-8') ?></a></div>
                </div>
                <div class="space"></div>

                <div class="entries">
                    <p class="role_selector_text">Raison du bannissement</p>
                    <textarea name="ban_reason" id="ban_reason" maxlength="800" required placeholder="Indiquez la raison du bannissement"></textarea>
                </div>
                <div class="space"></div>

                <div class="entries">
                    <p class="role_selector_text">Date d'expiration du bannissement</p>
                    <input name="ban_expiration" id="ban_expiration" type="date" required>
                </div>
            </div>
            <input type="hidden" name="id" value="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>">
            <button class="custom-button" id="add_btn" type="submit">Bannir</button>
        </form>
        <button class="custom-button" onclick="window.location.href='users.php'" id="cancel_btn">Annuler</button>
    </div>
</div>
<div class="space">
    &nbsp;
</div>
</div>

<?php
include_once 'footer.php';
?>