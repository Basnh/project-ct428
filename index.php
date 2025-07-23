<?php
session_start();
require 'db.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['id'];
$username = $_SESSION['username'];

// Lấy thống kê
$stmt = $conn->prepare("SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN TrangThai = 1 THEN 1 ELSE 0 END) as completed,
    SUM(CASE WHEN TrangThai = 0 THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN NgayHetHan < CURDATE() AND TrangThai = 0 THEN 1 ELSE 0 END) as overdue
    FROM CongViec WHERE ID_NguoiDung = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stats = $stmt->get_result()->fetch_assoc();

// Lấy danh sách công việc
$stmt = $conn->prepare("SELECT * FROM CongViec WHERE ID_NguoiDung = ? ORDER BY TrangThai ASC, NgayHetHan ASC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý việc cần làm</title>
    <link rel="stylesheet" href="assets/index.css">
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <h1>📋 Quản lý việc cần làm</h1>
            
            <div class="nav-section">
                <div class="nav-title">Thanh điều hướng</div>
                <div class="nav-item">
                    <span class="icon">📝</span>
                    Việc cần làm
                    <span class="count"><?= $stats['pending'] ?></span>
                </div>
                <div class="nav-item">
                    <span class="icon">📅</span>
                    Hôm nay
                    <span class="count">0</span>
                </div>
                <div class="nav-item">
                    <span class="icon">⏰</span>
                    Ngày mai
                    <span class="count">0</span>
                </div>
                <div class="nav-item">
                    <span class="icon">📊</span>
                    Tuần này
                    <span class="count"><?= $stats['pending'] ?></span>
                </div>
                <div class="nav-item">
                    <span class="icon">📈</span>
                    Lịch sử
                    <span class="count"><?= $stats['completed'] ?></span>
                </div>
                <div class="nav-item">
                    <span class="icon">⏱️</span>
                    Timeline
                </div>
                <div class="nav-item">
                    <span class="icon">🔄</span>
                    Đang thực hiện
                    <span class="count">0</span>
                </div>
                <div class="nav-item active">
                    <span class="icon">✅</span>
                    Đã hoàn thành
                    <span class="count"><?= $stats['completed'] ?></span>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="content-header">
                <h1 class="content-title">Danh sách công việc</h1>
                <div class="user-info">
                    <span class="welcome-text">Xin chào, <strong><?= htmlspecialchars($username) ?></strong>!</span>
                    <a href="logout.php" class="logout-btn">Đăng xuất</a>
                </div>
            </div>

            <!-- Add Task Form -->
            <div class="add-task-form">
                <h3 style="margin-bottom: 20px;">✨ Thêm công việc mới</h3>
                <form method="POST" action="add_task.php">
                    <div class="form-group">
                        <label for="task_title">Tiêu đề công việc</label>
                        <input type="text" id="task_title" name="task_title" class="form-control" placeholder="Nhập tiêu đề công việc..." required>
                    </div>
                    
                    <div class="form-group">
                        <label for="task_priority">Độ ưu tiên</label>
                        <select id="task_priority" name="task_priority" class="form-control">
                            <option value="thap">🟢 Thấp</option>
                            <option value="trung_binh" selected>🟡 Trung bình</option>
                            <option value="cao">🔴 Cao</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="task_deadline">Hạn chót</label>
                        <input type="date" id="task_deadline" name="task_deadline" class="form-control" min="<?= date('Y-m-d') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="task_description">Mô tả</label>
                        <textarea id="task_description" name="task_description" class="form-control" rows="3" placeholder="Mô tả chi tiết công việc..."></textarea>
                    </div>
                    
                    <button type="submit" class="btn-primary">Thêm công việc</button>
                </form>
            </div>

            <!-- Task List -->
            <div class="task-section">
                <div class="task-header">
                    <div class="task-filters">
                        <button class="filter-btn active" data-filter="all">📋 Tất cả</button>
                        <button class="filter-btn" data-filter="pending">📝 Chưa hoàn thành</button>
                        <button class="filter-btn" data-filter="completed">✅ Đã hoàn thành</button>
                    </div>
                </div>

                <?php if ($result->num_rows > 0): ?>
                    <table class="task-table">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Tiêu đề</th>
                                <th>Trạng thái</th>
                                <th>Độ ưu tiên</th>
                                <th>Hạn chót</th>
                                <th>Tiến độ</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): 
                                $isOverdue = false;
                                $priorityClass = 'priority-medium';
                                $priorityText = '🟡 Trung bình';
                                
                                // Xác định priority
                                if (isset($row['DoUuTien'])) {
                                    switch($row['DoUuTien']) {
                                        case 'cao':
                                            $priorityClass = 'priority-high';
                                            $priorityText = '🔴 Cao';
                                            break;
                                        case 'thap':
                                            $priorityClass = 'priority-low';
                                            $priorityText = '🟢 Thấp';
                                            break;
                                        default:
                                            $priorityClass = 'priority-medium';
                                            $priorityText = '🟡 Trung bình';
                                    }
                                }
                                
                                // Kiểm tra quá hạn
                                if (!empty($row['NgayHetHan']) && $row['NgayHetHan'] != '0000-00-00') {
                                    $today = date('Y-m-d');
                                    $deadline = $row['NgayHetHan'];
                                    
                                    if ($deadline < $today && !$row['TrangThai']) {
                                        $isOverdue = true;
                                        $priorityClass = 'priority-high';
                                        $priorityText = '🚨 Quá hạn';
                                    }
                                }
                                
                                $progress = $row['TrangThai'] ? 100 : 0;
                            ?>
                                <tr data-status="<?= $row['TrangThai'] ? 'completed' : 'pending' ?>">
                                    <td>
                                        <form method="POST" action="complete_task.php" style="display: inline;">
                                            <input type="hidden" name="id" value="<?= $row['ID'] ?>">
                                            <input type="checkbox" class="task-checkbox" onchange="this.form.submit()" <?= $row['TrangThai'] ? 'checked' : '' ?>>
                                        </form>
                                    </td>
                                    <td>
                                        <div class="task-title <?= $row['TrangThai'] ? 'completed' : '' ?>">
                                            <?= htmlspecialchars($row['TieuDe']) ?>
                                        </div>
                                        <?php if (!empty($row['MoTa'])): ?>
                                            <div class="task-description">
                                                <?= htmlspecialchars(substr($row['MoTa'], 0, 80)) ?><?= strlen($row['MoTa']) > 80 ? '...' : '' ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="status-badge <?= $row['TrangThai'] ? 'status-completed' : 'status-pending' ?>">
                                            <?= $row['TrangThai'] ? '✅ Đã hoàn thành' : '📝 Chưa hoàn thành' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="priority-badge <?= $priorityClass ?>">
                                            <?= $priorityText ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (!empty($row['NgayHetHan']) && $row['NgayHetHan'] != '0000-00-00'): ?>
                                            <?= date("d/m/Y", strtotime($row['NgayHetHan'])) ?>
                                            <?php if ($isOverdue): ?>
                                                <br><small style="color: #dc3545;">Quá hạn</small>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span style="color: #6c757d;">Không có</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width: <?= $progress ?>%"></div>
                                        </div>
                                        <small style="color: #6c757d;"><?= $progress ?>%</small>
                                    </td>
                                    <td>
                                        <div class="task-actions">
                                            <a href="edit_task.php?id=<?= $row['ID'] ?>" class="btn-sm btn-edit">Sửa</a>
                                            <form method="POST" action="delete_task.php" style="display: inline;" 
                                                onsubmit="return confirm('Bạn có chắc chắn muốn xóa công việc này?');">
                                                <input type="hidden" name="id" value="<?= $row['ID'] ?>">
                                                <button type="submit" class="btn-sm btn-delete">Xóa</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-state">
                        <h3>🎉 Chưa có công việc nào!</h3>
                        <p>Hãy thêm công việc đầu tiên của bạn.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // Filter functionality
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                // Remove active class from all buttons
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                // Get filter type
                const filterType = this.getAttribute('data-filter');
                const tableRows = document.querySelectorAll('.task-table tbody tr');
                let visibleCount = 0;
                
                tableRows.forEach(row => {
                    const status = row.getAttribute('data-status');
                    let showRow = true;
                    
                    switch(filterType) {
                        case 'pending':
                            showRow = status === 'pending';
                            break;
                        case 'completed':
                            showRow = status === 'completed';
                            break;
                        case 'all':
                        default:
                            showRow = true;
                            break;
                    }
                    
                    row.style.display = showRow ? '' : 'none';
                    if (showRow) visibleCount++;
                });
                
                // Update button text with count
                updateFilterCounts();
            });
        });

        // Update filter counts
        function updateFilterCounts() {
            const allRows = document.querySelectorAll('.task-table tbody tr');
            const pendingRows = document.querySelectorAll('.task-table tbody tr[data-status="pending"]');
            const completedRows = document.querySelectorAll('.task-table tbody tr[data-status="completed"]');
            
            document.querySelector('[data-filter="all"]').textContent = `📋 Tất cả (${allRows.length})`;
            document.querySelector('[data-filter="pending"]').textContent = `📝 Chưa hoàn thành (${pendingRows.length})`;
            document.querySelector('[data-filter="completed"]').textContent = `✅ Đã hoàn thành (${completedRows.length})`;
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateFilterCounts();
        });

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const title = document.getElementById('task_title').value.trim();
            const deadline = document.getElementById('task_deadline').value;
            
            if (!title) {
                e.preventDefault();
                alert('Vui lòng nhập tiêu đề công việc!');
                return;
            }
            
            if (deadline && deadline < new Date().toISOString().split('T')[0]) {
                e.preventDefault();
                alert('Hạn chót không thể là ngày trong quá khứ!');
                return;
            }
        });
    </script>
</body>
</html>