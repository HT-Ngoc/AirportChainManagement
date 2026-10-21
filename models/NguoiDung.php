<?php


class NguoiDung
{
    /** @var mysqli Kết nối CSDL dùng chung */
    private $conn;

    public function __construct($db_conn)
    {
        $this->conn = $db_conn;
    }

    /**
     * Tìm người dùng theo tên đăng nhập.
     *
     * @param string $ten_dang_nhap Tên đăng nhập người dùng nhập vào form
     * @return array|false Mảng dữ liệu user nếu tìm thấy, ngược lại false
     */
    public function kiemTraDangNhap($ten_dang_nhap)
    {
        // Chống SQL Injection bằng mysqli_real_escape_string
        $ten_dang_nhap_safe = mysqli_real_escape_string($this->conn, $ten_dang_nhap);

        $sql = "SELECT * FROM nguoi_dung WHERE ten_dang_nhap = '$ten_dang_nhap_safe'";
        $result = mysqli_query($this->conn, $sql);

        // Nếu câu lệnh SQL lỗi thì coi như không tìm thấy
        if (!$result) {
            return false;
        }

        if (mysqli_num_rows($result) > 0) {
            return mysqli_fetch_assoc($result); // Trả về mảng dữ liệu user
        }

        return false;
    }

    /**
     * Lấy thông tin người dùng theo khóa chính id.
     *
     * @param int $id
     * @return array|false
     */
    public function layTheoId($id)
    {
        $id = (int) $id;

        $sql = "SELECT * FROM nguoi_dung WHERE id = $id LIMIT 1";
        $result = mysqli_query($this->conn, $sql);

        if (!$result || mysqli_num_rows($result) === 0) {
            return false;
        }

        return mysqli_fetch_assoc($result);
    }
}
