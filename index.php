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
    <title>Hệ thống Quản lý Công việc - To-Do List</title>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>📋 Hệ thống Quản lý Công việc</h1>
            <div class="user-info">
                <span class="welcome-text">Xin chào, <strong><?= htmlspecialchars($username) ?></strong>!</span>
                <a href="logout.php" class="logout-btn">Đăng xuất</a>
            </div>
        </div>

        <!-- Thống kê -->
        <div class="stats">
            <div class="stat-card">
                <div class="stat-number"><?= $stats['total'] ?></div>
                <div class="stat-label">Tổng công việc</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $stats['completed'] ?></div>
                <div class="stat-label">Đã hoàn thành</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $stats['pending'] ?></div>
                <div class="stat-label">Chưa hoàn thành</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $stats['overdue'] ?></div>
                <div class="stat-label">Quá hạn</div>
            </div>
        </div>

        <!-- Form thêm công việc -->
        <div class="content">
            <h2>✨ Thêm công việc mới</h2>
            <form class="todo-form" method="POST" action="add_task.php">
                <input class="todo-input" type="text" name="task_title" placeholder="Nhập tiêu đề công việc..." required />
                <input class="todo-deadline" type="date" name="task_deadline" min="<?= date('Y-m-d') ?>" /> 
                <textarea class="todo-textarea" name="task_description" placeholder="Mô tả chi tiết công việc (không bắt buộc)..." rows="3"></textarea>
                <button class="add-btn" type="submit" title="Thêm công việc">+</button>
            </form>
        </div>

        <!-- Danh sách công việc -->
        <div class="task-list">
            <h2>📝 Danh sách công việc của bạn</h2>
            
            <?php if ($result->num_rows > 0): ?>
                <ul class="task-items">
                    <?php while ($row = $result->fetch_assoc()): 
                        $isOverdue = false;
                        $isToday = false;
                        $deadlineClass = '';
                        
                        if (!empty($row['NgayHetHan']) && $row['NgayHetHan'] != '0000-00-00') {
                            $today = date('Y-m-d');
                            $deadline = $row['NgayHetHan'];
                            
                            if ($deadline < $today && !$row['TrangThai']) {
                                $isOverdue = true;
                                $deadlineClass = 'overdue';
                            } elseif ($deadline == $today && !$row['TrangThai']) {
                                $isToday = true;
                                $deadlineClass = 'today';
                            }
                        }
                        
                        $itemClass = $row['TrangThai'] ? 'task-item completed' : 'task-item';
                    ?>
                        <li class="<?= $itemClass ?>">
                            <form method="POST" action="complete_task.php">
                                <input type="hidden" name="id" value="<?= $row['ID'] ?>">
                                <input class="task-checkbox" type="checkbox" onchange="this.form.submit()" <?= $row['TrangThai'] ? 'checked' : '' ?>>
                            </form>

                            <div class="task-content">
                                <span class="task-title <?= $row['TrangThai'] ? 'done' : '' ?>">
                                    <?= htmlspecialchars($row['TieuDe']) ?>
                                </span>

                                <?php if (!empty($row['MoTa'])): ?>
                                    <hr class="task-partition">
                                    <div class="task-desc">
                                        <strong>Mô tả:</strong> <?= htmlspecialchars($row['MoTa']) ?>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($row['NgayHetHan']) && $row['NgayHetHan'] != '0000-00-00'): ?>
                                    <hr class="task-partition">
                                    <div class="task-deadline <?= $deadlineClass ?>">
                                        <strong>Hạn chót:</strong> <?= date("d/m/Y", strtotime($row['NgayHetHan'])) ?>
                                        <?php if ($isOverdue): ?>
                                            <span style="color: #dc3545; font-weight: bold;"> ⚠️ Quá hạn!</span>
                                        <?php elseif ($isToday): ?>
                                            <span style="color: #fd7e14; font-weight: bold;"> 📅 Hôm nay!</span>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="btn-gr">
                                <a title="Chỉnh sửa công việc" href="edit_task.php?id=<?= $row['ID'] ?>" class="edit-btn">✏️</a>
                                <form method="POST" action="delete_task.php" style="display:inline;" 
                                onsubmit="return confirm('⚠️ Bạn có chắc chắn muốn xóa công việc này không?\n\nHành động này không thể hoàn tác!');">
                                    <input type="hidden" name="id" value="<?= $row['ID'] ?>">
                                    <button type="submit" title="Xóa công việc" class="delete-task">🗑️</button>
                                </form>
                            </div>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <div class="empty-state">
                    <h3>🎉 Chưa có công việc nào!</h3>
                    <p>Hãy thêm công việc đầu tiên của bạn bằng cách sử dụng form bên trên.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Tự động submit form khi checkbox thay đổi
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('.task-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    // Thêm hiệu ứng loading
                    const taskItem = this.closest('.task-item');
                    taskItem.style.opacity = '0.6';
                    
                    // Submit form
                    this.closest('form').submit();
                });
            });
        });
    </script>
</body>
</html>