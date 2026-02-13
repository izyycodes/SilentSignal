<?php
require_once VIEW_PATH . 'includes/dashboard-header.php';
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/admin-messages.css">

<div class="dashboard-container">

    <!-- Page Header -->
    <div class="page-header">
        <div class="header-content">
            <h1>Message Inquiries Management</h1>
            <p>Review and respond to user messages and support inquiries</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon blue">
                <i class="ri-message-3-line"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Total Messages</div>
                <div class="stat-value"><?php echo number_format($stats['total']); ?></div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon orange">
                <i class="ri-time-line"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Pending Response</div>
                <div class="stat-value"><?php echo number_format($stats['pending']); ?></div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon green">
                <i class="ri-checkbox-circle-line"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Replied Today</div>
                <div class="stat-value"><?php echo number_format($stats['replied_today']); ?></div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon red">
                <i class="ri-alert-line"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Urgent Messages</div>
                <div class="stat-value"><?php echo number_format($stats['urgent']); ?></div>
            </div>
        </div>
    </div>

    <!-- Messages Section -->
    <div class="section-header">
        <h2><i class="ri-message-3-line"></i> Recent Message Inquiries</h2>
        <div class="section-controls">
            <div class="search-box">
                <i class="ri-search-line"></i>
                <input type="text" id="searchInput" placeholder="Search by name or subject...">
            </div>
            <select class="filter-select" id="categoryFilter">
                <option value="">All Categories</option>
                <option value="emergency">Emergency</option>
                <option value="technical">Technical</option>
                <option value="feedback">Feedback</option>
                <option value="support">Support</option>
                <option value="general">General</option>
            </select>
            <select class="filter-select" id="statusFilter">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="replied">Replied</option>
                <option value="resolved">Resolved</option>
            </select>
            <button class="btn-primary">
                <i class="ri-download-line"></i> Export Messages
            </button>
        </div>
    </div>

    <!-- Messages Table -->
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>MESSAGE ID</th>
                    <th>USER</th>
                    <th>CATEGORY</th>
                    <th>SUBJECT</th>
                    <th>MESSAGE PREVIEW</th>
                    <th>PRIORITY</th>
                    <th>DATE RECEIVED</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($messages as $message): ?>
                    <tr class="message-row" onclick="viewMessage(<?php echo $message['id']; ?>)">
                        <td class="message-id"><?php echo $message['message_id']; ?></td>
                        <td>
                            <div class="user-cell">
                                <div class="users-avatar <?php echo strtolower(substr($message['user_name'], 0, 1)); ?>">
                                    <?php echo strtoupper(substr($message['user_name'], 0, 2)); ?>
                                </div>
                                <div class="user-info">
                                    <div class="user-name"><?php echo $message['user_name']; ?></div>
                                    <div class="user-email"><?php echo $message['user_email']; ?></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="category-badge <?php echo $message['category']; ?>">
                                <?php 
                                    $icon = match($message['category']) {
                                        'Emergency' => 'ri-alarm-warning-line',
                                        'Technical' => 'ri-tools-line',
                                        'Feedback' => 'ri-chat-smile-2-line',
                                        'Support' => 'ri-customer-service-2-line',
                                        'General' => 'ri-question-line',
                                        default => 'ri-message-3-line'
                                    };
                                ?>
                                <i class="<?php echo $icon; ?>"></i>
                                <?php echo $message['category']; ?>
                            </span>
                        </td>
                        <td>
                            <div class="subject"><?php echo $message['subject']; ?></div>
                        </td>
                        <td>
                            <div class="message-preview"><?php echo $message['preview']; ?></div>
                        </td>
                        <td>
                            <span class="priority-badge <?php echo $message['priority']; ?>">
                                <?php echo strtoupper($message['priority']); ?>
                            </span>
                        </td>
                        <td>
                            <div class="time-info">
                                <i class="ri-time-line"></i>
                                <?php echo $message['date_received']; ?>
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
            Showing 1-10 of 156 messages
        </div>
        <div class="pagination-controls">
            <button class="page-btn" disabled>Previous</button>
            <button class="page-number active">1</button>
            <button class="page-number">2</button>
            <button class="page-number">3</button>
            <span>...</span>
            <button class="page-number">16</button>
            <button class="page-btn">Next</button>
        </div>
    </div>
</div>

<!-- Message Detail Modal -->
<div id="messageModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Message Details</h3>
            <button class="close-btn" onclick="closeMessageModal()">
                <i class="ri-close-line"></i>
            </button>
        </div>
        <div class="modal-body" id="messageModalBody">
            <!-- Message details will be loaded here -->
        </div>
    </div>
</div>

<?php
require_once VIEW_PATH . 'includes/dashboard-footer.php';
?>

<script src="<?php echo BASE_URL; ?>assets/js/admin-messages.js"></script>