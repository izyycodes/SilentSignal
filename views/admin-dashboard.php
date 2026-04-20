<?php
// views/admin/dashboard.php
$pageStyles = [BASE_URL . 'assets/css/admin-dashboard.css'];
require_once VIEW_PATH . 'includes/dashboard-header.php';
?>


<div class="dashboard-container">
    <!-- 1Admin Dashboard Welcome Header -->
    <div class="admin-dashboard-header">
        <div class="admin-dashboard-header-content">
            <h1>Welcome, <?php echo $_SESSION['user_name']; ?></h1>
            <p>Administrator Dashboard</p>
        </div>
        <div class="header-actions">
            <button class="header-btn" onclick="window.location.reload()">
                <i class="ri-refresh-line"></i>
            </button>
            <button class="header-btn">
                <i class="ri-notification-3-line"></i>
                <span class="badge">3</span>
            </button>
            <a href="<?php echo BASE_URL; ?>index.php?action=dashboard" class="header-btn" title="User View">
                <i class="ri-eye-line"></i>
            </a>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="stats-grid">
        <div class="stat-card blue">
            <div class="stat-icon">
                <i class="ri-user-line"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value"><?php echo number_format($stats['totalUsers']); ?></div>
                <div class="stat-label">Total Users</div>
                <div class="stat-change positive">
                    <i class="ri-arrow-up-line"></i> <?php echo $stats['userGrowth']; ?>% this month
                </div>
            </div>
        </div>

        <div class="stat-card red">
            <div class="stat-icon">
                <i class="ri-alarm-warning-line"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value"><?php echo $stats['activeAlerts']; ?></div>
                <div class="stat-label">Active Alerts</div>
                <div class="stat-change">
                    <?php echo $stats['resolvedToday']; ?> resolved today
                </div>
            </div>
        </div>

        <div class="stat-card orange">
            <div class="stat-icon">
                <i class="ri-earth-line"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value"><?php echo $stats['disasterAlerts']; ?></div>
                <div class="stat-label">Disaster Alerts</div>
                <div class="stat-change">
                    <?php echo $stats['activeDisasters']; ?> active
                </div>
            </div>
        </div>

        <div class="stat-card green">
            <div class="stat-icon">
                <i class="ri-message-3-line"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value"><?php echo $stats['messageInquiries']; ?></div>
                <div class="stat-label">Message Inquiries</div>
                <div class="stat-change">
                    <?php echo $stats['pendingMessages']; ?> pending
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Grid -->
    <div class="quick-actions-section">
        <h2><i class="ri-flashlight-line"></i> Quick Actions</h2>
        <div class="quick-actions-grid">
            <div class="action-card" onclick="window.location.href='<?php echo BASE_URL; ?>index.php?action=admin-users'">
                <div class="action-icon blue">
                    <i class="ri-user-settings-line"></i>
                </div>
                <h3>Manage Users</h3>
                <p>View and manage user accounts</p>
                <span class="action-arrow"><i class="ri-arrow-right-line"></i></span>
            </div>

            <div class="action-card" onclick="window.location.href='<?php echo BASE_URL; ?>index.php?action=admin-emergency-alerts'">
                <div class="action-icon red">
                    <i class="ri-alarm-warning-fill"></i>
                </div>
                <h3>Emergency Alerts</h3>
                <p>Monitor emergency situations</p>
                <span class="action-arrow"><i class="ri-arrow-right-line"></i></span>
            </div>

            <div class="action-card" onclick="window.location.href='<?php echo BASE_URL; ?>index.php?action=admin-disaster-alerts'">
                <div class="action-icon orange">
                    <i class="ri-flood-line"></i>
                </div>
                <h3>Disaster Alerts</h3>
                <p>Manage disaster notifications</p>
                <span class="action-arrow"><i class="ri-arrow-right-line"></i></span>
            </div>

            <div class="action-card" onclick="window.location.href='<?php echo BASE_URL; ?>index.php?action=admin-messages'">
                <div class="action-icon green">
                    <i class="ri-chat-3-fill"></i>
                </div>
                <h3>Message Inquiries</h3>
                <p>Respond to user messages</p>
                <span class="action-arrow"><i class="ri-arrow-right-line"></i></span>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="dashboard-section">
        <div class="section-header">
            <h2><i class="ri-time-line"></i> Recent Activity</h2>
            <a href="#" class="view-all-link">View All <i class="ri-arrow-right-line"></i></a>
        </div>
        <div class="activity-list">
            <?php foreach ($recentActivity as $activity): ?>
                <div class="activity-item">
                    <div class="activity-icon <?php echo $activity['type']; ?>">
                        <i class="<?php echo $activity['icon']; ?>"></i>
                    </div>
                    <div class="activity-content">
                        <div class="activity-title"><?php echo $activity['title']; ?></div>
                        <div class="activity-desc"><?php echo $activity['description']; ?></div>
                    </div>
                    <div class="activity-time"><?php echo $activity['time']; ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="charts-section">
        <div class="charts-section-header">
            <h2><i class="ri-bar-chart-2-line"></i> Analytics Overview</h2>
            <div class="charts-header-right">
                <!-- Period Filter -->
                <div class="chart-period-filter">
                    <button class="period-btn" data-period="daily">Daily</button>
                    <button class="period-btn active" data-period="monthly">Monthly</button>
                    <button class="period-btn" data-period="weekly">Weekly</button>
                    <button class="period-btn" data-period="yearly">Yearly</button>
                </div>
                <a href="<?php echo BASE_URL; ?>index.php?action=admin-print-pdf" target="_blank" class="btn-print-pdf">
                    <i class="ri-file-pdf-2-line"></i> Print PDF Report
                </a>
            </div>
        </div>
        <div class="charts-grid">

            <!-- User Role Breakdown Doughnut -->
            <div class="chart-card">
                <div class="chart-card-header">
                    <div class="chart-card-title">
                        <i class="ri-group-line"></i> User Role Breakdown
                    </div>
                    <span class="chart-badge blue">By Role</span>
                </div>
                <div class="chart-wrap">
                    <canvas id="userRolesChart"></canvas>
                </div>
            </div>

            <!-- Alert Status Doughnut -->
            <div class="chart-card">
                <div class="chart-card-header">
                    <div class="chart-card-title">
                        <i class="ri-alarm-warning-line"></i> Alert Status
                    </div>
                    <span class="chart-badge red">Breakdown</span>
                </div>
                <div class="chart-wrap">
                    <canvas id="alertStatusChart"></canvas>
                </div>
            </div>

            <!-- Monthly Activity Bar Chart -->
            <div class="chart-card chart-card-wide">
                <div class="chart-card-header">
                    <div class="chart-card-title">
                    <i class="ri-bar-chart-line"></i> <span id="activityChartTitle">Activity Overview</span>
                    </div>
                    <span class="chart-badge green">Alerts vs Messages</span>
                </div>
                <div class="chart-wrap">
                    <canvas id="monthlyActivityChart"></canvas>
                </div>
            </div>

            <!-- Message Categories Doughnut -->
            <div class="chart-card chart-card-wide">
                <div class="chart-card-header">
                    <div class="chart-card-title">
                        <i class="ri-message-3-line"></i> Message Inquiry Categories
                    </div>
                    <span class="chart-badge orange">By Category</span>
                </div>
                <div class="chart-wrap">
                    <canvas id="msgCategoriesChart"></canvas>
                </div>
            </div>

        </div>
    </div>

    <!-- SMS Broadcast Panel -->
    <div class="dashboard-section">
        <div class="section-header">
            <h2><i class="ri-message-2-line"></i> SMS Broadcast</h2>
            <span class="sms-badge"><i class="ri-signal-tower-line"></i> PhilSMS Active</span>
        </div>
        <div class="sms-broadcast-panel">
            <div class="sms-form-group">
                <label for="smsRecipients"><i class="ri-user-line"></i> Recipient Phone Numbers</label>
                <input type="text" id="smsRecipients" placeholder="e.g. 09991234567, 09997654321 (comma-separated)" />
            </div>
            <div class="sms-form-group">
                <label for="smsMessage"><i class="ri-chat-1-line"></i> Message <span id="smsCharCount">0 / 160</span></label>
                <textarea id="smsMessage" rows="3" maxlength="160" placeholder="Type your emergency broadcast message here..."></textarea>
            </div>
            <div class="sms-form-actions">
                <button class="btn-sms-send" id="btnSmsSend">
                    <i class="ri-send-plane-fill"></i> Send SMS Broadcast
                </button>
                <button class="btn-sms-clear" id="btnSmsClear">
                    <i class="ri-delete-bin-line"></i> Clear
                </button>
            </div>
            <div id="smsResult" class="sms-result" style="display:none;"></div>
        </div>
    </div>

    <!-- System Health -->
    <div class="dashboard-section">
        <div class="section-header">
            <h2><i class="ri-pulse-line"></i> System Health</h2>
        </div>
        <div class="health-grid">
            <div class="health-card">
                <div class="health-label">Server Status</div>
                <div class="health-value">
                    <span class="status-dot online"></span> Online
                </div>
            </div>
            <div class="health-card">
                <div class="health-label">Database</div>
                <div class="health-value">
                    <span class="status-dot online"></span> Connected
                </div>
            </div>
            <div class="health-card">
                <div class="health-label">SMS Service</div>
                <div class="health-value">
                    <span class="status-dot online"></span> Active
                </div>
            </div>
            <div class="health-card">
                <div class="health-label">API Status</div>
                <div class="health-value">
                    <span class="status-dot online"></span> Operational
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Pass chart data to JS -->
<script>
const BASE_URL             = '<?php echo BASE_URL; ?>';
const chartUserRoles       = <?php echo $chartUserRoles; ?>;
const chartAlertStatus     = <?php echo $chartAlertStatus; ?>;
const chartMonthlyActivity = <?php echo $chartMonthlyActivity; ?>;
const chartMsgCategories   = <?php echo $chartMsgCategories; ?>;
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="<?php echo BASE_URL; ?>assets/js/admin-dashboard.js"></script>

