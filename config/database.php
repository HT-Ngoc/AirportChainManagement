<?php


$host    = 'localhost';
$user    = 'root';
$pass    = ''; 
$db_name = 'quan_ly_chuoi_san_bay';

// Khởi tạo kết nối mysqli
$conn = mysqli_connect($host, $user, $pass, $db_name);

if (!$conn) {
    die('Kết nối CSDL thất bại: ' . mysqli_connect_error());
}


mysqli_set_charset($conn, 'utf8mb4');
