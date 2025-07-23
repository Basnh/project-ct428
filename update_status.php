<?php
session_start();
require 'db.php';

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['id'];
    $task_id = $_POST['id'];
    $status = $_POST['status'];
    
    // Validate input
    if (!in_array($status, ['chua_bat_dau', 'dang_thuc_hien', 'da_hoan_thanh'])) {
        header('Location: index.php?error=invalid_status');
        exit;
    }
    
    // Kiểm tra quyền sở hữu
    $stmt = $conn->prepare("SELECT ID FROM CongViec WHERE ID = ? AND ID_NguoiDung = ?");
    $stmt->bind_param("ii", $task_id, $user_id);
    $stmt->execute();
    
    if ($stmt->get_result()->num_rows === 0) {
        header('Location: index.php?error=not_found');
        exit;
    }
    
    // Cập nhật trạng thái
    $trang_thai_cu = ($status == 'da_hoan_thanh') ? 1 : 0;
    
    $stmt = $conn->prepare("UPDATE CongViec SET TrangThaiChiTiet = ?, TrangThai = ? WHERE ID = ? AND ID_NguoiDung = ?");
    $stmt->bind_param("siii", $status, $trang_thai_cu, $task_id, $user_id);
    
    if ($stmt->execute()) {
        header('Location: index.php?success=status_updated');
    } else {
        header('Location: index.php?error=update_failed');
    }
    
    $stmt->close();
    $conn->close();
} else {
    header('Location: index.php');
}
?>