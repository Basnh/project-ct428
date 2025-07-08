<?php
$servername = "localhost"; 
$username = "root";
$passwd = ""; 
$dbname = "todo-list"; 

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: ");
}
?>