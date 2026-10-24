<?php
class BaoTriController {
    private $db;

    /** Các giá trị hợp lệ theo enum trong CSDL */
    private const MUC_DO_HOP_LE            = ['Thap', 'Trung_Binh', 'Khan_Cap'];
    private const TRANG_THAI_TICKET_HOP_LE = ['Moi', 'Dang_Xu_Ly', 'Hoan_Thanh'];
    private const LOAI_THIET_BI_HOP_LE     = ['Den_Tin_Hieu', 'Thiet_Bi_Dan_Duong', 'Xe_Thang', 'Xe_Keo', 'Khac'];
    private const DINH_DANG_ANH_HOP_LE     = ['jpg', 'jpeg', 'png', 'gif'];
    private const KICH_THUOC_ANH_TOI_DA    = 5242880; // 5 MB

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /* =========================================================
     * DỮ LIỆU CHO VIEW
     * ========================================================= */

    public function getIndexData() {
        $sql = "SELECT y.id, y.id_thiet_bi, y.mo_ta_su_co, y.hinh_anh_minh_hoa, y.muc_do,
                       y.trang_thai_xu_ly, y.ngay_tao, t.ten_thiet_bi
                FROM yeu_cau_bao_tri y
                JOIN thiet_bi_san_bay t ON y.id_thiet_bi = t.id
                ORDER BY y.ngay_tao DESC";
        $result = $this->db->query($sql);
        $tickets = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

        // Thống kê lấy trực tiếp từ CSDL (không hardcode)
        $thong_ke = [
            'dang_mo'          => 0,
            'khan_cap'         => 0,
            'hoan_thanh_thang' => 0,
            'thiet_bi_bao_tri' => 0,
        ];

        $result = $this->db->query(
            "SELECT
                SUM(trang_thai_xu_ly IN ('Moi', 'Dang_Xu_Ly')) AS dang_mo,
                SUM(muc_do = 'Khan_Cap' AND trang_thai_xu_ly <> 'Hoan_Thanh') AS khan_cap,
                SUM(trang_thai_xu_ly = 'Hoan_Thanh'
                    AND MONTH(ngay_tao) = MONTH(CURRENT_DATE())
                    AND YEAR(ngay_tao) = YEAR(CURRENT_DATE())) AS hoan_thanh_thang
             FROM yeu_cau_bao_tri"
        );
        if ($result && ($row = $result->fetch_assoc())) {
            $thong_ke['dang_mo']          = (int) $row['dang_mo'];
            $thong_ke['khan_cap']         = (int) $row['khan_cap'];
            $thong_ke['hoan_thanh_thang'] = (int) $row['hoan_thanh_thang'];
        }

        $result = $this->db->query("SELECT COUNT(*) AS so_luong FROM thiet_bi_san_bay WHERE trang_thai = 'Bao_Tri'");
        if ($result && ($row = $result->fetch_assoc())) {
            $thong_ke['thiet_bi_bao_tri'] = (int) $row['so_luong'];
        }

        return [
            'tickets'  => $tickets,
            'thong_ke' => $thong_ke,
        ];
    }

