-- Database: todo-list

-- Tạo bảng user
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `passwd` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
);

-- Tạo bảng CongViec
CREATE TABLE IF NOT EXISTS `CongViec` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `TieuDe` varchar(255) NOT NULL,
  `MoTa` text,
  `NgayHetHan` date DEFAULT NULL,
  `TrangThai` tinyint(1) DEFAULT '0',
  `ID_NguoiDung` int(11) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `ID_NguoiDung` (`ID_NguoiDung`),
  CONSTRAINT `CongViec_ibfk_1` FOREIGN KEY (`ID_NguoiDung`) REFERENCES `user` (`id`) ON DELETE CASCADE
);

-- Dữ liệu mẫu cho test
INSERT INTO `user` (`username`, `passwd`) VALUES
('admin', 'admin123'),
('user1', 'password123');

-- Dữ liệu mẫu công việc
INSERT INTO `CongViec` (`TieuDe`, `MoTa`, `NgayHetHan`, `TrangThai`, `ID_NguoiDung`) VALUES
('Hoàn thành báo cáo', 'Viết báo cáo tổng kết tháng', '2025-01-15', 0, 1),
('Họp team', 'Họp weekly meeting với team', '2025-01-12', 1, 1),
('Mua sắm', 'Mua đồ dùng cho văn phòng', '2025-01-10', 0, 1);
