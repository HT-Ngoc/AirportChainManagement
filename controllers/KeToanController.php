<?php
class KeToanController {
    private $conn;
    public function __construct($db) {
        $this->conn = $db;
    }

//1. QUẢN LÝ ĐƠN GIÁ

// Danh sách đơn giá
    public function DanhSachDonGia() {
        $sql = "SELECT * FROM bang_gia_dich_vu";
        $result = mysqli_query($this->conn, $sql);
        $data = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }
        }
        return $data;
    }

// Thêm đơn giá mới
    public function ThemDonGia($ten, $gia, $don_vi) {
        // Dùng mysqli_real_escape_string để chống SQL Injection
        $ten = mysqli_real_escape_string($this->conn, $ten);
        $gia = mysqli_real_escape_string($this->conn, $gia);
        $don_vi = mysqli_real_escape_string($this->conn, $don_vi);
        $sql = "INSERT INTO bang_gia_dich_vu (loai_dich_vu, don_gia, don_vi_tinh) 
                VALUES ('$ten', '$gia', '$don_vi')";
        return mysqli_query($this->conn, $sql);
    }
}
