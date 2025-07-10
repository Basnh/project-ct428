<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "todo_app";

// Kết nối CSDL
$conn = new mysqli($host, $username, $password, $database);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Đặt charset để tránh lỗi font
$conn->set_charset("utf8");
?>
