<?php
require_once __DIR__ . '/_bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'cap_nhat_ticket') {
    $baoTriController->capNhatTrangThaiTicket();
}

if (!isset($tickets, $thong_ke)) {
    $data     = $baoTriController->getIndexData();
    $tickets  = $data['tickets'];
    $thong_ke = $data['thong_ke'];
}

$pageTitle  = 'Bảo trì & khắc phục';
$pageHeader = 'Bảo trì & khắc phục';
$navActive  = 'bao_tri';
$breadcrumb = [
    ['label' => 'Tổng quan', 'url' => BASE_URL . '/index.php'],
    ['label' => 'Bảo trì', 'url' => null],
];

$uploadBase = BASE_URL . '/public/uploads/bao_tri/';

$statusColors = [
    'Moi'        => 'info',
    'Dang_Xu_Ly' => 'warning',
    'Hoan_Thanh' => 'success',
];

$statusLabels = [
    'Moi'        => 'Mới',
    'Dang_Xu_Ly' => 'Đang xử lý',
    'Hoan_Thanh' => 'Hoàn thành',
];

$priorityColors = [
    'Thap'       => 'secondary',
    'Trung_Binh' => 'primary',
    'Khan_Cap'   => 'danger',
];

$priorityLabels = [
    'Thap'       => 'Thấp',
    'Trung_Binh' => 'Trung bình',
    'Khan_Cap'   => 'Khẩn cấp',
];

// Giá trị mặc định phòng khi view được nạp trực tiếp
$thong_ke = array_merge([
    'dang_mo'          => 0,
    'khan_cap'         => 0,
    'hoan_thanh_thang' => 0,
    'thiet_bi_bao_tri' => 0,
], $thong_ke ?? []);

ob_start();
?>
<?php require __DIR__ . '/_flash.php'; ?>
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box text-bg-primary">
            <div class="inner"><h3><?= (int) $thong_ke['dang_mo'] ?></h3><p>Phiếu đang mở</p></div>
            <i class="small-box-icon bi bi-clipboard-check"></i>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box text-bg-danger">
            <div class="inner"><h3><?= (int) $thong_ke['khan_cap'] ?></h3><p>Sự cố khẩn cấp</p></div>
            <i class="small-box-icon bi bi-exclamation-octagon"></i>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box text-bg-success">
            <div class="inner"><h3><?= (int) $thong_ke['hoan_thanh_thang'] ?></h3><p>Hoàn thành trong tháng</p></div>
            <i class="small-box-icon bi bi-check2-circle"></i>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box text-bg-info">
            <div class="inner"><h3><?= (int) $thong_ke['thiet_bi_bao_tri'] ?></h3><p>Thiết bị đang bảo trì</p></div>
            <i class="small-box-icon bi bi-tools"></i>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Danh sách phiếu bảo trì</h3>
                <div class="card-tools d-flex flex-wrap gap-1">
                    <a href="<?= baoTriUrl('thiet_bi.php') ?>" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-hdd-stack me-1"></i> Trang thiết bị
                    </a>
                    <a href="<?= baoTriUrl('tau_bay.php') ?>" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-airplane me-1"></i> Loại tàu bay
                    </a>
                    <a href="<?= baoTriUrl('create_ticket.php') ?>" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-lg me-1"></i> Tạo phiếu bảo trì
                    </a>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Mã phiếu</th>
                            <th>Thiết bị sự cố</th>
                            <th>Mô tả sự cố</th>
                            <th>Ảnh</th>
                            <th>Ngày tạo</th>
                            <th>Mức độ</th>
                            <th>Trạng thái</th>
                            <th class="text-end">Cập nhật trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($tickets)): ?>
                            <?php foreach ($tickets as $ticket): ?>
                                <tr>
                                    <td><strong>#BT-<?= (int) $ticket['id'] ?></strong></td>
                                    <td><?= htmlspecialchars($ticket['ten_thiet_bi'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td style="max-width: 300px; white-space: normal; word-break: break-word;">
                                        <?= htmlspecialchars($ticket['mo_ta_su_co'], ENT_QUOTES, 'UTF-8') ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($ticket['hinh_anh_minh_hoa'])): ?>
                                            <?php $anhUrl = $uploadBase . rawurlencode($ticket['hinh_anh_minh_hoa']); ?>
                                            <a href="<?= $anhUrl ?>" target="_blank" rel="noopener">
                                                <img src="<?= $anhUrl ?>" alt="Ảnh sự cố"
                                                     class="rounded border"
                                                     style="width: 56px; height: 56px; object-fit: cover;">
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= date('d/m/Y H:i', strtotime($ticket['ngay_tao'])) ?></td>
                                    <td>
                                        <span class="badge text-bg-<?= $priorityColors[$ticket['muc_do']] ?? 'secondary' ?>">
                                            <?= $priorityLabels[$ticket['muc_do']] ?? htmlspecialchars($ticket['muc_do'], ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge text-bg-<?= $statusColors[$ticket['trang_thai_xu_ly']] ?? 'secondary' ?>">
                                            <?= $statusLabels[$ticket['trang_thai_xu_ly']] ?? htmlspecialchars($ticket['trang_thai_xu_ly'], ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <form action="<?= baoTriUrl('index.php') ?>" method="POST" class="d-flex justify-content-end align-items-center">
                                            <input type="hidden" name="action" value="cap_nhat_ticket">
                                            <input type="hidden" name="id_ticket" value="<?= (int) $ticket['id'] ?>">
                                            <select name="trang_thai_xu_ly" class="form-select form-select-sm w-auto me-1" <?= $ticket['trang_thai_xu_ly'] === 'Hoan_Thanh' ? 'disabled' : '' ?>>
                                                <option value="Moi" <?= $ticket['trang_thai_xu_ly'] === 'Moi' ? 'selected' : '' ?>>Mới</option>
                                                <option value="Dang_Xu_Ly" <?= $ticket['trang_thai_xu_ly'] === 'Dang_Xu_Ly' ? 'selected' : '' ?>>Đang xử lý</option>
                                                <option value="Hoan_Thanh" <?= $ticket['trang_thai_xu_ly'] === 'Hoan_Thanh' ? 'selected' : '' ?>>Hoàn thành</option>
                                            </select>
                                            <?php if ($ticket['trang_thai_xu_ly'] !== 'Hoan_Thanh'): ?>
                                                <button type="submit" class="btn btn-sm btn-success">Lưu</button>
                                            <?php endif; ?>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="8" class="text-center text-muted">Chưa có yêu cầu bảo trì nào.</td></tr>
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
