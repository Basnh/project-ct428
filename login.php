<?php
session_start();
include 'db.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM user WHERE username = '$username' AND passwd = '$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        //lấy dữ liệu user tìm được trong biến result
        $row = $result->fetch_assoc();
        $_SESSION['id'] = $row['id'];
        $_SESSION['username'] = $row['username'];
        header("Location: index.php"); // Chuyển hướng đến trang quản lý công việc
        exit();
    } else {
        $message = "Tên đăng nhập hoặc mật khẩu không đúng.";
    }
}
$conn->close();
?>