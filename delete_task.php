<?php
// ...Code của phần khác nằm ở trên đây...
// ...Code để kiểm tra quyền trước khi xóa task...
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $task_id = intval($_POST['id']); 
    $user_id = $_SESSION['user_id'];

    // Kiểm tra quyền sở hữu
    // ...Không lấy ID_NguoiDung từ client, chỉ lấy từ session...
    $stmt = $conn->prepare("SELECT * FROM CongViec WHERE ID = ? AND ID_NguoiDung = ?");
    $stmt->bind_param("ii", $task_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        header('HTTP/1.1 403 Forbidden');
        exit('Bạn không có quyền xóa công việc này.');
    }

    // ...tiếp tục xử lý xóa công việc...
    $stmt = $conn->prepare("DELETE FROM CongViec WHERE ID = ? AND ID_NguoiDung = ?");
    $stmt->bind_param("ii", $task_id, $user_id);
    $stmt->execute();
    $stmt->close();

    header('Location: index.php');
    exit;
}

$conn -> close();
?>
