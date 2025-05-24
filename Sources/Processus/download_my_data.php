<?php

session_start();

require_once '../FPDF/fpdf.php';
require_once '../database/database.php';

if (!isset($_SESSION['mail'])) {
    echo "Accès non autorisé.";
    exit;
}

$email = $_SESSION['mail'];

$stmt = $pdo->prepare("SELECT id, username, email, gender, birthdate, description, newsletter FROM USER WHERE email = :email");
$stmt->bindParam(':email', $email);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "Utilisateur introuvable.";
    exit;
}

$user_id = $user['id'];
$formatted_birthdate = date('d/m/Y', strtotime($user['birthdate']));

$stmt_pet = $pdo->prepare("SELECT COUNT(*) FROM PETITION WHERE user = :id");
$stmt_pet->bindParam(':id', $user_id);
$stmt_pet->execute();
$nb_petitions = $stmt_pet->fetchColumn() ?: 0;

$all_user_petitions = $pdo->prepare("SELECT * FROM PETITION WHERE user = :id");
$all_user_petitions->bindParam(':id', $user_id);
$all_user_petitions->execute();
$all_user_petitions = $all_user_petitions->fetchAll(PDO::FETCH_ASSOC);

$stmt_sig = $pdo->prepare("SELECT COUNT(*) FROM SIGNATURE WHERE id_user = :id");
$stmt_sig->bindParam(':id', $user_id);
$stmt_sig->execute();
$nb_signatures = $stmt_sig->fetchColumn() ?: 0;

$description = html_entity_decode($user['description'] ?? 'Aucune description renseignée.');

class StyledPDF extends FPDF {
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial','I',9);
        $this->SetTextColor(120, 120, 120);
        $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
    }
    
    function SectionHeader($title, $primary_color = [255, 217, 142], $height = 10) {
        $this->SetFillColor(...$primary_color);
        $this->SetTextColor(26, 65, 78);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, $height, utf8_decode($title), 1, 1, 'L', true);
        $this->SetTextColor(40, 40, 40);
        $this->SetFont('Arial', '', 11);
        $this->Ln(2);
    }
    
    function InfoRow($label, $value, $width_label = 60, $height = 8) {
        $this->SetFont('Arial', 'B', 11);
        $this->Cell($width_label, $height, utf8_decode($label), 0);
        $this->SetFont('Arial', '', 11);
        $this->Cell(0, $height, utf8_decode($value), 0, 1);
    }
}

$check_roles = $pdo->prepare("SELECT is_admin, is_benevole FROM USER WHERE id = :id");
$check_roles->bindParam(':id', $user_id);
$check_roles->execute();
$roles = $check_roles->fetch(PDO::FETCH_ASSOC);

$pdf = new StyledPDF();
$pdf->AliasNbPages();
$pdf->AddPage();

$pdf->SetFont('Arial', 'B', 18);
$pdf->SetTextColor(255, 140, 0);
$pdf->Cell(0, 20, utf8_decode("Téléchargement de mes données personnelles"), 0, 1, 'C');
$pdf->SetTextColor(40, 40, 40);

$pdf->SetFont('Arial', 'I', 11);
$pdf->SetTextColor(100, 100, 100);
$pdf->Cell(0, 10, utf8_decode("Généré le " . date("d/m/Y à H:i")) . " UTC", 0, 1, 'C');
$pdf->Ln(5);

$pdf->SectionHeader("Informations personnelles");
$pdf->InfoRow("Nom d'utilisateur :", $user['username']);
$pdf->InfoRow("Adresse email :", $user['email']);
$pdf->InfoRow("Genre :", $user['gender']);
$pdf->InfoRow("Date de naissance :", $formatted_birthdate);
$pdf->InfoRow("Newsletter :", $user['newsletter'] == 1 ? "Oui" : "Non");
$pdf->Ln(5);
$pdf->InfoRow("Pétitions créées :", $nb_petitions);
$pdf->InfoRow("Pétitions signées :", $nb_signatures);
$pdf->Ln(5);
$pdf->InfoRow("Rôle(s) :", 
    ($roles['is_admin'] == 1 && $roles['is_benevole'] == 1) ? "Administrateur et Bénévole" : 
    ($roles['is_admin'] == 1 ? "Administrateur" : 
    ($roles['is_benevole'] == 1 ? "Bénévole" : "Utilisateur"))
);
$pdf->Ln(5);

$pdf->SectionHeader("Description du profil");
$pdf->MultiCell(0, 8, utf8_decode($description), 0);
$pdf->Ln(8);

