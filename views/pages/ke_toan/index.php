<?php

//1. Kết nối cơ sở dữ liệu
require_once __DIR__ . '/../../../config/database.php'; 
require_once __DIR__ . '/../../../controllers/KeToanController.php';
$keToan = new KeToanController($conn);

//2. Xử lý các hành động được gửi lên
//Xử lý thêm đơn giá mới
if (isset($_POST['btn_them_gia'])) {
    $keToan->ThemDonGia($_POST['ten_dich_vu'], $_POST['don_gia'], $_POST['don_vi_tinh']);
    echo "<script>alert('Thêm đơn giá thành công!'); 
    window.location.href='index.php?page=ke_toan';</script>";
    exit();
}

// Xử lý phát hành hóa đơn tự động
if (isset($_GET['tao_hoa_don']) && !empty($_GET['tao_hoa_don'])) {
    $ma_cb = $_GET['tao_hoa_don'];
    
    if ($keToan->TinhVaTaoHoaDon($ma_cb)) {
        echo "<script>alert('Đã tính toán và tạo hóa đơn thành công!'); 
        window.location.href='index.php?page=ke_toan';</script>";
    } else {
        echo "<script>alert('Lỗi khi tạo hóa đơn! Vui lòng kiểm tra lại dữ liệu chuyến bay.');</script>";
    }
    exit();
}

// Xử lý xác nhận thanh toán
if (isset($_GET['thanh_toan_id'])) {
    $keToan->XacNhanThanhToan($_GET['thanh_toan_id']);
    echo "<script>alert('Đã xác nhận thanh toán thành công!'); 
    window.location.href='index.php?page=ke_toan';</script>";
    exit();
}

// 3. Lấy dữ liệu để hiển thị trên trang
$danh_sach_gia = $keToan->DanhSachDonGia();
$danh_sach_chuyen_bay = $keToan->ChuyenBayHoanThanh();
$data_thang = $keToan->DoanhThuTheoThang();
$data_hang = $keToan->DoanhThuTheoHang();
$stats = $keToan->ThongKeNhanh();
$danh_sach_hoa_don_cho = $keToan->DanhSachHoaDonCho();
$danh_sach_san_bay = $keToan->DanhSachSanBay();
$san_bay_chon = isset($_GET['san_bay']) ? (int)$_GET['san_bay'] : 0;
$tong_khach_san_bay = 0;
if ($san_bay_chon > 0) {
    $tong_khach_san_bay = $keToan->ThongKeHanhKhachSanBay($san_bay_chon);
}

// 4. Cấu hình trang
$pageTitle  = 'Kế toán & doanh thu';
$pageHeader = 'Kế toán & doanh thu';
$navActive  = 'ke_toan';
$breadcrumb = [
    ['label' => 'Tổng quan', 'url' => '/AirportChainManagement/index.php'],
    ['label' => 'Kế toán', 'url' => null],
];
ob_start();
?>


    <!--PHẦN 2: QUẢN LÝ BIỂU PHÍ-->
    <div class="col-md-4">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">Biểu phí dịch vụ</h3>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-2">
                        <input type="text" name="ten_dich_vu" class="form-control form-control-sm" 
                        placeholder="Tên phí (VD: Phí hạ cánh)" required>
                    </div>
                    <div class="mb-2">
                        <input type="number" name="don_gia" class="form-control form-control-sm" 
                        placeholder="Đơn giá (VNĐ)" required>
                    </div>
                    <div class="mb-2">
                        <input type="text" name="don_vi_tinh" class="form-control form-control-sm" 
                        placeholder="Đơn vị (VD: VNĐ/Tấn)" required>
                    </div>
                    <button type="submit" name="btn_them_gia" class="btn btn-sm btn-primary w-100">Thêm đơn giá</button>
                </form>
                <hr>
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>Tên phí</th>
                            <th>Đơn giá</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($danh_sach_gia as $gia): ?>
                        <tr>
                            <td><?= htmlspecialchars($gia['loai_dich_vu']) ?></td>
                            <td><?= number_format($gia['don_gia']) ?> <br><small class="text-muted">
                                <?= htmlspecialchars($gia['don_vi_tinh']) ?></small></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/main.php';
?>