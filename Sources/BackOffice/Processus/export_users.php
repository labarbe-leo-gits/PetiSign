<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../../FPDF/fpdf.php';
include_once __DIR__ . '/../../database/database.php';
include_once 'security.php';

if ($is_admin != 0) {
    $user_stmt = $pdo->prepare("SELECT username FROM USER WHERE email = :mail");
    $user_stmt->bindParam(':mail', $_SESSION['mail'], PDO::PARAM_STR);
    $user_stmt->execute();
    $user = $user_stmt->fetchColumn();

    $date = date("d/m/Y à H:i") . ' UTC ';

    $query = "
        SELECT 
            USER.id,
            USER.username AS nom,
            USER.email,
            USER.is_admin,
            USER.is_benevole,
            IF(BAN.id_user IS NOT NULL, 'Oui', 'Non') AS bloque,
            (SELECT COUNT(*) FROM PETITION WHERE PETITION.user = USER.id) AS nb_petitions,
            (SELECT COUNT(*) FROM SIGNATURE WHERE SIGNATURE.id_user = USER.id) AS nb_signatures
        FROM USER
        LEFT JOIN BAN ON USER.id = BAN.id_user
    ";

    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
    $pdf->Cell(0, 10, "Export des Utilisateurs", 0, 1, 'C');

    $pdf->SetFont('Arial', '', 14);
    $pdf->Ln(20);
    $pdf->Cell(0, 10, "Fichier genere par : " . $user, 0, 1, 'C');
    $pdf->Cell(0, 10, "Le : " . $date, 0, 1, 'C');

    $pdf->Ln(30);
    $pdf->SetFont('Arial', 'I', 12);
    $pdf->SetTextColor(100, 100, 100);
    $pdf->MultiCell(0, 10, "Ce document contient la liste des utilisateurs actifs du systeme.\nNe pas partager sans autorisation.", 0, 'C');

    $pdf->AddPage();
    $header_bg = [255, 217, 142];
    $row_even = [255, 255, 255];
    $row_odd  = [245, 246, 226];
    $text_color = [26, 65, 78];

    $col_widths = [12, 30, 60, 30, 18, 20, 20];
    $headers = ['ID', 'Nom', 'E-mail', 'Role', 'BAN', 'Petitions', 'Signatures'];

    $pdf->SetFillColor(...$header_bg);
    $pdf->SetTextColor(0);
    $pdf->SetFont('Arial','B',11);
    foreach ($headers as $i => $col) {
        $pdf->Cell($col_widths[$i], 10, $col, 1, 0, 'C', true);
    }
    $pdf->Ln();

    $pdf->SetFont('Arial','',10);
    $pdf->SetTextColor(...$text_color);
    $fill = false;

    foreach ($users as $u) {

        if ($u['is_admin'] == 1 && $u['is_benevole'] == 1) {
            $role = 'Admin + Benevole';
        } elseif ($u['is_admin'] == 1) {
            $role = 'Admin';
        } elseif ($u['is_benevole'] == 1) {
            $role = 'Benevole';
        } else {
            $role = 'Utilisateur';
        }

        $pdf->SetFillColor(...($fill ? $row_odd : $row_even));
        $pdf->Cell($col_widths[0], 10, $u['id'], 1, 0, 'C', true);
        $pdf->Cell($col_widths[1], 10, $u['nom'], 1, 0, 'L', true);
        $pdf->Cell($col_widths[2], 10, $u['email'], 1, 0, 'L', true);
        $pdf->Cell($col_widths[3], 10, $role, 1, 0, 'C', true);
        $pdf->Cell($col_widths[4], 10, $u['bloque'], 1, 0, 'C', true);
        $pdf->Cell($col_widths[5], 10, $u['nb_petitions'], 1, 0, 'C', true);
        $pdf->Cell($col_widths[6], 10, $u['nb_signatures'], 1, 1, 'C', true);
        $fill = !$fill;
    }

    $pdf->Output('D', 'export_utilisateurs.pdf');
    exit;
} else {
    echo "Acces interdit.";
}
?>
