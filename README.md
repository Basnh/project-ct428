# 📋 Hệ thống Quản lý Công việc (To-Do List nâng cao)

Đây là một ứng dụng web giúp người dùng quản lý các công việc cá nhân của mình. Mỗi người dùng có thể đăng ký, đăng nhập và quản lý danh sách công việc riêng.

---

## 🚀 Tính năng chính

### 👤 Quản lý người dùng
- [x] Đăng ký người dùng mới với validation
- [x] Đăng nhập / Đăng xuất an toàn
- [x] Bảo vệ truy cập bằng **session**
- [x] Mã hóa mật khẩu với `password_hash()`

### 📝 Quản lý công việc
- [x] Thêm công việc mới với thông tin chi tiết
- [x] Sửa / Xóa công việc có xác nhận
- [x] Hiển thị danh sách công việc của từng người dùng
- [x] Đánh dấu công việc **hoàn thành** / **chưa hoàn thành**
- [x] **Độ ưu tiên** công việc (Thấp, Trung bình, Cao)
- [x] **Hạn chót** cho từng công việc
- [x] **Mô tả chi tiết** công việc
- [x] **Tự động phát hiện công việc quá hạn**


---

## 🛠️ Công nghệ sử dụng

| Thành phần         | Công nghệ                    | Mô tả |
|--------------------|------------------------------|-------|
| **Backend**        | PHP 7.4+ (thuần)           | Xử lý logic server |
| **Database**       | MySQL 5.7+                  | Lưu trữ dữ liệu |
| **Frontend**       | HTML5 + CSS3 + JavaScript   | Giao diện người dùng |
| **Styling**        | CSS Grid + Flexbox          | Layout responsive |
| **Security**       | Session + Password Hash      | Bảo mật |
| **Server**         | XAMPP / Laragon / WAMP       | Môi trường phát triển |

---

## 📁 Cấu trúc thư mục

```bash
project-ct428/
└── 📂 assets/                     # Tài nguyên 
    ├── 📄style.css                 # CSS dành cho trang login.php
    ├── 📄edit.css                  # CSS dành cho trang edit.php
    ├── 📄register.css              # CSS dành cho trang register.php
    ├── 📄index.css                 # CSS dành cho trang index.php
├── 📄 index.php                    # Trang chính - quản lý công việc
├── 📄 login.php                    # Đăng nhập
├── 📄 register.php                 # Đăng ký tài khoản
├── 📄 logout.php                   # Đăng xuất
├── 📄 db.php                       # Kết nối cơ sở dữ liệu
├── 📄 add_task.php                 # Thêm công việc mới
├── 📄 edit_task.php                # Chỉnh sửa công việc
├── 📄 delete_task.php              # Xóa công việc
├── 📄 complete_task.php            # Đánh dấu hoàn thành
├── 📄 database_setup.sql           # Script tạo cơ sở dữ liệu
├── 📄 update_status.php            # Cập nhật trạng thái cho công việc
├── 📄 README.md                    # Tài liệu hướng dẫn

```

---

## ⚙️ Hướng dẫn cài đặt & chạy ứng dụng

### 📋 Yêu cầu hệ thống
- **PHP** 7.4 hoặc cao hơn
- **MySQL** 5.7 hoặc cao hơn  
- **Apache Server** (XAMPP/Laragon/WAMP)

