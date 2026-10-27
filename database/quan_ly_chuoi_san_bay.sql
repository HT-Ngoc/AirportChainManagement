-- ============================================================
-- CƠ SỞ DỮ LIỆU: `quan_ly_chuoi_san_bay`  (FILE TỔNG HỢP CUỐI CÙNG)
-- ------------------------------------------------------------
-- Được gộp từ 2 file:
--   1) database/quan_ly_chuoi_san_bay.sql        (bản gốc)
--   2) database/quan_ly_chuoi_san_baypull1.sql   (bản mở rộng)
--
-- NGUYÊN TẮC GỘP:
--   - Cấu trúc: lấy bản pull1, vì bản này là SUPERSET của bản gốc
--     (giữ đủ 16 bảng, chỉ thêm cột chứ không bỏ cột nào):
--       + bang_chuyen_hanh_ly : thêm khu_vuc
--       + cua_ra_may_bay      : thêm khu_vuc
--       + quay_checkin        : thêm khu_vuc
--       + nha_ga              : thêm ma_nha_ga, loai_nha_ga, nam_khai_thac,
--                                      dien_tich, cong_suat, so_do, mo_ta
--       + san_bay             : thêm ma_icao, ten_ngan, hinh_anh, mo_ta, thu_tu_hien_thi
--       + các bảng trên có thêm UNIQUE KEY chống trùng dữ liệu
--   - Dữ liệu: lấy bản pull1 vì đã bao trùm toàn bộ dữ liệu mẫu của bản gốc
--     (nha_ga 21 dòng, san_bay 15 dòng, cua_ra_may_bay 140 dòng,
--      bang_chuyen_hanh_ly 64 dòng, quay_checkin 42 dòng...).
--   - Bổ sung: CREATE DATABASE IF NOT EXISTS + USE để import được trên máy mới.
--
-- CÁCH IMPORT:
--   Cách 1 - dòng lệnh (khuyến nghị, giữ đúng tiếng Việt):
--     mysql -u root --default-character-set=utf8mb4 < database/quan_ly_chuoi_san_bayfinal.sql
--   Cách 2 - phpMyAdmin: chọn Import -> Chọn file -> Character set: utf-8 -> Go
--
-- LƯU Ý:
--   - File KHÔNG có DROP TABLE nên chỉ import được vào CSDL đang trống.
--     Muốn import lại từ đầu thì xóa CSDL cũ trước:
--       DROP DATABASE `quan_ly_chuoi_san_bay`;
--   - Tài khoản mẫu trong bảng `nguoi_dung` dùng mật khẩu đã hash bcrypt,
--     KHÔNG phải mật khẩu chữ thường.
-- ============================================================

CREATE DATABASE IF NOT EXISTS `quan_ly_chuoi_san_bay`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

