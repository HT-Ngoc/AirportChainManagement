<?php
require_once __DIR__ . '/_bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $baoTriController->storeThietBi();
}

if (!isset($san_bay_list)) {
    $san_bay_list = $baoTriController->getSanBayList();
}

$pageTitle  = 'Thêm Thiết Bị Mới';
$pageHeader = 'Khai báo trang thiết bị mặt đất';
$navActive  = 'bao_tri';
$breadcrumb = [
    ['label' => 'Tổng quan', 'url' => BASE_URL . '/index.php'],
    ['label' => 'Bảo trì', 'url' => baoTriUrl('index.php')],
    ['label' => 'Trang thiết bị', 'url' => baoTriUrl('thiet_bi.php')],
    ['label' => 'Thêm mới', 'url' => null],
];

ob_start();
?>
<?php require __DIR__ . '/_flash.php'; ?>
<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card card-success">
            <div class="card-header"><h3 class="card-title">Thông tin thiết bị</h3></div>
            <form action="<?= baoTriUrl('them_thiet_bi.php') ?>" method="POST">
                <div class="card-body">
                    <div class="form-group mb-3">
                        <label for="ten_thiet_bi">Tên thiết bị <span class="text-danger">*</span></label>
                        <input type="text" name="ten_thiet_bi" id="ten_thiet_bi" class="form-control" maxlength="100" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="loai_thiet_bi">Phân loại <span class="text-danger">*</span></label>
                        <select name="loai_thiet_bi" id="loai_thiet_bi" class="form-select" required>
                            <option value="Den_Tin_Hieu">Đèn tín hiệu</option>
                            <option value="Thiet_Bi_Dan_Duong">Thiết bị dẫn đường</option>
                            <option value="Xe_Thang">Xe thang</option>
                            <option value="Xe_Keo">Xe kéo</option>
                            <option value="Khac">Khác</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="id_san_bay">Sân bay quản lý <span class="text-danger">*</span></label>
                        <select name="id_san_bay" id="id_san_bay" class="form-select" required>
                            <?php if (!empty($san_bay_list)): ?>
                                <?php foreach ($san_bay_list as $sb): ?>
                                    <option value="<?= (int) $sb['id'] ?>"><?= htmlspecialchars($sb['ten_san_bay'], ENT_QUOTES, 'UTF-8') ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <a href="<?= baoTriUrl('thiet_bi.php') ?>" class="btn btn-secondary">Hủy</a>
                    <button type="submit" class="btn btn-success">Lưu Thiết Bị</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/main.php';
