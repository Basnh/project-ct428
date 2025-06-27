# project-ct428
Chủ đề 3: Hệ thống Quản lý Công việc (To-Do List nâng cao)
- Mô tả: Ứng dụng giúp người dùng quản lý các công việc của họ.
- Các thực thể (MySQL): NguoiDung (ID, Tên đăng nhập, Mật khẩu), CongViec (ID,
Tiêu đề, Mô tả, Ngày hết hạn, Trạng thái, ID_NguoiDung).
- Chức năng chính:
+ Đăng ký, Đăng nhập, Đăng xuất cho người dùng.
+ Mỗi người dùng chỉ xem và quản lý công việc của mình.
+ Thêm, Sửa, Xóa, Xem danh sách công việc.
+ Đánh dấu công việc là hoàn thành/chưa hoàn thành.


Công nghệ Backend: Sử dụng một trong hai công nghệ sau:
- PHP: Có thể sử dụng PHP thuần hoặc một framework PHP (ví dụ: Laravel,
CodeIgniter, Slim Framework - khuyến khích cho dự án đơn giản).
- Cơ sở dữ liệu: Bắt buộc sử dụng MySQL để lưu trữ và quản lý dữ liệu.
- Giao diện người dùng (Frontend):
+ Yêu cầu giao diện đơn giản, không cần quá phức tạp về UI/UX. Mục tiêu chính là
hiển thị và tương tác với dữ liệu từ backend. Tuy nhiên giao diện cũng cần hợp lý,
dễ nhìn.
+ Có thể sử dụng HTML, CSS, JavaScript thuần hoặc một framework/thư viện
frontend nhẹ (ví dụ: jQuery, Vue.js, React.js ở mức độ cơ bản) để tương tác với
API backend.
+ Trọng tâm là Backend, không đánh giá quá nhiều về Frontend phức tạp.
- Chức năng cơ bản: Ứng dụng phải thực hiện được ít nhất các thao tác CRUD (Create,
Read, Update, Delete) dữ liệu từ cơ sở dữ liệu thông qua backend API.
- Quản lý phiên (Session Management): Tối thiểu cần có chức năng đăng nhập/đăng xuất
đơn giản.
