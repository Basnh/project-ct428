<?php
$servername = "localhost"; // Tên máy chủ MySQL
$username = "root"; // Tên người dùng MySQL (thường là root cho môi trường dev)
$password = ""; // Mật khẩu MySQL (để trống nếu không có)
$dbname = "todo-list"; // Tên cơ sở dữ liệu của bạn

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: ");
}
?>