    public function getThietBiList() {
        $result = $this->db->query(
            "SELECT id, ten_thiet_bi, loai_thiet_bi, trang_thai
             FROM thiet_bi_san_bay
             ORDER BY ten_thiet_bi"
        );
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getSanBayList() {
        $result = $this->db->query("SELECT id, ten_san_bay FROM san_bay ORDER BY ten_san_bay");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getDanhSachThietBi() {
        $sql = "SELECT tb.id, tb.ten_thiet_bi, tb.loai_thiet_bi, tb.trang_thai, sb.ten_san_bay,
                       (SELECT COUNT(*) FROM yeu_cau_bao_tri y WHERE y.id_thiet_bi = tb.id) AS so_phieu
                FROM thiet_bi_san_bay tb
                JOIN san_bay sb ON tb.id_san_bay = sb.id
                ORDER BY tb.id DESC";
        $result = $this->db->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getDanhSachTauBay() {
        $sql = "SELECT id, ma_loai, tai_trong_cat_canh_mtow, suc_chua_hanh_khach
                FROM loai_tau_bay
                ORDER BY id DESC";
        $result = $this->db->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    /* =========================================================
     * CÁC MÀN HÌNH (giữ để tương thích nếu có router bên ngoài gọi)
     * ========================================================= */

    public function index() {
        $data     = $this->getIndexData();
        $tickets  = $data['tickets'];
        $thong_ke = $data['thong_ke'];

        require_once __DIR__ . '/../views/pages/bao_tri/index.php';
    }

    public function createTicket() {
        // Form tạo phiếu đã chuyển thành modal trên trang danh sách
        $this->redirect($this->viewUrl('index.php', ['open' => 'create']));
    }

    public function danhSachThietBi() {
        $thiet_bi = $this->getDanhSachThietBi();

        require_once __DIR__ . '/../views/pages/bao_tri/thiet_bi.php';
    }

    public function createThietBi() {
        $san_bay_list = $this->getSanBayList();

        require_once __DIR__ . '/../views/pages/bao_tri/them_thiet_bi.php';
    }

    public function danhSachTauBay() {
        $tau_bay = $this->getDanhSachTauBay();

        require_once __DIR__ . '/../views/pages/bao_tri/tau_bay.php';
    }

    /* =========================================================
     * XỬ LÝ NGHIỆP VỤ
     * ========================================================= */

    public function storeTicket() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect($this->viewUrl('index.php', ['open' => 'create']));
        }

        $id_thiet_bi = isset($_POST['id_thiet_bi']) ? (int) $_POST['id_thiet_bi'] : 0;
        $mo_ta_su_co = isset($_POST['mo_ta_su_co']) ? trim((string) $_POST['mo_ta_su_co']) : '';
        $muc_do      = isset($_POST['muc_do']) ? (string) $_POST['muc_do'] : '';

        if ($id_thiet_bi <= 0) {
            $this->setFlash('error', 'Vui lòng chọn thiết bị gặp sự cố.');
            $this->redirect($this->viewUrl('index.php', ['open' => 'create']));
        }
        if ($mo_ta_su_co === '') {
            $this->setFlash('error', 'Vui lòng nhập mô tả sự cố.');
            $this->redirect($this->viewUrl('index.php', ['open' => 'create']));
        }
        if (!in_array($muc_do, self::MUC_DO_HOP_LE, true)) {
            $this->setFlash('error', 'Mức độ sự cố không hợp lệ.');
            $this->redirect($this->viewUrl('index.php', ['open' => 'create']));
        }

        // Kiểm tra thiết bị có thật trong CSDL
        $stmt = $this->db->prepare("SELECT id FROM thiet_bi_san_bay WHERE id = ?");
        $stmt->bind_param('i', $id_thiet_bi);
        $stmt->execute();
        $stmt->store_result();
        $thiet_bi_ton_tai = $stmt->num_rows > 0;
        $stmt->close();

        if (!$thiet_bi_ton_tai) {
            $this->setFlash('error', 'Thiết bị không tồn tại trong hệ thống.');
            $this->redirect($this->viewUrl('index.php', ['open' => 'create']));
        }

        // Ảnh minh họa là tùy chọn (cột hinh_anh_minh_hoa cho phép NULL)
        $hinh_anh_minh_hoa = null;
        $uploadDir         = null;
        $newFileName       = null;

        if (isset($_FILES['hinh_anh_minh_hoa']) && $_FILES['hinh_anh_minh_hoa']['error'] !== UPLOAD_ERR_NO_FILE) {
            $file = $_FILES['hinh_anh_minh_hoa'];

            if ($file['error'] !== UPLOAD_ERR_OK) {
                $this->setFlash('error', 'Tải ảnh minh họa thất bại. Vui lòng thử lại.');
                $this->redirect($this->viewUrl('index.php', ['open' => 'create']));
            }
            if ($file['size'] > self::KICH_THUOC_ANH_TOI_DA) {
                $this->setFlash('error', 'Ảnh minh họa không được vượt quá 5 MB.');
                $this->redirect($this->viewUrl('index.php', ['open' => 'create']));
            }

            $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($fileExtension, self::DINH_DANG_ANH_HOP_LE, true)) {
                $this->setFlash('error', 'Chỉ chấp nhận ảnh JPG, JPEG, PNG hoặc GIF.');
                $this->redirect($this->viewUrl('index.php', ['open' => 'create']));
            }

            $uploadDir = __DIR__ . '/../public/uploads/bao_tri/';
            if (!is_dir($uploadDir) && !mkdir($uploadDir, 0777, true) && !is_dir($uploadDir)) {
                $this->setFlash('error', 'Không thể tạo thư mục lưu ảnh.');
                $this->redirect($this->viewUrl('index.php', ['open' => 'create']));
            }

            $newFileName = time() . '_' . bin2hex(random_bytes(6)) . '.' . $fileExtension;
            if (!move_uploaded_file($file['tmp_name'], $uploadDir . $newFileName)) {
                $this->setFlash('error', 'Không thể lưu ảnh minh họa lên máy chủ.');
                $this->redirect($this->viewUrl('index.php', ['open' => 'create']));
            }

            $hinh_anh_minh_hoa = $newFileName;
        }

        try {
            $this->db->begin_transaction();

            $stmt = $this->db->prepare(
                "INSERT INTO yeu_cau_bao_tri (id_thiet_bi, mo_ta_su_co, hinh_anh_minh_hoa, muc_do, trang_thai_xu_ly)
                 VALUES (?, ?, ?, ?, 'Moi')"
            );
            $stmt->bind_param('isss', $id_thiet_bi, $mo_ta_su_co, $hinh_anh_minh_hoa, $muc_do);
            $stmt->execute();
            $stmt->close();

            // Cập nhật trạng thái thiết bị thành Hỏng
            $stmt = $this->db->prepare("UPDATE thiet_bi_san_bay SET trang_thai = 'Hong' WHERE id = ?");
            $stmt->bind_param('i', $id_thiet_bi);
            $stmt->execute();
            $stmt->close();

            $this->db->commit();
        } catch (Throwable $e) {
            try {
                $this->db->rollback();
            } catch (Throwable $ignored) {
            }

            if ($newFileName !== null && $uploadDir !== null && is_file($uploadDir . $newFileName)) {
                @unlink($uploadDir . $newFileName);
            }

            $this->setFlash('error', 'Lỗi cơ sở dữ liệu: ' . $e->getMessage());
            $this->redirect($this->viewUrl('index.php', ['open' => 'create']));
        }

        $this->setFlash('success', 'Đã tạo phiếu bảo trì thành công.');
        $this->redirect($this->viewUrl('index.php'));
    }

    public function storeThietBi() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect($this->viewUrl('them_thiet_bi.php'));
        }

