<?php
// views/admin-print-pdf.php
// Generates an FPDF analytics report for the Admin Dashboard

// Auth check
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: " . BASE_URL . "index.php?action=auth");
    exit();
}

// Drain ALL output buffer levels before FPDF sends headers
while (ob_get_level() > 0) {
    ob_end_clean();
}

require_once BASE_PATH . 'assets/fsl/fpdf/fpdf.php';
require_once BASE_PATH . 'config/Database.php';
require_once BASE_PATH . 'models/AdminDashboard.php';
require_once BASE_PATH . 'models/User.php';
require_once BASE_PATH . 'models/EmergencyAlert.php';
require_once BASE_PATH . 'models/ContactInquiry.php';

// ── Fetch data ──────────────────────────────────────────────
$db           = (new Database())->getConnection();
$userModel    = new User();
$alertModel   = new EmergencyAlert();
$inquiryModel = new ContactInquiry();
$dashModel    = new AdminDashboard();

$userStats  = $userModel->getStats();
$alertStats = $alertModel->getStats();
$msgStats   = $inquiryModel->getStats();

$totalDisasters  = (int)$db->query("SELECT COUNT(*) FROM disaster_alerts")->fetchColumn();
$activeDisasters = (int)$db->query("SELECT COUNT(*) FROM disaster_alerts WHERE status = 'active'")->fetchColumn();

$chartUserRoles   = $dashModel->getUserRoleBreakdown();
$chartAlertStatus = $dashModel->getAlertStatusChart();

