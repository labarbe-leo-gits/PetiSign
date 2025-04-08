<?php
session_start();
require_once '../../FPDF/fpdf.php';
include_once '/Sources/database/database.php';
include_once 'security.php';

if($is_admin != 0){

    $admin_consent = $_POST['check2'];

    if (!isset($admin_consent) || $admin_consent != 'on') {
        header('Location: '.$_SERVER['HTTP_REFERER']);
        exit();
    }

    $user_stmt = $pdo->prepare("SELECT username FROM USER WHERE email = :mail");
    $user_stmt->bindParam(':mail', $_SESSION['mail'], PDO::PARAM_STR);
    $user_stmt->execute();
    $user = $user_stmt->fetchColumn();

    $date = date("d/m/Y à H:i") . ' UTC ';

    $logFile = '../../logs/log.txt';
    $logs = file_exists($logFile) ? array_filter(file($logFile)) : [];

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
    $pdf->Cell(0, 10, utf8_decode("Fichier de Logs"), 0, 1, 'C');

    $pdf->SetFont('Arial', '', 14);
    $pdf->Ln(20);
    $pdf->Cell(0, 10, utf8_decode("Fichier généré par : ") . utf8_decode($user), 0, 1, 'C');
    $pdf->Cell(0, 10, utf8_decode("Le : ") . utf8_decode($date), 0, 1, 'C');

    $pdf->Ln(30);
    $pdf->SetFont('Arial', 'I', 12);
    $pdf->SetTextColor(100, 100, 100);
    $pdf->MultiCell(0, 10, utf8_decode("Ce document contient les logs du système.\nNe pas partager sans autorisation."), 0, 'C');

    $pdf->AddPage();

    $header_bg = [255, 217, 142];
    $row_even = [255, 255, 255];
    $row_odd = [245, 246, 226];
    $text_color = [26, 65, 78];

    $col_widths = [40, 20, 40, 25, 60];
    $headers = ['Date/Heure', 'Type', 'Utilisateur', 'IP', 'Action'];

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

    foreach ($logs as $line) {
        $fill_color = $fill ? $row_odd : $row_even;
        $pdf->SetFillColor(...$fill_color);

        $parts = explode('-', $line);
        $parts = array_map('trim', $parts);
        while (count($parts) < 5) $parts[] = ''; 

        foreach ($parts as $i => $value) {
            $align = ($i === 3 ) ? 'C' : 'L';
            $pdf->Cell($col_widths[$i], 10, utf8_decode($value), 1, 0, 'C', true);
        }

        $pdf->Ln();
        $fill = !$fill;
    }

    $pdf->Output('D', 'logs.pdf');
    exit;
} else {
    header('Location: /Sources/error.php?code=403');
    exit();
}
?>
