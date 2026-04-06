<?php
// fsl-evacuation-instructions.php
// Generates FSL Evacuation Instructions PDF using FPDF

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

    function NumberedItem($num, $text) {
        $this->SetFont('Helvetica', '', 10);
        $this->SetTextColor(50, 50, 50);
        $this->SetX(15);
        $this->Cell(8, 6, $num . '.', 0, 0, 'L');
        $this->MultiCell(172, 6, $text, 0, 'L');
    }
}

$pdf = new FSL_PDF();
$pdf->SetMargins(10, 25, 10);
$pdf->SetAutoPageBreak(true, 20);
$pdf->AddPage();

// ── Title ──
$pdf->SetFont('Helvetica', 'B', 20);
$pdf->SetTextColor(0, 123, 191);
$pdf->Cell(0, 12, 'FSL Evacuation Instructions', 0, 1, 'C');
$pdf->Ln(4);

// ── When to Evacuate ──
$pdf->SectionTitle('When to Evacuate');
$pdf->BodyText('Evacuate immediately when you see or receive any of the following:');
$pdf->BulletItem('Official evacuation order from barangay, city, or government authorities.');
$pdf->BulletItem('Rising flood waters reaching knee height inside your home.');
$pdf->BulletItem('Earthquake strong enough to crack walls or cause landslides.');
$pdf->BulletItem('Typhoon signal 3 or higher in your area.');
$pdf->BulletItem('Tsunami warning (move to higher ground IMMEDIATELY - do not wait).');

// ── Step-by-Step ──
$pdf->SectionTitle('Step-by-Step Evacuation in FSL');
$pdf->NumberedItem(1, 'Alert all family members. Use the FSL sign for EVACUATE (point outward, wave forward).');
$pdf->NumberedItem(2, 'Grab your Go-Bag. One designated bag per person if possible.');
$pdf->NumberedItem(3, 'Turn off electricity at the main breaker and close the gas valve.');
$pdf->NumberedItem(4, 'Lock your home and leave a note on the door with your destination.');
$pdf->NumberedItem(5, 'Help PWD members first. Deaf, mobility-impaired, or elderly members need extra support.');
$pdf->NumberedItem(6, 'Follow the safest route - avoid flooded roads, downed power lines, and unstable bridges.');
$pdf->NumberedItem(7, 'Go directly to the designated evacuation center. Do not stop unnecessarily.');

// ── At the Evacuation Center ──
$pdf->SectionTitle('At the Evacuation Center');
$pdf->BulletItem('Inform the registration desk that there is a Deaf or hard-of-hearing family member.');
$pdf->BulletItem('Request an FSL interpreter if available.');
$pdf->BulletItem('Keep the Silent Signal app active to receive disaster alerts.');
$pdf->BulletItem('Signal SAFE status on the app so your family dashboard is updated.');
$pdf->BulletItem('Do not leave the evacuation center without informing the center staff.');

// ── FSL Signs ──
$pdf->SectionTitle('FSL Signs for Evacuation');
$pdf->BulletItem('GO / MOVE - Point index finger forward, flick wrist.');
$pdf->BulletItem('FLOOD - Both hands flat, wave upward like water rising.');
$pdf->BulletItem('EARTHQUAKE - Both fists shake horizontally.');
$pdf->BulletItem('TYPHOON - Circle index finger overhead (like wind).');
$pdf->BulletItem('SHELTER / ROOF - Form a triangle overhead with both hands.');
$pdf->BulletItem('REGISTER / SIGN IN - Mime writing on palm.');

// ── After You Are Safe ──
$pdf->SectionTitle('After You Are Safe');
$pdf->BulletItem('Open the Silent Signal app and update your status to SAFE.');
$pdf->BulletItem('Contact family members through the app Communication Hub.');
$pdf->BulletItem('Stay at the evacuation center until authorities declare it safe to return.');
$pdf->BulletItem('Do not re-enter flood- or earthquake-damaged structures.');

$pdf->Output('D', 'fsl-evacuation-instructions.pdf');