// Recent Alerts — limit 8
$recentAlertsData = $db->query("
    SELECT ea.alert_type, ea.status, ea.created_at,
           CONCAT(u.fname, ' ', u.lname) AS user_name
    FROM emergency_alerts ea
    LEFT JOIN users u ON ea.user_id = u.id
    ORDER BY ea.created_at DESC
    LIMIT 8
")->fetchAll(PDO::FETCH_ASSOC);

// Recent Users — limit 8
$recentUsersData = $db->query("
    SELECT CONCAT(fname, ' ', lname) AS full_name,
           email, role,
           DATE_FORMAT(created_at, '%b %d, %Y') AS joined
    FROM users
    ORDER BY created_at DESC
    LIMIT 8
")->fetchAll(PDO::FETCH_ASSOC);

// ── Custom FPDF class ───────────────────────────────────────
class AdminReportPDF extends FPDF {

    function Header() {
        $this->SetFillColor(26, 77, 127);
        $this->Rect(0, 0, 210, 28, 'F');

        $logoPath = BASE_PATH . 'assets/images/logo.png';
        if (file_exists($logoPath)) {
            $this->Image($logoPath, 5, 4, 20, 20, 'PNG');
        }

        $this->SetFont('Helvetica', 'B', 17);
        $this->SetTextColor(255, 255, 255);
        $this->SetY(7);
        $this->Cell(0, 10, 'Silent Signal - Admin Analytics Report', 0, 1, 'C');

        $this->SetFont('Helvetica', '', 9);
        $this->SetTextColor(200, 220, 245);
        $this->Cell(0, 6, 'Generated: ' . date('F j, Y  H:i:s'), 0, 1, 'C');

        $this->Ln(5);
        $this->SetTextColor(0, 0, 0);
    }

    function Footer() {
        $this->SetY(-14);
        $this->SetFont('Helvetica', 'I', 8);
        $this->SetTextColor(150, 150, 150);
        $this->Cell(0, 8, 'Silent Signal | Admin Report | Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    function SectionTitle($title, $r = 37, $g = 99, $b = 235) {
        $this->Ln(3);
        $this->SetFillColor($r, $g, $b);
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Helvetica', 'B', 10);
        $this->Cell(0, 8, '  ' . $title, 0, 1, 'L', true);
        $this->SetTextColor(0, 0, 0);
        $this->Ln(2);
    }

    function StatBox($x, $y, $w, $h, $label, $value, $sub, $r, $g, $b) {
        $this->SetFillColor(230, 235, 245);
        $this->Rect($x + 1, $y + 1, $w, $h, 'F');
        $this->SetFillColor(255, 255, 255);
        $this->Rect($x, $y, $w, $h, 'F');
        $this->SetFillColor($r, $g, $b);
        $this->Rect($x, $y, $w, 3, 'F');

        $this->SetFont('Helvetica', 'B', 18);
        $this->SetTextColor($r, $g, $b);
        $this->SetXY($x, $y + 4);
        $this->Cell($w, 9, $value, 0, 1, 'C');

        $this->SetFont('Helvetica', 'B', 7);
        $this->SetTextColor(80, 80, 80);
        $this->SetX($x);
        $this->Cell($w, 4, strtoupper($label), 0, 1, 'C');

        $this->SetFont('Helvetica', '', 6.5);
        $this->SetTextColor(140, 140, 140);
        $this->SetX($x);
        $this->Cell($w, 4, $sub, 0, 1, 'C');

        $this->SetTextColor(0, 0, 0);
    }

    function HorizBarChart($labels, $values, $colors, $maxVal, $x, $y, $barW, $barH, $gap) {
        foreach ($values as $i => $val) {
            $barLen = $maxVal > 0 ? ($val / $maxVal) * $barW : 0;
            list($r, $g, $b) = $colors[$i % count($colors)];
            $this->SetFillColor($r, $g, $b);
            if ($barLen > 0) $this->Rect($x, $y + $i * ($barH + $gap), $barLen, $barH, 'F');
            $this->SetFillColor(240, 242, 246);
            if ($barLen < $barW) $this->Rect($x + $barLen, $y + $i * ($barH + $gap), $barW - $barLen, $barH, 'F');
            $this->SetFont('Helvetica', '', 8);
            $this->SetTextColor(60, 60, 60);
            $this->SetXY($x - 50, $y + $i * ($barH + $gap) + 1);
            $this->Cell(48, $barH - 2, $labels[$i], 0, 0, 'R');
            $this->SetFont('Helvetica', 'B', 8);
            $this->SetTextColor($r, $g, $b);
            $this->SetXY($x + $barLen + 2, $y + $i * ($barH + $gap) + 1);
            $this->Cell(15, $barH - 2, (string)$val, 0, 0, 'L');
        }
        $this->SetTextColor(0, 0, 0);
    }

    function DataTable($headers, $rows, $widths, $headerBg = [26, 77, 127]) {
        $this->SetFillColor(...$headerBg);
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Helvetica', 'B', 9);
        foreach ($headers as $i => $h) {
            $this->Cell($widths[$i], 8, $h, 0, 0, 'C', true);
        }
        $this->Ln();
        $this->SetTextColor(0, 0, 0);
        $fill = false;
        foreach ($rows as $row) {
            $this->SetFillColor($fill ? 245 : 255, $fill ? 248 : 255, $fill ? 255 : 255);
            $this->SetFont('Helvetica', '', 8.5);
            foreach ($row as $i => $cell) {
                $this->Cell($widths[$i], 7, $cell, 0, 0, 'C', true);
            }
            $this->Ln();
            $fill = !$fill;
        }
        $totalW = array_sum($widths);
        $this->SetDrawColor(200, 210, 230);
        $this->SetLineWidth(0.3);
        $this->Line($this->GetX(), $this->GetY(), $this->GetX() + $totalW, $this->GetY());
        $this->SetLineWidth(0.2);
        $this->Ln(3);
    }
}

// ── Build PDF ───────────────────────────────────────────────
$pdf = new AdminReportPDF('P', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->SetAutoPageBreak(true, 18);
$pdf->SetMargins(14, 34, 14);
$pdf->AddPage();

$margin = 14;
$pageW  = 182;

// ── PAGE 1: Stats + Charts ──────────────────────────────────
$pdf->SectionTitle('System Summary');

$boxW   = 42;
$boxH   = 28;
$gap    = 4;
$startX = $margin;
$startY = $pdf->GetY();

$statsData = [
    ['Total Users',       number_format($userStats['total'] ?? 0), 'Registered accounts',         59, 130, 246],
    ['Active Alerts',     (string)($alertStats['active'] ?? 0),    'Currently active',             239, 68, 68],
    ['Disaster Alerts',   (string)$totalDisasters,                  $activeDisasters . ' active',   245, 158, 11],
    ['Message Inquiries', number_format($msgStats['total'] ?? 0),  ($msgStats['pending'] ?? 0) . ' pending', 16, 185, 129],
];

foreach ($statsData as $i => $s) {
    $pdf->StatBox($startX + $i * ($boxW + $gap), $startY, $boxW, $boxH, $s[0], $s[1], $s[2], $s[3], $s[4], $s[5]);
}
$pdf->SetY($startY + $boxH + 6);

// ── Chart 1: User Role Breakdown ──
$pdf->SectionTitle('User Role Breakdown', 59, 130, 246);

$roleMap    = ['pwd' => 'PWD User', 'family' => 'Family Member', 'admin' => 'Administrator'];
$roleColors = [[59,130,246],[16,185,129],[139,92,246]];
$roleLabels = [];
$roleValues = [];
$totalRoles = 0;
foreach ($chartUserRoles as $d) {
    $roleLabels[] = $roleMap[$d['role']] ?? ucfirst($d['role']);
    $roleValues[] = (int)$d['count'];
    $totalRoles  += (int)$d['count'];
}

$maxRole = $totalRoles > 0 ? max($roleValues) : 1;
$chartX  = $margin + 52;
$chartY  = $pdf->GetY();
$pdf->HorizBarChart($roleLabels, $roleValues, $roleColors, $maxRole, $chartX, $chartY, 90, 9, 3);
$pdf->SetY($chartY + count($roleLabels) * 12 + 4);

// ── Chart 2: Alert Status Breakdown ──
$pdf->SectionTitle('Alert Status Breakdown', 239, 68, 68);

$alertMap    = ['active' => 'Active', 'resolved' => 'Resolved', 'false_alarm' => 'False Alarm', 'pending' => 'Pending'];
$alertColors = [[239,68,68],[16,185,129],[245,158,11],[148,163,184]];
$alertLabels = [];
$alertValues = [];
foreach ($chartAlertStatus as $d) {
    $alertLabels[] = $alertMap[$d['status']] ?? ucfirst($d['status']);
    $alertValues[] = (int)$d['count'];
}

$maxAlert = count($alertValues) ? max(array_merge($alertValues, [1])) : 1;
$chartY   = $pdf->GetY();
$pdf->HorizBarChart($alertLabels, $alertValues, $alertColors, $maxAlert, $chartX, $chartY, 90, 9, 3);
$pdf->SetY($chartY + count($alertLabels) * 12 + 4);

// ── PAGE 2: Data Tables ─────────────────────────────────────
$pdf->AddPage();

// Recent Emergency Alerts
$pdf->SectionTitle('Recent Emergency Alerts', 239, 68, 68);
$alertDetailHeaders = ['User', 'Type', 'Status', 'Date'];
$alertDetailWidths  = [50, 42, 34, 56];
$alertDetailRows    = [];
foreach ($recentAlertsData as $row) {
    $alertDetailRows[] = [
        mb_substr($row['user_name'] ?? 'Unknown', 0, 22),
        ucwords(str_replace('_', ' ', $row['alert_type'] ?? 'SOS')),
        ucfirst($row['status'] ?? 'pending'),
        date('M d, Y H:i', strtotime($row['created_at'])),
    ];
}
if (empty($alertDetailRows)) {
    $alertDetailRows[] = ['No recent alerts', '', '', ''];
}
$pdf->DataTable($alertDetailHeaders, $alertDetailRows, $alertDetailWidths);

// Recently Registered Users
$pdf->SectionTitle('Recently Registered Users', 59, 130, 246);
$userDetailHeaders = ['Name', 'Email', 'Role', 'Joined'];
$userDetailWidths  = [45, 65, 28, 44];
$userDetailRows    = [];
$roleMap2          = ['pwd' => 'PWD User', 'family' => 'Family', 'admin' => 'Admin'];
foreach ($recentUsersData as $row) {
    $userDetailRows[] = [
        mb_substr($row['full_name'] ?? 'N/A', 0, 22),
        mb_substr($row['email'] ?? '', 0, 30),
        $roleMap2[$row['role']] ?? ucfirst($row['role']),
        $row['joined'] ?? '',
    ];
}
if (empty($userDetailRows)) {
    $userDetailRows[] = ['No users found', '', '', ''];
}
$pdf->DataTable($userDetailHeaders, $userDetailRows, $userDetailWidths);

// ── Output ──────────────────────────────────────────────────
$pdf->Output('I', 'SilentSignal_Admin_Report_' . date('Ymd_His') . '.pdf');
exit();
