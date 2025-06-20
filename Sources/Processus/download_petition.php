<?php
session_start();

require_once '../FPDF/fpdf.php';
require_once '../database/database.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "ID de pétition manquant.";
    echo "<script>window.location.href = '../discover.php';</script>";
    exit;
}

$pet_id = $_GET['id'];

$stmt = $pdo->prepare("SELECT p.*, u.username, u.email FROM PETITION p JOIN USER u ON p.user = u.id WHERE p.id = :id");
$stmt->bindParam(':id', $pet_id, PDO::PARAM_INT);
$stmt->execute();
$petition = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$petition) {
    echo "Pétition introuvable.";
    echo "<script>window.location.href = '../discover.php';</script>";
    exit;
}

class ModernPetitionPDF extends FPDF {
    private $petition_data;
    
    function __construct($petition) {
        parent::__construct();
        $this->petition_data = $petition;
    }
    
    function Header() {
    }
    
function AddPetitionContent() {
    $this->SetFillColor(255, 217, 142); 
    $this->Rect(0, 0, 210, 297, 'F');

    $headerY = 0;
    $headerH = 120; 
    $imageDisplayed = false;

    $bgPath = '../../Resources/img/petition_selection/' . $this->petition_data['image_id'] . '.jpg';
    if (!empty($this->petition_data['image_id']) && file_exists($bgPath)) {
        $headerW = 210;
        $headerH = 120;
        list($imgW, $imgH) = getimagesize($bgPath);
        $imgWmm = $imgW * 0.264583;
        $imgHmm = $imgH * 0.264583;
        $ratio = max($headerW / $imgWmm, $headerH / $imgHmm);
        $w = $imgWmm * $ratio;
        $h = $imgHmm * $ratio;
        $x = ($headerW - $w) / 2;
        $y = ($headerH - $h) / 2;
        $this->Image($bgPath, $x, $y, $w, $h);
        $headerH = $h + $y;
        $imageDisplayed = true;
    } else {
        $this->SetFillColor(200, 200, 200);
        $this->Rect(0, 0, 210, $headerH, 'F');
    }

    $this->SetFillColor(255, 255, 255);
    $this->Rect(13, 0, 32, 42, 'F');
    $logoPath = '../../Resources/img/logo/logo min SF.png';
    if (file_exists($logoPath)) {
        $this->Image($logoPath, 17, 5, 25);
    }

    $textMarginTop = 10;
    $titleY = $headerH + $textMarginTop;
    $this->SetY($titleY);
    $this->SetLeftMargin(15);
    $this->SetRightMargin(15);

    $this->SetFont('Arial', 'B', 35);
    $this->SetTextColor(255, 255, 255);
    $this->SetX(15);
    $this->MultiCell(180, 14, utf8_decode(strtoupper($this->petition_data['title'])), 0, 'L');


    $afterTitleY = $this->GetY();
    $this->SetY($afterTitleY + 2);
    $this->SetFont('Arial', '', 12);
    $this->SetTextColor(26, 65, 78);

    $description = html_entity_decode($this->petition_data['description']);
    $description = strip_tags($description);

    $this->MultiCell(0, 6, utf8_decode($description), 0, 'J');

    $this->SetY(250);
    $this->SetFont('Arial', 'B', 24);
    $this->SetTextColor(26, 65, 78);

    $this->SetY(265);
    $this->SetFont('Arial', 'B', 20);
    $this->Cell(120, 10, utf8_decode('SIGNE ICI'), 0, 0, 'R');
    $this->SetFont('Arial', 'B', 24);

    $qrPath = '../../Resources/qrcode/qr_code_petition_id' . $this->petition_data['id'] . '.png';
    $this->SetFillColor(255, 255, 255);
    $this->Rect(152, 250, 41, 60, 'F');

    if (file_exists($qrPath)) {
        $this->Image($qrPath, 155, 253, 35);
        $this->SetXY(155, 290);

        $this->SetFont('Arial', 'B', 12);
        $this->SetTextColor(26, 65, 78);
        $this->Cell(35, 5, utf8_decode('Pétisign'), 0, 0, 'C');
    }
    $this->SetDrawColor(26, 65, 78);
    $this->SetLineWidth(2);

    $this->Line(15, 260, 140, 260);
    $arrowSize = 8;
    $this->Line(140, 260, 132, 256);
    $this->Line(140, 260, 132, 264);
}
}

$pdf = new ModernPetitionPDF($petition);
$pdf->SetAutoPageBreak(false);
$pdf->AddPage();
$pdf->AddPetitionContent();

$pdf->Output('D', 'petition_moderne_'.$pet_id.'.pdf');
exit;
?>