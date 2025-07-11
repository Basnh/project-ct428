<?php
session_start(); // Nếu có login thì bật session
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST['task_title']);
    $description = trim($_POST['task_description']);
    $deadline = $_POST['task_deadline'] ?? null;
    $user_id = $_SESSION['user_id'];
    
    if (!empty($title)) {
        // Nếu deadline trống, set null
        $deadline = !empty($deadline) ? $deadline : null;

        $stmt = $conn->prepare("INSERT INTO CongViec (TieuDe, MoTa, NgayHetHan, TrangThai, ID_NguoiDung) VALUES (?, ?, ?, 0, ?)");
        $stmt->bind_param("sssi", $title, $description, $deadline, $user_id);
        
        if ($stmt->execute()) {
            $stmt->close();
            header("Location: index.php");
            exit();
        } else {
            echo "Lỗi khi thêm công việc: " . $stmt->error;
        }
    } else {
        echo "Tên công việc không được để trống!";
    }
} else {
    echo "Dữ liệu không hợp lệ!";
}

$conn -> close();
?>