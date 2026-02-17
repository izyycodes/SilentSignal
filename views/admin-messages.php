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
            <select class="filter-select" id="priorityFilter">
                <option value="">All Priorities</option>
                <option value="urgent">Urgent</option>
                <option value="high">High</option>
                <option value="normal">Normal</option>
                <option value="low">Low</option>
            </select>
            <select class="filter-select" id="statusFilter">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="in_review">In Review</option>
                <option value="replied">Replied</option>
                <option value="resolved">Resolved</option>
            </select>
            <button class="btn-primary">
                <i class="ri-download-line"></i> Export
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
                    <th>STATUS</th>
                    <th>DATE RECEIVED</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($messages as $message): ?>
                    <tr class="message-row"
                        data-id="<?php echo $message['id']; ?>"
                        data-message-id="<?php echo htmlspecialchars($message['message_id']); ?>"
                        data-name="<?php echo htmlspecialchars($message['user_name']); ?>"
                        data-email="<?php echo htmlspecialchars($message['user_email']); ?>"
                        data-category="<?php echo htmlspecialchars($message['category']); ?>"
                        data-subject="<?php echo htmlspecialchars($message['subject']); ?>"
                        data-message="<?php echo htmlspecialchars($message['message']); ?>"
                        data-priority="<?php echo htmlspecialchars($message['priority']); ?>"
                        data-status="<?php echo htmlspecialchars($message['status']); ?>"
                        data-date="<?php echo htmlspecialchars($message['date_received']); ?>"
                        data-reply="<?php echo htmlspecialchars($message['reply_message'] ?? ''); ?>"
                        data-date-replied="<?php echo htmlspecialchars($message['date_replied'] ?? ''); ?>"
                        onclick="viewMessage(this)">

                        <td class="message-id"><?php echo $message['message_id']; ?></td>
                        <td>
                            <div class="user-cell">
                                <div class="users-avatar">
                                    <?php echo strtoupper(substr($message['user_name'], 0, 2)); ?>
                                </div>
                                <div class="user-info">
                                    <div class="user-name"><?php echo htmlspecialchars($message['user_name']); ?></div>
                                    <div class="user-email"><?php echo htmlspecialchars($message['user_email']); ?></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <?php
                                $icon = match($message['category']) {
                                    'Emergency' => 'ri-alarm-warning-line',
                                    'Technical' => 'ri-tools-line',
                                    'Feedback'  => 'ri-chat-smile-2-line',
                                    'Support'   => 'ri-customer-service-2-line',
                                    'General'   => 'ri-question-line',
                                    default     => 'ri-message-3-line'
                                };
                            ?>
                            <span class="category-badge <?php echo $message['category']; ?>">
                                <i class="<?php echo $icon; ?>"></i>
                                <?php echo $message['category']; ?>
                            </span>
                        </td>
                        <td>
                            <div class="subject"><?php echo htmlspecialchars($message['subject']); ?></div>
                        </td>
                        <td>
                            <div class="message-preview"><?php echo htmlspecialchars($message['preview']); ?>...</div>
                        </td>
                        <td>
                            <span class="priority-badge <?php echo $message['priority']; ?>">
                                <?php echo strtoupper($message['priority']); ?>
                            </span>
                        </td>
                        <td>
                            <span class="status-badge <?php echo $message['status']; ?>">
                                <?php echo ucfirst(str_replace('_', ' ', $message['status'])); ?>
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
            Showing <strong><?php echo $rangeStart; ?>-<?php echo $rangeEnd; ?></strong> of <strong><?php echo number_format($stats['total']); ?></strong> messages
        </div>
        <div class="pagination-controls">

            <?php if ($currentPage > 1): ?>
                <a href="<?php echo BASE_URL; ?>index.php?action=admin-messages&page=<?php echo $currentPage - 1; ?>" class="page-btn">Previous</a>
            <?php else: ?>
                <button class="page-btn" disabled>Previous</button>
            <?php endif; ?>

            <?php
                // Show up to 5 page number links centered around current page
                $startPage = max(1, $currentPage - 2);
                $endPage   = min($totalPages, $startPage + 4);
                $startPage = max(1, $endPage - 4); // re-anchor if near the end

                if ($startPage > 1): ?>
                    <a href="<?php echo BASE_URL; ?>index.php?action=admin-messages&page=1" class="page-number">1</a>
                    <?php if ($startPage > 2): ?><span class="page-ellipsis">...</span><?php endif; ?>
                <?php endif;

                for ($i = $startPage; $i <= $endPage; $i++): ?>
                    <?php if ($i === $currentPage): ?>
                        <span class="page-number active"><?php echo $i; ?></span>
                    <?php else: ?>
                        <a href="<?php echo BASE_URL; ?>index.php?action=admin-messages&page=<?php echo $i; ?>" class="page-number"><?php echo $i; ?></a>
                    <?php endif; ?>
                <?php endfor;

                if ($endPage < $totalPages): ?>
                    <?php if ($endPage < $totalPages - 1): ?><span class="page-ellipsis">...</span><?php endif; ?>
                    <a href="<?php echo BASE_URL; ?>index.php?action=admin-messages&page=<?php echo $totalPages; ?>" class="page-number"><?php echo $totalPages; ?></a>
                <?php endif; ?>

            <?php if ($currentPage < $totalPages): ?>
                <a href="<?php echo BASE_URL; ?>index.php?action=admin-messages&page=<?php echo $currentPage + 1; ?>" class="page-btn">Next</a>
            <?php else: ?>
                <button class="page-btn" disabled>Next</button>
            <?php endif; ?>

        </div>
    </div>