        $ten_thiet_bi  = isset($_POST['ten_thiet_bi']) ? trim((string) $_POST['ten_thiet_bi']) : '';
        $loai_thiet_bi = isset($_POST['loai_thiet_bi']) ? (string) $_POST['loai_thiet_bi'] : '';
        $id_san_bay    = isset($_POST['id_san_bay']) ? (int) $_POST['id_san_bay'] : 0;

        if ($ten_thiet_bi === '' || $id_san_bay <= 0 || !in_array($loai_thiet_bi, self::LOAI_THIET_BI_HOP_LE, true)) {
            $this->setFlash('error', 'Dữ liệu thiết bị không hợp lệ.');
            $this->redirect($this->viewUrl('them_thiet_bi.php'));
        }

        try {
            $stmt = $this->db->prepare(
                "INSERT INTO thiet_bi_san_bay (ten_thiet_bi, loai_thiet_bi, id_san_bay, trang_thai)
                 VALUES (?, ?, ?, 'Tot')"
            );
            $stmt->bind_param('ssi', $ten_thiet_bi, $loai_thiet_bi, $id_san_bay);
            $stmt->execute();
            $stmt->close();
        } catch (Throwable $e) {
            $this->setFlash('error', 'Lỗi thêm thiết bị: ' . $e->getMessage());
            $this->redirect($this->viewUrl('them_thiet_bi.php'));
        }

