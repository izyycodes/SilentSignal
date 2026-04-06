<?php
// fsl-emergency-preparedness.php
// Generates FSL Emergency Preparedness Guide PDF using FPDF

require_once __DIR__ . '/fpdf/fpdf.php';

class FSL_PDF extends FPDF {

    function Header() {
        // Header bar
        $this->SetFillColor(0, 123, 191);
        $this->Rect(0, 0, 210, 18, 'F');
        $this->SetFont('Helvetica', 'B', 9);
        $this->SetTextColor(255, 255, 255);
        $this->SetXY(10, 5);
        $this->Cell(95, 8, 'SILENT SIGNAL', 0, 0, 'L');
        $this->Cell(95, 8, 'Filipino Sign Language (FSL) Emergency Resource', 0, 0, 'R');
        // Divider line
        $this->SetDrawColor(0, 123, 191);
        $this->SetLineWidth(0.8);
        $this->Line(10, 20, 200, 20);
        $this->Ln(8);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Helvetica', 'I', 8);
        $this->SetTextColor(150, 150, 150);
        $this->Cell(0, 10, 'Silent Signal — Emergency Resource for Persons with Disabilities (PWD) and Families | silentsignal.helioho.st', 0, 0, 'C');
    }

    function SectionTitle($text) {
        $this->Ln(4);
        $this->SetFont('Helvetica', 'B', 13);
        $this->SetTextColor(0, 123, 191);
        $this->Cell(0, 8, $text, 0, 1, 'L');
        $this->SetDrawColor(0, 123, 191);
        $this->SetLineWidth(0.4);
        $this->Line(10, $this->GetY(), 200, $this->GetY());
        $this->Ln(3);
    }

    function BodyText($text) {
        $this->SetFont('Helvetica', '', 10);
        $this->SetTextColor(50, 50, 50);
        $this->MultiCell(0, 6, $text, 0, 'L');
        $this->Ln(2);
    }

    function BulletItem($text) {
        $this->SetFont('Helvetica', '', 10);
        $this->SetTextColor(50, 50, 50);
        $this->SetX(15);
        $this->Cell(5, 6, chr(149), 0, 0, 'L');
        $this->MultiCell(175, 6, $text, 0, 'L');
    }

    function NumberedItem($num, $text) {
        $this->SetFont('Helvetica', '', 10);
        $this->SetTextColor(50, 50, 50);
        $this->SetX(15);
        $this->Cell(8, 6, $num . '.', 0, 0, 'L');
        $this->MultiCell(172, 6, $text, 0, 'L');
    }

    function SubHeading($text) {
        $this->SetFont('Helvetica', 'B', 10);
        $this->SetTextColor(50, 50, 50);
        $this->Cell(0, 7, $text, 0, 1, 'L');
    }
}

$pdf = new FSL_PDF();
$pdf->SetMargins(10, 25, 10);
$pdf->SetAutoPageBreak(true, 20);
$pdf->AddPage();

// ── Title ──
$pdf->SetFont('Helvetica', 'B', 20);
$pdf->SetTextColor(0, 123, 191);
$pdf->Cell(0, 12, 'FSL Emergency Preparedness Guide', 0, 1, 'C');
$pdf->Ln(4);

// ── What is FSL ──
$pdf->SectionTitle('What is FSL?');
$pdf->BodyText('Filipino Sign Language (FSL) is the natural language of the Deaf community in the Philippines. It uses hand shapes, facial expressions, and body movements to communicate.');
$pdf->BodyText('FSL is distinct from Signed Exact Filipino (SEF) and uses its own grammar and vocabulary recognized by Republic Act 11106 (Filipino Sign Language Act of 2018).');

// ── Before a Disaster ──
$pdf->SectionTitle('Before a Disaster - Prepare');
$pdf->BulletItem('Learn key FSL emergency signs with your family and neighbors.');
$pdf->BulletItem('Prepare a Go-Bag: water, food (3-day supply), medications, copies of important IDs.');
$pdf->BulletItem('Identify two exit routes from your home.');
$pdf->BulletItem('Agree on a family meeting point that everyone (including Deaf members) knows.');
$pdf->BulletItem('Register with your local PDRRMO (Provincial Disaster Risk Reduction Management Office) as a PWD household.');
$pdf->BulletItem('Store the Silent Signal app on your phone and keep it charged.');
$pdf->BulletItem('Write emergency numbers on paper - do not rely only on your phone.');

// ── Key FSL Signs ──
$pdf->SectionTitle('Key FSL Signs to Learn');
$pdf->BulletItem('HELP - Open hand, thumb up, palm facing out, lift upward.');
$pdf->BulletItem('DANGER - Crossed arms in an "X" shape, then push outward.');
$pdf->BulletItem('FIRE - Wiggling fingers raised upward like flames.');
$pdf->BulletItem('EARTHQUAKE - Both fists, shake side to side.');
$pdf->BulletItem('EVACUATE - Point outward, then wave hand forward.');
$pdf->BulletItem('SAFE - Cross arms over chest, then open outward.');
$pdf->BulletItem('WATER - "W" handshape tapped on chin.');
$pdf->BulletItem('MEDICINE - Tap wrist with index and middle fingers (like checking pulse).');

// ── Family Communication Plan ──
$pdf->SectionTitle('Family Communication Plan');
$pdf->BulletItem('Assign a family communication coordinator (a hearing member who knows FSL).');
$pdf->BulletItem('Practice monthly drills using only FSL for 10 minutes.');
$pdf->BulletItem('Keep a printed FSL emergency flashcard set in your Go-Bag.');
$pdf->BulletItem('Use the Silent Signal app to send pre-written emergency messages.');

$pdf->Output('D', 'fsl-emergency-preparedness.pdf');