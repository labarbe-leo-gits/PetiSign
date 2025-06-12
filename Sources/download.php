<?php

include_once 'header.php';
include_once 'database/database.php';
include_once 'Processus/write_logs.php';

if(!isset($_SESSION['mail'])){
    $user = 'Anonyme';
}else{
    $stmt = $pdo->prepare("SELECT username FROM USER WHERE email = :mail");
    $stmt->bindParam(':mail', $_SESSION['mail']);
    $stmt->execute();
    $user = $stmt->fetchColumn();
}

$user_ip = $_SERVER['REMOTE_ADDR'];

write_logs('logs/log.txt', 'D0WNLD', $user, $user_ip, 'Visite de la page "Téléchargement"');

?>

<link rel="stylesheet" href="css/download.css">

<div class="download-page">
    <h1 class="page_title">Télécharger l'application mobile</h1>
    
    <div class="container">        
        <div class="button-group">
            <button type="button" class="download-btn primary"  onclick="downloadAPK()">
                <img src="/Resources/img/ui_icons/android.png" alt="Android">
                Télécharger l'APK
            </button>
            <button type="button" class="download-btn secondary" onclick="show_popup_install()">
                Procédure d'installation
            </button>
        </div>
        
        <div class="info-section">
            <h3>Fonctionnalités de l'application :</h3>
            <ul>
                <li>🔍 <strong>Navigation intuitive</strong> - Interface optimisée pour mobile</li>
                <li>📝 <strong>Création de pétitions</strong> - Directement depuis votre téléphone</li>
                <li>✍️ <strong>Signature rapide</strong> - En quelques touches seulement</li>
            </ul>
        </div>
    </div>

    <div class="filter">&nbsp;</div>

    <div class="install-popup popup">
        <div class="popup-content">
            <div class="close">
                <img onclick="hide_popup_install()" src="../Resources/img/ui_icons/plus.png" alt="Fermer la Popup">
            </div>
            <div class="popup-body">
                <h1>Procédure d'installation</h1>
                <div class="scrollable-content">
                    <div class="step">
                        <h2>Étape 1 : Téléchargement</h2>
                        <p>Cliquez sur le bouton "Télécharger l'APK" pour télécharger le fichier d'installation de l'application PétiSign.</p>
                    </div>
                    
                    <div class="step">
                        <h2>Étape 2 : Localiser le fichier</h2>
                        <p>Ouvrez votre gestionnaire de fichiers et naviguez vers le dossier <strong>Téléchargements</strong>. Trouvez le fichier <code>petisign.apk</code></p>
                    </div>
                    
                    <div class="step">
                        <h2>Étape 3 : Installation</h2>
                        <p>Tapez sur le fichier APK pour lancer l'installation. Suivez les instructions à l'écran :</p>
                        <ul>
                            <li>Tapez sur <strong>"Installer"</strong></li>
                            <li>Attendez que l'installation se termine</li>
                            <li>Tapez sur <strong>"Ouvrir"</strong> ou cherchez l'application dans votre menu</li>
                        </ul>
                    </div>
                    
                    <div class="step">
                        <h2>Étape 4 : Premier lancement</h2>
                        <p>Lancez l'application PétiSign. Vous pouvez maintenant :</p>
                        <ul>
                            <li>Vous connecter avec votre compte existant</li>
                            <li>Créer un nouveau compte</li>
                            <li>Explorer les pétitions disponibles</li>
                        </ul>
                    </div>
                    
                    <div class="support">
                        <h3>Besoin d'aide ?</h3>
                        <p>Si vous rencontrez des difficultés lors de l'installation, n'hésitez pas à nous contacter via notre page de support.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="js/download_apk.js"></script>
<script src="js/install_popup.js"></script>

<?php
include_once 'footer.php';
?>