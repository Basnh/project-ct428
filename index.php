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
    <title>Todo List Manager</title>
    <link rel="stylesheet" href="assets/index.css">
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <h1>📋 Todo List Manager</h1>
            
            <div class="nav-section">
                <div class="nav-title">Navigation</div>
                <div class="nav-item">
                    <span class="icon">📝</span>
                    Action Items
                    <span class="count"><?= $stats['pending'] ?></span>
                </div>
                <div class="nav-item">
                    <span class="icon">📅</span>
                    Today
                    <span class="count">0</span>
                </div>
                <div class="nav-item">
                    <span class="icon">⏰</span>
                    Tomorrow
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
                    Timeline
                </div>
                <div class="nav-item">
                    <span class="icon">🔄</span>
                    In Progress
                    <span class="count"><?= $stats['pending'] ?></span>
                </div>
                <div class="nav-item active">
                    <span class="icon">✅</span>
                    Completed
                    <span class="count"><?= $stats['completed'] ?></span>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="content-header">
                <h1 class="content-title">Todo List</h1>
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
                        <button class="filter-btn active">📝 To Do</button>
                        <button class="filter-btn">🔄 In Progress</button>
                        <button class="filter-btn">✅ Completed</button>
                        <button class="filter-btn">📋 All</button>
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
                                $statusClass = $row['TrangThai'] ? 'status-completed' : 'status-not-started';
                                $statusText = $row['TrangThai'] ? 'Completed' : 'Not started';
                                $progress = $row['TrangThai'] ? 100 : 0;
                                
                                if (!empty($row['NgayHetHan']) && $row['NgayHetHan'] != '0000-00-00') {
                                    $today = date('Y-m-d');
                                    $deadline = $row['NgayHetHan'];
                                    
                                    if ($deadline < $today && !$row['TrangThai']) {
                                        $isOverdue = true;
                                        $priorityClass = 'priority-high';
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
                                        <span class="status-badge <?= $statusClass ?>"><?= $statusText ?></span>
                                    </td>
                                    <td>
                                        <span class="priority-badge <?= $priorityClass ?>">
                                            <?= $isOverdue ? 'Urgent' : 'Medium' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (!empty($row['NgayHetHan']) && $row['NgayHetHan'] != '0000-00-00'): ?>
                                            <?= date("M j, Y", strtotime($row['NgayHetHan'])) ?>
                                        <?php else: ?>
                                            <span style="color: #6c757d;">No date</span>
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
                                            <a href="edit_task.php?id=<?= $row['ID'] ?>" class="btn-sm btn-edit">Edit</a>
                                            <form method="POST" action="delete_task.php" style="display: inline;" 
                                                onsubmit="return confirm('Bạn có chắc chắn muốn xóa công việc này?');">
                                                <input type="hidden" name="id" value="<?= $row['ID'] ?>">
                                                <button type="submit" class="btn-sm btn-delete">Delete</button>
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
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
            });
        });

        // Checkbox handling
        document.querySelectorAll('.task-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const row = this.closest('tr');
                row.style.opacity = '0.6';
                this.closest('form').submit();
            });
        });
    </script>
</body>
</html>