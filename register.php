<?php
session_start();
include 'db.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    
    // Validate input
    if (empty($username) || empty($password) || empty($confirm_password)) {
        $message = "Vui lòng điền đầy đủ thông tin.";
    } elseif ($password !== $confirm_password) {
        $message = "Mật khẩu xác nhận không khớp.";
    } elseif (strlen($password) < 6) {
        $message = "Mật khẩu phải có ít nhất 6 ký tự.";
    } else {
        // Kiểm tra xem tên đăng nhập đã tồn tại chưa (sử dụng prepared statement)
        $stmt = $conn->prepare("SELECT ID FROM NguoiDung WHERE TenDangNhap = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $message = "Tên đăng nhập đã tồn tại. Vui lòng chọn tên khác.";
        } else {
            // Hash mật khẩu trước khi lưu
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Thêm người dùng mới
            $stmt = $conn->prepare("INSERT INTO NguoiDung (TenDangNhap, MatKhau) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $hashed_password);
            
            if ($stmt->execute()) {
                $message = "Tài khoản '$username' đã đăng ký thành công! Bạn có thể đăng nhập ngay.";
            } else {
                $message = "Lỗi: Không thể tạo tài khoản. Vui lòng thử lại.";
            }
        }
        $stmt->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký - Hệ thống Quản lý Công việc</title>
    <link rel="stylesheet" href="assets/register.css">
    
</head>
<body>
    <div class="login-container">
        <div class="login-form">
            <div class="login-header">
                <h1>📝 Đăng ký</h1>
                <p>Tạo tài khoản mới để sử dụng hệ thống</p>
            </div>
            
            <?php if (!empty($message)): ?>
                <?php if (strpos($message, 'thành công') !== false): ?>
                    <div class="success-message">
                        <?= htmlspecialchars($message) ?>
                    </div>
                <?php else: ?>
                    <div class="error-message">
                        <?= htmlspecialchars($message) ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Tên đăng nhập:</label>
                    <input type="text" id="username" name="username" required minlength="3" maxlength="50">
                </div>

                <div class="form-group">
                    <label for="full_name">Họ và tên:</label>
                    <input type="text" id="full_name" name="full_name" maxlength="50">
                </div>

                <div class="form-group">
                    <label for="phone_number">Số điện thoại:</label>
                    <input type="tel" id="phone_number" name="phone_number" required length="10">
                </div>
                
                <div class="form-group">
                    <label for="password">Mật khẩu:</label>
                    <input type="password" id="password" name="password" required minlength="6">
                </div>

                <div class="form-group">
                    <label for="confirm_password">Nhập lại mật khẩu:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required minlength="6">
                </div>

                

                <button type="submit" class="login-btn">Đăng ký</button>
            </form>
            
            <div class="register-link">
                <p>Đã có tài khoản? <a href="login.php">Đăng nhập ngay</a></p>
            </div>
        </div>
    </div>
</body>
</html>