USE `quan_ly_chuoi_san_bay`;

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
  `khu_vuc` varchar(50) DEFAULT NULL,
  `trang_thai` enum('Hoat_Dong','Bao_Tri') DEFAULT 'Hoat_Dong'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bang_chuyen_hanh_ly`
--

INSERT INTO `bang_chuyen_hanh_ly` (`id`, `id_nha_ga`, `so_bang_chuyen`, `khu_vuc`, `trang_thai`) VALUES
(1, 1, '01', 'Sảnh đến', 'Hoat_Dong'),
(2, 7, '01', 'Sảnh đến', 'Hoat_Dong'),
(3, 1, '02', 'Sảnh đến', 'Hoat_Dong'),
(4, 1, '03', 'Sảnh đến', 'Hoat_Dong'),
(5, 1, '04', 'Sảnh đến', 'Hoat_Dong'),
(6, 1, '05', 'Sảnh đến', 'Hoat_Dong'),
(7, 1, '06', 'Sảnh đến', 'Bao_Tri'),
(8, 2, '07', 'Sảnh đến', 'Hoat_Dong'),
(9, 2, '08', 'Sảnh đến', 'Hoat_Dong'),
(10, 2, '09', 'Sảnh đến', 'Hoat_Dong'),
(11, 2, '10', 'Sảnh đến', 'Hoat_Dong'),
(12, 2, '11', 'Sảnh đến', 'Hoat_Dong'),
(13, 2, '12', 'Sảnh đến', 'Bao_Tri'),
(14, 3, '01', 'Sảnh đến', 'Hoat_Dong'),
(15, 3, '02', 'Sảnh đến', 'Hoat_Dong'),
(16, 3, '03', 'Sảnh đến', 'Hoat_Dong'),
(17, 3, '04', 'Sảnh đến', 'Hoat_Dong'),
(18, 3, '05', 'Sảnh đến', 'Hoat_Dong'),
(19, 3, '06', 'Sảnh đến', 'Hoat_Dong'),
(20, 4, '07', 'Sảnh đến', 'Hoat_Dong'),
(21, 4, '08', 'Sảnh đến', 'Hoat_Dong'),
(22, 4, '09', 'Sảnh đến', 'Hoat_Dong'),
(23, 4, '10', 'Sảnh đến', 'Hoat_Dong'),
(24, 4, '11', 'Sảnh đến', 'Hoat_Dong'),
(25, 4, '12', 'Sảnh đến', 'Bao_Tri'),
(26, 5, '01', 'Sảnh đến', 'Hoat_Dong'),
(27, 5, '02', 'Sảnh đến', 'Hoat_Dong'),
(28, 5, '03', 'Sảnh đến', 'Hoat_Dong'),
(29, 5, '04', 'Sảnh đến', 'Hoat_Dong'),
(30, 6, '05', 'Sảnh đến', 'Hoat_Dong'),
(31, 6, '06', 'Sảnh đến', 'Hoat_Dong'),
(32, 6, '07', 'Sảnh đến', 'Hoat_Dong'),
(33, 6, '08', 'Sảnh đến', 'Hoat_Dong'),
(34, 7, '02', 'Sảnh đến', 'Hoat_Dong'),
(35, 7, '03', 'Sảnh đến', 'Hoat_Dong'),
(36, 8, '04', 'Sảnh đến', 'Hoat_Dong'),
(37, 8, '05', 'Sảnh đến', 'Hoat_Dong'),
(38, 8, '06', 'Sảnh đến', 'Bao_Tri'),
(39, 9, '01', 'Sảnh đến', 'Hoat_Dong'),
(40, 9, '02', 'Sảnh đến', 'Hoat_Dong'),
(41, 9, '03', 'Sảnh đến', 'Hoat_Dong'),
(42, 9, '04', 'Sảnh đến', 'Hoat_Dong'),
(43, 9, '05', 'Sảnh đến', 'Hoat_Dong'),
(44, 9, '06', 'Sảnh đến', 'Hoat_Dong'),
(45, 11, '01', 'Sảnh đến', 'Hoat_Dong'),
(46, 11, '02', 'Sảnh đến', 'Hoat_Dong'),
(47, 11, '03', 'Sảnh đến', 'Hoat_Dong'),
(48, 11, '04', 'Sảnh đến', 'Hoat_Dong'),
(49, 12, '01', 'Sảnh đến', 'Hoat_Dong'),
(50, 12, '02', 'Sảnh đến', 'Hoat_Dong'),
(51, 12, '03', 'Sảnh đến', 'Bao_Tri'),
(52, 13, '01', 'Sảnh đến', 'Hoat_Dong'),
(53, 13, '02', 'Sảnh đến', 'Hoat_Dong'),
(54, 14, '01', 'Sảnh đến', 'Hoat_Dong'),
(55, 14, '02', 'Sảnh đến', 'Hoat_Dong'),
(56, 15, '01', 'Sảnh đến', 'Hoat_Dong'),
(57, 16, '01', 'Sảnh đến', 'Hoat_Dong'),
(58, 16, '02', 'Sảnh đến', 'Hoat_Dong'),
(59, 17, '01', 'Sảnh đến', 'Hoat_Dong'),
(60, 18, '01', 'Sảnh đến', 'Hoat_Dong'),
(61, 18, '02', 'Sảnh đến', 'Bao_Tri'),
(62, 19, '01', 'Sảnh đến', 'Hoat_Dong'),
(63, 19, '02', 'Sảnh đến', 'Hoat_Dong'),
(64, 20, '01', 'Sảnh đến', 'Hoat_Dong');

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
  `khu_vuc` varchar(50) DEFAULT NULL,
  `trang_thai` enum('Hoat_Dong','Bao_Tri') DEFAULT 'Hoat_Dong'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cua_ra_may_bay`
--

