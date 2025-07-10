<?php
// ...Code của phần khác nằm ở trên đây...
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$task_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// Kiểm tra quyền sở hữu
$stmt = $conn->prepare("SELECT * FROM CongViec WHERE ID = ? AND ID_NguoiDung = ?");
$stmt->bind_param("ii", $task_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('HTTP/1.1 403 Forbidden');
    exit('Bạn không có quyền sửa công việc này.');
}

$task = $result->fetch_assoc();

// ...tiếp tục xử lý cập nhật công việc...
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $deadline = $_POST['deadline'] ?? null;

    $stmt = $conn->prepare("UPDATE CongViec SET TieuDe = ?, MoTa = ?, NgayHetHan = ? WHERE ID = ? AND ID_NguoiDung = ?");
    $stmt->bind_param("sssii", $title, $description, $deadline, $task_id, $user_id);
    $stmt->execute();
    $stmt->close();

    header("Location: index.php");
    exit;
}

$conn->close();
?>

<!-- HTML -->
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chỉnh sửa công việc</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <h1>Chỉnh sửa công việc</h1>
        <form class="edit-form" method="post" action="">
            <input type="hidden" name="id" value="<?= htmlspecialchars($task['ID']) ?>">
            
            <label>Tiêu đề:</label>
            <input class="edit-title" type="text" name="title" value="<?= htmlspecialchars($task['TieuDe']) ?>" required>
            
            <label>Mô tả:</label>
            <textarea class="edit-desc" name="description" rows="4"><?= htmlspecialchars($task['MoTa']) ?></textarea>

            <label>Hạn chót:</label>
            <input class="edit-deadline" type="date" name="deadline" value="<?= htmlspecialchars($task['NgayHetHan']) ?>">
            
            <br><br>
            <button class="save-btn" type="submit">Lưu</button>
            <a class="cancel-btn" href="index.php">Huỷ</a>
        </form>
    </div>
</body>
</html>
