<?php
session_start();
include 'db.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone_number = trim($_POST['phone_number']);
    
    // Validate input
    if (empty($username) || empty($password) || empty($confirm_password)) {
        $message = "Vui lòng điền đầy đủ thông tin bắt buộc.";
    } elseif ($password !== $confirm_password) {
        $message = "Mật khẩu xác nhận không khớp.";
    } elseif (strlen($password) < 6) {
        $message = "Mật khẩu phải có ít nhất 6 ký tự.";
    } elseif (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Định dạng email không hợp lệ.";
    } elseif (!empty($phone_number) && !preg_match('/^[0-9]{10,11}$/', $phone_number)) {
        $message = "Số điện thoại phải có 10-11 chữ số.";
    } else {
        // Kiểm tra xem tên đăng nhập đã tồn tại chưa
        $stmt = $conn->prepare("SELECT ID FROM NguoiDung WHERE TenDangNhap = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $message = "Tên đăng nhập đã tồn tại. Vui lòng chọn tên khác.";
        } else {
            // Kiểm tra email đã tồn tại chưa (nếu có nhập)
            if (!empty($email)) {
                $stmt = $conn->prepare("SELECT ID FROM NguoiDung WHERE Email = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    $message = "Email đã được sử dụng. Vui lòng chọn email khác.";
                } else {
                    $create_account = true;
                }
            } else {
                $create_account = true;
            }
            
            if (isset($create_account) && $create_account) {
                // Hash mật khẩu trước khi lưu
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // Thêm người dùng mới với đầy đủ thông tin
                $stmt = $conn->prepare("INSERT INTO NguoiDung (TenDangNhap, MatKhau, HoTen, Email, SoDienThoai) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("sssss", $username, $hashed_password, $full_name, $email, $phone_number);
                
                if ($stmt->execute()) {
                    $message = "Tài khoản '$username' đã đăng ký thành công! Bạn có thể đăng nhập ngay.";
                } else {
                    $message = "Lỗi: Không thể tạo tài khoản. Vui lòng thử lại.";
                }
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
                    <label for="username">Tên đăng nhập <span class="required">*</span>:</label>
                    <input type="text" id="username" name="username" required minlength="3" maxlength="50" value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>">
                </div>

                <div class="form-group">
                    <label for="full_name">Họ và tên:</label>
                    <input type="text" id="full_name" name="full_name" maxlength="100" value="<?= isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : '' ?>">
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" maxlength="100" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                    <div class="form-note">Email sẽ được sử dụng để khôi phục mật khẩu</div>
                </div>

                <div class="form-group">
                    <label for="phone_number">Số điện thoại:</label>
                    <input type="tel" id="phone_number" name="phone_number" pattern="[0-9]{10,11}" placeholder="Ví dụ: 0123456789" value="<?= isset($_POST['phone_number']) ? htmlspecialchars($_POST['phone_number']) : '' ?>">
                    <div class="form-note">Số điện thoại gồm 10-11 chữ số</div>
                </div>
                
                <div class="form-group">
                    <label for="password">Mật khẩu <span class="required">*</span>:</label>
                    <input type="password" id="password" name="password" required minlength="6">
                    <div class="form-note">Mật khẩu phải có ít nhất 6 ký tự</div>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Nhập lại mật khẩu <span class="required">*</span>:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required minlength="6">
                </div>

                <button type="submit" class="login-btn">Đăng ký</button>
            </form>
            
            <div class="register-link">
                <p>Đã có tài khoản? <a href="login.php">Đăng nhập ngay</a></p>
            </div>
        </div>
    </div>

    <script>
        // Validation thời gian thực
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (confirmPassword && password !== confirmPassword) {
                this.style.borderColor = '#dc3545';
            } else {
                this.style.borderColor = '#e1e5e9';
            }
        });

        // Validation email
        document.getElementById('email').addEventListener('input', function() {
            const email = this.value;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (email && !emailRegex.test(email)) {
                this.style.borderColor = '#dc3545';
            } else {
                this.style.borderColor = '#e1e5e9';
            }
        });

        // Validation số điện thoại
        document.getElementById('phone_number').addEventListener('input', function() {
            const phone = this.value;
            const phoneRegex = /^[0-9]{10,11}$/;
            
            if (phone && !phoneRegex.test(phone)) {
                this.style.borderColor = '#dc3545';
            } else {
                this.style.borderColor = '#e1e5e9';
            }
        });
    </script>
</body>
</html>