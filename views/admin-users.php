<?php
require_once VIEW_PATH . 'includes/dashboard-header.php';
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/admin-users.css">

<div class="dashboard-container">

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
                <option value="responder">Responder</option>
                <option value="admin">Admin</option>
            </select>
            <select class="filter-select" id="disabilityFilter">
                <option value="">All Disabilities</option>
                <option value="deaf">Deaf/Mute</option>
                <option value="blind">Blind</option>
                <option value="mobility">Mobility Impaired</option>
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
                    <tr>
                        <td>
                            <div class="user-cell">
                                <div class="users-avatar <?php echo strtolower(substr($user['name'], 0, 1)); ?>">
                                    <?php echo strtoupper(substr($user['name'], 0, 2)); ?>
                                </div>
                                <div class="user-info">
                                    <div class="user-name"><?php echo $user['name']; ?></div>
                                    <div class="user-email"><?php echo $user['email']; ?></div>
                                </div>
                            </div>
                        </td>
                        <td><?php echo $user['phone']; ?></td>
                        <td>
                            <span class="role-badge <?php echo $user['role']; ?>">
                                <?php echo strtoupper($user['role']); ?>
                            </span>
                        </td>
                        <td><?php echo $user['pwd_id']; ?></td>
                        <td>
                            <span class="disability-badge">
                                <i class="ri-wheelchair-line"></i>
                                <?php echo $user['disability']; ?>
                            </span>
                        </td>
                        <td><?php echo $user['location']; ?></td>
                        <td><?php echo $user['registration_date']; ?></td>
                        <td>
                            <span class="status-badge <?php echo $user['status']; ?>">
                                <?php echo strtoupper($user['status']); ?>
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn-icon view" onclick="viewUser(<?php echo $user['id']; ?>)" title="View">
                                    <i class="ri-eye-line"></i>
                                </button>
                                <button class="btn-icon edit" onclick="editUser(<?php echo $user['id']; ?>)" title="Edit">
                                    <i class="ri-edit-line"></i>
                                </button>
                                <button class="btn-icon delete" onclick="deleteUser(<?php echo $user['id']; ?>)" title="Delete">
                                    <i class="ri-delete-bin-line"></i>
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
            Showing 1-10 of <?php echo number_format($stats['total']); ?> accounts
        </div>
        <div class="pagination-controls">
            <button class="page-btn" disabled>Previous</button>
            <button class="page-number active">1</button>
            <button class="page-number">2</button>
            <button class="page-number">3</button>
            <span>...</span>
            <button class="page-number">97</button>
            <button class="page-btn">Next</button>
        </div>
    </div>
</div>

<?php
require_once VIEW_PATH . 'includes/dashboard-footer.php';
?>

<script src="<?php echo BASE_URL; ?>assets/js/admin-users.js"></script>