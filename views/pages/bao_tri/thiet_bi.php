<?php
require_once __DIR__ . '/_bootstrap.php';

if (isset($_GET['action']) && $_GET['action'] === 'xoa') {
    $baoTriController->xoaThietBi();
}

if (!isset($thiet_bi)) {
    $thiet_bi = $baoTriController->getDanhSachThietBi();
}

$pageTitle  = 'Quản lý Thiết bị Sân bay';
$pageHeader = 'Danh mục Trang thiết bị';
$navActive  = 'bao_tri';
$breadcrumb = [
    ['label' => 'Tổng quan', 'url' => BASE_URL . '/index.php'],
    ['label' => 'Bảo trì', 'url' => baoTriUrl('index.php')],
    ['label' => 'Trang thiết bị', 'url' => null],
];

$statusBadge = [
    'Tot'     => 'success',
    'Bao_Tri' => 'warning',
    'Hong'    => 'danger',
];

$statusLabels = [
    'Tot'     => 'Tốt',
    'Bao_Tri' => 'Đang bảo trì',
    'Hong'    => 'Hỏng',
];

$loaiThietBiLabels = [
    'Den_Tin_Hieu'       => 'Đèn tín hiệu',
    'Thiet_Bi_Dan_Duong' => 'Thiết bị dẫn đường',
    'Xe_Thang'           => 'Xe thang',
    'Xe_Keo'             => 'Xe kéo',
    'Khac'               => 'Khác',
];

ob_start();
?>
<?php require __DIR__ . '/_flash.php'; ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Danh sách thiết bị toàn mạng lưới</h3>
                <div class="card-tools">
                    <a href="<?= baoTriUrl('them_thiet_bi.php') ?>" class="btn btn-sm btn-success"><i class="bi bi-plus"></i> Thêm thiết bị</a>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap align-middle">
                    <thead>
                        <tr>
                            <th>Mã TB</th>
                            <th>Tên thiết bị</th>
                            <th>Phân loại</th>
                            <th>Sân bay quản lý</th>
                            <th>Trạng thái</th>
                            <th>Phiếu BT</th>
                            <th class="text-end">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($thiet_bi)): ?>
                            <?php foreach ($thiet_bi as $item): ?>
                                <tr>
                                    <td>TB-<?= sprintf('%03d', (int) $item['id']) ?></td>
                                    <td><?= htmlspecialchars($item['ten_thiet_bi'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= $loaiThietBiLabels[$item['loai_thiet_bi']] ?? htmlspecialchars(str_replace('_', ' ', $item['loai_thiet_bi']), ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($item['ten_san_bay'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td>
                                        <span class="badge text-bg-<?= $statusBadge[$item['trang_thai']] ?? 'secondary' ?>">
                                            <?= $statusLabels[$item['trang_thai']] ?? htmlspecialchars($item['trang_thai'], ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ((int) $item['so_phieu'] > 0): ?>
                                            <span class="badge text-bg-secondary"><?= (int) $item['so_phieu'] ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">0</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <?php if ((int) $item['so_phieu'] > 0): ?>
                                            <span class="btn btn-sm btn-outline-danger disabled"
                                                  title="Không thể xóa vì thiết bị đang có phiếu bảo trì">Xóa</span>
                                        <?php else: ?>
                                            <a href="<?= baoTriUrl('thiet_bi.php', ['action' => 'xoa', 'id' => (int) $item['id']]) ?>"
                                               class="btn btn-sm btn-outline-danger"
                                               onclick="return confirm('Bạn có chắc chắn muốn xóa thiết bị này không?');">Xóa</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="7" class="text-center">Chưa có dữ liệu thiết bị</td></tr>
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
