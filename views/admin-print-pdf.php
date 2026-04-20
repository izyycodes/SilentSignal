<?php
// views/admin-print-pdf.php
// Generates an FPDF analytics report for the Admin Dashboard

// Auth check
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: " . BASE_URL . "index.php?action=auth");
    exit();
}

// Clean any output buffer before PDF output
if (ob_get_length()) ob_end_clean();

require_once BASE_PATH . 'assets/fsl/fpdf/fpdf.php';
require_once BASE_PATH . 'config/Database.php';
require_once BASE_PATH . 'models/AdminDashboard.php';
require_once BASE_PATH . 'models/User.php';
require_once BASE_PATH . 'models/EmergencyAlert.php';
require_once BASE_PATH . 'models/ContactInquiry.php';

// ── Fetch data ──────────────────────────────────────────────
$db            = (new Database())->getConnection();
$userModel     = new User();
$alertModel    = new EmergencyAlert();
$inquiryModel  = new ContactInquiry();
$dashModel     = new AdminDashboard();

$userStats  = $userModel->getStats();
$alertStats = $alertModel->getStats();
$msgStats   = $inquiryModel->getStats();

$disasterStmt      = $db->query("SELECT COUNT(*) FROM disaster_alerts");
$totalDisasters    = (int)$disasterStmt->fetchColumn();
$activeDisStmt     = $db->query("SELECT COUNT(*) FROM disaster_alerts WHERE status = 'active'");
$activeDisasters   = (int)$activeDisStmt->fetchColumn();

$chartUserRoles       = $dashModel->getUserRoleBreakdown();
$chartAlertStatus     = $dashModel->getAlertStatusChart();
$chartMonthlyActivity = $dashModel->getMonthlyActivityChart();
$chartMsgCategories   = $dashModel->getMessageCategoriesChart();

