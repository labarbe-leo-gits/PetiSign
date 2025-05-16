<?php
session_start();
require_once '../../FPDF/fpdf.php';
include_once '/Sources/database/database.php';
include_once 'security.php';

if($id_benevole != 0){

    $activity_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    $user_stmt = $pdo->prepare("SELECT username FROM USER WHERE email = :mail");
    $user_stmt->bindParam(':mail', $_SESSION['mail'], PDO::PARAM_STR);
    $user_stmt->execute();
    $user = $user_stmt->fetchColumn();

    $date = date("d/m/Y à H:i") . ' UTC ';

    class StyledPDF extends FPDF {
        function Footer() {
            $this->SetY(-15);
            $this->SetFont('Arial','I',9);
            $this->SetTextColor(120, 120, 120);
            $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
        }
    }

    $pdf = new StyledPDF();
    $pdf->AliasNbPages();
    $pdf->AddPage();

    $pdf->SetFont('Arial', 'B', 20);
    $pdf->SetTextColor(40, 40, 40);
    $pdf->Cell(0, 60, '', 0, 1);
    $pdf->Cell(0, 10, utf8_decode("Liste de présence"), 0, 1, 'C');

    $pdf->SetFont('Arial', '', 14);
    $pdf->Ln(20);
    $pdf->Cell(0, 10, utf8_decode("Fichier généré par : ") . utf8_decode($user), 0, 1, 'C');
    $pdf->Cell(0, 10, utf8_decode("Le : ") . utf8_decode($date), 0, 1, 'C');

    $event_name_stmt = $pdo->prepare("SELECT name FROM TEAM_ACTIVITY WHERE id = :id_activity");
    $event_name_stmt->bindParam(':id_activity', $activity_id, PDO::PARAM_INT);
    $event_name_stmt->execute();
    $event_name = $event_name_stmt->fetchColumn();

    $pdf->Ln(30);
    $pdf->SetFont('Arial', 'I', 12);
    $pdf->SetTextColor(100, 100, 100);
    $pdf->MultiCell(0, 10, utf8_decode("Ce document contient la liste des membres présents sous forme de feuille d'appel.\nActivité / évènement : ". $event_name ."."), 0, 'C');

    $pdf->AddPage();

    $header_bg = [255, 217, 142];
    $row_even = [255, 255, 255];
    $row_odd = [245, 246, 226];
    $text_color = [26, 65, 78];

    $col_widths = [115, 75];
    $headers = ['Utilisateur', 'Présence'];

    $query = "SELECT id_user FROM ACTIVITY_INSCRIPTION WHERE id_activity = :id_activity";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id_activity', $activity_id, PDO::PARAM_INT);
    $stmt->execute();
    $user_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $pdf->SetFillColor(...$header_bg);
    $pdf->SetTextColor(0);
    $pdf->SetFont('Arial','B',11);
    foreach ($headers as $i => $col) {
        $pdf->Cell($col_widths[$i], 10, utf8_decode($col), 1, 0, 'C', true);
    }
    $pdf->Ln();

    $pdf->SetFont('Arial','',10);
    $pdf->SetTextColor(...$text_color);
    $fill = false;

    foreach ($user_ids as $user_id) {
        $stmt = $pdo->prepare("SELECT username FROM USER WHERE id = :id");
        $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $username = $stmt->fetchColumn();

        $fill_color = $fill ? $row_odd : $row_even;
        $pdf->SetFillColor(...$fill_color);

        $pdf->Cell($col_widths[0], 10, utf8_decode($username), 1, 0, 'C', true);

        $pdf->Cell($col_widths[1], 10, '', 1, 0, 'C', true);
        $x = $pdf->GetX() - ($col_widths[1]/2) - 3;
        $y = $pdf->GetY() + 3; 
        $pdf->Rect($x, $y, 4, 4);
        $pdf->Ln();

        $fill = !$fill;
    }

    $pdf->Output('D', 'liste-de-presence.pdf');
    exit;
} else {
    header('Location: /Sources/error.php?code=403');
    exit();
}
?>
