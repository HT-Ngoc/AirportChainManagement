-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 21, 2026 at 08:29 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `quan_ly_chuoi_san_bay`
--

-- --------------------------------------------------------

--
-- Table structure for table `bang_chuyen_hanh_ly`
--

CREATE TABLE `bang_chuyen_hanh_ly` (
  `id` int(11) NOT NULL,
  `id_nha_ga` int(11) NOT NULL,
  `so_bang_chuyen` varchar(20) NOT NULL,
  `trang_thai` enum('Hoat_Dong','Bao_Tri') DEFAULT 'Hoat_Dong'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bang_chuyen_hanh_ly`
--

INSERT INTO `bang_chuyen_hanh_ly` (`id`, `id_nha_ga`, `so_bang_chuyen`, `trang_thai`) VALUES
(1, 1, 'Belt 01', 'Hoat_Dong'),
(2, 3, 'Belt 05', 'Hoat_Dong');

-- --------------------------------------------------------

--
-- Table structure for table `bang_gia_dich_vu`
--

CREATE TABLE `bang_gia_dich_vu` (
  `id` int(11) NOT NULL,
  `loai_dich_vu` enum('Phi_Cat_Ha_Canh','Phi_Do_Tau_Bay','Phi_Phuc_Vu_Hanh_Khach') NOT NULL,
  `don_gia` decimal(15,2) NOT NULL,
  `don_vi_tinh` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bang_gia_dich_vu`
--

INSERT INTO `bang_gia_dich_vu` (`id`, `loai_dich_vu`, `don_gia`, `don_vi_tinh`) VALUES
(1, 'Phi_Cat_Ha_Canh', 1500000.00, 'Lượt'),
(2, 'Phi_Do_Tau_Bay', 50000.00, 'Giờ/Tấn MTOW'),
(3, 'Phi_Phuc_Vu_Hanh_Khach', 100000.00, 'Hành khách');

-- --------------------------------------------------------

--
-- Table structure for table `chuyen_bay_ngay`
--

CREATE TABLE `chuyen_bay_ngay` (
  `id` int(11) NOT NULL,
  `id_lich_mua` int(11) DEFAULT NULL,
  `so_hieu` varchar(20) NOT NULL,
  `id_hang_hang_khong` int(11) NOT NULL,
  `id_loai_tau_bay` int(11) NOT NULL,
  `id_san_bay_di` int(11) NOT NULL,
  `id_san_bay_den` int(11) NOT NULL,
  `id_cua_ra_may_bay` int(11) DEFAULT NULL,
  `id_quay_checkin` int(11) DEFAULT NULL,
  `id_bang_chuyen` int(11) DEFAULT NULL,
  `gio_du_kien` datetime NOT NULL,
  `gio_thuc_te` datetime DEFAULT NULL,
  `luong_hanh_khach` int(11) DEFAULT 0,
  `luong_hang_hoa_tan` decimal(10,2) DEFAULT 0.00,
  `trang_thai_tong` enum('Len_Lich','Dang_Phuc_Vu','Cat_Canh','Ha_Canh','Hoan_Thanh','Huy','Tri_Hoan') DEFAULT 'Len_Lich'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chuyen_bay_ngay`
--

INSERT INTO `chuyen_bay_ngay` (`id`, `id_lich_mua`, `so_hieu`, `id_hang_hang_khong`, `id_loai_tau_bay`, `id_san_bay_di`, `id_san_bay_den`, `id_cua_ra_may_bay`, `id_quay_checkin`, `id_bang_chuyen`, `gio_du_kien`, `gio_thuc_te`, `luong_hanh_khach`, `luong_hang_hoa_tan`, `trang_thai_tong`) VALUES
(1, 1, 'VN210', 1, 2, 2, 1, 3, 3, 1, '2026-10-20 08:00:00', '2026-10-20 08:05:00', 295, 12.50, 'Hoan_Thanh'),
(2, 1, 'VN210', 1, 2, 2, 1, NULL, NULL, NULL, '2026-10-21 08:00:00', NULL, 0, 0.00, 'Len_Lich'),
(3, 2, 'VJ777', 2, 1, 1, 3, 1, 1, 2, '2026-10-20 14:30:00', NULL, 210, 5.00, 'Dang_Phuc_Vu');

-- --------------------------------------------------------

--
-- Table structure for table `cua_ra_may_bay`
--

CREATE TABLE `cua_ra_may_bay` (
  `id` int(11) NOT NULL,
  `id_nha_ga` int(11) NOT NULL,
  `so_cua` varchar(20) NOT NULL,
  `trang_thai` enum('Hoat_Dong','Bao_Tri') DEFAULT 'Hoat_Dong'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cua_ra_may_bay`
--

INSERT INTO `cua_ra_may_bay` (`id`, `id_nha_ga`, `so_cua`, `trang_thai`) VALUES
(1, 1, 'Gate 1', 'Hoat_Dong'),
(2, 1, 'Gate 2', 'Hoat_Dong'),
(3, 3, 'Gate 10', 'Hoat_Dong'),
(4, 3, 'Gate 11', 'Bao_Tri');

-- --------------------------------------------------------

--
-- Table structure for table `don_vi_mat_dat`
--

CREATE TABLE `don_vi_mat_dat` (
  `id` int(11) NOT NULL,
  `ma_don_vi` varchar(10) NOT NULL,
  `ten_don_vi` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `don_vi_mat_dat`
--

INSERT INTO `don_vi_mat_dat` (`id`, `ma_don_vi`, `ten_don_vi`) VALUES
(1, 'VIAGS', 'Công ty Dịch vụ mặt đất sân bay Việt Nam'),
(2, 'SAGS', 'Công ty Phục vụ mặt đất Sài Gòn');

-- --------------------------------------------------------

--
-- Table structure for table `hang_hang_khong`
--

CREATE TABLE `hang_hang_khong` (
  `id` int(11) NOT NULL,
  `ma_iata` varchar(2) NOT NULL,
  `ten_hang` varchar(100) NOT NULL,
  `quoc_gia` varchar(50) DEFAULT 'Viet Nam'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hang_hang_khong`
--

INSERT INTO `hang_hang_khong` (`id`, `ma_iata`, `ten_hang`, `quoc_gia`) VALUES
(1, 'VN', 'Vietnam Airlines', 'Viet Nam'),
(2, 'VJ', 'Vietjet Air', 'Viet Nam'),
(3, 'QH', 'Bamboo Airways', 'Viet Nam');

-- --------------------------------------------------------

--
-- Table structure for table `hoa_don`
--

CREATE TABLE `hoa_don` (
  `id` int(11) NOT NULL,
  `id_chuyen_bay` int(11) NOT NULL,
  `id_hang_hang_khong` int(11) NOT NULL,
  `phi_cat_ha_canh` decimal(15,2) DEFAULT 0.00,
  `phi_do_tau_bay` decimal(15,2) DEFAULT 0.00,
  `phi_phuc_vu_hk` decimal(15,2) DEFAULT 0.00,
  `tong_tien` decimal(15,2) DEFAULT 0.00,
  `ngay_xuat` datetime DEFAULT current_timestamp(),
  `trang_thai_thanh_toan` enum('Chua_Thanh_Toan','Da_Thanh_Toan') DEFAULT 'Chua_Thanh_Toan'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hoa_don`
--

INSERT INTO `hoa_don` (`id`, `id_chuyen_bay`, `id_hang_hang_khong`, `phi_cat_ha_canh`, `phi_do_tau_bay`, `phi_phuc_vu_hk`, `tong_tien`, `ngay_xuat`, `trang_thai_thanh_toan`) VALUES
(1, 1, 1, 1500000.00, 25400000.00, 29500000.00, 56400000.00, '2026-10-18 19:45:46', 'Chua_Thanh_Toan');

-- --------------------------------------------------------

--
-- Table structure for table `lich_bay_mua`
--

CREATE TABLE `lich_bay_mua` (
  `id` int(11) NOT NULL,
  `so_hieu` varchar(20) NOT NULL,
  `id_hang_hang_khong` int(11) NOT NULL,
  `id_san_bay_di` int(11) NOT NULL,
  `id_san_bay_den` int(11) NOT NULL,
  `tan_suat_tuan` varchar(50) NOT NULL,
  `tu_ngay` date NOT NULL,
  `den_ngay` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lich_bay_mua`
--

INSERT INTO `lich_bay_mua` (`id`, `so_hieu`, `id_hang_hang_khong`, `id_san_bay_di`, `id_san_bay_den`, `tan_suat_tuan`, `tu_ngay`, `den_ngay`) VALUES
(1, 'VN210', 1, 2, 1, 'Hàng ngày', '2026-10-01', '2026-12-31'),
(2, 'VJ777', 2, 1, 3, 'Thứ 2, 4, 6', '2026-10-01', '2026-12-31');

-- --------------------------------------------------------

--
-- Table structure for table `loai_tau_bay`
--

CREATE TABLE `loai_tau_bay` (
  `id` int(11) NOT NULL,
  `ma_loai` varchar(50) NOT NULL,
  `tai_trong_cat_canh_mtow` decimal(10,2) NOT NULL,
  `suc_chua_hanh_khach` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loai_tau_bay`
--

INSERT INTO `loai_tau_bay` (`id`, `ma_loai`, `tai_trong_cat_canh_mtow`, `suc_chua_hanh_khach`) VALUES
(1, 'Airbus A321', 89.00, 230),
(2, 'Boeing 787-9', 254.00, 310),
(3, 'Airbus A330', 233.00, 290);

-- --------------------------------------------------------

--
-- Table structure for table `nguoi_dung`
--

CREATE TABLE `nguoi_dung` (
  `id` int(11) NOT NULL,
  `ten_dang_nhap` varchar(50) NOT NULL,
  `mat_khau_hash` varchar(255) NOT NULL,
  `ho_ten` varchar(100) NOT NULL,
  `vai_tro` enum('Admin_Tong_Cong_Ty','Quan_Ly_San_Bay','Nhan_Vien_Dieu_Hanh','Ke_Toan') NOT NULL,
  `id_san_bay` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nguoi_dung`
--

INSERT INTO `nguoi_dung` (`id`, `ten_dang_nhap`, `mat_khau_hash`, `ho_ten`, `vai_tro`, `id_san_bay`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nguyễn Đình Khánh', 'Admin_Tong_Cong_Ty', NULL),
(2, 'dieuhanh_sgn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Trần Bảo Thiên', 'Nhan_Vien_Dieu_Hanh', 2),
(3, 'ketoan_han', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nguyễn Thị Mỹ Khánh', 'Ke_Toan', 1),
(4, 'quanly_cxr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Huỳnh Tuấn Ngọc', 'Quan_Ly_San_Bay', 3);

-- --------------------------------------------------------

--
-- Table structure for table `nhat_ky_trang_thai_bay`
--

CREATE TABLE `nhat_ky_trang_thai_bay` (
  `id` int(11) NOT NULL,
  `id_chuyen_bay` int(11) NOT NULL,
  `id_nguoi_dung` int(11) NOT NULL,
  `trang_thai_cu` varchar(50) DEFAULT NULL,
  `trang_thai_moi` varchar(50) NOT NULL,
  `thoi_gian_cap_nhat` datetime DEFAULT current_timestamp(),
  `ghi_chu` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nhat_ky_trang_thai_bay`
--

INSERT INTO `nhat_ky_trang_thai_bay` (`id`, `id_chuyen_bay`, `id_nguoi_dung`, `trang_thai_cu`, `trang_thai_moi`, `thoi_gian_cap_nhat`, `ghi_chu`) VALUES
(1, 1, 2, 'Dang_Phuc_Vu', 'Cat_Canh', '2026-10-18 19:45:46', 'Cất cánh muộn 5 phút do không lưu'),
(2, 1, 2, 'Cat_Canh', 'Hoan_Thanh', '2026-10-18 19:45:46', 'Hạ cánh an toàn tại HAN');

-- --------------------------------------------------------

--
-- Table structure for table `nha_ga`
--

CREATE TABLE `nha_ga` (
  `id` int(11) NOT NULL,
  `id_san_bay` int(11) NOT NULL,
  `ten_nha_ga` varchar(50) NOT NULL,
  `trang_thai` enum('Hoat_Dong','Bao_Tri') DEFAULT 'Hoat_Dong'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nha_ga`
--

INSERT INTO `nha_ga` (`id`, `id_san_bay`, `ten_nha_ga`, `trang_thai`) VALUES
(1, 1, 'T1 - Quốc nội Nội Bài', 'Hoat_Dong'),
(2, 1, 'T2 - Quốc tế Nội Bài', 'Hoat_Dong'),
(3, 2, 'T1 - Quốc nội Tân Sơn Nhất', 'Hoat_Dong');

-- --------------------------------------------------------

--
-- Table structure for table `quay_checkin`
--

CREATE TABLE `quay_checkin` (
  `id` int(11) NOT NULL,
  `id_nha_ga` int(11) NOT NULL,
  `so_quay` varchar(20) NOT NULL,
  `trang_thai` enum('Hoat_Dong','Bao_Tri') DEFAULT 'Hoat_Dong'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quay_checkin`
--

INSERT INTO `quay_checkin` (`id`, `id_nha_ga`, `so_quay`, `trang_thai`) VALUES
(1, 1, 'A01-A10', 'Hoat_Dong'),
(2, 1, 'B01-B10', 'Hoat_Dong'),
(3, 3, 'C01-C15', 'Hoat_Dong');

-- --------------------------------------------------------

--
-- Table structure for table `san_bay`
--

CREATE TABLE `san_bay` (
  `id` int(11) NOT NULL,
  `ma_iata` varchar(3) NOT NULL,
  `ten_san_bay` varchar(100) NOT NULL,
  `phan_cap` enum('Quoc_Noi','Quoc_Te') NOT NULL,
  `vi_tri` varchar(255) NOT NULL,
  `trang_thai` enum('Hoat_Dong','Bao_Tri','Dong_Cua_Khan_Cap') DEFAULT 'Hoat_Dong'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `san_bay`
--

INSERT INTO `san_bay` (`id`, `ma_iata`, `ten_san_bay`, `phan_cap`, `vi_tri`, `trang_thai`) VALUES
(1, 'HAN', 'Cảng HKQT Nội Bài', 'Quoc_Te', 'Hà Nội', 'Hoat_Dong'),
(2, 'SGN', 'Cảng HKQT Tân Sơn Nhất', 'Quoc_Te', 'TP. Hồ Chí Minh', 'Hoat_Dong'),
(3, 'CXR', 'Cảng HKQT Cam Ranh', 'Quoc_Te', 'Khánh Hòa', 'Hoat_Dong');

-- --------------------------------------------------------

--
-- Table structure for table `thiet_bi_san_bay`
--

CREATE TABLE `thiet_bi_san_bay` (
  `id` int(11) NOT NULL,
  `id_san_bay` int(11) NOT NULL,
  `ten_thiet_bi` varchar(100) NOT NULL,
  `loai_thiet_bi` enum('Den_Tin_Hieu','Thiet_Bi_Dan_Duong','Xe_Thang','Xe_Keo','Khac') NOT NULL,
  `trang_thai` enum('Tot','Bao_Tri','Hong') DEFAULT 'Tot'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `thiet_bi_san_bay`
--

INSERT INTO `thiet_bi_san_bay` (`id`, `id_san_bay`, `ten_thiet_bi`, `loai_thiet_bi`, `trang_thai`) VALUES
(1, 2, 'Xe kéo đẩy máy bay Goldhofer', 'Xe_Keo', 'Tot'),
(2, 1, 'Xe thang hành khách T1', 'Xe_Thang', 'Bao_Tri'),
(3, 3, 'Hệ thống đèn chớp đường băng', 'Den_Tin_Hieu', 'Tot');

-- --------------------------------------------------------

--
-- Table structure for table `yeu_cau_bao_tri`
--

CREATE TABLE `yeu_cau_bao_tri` (
  `id` int(11) NOT NULL,
  `id_thiet_bi` int(11) NOT NULL,
  `mo_ta_su_co` text NOT NULL,
  `hinh_anh_minh_hoa` varchar(255) DEFAULT NULL,
  `muc_do` enum('Thap','Trung_Binh','Khan_Cap') NOT NULL,
  `trang_thai_xu_ly` enum('Moi','Dang_Xu_Ly','Hoan_Thanh') DEFAULT 'Moi',
  `ngay_tao` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `yeu_cau_bao_tri`
--

INSERT INTO `yeu_cau_bao_tri` (`id`, `id_thiet_bi`, `mo_ta_su_co`, `hinh_anh_minh_hoa`, `muc_do`, `trang_thai_xu_ly`, `ngay_tao`) VALUES
(1, 2, 'Bơm thủy lực nâng thang bị rò rỉ dầu', NULL, 'Trung_Binh', 'Dang_Xu_Ly', '2026-10-18 19:45:46'),
(2, 3, 'Cháy 3 bóng đèn LED đoạn giữa đường băng', NULL, 'Khan_Cap', 'Moi', '2026-10-18 19:45:46');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bang_chuyen_hanh_ly`
--
ALTER TABLE `bang_chuyen_hanh_ly`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_nha_ga` (`id_nha_ga`);

--
-- Indexes for table `bang_gia_dich_vu`
--
ALTER TABLE `bang_gia_dich_vu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `chuyen_bay_ngay`
--
ALTER TABLE `chuyen_bay_ngay`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_lich_mua` (`id_lich_mua`),
  ADD KEY `id_hang_hang_khong` (`id_hang_hang_khong`),
  ADD KEY `id_loai_tau_bay` (`id_loai_tau_bay`),
  ADD KEY `id_san_bay_di` (`id_san_bay_di`),
  ADD KEY `id_san_bay_den` (`id_san_bay_den`),
  ADD KEY `id_cua_ra_may_bay` (`id_cua_ra_may_bay`),
  ADD KEY `id_quay_checkin` (`id_quay_checkin`),
  ADD KEY `id_bang_chuyen` (`id_bang_chuyen`);

--
-- Indexes for table `cua_ra_may_bay`
--
ALTER TABLE `cua_ra_may_bay`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_nha_ga` (`id_nha_ga`);

--
-- Indexes for table `don_vi_mat_dat`
--
ALTER TABLE `don_vi_mat_dat`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ma_don_vi` (`ma_don_vi`);

--
-- Indexes for table `hang_hang_khong`
--
ALTER TABLE `hang_hang_khong`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ma_iata` (`ma_iata`);

--
-- Indexes for table `hoa_don`
--
ALTER TABLE `hoa_don`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_chuyen_bay` (`id_chuyen_bay`),
  ADD KEY `id_hang_hang_khong` (`id_hang_hang_khong`);

--
-- Indexes for table `lich_bay_mua`
--
ALTER TABLE `lich_bay_mua`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_hang_hang_khong` (`id_hang_hang_khong`),
  ADD KEY `id_san_bay_di` (`id_san_bay_di`),
  ADD KEY `id_san_bay_den` (`id_san_bay_den`);

--
-- Indexes for table `loai_tau_bay`
--
ALTER TABLE `loai_tau_bay`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ma_loai` (`ma_loai`);

--
-- Indexes for table `nguoi_dung`
--
ALTER TABLE `nguoi_dung`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ten_dang_nhap` (`ten_dang_nhap`),
  ADD KEY `id_san_bay` (`id_san_bay`);

--
-- Indexes for table `nhat_ky_trang_thai_bay`
--
ALTER TABLE `nhat_ky_trang_thai_bay`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_chuyen_bay` (`id_chuyen_bay`),
  ADD KEY `id_nguoi_dung` (`id_nguoi_dung`);

--
-- Indexes for table `nha_ga`
--
ALTER TABLE `nha_ga`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_san_bay` (`id_san_bay`);

--
-- Indexes for table `quay_checkin`
--
ALTER TABLE `quay_checkin`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_nha_ga` (`id_nha_ga`);

--
-- Indexes for table `san_bay`
--
ALTER TABLE `san_bay`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ma_iata` (`ma_iata`);

--
-- Indexes for table `thiet_bi_san_bay`
--
ALTER TABLE `thiet_bi_san_bay`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_san_bay` (`id_san_bay`);

--
-- Indexes for table `yeu_cau_bao_tri`
--
ALTER TABLE `yeu_cau_bao_tri`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_thiet_bi` (`id_thiet_bi`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bang_chuyen_hanh_ly`
--
ALTER TABLE `bang_chuyen_hanh_ly`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `bang_gia_dich_vu`
--
ALTER TABLE `bang_gia_dich_vu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `chuyen_bay_ngay`
--
ALTER TABLE `chuyen_bay_ngay`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `cua_ra_may_bay`
--
ALTER TABLE `cua_ra_may_bay`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `don_vi_mat_dat`
--
ALTER TABLE `don_vi_mat_dat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `hang_hang_khong`
--
ALTER TABLE `hang_hang_khong`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `hoa_don`
--
ALTER TABLE `hoa_don`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `lich_bay_mua`
--
ALTER TABLE `lich_bay_mua`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `loai_tau_bay`
--
ALTER TABLE `loai_tau_bay`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `nguoi_dung`
--
ALTER TABLE `nguoi_dung`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `nhat_ky_trang_thai_bay`
--
ALTER TABLE `nhat_ky_trang_thai_bay`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `nha_ga`
--
ALTER TABLE `nha_ga`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `quay_checkin`
--
ALTER TABLE `quay_checkin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `san_bay`
--
ALTER TABLE `san_bay`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `thiet_bi_san_bay`
--
ALTER TABLE `thiet_bi_san_bay`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `yeu_cau_bao_tri`
--
ALTER TABLE `yeu_cau_bao_tri`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bang_chuyen_hanh_ly`
--
ALTER TABLE `bang_chuyen_hanh_ly`
  ADD CONSTRAINT `bang_chuyen_hanh_ly_ibfk_1` FOREIGN KEY (`id_nha_ga`) REFERENCES `nha_ga` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `chuyen_bay_ngay`
--
ALTER TABLE `chuyen_bay_ngay`
  ADD CONSTRAINT `chuyen_bay_ngay_ibfk_1` FOREIGN KEY (`id_lich_mua`) REFERENCES `lich_bay_mua` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `chuyen_bay_ngay_ibfk_2` FOREIGN KEY (`id_hang_hang_khong`) REFERENCES `hang_hang_khong` (`id`),
  ADD CONSTRAINT `chuyen_bay_ngay_ibfk_3` FOREIGN KEY (`id_loai_tau_bay`) REFERENCES `loai_tau_bay` (`id`),
  ADD CONSTRAINT `chuyen_bay_ngay_ibfk_4` FOREIGN KEY (`id_san_bay_di`) REFERENCES `san_bay` (`id`),
  ADD CONSTRAINT `chuyen_bay_ngay_ibfk_5` FOREIGN KEY (`id_san_bay_den`) REFERENCES `san_bay` (`id`),
  ADD CONSTRAINT `chuyen_bay_ngay_ibfk_6` FOREIGN KEY (`id_cua_ra_may_bay`) REFERENCES `cua_ra_may_bay` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `chuyen_bay_ngay_ibfk_7` FOREIGN KEY (`id_quay_checkin`) REFERENCES `quay_checkin` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `chuyen_bay_ngay_ibfk_8` FOREIGN KEY (`id_bang_chuyen`) REFERENCES `bang_chuyen_hanh_ly` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `cua_ra_may_bay`
--
ALTER TABLE `cua_ra_may_bay`
  ADD CONSTRAINT `cua_ra_may_bay_ibfk_1` FOREIGN KEY (`id_nha_ga`) REFERENCES `nha_ga` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hoa_don`
--
ALTER TABLE `hoa_don`
  ADD CONSTRAINT `hoa_don_ibfk_1` FOREIGN KEY (`id_chuyen_bay`) REFERENCES `chuyen_bay_ngay` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hoa_don_ibfk_2` FOREIGN KEY (`id_hang_hang_khong`) REFERENCES `hang_hang_khong` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lich_bay_mua`
--
ALTER TABLE `lich_bay_mua`
  ADD CONSTRAINT `lich_bay_mua_ibfk_1` FOREIGN KEY (`id_hang_hang_khong`) REFERENCES `hang_hang_khong` (`id`),
  ADD CONSTRAINT `lich_bay_mua_ibfk_2` FOREIGN KEY (`id_san_bay_di`) REFERENCES `san_bay` (`id`),
  ADD CONSTRAINT `lich_bay_mua_ibfk_3` FOREIGN KEY (`id_san_bay_den`) REFERENCES `san_bay` (`id`);

--
-- Constraints for table `nguoi_dung`
--
ALTER TABLE `nguoi_dung`
  ADD CONSTRAINT `nguoi_dung_ibfk_1` FOREIGN KEY (`id_san_bay`) REFERENCES `san_bay` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `nhat_ky_trang_thai_bay`
--
ALTER TABLE `nhat_ky_trang_thai_bay`
  ADD CONSTRAINT `nhat_ky_trang_thai_bay_ibfk_1` FOREIGN KEY (`id_chuyen_bay`) REFERENCES `chuyen_bay_ngay` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `nhat_ky_trang_thai_bay_ibfk_2` FOREIGN KEY (`id_nguoi_dung`) REFERENCES `nguoi_dung` (`id`);

--
-- Constraints for table `nha_ga`
--
ALTER TABLE `nha_ga`
  ADD CONSTRAINT `nha_ga_ibfk_1` FOREIGN KEY (`id_san_bay`) REFERENCES `san_bay` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quay_checkin`
--
ALTER TABLE `quay_checkin`
  ADD CONSTRAINT `quay_checkin_ibfk_1` FOREIGN KEY (`id_nha_ga`) REFERENCES `nha_ga` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `thiet_bi_san_bay`
--
ALTER TABLE `thiet_bi_san_bay`
  ADD CONSTRAINT `thiet_bi_san_bay_ibfk_1` FOREIGN KEY (`id_san_bay`) REFERENCES `san_bay` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `yeu_cau_bao_tri`
--
ALTER TABLE `yeu_cau_bao_tri`
  ADD CONSTRAINT `yeu_cau_bao_tri_ibfk_1` FOREIGN KEY (`id_thiet_bi`) REFERENCES `thiet_bi_san_bay` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