        $this->setFlash('success', 'Đã thêm thiết bị mới.');
        $this->redirect($this->viewUrl('thiet_bi.php'));
    }

    public function themTauBay() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect($this->viewUrl('tau_bay.php'));
        }

        $ma_loai  = isset($_POST['ma_loai']) ? trim((string) $_POST['ma_loai']) : '';
        $mtow     = isset($_POST['tai_trong_cat_canh_mtow']) ? (float) $_POST['tai_trong_cat_canh_mtow'] : 0;
        $suc_chua = isset($_POST['suc_chua_hanh_khach']) ? (int) $_POST['suc_chua_hanh_khach'] : 0;

        if ($ma_loai === '' || $mtow <= 0 || $suc_chua <= 0) {
            $this->setFlash('error', 'Thông tin loại tàu bay không hợp lệ.');
            $this->redirect($this->viewUrl('tau_bay.php'));
        }

        try {
            $stmt = $this->db->prepare(
                "INSERT INTO loai_tau_bay (ma_loai, tai_trong_cat_canh_mtow, suc_chua_hanh_khach)
                 VALUES (?, ?, ?)"
            );
            $stmt->bind_param('sdi', $ma_loai, $mtow, $suc_chua);
            $stmt->execute();
            $stmt->close();
        } catch (mysqli_sql_exception $e) {
            if ((int) $e->getCode() === 1062) {
                $this->setFlash('error', 'Mã loại máy bay này đã tồn tại trong hệ thống.');
            } else {
                $this->setFlash('error', 'Lỗi thêm loại tàu bay: ' . $e->getMessage());
            }
            $this->redirect($this->viewUrl('tau_bay.php'));
        } catch (Throwable $e) {
            $this->setFlash('error', 'Lỗi thêm loại tàu bay: ' . $e->getMessage());
            $this->redirect($this->viewUrl('tau_bay.php'));
        }

        $this->setFlash('success', 'Đã thêm loại tàu bay mới.');
        $this->redirect($this->viewUrl('tau_bay.php'));
    }

    public function capNhatTrangThaiTicket() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect($this->viewUrl('index.php'));
        }

        $id_ticket      = isset($_POST['id_ticket']) ? (int) $_POST['id_ticket'] : 0;
        $trang_thai_moi = isset($_POST['trang_thai_xu_ly']) ? (string) $_POST['trang_thai_xu_ly'] : '';

        if ($id_ticket <= 0 || !in_array($trang_thai_moi, self::TRANG_THAI_TICKET_HOP_LE, true)) {
            $this->setFlash('error', 'Dữ liệu cập nhật phiếu không hợp lệ.');
            $this->redirect($this->viewUrl('index.php'));
        }

        // Lấy id_thiet_bi từ CSDL, không tin hidden input của form
        $stmt = $this->db->prepare("SELECT id_thiet_bi FROM yeu_cau_bao_tri WHERE id = ?");
        $stmt->bind_param('i', $id_ticket);
        $stmt->execute();
        $stmt->bind_result($id_thiet_bi);
        $tim_thay = $stmt->fetch();
        $stmt->close();

        if (!$tim_thay) {
            $this->setFlash('error', 'Không tìm thấy phiếu bảo trì cần cập nhật.');
            $this->redirect($this->viewUrl('index.php'));
        }

        try {
            $this->db->begin_transaction();

            $stmt = $this->db->prepare("UPDATE yeu_cau_bao_tri SET trang_thai_xu_ly = ? WHERE id = ?");
            $stmt->bind_param('si', $trang_thai_moi, $id_ticket);
            $stmt->execute();
            $stmt->close();

            $trang_thai_thiet_bi = 'Hong';
            if ($trang_thai_moi === 'Hoan_Thanh') {
                // Chỉ chuyển thiết bị về Tốt khi không còn phiếu nào đang mở
                $stmt = $this->db->prepare(
                    "SELECT COUNT(*) FROM yeu_cau_bao_tri
                     WHERE id_thiet_bi = ? AND trang_thai_xu_ly <> 'Hoan_Thanh'"
                );
                $stmt->bind_param('i', $id_thiet_bi);
                $stmt->execute();
                $stmt->bind_result($so_phieu_mo);
                $stmt->fetch();
                $stmt->close();

                if ((int) $so_phieu_mo === 0) {
                    $trang_thai_thiet_bi = 'Tot';
                }
            }

            $stmt = $this->db->prepare("UPDATE thiet_bi_san_bay SET trang_thai = ? WHERE id = ?");
            $stmt->bind_param('si', $trang_thai_thiet_bi, $id_thiet_bi);
            $stmt->execute();
            $stmt->close();

            $this->db->commit();
        } catch (Throwable $e) {
            try {
                $this->db->rollback();
            } catch (Throwable $ignored) {
            }

            $this->setFlash('error', 'Lỗi cập nhật phiếu bảo trì: ' . $e->getMessage());
            $this->redirect($this->viewUrl('index.php'));
        }

        $this->setFlash('success', 'Đã cập nhật trạng thái phiếu bảo trì.');
        $this->redirect($this->viewUrl('index.php'));
    }

    public function xoaThietBi() {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if ($id <= 0) {
            $this->setFlash('error', 'Thiết bị cần xóa không hợp lệ.');
            $this->redirect($this->viewUrl('thiet_bi.php'));
        }

        // Không xóa thiết bị đã có lịch sử phiếu bảo trì (tránh CASCADE xóa mất lịch sử)
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM yeu_cau_bao_tri WHERE id_thiet_bi = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->bind_result($so_phieu);
        $stmt->fetch();
        $stmt->close();

        if ((int) $so_phieu > 0) {
            $this->setFlash('error', 'Không thể xóa thiết bị vì đang có ' . (int) $so_phieu . ' phiếu bảo trì liên quan.');
            $this->redirect($this->viewUrl('thiet_bi.php'));
        }

        try {
            $stmt = $this->db->prepare("DELETE FROM thiet_bi_san_bay WHERE id = ?");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $stmt->close();
        } catch (Throwable $e) {
            $this->setFlash('error', 'Lỗi xóa thiết bị: ' . $e->getMessage());
            $this->redirect($this->viewUrl('thiet_bi.php'));
        }

        $this->setFlash('success', 'Đã xóa thiết bị.');
        $this->redirect($this->viewUrl('thiet_bi.php'));
    }

    public function xoaTauBay() {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if ($id <= 0) {
            $this->setFlash('error', 'Loại tàu bay cần xóa không hợp lệ.');
            $this->redirect($this->viewUrl('tau_bay.php'));
        }

        // Kiểm tra ràng buộc khóa ngoại với chuyen_bay_ngay trước khi xóa (chỉ đọc)
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM chuyen_bay_ngay WHERE id_loai_tau_bay = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->bind_result($so_chuyen_bay);
        $stmt->fetch();
        $stmt->close();

        if ((int) $so_chuyen_bay > 0) {
            $this->setFlash('error', 'Không thể xóa loại tàu bay vì đang được dùng trong ' . (int) $so_chuyen_bay . ' chuyến bay.');
            $this->redirect($this->viewUrl('tau_bay.php'));
        }

        try {
            $stmt = $this->db->prepare("DELETE FROM loai_tau_bay WHERE id = ?");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $stmt->close();
        } catch (Throwable $e) {
            $this->setFlash('error', 'Lỗi xóa loại tàu bay: ' . $e->getMessage());
            $this->redirect($this->viewUrl('tau_bay.php'));
        }

        $this->setFlash('success', 'Đã xóa loại tàu bay.');
        $this->redirect($this->viewUrl('tau_bay.php'));
    }

    /* =========================================================
     * TIỆN ÍCH
     * ========================================================= */

    private function viewUrl($file, array $query = []) {
        $base = defined('BASE_URL') ? rtrim(BASE_URL, '/') : '';
        $url  = $base . '/views/pages/bao_tri/' . ltrim($file, '/');

        if (!empty($query)) {
            $url .= '?' . http_build_query($query);
        }

        return $url;
    }

    private function setFlash($type, $message) {
        $_SESSION['flash_' . $type] = $message;
    }

    private function redirect($url) {
        header('Location: ' . $url);
        exit();
    }
}
