<?php
session_start();
require 'db.php'; // Kết nối với cơ sở dữ liệu tại db.php

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
    // Không có quyền
    header('HTTP/1.1 403 Forbidden');
    exit('Bạn không có quyền thao tác công việc này.');
}

// Đánh dấu hoàn thành/chưa hoàn thành
$row = $result->fetch_assoc();
$new_status = $row['TrangThai'] ? 0 : 1;

$update = $conn->prepare("UPDATE CongViec SET TrangThai = ? WHERE ID = ?");
$update->bind_param("ii", $new_status, $task_id);
$update->execute();

header('Location: index.php');
exit;
?>