<?php
session_start();
require 'db.php';

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

$task_id = intval($_GET['id']);
$user_id = $_SESSION['id'];

// Kiểm tra quyền sở hữu
$stmt = $conn->prepare("SELECT * FROM CongViec WHERE ID = ? AND ID_NguoiDung = ?");
$stmt->bind_param("ii", $task_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: index.php?error=not_found');
    exit;
}

$task = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $deadline = !empty($_POST['deadline']) ? $_POST['deadline'] : null;
    $priority = $_POST['priority'] ?? 'trung_binh';

    if (empty($title)) {
        $error = "Tiêu đề không được để trống!";
    } else {
        // Cập nhật với cả priority nếu có cột
        $check_column = $conn->query("SHOW COLUMNS FROM CongViec LIKE 'DoUuTien'");
        
        if ($check_column->num_rows > 0) {
            $stmt = $conn->prepare("UPDATE CongViec SET TieuDe = ?, MoTa = ?, NgayHetHan = ?, DoUuTien = ? WHERE ID = ? AND ID_NguoiDung = ?");
            $stmt->bind_param("ssssii", $title, $description, $deadline, $priority, $task_id, $user_id);
        } else {
            $stmt = $conn->prepare("UPDATE CongViec SET TieuDe = ?, MoTa = ?, NgayHetHan = ? WHERE ID = ? AND ID_NguoiDung = ?");
            $stmt->bind_param("sssii", $title, $description, $deadline, $task_id, $user_id);
        }
        
        if ($stmt->execute()) {
            header("Location: index.php?success=updated");
            exit;
        } else {
            $error = "Có lỗi xảy ra khi cập nhật!";
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa công việc</title>
    <link rel="stylesheet" href="assets/edit.css">
    
</head>
<body>
    <div class="container">
        <div class="breadcrumb">
            <a href="index.php">📋 Danh sách công việc</a>
            <span>›</span>
            <span>Chỉnh sửa công việc</span>
        </div>

        <div class="header">
            <h1>✏️ Chỉnh sửa công việc</h1>
            <p>Cập nhật thông tin cho công việc của bạn</p>
        </div>

        <div class="task-info">
            <h3>Thông tin hiện tại</h3>
            <div class="task-meta">
                <span>📅 Tạo: <?= date('d/m/Y', strtotime($task['NgayTao'] ?? date('Y-m-d'))) ?></span>
                <span>⏰ Trạng thái: <?= $task['TrangThai'] ? '✅ Đã hoàn thành' : '📝 Chưa hoàn thành' ?></span>
            </div>
        </div>

        <?php if (isset($error)): ?>
            <div class="error-message">
                ⚠️ <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" id="editForm">
            <input type="hidden" name="id" value="<?= htmlspecialchars($task['ID']) ?>">
            
            <div class="form-group">
                <label for="title">📝 Tiêu đề công việc</label>
                <input 
                    type="text" 
                    id="title" 
                    name="title" 
                    class="form-control" 
                    value="<?= htmlspecialchars($task['TieuDe']) ?>" 
                    required
                    placeholder="Nhập tiêu đề công việc..."
                >
            </div>

            <div class="form-group">
                <label for="priority">🎯 Độ ưu tiên</label>
                <select id="priority" name="priority" class="form-control priority-select">
                    <option value="thap" <?= (($task['DoUuTien'] ?? '') == 'thap') ? 'selected' : '' ?>>🟢 Thấp</option>
                    <option value="trung_binh" <?= (($task['DoUuTien'] ?? 'trung_binh') == 'trung_binh') ? 'selected' : '' ?>>🟡 Trung bình</option>
                    <option value="cao" <?= (($task['DoUuTien'] ?? '') == 'cao') ? 'selected' : '' ?>>🔴 Cao</option>
                </select>
            </div>

            <div class="form-group">
                <label for="deadline">📅 Hạn chót</label>
                <input 
                    type="date" 
                    id="deadline" 
                    name="deadline" 
                    class="form-control" 
                    value="<?= htmlspecialchars($task['NgayHetHan']) ?>"
                    min="<?= date('Y-m-d') ?>"
                >
                <small style="color: #6c757d; font-size: 0.8rem; margin-top: 5px; display: block;">
                    Để trống nếu không có hạn chót cụ thể
                </small>
            </div>
            
            <div class="form-group">
                <label for="description">📄 Mô tả chi tiết</label>
                <textarea 
                    id="description" 
                    name="description" 
                    class="form-control" 
                    rows="4"
                    placeholder="Mô tả chi tiết về công việc này..."
                ><?= htmlspecialchars($task['MoTa']) ?></textarea>
            </div>

            <div class="button-group">
                <a href="index.php" class="btn btn-secondary">
                    ❌ Hủy bỏ
                </a>
                
                <button type="button" class="btn btn-delete" onclick="deleteTask(<?= $task['ID'] ?>)">
                    🗑️ Xóa
                </button>
                
                <button type="submit" class="btn btn-primary" id="saveBtn">
                    <span class="loading"></span>
                    💾 Lưu thay đổi
                </button>
            </div>
        </form>
    </div>

    <script>
        // Form validation
        document.getElementById('editForm').addEventListener('submit', function(e) {
            const title = document.getElementById('title').value.trim();
            const deadline = document.getElementById('deadline').value;
            const saveBtn = document.getElementById('saveBtn');
            
            if (!title) {
                e.preventDefault();
                alert('⚠️ Vui lòng nhập tiêu đề công việc!');
                return;
            }
            
            if (deadline && deadline < new Date().toISOString().split('T')[0]) {
                if (!confirm('⚠️ Hạn chót đã qua. Bạn có chắc chắn muốn tiếp tục?')) {
                    e.preventDefault();
                    return;
                }
            }
            
            // Show loading
            saveBtn.classList.add('loading');
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<span class="loading"></span> Đang lưu...';
        });

        // Delete function
        function deleteTask(taskId) {
            if (confirm('🗑️ Bạn có chắc chắn muốn xóa công việc này không?\n\nHành động này không thể hoàn tác!')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'delete_task.php';
                
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'id';
                input.value = taskId;
                
                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Auto-save draft (optional)
        let autoSaveTimer;
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('input', function() {
                clearTimeout(autoSaveTimer);
                autoSaveTimer = setTimeout(() => {
                    console.log('💾 Đã lưu nháp tự động');
                    // Có thể implement auto-save ở đây
                }, 2000);
            });
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl + S để lưu
            if (e.ctrlKey && e.key === 's') {
                e.preventDefault();
                document.getElementById('editForm').submit();
            }
            
            // Escape để hủy
            if (e.key === 'Escape') {
                if (confirm('❓ Bạn có muốn hủy bỏ các thay đổi?')) {
                    window.location.href = 'index.php';
                }
            }
        });
    </script>
</body>
</html>