<?php
ob_start(); // catch any accidental output before FPDF headers
// fsl-emergency-preparedness.php
require_once __DIR__ . '/fpdf/fpdf.php';

class FSL_PDF extends FPDF {

    function Header() {
        $this->SetFillColor(0, 123, 191);
        $this->Rect(0, 0, 210, 18, 'F');
        $this->SetFont('Helvetica', 'B', 9);
        $this->SetTextColor(255, 255, 255);
        $this->SetXY(10, 5);
        $this->Cell(95, 8, 'SILENT SIGNAL', 0, 0, 'L');
        $this->Cell(95, 8, 'Filipino Sign Language (FSL) Emergency Resource', 0, 0, 'R');
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
$pdf->SetFont('Helvetica', 'B', 18);
$pdf->SetTextColor(0, 123, 191);
$pdf->Cell(0, 12, 'Basic First Aid in Filipino Sign Language (FSL)', 0, 1, 'C');
$pdf->Ln(4);

// ── Introduction ──
$pdf->SectionTitle('Introduction');
$pdf->BodyText('This guide provides basic first aid instructions alongside FSL signs for emergency communication. Always call for professional medical help (911 or your local emergency line) first when possible.');

// ── Calling for Help ──
$pdf->SectionTitle('Calling for Help');
$pdf->BulletItem('FSL sign for HELP: Open hand, thumb up, palm facing out, lift upward.');
$pdf->BulletItem('FSL sign for PAIN: Tap both index fingers together (or point to the location of pain).');
$pdf->BulletItem('FSL sign for DOCTOR: "D" handshape tapped on wrist (like checking pulse).');
$pdf->BulletItem('FSL sign for HOSPITAL: "H" handshape, draw a cross on upper arm.');
$pdf->BulletItem('Show the Silent Signal SOS screen to bystanders if you cannot speak.');

// ── Basic First Aid Steps ──
$pdf->SectionTitle('Basic First Aid Steps');

$pdf->SubHeading('Bleeding');
$pdf->BulletItem('Apply firm, steady pressure with a clean cloth or bandage.');
$pdf->BulletItem('Keep pressure for at least 10 minutes without lifting.');
$pdf->BulletItem('Elevate the injured limb above heart level if possible.');
$pdf->BulletItem('Do NOT remove a deeply embedded object - stabilize it.');
$pdf->Ln(2);

$pdf->SubHeading('Burns');
$pdf->BulletItem('Cool the burn under cool (not cold) running water for at least 10 minutes.');
$pdf->BulletItem('Do not apply ice, butter, or toothpaste.');
$pdf->BulletItem('Cover with a clean, non-fluffy dressing.');
$pdf->Ln(2);

$pdf->SubHeading('Choking (Adult/Child)');
$pdf->BulletItem('Ask the person "Are you choking?" - they may signal YES or point to throat.');
$pdf->BulletItem('Encourage forceful coughing if they can cough.');
$pdf->BulletItem('Give 5 back blows between the shoulder blades.');
$pdf->BulletItem('Give 5 abdominal thrusts (Heimlich maneuver).');
$pdf->BulletItem('Repeat until the object is cleared or help arrives.');
$pdf->Ln(2);

$pdf->SubHeading('Unconscious Person');
$pdf->BulletItem('Check for response: tap shoulders, look for chest movement.');
$pdf->BulletItem('Call 911 or direct someone else to call.');
$pdf->BulletItem('If not breathing normally, begin CPR: 30 chest compressions + 2 rescue breaths.');
$pdf->BulletItem('Continue until emergency services arrive.');

// ── FSL Medical Signs ──
$pdf->SectionTitle('FSL Medical Signs');
$pdf->BulletItem('PAIN - Both index fingers tapped together.');
$pdf->BulletItem('BREATHE - Hands on chest, move outward (like breathing).');
$pdf->BulletItem('HEARTBEAT - Fist on chest, tap twice.');
$pdf->BulletItem('BLOOD - Index finger on lips, move downward (red).');
$pdf->BulletItem('DIZZY - Index finger pointing at temple, rotate in circle.');
$pdf->BulletItem('ALLERGIC - Claw hand on arm, move upward (like hives).');

ob_end_clean();
$pdf->Output('D', 'fsl-first-aid.pdf');
exit();