INSERT INTO `cua_ra_may_bay` (`id`, `id_nha_ga`, `so_cua`, `khu_vuc`, `trang_thai`) VALUES
(1, 1, '1', 'Tầng 2 - Khu A', 'Hoat_Dong'),
(2, 1, '2', 'Tầng 2 - Khu A', 'Hoat_Dong'),
(3, 3, '10', 'Tầng 2 - Khu B', 'Hoat_Dong'),
(4, 3, '11', 'Tầng 2 - Khu B', 'Bao_Tri'),
(5, 1, '3', 'Tầng 2 - Khu A', 'Hoat_Dong'),
(6, 1, '4', 'Tầng 2 - Khu A', 'Hoat_Dong'),
(7, 1, '5', 'Tầng 2 - Khu A', 'Hoat_Dong'),
(8, 1, '6', 'Tầng 2 - Khu A', 'Hoat_Dong'),
(9, 1, '7', 'Tầng 2 - Khu A', 'Hoat_Dong'),
(10, 1, '8', 'Tầng 2 - Khu A', 'Hoat_Dong'),
(11, 1, '9', 'Tầng 2 - Khu B', 'Hoat_Dong'),
(12, 1, '10', 'Tầng 2 - Khu B', 'Hoat_Dong'),
(13, 1, '11', 'Tầng 2 - Khu B', 'Hoat_Dong'),
(14, 1, '12', 'Tầng 2 - Khu B', 'Hoat_Dong'),
(15, 1, '13', 'Tầng 2 - Khu B', 'Hoat_Dong'),
(16, 1, '14', 'Tầng 2 - Khu B', 'Bao_Tri'),
(17, 2, '21', 'Tầng 2 - Khu quốc tế', 'Hoat_Dong'),
(18, 2, '22', 'Tầng 2 - Khu quốc tế', 'Hoat_Dong'),
(19, 2, '23', 'Tầng 2 - Khu quốc tế', 'Hoat_Dong'),
(20, 2, '24', 'Tầng 2 - Khu quốc tế', 'Hoat_Dong'),
(21, 2, '25', 'Tầng 2 - Khu quốc tế', 'Hoat_Dong'),
(22, 2, '26', 'Tầng 2 - Khu quốc tế', 'Hoat_Dong'),
(23, 2, '27', 'Tầng 2 - Khu quốc tế', 'Hoat_Dong'),
(24, 2, '28', 'Tầng 2 - Khu quốc tế', 'Hoat_Dong'),
(25, 2, '29', 'Tầng 2 - Khu quốc tế', 'Bao_Tri'),
(26, 2, '30', 'Tầng 2 - Khu quốc tế', 'Hoat_Dong'),
(27, 3, '1', 'Tầng 2 - Khu A', 'Hoat_Dong'),
(28, 3, '2', 'Tầng 2 - Khu A', 'Hoat_Dong'),
(29, 3, '3', 'Tầng 2 - Khu A', 'Hoat_Dong'),
(30, 3, '4', 'Tầng 2 - Khu A', 'Hoat_Dong'),
(31, 3, '5', 'Tầng 2 - Khu A', 'Hoat_Dong'),
(32, 3, '6', 'Tầng 2 - Khu A', 'Hoat_Dong'),
(33, 3, '7', 'Tầng 2 - Khu A', 'Hoat_Dong'),
(34, 3, '8', 'Tầng 2 - Khu A', 'Hoat_Dong'),
(35, 3, '9', 'Tầng 2 - Khu B', 'Hoat_Dong'),
(36, 3, '12', 'Tầng 2 - Khu B', 'Hoat_Dong'),
(37, 3, '13', 'Tầng 2 - Khu B', 'Hoat_Dong'),
(38, 3, '14', 'Tầng 2 - Khu B', 'Hoat_Dong'),
(39, 3, '15', 'Tầng 2 - Khu B', 'Hoat_Dong'),
(40, 3, '16', 'Tầng 2 - Khu B', 'Hoat_Dong'),
(41, 4, '13', 'Tầng 2 - Khu quốc tế', 'Hoat_Dong'),
(42, 4, '14', 'Tầng 2 - Khu quốc tế', 'Hoat_Dong'),
(43, 4, '15', 'Tầng 2 - Khu quốc tế', 'Hoat_Dong'),
(44, 4, '16', 'Tầng 2 - Khu quốc tế', 'Hoat_Dong'),
(45, 4, '17', 'Tầng 2 - Khu quốc tế', 'Hoat_Dong'),
(46, 4, '18', 'Tầng 2 - Khu quốc tế', 'Hoat_Dong'),
(47, 4, '19', 'Tầng 2 - Khu quốc tế', 'Hoat_Dong'),
(48, 4, '20', 'Tầng 2 - Khu quốc tế', 'Hoat_Dong'),
(49, 4, '21', 'Tầng 2 - Khu quốc tế', 'Hoat_Dong'),
(50, 4, '22', 'Tầng 2 - Khu quốc tế', 'Hoat_Dong'),
(51, 4, '23', 'Tầng 2 - Khu quốc tế', 'Hoat_Dong'),
(52, 4, '24', 'Tầng 2 - Khu quốc tế', 'Bao_Tri'),
(53, 5, '1', 'Khu quốc nội', 'Hoat_Dong'),
(54, 5, '2', 'Khu quốc nội', 'Hoat_Dong'),
(55, 5, '3', 'Khu quốc nội', 'Hoat_Dong'),
(56, 5, '4', 'Khu quốc nội', 'Hoat_Dong'),
(57, 5, '5', 'Khu quốc nội', 'Hoat_Dong'),
(58, 5, '6', 'Khu quốc nội', 'Hoat_Dong'),
(59, 5, '7', 'Khu quốc nội', 'Hoat_Dong'),
(60, 5, '8', 'Khu quốc nội', 'Hoat_Dong'),
(61, 6, '9', 'Khu quốc tế', 'Hoat_Dong'),
(62, 6, '10', 'Khu quốc tế', 'Hoat_Dong'),
(63, 6, '11', 'Khu quốc tế', 'Hoat_Dong'),
(64, 6, '12', 'Khu quốc tế', 'Hoat_Dong'),
(65, 6, '13', 'Khu quốc tế', 'Hoat_Dong'),
(66, 6, '14', 'Khu quốc tế', 'Hoat_Dong'),
(67, 6, '15', 'Khu quốc tế', 'Hoat_Dong'),
(68, 6, '16', 'Khu quốc tế', 'Bao_Tri'),
(69, 7, '1', 'Khu quốc nội', 'Hoat_Dong'),
(70, 7, '2', 'Khu quốc nội', 'Hoat_Dong'),
(71, 7, '3', 'Khu quốc nội', 'Hoat_Dong'),
(72, 7, '4', 'Khu quốc nội', 'Hoat_Dong'),
(73, 7, '5', 'Khu quốc nội', 'Hoat_Dong'),
(74, 7, '6', 'Khu quốc nội', 'Hoat_Dong'),
(75, 8, '7', 'Khu quốc tế', 'Hoat_Dong'),
(76, 8, '8', 'Khu quốc tế', 'Hoat_Dong'),
(77, 8, '9', 'Khu quốc tế', 'Hoat_Dong'),
(78, 8, '10', 'Khu quốc tế', 'Hoat_Dong'),
(79, 8, '11', 'Khu quốc tế', 'Hoat_Dong'),
(80, 8, '12', 'Khu quốc tế', 'Hoat_Dong'),
(81, 8, '13', 'Khu quốc tế', 'Hoat_Dong'),
(82, 8, '14', 'Khu quốc tế', 'Hoat_Dong'),
(83, 9, '1', 'Khu quốc nội', 'Hoat_Dong'),
(84, 9, '2', 'Khu quốc nội', 'Hoat_Dong'),
(85, 9, '3', 'Khu quốc nội', 'Hoat_Dong'),
(86, 9, '4', 'Khu quốc nội', 'Hoat_Dong'),
(87, 9, '5', 'Khu quốc nội', 'Hoat_Dong'),
(88, 9, '6', 'Khu quốc nội', 'Hoat_Dong'),
(89, 9, '7', 'Khu quốc tế', 'Hoat_Dong'),
(90, 9, '8', 'Khu quốc tế', 'Hoat_Dong'),
(91, 9, '9', 'Khu quốc tế', 'Hoat_Dong'),
(92, 9, '10', 'Khu quốc tế', 'Hoat_Dong'),
(93, 9, '11', 'Khu quốc tế', 'Hoat_Dong'),
(94, 9, '12', 'Khu quốc tế', 'Hoat_Dong'),
(95, 11, '1', 'Khu khởi hành', 'Hoat_Dong'),
(96, 11, '2', 'Khu khởi hành', 'Hoat_Dong'),
(97, 11, '3', 'Khu khởi hành', 'Hoat_Dong'),
(98, 11, '4', 'Khu khởi hành', 'Hoat_Dong'),
(99, 11, '5', 'Khu khởi hành', 'Hoat_Dong'),
(100, 11, '6', 'Khu khởi hành', 'Hoat_Dong'),
(101, 11, '7', 'Khu khởi hành', 'Hoat_Dong'),
(102, 11, '8', 'Khu khởi hành', 'Bao_Tri'),
(103, 12, '1', 'Khu khởi hành', 'Hoat_Dong'),
(104, 12, '2', 'Khu khởi hành', 'Hoat_Dong'),
(105, 12, '3', 'Khu khởi hành', 'Hoat_Dong'),
(106, 12, '4', 'Khu khởi hành', 'Hoat_Dong'),
(107, 12, '5', 'Khu khởi hành', 'Hoat_Dong'),
(108, 12, '6', 'Khu khởi hành', 'Hoat_Dong'),
(109, 13, '1', 'Khu khởi hành', 'Hoat_Dong'),
(110, 13, '2', 'Khu khởi hành', 'Hoat_Dong'),
(111, 13, '3', 'Khu khởi hành', 'Hoat_Dong'),
(112, 13, '4', 'Khu khởi hành', 'Hoat_Dong'),
(113, 14, '1', 'Khu khởi hành', 'Hoat_Dong'),
(114, 14, '2', 'Khu khởi hành', 'Hoat_Dong'),
(115, 14, '3', 'Khu khởi hành', 'Hoat_Dong'),
(116, 14, '4', 'Khu khởi hành', 'Hoat_Dong'),
(117, 14, '5', 'Khu khởi hành', 'Hoat_Dong'),
(118, 15, '1', 'Khu khởi hành', 'Hoat_Dong'),
(119, 15, '2', 'Khu khởi hành', 'Hoat_Dong'),
(120, 15, '3', 'Khu khởi hành', 'Hoat_Dong'),
(121, 16, '1', 'Khu khởi hành', 'Hoat_Dong'),
(122, 16, '2', 'Khu khởi hành', 'Hoat_Dong'),
(123, 16, '3', 'Khu khởi hành', 'Hoat_Dong'),
(124, 16, '4', 'Khu khởi hành', 'Hoat_Dong'),
(125, 17, '1', 'Khu khởi hành', 'Hoat_Dong'),
(126, 17, '2', 'Khu khởi hành', 'Hoat_Dong'),
(127, 17, '3', 'Khu khởi hành', 'Hoat_Dong'),
(128, 17, '4', 'Khu khởi hành', 'Hoat_Dong'),
(129, 18, '1', 'Khu khởi hành', 'Hoat_Dong'),
(130, 18, '2', 'Khu khởi hành', 'Hoat_Dong'),
(131, 18, '3', 'Khu khởi hành', 'Hoat_Dong'),
(132, 18, '4', 'Khu khởi hành', 'Hoat_Dong'),
(133, 19, '1', 'Khu khởi hành', 'Hoat_Dong'),
(134, 19, '2', 'Khu khởi hành', 'Hoat_Dong'),
(135, 19, '3', 'Khu khởi hành', 'Hoat_Dong'),
(136, 19, '4', 'Khu khởi hành', 'Hoat_Dong'),
(137, 19, '5', 'Khu khởi hành', 'Bao_Tri'),
(138, 20, '1', 'Khu khởi hành', 'Hoat_Dong'),
(139, 20, '2', 'Khu khởi hành', 'Hoat_Dong'),
(140, 20, '3', 'Khu khởi hành', 'Bao_Tri');

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
  `ma_nha_ga` varchar(10) NOT NULL,
  `ten_nha_ga` varchar(100) NOT NULL,
  `loai_nha_ga` enum('Quoc_Noi','Quoc_Te','Hon_Hop') NOT NULL DEFAULT 'Hon_Hop',
  `nam_khai_thac` smallint(6) DEFAULT NULL,
  `dien_tich` varchar(50) DEFAULT NULL,
  `cong_suat` varchar(50) DEFAULT NULL,
  `so_do` varchar(255) DEFAULT NULL,
  `mo_ta` varchar(500) DEFAULT NULL,
  `trang_thai` enum('Hoat_Dong','Bao_Tri') DEFAULT 'Hoat_Dong'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nha_ga`
--

INSERT INTO `nha_ga` (`id`, `id_san_bay`, `ma_nha_ga`, `ten_nha_ga`, `loai_nha_ga`, `nam_khai_thac`, `dien_tich`, `cong_suat`, `so_do`, `mo_ta`, `trang_thai`) VALUES
(1, 1, 'T1', 'Nhà ga T1 - Quốc nội', 'Quoc_Noi', 2001, '90.000 m²', '15 triệu khách/năm', NULL, 'Nhà ga quốc nội phục vụ các chuyến bay nội địa, được cải tạo và mở rộng năm 2015.', 'Hoat_Dong'),
(2, 1, 'T2', 'Nhà ga T2 - Quốc tế', 'Quoc_Te', 2015, '150.000 m²', '10 triệu khách/năm', NULL, 'Nhà ga quốc tế hiện đại, có thể mở rộng công suất lên 15 triệu khách/năm.', 'Hoat_Dong'),
(3, 2, 'T1', 'Nhà ga T1 - Quốc nội', 'Quoc_Noi', 1980, '40.000 m²', '13 triệu khách/năm', NULL, 'Nhà ga quốc nội của sân bay lớn nhất Việt Nam.', 'Hoat_Dong'),
(4, 2, 'T2', 'Nhà ga T2 - Quốc tế', 'Quoc_Te', 2007, '100.000 m²', '12 triệu khách/năm', NULL, 'Nhà ga quốc tế phục vụ các chuyến bay đến và đi quốc tế.', 'Hoat_Dong'),
(5, 4, 'T1', 'Nhà ga T1 - Quốc nội', 'Quoc_Noi', 2011, '36.000 m²', '6 triệu khách/năm', NULL, 'Nhà ga quốc nội của sân bay Đà Nẵng.', 'Hoat_Dong'),
(6, 4, 'T2', 'Nhà ga T2 - Quốc tế', 'Quoc_Te', 2017, '48.000 m²', '4 triệu khách/năm', NULL, 'Nhà ga quốc tế được thiết kế theo kiến trúc đặc trưng của miền Trung.', 'Hoat_Dong'),
(7, 3, 'T1', 'Nhà ga T1 - Quốc nội', 'Quoc_Noi', 2009, '13.000 m²', '2,5 triệu khách/năm', NULL, 'Nhà ga quốc nội phục vụ Khánh Hòa và Nam Trung Bộ.', 'Hoat_Dong'),
(8, 3, 'T2', 'Nhà ga T2 - Quốc tế', 'Quoc_Te', 2018, '50.000 m²', '6 triệu khách/năm', NULL, 'Nhà ga quốc tế Cam Ranh có công suất lớn, phục vụ khách quốc tế đến Khánh Hòa.', 'Hoat_Dong'),
(9, 5, 'T1', 'Nhà ga hành khách Cát Bi', 'Hon_Hop', 2016, NULL, '2 triệu khách/năm', NULL, 'Nhà ga hành khách phục vụ cả chuyến bay nội địa và quốc tế tại Cát Bi.', 'Hoat_Dong'),
(11, 6, 'T1', 'Nhà ga hành khách Phú Quốc', 'Hon_Hop', 2012, '24.000 m²', '4 triệu khách/năm', NULL, 'Nhà ga hành khách phục vụ cả chuyến bay nội địa và quốc tế tại Phú Quốc.', 'Hoat_Dong'),
(12, 7, 'T1', 'Nhà ga hành khách Cần Thơ', 'Hon_Hop', 2011, '20.000 m²', '3 triệu khách/năm', NULL, 'Nhà ga hành khách trung tâm vùng Đồng bằng sông Cửu Long.', 'Hoat_Dong'),
(13, 8, 'T1', 'Nhà ga hành khách Điện Biên', 'Quoc_Noi', 2024, NULL, '500.000 khách/năm', NULL, 'Nhà ga mới được đưa vào khai thác năm 2024, phục vụ tỉnh Điện Biên.', 'Hoat_Dong'),
(14, 9, 'T1', 'Nhà ga hành khách Thọ Xuân', 'Quoc_Noi', 2013, NULL, '1,2 triệu khách/năm', NULL, 'Nhà ga hành khách phục vụ Thanh Hóa và khu vực Bắc Trung Bộ.', 'Hoat_Dong'),
(15, 10, 'T1', 'Nhà ga hành khách Đồng Hới', 'Quoc_Noi', 2008, NULL, '500.000 khách/năm', NULL, 'Nhà ga hành khách phục vụ Quảng Bình và vùng Phong Nha - Kẻ Bàng.', 'Hoat_Dong'),
(16, 11, 'T1', 'Nhà ga hành khách Tuy Hòa', 'Quoc_Noi', 2013, NULL, '1 triệu khách/năm', NULL, 'Nhà ga hành khách phục vụ Phú Yên và khu vực Nam Trung Bộ.', 'Hoat_Dong'),
(17, 12, 'T1', 'Nhà ga hành khách Chu Lai', 'Quoc_Noi', 2015, NULL, '1 triệu khách/năm', NULL, 'Nhà ga hành khách phục vụ Quảng Nam và vùng kinh tế trọng điểm miền Trung.', 'Hoat_Dong'),
(18, 13, 'T1', 'Nhà ga hành khách Pleiku', 'Quoc_Noi', 2018, NULL, '1,5 triệu khách/năm', NULL, 'Nhà ga hành khách phục vụ Gia Lai và khu vực Tây Nguyên.', 'Hoat_Dong'),
(19, 14, 'T1', 'Nhà ga hành khách Phù Cát', 'Quoc_Noi', 2018, NULL, '1,5 triệu khách/năm', NULL, 'Nhà ga hành khách phục vụ Bình Định và vùng duyên hải Nam Trung Bộ.', 'Hoat_Dong'),
(20, 15, 'T1', 'Nhà ga hành khách Rạch Giá', 'Quoc_Noi', 2013, NULL, '500.000 khách/năm', NULL, 'Nhà ga hành khách phục vụ Kiên Giang và khu vực Tây Nam Bộ.', 'Hoat_Dong');

INSERT INTO `nha_ga` (`id`, `id_san_bay`, `ma_nha_ga`, `ten_nha_ga`, `loai_nha_ga`, `nam_khai_thac`, `dien_tich`, `cong_suat`, `so_do`, `mo_ta`, `trang_thai`) VALUES
(21, 4, 'T3', 'Nhà ga T3 - Quốc nội', 'Quoc_Noi', NULL, NULL, NULL, NULL, 'Nhà ga T3 phục vụ các chuyến bay nội địa tại Cảng HKQT Đà Nẵng.', 'Hoat_Dong'),
(22, 2, 'T3', 'Nhà ga T3 - Quốc nội', 'Quoc_Noi', 2025, '112.500 m²', '20 triệu khách/năm', NULL, 'Nhà ga quốc nội mới, khai thác từ tháng 4/2025, giảm tải cho nhà ga T1.', 'Hoat_Dong');

-- --------------------------------------------------------

--
-- Table structure for table `quay_checkin`
--

CREATE TABLE `quay_checkin` (
  `id` int(11) NOT NULL,
  `id_nha_ga` int(11) NOT NULL,
  `so_quay` varchar(20) NOT NULL,
  `khu_vuc` varchar(50) DEFAULT NULL,
  `trang_thai` enum('Hoat_Dong','Bao_Tri') DEFAULT 'Hoat_Dong'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quay_checkin`
