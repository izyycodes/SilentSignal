// ============================================================
// ADMIN DASHBOARD — Chart.js Analytics
// ============================================================

document.addEventListener('DOMContentLoaded', function () {

    const isDark     = document.documentElement.getAttribute('data-theme') === 'dark';
    const gridColor  = isDark ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.06)';
    const labelColor = isDark ? '#94a3b8' : '#64748b';
    const borderCol  = isDark ? '#1e293b' : '#fff';

    function getMonths(data) {
        return [...new Set(data.map(d => d.month))];
    }

    // ── 1. User Role Breakdown Doughnut ──
    (function () {
        const ctx = document.getElementById('userRolesChart');
        if (!ctx || typeof chartUserRoles === 'undefined' || !chartUserRoles.length) return;

        const labelMap = { pwd: 'PWD User', family: 'Family Member', admin: 'Administrator' };
        const colors   = ['#3b82f6', '#10b981', '#8b5cf6'];
        const labels   = chartUserRoles.map(d => labelMap[d.role] || d.role);
        const data     = chartUserRoles.map(d => parseInt(d.count));

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels,
                datasets: [{
                    data,
                    backgroundColor: colors.slice(0, data.length),
                    borderWidth: 2,
                    borderColor: borderCol,
                    hoverOffset: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: labelColor, padding: 16, font: { size: 12 } }
                    },
                    tooltip: { mode: 'index' }
                },
                cutout: '65%',
            }
        });
    })();

    // ── 2. Alert Status Breakdown Doughnut ──
    (function () {
        const ctx = document.getElementById('alertStatusChart');
        if (!ctx || typeof chartAlertStatus === 'undefined' || !chartAlertStatus.length) return;

        const labelMap = {
            active:      'Active',
            resolved:    'Resolved',
            false_alarm: 'False Alarm',
            pending:     'Pending',
        };
        const colors = ['#ef4444', '#10b981', '#f59e0b', '#94a3b8'];
        const labels = chartAlertStatus.map(d => labelMap[d.status] || d.status);
        const data   = chartAlertStatus.map(d => parseInt(d.count));

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels,
                datasets: [{
                    data,
                    backgroundColor: colors.slice(0, data.length),
                    borderWidth: 2,
                    borderColor: borderCol,
                    hoverOffset: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: labelColor, padding: 16, font: { size: 12 } }
                    },
                    tooltip: { mode: 'index' }
                },
                cutout: '65%',
            }
        });
    })();

    // ── 3. Monthly Activity Bar Chart ──
    (function () {
        const ctx = document.getElementById('monthlyActivityChart');
        if (!ctx || typeof chartMonthlyActivity === 'undefined') return;

        const months       = getMonths(chartMonthlyActivity);
        const alertsData   = months.map(m => {
            const found = chartMonthlyActivity.find(d => d.month === m && d.type === 'alerts');
            return found ? parseInt(found.count) : 0;
        });
        const messagesData = months.map(m => {
            const found = chartMonthlyActivity.find(d => d.month === m && d.type === 'messages');
            return found ? parseInt(found.count) : 0;
        });

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'Emergency Alerts',
                        data: alertsData,
                        backgroundColor: 'rgba(239,68,68,0.8)',
                        borderRadius: 6,
                        borderSkipped: false,
                    },
                    {
                        label: 'Message Inquiries',
                        data: messagesData,
                        backgroundColor: 'rgba(16,185,129,0.8)',
                        borderRadius: 6,
                        borderSkipped: false,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { color: labelColor, padding: 16, font: { size: 12 } }
                    },
                    tooltip: { mode: 'index', intersect: false }
                },
                scales: {
                    x: { grid: { color: gridColor }, ticks: { color: labelColor } },
                    y: { grid: { color: gridColor }, ticks: { color: labelColor, stepSize: 1 }, beginAtZero: true }
                }
            }
        });
    })();

    // ── 4. Message Inquiry Categories Doughnut ──
    (function () {
        const ctx = document.getElementById('msgCategoriesChart');
        if (!ctx || typeof chartMsgCategories === 'undefined' || !chartMsgCategories.length) return;

        const labelMap = {
            general:       'General',
            technical:     'Technical',
            account:       'Account',
            emergency:     'Emergency',
            feedback:      'Feedback',
            billing:       'Billing',
            accessibility: 'Accessibility',
        };
        const colors = ['#3b82f6','#f59e0b','#10b981','#ef4444','#8b5cf6','#ec4899','#14b8a6'];
        const labels = chartMsgCategories.map(d => labelMap[d.category] || d.category);
        const data   = chartMsgCategories.map(d => parseInt(d.count));

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels,
                datasets: [{
                    data,
                    backgroundColor: colors.slice(0, data.length),
                    borderWidth: 2,
                    borderColor: borderCol,
                    hoverOffset: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: { color: labelColor, padding: 14, font: { size: 12 } }
                    },
                    tooltip: { mode: 'index' }
                },
                cutout: '60%',
            }
        });
    })();

});