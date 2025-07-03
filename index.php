<?php
// ...Code của phần khác nằm ở trên đây
// Code sử dụng để khi hiển thị danh sách, chỉ lấy công việc của người dùng hiện tại ...
session_start();
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

// Hiển thị danh sách công việc
// ...Code của phần khác nằm ở phía dưới...