<script>
// ── SMS Broadcast ────────────────────────────────────────────
(function () {
    const msgEl      = document.getElementById('smsMessage');
    const countEl    = document.getElementById('smsCharCount');
    const sendBtn    = document.getElementById('btnSmsSend');
    const clearBtn   = document.getElementById('btnSmsClear');
    const resultEl   = document.getElementById('smsResult');
    const recipEl    = document.getElementById('smsRecipients');

    if (!msgEl) return;

    msgEl.addEventListener('input', () => {
        countEl.textContent = msgEl.value.length + ' / 160';
        countEl.style.color = msgEl.value.length > 140 ? '#ef4444' : '';
    });

    clearBtn.addEventListener('click', () => {
        msgEl.value = '';
        recipEl.value = '';
        countEl.textContent = '0 / 160';
        resultEl.style.display = 'none';
    });

    sendBtn.addEventListener('click', async () => {
        const phones  = recipEl.value.trim();
        const message = msgEl.value.trim();

        if (!phones) { showSmsResult('error', 'Please enter at least one phone number.'); return; }
        if (!message) { showSmsResult('error', 'Please type a message before sending.'); return; }

        sendBtn.disabled = true;
        sendBtn.innerHTML = '<i class="ri-loader-4-line ri-spin"></i> Sending...';

        try {
            const res = await fetch(BASE_URL + 'index.php?action=send-philsms', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ phones, message })
            });
            const data = await res.json();
            if (data.success) {
                showSmsResult('success', '✓ ' + data.message);
                msgEl.value = '';
                countEl.textContent = '0 / 160';
            } else {
                showSmsResult('error', '✗ ' + (data.message || 'Failed to send SMS.'));
            }
        } catch (err) {
            showSmsResult('error', '✗ Network error. Please try again.');
        } finally {
            sendBtn.disabled = false;
            sendBtn.innerHTML = '<i class="ri-send-plane-fill"></i> Send SMS Broadcast';
        }
    });

    function showSmsResult(type, msg) {
        resultEl.style.display = 'block';
        resultEl.className = 'sms-result sms-result-' + type;
        resultEl.textContent = msg;
    }
})();
</script>

<?php require_once VIEW_PATH . 'includes/dashboard-footer.php'; ?>