</div>

<!-- Message Detail Modal -->
<div id="messageModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-header-left">
                <h3 id="modalMessageId"></h3>
                <div class="modal-badges">
                    <span class="category-badge" id="modalCategory"></span>
                    <span class="priority-badge" id="modalPriority"></span>
                    <span class="status-badge" id="modalStatus"></span>
                </div>
            </div>
            <button class="close-btn" onclick="closeMessageModal()">
                <i class="ri-close-line"></i>
            </button>
        </div>
        <div class="modal-body" id="messageModalBody">

            <!-- Sender Info -->
            <div class="modal-section">
                <div class="modal-section-title">
                    <i class="ri-user-line"></i> Sender Information
                </div>
                <div class="modal-info-grid">
                    <div class="modal-info-item">
                        <span class="modal-info-label">Name</span>
                        <span class="modal-info-value" id="modalName"></span>
                    </div>
                    <div class="modal-info-item">
                        <span class="modal-info-label">Email</span>
                        <span class="modal-info-value" id="modalEmail"></span>
                    </div>
                    <div class="modal-info-item">
                        <span class="modal-info-label">Date Received</span>
                        <span class="modal-info-value" id="modalDate"></span>
                    </div>
                    <div class="modal-info-item">
                        <span class="modal-info-label">Subject</span>
                        <span class="modal-info-value" id="modalSubject"></span>
                    </div>
                </div>
            </div>

            <!-- Message Content -->
            <div class="modal-section">
                <div class="modal-section-title">
                    <i class="ri-message-2-line"></i> Message
                </div>
                <div class="modal-message-body" id="modalMessage"></div>
            </div>

            <!-- Previous Reply (shown only if already replied) -->
            <div class="modal-section" id="previousReplySection" style="display:none;">
                <div class="modal-section-title">
                    <i class="ri-reply-line"></i> Previous Reply
                    <span class="replied-at" id="modalDateReplied"></span>
                </div>
                <div class="modal-message-body replied" id="modalReply"></div>
            </div>

            <!-- Reply Textarea -->
            <div class="modal-section" id="replySection">
                <div class="modal-section-title">
                    <i class="ri-send-plane-line"></i> Reply
                </div>
                <textarea class="reply-textarea" id="replyTextarea" placeholder="Type your reply here..." rows="4"></textarea>
            </div>

        </div>

        <!-- Action Buttons — outside modal-body so they're always visible -->
        <div class="modal-actions">
            <button class="btn-modal-primary" onclick="sendReply()">
                <i class="ri-send-plane-line"></i> Send Reply
            </button>
            <button class="btn-modal-secondary" onclick="markAsResolved()">
                <i class="ri-check-double-line"></i> Mark as Resolved
            </button>
        </div>
    </div>
</div>

<?php
require_once VIEW_PATH . 'includes/dashboard-footer.php';
?>

<script src="<?php echo BASE_URL; ?>assets/js/admin-messages.js"></script>