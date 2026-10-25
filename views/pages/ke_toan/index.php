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

    <!--PHẦN 3: DANH SÁCH CHUYẾN BAY-->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Chuyến bay đã hoàn thành (Chờ lập hóa đơn)</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap align-middle">
                    <thead>
                        <tr>
                            <th>Mã chuyến bay</th>
                            <th>Trạng thái</th>
                            <th class="text-end">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($danh_sach_chuyen_bay)): ?>
                            <tr><td colspan="3" class="text-center">Không có chuyến bay nào cần lập hóa đơn.</td></tr>
                        <?php endif; ?>
                        
                        <?php foreach ($danh_sach_chuyen_bay as $cb): ?>
                        <tr>
                            <td>
                                <strong>Số hiệu: <?= htmlspecialchars($cb['so_hieu']) ?></strong> 
                                <br><small class="text-muted">ID: #<?= htmlspecialchars($cb['id']) ?></small>
                            </td>
                            <td><span class="badge text-bg-success"><?= htmlspecialchars($cb['trang_thai_tong']) ?></span></td>
                            <td class="text-end">
                                <a href="index.php?page=ke_toan&tao_hoa_don=<?= $cb['id'] ?>" 
                                   class="btn btn-sm btn-warning"
                                   onclick="return confirm('Hệ thống sẽ tự động tính MTOW, giờ đỗ, số khách và lập hóa đơn. Tiếp tục?');">
                                   <i class="bi bi-calculator"></i> Tính và Lập hóa đơn
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!--Hóa đơn chờ thanh toán-->
<div class="row mt-4">
    <div class="col-12">
        <div class="card card-warning card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="bi bi-cash-coin me-2"></i>Hóa đơn chờ thanh toán</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Mã HĐ</th>
                            <th>Chuyến bay</th>
                            <th>Tổng tiền</th>
                            <th>Hạn chót</th>
                            <th class="text-end">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($danh_sach_hoa_don_cho)): ?>
                            <tr><td colspan="5" class="text-center">Không có hóa đơn nào chờ thanh toán.</td></tr>
                        <?php endif; ?>
                        
                        <?php foreach ($danh_sach_hoa_don_cho as $hd): ?>
                        <tr>
                            <td>#<?= $hd['id'] ?></td>
                            <td><?= htmlspecialchars($hd['so_hieu']) ?></td>
                            <td class="text-danger fw-bold"><?= number_format($hd['tong_tien']) ?> VNĐ</td>
                            <td><?= date('d/m/Y', strtotime($hd['han_thanh_toan'])) ?></td>
                            <td class="text-end">
                                <a href="index.php?page=ke_toan&thanh_toan_id=<?= $hd['id'] ?>" 
                                   class="btn btn-sm btn-success"
                                   onclick="return confirm('Xác nhận khách đã thanh toán hóa đơn này?');">
                                   <i class="bi bi-check-circle"></i> Xác nhận thu tiền
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!--PHẦN 4: BÁO CÁO TÀI CHÍNH-->
<div class="row mt-4">
    <!--BẢNG 1: DOANH THU THEO THÁNG-->
    <div class="col-md-6">
        <div class="card card-success card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="bi bi-graph-up me-2"></i>Doanh thu theo tháng</h3>
            </div>
            <div class="card-body p-0 table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Tháng</th>
                            <th class="text-end">Tổng Doanh Thu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($data_thang)): ?>
                            <tr><td colspan="2" class="text-center text-muted">Chưa có dữ liệu doanh thu</td></tr>
                        <?php endif; ?>
                        
                        <?php foreach ($data_thang as $dt): ?>
                        <tr>
                            <td>Tháng <?= $dt['thang'] ?></td>
                            <td class="text-end fw-bold text-success">
                                <?= number_format($dt['tong_doanh_thu']) ?> VNĐ
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!--BẢNG 2: DOANH THU THEO HÃNG HÀNG KHÔNG-->
    <div class="col-md-6">
        <div class="card card-info card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="bi bi-airplane me-2"></i>Doanh thu theo Hãng Hàng Không</h3>
            </div>
            <div class="card-body p-0 table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Hãng Hàng Không</th>
                            <th class="text-end">Doanh Thu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($data_hang)): ?>
                            <tr><td colspan="2" class="text-center text-muted">Chưa có dữ liệu đối tác</td></tr>
                        <?php endif; ?>

                        <?php foreach ($data_hang as $hh): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($hh['ten_hang']) ?></strong></td>
                            <td class="text-end fw-bold text-primary">
                                <?= number_format($hh['doanh_thu']) ?> VNĐ
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!--PHẦN 5: THỐNG KÊ HÀNH KHÁCH THEO SÂN BAY-->
<div class="row mt-3 mb-5">
    <div class="col-12">
        <div class="card card-secondary card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="bi bi-people me-2"></i>Thống kê sản lượng hành khách theo Sân bay</h3>
            </div>
            <div class="card-body">
                <form method="GET" class="row g-3 align-items-center">
                    <input type="hidden" name="page" value="ke_toan">
                    <div class="col-auto">
                        <label class="col-form-label fw-bold">Chọn sân bay:</label>
                    </div>
                    <div class="col-md-4">
                        <select name="san_bay" class="form-select" required>
                            <option value="">-- Chọn sân bay để thống kê --</option>
                            <?php foreach ($danh_sach_san_bay as $sb): ?>
                                <option value="<?= $sb['id'] ?>" <?= ($san_bay_chon == $sb['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($sb['ten_san_bay']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary">Thống kê</button>
                    </div>
                </form>

                <?php if ($san_bay_chon > 0): ?>
                    <div class="alert alert-info mt-4 text-center">
                        <h4 class="mb-0">Tổng sản lượng hành khách (Đi + Đến): <strong>
                            <?= number_format($tong_khach_san_bay) ?></strong> lượt khách</h4>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/main.php';
?>