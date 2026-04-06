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

    function SignRow($sign, $description) {
        // Sign name in bold blue, description in normal text
        $this->SetFont('Helvetica', 'B', 10);
        $this->SetTextColor(0, 123, 191);
        $this->SetX(15);
        $this->Cell(35, 7, $sign, 0, 0, 'L');
        $this->SetFont('Helvetica', '', 10);
        $this->SetTextColor(50, 50, 50);
        $this->MultiCell(155, 7, $description, 0, 'L');
    }
}

$pdf = new FSL_PDF();
$pdf->SetMargins(10, 25, 10);
$pdf->SetAutoPageBreak(true, 20);
$pdf->AddPage();

// ── Title ──
$pdf->SetFont('Helvetica', 'B', 18);
$pdf->SetTextColor(0, 123, 191);
$pdf->Cell(0, 12, 'Disaster Communication Signs in FSL', 0, 1, 'C');
$pdf->Ln(4);

// ── Introduction ──
$pdf->SectionTitle('About This Guide');
$pdf->BodyText('This guide contains common Filipino Sign Language (FSL) signs used during disasters and emergencies. Share this guide with family members, neighbors, and first responders so everyone can communicate effectively with Deaf and hard-of-hearing individuals.');

// ── Emergency Status Signs ──
$pdf->SectionTitle('Emergency Status Signs');
$pdf->SignRow('HELP', 'Open hand, thumb up, palm facing out, lift upward.');
$pdf->SignRow('SAFE', 'Cross arms over chest, then open outward.');
$pdf->SignRow('DANGER', 'Crossed arms in an "X" shape, then push outward.');
$pdf->SignRow('EMERGENCY', 'Fist with index finger raised, shake side to side rapidly.');
$pdf->SignRow('SOS', 'Alternate tapping both fists together three times.');
$pdf->SignRow('OKAY', 'Thumb and index finger form a circle, other fingers raised.');

// ── Disaster Type Signs ──
$pdf->SectionTitle('Disaster Type Signs');
$pdf->SignRow('FLOOD', 'Both hands flat, wave upward like water rising.');
$pdf->SignRow('FIRE', 'Wiggling fingers raised upward like flames.');
$pdf->SignRow('EARTHQUAKE', 'Both fists, shake side to side.');
$pdf->SignRow('TYPHOON', 'Circle index finger overhead (like wind).');
$pdf->SignRow('TSUNAMI', 'Both hands wave forward in large motion like a wave.');
$pdf->SignRow('LANDSLIDE', 'Fingers of both hands slide downward together.');

// ── Action Signs ──
$pdf->SectionTitle('Action Signs');
$pdf->SignRow('EVACUATE', 'Point outward, then wave hand forward.');
$pdf->SignRow('GO / MOVE', 'Point index finger forward, flick wrist.');
$pdf->SignRow('STOP', 'Palm facing outward, push forward firmly.');
$pdf->SignRow('WAIT', 'Index finger raised, move side to side slowly.');
$pdf->SignRow('COME HERE', 'Wave hand toward yourself repeatedly.');
$pdf->SignRow('FOLLOW ME', 'Point to yourself, then wave forward.');

// ── Needs Signs ──
$pdf->SectionTitle('Basic Needs Signs');
$pdf->SignRow('WATER', '"W" handshape tapped on chin.');
$pdf->SignRow('FOOD', 'Fingers bunched together, tap lips twice.');
$pdf->SignRow('SHELTER', 'Form a triangle overhead with both hands.');
$pdf->SignRow('MEDICINE', 'Tap wrist with index and middle fingers (checking pulse).');
$pdf->SignRow('TOILET', '"T" handshape, shake side to side.');
$pdf->SignRow('HELP NEEDED', 'Open hand, thumb up, lift upward - then point to self.');

// ── Communication Tips ──
$pdf->SectionTitle('Tips for Communicating with Deaf Individuals');
$pdf->BulletItem('Face the person directly and maintain eye contact.');
$pdf->BulletItem('Speak clearly and naturally if they lip-read - do not exaggerate.');
$pdf->BulletItem('Use gestures, writing, or the Silent Signal app if signs are unknown.');
$pdf->BulletItem('Do not shout - it distorts lip movement and does not help.');
$pdf->BulletItem('Ask yes/no questions by nodding or shaking your head.');
$pdf->BulletItem('Use the Silent Signal Communication Hub for pre-written emergency messages.');

// ── Using Silent Signal ──
$pdf->SectionTitle('Using Silent Signal During Disasters');
$pdf->BulletItem('Open the app and go to Emergency Alert to send an SOS with GPS location.');
$pdf->BulletItem('Use the Communication Hub to send pre-written icon-based messages.');
$pdf->BulletItem('Update your status in Family Check-in so your family knows you are safe.');
$pdf->BulletItem('Check the Disaster Monitoring tab for real-time alerts in your area.');

ob_end_clean();
$pdf->Output('D', 'fsl-disaster-communication.pdf');
exit();