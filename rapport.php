<?php
require_once 'config/config.php';
require('fpdf/fpdf.php');

class PDF extends FPDF
{
    function Header()
    {
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, 'Rapport de Paiement', 0, 1, 'C');
    }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 12);

$pdf->Cell(50, 10, 'Nom complet', 1);
$pdf->Cell(35, 10, 'Telephone', 1);
$pdf->Cell(25, 10, 'Mois', 1);
$pdf->Cell(30, 10, 'Montant', 1);
$pdf->Cell(40, 10, 'Date', 1);
$pdf->Ln();

$stmt = $pdo->query("SELECT e.nom_complet, e.telephone, p.mois, p.montant, p.date_paiement
                     FROM paiements p
                     JOIN employees e ON p.employee_id = e.id");
if (!$stmt) {
    die("Erreur lors de la récupération des paiements : " . $pdo->errorInfo()[2]);
}

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $pdf->Cell(50, 10, $row['nom_complet'], 1);
    $pdf->Cell(35, 10, $row['telephone'], 1);
    $pdf->Cell(25, 10, $row['mois'], 1);
    $pdf->Cell(30, 10, $row['montant'], 1);
    $pdf->Cell(40, 10, $row['date_paiement'], 1);
    $pdf->Ln();
}

$pdf->Output();
