<?php
require_once VIEW_PATH . 'includes/dashboard-header.php';
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/admin-users.css">

<div class="dashboard-container">

    <!-- Success/Error Messages -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <i class="ri-checkbox-circle-line"></i>
            <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <i class="ri-error-warning-line"></i>
            <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <!-- Page Header -->
    <div class="page-header">
        <div class="header-content">
            <h1>Account Management</h1>
            <p>Manage and monitor all registered user accounts</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon blue">
                <i class="ri-group-line"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Total Accounts</div>
                <div class="stat-value"><?php echo number_format($stats['total']); ?></div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon green">
                <i class="ri-checkbox-circle-line"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Verified Users</div>
                <div class="stat-value"><?php echo number_format($stats['verified']); ?></div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon orange">
                <i class="ri-time-line"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Pending Verification</div>
                <div class="stat-value"><?php echo number_format($stats['pending']); ?></div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon red">
                <i class="ri-close-circle-line"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Inactive</div>
                <div class="stat-value"><?php echo number_format($stats['inactive']); ?></div>
            </div>
        </div>
    </div>

    <!-- Accounts Section -->
    <div class="section-header">
        <h2><i class="ri-user-add-line"></i> Created Accounts</h2>
        <div class="section-controls">
            <div class="search-box">
                <i class="ri-search-line"></i>
                <input type="text" id="searchInput" placeholder="Search by name or email...">
            </div>
            <select class="filter-select" id="statusFilter">
                <option value="">All Status</option>
                <option value="verified">Verified</option>
                <option value="pending">Pending</option>
                <option value="inactive">Inactive</option>
            </select>
            <select class="filter-select" id="roleFilter">
                <option value="">All Roles</option>
                <option value="pwd">PWD</option>
                <option value="family">Family</option>
                <option value="admin">Admin</option>
            </select>
            <select class="filter-select" id="disabilityFilter">
                <option value="">All Disabilities</option>
                <?php foreach ($disabilityTypes as $dtype): ?>
                    <option value="<?php echo htmlspecialchars(strtolower($dtype)); ?>"><?php echo htmlspecialchars($dtype); ?></option>
                <?php endforeach; ?>
            </select>
            <button class="btn-primary">
                <i class="ri-download-line"></i> Export Data
            </button>
        </div>
    </div>

    <!-- Users Table -->
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>USER</th>
                    <th>PHONE NUMBER</th>
                    <th>ROLE</th>
                    <th>PWD ID</th>
                    <th>DISABILITY STATUS</th>
                    <th>LOCATION</th>
                    <th>REGISTRATION DATE</th>
                    <th>STATUS</th>
                    <th>ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <?php
                        // Determine status based on is_verified and is_active
                        if (!$user['is_active']) {
                            $status = 'inactive';
                            $statusLabel = 'INACTIVE';
                        } elseif ($user['is_verified']) {
                            $status = 'verified';
                            $statusLabel = 'VERIFIED';
                        } else {
                            $status = 'pending';
                            $statusLabel = 'PENDING';
                        }

                        // Handle missing medical profile data
                        $pwd_id = $user['pwd_id'] ?? 'N/A';
                        $disability = $user['disability_type'] ?? 'N/A';
                        $location = $user['location'] ?? 'N/A';
                    ?>
                    <tr>
                        <td>
                            <div class="user-cell">
                                <div class="users-avatar">
                                    <?php echo strtoupper(substr($user['name'], 0, 2)); ?>
                                </div>
                                <div class="user-info">
                                    <div class="user-name"><?php echo htmlspecialchars($user['name']); ?></div>
                                    <div class="user-email"><?php echo htmlspecialchars($user['email']); ?></div>
                                </div>
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars($user['phone'] ?? 'N/A'); ?></td>
                        <td>
                            <span class="role-badge <?php echo $user['role']; ?>">
                                <?php echo strtoupper($user['role']); ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($pwd_id); ?></td>
                        <td>
                            <?php if ($disability !== 'N/A'): ?>
                                <span class="disability-badge">
                                    <i class="ri-wheelchair-line"></i>
                                    <?php echo htmlspecialchars($disability); ?>
                                </span>
                            <?php else: ?>
                                <span style="color: #94a3b8;">N/A</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($location); ?></td>
                        <td><?php echo htmlspecialchars($user['registration_date']); ?></td>
                        <td>
                            <span class="status-badge <?php echo $status; ?>">
                                <?php echo $statusLabel; ?>
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn-icon view" onclick="viewUser(<?php echo $user['id']; ?>)" title="View Details">
                                    <i class="ri-eye-line"></i>
                                </button>
                                <?php if (!$user['is_verified']): ?>
                                    <button class="btn-icon verify" onclick="verifyUser(<?php echo $user['id']; ?>, <?php echo $currentPage; ?>)" title="Verify Account">
                                        <i class="ri-checkbox-circle-line"></i>
                                    </button>
                                <?php endif; ?>
                                <button class="btn-icon <?php echo $user['is_active'] ? 'deactivate' : 'activate'; ?>" 
                                        onclick="toggleActive(<?php echo $user['id']; ?>, <?php echo $currentPage; ?>)" 
                                        title="<?php echo $user['is_active'] ? 'Deactivate Account' : 'Activate Account'; ?>">
                                    <i class="ri-<?php echo $user['is_active'] ? 'close' : 'checkbox'; ?>-circle-line"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="pagination">
        <div class="pagination-info">
            Showing <strong><?php echo $rangeStart; ?>-<?php echo $rangeEnd; ?></strong> of <strong><?php echo number_format($stats['total']); ?></strong> accounts
        </div>
        <div class="pagination-controls">

            <?php if ($currentPage > 1): ?>
                <a href="<?php echo BASE_URL; ?>index.php?action=admin-users&page=<?php echo $currentPage - 1; ?>" class="page-btn">Previous</a>
            <?php else: ?>
                <button class="page-btn" disabled>Previous</button>
            <?php endif; ?>

            <?php
                // Show up to 5 page number links centered around current page
                $startPage = max(1, $currentPage - 2);
                $endPage   = min($totalPages, $startPage + 4);
                $startPage = max(1, $endPage - 4); // re-anchor if near the end

                if ($startPage > 1): ?>
                    <a href="<?php echo BASE_URL; ?>index.php?action=admin-users&page=1" class="page-number">1</a>
                    <?php if ($startPage > 2): ?><span class="page-ellipsis">...</span><?php endif; ?>
                <?php endif;

                for ($i = $startPage; $i <= $endPage; $i++): ?>
                    <?php if ($i === $currentPage): ?>
                        <span class="page-number active"><?php echo $i; ?></span>
                    <?php else: ?>
                        <a href="<?php echo BASE_URL; ?>index.php?action=admin-users&page=<?php echo $i; ?>" class="page-number"><?php echo $i; ?></a>
                    <?php endif; ?>
                <?php endfor;

                if ($endPage < $totalPages): ?>
                    <?php if ($endPage < $totalPages - 1): ?><span class="page-ellipsis">...</span><?php endif; ?>
                    <a href="<?php echo BASE_URL; ?>index.php?action=admin-users&page=<?php echo $totalPages; ?>" class="page-number"><?php echo $totalPages; ?></a>
                <?php endif; ?>

            <?php if ($currentPage < $totalPages): ?>
                <a href="<?php echo BASE_URL; ?>index.php?action=admin-users&page=<?php echo $currentPage + 1; ?>" class="page-btn">Next</a>
            <?php else: ?>
                <button class="page-btn" disabled>Next</button>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php
require_once VIEW_PATH . 'includes/dashboard-footer.php';
?>

<script src="<?php echo BASE_URL; ?>assets/js/admin-users.js"></script>