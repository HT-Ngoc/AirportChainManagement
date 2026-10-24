<?php
require_once __DIR__ . '/_bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $baoTriController->storeTicket();
}

if (!isset($thiet_bi_list)) {
    $thiet_bi_list = $baoTriController->getThietBiList();
}

$pageTitle  = 'Tạo Yêu Cầu Bảo Trì';
$pageHeader = 'Báo cáo sự cố thiết bị';
$navActive  = 'bao_tri';
$breadcrumb = [
    ['label' => 'Tổng quan', 'url' => BASE_URL . '/index.php'],
    ['label' => 'Bảo trì', 'url' => baoTriUrl('index.php')],
    ['label' => 'Tạo phiếu', 'url' => null],
];

$loaiThietBiLabels = [
    'Den_Tin_Hieu'        => 'Đèn tín hiệu',
    'Thiet_Bi_Dan_Duong'  => 'Thiết bị dẫn đường',
    'Xe_Thang'            => 'Xe thang',
    'Xe_Keo'              => 'Xe kéo',
    'Khac'                => 'Khác',
];

$trangThaiThietBiLabels = [
    'Tot'     => 'Tốt',
    'Bao_Tri' => 'Đang bảo trì',
    'Hong'    => 'Hỏng',
];

ob_start();
?>
<?php require __DIR__ . '/_flash.php'; ?>
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card card-danger">
            <div class="card-header">
                <h3 class="card-title"><i class="bi bi-exclamation-triangle-fill me-2"></i> Lập phiếu báo hỏng thiết bị</h3>
            </div>
            <form action="<?= baoTriUrl('create_ticket.php') ?>" method="POST" enctype="multipart/form-data">
                <div class="card-body">
                    <div class="form-group mb-3">
                        <label for="id_thiet_bi">Chọn thiết bị gặp sự cố <span class="text-danger">*</span></label>
                        <select name="id_thiet_bi" id="id_thiet_bi" class="form-select" required>
                            <option value="">-- Chọn thiết bị --</option>
                            <?php if (!empty($thiet_bi_list)): ?>
                                <?php foreach ($thiet_bi_list as $tb): ?>
                                    <option value="<?= (int) $tb['id'] ?>">
                                        <?= htmlspecialchars($tb['ten_thiet_bi'], ENT_QUOTES, 'UTF-8') ?>
                                        (<?= $loaiThietBiLabels[$tb['loai_thiet_bi']] ?? htmlspecialchars(str_replace('_', ' ', $tb['loai_thiet_bi']), ENT_QUOTES, 'UTF-8') ?>)
                                        - <?= $trangThaiThietBiLabels[$tb['trang_thai']] ?? htmlspecialchars($tb['trang_thai'], ENT_QUOTES, 'UTF-8') ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="mo_ta_su_co">Mô tả chi tiết sự cố <span class="text-danger">*</span></label>
                        <textarea name="mo_ta_su_co" id="mo_ta_su_co" class="form-control" rows="4" required></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label for="muc_do">Mức độ nghiêm trọng <span class="text-danger">*</span></label>
                        <select name="muc_do" id="muc_do" class="form-select" required>
                            <option value="Thap">Thấp</option>
                            <option value="Trung_Binh">Trung bình</option>
                            <option value="Khan_Cap">Khẩn cấp</option>
                        </select>
                    </div>
                    <div class="form-group mb-4">
                        <label for="hinh_anh_minh_hoa">Hình ảnh minh họa</label>
                        <input type="file" name="hinh_anh_minh_hoa" id="hinh_anh_minh_hoa" class="form-control"
                               accept="image/png, image/jpeg, image/jpg, image/gif">
                        <div class="form-text">
                            Không bắt buộc. Chấp nhận JPG, JPEG, PNG, GIF — tối đa 5 MB.
                        </div>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <a href="<?= baoTriUrl('index.php') ?>" class="btn btn-secondary">Hủy bỏ</a>
                    <button type="submit" class="btn btn-danger">Gửi Yêu Cầu</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/main.php';
