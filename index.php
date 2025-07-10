<?php
session_start();
$_SESSION['user_id'] = 1; // Gán user test  
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM CongViec WHERE ID_NguoiDung = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$conn -> close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="assets/style.css">    
</head>
<body>
    <div class="container">
        <div class="content">
            <h1>Your To Do</h1>
            <form class="todo-form" method="POST" action="add_task.php">
                <input class="todo-input" type="text" name="task_title" placeholder="Thêm việc cần làm" required />
                <textarea class="todo-textarea" name="task_description" placeholder="Mô tả chi tiết"></textarea>
                <input class="todo-deadline" type="date" name="task_deadline" /> 
                <button class="add-btn" type="submit">+</button>
            </form>
        </div>

        <div class="task-list">
            <ul class="task-items">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <li class="task-item">
                        <form method="get" action="complete_task.php">
                            <input type="hidden" name="id" value="<?= $row['ID'] ?>">
                            <input class="task-checkbox" type="checkbox" onchange="this.form.submit()" <?= $row['TrangThai'] ? 'checked' : '' ?>>
                        </form>

                        <div class="task-content">
                            <span class="task-title <?= $row['TrangThai'] ? 'done' : '' ?>">
                                <?= htmlspecialchars($row['TieuDe']) ?>
                            </span>

                            <?php if (!empty($row['MoTa'])): ?>
                                <hr class="task-partition">
                                <div class="task-desc">Mô tả: <?= htmlspecialchars($row['MoTa']) ?></div>
                            <?php endif; ?>

                            <?php if (!empty($row['NgayHetHan']) && $row['NgayHetHan'] != '0000-00-00'): ?>
                                <hr class="task-partition">
                                <div class="task-deadline">Hạn chót: <?= date("d/m/Y", strtotime($row['NgayHetHan'])) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="btn-gr">
                            <a title="Chỉnh sửa task" href="edit_task.php?id=<?= $row['ID'] ?>" class="edit-btn">✎</a>
                            <form method="POST" action="delete_task.php" style="display:inline;" 
                            onsubmit="return confirm('Bạn có chắc muốn xóa task này không?');">
                                <input type="hidden" name="id" value="<?= $row['ID'] ?>">
                                <button type="submit" title="Xóa task" class="delete-task">✕</button>
                            </form>
                        </div>
                    </li>
                <?php endwhile; ?>
            </ul>
        </div>
    </div>
</body>
</html>