### 🔧 Bước 1: Cài đặt môi trường
1. Tải và cài đặt **XAMPP** từ [https://www.apachefriends.org](https://www.apachefriends.org)
2. Khởi động **Apache** và **MySQL** trong XAMPP Control Panel
3. Đảm bảo port 80 (Apache) và 3306 (MySQL) không bị xung đột

### 🗄️ Bước 2: Tạo cơ sở dữ liệu
1. Mở trình duyệt và truy cập `http://localhost/phpmyadmin`
2. Tạo database mới với tên: `todo-list`
3. Chọn database vừa tạo và import file `database_setup.sql`
4. Kiểm tra các bảng đã được tạo thành công:
   - `NguoiDung` (Quản lý user)
   - `CongViec` (Quản lý task)

### ⚙️ Bước 3: Cấu hình kết nối database
Mở file `db.php` và điều chỉnh thông tin kết nối nếu cần:


### 📂 Bước 4: Đặt source code vào thư mục server
1. Copy toàn bộ folder `project-ct428` vào thư mục:
   - **XAMPP**: `C:\xampp\htdocs\`
   - **Laragon**: `C:\laragon\www\`
   - **WAMP**: `C:\wamp64\www\`

### 🚀 Bước 5: Chạy ứng dụng
Mở trình duyệt và truy cập:

| Trang | URL | Mô tả |
|-------|-----|-------|
| **Đăng ký** | `http://localhost/project-ct428/register.php` | Tạo tài khoản mới |
| **Đăng nhập** | `http://localhost/project-ct428/login.php` | Đăng nhập hệ thống |
| **Quản lý công việc** | `http://localhost/project-ct428/index.php` | Trang chính |

---

## 🗃️ Cấu trúc Database

### Bảng `NguoiDung`
| Cột | Kiểu dữ liệu | Mô tả |
|-----|--------------|-------|
| `ID` | INT (PK, AI) | ID người dùng |
| `TenDangNhap` | VARCHAR(50) | Tên đăng nhập |
| `MatKhau` | VARCHAR(255) | Mật khẩu đã mã hóa |
| `HoTen` | VARCHAR(100) | Họ và tên của người dùng |
| `Email` | VARCHAR(100) | Email người dùng |
| `SoDienThoai` | VARCHAR(15) | Số điện thoại của người dùng |

### Bảng `CongViec`
| Cột | Kiểu dữ liệu | Mô tả |
|-----|--------------|-------|
| `ID` | INT  | ID công việc |
| `TieuDe` | VARCHAR(200) | Tiêu đề công việc |
| `MoTa` | TEXT | Mô tả chi tiết |
| `NgayHetHan` | DATE | Hạn chót |
| `TrangThai` | BOOLEAN | Trạng thái (0: chưa, 1: hoàn thành) |
| `DoUuTien` | ENUM | Độ ưu tiên (thap, trung_binh, cao) |
| `ID_NguoiDung` | INT (FK) | ID người tạo |


---

## 👨‍💻 Nhóm thực hiện

| Thành viên  | Nhiệm vụ chính | Tiến độ hoàn thành 
|------------|---------|----------------|
| **Tô Anh Khải** | Quản lý người dùng (đăng ký, đăng nhập, session) | 100%
| **Võ Phúc Khang** | Xử lý CRUD công việc, database design | 100%
| **Đỗ Nhật Anh** (Nhóm trưởng) |  UI/UX design, bảo mật, tối ưu logic | 100%

---

## 🐛 Troubleshooting

### ❌ Lỗi thường gặp

**1. Không kết nối được database:**
```
Fatal error: Uncaught mysqli_sql_exception: Access denied
```
**Giải pháp:** Kiểm tra thông tin kết nối trong `db.php`

**2. Session không hoạt động:**
```
Warning: session_start(): Cannot send session cookie
```
**Giải pháp:** Đảm bảo `session_start()` được gọi trước khi output HTML

**3. CSS không load:**
**Giải pháp:** Kiểm tra đường dẫn file CSS và đảm bảo Apache đang chạy

---

## 🚀 Hướng phát triển

### 📋 Tính năng có thể mở rộng
- [ ] **API RESTful** cho mobile app
- [ ] **Real-time notifications** với WebSocket
- [ ] **File upload** cho công việc
- [ ] **Team collaboration** - chia sẻ công việc
- [ ] **Calendar integration** 
- [ ] **Export/Import** Excel/CSV
- [ ] **Dark mode** theme
- [ ] **PWA** support
- [ ] **Email reminders** cho deadline
- [ ] **Advanced search** và filtering

### 🛠️ Cải tiến kỹ thuật
- [ ] Chuyển sang **MVC pattern**
- [ ] Sử dụng **Composer** cho autoloading
- [ ] **Docker** containerization
- [ ] **Unit testing** với PHPUnit
- [ ] **CI/CD** pipeline

---
### Link Github của dự án: https://github.com/Basnh/project-ct428


