CREATE TABLE NguoiDung (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    TenDangNhap VARCHAR(50) UNIQUE NOT NULL,
    MatKhau VARCHAR (255) NOT NULL,
    HoTen VARCHAR(100),
    Email VARCHAR(100),
    SoDienThoai VARCHAR(15)
);

CREATE TABLE CongViec (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    TieuDe VARCHAR(100) NOT NULL,
    MoTa TEXT,
    NgayHetHan DATE,
    TrangThai BOOLEAN DEFAULT FALSE,
    ID_NguoiDung INT,
    DoUuTien ENUM('thap', 'trung_binh', 'cao') DEFAULT 'trung_binh',
    FOREIGN KEY (ID_NguoiDung) REFERENCES NguoiDung(ID) ON DELETE CASCADE
);