if (count($all_user_petitions) > 0) {
    $pdf->SectionHeader("Pétitions créées");
    
    $header_bg = [255, 217, 142];
    $row_even = [255, 255, 255];
    $row_odd = [245, 246, 226];
    $text_color = [26, 65, 78];

    $col_widths = [30, 90, 30, 40];
    $headers = ['ID', 'Titre', 'Date', 'Signatures'];
    
    $pdf->SetFillColor(...$header_bg);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetTextColor(0);
    foreach ($headers as $i => $col) {
        $pdf->Cell($col_widths[$i], 10, utf8_decode($col), 1, 0, 'C', true);
    }
    $pdf->Ln();

    $pdf->SetFont('Arial', '', 10);
    $pdf->SetTextColor(...$text_color);
    $fill = false;
    
    foreach ($all_user_petitions as $petition) {
        $fill_color = $fill ? $row_odd : $row_even;
        $pdf->SetFillColor(...$fill_color);

        $stmt_sign = $pdo->prepare("SELECT COUNT(*) FROM SIGNATURE WHERE id_petition = :id");
        $stmt_sign->bindParam(':id', $petition['id']);
        $stmt_sign->execute();
        $petition_signatures = $stmt_sign->fetchColumn() ?: 0;
        
        $petition_date = date('d/m/Y', strtotime($petition['date']));
        
        $pdf->Cell($col_widths[0], 10, $petition['id'], 1, 0, 'C', true);
        $pdf->Cell($col_widths[1], 10, utf8_decode(substr($petition['title'], 0, 40)), 1, 0, 'C', true);
        $pdf->Cell($col_widths[2], 10, $petition_date, 1, 0, 'C', true);
        $pdf->Cell($col_widths[3], 10, $petition_signatures, 1, 0, 'C', true);
        $pdf->Ln();
        
        $fill = !$fill;
    }
} else {
    $pdf->SetFont('Arial', 'I', 11);
    $pdf->Cell(0, 10, utf8_decode("Vous n'avez créé aucune pétition."), 0, 1);
}
$pdf->Ln(10);

$signed_petitions = $pdo->prepare("
    SELECT p.id, p.title, p.date, s.date as signature_date 
    FROM PETITION p
    JOIN SIGNATURE s ON p.id = s.id_petition
    WHERE s.id_user = :id
    ORDER BY s.date DESC
");
$signed_petitions->bindParam(':id', $user_id);
$signed_petitions->execute();
$signed_petitions = $signed_petitions->fetchAll(PDO::FETCH_ASSOC);

if (count($signed_petitions) > 0) {
    $pdf->SectionHeader("Pétitions signées");
    
    $pdf->SetFillColor(...$header_bg);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetTextColor(0);
    
    $col_widths = [30, 90, 40, 30];
    $headers = ['ID', 'Titre', 'Date pétition', 'Date signature'];
    
    foreach ($headers as $i => $col) {
        $pdf->Cell($col_widths[$i], 10, utf8_decode($col), 1, 0, 'C', true);
    }
    $pdf->Ln();
    
    $pdf->SetFont('Arial', '', 10);
    $pdf->SetTextColor(...$text_color);
    $fill = false;
    
    foreach ($signed_petitions as $petition) {
        $fill_color = $fill ? $row_odd : $row_even;
        $pdf->SetFillColor(...$fill_color);

        $petition_date = date('d/m/Y', strtotime($petition['date']));
        $signature_date = date('d/m/Y', strtotime($petition['signature_date']));
        
        $pdf->Cell($col_widths[0], 10, $petition['id'], 1, 0, 'C', true);
        $pdf->Cell($col_widths[1], 10, utf8_decode(substr($petition['title'], 0, 40)), 1, 0, 'C', true);
        $pdf->Cell($col_widths[2], 10, $petition_date, 1, 0, 'C', true);
        $pdf->Cell($col_widths[3], 10, $signature_date, 1, 0, 'C', true);
        $pdf->Ln();
        
        $fill = !$fill;
    }
}

$pdf->Ln(10);

if($roles['is_benevole'] == 1) {
    
    $pdf->SectionHeader("Équipes de bénévoles");

    $pdf->SetFillColor(...$header_bg);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetTextColor(0);

    $col_widths = [30, 160];

    $all_teams_of_user = $pdo->prepare("
        SELECT t.id, t.name 
        FROM TEAM t
        JOIN TEAM_MEMBER tm ON t.id = tm.id_team
        WHERE tm.id_user = :id
        ORDER BY t.name
    ");
    $all_teams_of_user->bindParam(':id', $user_id);
    $all_teams_of_user->execute();
    $all_teams_of_user = $all_teams_of_user->fetchAll(PDO::FETCH_ASSOC);

    if(count($all_teams_of_user) > 0) {
        $headers = ['ID', 'Nom de l\'équipe'];
        foreach ($headers as $i => $col) {
            $pdf->Cell($col_widths[$i], 10, utf8_decode($col), 1, 0, 'C', true);
        }
        $pdf->Ln();
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetTextColor(...$text_color);
        $fill = false;
        foreach ($all_teams_of_user as $team) {
            $fill_color = $fill ? $row_odd : $row_even;
            $pdf->SetFillColor(...$fill_color);

            $pdf->Cell($col_widths[0], 10, $team['id'], 1, 0, 'C', true);
            $pdf->Cell($col_widths[1], 10, utf8_decode(substr($team['name'], 0, 40)), 1, 0, 'C', true);
            $pdf->Ln();
            
            $fill = !$fill;
        }
        $pdf->Ln(5);
    } else {
        $pdf->SetFont('Arial', 'I', 11);
        $pdf->Cell(0, 10, utf8_decode("Vous n'êtes membre d'aucune équipe."), 0, 1);
    }
}

$pdf->Ln(10);
$pdf->SetFont('Arial', 'I', 10);
$pdf->SetTextColor(120, 120, 120);
$pdf->MultiCell(0, 7, utf8_decode("Document généré automatiquement sur demande de l'utilisateur.\nAucune information n'est transmise à des tiers."), 0, 'C');

$pdf->Output('D', 'mes_donnees_personnelles.pdf');
exit;
?>