--

INSERT INTO `quay_checkin` (`id`, `id_nha_ga`, `so_quay`, `khu_vuc`, `trang_thai`) VALUES
(1, 1, 'A01-A12', 'Sảnh A', 'Hoat_Dong'),
(2, 1, 'B01-B12', 'Sảnh B', 'Hoat_Dong'),
(3, 3, 'C01-C15', 'Sảnh C', 'Hoat_Dong'),
(4, 1, 'C01-C10', 'Sảnh C', 'Hoat_Dong'),
(5, 2, 'D01-D12', 'Sảnh D', 'Hoat_Dong'),
(6, 2, 'E01-E12', 'Sảnh E', 'Hoat_Dong'),
(7, 2, 'F01-F10', 'Sảnh F', 'Bao_Tri'),
(8, 3, 'A01-A12', 'Sảnh A', 'Hoat_Dong'),
(9, 3, 'B01-B12', 'Sảnh B', 'Hoat_Dong'),
(10, 4, 'D01-D12', 'Sảnh D', 'Hoat_Dong'),
(11, 4, 'E01-E12', 'Sảnh E', 'Hoat_Dong'),
(12, 4, 'F01-F12', 'Sảnh F', 'Hoat_Dong'),
(13, 5, 'A01-A10', 'Sảnh A', 'Hoat_Dong'),
(14, 5, 'B01-B10', 'Sảnh B', 'Hoat_Dong'),
(15, 6, 'C01-C12', 'Sảnh C', 'Hoat_Dong'),
(16, 6, 'D01-D12', 'Sảnh D', 'Hoat_Dong'),
(17, 7, 'A01-A08', 'Sảnh A', 'Hoat_Dong'),
(18, 7, 'B01-B08', 'Sảnh B', 'Hoat_Dong'),
(19, 8, 'C01-C12', 'Sảnh C', 'Hoat_Dong'),
(20, 8, 'D01-D12', 'Sảnh D', 'Hoat_Dong'),
(21, 8, 'E01-E08', 'Sảnh E', 'Bao_Tri'),
(22, 9, 'A01-A08', 'Sảnh A', 'Hoat_Dong'),
(23, 9, 'B01-B08', 'Sảnh B', 'Hoat_Dong'),
(24, 9, 'C01-C10', 'Sảnh C', 'Hoat_Dong'),
(25, 9, 'D01-D10', 'Sảnh D', 'Hoat_Dong'),
(26, 11, 'A01-A10', 'Sảnh A', 'Hoat_Dong'),
(27, 11, 'B01-B10', 'Sảnh B', 'Hoat_Dong'),
(28, 11, 'C01-C06', 'Sảnh C', 'Hoat_Dong'),
(29, 12, 'A01-A08', 'Sảnh A', 'Hoat_Dong'),
(30, 12, 'B01-B08', 'Sảnh B', 'Hoat_Dong'),
(31, 13, 'A01-A06', 'Sảnh A', 'Hoat_Dong'),
(32, 13, 'B01-B06', 'Sảnh B', 'Bao_Tri'),
(33, 14, 'A01-A08', 'Sảnh A', 'Hoat_Dong'),
(34, 14, 'B01-B08', 'Sảnh B', 'Hoat_Dong'),
(35, 15, 'A01-A06', 'Sảnh A', 'Hoat_Dong'),
(36, 16, 'A01-A08', 'Sảnh A', 'Hoat_Dong'),
(37, 16, 'B01-B08', 'Sảnh B', 'Hoat_Dong'),
(38, 17, 'A01-A06', 'Sảnh A', 'Hoat_Dong'),
(39, 18, 'A01-A08', 'Sảnh A', 'Hoat_Dong'),
(40, 19, 'A01-A08', 'Sảnh A', 'Hoat_Dong'),
(41, 19, 'B01-B08', 'Sảnh B', 'Hoat_Dong'),
(42, 20, 'A01-A06', 'Sảnh A', 'Hoat_Dong');

