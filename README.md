# 📋 Hệ thống Quản lý Công việc (To-Do List nâng cao)

Đây là một ứng dụng web giúp người dùng quản lý các công việc cá nhân của mình. Mỗi người dùng có thể đăng ký, đăng nhập và quản lý danh sách công việc riêng.

---

## 🚀 Tính năng chính

- [x] Đăng ký người dùng mới
- [x] Đăng nhập / Đăng xuất
- [x] Thêm / Sửa / Xóa công việc
- [x] Hiển thị danh sách công việc của từng người dùng
- [x] Đánh dấu công việc là **hoàn thành** hoặc **chưa hoàn thành**
- [x] Bảo vệ truy cập bằng **session**

---

## 🛠️ Công nghệ sử dụng

| Thành phần       | Công nghệ        |
|------------------|------------------|
| Ngôn ngữ backend | PHP thuần        |
| Cơ sở dữ liệu     | MySQL            |
| Giao diện frontend| HTML + CSS (thuần) |
| Server local     | XAMPP / Laragon  |

---

## 📁 Cấu trúc thư mục
todo-app/
├── css/
│ └── style.css
├── db.php
├── session.php
├── database.sql
├── register.php
├── login.php
├── logout.php
├── index.php
├── add_task.php
├── edit_task.php
├── delete_task.php
└── complete_task.php


---

## ⚙️ Hướng dẫn cài đặt & chạy ứng dụng

### Bước 1: Cài đặt môi trường
- Cài đặt **XAMPP**
- Đảm bảo **Apache** và **MySQL** đang chạy

### Bước 2: Tạo cơ sở dữ liệu
- Mở `phpMyAdmin` tại `http://localhost/phpmyadmin`
- Tạo database mới tên: `todo_app`
- Import file `database.sql` đi kèm để tạo bảng

### Bước 3: Cấu hình kết nối database
- Mở file `db.php` và chỉnh thông tin kết nối MySQL nếu cần:
```php
$mysqli = new mysqli("localhost", "root", "", "todo_app");

Bước 4: Đặt source code vào thư mục server
Ví dụ: C:\xampp\htdocs\todo-app

Bước 5: Chạy ứng dụng
Truy cập trình duyệt:
👉 http://localhost/todo-app/register.php – Đăng ký tài khoản
👉 http://localhost/todo-app/login.php – Đăng nhập
👉 http://localhost/todo-app/index.php – Quản lý công việc

👨‍💻 Nhóm thực hiện
Tô Anh Khải: Quản lý người dùng (đăng ký, đăng nhập, session)

Võ Phúc Khang: Xử lý công việc (CRUD)

Đỗ Nhật Anh : Trạng thái & bảo mật	

✅ Ghi chú
Mật khẩu người dùng được mã hóa bằng password_hash().

Ứng dụng không sử dụng framework để dễ học và triển khai cho sinh viên. (?)

Giao diện đơn giản, dễ mở rộng bằng Bootstrap hoặc jQuery nếu cần.


