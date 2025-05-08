<?php
session_start();

require_once '/../../FPDF/fpdf.php';
require_once '/../../database/database.php';
include_once '/security.php';

if (!isset($_SESSION['mail'])) {
    echo "Accès non autorisé.";
    exit;
}

$email = $_SESSION['mail'];

if (!isset($pdo)) {
    echo "Erreur de connexion à la base de données.";
    exit;
}

$stmt = $pdo->prepare("SELECT id, username, email, gender, birthdate, description FROM USER WHERE email = :email");
$stmt->bindParam(':email', $email);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "Utilisateur introuvable.";
    exit;
}

$user_id = $user['id'];

$stmt_pet = $pdo->prepare("SELECT COUNT(*) FROM PETITION WHERE user = :id");
$stmt_pet->bindParam(':id', $user_id);
$stmt_pet->execute();
$nb_petitions = $stmt_pet->fetchColumn() ?: 0;

$stmt_sig = $pdo->prepare("SELECT COUNT(*) FROM SIGNATURE WHERE id_user = :id");
$stmt_sig->bindParam(':id', $user_id);
$stmt_sig->execute();
$nb_signatures = $stmt_sig->fetchColumn() ?: 0;

class StyledPDF extends FPDF {
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 9);
        $this->SetTextColor(120, 120, 120);
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}

$pdf = new StyledPDF();
$pdf->AliasNbPages();
$pdf->AddPage();

$pdf->SetFont('Arial', 'B', 18);
$pdf->SetTextColor(40, 40, 40);
$pdf->Cell(0, 20, utf8_decode("Téléchargement de mes données personnelles"), 0, 1, 'C');

$pdf->SetFont('Arial', '', 12);
$pdf->Ln(10);
$pdf->MultiCell(0, 10, utf8_decode("Voici les informations personnelles associées à votre compte :"), 0);

$pdf->Ln(5);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(60, 10, utf8_decode("Nom d'utilisateur :"), 0);
$pdf->Cell(0, 10, utf8_decode($user['username']), 0, 1);

$pdf->Cell(60, 10, utf8_decode("Adresse email :"), 0);
$pdf->Cell(0, 10, utf8_decode($user['email']), 0, 1);

$pdf->Cell(60, 10, utf8_decode("Genre :"), 0);
$pdf->Cell(0, 10, utf8_decode($user['gender']), 0, 1);

$pdf->Cell(60, 10, utf8_decode("Date de naissance :"), 0);
$pdf->Cell(0, 10, utf8_decode($user['birthdate']), 0, 1);

$pdf->Ln(5);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(60, 10, utf8_decode("Pétitions créées :"), 0);
$pdf->Cell(0, 10, $nb_petitions, 0, 1);

$pdf->Cell(60, 10, utf8_decode("Pétitions signées :"), 0);
$pdf->Cell(0, 10, $nb_signatures, 0, 1);

$pdf->Ln(10);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, utf8_decode("Description :"), 0, 1);

$pdf->SetFont('Arial', '', 11);
$pdf->MultiCell(0, 10, utf8_decode($user['description'] ?: 'Aucune description renseignée.'), 0);

$pdf->Ln(10);
$pdf->SetFont('Arial', 'I', 10);
$pdf->SetTextColor(120, 120, 120);
$pdf->MultiCell(0, 7, utf8_decode("Document généré automatiquement sur demande de l'utilisateur.\nAucune information n'est transmise à des tiers."), 0, 'C');

$pdf->Output('D', 'mes_donnees_personnelles.pdf');
exit;
?>