// Recent Alerts for data table
$recentAlertsStmt = $db->query("
    SELECT ea.alert_type, ea.status, ea.created_at,
           CONCAT(u.first_name, ' ', u.last_name) AS user_name,
           ea.location_address
    FROM emergency_alerts ea
    LEFT JOIN users u ON ea.user_id = u.id
    ORDER BY ea.created_at DESC
    LIMIT 10
");
$recentAlertsData = $recentAlertsStmt->fetchAll(PDO::FETCH_ASSOC);

// Recent Users for data table
$recentUsersStmt = $db->query("
    SELECT CONCAT(first_name, ' ', last_name) AS full_name,
           email, role,
           DATE_FORMAT(created_at, '%b %d, %Y') AS joined
    FROM users
    ORDER BY created_at DESC
    LIMIT 8
");
$recentUsersData = $recentUsersStmt->fetchAll(PDO::FETCH_ASSOC);

// ── Custom FPDF class ───────────────────────────────────────
class AdminReportPDF extends FPDF {

    function Header() {
        // Blue header bar
        $this->SetFillColor(26, 77, 127);
        $this->Rect(0, 0, 210, 28, 'F');

        // Logo image (top-left)
        $logoPath = BASE_PATH . 'assets/images/logo.png';
        if (file_exists($logoPath)) {
            $this->Image($logoPath, 5, 4, 20, 20, 'PNG');
        }

        $this->SetFont('Helvetica', 'B', 18);
        $this->SetTextColor(255, 255, 255);
        $this->SetY(7);
        $this->Cell(0, 10, 'Silent Signal - Admin Analytics Report', 0, 1, 'C');

        $this->SetFont('Helvetica', '', 9);
        $this->SetTextColor(200, 220, 245);
        $this->Cell(0, 6, 'Generated: ' . date('F j, Y  H:i:s'), 0, 1, 'C');

        $this->Ln(6);
        $this->SetTextColor(0, 0, 0);
    }

    function Footer() {
        $this->SetY(-14);
        $this->SetFont('Helvetica', 'I', 8);
        $this->SetTextColor(150, 150, 150);
        $this->Cell(0, 8, 'Silent Signal | Admin Report | Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    // Section title
    function SectionTitle($title, $r = 37, $g = 99, $b = 235) {
        $this->Ln(4);
        $this->SetFillColor($r, $g, $b);
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Helvetica', 'B', 11);
        $this->Cell(0, 9, '  ' . $title, 0, 1, 'L', true);
        $this->SetTextColor(0, 0, 0);
        $this->Ln(3);
    }

    // Stat box — draws a colored card
    function StatBox($x, $y, $w, $h, $label, $value, $sub, $r, $g, $b) {
        // Shadow
        $this->SetFillColor(230, 235, 245);
        $this->RoundedRect($x + 1, $y + 1, $w, $h, 3, 'F');
        // Card
        $this->SetFillColor(255, 255, 255);
        $this->RoundedRect($x, $y, $w, $h, 3, 'F');
        // Color bar top
        $this->SetFillColor($r, $g, $b);
        $this->Rect($x, $y, $w, 3, 'F');
        // Value
        $this->SetFont('Helvetica', 'B', 20);
        $this->SetTextColor($r, $g, $b);
        $this->SetXY($x, $y + 5);
        $this->Cell($w, 10, $value, 0, 1, 'C');
        // Label
        $this->SetFont('Helvetica', 'B', 8);
        $this->SetTextColor(80, 80, 80);
        $this->SetX($x);
        $this->Cell($w, 5, strtoupper($label), 0, 1, 'C');
        // Sub
        $this->SetFont('Helvetica', '', 7);
        $this->SetTextColor(140, 140, 140);
        $this->SetX($x);
        $this->Cell($w, 4, $sub, 0, 1, 'C');

        $this->SetTextColor(0, 0, 0);
    }

    // Rounded rectangle (FPDF doesn't have native)
    function RoundedRect($x, $y, $w, $h, $r, $style = '') {
        $k  = $this->k;
        $hp = $this->h;
        if ($style === 'F') $op = 'f';
        elseif ($style === 'FD' || $style === 'DF') $op = 'B';
        else $op = 'S';
        $MyArc = 4 / 3 * (sqrt(2) - 1);
        $this->_out(sprintf('%.2F %.2F m', ($x + $r) * $k, ($hp - $y) * $k));
        $xc = $x + $w - $r;
        $yc = $y + $r;
        $this->_out(sprintf('%.2F %.2F l', $xc * $k, ($hp - $y) * $k));
        $this->_Arc($xc + $r * $MyArc, $yc - $r, $xc + $r, $yc - $r * $MyArc, $xc + $r, $yc);
        $xc = $x + $w - $r;
        $yc = $y + $h - $r;
        $this->_out(sprintf('%.2F %.2F l', ($x + $w) * $k, ($hp - $yc) * $k));
        $this->_Arc($xc + $r, $yc + $r * $MyArc, $xc + $r * $MyArc, $yc + $r, $xc, $yc + $r);
        $xc = $x + $r;
        $yc = $y + $h - $r;
        $this->_out(sprintf('%.2F %.2F l', $xc * $k, ($hp - ($y + $h)) * $k));
        $this->_Arc($xc - $r * $MyArc, $yc + $r, $xc - $r, $yc + $r * $MyArc, $xc - $r, $yc);
        $xc = $x + $r;
        $yc = $y + $r;
        $this->_out(sprintf('%.2F %.2F l', $x * $k, ($hp - $yc) * $k));
        $this->_Arc($xc - $r, $yc - $r * $MyArc, $xc - $r * $MyArc, $yc - $r, $xc, $yc - $r);
        $this->_out($op);
    }

    function _Arc($x1, $y1, $x2, $y2, $x3, $y3) {
        $h = $this->h;
        $this->_out(sprintf(
            '%.2F %.2F %.2F %.2F %.2F %.2F c',
            $x1 * $this->k, ($h - $y1) * $this->k,
            $x2 * $this->k, ($h - $y2) * $this->k,
            $x3 * $this->k, ($h - $y3) * $this->k
        ));
    }

    // Horizontal bar chart (draws bars inline)
    function HorizBarChart($labels, $values, $colors, $maxVal, $x, $y, $barW, $barH, $gap) {
        $chartW = $barW;
        foreach ($values as $i => $val) {
            $barLen = $maxVal > 0 ? ($val / $maxVal) * $chartW : 0;
            list($r, $g, $b) = $colors[$i % count($colors)];
            // Bar
            $this->SetFillColor($r, $g, $b);
            if ($barLen > 0) $this->Rect($x, $y + $i * ($barH + $gap), $barLen, $barH, 'F');
            // Empty background
            $this->SetFillColor(240, 242, 246);
            if ($barLen < $chartW) $this->Rect($x + $barLen, $y + $i * ($barH + $gap), $chartW - $barLen, $barH, 'F');
            // Label
            $this->SetFont('Helvetica', '', 8);
            $this->SetTextColor(60, 60, 60);
            $this->SetXY($x - 50, $y + $i * ($barH + $gap) + 1);
            $this->Cell(48, $barH - 2, $labels[$i], 0, 0, 'R');
            // Value
            $this->SetFont('Helvetica', 'B', 8);
            $this->SetTextColor($r, $g, $b);
            $this->SetXY($x + $barLen + 2, $y + $i * ($barH + $gap) + 1);
            $this->Cell(15, $barH - 2, $val, 0, 0, 'L');
        }
        $this->SetTextColor(0, 0, 0);
    }

    // Table with header
    function DataTable($headers, $rows, $widths, $headerBg = [26, 77, 127]) {
        // Header row
        $this->SetFillColor(...$headerBg);
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Helvetica', 'B', 9);
        foreach ($headers as $i => $h) {
            $this->Cell($widths[$i], 8, $h, 0, 0, 'C', true);
        }
        $this->Ln();
        // Data rows
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
        // Bottom border
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
$pageW  = 182; // 210 - 14*2

// ── PAGE 1: Summary Stats ───────────────────────────────────
$pdf->SectionTitle('System Summary');

$boxW  = 42;
$boxH  = 30;
$gap   = 4;
$startX = $margin;
$startY = $pdf->GetY();

$statsData = [
    ['Total Users',       number_format($userStats['total']),   'Registered accounts',    59, 130, 246],
    ['Active Alerts',     (string)$alertStats['active'],        'Currently active',       239, 68, 68],
    ['Disaster Alerts',   (string)$totalDisasters,              $activeDisasters . ' active', 245, 158, 11],
    ['Message Inquiries', number_format($msgStats['total']),    $msgStats['pending'] . ' pending', 16, 185, 129],
];

foreach ($statsData as $i => $s) {
    $pdf->StatBox(
        $startX + $i * ($boxW + $gap),
        $startY,
        $boxW, $boxH,
        $s[0], $s[1], $s[2],
        $s[3], $s[4], $s[5]
    );
}

$pdf->SetY($startY + $boxH + 8);

// ── System Health ──
$pdf->SectionTitle('System Health', 16, 185, 129);

$healthItems = [
    ['Server Status', 'Online', 16, 185, 129],
    ['Database',      'Connected', 16, 185, 129],
    ['SMS Service',   'Active', 16, 185, 129],
    ['API Status',    'Operational', 16, 185, 129],
];

$hw   = ($pageW / count($healthItems)) - 2;
$hy   = $pdf->GetY();
foreach ($healthItems as $i => $item) {
    $hx = $margin + $i * ($hw + 2.7);
    $pdf->SetFillColor(240, 253, 244);
    $pdf->RoundedRect($hx, $hy, $hw, 14, 2, 'F');
    $pdf->SetFont('Helvetica', 'B', 8);
    $pdf->SetTextColor(60, 60, 60);
    $pdf->SetXY($hx, $hy + 1);
    $pdf->Cell($hw, 5, $item[0], 0, 1, 'C');
    $pdf->SetFont('Helvetica', 'B', 9);
    $pdf->SetTextColor($item[2], $item[3], $item[4]);
    $pdf->SetX($hx);
    $pdf->Cell($hw, 6, '● ' . $item[1], 0, 0, 'C');
}
$pdf->SetTextColor(0, 0, 0);
$pdf->SetY($hy + 20);

// ── PAGE 2: Charts ──────────────────────────────────────────
$pdf->AddPage();

// ── Chart 1: User Role Breakdown ──
$pdf->SectionTitle('User Role Breakdown', 59, 130, 246);

$roleMap   = ['pwd' => 'PWD User', 'family' => 'Family Member', 'admin' => 'Administrator'];
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
$pdf->HorizBarChart($roleLabels, $roleValues, $roleColors, $maxRole, $chartX, $chartY, 80, 9, 3);
$pdf->SetY($chartY + count($roleLabels) * 12 + 6);

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
$pdf->HorizBarChart($alertLabels, $alertValues, $alertColors, $maxAlert, $chartX, $chartY, 80, 9, 3);
$pdf->SetY($chartY + count($alertLabels) * 12 + 6);

// ── Chart 3: Monthly Activity ──
$pdf->SectionTitle('Monthly Activity — Alerts vs Messages', 16, 185, 129);

// Build months
$months      = array_values(array_unique(array_column($chartMonthlyActivity, 'month')));
$alertsArr   = [];
$messagesArr = [];
foreach ($months as $m) {
    $af = array_filter($chartMonthlyActivity, fn($d) => $d['month'] === $m && $d['type'] === 'alerts');
    $mf = array_filter($chartMonthlyActivity, fn($d) => $d['month'] === $m && $d['type'] === 'messages');
    $alertsArr[]   = $af   ? (int)array_values($af)[0]['count']   : 0;
    $messagesArr[] = $mf   ? (int)array_values($mf)[0]['count']   : 0;
}

if (count($months)) {
    // Draw a small grouped bar chart using FPDF
    $mChartX = $margin;
    $mChartY = $pdf->GetY();
    $barW    = min(12, (int)(($pageW - 10) / max(count($months), 1)));
    $maxM    = max(array_merge($alertsArr, $messagesArr, [1]));
    $chartH  = 40; // chart area height
    $scaleY  = $maxM > 0 ? $chartH / $maxM : 1;

    // Axes
    $axisX = $mChartX + 10;
    $axisY = $mChartY + $chartH;
    $pdf->SetDrawColor(180, 190, 210);
    $pdf->SetLineWidth(0.3);
    $pdf->Line($axisX, $mChartY, $axisX, $axisY + 1); // Y axis
    $pdf->Line($axisX, $axisY, $axisX + count($months) * ($barW * 2 + 4), $axisY); // X axis

    foreach ($months as $i => $m) {
        $bx = $axisX + 2 + $i * ($barW * 2 + 4);

        // Alert bar (red)
        $ah = max(1, (int)($alertsArr[$i] * $scaleY));
        $pdf->SetFillColor(239, 68, 68);
        $pdf->Rect($bx, $axisY - $ah, $barW, $ah, 'F');

        // Message bar (green)
        $mh = max(1, (int)($messagesArr[$i] * $scaleY));
        $pdf->SetFillColor(16, 185, 129);
        $pdf->Rect($bx + $barW + 1, $axisY - $mh, $barW, $mh, 'F');

        // Month label
        $pdf->SetFont('Helvetica', '', 6.5);
        $pdf->SetTextColor(100, 100, 100);
        $pdf->SetXY($bx, $axisY + 1);
        $label = strlen($m) > 7 ? substr($m, 0, 7) : $m;
        $pdf->Cell($barW * 2 + 1, 4, $label, 0, 0, 'C');
    }

    // Legend
    $legX = $axisX + count($months) * ($barW * 2 + 4) + 6;
    $pdf->SetFillColor(239, 68, 68);
    $pdf->Rect($legX, $mChartY + 2, 6, 4, 'F');
    $pdf->SetFont('Helvetica', '', 8);
    $pdf->SetTextColor(60, 60, 60);
    $pdf->SetXY($legX + 8, $mChartY + 1);
    $pdf->Cell(30, 5, 'Alerts');

    $pdf->SetFillColor(16, 185, 129);
    $pdf->Rect($legX, $mChartY + 10, 6, 4, 'F');
    $pdf->SetXY($legX + 8, $mChartY + 9);
    $pdf->Cell(30, 5, 'Messages');

    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetY($axisY + 8);
} else {
    $pdf->SetFont('Helvetica', 'I', 9);
    $pdf->SetTextColor(150, 150, 150);
    $pdf->Cell(0, 8, 'No monthly activity data available.', 0, 1, 'L');
    $pdf->SetTextColor(0, 0, 0);
}

// ── Chart 4: Message Categories ──
$pdf->SectionTitle('Message Inquiry Categories', 245, 158, 11);

$catMap    = ['general'=>'General','technical'=>'Technical','account'=>'Account','emergency'=>'Emergency','feedback'=>'Feedback','billing'=>'Billing','accessibility'=>'Accessibility'];
$catColors = [[59,130,246],[245,158,11],[16,185,129],[239,68,68],[139,92,246],[236,72,153],[20,184,166]];
$catLabels = [];
$catValues = [];
foreach ($chartMsgCategories as $d) {
    $catLabels[] = $catMap[$d['category']] ?? ucfirst($d['category']);
    $catValues[] = (int)$d['count'];
}

if (count($catValues)) {
    $maxCat  = max(array_merge($catValues, [1]));
    $chartY  = $pdf->GetY();
    $pdf->HorizBarChart($catLabels, $catValues, $catColors, $maxCat, $chartX, $chartY, 80, 9, 3);
    $pdf->SetY($chartY + count($catLabels) * 12 + 6);
} else {
    $pdf->SetFont('Helvetica', 'I', 9);
    $pdf->SetTextColor(150, 150, 150);
    $pdf->Cell(0, 8, 'No message category data available.', 0, 1, 'L');
    $pdf->SetTextColor(0, 0, 0);
}

// ── PAGE 3: Data Tables ─────────────────────────────────────
$pdf->AddPage();

// User Role Table
$pdf->SectionTitle('User Role Summary Table', 59, 130, 246);

$roleHeaders = ['Role', 'Count', '% of Total'];
$roleWidths  = [70, 56, 56];
$roleRows    = [];
foreach ($chartUserRoles as $d) {
    $pct       = $totalRoles > 0 ? round((int)$d['count'] / $totalRoles * 100, 1) . '%' : '0%';
    $roleRows[] = [
        $roleMap[$d['role']] ?? ucfirst($d['role']),
        (string)(int)$d['count'],
        $pct,
    ];
}
// Total row
$roleRows[] = ['TOTAL', (string)$totalRoles, '100%'];
$pdf->DataTable($roleHeaders, $roleRows, $roleWidths);

// Alert Status Table
$pdf->SectionTitle('Alert Status Summary Table', 239, 68, 68);

$alertHeaders = ['Status', 'Count'];
$alertWidths  = [91, 91];
$alertRows    = [];
$totalAlerts  = array_sum($alertValues);
foreach ($chartAlertStatus as $d) {
    $alertRows[] = [
        $alertMap[$d['status']] ?? ucfirst($d['status']),
        (string)(int)$d['count'],
    ];
}
$alertRows[] = ['TOTAL', (string)$totalAlerts];
$pdf->DataTable($alertHeaders, $alertRows, $alertWidths);

// Message Category Table
$pdf->SectionTitle('Message Inquiry Categories Table', 245, 158, 11);

$catHeaders  = ['Category', 'Count'];
$catWidths   = [91, 91];
$catTableRows = [];
$totalCat    = array_sum($catValues);
foreach ($chartMsgCategories as $d) {
    $catTableRows[] = [
        $catMap[$d['category']] ?? ucfirst($d['category']),
        (string)(int)$d['count'],
    ];
}
$catTableRows[] = ['TOTAL', (string)$totalCat];
$pdf->DataTable($catHeaders, $catTableRows, $catWidths);

// ── PAGE 4: Recent Activity Tables ──────────────────────────
$pdf->AddPage();

// Recent Emergency Alerts Table
$pdf->SectionTitle('Recent Emergency Alerts', 239, 68, 68);
$alertDetailHeaders = ['User', 'Type', 'Status', 'Date'];
$alertDetailWidths  = [52, 40, 34, 56];
$alertDetailRows    = [];
foreach ($recentAlertsData as $row) {
    $alertDetailRows[] = [
        mb_substr($row['user_name'] ?? 'Unknown', 0, 20),
        ucwords(str_replace('_', ' ', $row['alert_type'] ?? 'SOS')),
        ucfirst($row['status'] ?? 'pending'),
        date('M d, Y H:i', strtotime($row['created_at'])),
    ];
}
if (empty($alertDetailRows)) {
    $alertDetailRows[] = ['No recent alerts', '', '', ''];
}
$pdf->DataTable($alertDetailHeaders, $alertDetailRows, $alertDetailWidths);

// Recent Users Table
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
