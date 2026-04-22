<?php
// views/admin-print-pdf.php
// Generates an FPDF analytics report for the Admin Dashboard

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: " . BASE_URL . "index.php?action=auth");
    exit();
}

while (ob_get_level() > 0) ob_end_clean();

require_once BASE_PATH . 'assets/fsl/fpdf/fpdf.php';
require_once BASE_PATH . 'config/Database.php';
require_once BASE_PATH . 'models/AdminDashboard.php';
require_once BASE_PATH . 'models/User.php';
require_once BASE_PATH . 'models/EmergencyAlert.php';
require_once BASE_PATH . 'models/ContactInquiry.php';

// ── Fetch all data ──────────────────────────────────────────
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

// User role breakdown
$chartUserRoles   = $dashModel->getUserRoleBreakdown();
// Alert status breakdown
$chartAlertStatus = $dashModel->getAlertStatusChart();

// Recent emergency alerts — limit 10
$recentAlertsData = $db->query("
    SELECT ea.id, ea.alert_type, ea.status, ea.priority, ea.created_at,
           ea.location_address,
           CONCAT(u.fname, ' ', u.lname) AS user_name
    FROM emergency_alerts ea
    LEFT JOIN users u ON ea.user_id = u.id
    ORDER BY ea.created_at DESC
    LIMIT 10
")->fetchAll(PDO::FETCH_ASSOC);

// Recent users — limit 10
$recentUsersData = $db->query("
    SELECT CONCAT(fname, ' ', lname) AS full_name,
           email, role, phone_number,
           CASE WHEN is_verified=1 THEN 'Verified' ELSE 'Pending' END AS verified,
           CASE WHEN is_active=1   THEN 'Active'   ELSE 'Inactive' END AS active_status,
           DATE_FORMAT(created_at, '%b %d, %Y') AS joined
    FROM users
    ORDER BY created_at DESC
    LIMIT 10
")->fetchAll(PDO::FETCH_ASSOC);

// Disaster alerts — limit 10
$disasterData = $db->query("
    SELECT id, alert_type, severity, location, status,
           DATE_FORMAT(created_at, '%b %d, %Y') AS issued_date
    FROM disaster_alerts
    ORDER BY created_at DESC
    LIMIT 10
")->fetchAll(PDO::FETCH_ASSOC);

// Message inquiry breakdown
$msgCategoryData = $db->query("
    SELECT category, COUNT(*) AS cnt FROM contact_inquiries GROUP BY category ORDER BY cnt DESC
")->fetchAll(PDO::FETCH_ASSOC);

$msgStatusData = $db->query("
    SELECT status, COUNT(*) AS cnt FROM contact_inquiries GROUP BY status ORDER BY cnt DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Alert type breakdown
$alertTypeData = $db->query("
    SELECT alert_type, COUNT(*) AS cnt FROM emergency_alerts GROUP BY alert_type ORDER BY cnt DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Monthly registration trend (last 6 months)
$monthlyUsers = $db->query("
    SELECT DATE_FORMAT(created_at,'%b %Y') AS month_label,
           COUNT(*) AS cnt
    FROM users
    WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    GROUP BY YEAR(created_at), MONTH(created_at)
    ORDER BY YEAR(created_at), MONTH(created_at)
")->fetchAll(PDO::FETCH_ASSOC);

// ── Custom FPDF class ───────────────────────────────────────
class AdminReportPDF extends FPDF {

    private $logoPath = '';

    function setLogoPath($p) { $this->logoPath = $p; }

    // ── Header ─────────────────────────────────────────────
    function Header() {
        // Deep navy bar
        $this->SetFillColor(15, 40, 80);
        $this->Rect(0, 0, 210, 26, 'F');

        // Accent stripe
        $this->SetFillColor(59, 130, 246);
        $this->Rect(0, 26, 210, 3, 'F');

        // Logo
        if ($this->logoPath && file_exists($this->logoPath)) {
            $this->Image($this->logoPath, 6, 3, 18, 18, 'PNG');
        }

        // Title
        $this->SetFont('Helvetica', 'B', 15);
        $this->SetTextColor(255, 255, 255);
        $this->SetXY(28, 5);
        $this->Cell(0, 8, 'Silent Signal  |  Admin Analytics Report', 0, 1, 'L');

        $this->SetFont('Helvetica', '', 8);
        $this->SetTextColor(160, 195, 240);
        $this->SetX(28);
        $this->Cell(0, 6, 'Generated: ' . date('F j, Y  \a\t  H:i:s'), 0, 1, 'L');

        // Right: page number placeholder
        $this->SetFont('Helvetica', 'I', 8);
        $this->SetTextColor(120, 160, 210);
        $this->SetXY(0, 5);
        $this->Cell(204, 6, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'R');

        $this->SetY(34);
        $this->SetTextColor(0, 0, 0);
    }

    // ── Footer ─────────────────────────────────────────────
    function Footer() {
        $this->SetY(-13);
        $this->SetFillColor(240, 245, 255);
        $this->Rect(0, $this->GetY(), 210, 13, 'F');
        $this->SetFont('Helvetica', 'I', 7.5);
        $this->SetTextColor(120, 130, 160);
        $this->Cell(0, 13,
            'Silent Signal Admin Report  |  Confidential  |  Page ' . $this->PageNo() . '/{nb}  |  ' . date('Y'),
            0, 0, 'C');
    }

    // ── Section Title Bar ───────────────────────────────────
    function SectionTitle($title, $r=37, $g=99, $b=235, $icon='') {
        $this->Ln(2);
        // Left accent line
        $this->SetFillColor($r, $g, $b);
        $this->Rect(14, $this->GetY(), 3, 7, 'F');
        // Background
        $this->SetFillColor($r+190 > 255 ? 255 : $r+190,
                            $g+210 > 255 ? 255 : $g+210,
                            $b+190 > 255 ? 255 : $b+190);
        $this->Rect(17, $this->GetY(), 179, 7, 'F');

        $this->SetFont('Helvetica', 'B', 9.5);
        $this->SetTextColor($r, $g, $b);
        $this->SetX(20);
        $this->Cell(0, 7, strtoupper($title), 0, 1, 'L');
        $this->SetTextColor(0,0,0);
        $this->Ln(2);
    }

    // ── Stat Card (4-up row) ────────────────────────────────
    function StatCard($x, $y, $w, $h, $label, $value, $sub, $r, $g, $b) {
        // Shadow
        $this->SetFillColor(210, 215, 230);
        $this->Rect($x+1.5, $y+1.5, $w, $h, 'F');

        // Card background
        $this->SetFillColor(255, 255, 255);
        $this->Rect($x, $y, $w, $h, 'F');

        // Top colour bar
        $this->SetFillColor($r, $g, $b);
        $this->Rect($x, $y, $w, 3.5, 'F');

        // Small colour dot / badge area
        $this->SetFillColor($r, $g, $b);
        $dotX = $x + $w - 12;
        $dotY = $y + 5;
        $this->Rect($dotX, $dotY, 9, 9, 'F');  // square icon area

        // Value
        $this->SetFont('Helvetica', 'B', 20);
        $this->SetTextColor($r, $g, $b);
        $this->SetXY($x, $y + 5);
        $this->Cell($w - 13, 11, $value, 0, 0, 'L');

        // Label
        $this->SetFont('Helvetica', 'B', 7);
        $this->SetTextColor(50, 60, 80);
        $this->SetXY($x + 2, $y + 17);
        $this->Cell($w - 4, 4, strtoupper($label), 0, 1, 'L');

        // Sub text
        $this->SetFont('Helvetica', '', 6.5);
        $this->SetTextColor(130, 140, 160);
        $this->SetX($x + 2);
        $this->Cell($w - 4, 4, $sub, 0, 1, 'L');

        $this->SetTextColor(0,0,0);
    }

    // ── Horizontal Bar Chart ────────────────────────────────
    function HorizBarChart($labels, $values, $colors, $maxVal, $x, $y, $barW, $barH, $gap) {
        if (empty($values)) return;
        $total = array_sum($values);
        foreach ($values as $i => $val) {
            $barLen = $maxVal > 0 ? round(($val / $maxVal) * $barW) : 0;
            list($r, $g, $b) = $colors[$i % count($colors)];
            $by = $y + $i * ($barH + $gap);

            // Background track
            $this->SetFillColor(235, 238, 245);
            $this->Rect($x, $by, $barW, $barH, 'F');

            // Filled bar
            if ($barLen > 0) {
                $this->SetFillColor($r, $g, $b);
                $this->Rect($x, $by, $barLen, $barH, 'F');
            }

            // Label (left)
            $this->SetFont('Helvetica', '', 7.5);
            $this->SetTextColor(60, 70, 90);
            $this->SetXY($x - 52, $by + 0.8);
            $this->Cell(50, $barH - 1.5, $labels[$i], 0, 0, 'R');

            // Value + % (right of bar)
            $pct = $total > 0 ? round($val / $total * 100) : 0;
            $this->SetFont('Helvetica', 'B', 7.5);
            $this->SetTextColor($r, $g, $b);
            $this->SetXY($x + $barLen + 2, $by + 0.8);
            $this->Cell(18, $barH - 1.5, $val . '  (' . $pct . '%)', 0, 0, 'L');
        }
        $this->SetTextColor(0,0,0);
    }

    // ── Mini Donut-Style Legend ─────────────────────────────
    function LegendRow($labels, $values, $colors, $x, $y, $colW) {
        $total = array_sum($values);
        foreach ($labels as $i => $lbl) {
            $cx = $x + $i * $colW;
            list($r,$g,$b) = $colors[$i % count($colors)];
            $pct = $total > 0 ? round($values[$i] / $total * 100) : 0;

            $this->SetFillColor($r, $g, $b);
            $this->Rect($cx, $y, 4, 4, 'F');

            $this->SetFont('Helvetica', '', 7);
            $this->SetTextColor(60, 70, 90);
            $this->SetXY($cx + 6, $y - 0.5);
            $this->Cell($colW - 8, 5, $lbl . ' (' . $pct . '%)', 0, 0, 'L');
        }
        $this->SetTextColor(0,0,0);
    }

    // ── Divider line ────────────────────────────────────────
    function Divider($margin=14) {
        $this->SetDrawColor(210, 218, 235);
        $this->SetLineWidth(0.25);
        $this->Line($margin, $this->GetY(), 210 - $margin, $this->GetY());
        $this->SetLineWidth(0.2);
        $this->Ln(3);
    }

    // ── Data Table with coloured header + alt rows ──────────
    function DataTable($headers, $rows, $widths, $aligns, $headerBg=[15,40,80], $badgeCols=[]) {
        // Header
        $this->SetFillColor(...$headerBg);
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Helvetica', 'B', 7.5);
        $this->SetDrawColor(255,255,255);
        $this->SetLineWidth(0.4);
        foreach ($headers as $i => $h) {
            $this->Cell($widths[$i], 7.5, ' ' . $h, 'B', 0, $aligns[$i] ?? 'L', true);
        }
        $this->Ln();
        $this->SetDrawColor(200, 210, 230);
        $this->SetLineWidth(0.2);

        $fill = false;
        foreach ($rows as $ri => $row) {
            // Auto page break check
            if ($this->GetY() + 7 > ($this->h - 18)) {
                $this->AddPage();
                // Repeat header
                $this->SetFillColor(...$headerBg);
                $this->SetTextColor(255, 255, 255);
                $this->SetFont('Helvetica', 'B', 7.5);
                foreach ($headers as $i => $h) {
                    $this->Cell($widths[$i], 7.5, ' ' . $h, 'B', 0, $aligns[$i] ?? 'L', true);
                }
                $this->Ln();
            }

            $this->SetFillColor($fill ? 244 : 255, $fill ? 247 : 255, $fill ? 255 : 255);
            $this->SetTextColor(30, 40, 60);
            $this->SetFont('Helvetica', '', 7.5);

            foreach ($row as $i => $cell) {
                if (in_array($i, $badgeCols)) {
                    // Draw badge background
                    list($br,$bg,$bb) = $this->statusColor($cell);
                    $cx = $this->GetX();
                    $cy = $this->GetY();
                    $cw = $widths[$i];
                    // Draw the cell fill first
                    $this->SetFillColor($fill ? 244 : 255, $fill ? 247 : 255, $fill ? 255 : 255);
                    $this->Cell($cw, 7, '', 0, 0, 'C', true);
                    // Badge rect
                    $bw = min(strlen($cell) * 2.2 + 6, $cw - 4);
                    $bx = $cx + ($cw - $bw) / 2;
                    $by = $cy + 1.2;
                    $this->SetFillColor($br, $bg, $bb);
                    $this->Rect($bx, $by, $bw, 4.5, 'F');
                    // Badge text
                    $this->SetFont('Helvetica', 'B', 6.5);
                    $this->SetTextColor(255,255,255);
                    $this->SetXY($bx, $by - 0.2);
                    $this->Cell($bw, 4.8, strtoupper($cell), 0, 0, 'C');
                    // Move cursor forward
                    $this->SetXY($cx + $cw, $cy);
                    $this->SetFont('Helvetica', '', 7.5);
                    $this->SetTextColor(30, 40, 60);
                } else {
                    $align = $aligns[$i] ?? 'L';
                    $this->SetFillColor($fill ? 244 : 255, $fill ? 247 : 255, $fill ? 255 : 255);
                    $this->Cell($widths[$i], 7, ' ' . mb_substr((string)$cell, 0, 35), 0, 0, $align, true);
                }
            }
            $this->Ln();

            // Subtle row border
            $this->SetDrawColor(225, 230, 242);
            $this->Line(14, $this->GetY(), 196, $this->GetY());

            $fill = !$fill;
        }

        // Bottom border
        $this->SetDrawColor(150, 170, 210);
        $this->SetLineWidth(0.4);
        $this->Line(14, $this->GetY(), 196, $this->GetY());
        $this->SetLineWidth(0.2);
        $this->Ln(4);
    }

    // ── Status → badge colour ───────────────────────────────
    function statusColor($status) {
        $s = strtolower(trim($status));
        $map = [
            'active'       => [220, 38,  38],
            'resolved'     => [22,  163, 74],
            'pending'      => [245, 158, 11],
            'false alarm'  => [107, 114, 128],
            'false_alarm'  => [107, 114, 128],
            'acknowledged' => [59,  130, 246],
            'responded'    => [16,  185, 129],
            'cancelled'    => [156, 163, 175],
            'monitoring'   => [139, 92,  246],
            'cleared'      => [20,  184, 166],
            'extreme'      => [127, 0,   0],
            'severe'       => [220, 38,  38],
            'moderate'     => [245, 158, 11],
            'minor'        => [34,  197, 94],
            'verified'     => [22,  163, 74],
            'unverified'   => [245, 158, 11],
            'inactive'     => [156, 163, 175],
            'in_review'    => [59,  130, 246],
            'in review'    => [59,  130, 246],
            'replied'      => [16,  185, 129],
            'high'         => [220, 38,  38],
            'critical'     => [127, 0,   0],
            'low'          => [34,  197, 94],
            'normal'       => [59,  130, 246],
        ];
        return $map[$s] ?? [100, 116, 139];
    }

    // ── Two-column chart layout helper ──────────────────────
    function TwoColSection($leftTitle, $rightTitle, $lR, $lG, $lB, $rR, $rG, $rB) {
        // Returns nothing; caller must position content manually
    }

    // ── Mini summary box ────────────────────────────────────
    function MiniStat($x, $y, $w, $h, $label, $value, $r, $g, $b) {
        $this->SetFillColor(255,255,255);
        $this->Rect($x, $y, $w, $h, 'F');
        $this->SetFillColor($r,$g,$b);
        $this->Rect($x, $y, 2.5, $h, 'F');

        $this->SetFont('Helvetica', 'B', 13);
        $this->SetTextColor($r,$g,$b);
        $this->SetXY($x+5, $y+1);
        $this->Cell($w-7, 8, $value, 0, 0, 'L');

        $this->SetFont('Helvetica', '', 6.5);
        $this->SetTextColor(100,110,130);
        $this->SetXY($x+5, $y+9);
        $this->Cell($w-7, 4, $label, 0, 0, 'L');
        $this->SetTextColor(0,0,0);
    }
}

// ════════════════════════════════════════════════════════════
// BUILD PDF
// ════════════════════════════════════════════════════════════
$pdf = new AdminReportPDF('P', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->SetAutoPageBreak(true, 18);
$pdf->SetMargins(14, 38, 14);
$pdf->setLogoPath(BASE_PATH . 'assets/images/logo.png');
$pdf->AddPage();

$margin = 14;
$pageW  = 182;   // 210 - 2*14

// ════════════════════════════════════════════════════════════
// PAGE 1  ·  SYSTEM SUMMARY + ANALYTICS CHARTS
// ════════════════════════════════════════════════════════════

// ── 4 Stat Cards ─────────────────────────────────────────────
$pdf->SectionTitle('System Overview', 15, 40, 80);

$cardW   = 43;
$cardH   = 29;
$cardGap = 3;
$startX  = $margin;
$startY  = $pdf->GetY();

$cards = [
    ['Total Users',       number_format($userStats['total'] ?? 0), ($userStats['active'] ?? 0) . ' active accounts',       59,  130, 246],
    ['Emergency Alerts',  number_format($alertStats['total'] ?? 0), ($alertStats['active'] ?? 0) . ' currently active',    239, 68,  68],
    ['Disaster Alerts',   number_format($totalDisasters),           $activeDisasters . ' active disasters',                245, 158, 11],
    ['Message Inquiries', number_format($msgStats['total'] ?? 0),   ($msgStats['pending'] ?? 0) . ' awaiting response',    16,  185, 129],
];

foreach ($cards as $i => $c) {
    $pdf->StatCard($startX + $i * ($cardW + $cardGap), $startY, $cardW, $cardH, $c[0], $c[1], $c[2], $c[3], $c[4], $c[5]);
}
$pdf->SetY($startY + $cardH + 7);

// ── Two-column: User Roles  |  Alert Status ───────────────────
$colW   = 86;
$colGap = 10;
$col1X  = $margin + 54;   // bar start for col 1
$col2X  = $margin + $colW + $colGap + 54;  // bar start for col 2

// --- Col 1: User Role Breakdown ---
$roleMap    = ['pwd'=>'PWD User','family'=>'Family Member','admin'=>'Administrator'];
$roleColors = [[59,130,246],[16,185,129],[139,92,246]];
$roleLabels = $roleValues = [];
$totalRoles = 0;
foreach ($chartUserRoles as $d) {
    $roleLabels[] = $roleMap[$d['role']] ?? ucfirst($d['role']);
    $roleValues[] = (int)$d['count'];
    $totalRoles  += (int)$d['count'];
}
$maxRole = max(array_merge($roleValues, [1]));

// Section headers side by side
$yBefore = $pdf->GetY();

$pdf->SetFillColor(230, 238, 255);
$pdf->Rect($margin, $yBefore, $colW, 6.5, 'F');
$pdf->SetFillColor(59, 130, 246); $pdf->Rect($margin, $yBefore, 2.5, 6.5, 'F');
$pdf->SetFont('Helvetica', 'B', 8); $pdf->SetTextColor(30, 60, 140);
$pdf->SetXY($margin+4, $yBefore); $pdf->Cell($colW-4, 6.5, 'USER ROLE BREAKDOWN', 0, 0, 'L');

$col2TitleX = $margin + $colW + $colGap;
$pdf->SetFillColor(255, 235, 235);
$pdf->Rect($col2TitleX, $yBefore, $colW, 6.5, 'F');
$pdf->SetFillColor(239, 68, 68); $pdf->Rect($col2TitleX, $yBefore, 2.5, 6.5, 'F');
$pdf->SetFont('Helvetica', 'B', 8); $pdf->SetTextColor(160, 30, 30);
$pdf->SetXY($col2TitleX+4, $yBefore); $pdf->Cell($colW-4, 6.5, 'ALERT STATUS BREAKDOWN', 0, 0, 'L');

$pdf->SetTextColor(0,0,0);
$pdf->SetY($yBefore + 8);

// Chart rows
$chartY1 = $pdf->GetY();

// Alert status data
$alertMap    = ['active'=>'Active','resolved'=>'Resolved','false_alarm'=>'False Alarm','pending'=>'Pending','acknowledged'=>'Acknowledged','responded'=>'Responded','cancelled'=>'Cancelled'];
$alertColors = [[239,68,68],[16,185,129],[245,158,11],[148,163,184],[59,130,246],[139,92,246],[156,163,175]];
$alertLabels = $alertValues = [];
foreach ($chartAlertStatus as $d) {
    $alertLabels[] = $alertMap[$d['status']] ?? ucfirst($d['status']);
    $alertValues[] = (int)$d['count'];
}
$maxAlert = max(array_merge($alertValues, [1]));

// Draw col1 bars (role)
$barH = 7; $barGap = 3;
foreach ($roleValues as $i => $val) {
    $barLen = $maxRole > 0 ? round(($val / $maxRole) * ($colW - 54)) : 0;
    list($r,$g,$b) = $roleColors[$i % count($roleColors)];
    $by = $chartY1 + $i * ($barH + $barGap);
    $bx = $margin + 52;

    // Track
    $pdf->SetFillColor(235,238,245); $pdf->Rect($bx, $by, $colW-54, $barH, 'F');
    // Bar
    if ($barLen > 0) { $pdf->SetFillColor($r,$g,$b); $pdf->Rect($bx, $by, $barLen, $barH, 'F'); }
    // Label
    $pdf->SetFont('Helvetica','',7); $pdf->SetTextColor(60,70,90);
    $pdf->SetXY($margin, $by+1); $pdf->Cell(50, $barH-2, $roleLabels[$i], 0, 0, 'R');
    // Value
    $total = array_sum($roleValues);
    $pct = $total > 0 ? round($val/$total*100) : 0;
    $pdf->SetFont('Helvetica','B',7); $pdf->SetTextColor($r,$g,$b);
    $pdf->SetXY($bx + $barLen + 1, $by+1);
    $pdf->Cell(18, $barH-2, $val . ' ('.$pct.'%)', 0, 0, 'L');
}
$pdf->SetTextColor(0,0,0);

// Draw col2 bars (alert status)
foreach ($alertValues as $i => $val) {
    $barLen2 = $maxAlert > 0 ? round(($val / $maxAlert) * ($colW - 54)) : 0;
    list($r,$g,$b) = $alertColors[$i % count($alertColors)];
    $bx2 = $col2TitleX + 52;
    $by2 = $chartY1 + $i * ($barH + $barGap);

    $pdf->SetFillColor(235,238,245); $pdf->Rect($bx2, $by2, $colW-54, $barH, 'F');
    if ($barLen2 > 0) { $pdf->SetFillColor($r,$g,$b); $pdf->Rect($bx2, $by2, $barLen2, $barH, 'F'); }
    $pdf->SetFont('Helvetica','',7); $pdf->SetTextColor(60,70,90);
    $pdf->SetXY($col2TitleX, $by2+1); $pdf->Cell(50, $barH-2, $alertLabels[$i], 0, 0, 'R');
    $total2 = array_sum($alertValues);
    $pct2 = $total2 > 0 ? round($val/$total2*100) : 0;
    $pdf->SetFont('Helvetica','B',7); $pdf->SetTextColor($r,$g,$b);
    $pdf->SetXY($bx2 + $barLen2 + 1, $by2+1);
    $pdf->Cell(18, $barH-2, $val . ' ('.$pct2.'%)', 0, 0, 'L');
}
$pdf->SetTextColor(0,0,0);

$rowsMax = max(count($roleValues), count($alertValues));
$pdf->SetY($chartY1 + $rowsMax * ($barH + $barGap) + 5);

$pdf->Divider();

// ── Two-column: Alert Types  |  Message Categories ────────────

$yBefore2 = $pdf->GetY();

// Alert Type section header
$pdf->SetFillColor(255, 240, 230);
$pdf->Rect($margin, $yBefore2, $colW, 6.5, 'F');
$pdf->SetFillColor(245, 158, 11); $pdf->Rect($margin, $yBefore2, 2.5, 6.5, 'F');
$pdf->SetFont('Helvetica','B',8); $pdf->SetTextColor(140,80,10);
$pdf->SetXY($margin+4, $yBefore2); $pdf->Cell($colW-4, 6.5, 'ALERT TYPE DISTRIBUTION', 0, 0, 'L');

// Message Category section header
$pdf->SetFillColor(230, 255, 245);
$pdf->Rect($col2TitleX, $yBefore2, $colW, 6.5, 'F');
$pdf->SetFillColor(16, 185, 129); $pdf->Rect($col2TitleX, $yBefore2, 2.5, 6.5, 'F');
$pdf->SetFont('Helvetica','B',8); $pdf->SetTextColor(10,100,70);
$pdf->SetXY($col2TitleX+4, $yBefore2); $pdf->Cell($colW-4, 6.5, 'MESSAGE INQUIRY CATEGORIES', 0, 0, 'L');

$pdf->SetTextColor(0,0,0);
$pdf->SetY($yBefore2 + 8);

$chartY2 = $pdf->GetY();

// Alert type colors
$typeMap    = ['sos'=>'Emergency SOS','shake'=>'Shake Alert','panic_click'=>'Panic Button','medical'=>'Medi-Alert','assistance'=>'Assistance','fall_detection'=>'Fall Detection'];
$typeColors = [[239,68,68],[245,158,11],[139,92,246],[59,130,246],[16,185,129],[236,72,153]];
$typeLabels = $typeValues = [];
foreach ($alertTypeData as $d) {
    $typeLabels[] = $typeMap[$d['alert_type']] ?? ucfirst(str_replace('_',' ',$d['alert_type']));
    $typeValues[] = (int)$d['cnt'];
}
$maxType = max(array_merge($typeValues, [1]));

foreach ($typeValues as $i => $val) {
    $barLen3 = $maxType > 0 ? round(($val / $maxType) * ($colW - 54)) : 0;
    list($r,$g,$b) = $typeColors[$i % count($typeColors)];
    $by3 = $chartY2 + $i * ($barH + $barGap);
    $bx3 = $margin + 52;

    $pdf->SetFillColor(235,238,245); $pdf->Rect($bx3, $by3, $colW-54, $barH, 'F');
    if ($barLen3 > 0) { $pdf->SetFillColor($r,$g,$b); $pdf->Rect($bx3, $by3, $barLen3, $barH, 'F'); }
    $pdf->SetFont('Helvetica','',7); $pdf->SetTextColor(60,70,90);
    $pdf->SetXY($margin, $by3+1); $pdf->Cell(50, $barH-2, $typeLabels[$i], 0, 0, 'R');
    $total3 = array_sum($typeValues);
    $pct3 = $total3 > 0 ? round($val/$total3*100) : 0;
    $pdf->SetFont('Helvetica','B',7); $pdf->SetTextColor($r,$g,$b);
    $pdf->SetXY($bx3 + $barLen3 + 1, $by3+1);
    $pdf->Cell(18, $barH-2, $val . ' ('.$pct3.'%)', 0, 0, 'L');
}
$pdf->SetTextColor(0,0,0);

// Message categories
$catMap    = ['general'=>'General','technical'=>'Technical','account'=>'Account','emergency'=>'Emergency','feedback'=>'Feedback','billing'=>'Billing','accessibility'=>'Accessibility'];
$catColors = [[59,130,246],[245,158,11],[16,185,129],[239,68,68],[139,92,246],[236,72,153],[20,184,166]];
$catLabels = $catValues = [];
foreach ($msgCategoryData as $d) {
    $catLabels[] = $catMap[$d['category']] ?? ucfirst($d['category']);
    $catValues[] = (int)$d['cnt'];
}
$maxCat = max(array_merge($catValues, [1]));

foreach ($catValues as $i => $val) {
    $barLen4 = $maxCat > 0 ? round(($val / $maxCat) * ($colW - 54)) : 0;
    list($r,$g,$b) = $catColors[$i % count($catColors)];
    $by4 = $chartY2 + $i * ($barH + $barGap);
    $bx4 = $col2TitleX + 52;

    $pdf->SetFillColor(235,238,245); $pdf->Rect($bx4, $by4, $colW-54, $barH, 'F');
    if ($barLen4 > 0) { $pdf->SetFillColor($r,$g,$b); $pdf->Rect($bx4, $by4, $barLen4, $barH, 'F'); }
    $pdf->SetFont('Helvetica','',7); $pdf->SetTextColor(60,70,90);
    $pdf->SetXY($col2TitleX, $by4+1); $pdf->Cell(50, $barH-2, $catLabels[$i], 0, 0, 'R');
    $total4 = array_sum($catValues);
    $pct4 = $total4 > 0 ? round($val/$total4*100) : 0;
    $pdf->SetFont('Helvetica','B',7); $pdf->SetTextColor($r,$g,$b);
    $pdf->SetXY($bx4 + $barLen4 + 1, $by4+1);
    $pdf->Cell(18, $barH-2, $val . ' ('.$pct4.'%)', 0, 0, 'L');
}
$pdf->SetTextColor(0,0,0);

$rowsMax2 = max(count($typeValues), count($catValues));
$pdf->SetY($chartY2 + $rowsMax2 * ($barH + $barGap) + 5);

$pdf->Divider();

// ── User Registration Trend (last 6 months) ────────────────────
if (!empty($monthlyUsers)) {
    $pdf->SectionTitle('User Registration Trend  (Last 6 Months)', 139, 92, 246);

    $trendY   = $pdf->GetY();
    $trendX   = $margin + 28;
    $trendW   = $pageW - 28;
    $maxUsers = max(array_column($monthlyUsers, 'cnt'));
    $maxBar   = max($maxUsers, 1);
    $barWide  = floor(($trendW) / count($monthlyUsers)) - 3;
    $barMaxH  = 22;

    foreach ($monthlyUsers as $j => $mu) {
        $bh  = $maxBar > 0 ? round(($mu['cnt'] / $maxBar) * $barMaxH) : 1;
        $bh  = max($bh, 1);
        $bx  = $trendX + $j * ($barWide + 3);
        $byT = $trendY + $barMaxH - $bh;

        $pdf->SetFillColor(139, 92, 246);
        $pdf->Rect($bx, $byT, $barWide, $bh, 'F');

        // Count on top
        $pdf->SetFont('Helvetica','B',6.5); $pdf->SetTextColor(100,60,200);
        $pdf->SetXY($bx, $byT - 4);
        $pdf->Cell($barWide, 4, $mu['cnt'], 0, 0, 'C');

        // Month label below
        $pdf->SetFont('Helvetica','',6); $pdf->SetTextColor(80,90,110);
        $pdf->SetXY($bx, $trendY + $barMaxH + 1);
        $pdf->Cell($barWide, 4, $mu['month_label'], 0, 0, 'C');
    }
    $pdf->SetTextColor(0,0,0);
    $pdf->SetY($trendY + $barMaxH + 8);
    $pdf->Divider();
}

// ════════════════════════════════════════════════════════════
// PAGE 2  ·  EMERGENCY ALERTS TABLE
// ════════════════════════════════════════════════════════════
$pdf->AddPage();

$pdf->SectionTitle('Recent Emergency Alerts', 239, 68, 68);

// Mini summary row
$miniY = $pdf->GetY();
$miniH = 16;
$miniW = 44;
$miniGap = 3;
$miniStats = [
    ['Total Alerts',    number_format($alertStats['total'] ?? 0),    239, 68,  68],
    ['Active',          (string)($alertStats['active'] ?? 0),         220, 38,  38],
    ['Resolved Today',  (string)($alertStats['resolved_today'] ?? 0), 16,  185, 129],
    ['Pending',         (string)($alertStats['pending'] ?? 0),        245, 158, 11],
];
foreach ($miniStats as $mi => $ms) {
    $pdf->MiniStat($margin + $mi * ($miniW + $miniGap), $miniY, $miniW, $miniH, $ms[0], $ms[1], $ms[2], $ms[3], $ms[4]);
}
$pdf->SetY($miniY + $miniH + 5);

// Table
$prefixMap = ['sos'=>'SOS','shake'=>'SHAKE','panic_click'=>'PANIC','medical'=>'MED','assistance'=>'ASST','fall_detection'=>'FALL'];
$typeMap2  = ['sos'=>'Emergency SOS','shake'=>'Shake Alert','panic_click'=>'Panic Button','medical'=>'Medi-Alert','assistance'=>'Assistance','fall_detection'=>'Fall Detection'];

$alertHeaders = ['Alert ID', 'User', 'Type', 'Priority', 'Status', 'Location', 'Date'];
$alertWidths  = [22, 36, 28, 18, 20, 40, 28];
$alertAligns  = ['C','L','L','C','C','L','C'];
$alertRows    = [];

foreach ($recentAlertsData as $row) {
    $prefix   = $prefixMap[$row['alert_type']] ?? 'ALERT';
    $alertId  = '#'.$prefix.'-'.str_pad($row['id'], 4, '0', STR_PAD_LEFT);
    $loc      = mb_substr($row['location_address'] ?? 'N/A', 0, 28);
    $alertRows[] = [
        $alertId,
        mb_substr($row['user_name'] ?? 'Unknown', 0, 20),
        $typeMap2[$row['alert_type']] ?? ucfirst(str_replace('_',' ',$row['alert_type'])),
        ucfirst($row['priority'] ?? 'normal'),
        ucfirst($row['status'] ?? 'pending'),
        $loc,
        date('M d, Y', strtotime($row['created_at'])),
    ];
}
if (empty($alertRows)) $alertRows[] = ['-','No alerts found','','','','',''];

$pdf->DataTable($alertHeaders, $alertRows, $alertWidths, $alertAligns, [15,40,80], [3,4]);

// ── Message Inquiry Status breakdown ──────────────────────────
$pdf->SectionTitle('Message Inquiry Status', 16, 185, 129);

$msgMiniY = $pdf->GetY();
$msgMiniStats = [
    ['Total Messages', number_format($msgStats['total'] ?? 0),   16,  185, 129],
    ['Pending',        (string)($msgStats['pending'] ?? 0),       245, 158, 11],
    ['Resolved',       (string)($msgStats['resolved'] ?? 0),      22,  163, 74],
    ['In Review',      (string)($msgStats['in_review'] ?? 0),     59,  130, 246],
];
foreach ($msgMiniStats as $mi => $ms) {
    $pdf->MiniStat($margin + $mi * ($miniW + $miniGap), $msgMiniY, $miniW, $miniH, $ms[0], $ms[1], $ms[2], $ms[3], $ms[4]);
}
$pdf->SetY($msgMiniY + $miniH + 5);

// Message status bar chart
$msgStatusLabels = $msgStatusValues = [];
$msColorMap = ['pending'=>[245,158,11],'resolved'=>[22,163,74],'in_review'=>[59,130,246],'replied'=>[16,185,129]];
$msColors   = [];
foreach ($msgStatusData as $d) {
    $msgStatusLabels[] = ucwords(str_replace('_',' ',$d['status']));
    $msgStatusValues[] = (int)$d['cnt'];
    $msColors[]        = $msColorMap[$d['status']] ?? [100,116,139];
}
$maxMsgStatus = max(array_merge($msgStatusValues,[1]));

$msY = $pdf->GetY();
$msX = $margin + 42;
foreach ($msgStatusValues as $i => $val) {
    $barLen5 = $maxMsgStatus > 0 ? round(($val / $maxMsgStatus) * 100) : 0;
    list($r,$g,$b) = $msColors[$i];
    $by5 = $msY + $i * ($barH + $barGap);
    $pdf->SetFillColor(235,238,245); $pdf->Rect($msX, $by5, 100, $barH, 'F');
    if ($barLen5 > 0) { $pdf->SetFillColor($r,$g,$b); $pdf->Rect($msX, $by5, $barLen5, $barH, 'F'); }
    $pdf->SetFont('Helvetica','',7); $pdf->SetTextColor(60,70,90);
    $pdf->SetXY($margin, $by5+1); $pdf->Cell(40, $barH-2, $msgStatusLabels[$i], 0, 0, 'R');
    $totMs = array_sum($msgStatusValues);
    $pctMs = $totMs > 0 ? round($val/$totMs*100) : 0;
    $pdf->SetFont('Helvetica','B',7); $pdf->SetTextColor($r,$g,$b);
    $pdf->SetXY($msX + $barLen5 + 2, $by5+1);
    $pdf->Cell(20, $barH-2, $val . ' ('.$pctMs.'%)', 0, 0, 'L');
}
$pdf->SetTextColor(0,0,0);
$pdf->SetY($msY + count($msgStatusValues) * ($barH + $barGap) + 6);

$pdf->Divider();

// ════════════════════════════════════════════════════════════
// PAGE 3  ·  USER TABLE + DISASTER ALERTS TABLE
// ════════════════════════════════════════════════════════════
$pdf->AddPage();

// ── Recently Registered Users ──────────────────────────────────
$pdf->SectionTitle('Recently Registered Users', 59, 130, 246);

$userHeaders = ['Name', 'Email', 'Role', 'Phone', 'Verified', 'Status', 'Joined'];
$userWidths  = [35, 48, 20, 28, 17, 17, 17];
$userAligns  = ['L','L','C','C','C','C','C'];
$roleMap3    = ['pwd'=>'PWD User','family'=>'Family','admin'=>'Admin'];

$userRows = [];
foreach ($recentUsersData as $row) {
    $userRows[] = [
        mb_substr($row['full_name'] ?? 'N/A', 0, 22),
        mb_substr($row['email'] ?? '', 0, 32),
        $roleMap3[$row['role']] ?? ucfirst($row['role']),
        $row['phone_number'] ?? 'N/A',
        $row['verified'],
        $row['active_status'],
        $row['joined'] ?? '',
    ];
}
if (empty($userRows)) $userRows[] = ['No users found','','','','','',''];

$pdf->DataTable($userHeaders, $userRows, $userWidths, $userAligns, [15,40,80], [4,5]);

// ── Disaster Alerts Table ──────────────────────────────────────
$pdf->SectionTitle('Disaster Alerts', 245, 158, 11);

// Mini stats
$disasterMiniY = $pdf->GetY();
$disasterStats = [
    ['Total Disasters',  number_format($totalDisasters),   245, 158, 11],
    ['Active Disasters', number_format($activeDisasters),  220, 38,  38],
    ['Cleared',          number_format($totalDisasters - $activeDisasters > 0 ? $totalDisasters - $activeDisasters : 0), 22, 163, 74],
];
foreach ($disasterStats as $mi => $ms) {
    $pdf->MiniStat($margin + $mi * ($miniW + $miniGap), $disasterMiniY, $miniW, $miniH, $ms[0], $ms[1], $ms[2], $ms[3], $ms[4]);
}
$pdf->SetY($disasterMiniY + $miniH + 5);

if (!empty($disasterData)) {
    $disHeaders = ['#', 'Alert Type', 'Severity', 'Location', 'Status', 'Issued Date'];
    $disWidths  = [10, 45, 22, 58, 22, 25];
    $disAligns  = ['C','L','C','L','C','C'];
    $disRows    = [];

    foreach ($disasterData as $i => $d) {
        $disRows[] = [
            $i + 1,
            mb_substr(ucfirst($d['alert_type'] ?? 'N/A'), 0, 30),
            ucfirst($d['severity'] ?? 'N/A'),
            mb_substr($d['location'] ?? 'N/A', 0, 40),
            ucfirst($d['status'] ?? 'N/A'),
            $d['issued_date'] ?? '',
        ];
    }
    $pdf->DataTable($disHeaders, $disRows, $disWidths, $disAligns, [140, 90, 10], [2,4]);
} else {
    $pdf->SetFont('Helvetica','I',8.5);
    $pdf->SetTextColor(130,140,160);
    $pdf->Cell(0, 8, 'No disaster alerts on record.', 0, 1, 'C');
    $pdf->SetTextColor(0,0,0);
    $pdf->Ln(3);
}

$pdf->Divider();

// ── Report Footer Note ─────────────────────────────────────────
$pdf->SetFillColor(240, 245, 255);
$pdf->Rect($margin, $pdf->GetY(), $pageW, 14, 'F');
$pdf->SetFont('Helvetica','B',8);
$pdf->SetTextColor(15,40,80);
$pdf->SetX($margin + 3);
$pdf->Cell(0, 5, 'Silent Signal  |  Confidential Admin Analytics Report', 0, 1, 'L');
$pdf->SetFont('Helvetica','',7);
$pdf->SetTextColor(100,110,140);
$pdf->SetX($margin + 3);
$pdf->Cell(0, 5, 'This report was auto-generated on ' . date('F j, Y \a\t H:i:s') . '.  Data reflects current database state.', 0, 1, 'L');
$pdf->SetX($margin + 3);
$pdf->Cell(0, 4, 'For internal use only. Do not distribute without authorization.', 0, 1, 'L');
$pdf->SetTextColor(0,0,0);

// ── Output ─────────────────────────────────────────────────────
$pdf->Output('D', 'SilentSignal_Admin_Report_' . date('Ymd_His') . '.pdf');
exit();