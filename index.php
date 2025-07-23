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
    <title>Quản lí việc cần làm</title>
    <link rel="stylesheet" href="assets/index.css">
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <h1>📋 Quản lí việc cần làmr</h1>
            
            <div class="nav-section">
                <div class="nav-title">Thanh điều hướng</div>
                <div class="nav-item">
                    <span class="icon">📝</span>
                    Action Items
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
                    Trong tuần này
                    <span class="count"><?= $stats['pending'] ?></span>
                </div>
                <div class="nav-item">
                    <span class="icon">📈</span>
                    Lịch sử
                    <span class="count"><?= $stats['completed'] ?></span>
                </div>
                <div class="nav-item">
                    <span class="icon">⏱️</span>
                    Mốc thời gian
                </div>
                <div class="nav-item">
                    <span class="icon">🔄</span>
                    Trạng thái
                    <span class="count"><?= $stats['pending'] ?></span>
                </div>
                <div class="nav-item active">
                    <span class="icon">✅</span>
                    Đã hoàn thành
                    <span class="count"><?= $stats['completed'] ?></span>
                </div>
            </div>
        </div>

        <!-- Nội dung chính -->
        <div class="main-content">
            <div class="content-header">
                <h1 class="content-title">Danh sách cần làm</h1>
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
                        <button class="filter-btn active">📋 Tất cả</button>
                        <button class="filter-btn">📝 Chưa bắt đầu</button>
                        <button class="filter-btn">🔄 Đang thực hiện</button>
                        <button class="filter-btn">✅ Đã hoàn thành</button>
                    </div>
                </div>

                <?php if ($result->num_rows > 0): ?>
                    <table class="task-table">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Title</th>
                                <th>Status</th>
                                <th>Priority</th>
                                <th>Due Date</th>
                                <th>Progress</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): 
                                $isOverdue = false;
                                $priorityClass = 'priority-medium';
                                $priorityText = 'Trung bình';
                                
                                // Xác định priority class và text
                                switch($row['DoUuTien']) {
                                    case 'cao':
                                        $priorityClass = 'priority-high';
                                        $priorityText = 'Cao';
                                        break;
                                    case 'thap':
                                        $priorityClass = 'priority-low';
                                        $priorityText = 'Thấp';
                                        break;
                                    default:
                                        $priorityClass = 'priority-medium';
                                        $priorityText = 'Trung bình';
                                }
                                
                                $statusClass = $row['TrangThai'] ? 'status-completed' : 'status-not-started';
                                $statusText = $row['TrangThai'] ? 'Đã hoàn thành' : 'Chưa bắt đầu';
                                $progress = $row['TrangThai'] ? 100 : 0;
                                
                                if (!empty($row['NgayHetHan']) && $row['NgayHetHan'] != '0000-00-00') {
                                    $today = date('Y-m-d');
                                    $deadline = $row['NgayHetHan'];
                                    
                                    if ($deadline < $today && !$row['TrangThai']) {
                                        $isOverdue = true;
                                        $priorityClass = 'priority-high';
                                        $priorityText = 'Khẩn cấp';
                                    }
                                }
                            ?>
                                <tr>
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
                                            <small style="color: #6c757d;"><?= htmlspecialchars(substr($row['MoTa'], 0, 50)) ?>...</small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <form method="POST" action="update_status.php" style="display: inline;">
                                            <input type="hidden" name="id" value="<?= $row['ID'] ?>">
                                            <select name="status" class="status-select" onchange="this.form.submit()">
                                                <option value="chua_bat_dau" <?= ($row['TrangThaiChiTiet'] ?? 'chua_bat_dau') == 'chua_bat_dau' ? 'selected' : '' ?>>📝 Chưa bắt đầu</option>
                                                <option value="dang_thuc_hien" <?= ($row['TrangThaiChiTiet'] ?? '') == 'dang_thuc_hien' ? 'selected' : '' ?>>🔄 Đang thực hiện</option>
                                                <option value="da_hoan_thanh" <?= ($row['TrangThaiChiTiet'] ?? '') == 'da_hoan_thanh' || $row['TrangThai'] ? 'selected' : '' ?>>✅ Đã hoàn thành</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td>
                                        <span class="priority-badge <?= $priorityClass ?>">
                                            <?php 
                                                if ($isOverdue) {
                                                    echo '🚨 Khẩn cấp';
                                                } else {
                                                    switch($row['DoUuTien']) {
                                                        case 'cao': echo '🔴 Cao'; break;
                                                        case 'thap': echo '🟢 Thấp'; break;
                                                        default: echo '🟡 Trung bình';
                                                    }
                                                }
                                            ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (!empty($row['NgayHetHan']) && $row['NgayHetHan'] != '0000-00-00'): ?>
                                            <?= date("d/m/Y", strtotime($row['NgayHetHan'])) ?>
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
        // Filter functionality với logic thực tế
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                // Xóa class active khỏi tất cả nút
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                // Lấy loại filter
                const filterType = this.textContent.trim();
                const tableRows = document.querySelectorAll('.task-table tbody tr');
                
                tableRows.forEach(row => {
                    const statusBadge = row.querySelector('.status-badge');
                    const statusText = statusBadge ? statusBadge.textContent.trim() : '';
                    
                    let showRow = true;
                    
                    switch(filterType) {
                        case '📝 Chưa bắt đầu':
                            showRow = statusText === 'Chưa bắt đầu';
                            break;
                        case '🔄 Đang thực hiện':
                            showRow = statusText === 'Đang thực hiện'; // Cần thêm trạng thái này
                            break;
                        case '✅ Đã hoàn thành':
                            showRow = statusText === 'Đã hoàn thành';
                            break;
                        case '📋 Tất cả':
                            showRow = true;
                            break;
                    }
                    
                    row.style.display = showRow ? '' : 'none';
                });
                
                // Cập nhật counter
                updateTaskCounter();
            });
        });

        function updateTaskCounter() {
            const visibleRows = document.querySelectorAll('.task-table tbody tr:not([style*="display: none"])');
            console.log(`Hiển thị ${visibleRows.length} công việc`);
        }
    </script>
</body>
</html>