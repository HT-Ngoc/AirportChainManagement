<?php
require_once __DIR__ . '/_bootstrap.php';

if (isset($_GET['action']) && $_GET['action'] === 'xoa') {
    $baoTriController->xoaTauBay();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $baoTriController->themTauBay();
}

if (!isset($tau_bay)) {
    $tau_bay = $baoTriController->getDanhSachTauBay();
}

$pageTitle  = 'Quản lý Loại tàu bay';
$pageHeader = 'Danh mục Loại tàu bay (MTOW)';
$navActive  = 'bao_tri';
$breadcrumb = [
    ['label' => 'Tổng quan', 'url' => BASE_URL . '/index.php'],
    ['label' => 'Bảo trì', 'url' => baoTriUrl('index.php')],
    ['label' => 'Loại tàu bay', 'url' => null],
];

ob_start();
?>
<?php require __DIR__ . '/_flash.php'; ?>
<div class="row">
    <div class="col-md-4">
        <div class="card card-primary">
            <div class="card-header"><h3 class="card-title">Thêm Dòng Máy Bay Mới</h3></div>
            <form action="<?= baoTriUrl('tau_bay.php') ?>" method="POST">
                <div class="card-body">
                    <div class="form-group mb-3">
                        <label for="ma_loai">Mã loại (VD: Boeing 787)</label>
                        <input type="text" name="ma_loai" id="ma_loai" class="form-control" maxlength="50" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="tai_trong_cat_canh_mtow">Tải trọng cất cánh (MTOW - Tấn)</label>
                        <input type="number" step="0.01" min="0.01" name="tai_trong_cat_canh_mtow" id="tai_trong_cat_canh_mtow" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="suc_chua_hanh_khach">Sức chứa hành khách</label>
                        <input type="number" min="1" name="suc_chua_hanh_khach" id="suc_chua_hanh_khach" class="form-control" required>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Lưu dữ liệu</button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Danh sách Dòng máy bay hiện có</h3></div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Mã Loại</th>
                            <th>MTOW (Tấn)</th>
                            <th>Sức chứa (Khách)</th>
                            <th class="text-end">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($tau_bay)): ?>
                            <?php foreach ($tau_bay as $tb): ?>
                                <tr>
                                    <td><?= (int) $tb['id'] ?></td>
                                    <td><strong><?= htmlspecialchars($tb['ma_loai'], ENT_QUOTES, 'UTF-8') ?></strong></td>
                                    <td><?= number_format((float) $tb['tai_trong_cat_canh_mtow'], 2) ?></td>
                                    <td><?= number_format((int) $tb['suc_chua_hanh_khach']) ?></td>
                                    <td class="text-end">
                                        <a href="<?= baoTriUrl('tau_bay.php', ['action' => 'xoa', 'id' => (int) $tb['id']]) ?>"
                                           class="btn btn-sm btn-outline-danger"
                                           onclick="return confirm('Bạn có chắc chắn muốn xóa loại máy bay này không?');">Xóa</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="text-center">Chưa có dữ liệu</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/main.php';
