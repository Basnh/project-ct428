<?php
session_start();
require 'db.php';

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

$task_id = intval($_POST['id']);
$user_id = $_SESSION['id'];

// Kiểm tra quyền sở hữu
$stmt = $conn->prepare("SELECT * FROM CongViec WHERE ID = ? AND ID_NguoiDung = ?");
$stmt->bind_param("ii", $task_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('HTTP/1.1 403 Forbidden');
    exit('Bạn không có quyền thao tác công việc này.');
}

$row = $result->fetch_assoc();
$new_status = $row['TrangThai'] ? 0 : 1;

// Cập nhật trạng thái (có kiểm tra quyền)
$update = $conn->prepare("UPDATE CongViec SET TrangThai = ? WHERE ID = ? AND ID_NguoiDung = ?");
$update->bind_param("iii", $new_status, $task_id, $user_id);
$update->execute();

$update->close();
$conn->close();

header('Location: index.php');
exit;
?>