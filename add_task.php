<?php
session_start();
require 'db.php';

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['id'];
    $title = trim($_POST['task_title']);
    $description = trim($_POST['task_description']);
    $deadline = $_POST['task_deadline'];
    $priority = $_POST['task_priority']; // Thêm priority
    
    if (empty($title)) {
        header('Location: index.php?error=empty_title');
        exit;
    }
    
    // Nếu không có deadline, set NULL
    if (empty($deadline)) {
        $deadline = null;
    }
    
    // Validate priority
    if (!in_array($priority, ['thap', 'trung_binh', 'cao'])) {
        $priority = 'trung_binh';
    }
    
    $stmt = $conn->prepare("INSERT INTO CongViec (TieuDe, MoTa, NgayHetHan, DoUuTien, TrangThai, ID_NguoiDung) VALUES (?, ?, ?, ?, 0, ?)");
    $stmt->bind_param("ssssi", $title, $description, $deadline, $priority, $user_id);
    
    if ($stmt->execute()) {
        header('Location: index.php?success=added');
    } else {
        header('Location: index.php?error=add_failed');
    }
    
    $stmt->close();
    $conn->close();
} else {
    header('Location: index.php');
}
?>