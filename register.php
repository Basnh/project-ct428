<?php
session_start();
include 'db.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Kiểm tra xem tên đăng nhập đã tồn tại chưa
    $check_user = "SELECT * FROM user WHERE username = '$username'";

    //query câu truy vấn và lưu vào biến result_check
    $result_check = $conn->query($check_user);

    //nếu số dòng biến result_check lớn hơn 0 thì có user ở db
    if ($result_check->num_rows > 0) {
        $message = "Tên đăng nhập đã tồn tại. Vui lòng chọn tên khác.";
    } else {
        // Thêm người dùng mới
        $add_user = "INSERT INTO user (TenDangNhap, MatKhau) VALUES ('$username', '$password')";
        if ($conn->query($add_user) === TRUE) {
            $message = 'Tài khoản ' . $username.' đã đăng ký thành công!';
        } else {
            $message = "Lỗi: ";
        }
    }
}
$conn->close();
?>