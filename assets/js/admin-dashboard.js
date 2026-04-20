// ============================================================
// ADMIN DASHBOARD — Chart.js Analytics  (with period filters)
// ============================================================

document.addEventListener('DOMContentLoaded', function () {

    // ── Chart instances (kept so we can destroy & re-draw) ──
    const charts = {
        userRoles:       null,
        alertStatus:     null,
        monthlyActivity: null,
        msgCategories:   null,
    };

    // ── Theme helpers ────────────────────────────────────────
    function getThemeVars() {
        const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
        return {
            gridColor:  isDark ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.06)',
            labelColor: isDark ? '#94a3b8' : '#64748b',
            borderCol:  isDark ? '#1e293b' : '#fff',
        };
    }

    // ── Draw User Role Doughnut ──────────────────────────────
    function drawUserRoles(data) {
        const ctx = document.getElementById('userRolesChart');
        if (!ctx || !data || !data.length) return;
        if (charts.userRoles) charts.userRoles.destroy();
        const { labelColor, borderCol } = getThemeVars();
        const labelMap = { pwd: 'PWD User', family: 'Family Member', admin: 'Administrator' };
        const colors   = ['#3b82f6', '#10b981', '#8b5cf6'];
        charts.userRoles = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels:   data.map(d => labelMap[d.role] || d.role),
                datasets: [{ data: data.map(d => parseInt(d.count)), backgroundColor: colors, borderWidth: 2, borderColor: borderCol, hoverOffset: 8 }]
            },
            options: {
                responsive: true, maintainAspectRatio: false, cutout: '65%',
                plugins: { legend: { position: 'bottom', labels: { color: labelColor, padding: 16, font: { size: 12 } } }, tooltip: { mode: 'index' } }
            }
        });
    }

    // ── Draw Alert Status Doughnut ───────────────────────────
    function drawAlertStatus(data) {
        const ctx = document.getElementById('alertStatusChart');
        if (!ctx) return;

        // Destroy existing chart before redrawing
        if (charts.alertStatus) {
            charts.alertStatus.destroy();
            charts.alertStatus = null;
        }

        // Show empty state if no data
        if (!data || !data.length) {
            const parent = ctx.closest('.chart-wrap');
            if (parent) parent.innerHTML = '<div style="display:flex;align-items:center;justify-content:center;height:100%;color:#94a3b8;font-size:13px;">No data for this period</div>';
            return;
        }

        const { labelColor, borderCol } = getThemeVars();
        const labelMap = {
            active:       'Active',
            resolved:     'Resolved',
            false_alarm:  'False Alarm',
            pending:      'Pending',
            acknowledged: 'Acknowledged',
            responded:    'Responded',
            cancelled:    'Cancelled',
        };
        const colors = ['#ef4444', '#10b981', '#f59e0b', '#94a3b8', '#3b82f6', '#8b5cf6', '#ec4899'];
        const labels = data.map(d => labelMap[d.status] || d.status);
        const values = data.map(d => parseInt(d.count));

        charts.alertStatus = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels,
                datasets: [{
                    data:            values,
                    backgroundColor: colors.slice(0, values.length),
                    borderWidth:     2,
                    borderColor:     borderCol,
                    hoverOffset:     8,
                }]
            },
            options: {
                responsive:          true,
                maintainAspectRatio: false,
                cutout:              '65%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels:   { color: labelColor, padding: 16, font: { size: 12 } }
                    },
                    tooltip: { mode: 'index' }
                }
            }
        });
    }

    // ── Draw Activity Bar Chart ──────────────────────────────
    function drawActivityChart(data, period) {
        const titleEl = document.getElementById('activityChartTitle');
            if (titleEl) {
                const titles = {
                    daily:   'Daily Activity',
                    weekly:  'Weekly Activity',
                    monthly: 'Monthly Activity',
                    yearly:  'Yearly Activity'
                };
                titleEl.textContent = titles[period] || 'Activity Overview';
            }

        const ctx = document.getElementById('monthlyActivityChart');
        if (!ctx || !data) return;
        if (charts.monthlyActivity) charts.monthlyActivity.destroy();
        const { gridColor, labelColor } = getThemeVars();

        const labelsSet = [];
        data.forEach(d => { if (!labelsSet.includes(d.period_label)) labelsSet.push(d.period_label); });

        const alertsData   = labelsSet.map(l => { const f = data.find(d => d.period_label === l && d.type === 'alerts');   return f ? parseInt(f.count) : 0; });
        const messagesData = labelsSet.map(l => { const f = data.find(d => d.period_label === l && d.type === 'messages'); return f ? parseInt(f.count) : 0; });

        const periodLabel = { daily: 'Last 30 Days', weekly: 'Last 12 Weeks', monthly: 'Last 6 Months', yearly: 'Last 5 Years' };
        const badge = document.querySelector('#monthlyActivityChart')?.closest('.chart-card')?.querySelector('.chart-badge');
        if (badge) badge.textContent = periodLabel[period] || 'Alerts vs Messages';

        charts.monthlyActivity = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labelsSet,
                datasets: [
                    { label: 'Emergency Alerts',  data: alertsData,   backgroundColor: 'rgba(239,68,68,0.8)',  borderRadius: 6, borderSkipped: false },
                    { label: 'Message Inquiries', data: messagesData, backgroundColor: 'rgba(16,185,129,0.8)', borderRadius: 6, borderSkipped: false },
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top', labels: { color: labelColor, padding: 16, font: { size: 12 } } },
                    tooltip: { mode: 'index', intersect: false }
                },
                scales: {
                    x: { grid: { color: gridColor }, ticks: { color: labelColor, maxRotation: 45 } },
                    y: { grid: { color: gridColor }, ticks: { color: labelColor, stepSize: 1 }, beginAtZero: true }
                }
            }
        });
    }

    // ── Draw Message Categories Doughnut ─────────────────────
    function drawMsgCategories(data) {
        const ctx = document.getElementById('msgCategoriesChart');
        if (!ctx || !data || !data.length) return;
        if (charts.msgCategories) charts.msgCategories.destroy();
        const { labelColor, borderCol } = getThemeVars();
        const labelMap = { general: 'General', technical: 'Technical', account: 'Account', emergency: 'Emergency', feedback: 'Feedback', billing: 'Billing', accessibility: 'Accessibility' };
        const colors   = ['#3b82f6','#f59e0b','#10b981','#ef4444','#8b5cf6','#ec4899','#14b8a6'];
        charts.msgCategories = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels:   data.map(d => labelMap[d.category] || d.category),
                datasets: [{ data: data.map(d => parseInt(d.count)), backgroundColor: colors.slice(0, data.length), borderWidth: 2, borderColor: borderCol, hoverOffset: 8 }]
            },
            options: {
                responsive: true, maintainAspectRatio: false, cutout: '60%',
                plugins: { legend: { position: 'right', labels: { color: labelColor, padding: 14, font: { size: 12 } } }, tooltip: { mode: 'index' } }
            }
        });
    }

    // ── Initial render ───────────────────────────────────────
    if (typeof chartUserRoles       !== 'undefined') drawUserRoles(chartUserRoles);
    if (typeof chartAlertStatus     !== 'undefined') drawAlertStatus(chartAlertStatus);
    if (typeof chartMonthlyActivity !== 'undefined') drawActivityChart(chartMonthlyActivity, 'monthly');
    if (typeof chartMsgCategories   !== 'undefined') drawMsgCategories(chartMsgCategories);

    // ── Period filter buttons ────────────────────────────────
    const periodBtns = document.querySelectorAll('.period-btn');
    let currentPeriod = 'monthly';

    periodBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            const period = this.dataset.period;
            if (period === currentPeriod) return;

            periodBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentPeriod = period;

            // Loading state
            ['alertStatusChart', 'monthlyActivityChart', 'msgCategoriesChart'].forEach(id => {
                document.getElementById(id)?.closest('.chart-card')?.classList.add('loading');
            });

            const BASE = (typeof BASE_URL !== 'undefined') ? BASE_URL : '/';
            fetch(`${BASE}index.php?action=admin-chart-data&period=${period}`)
               .then(res => res.json())
               .then(json => {
                    console.log('Chart data received:', json); // ← add this
                    if (!json.success) throw new Error(json.error || 'Unknown error');
                    try { drawAlertStatus(json.alertStatus); } catch(e) { console.error('alertStatus error:', e); }
                    try { drawActivityChart(json.activity, period); } catch(e) { console.error('activity error:', e); }
                    try { drawMsgCategories(json.msgCategories); } catch(e) { console.error('msgCategories error:', e); }
                })
                .catch(err => {
                    console.error('Chart filter error:', err);
                    showChartError('Could not load chart data. Please try again.');
                })
                .finally(() => {
                    ['alertStatusChart', 'monthlyActivityChart', 'msgCategoriesChart'].forEach(id => {
                        document.getElementById(id)?.closest('.chart-card')?.classList.remove('loading');
                    });
                });
        });
    });

    // ── Error toast ──────────────────────────────────────────
    function showChartError(msg) {
        const toast = document.createElement('div');
        toast.textContent = '⚠ ' + msg;
        Object.assign(toast.style, {
            position: 'fixed', bottom: '24px', right: '24px', zIndex: '9999',
            background: '#ef4444', color: '#fff', padding: '12px 20px',
            borderRadius: '10px', fontWeight: '600', fontSize: '14px',
            boxShadow: '0 4px 16px rgba(0,0,0,0.15)'
        });
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 4000);
    }

});