-- --------------------------------------------------------

--
-- Table structure for table `san_bay`
--

CREATE TABLE `san_bay` (
  `id` int(11) NOT NULL,
  `ma_iata` varchar(3) NOT NULL,
  `ma_icao` varchar(4) DEFAULT NULL,
  `ten_san_bay` varchar(120) NOT NULL,
  `ten_ngan` varchar(50) NOT NULL,
  `phan_cap` enum('Quoc_Noi','Quoc_Te') NOT NULL,
  `vi_tri` varchar(255) NOT NULL,
  `hinh_anh` varchar(255) DEFAULT NULL,
  `mo_ta` varchar(500) DEFAULT NULL,
  `thu_tu_hien_thi` int(11) NOT NULL DEFAULT 0,
  `trang_thai` enum('Hoat_Dong','Bao_Tri','Dong_Cua_Khan_Cap') DEFAULT 'Hoat_Dong'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `san_bay`
--

INSERT INTO `san_bay` (`id`, `ma_iata`, `ma_icao`, `ten_san_bay`, `ten_ngan`, `phan_cap`, `vi_tri`, `hinh_anh`, `mo_ta`, `thu_tu_hien_thi`, `trang_thai`) VALUES
(1, 'HAN', 'VVNB', 'Cảng hàng không quốc tế Nội Bài', 'Nội Bài', 'Quoc_Te', 'Huyện Sóc Sơn, TP. Hà Nội', 'assets/img/san_bay/HAN.jpg', 'Cửa ngõ hàng không quốc tế của Thủ đô, khai thác hai nhà ga T1 (quốc nội) và T2 (quốc tế).', 1, 'Hoat_Dong'),
(2, 'SGN', 'VVTS', 'Cảng hàng không quốc tế Tân Sơn Nhất', 'Tân Sơn Nhất', 'Quoc_Te', 'Quận Tân Bình, TP. Hồ Chí Minh', 'assets/img/san_bay/SGN.jpg', 'Sân bay lớn nhất Việt Nam về lưu lượng hành khách, gồm T1 (quốc nội) và T2 (quốc tế).', 2, 'Hoat_Dong'),
(3, 'CXR', 'VVCR', 'Cảng hàng không quốc tế Cam Ranh', 'Cam Ranh', 'Quoc_Te', 'TP. Cam Ranh, tỉnh Khánh Hòa', 'assets/img/san_bay/CXR.jpg', 'Sân bay phục vụ Khánh Hòa và khu vực Nam Trung Bộ, có T1 quốc nội và T2 quốc tế.', 4, 'Hoat_Dong'),
(4, 'DAD', 'VVDN', 'Cảng hàng không quốc tế Đà Nẵng', 'Đà Nẵng', 'Quoc_Te', 'Quận Hải Châu, TP. Đà Nẵng', 'assets/img/san_bay/DAD.jpg', 'Sân bay trung tâm miền Trung, khai thác T1 quốc nội và T2 quốc tế.', 3, 'Hoat_Dong'),
(5, 'HPH', 'VVCI', 'Cảng hàng không quốc tế Cát Bi', 'Cát Bi', 'Quoc_Te', 'Quận Hải An, TP. Hải Phòng', 'assets/img/san_bay/HPH.jpg', 'Cửa ngõ hàng không của thành phố cảng và vùng duyên hải Bắc Bộ.', 5, 'Hoat_Dong'),
(6, 'PQC', 'VVPQ', 'Cảng hàng không quốc tế Phú Quốc', 'Phú Quốc', 'Quoc_Te', 'TP. Phú Quốc, tỉnh Kiên Giang', 'assets/img/san_bay/PQC.jpg', 'Sân bay phục vụ đảo ngọc Phú Quốc với nhà ga hành khách hiện đại.', 6, 'Hoat_Dong'),
(7, 'VCA', 'VVCT', 'Cảng hàng không quốc tế Cần Thơ', 'Cần Thơ', 'Quoc_Te', 'Quận Bình Thủy, TP. Cần Thơ', 'assets/img/san_bay/VCA.jpg', 'Sân bay trung tâm vùng Đồng bằng sông Cửu Long.', 7, 'Hoat_Dong'),
(8, 'DIN', 'VVDB', 'Cảng hàng không Điện Biên Phủ', 'Điện Biên', 'Quoc_Noi', 'TP. Điện Biên Phủ, tỉnh Điện Biên', 'assets/img/san_bay/DIN.jpg', 'Sân bay phục vụ tỉnh Điện Biên, nhà ga mới đưa vào khai thác năm 2024.', 1, 'Hoat_Dong'),
(9, 'THD', 'VVTX', 'Cảng hàng không Thọ Xuân', 'Thọ Xuân', 'Quoc_Noi', 'Huyện Thọ Xuân, tỉnh Thanh Hóa', 'assets/img/san_bay/THD.jpg', 'Sân bay phục vụ Thanh Hóa và khu vực Bắc Trung Bộ.', 2, 'Hoat_Dong'),
(10, 'VDH', 'VVDH', 'Cảng hàng không Đồng Hới', 'Đồng Hới', 'Quoc_Noi', 'TP. Đồng Hới, tỉnh Quảng Bình', 'assets/img/san_bay/VDH.jpg', 'Sân bay phục vụ Quảng Bình, gần Di sản Phong Nha - Kẻ Bàng.', 3, 'Hoat_Dong'),
(11, 'TBB', 'VVTH', 'Cảng hàng không Tuy Hòa', 'Tuy Hòa', 'Quoc_Noi', 'TP. Tuy Hòa, tỉnh Phú Yên', 'assets/img/san_bay/TBB.jpg', 'Sân bay phục vụ Phú Yên và khu vực Nam Trung Bộ.', 4, 'Hoat_Dong'),
(12, 'VCL', 'VVCA', 'Cảng hàng không Chu Lai', 'Chu Lai', 'Quoc_Noi', 'Huyện Núi Thành, tỉnh Quảng Nam', 'assets/img/san_bay/VCL.jpg', 'Sân bay phục vụ Quảng Nam và vùng kinh tế trọng điểm miền Trung.', 5, 'Hoat_Dong'),
(13, 'PXU', 'VVPK', 'Cảng hàng không Pleiku', 'Pleiku', 'Quoc_Noi', 'TP. Pleiku, tỉnh Gia Lai', 'assets/img/san_bay/PXU.jpg', 'Sân bay phục vụ Gia Lai và khu vực Tây Nguyên.', 6, 'Hoat_Dong'),
(14, 'UIH', 'VVPC', 'Cảng hàng không Phù Cát', 'Phù Cát', 'Quoc_Noi', 'Huyện Phù Cát, tỉnh Bình Định', 'assets/img/san_bay/UIH.jpg', 'Sân bay phục vụ Bình Định và vùng duyên hải Nam Trung Bộ.', 7, 'Hoat_Dong'),
(15, 'VKG', 'VVRG', 'Cảng hàng không Rạch Giá', 'Rạch Giá', 'Quoc_Noi', 'TP. Rạch Giá, tỉnh Kiên Giang', 'assets/img/san_bay/VKG.jpg', 'Sân bay phục vụ Kiên Giang và khu vực Tây Nam Bộ.', 8, 'Hoat_Dong');

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
  ADD UNIQUE KEY `uq_bang_chuyen_nha_ga` (`id_nha_ga`,`so_bang_chuyen`),
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
  ADD UNIQUE KEY `uq_cua_ra_nha_ga` (`id_nha_ga`,`so_cua`),
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
  ADD UNIQUE KEY `uq_nha_ga_san_bay_ma` (`id_san_bay`,`ma_nha_ga`),
  ADD KEY `id_san_bay` (`id_san_bay`);

--
-- Indexes for table `quay_checkin`
--
ALTER TABLE `quay_checkin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_quay_checkin_nha_ga` (`id_nha_ga`,`so_quay`),
  ADD KEY `id_nha_ga` (`id_nha_ga`);

--
-- Indexes for table `san_bay`
--
ALTER TABLE `san_bay`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ma_iata` (`ma_iata`),
  ADD UNIQUE KEY `ma_icao` (`ma_icao`),
  ADD KEY `thu_tu_hien_thi` (`thu_tu_hien_thi`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=141;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `quay_checkin`
--
ALTER TABLE `quay_checkin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `san_bay`
--
ALTER TABLE `san_bay`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

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
-- Cap nhat anh so do nha ga HAN T1/T2
UPDATE `nha_ga` SET `so_do` = 'assets/img/so_do_nha_ga/HAN-T1-01.jpg' WHERE `id` = 1;
UPDATE `nha_ga` SET `so_do` = 'assets/img/so_do_nha_ga/HAN-T2-01.jpg' WHERE `id` = 2;

-- Cap nhat anh so do nha ga CXR T1/T2
UPDATE `nha_ga` SET `so_do` = 'assets/img/so_do_nha_ga/CXR-T1-01.jpg' WHERE `id` = 7;
UPDATE `nha_ga` SET `so_do` = 'assets/img/so_do_nha_ga/CXR-T2-01.jpg' WHERE `id` = 8;

-- Cap nhat anh so do nha ga VCA, HPH, PQC, SGN, DAD
UPDATE `nha_ga` SET `so_do` = 'assets/img/so_do_nha_ga/VCA-T1-01.jpg' WHERE `id` = 12;
UPDATE `nha_ga` SET `so_do` = 'assets/img/so_do_nha_ga/HPH-T1-01.jpg' WHERE `id` = 9;
UPDATE `nha_ga` SET `so_do` = 'assets/img/so_do_nha_ga/PQC-T1-01.jpg' WHERE `id` = 11;
UPDATE `nha_ga` SET `so_do` = 'assets/img/so_do_nha_ga/SGN-T1-01.jpg' WHERE `id` = 3;
UPDATE `nha_ga` SET `so_do` = 'assets/img/so_do_nha_ga/SGN-T2-01.jpg' WHERE `id` = 4;
UPDATE `nha_ga` SET `so_do` = 'assets/img/so_do_nha_ga/SGN-T3-01.jpg' WHERE `id` = 22;
UPDATE `nha_ga` SET `so_do` = 'assets/img/so_do_nha_ga/DAD-T1-01.jpg' WHERE `id` = 5;
UPDATE `nha_ga` SET `so_do` = 'assets/img/so_do_nha_ga/DAD-T3-01.jpg' WHERE `id` = 21;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
