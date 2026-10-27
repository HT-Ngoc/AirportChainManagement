<?php
require_once __DIR__ . '/_bootstrap.php';

/* Xóa chỉ chạy khi form xác nhận được gửi (POST), không xóa bằng link GET */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'xoa_tau_bay') {
    $baoTriController->xoaTauBay();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') !== 'xoa_tau_bay') {
    $baoTriController->themTauBay();
}

if (!isset($tau_bay)) {
    $tau_bay = $baoTriController->getDanhSachTauBay();
}

// Tự mở lại modal khi vừa có lỗi nhập liệu
$modalError = null;
if (isset($_GET['open']) && $_GET['open'] === 'create' && !empty($_SESSION['flash_error'])) {
    $modalError = (string) $_SESSION['flash_error'];
    unset($_SESSION['flash_error']);
}

$pageTitle  = 'Quản lý Loại tàu bay';
$pageHeader = 'Danh mục Loại tàu bay (MTOW)';
$navActive    = 'bao_tri';
$navSubActive = 'aircraft_types';
$breadcrumb = [
    ['label' => 'Tổng quan', 'url' => BASE_URL . '/index.php'],
    ['label' => 'Bảo trì', 'url' => baoTriUrl('index.php')],
    ['label' => 'Loại tàu bay', 'url' => null],
];

$tongLoaiTauBay = count($tau_bay);
$sucChuaLonNhat = 0;
$tongMtow       = 0.0;
$mtowLonNhat    = 0.0;

foreach ($tau_bay as $tb) {
    $sucChua = (int) $tb['suc_chua_hanh_khach'];
    $mtow    = (float) $tb['tai_trong_cat_canh_mtow'];

    if ($sucChua > $sucChuaLonNhat) {
        $sucChuaLonNhat = $sucChua;
    }
    if ($mtow > $mtowLonNhat) {
        $mtowLonNhat = $mtow;
    }

    $tongMtow += $mtow;
}

$mtowTrungBinh = $tongLoaiTauBay > 0 ? $tongMtow / $tongLoaiTauBay : 0.0;

/* ------------------------------------------------------------
 * Lọc danh sách phía máy chủ (thay cho bộ lọc JavaScript cũ).
 * Tham số: ?q=... — form GET bên dưới.
 * ------------------------------------------------------------ */
$tuKhoa          = trim((string) ($_GET['q'] ?? ''));
$tongLoaiTauBayGoc = $tongLoaiTauBay;

if ($tuKhoa !== '') {
    $tau_bay = array_values(array_filter($tau_bay, static function (array $tb) use ($tuKhoa): bool {
        $maDayDu = 'LTB-' . sprintf('%03d', (int) $tb['id']);
        return mb_stripos((string) $tb['ma_loai'], $tuKhoa, 0, 'UTF-8') !== false
            || mb_stripos($maDayDu, $tuKhoa, 0, 'UTF-8') !== false;
    }));
}

$soLuongHienThi = count($tau_bay);
$urlDongModal   = baoTriUrl('tau_bay.php', $tuKhoa !== '' ? ['q' => $tuKhoa] : []);

/* ------------------------------------------------------------
 * Xác nhận xóa (thuần PHP + CSS, không dùng confirm() của JS):
 * link Xóa trỏ tới ?xac_nhan_xoa=<id>; trang hiện hộp thoại,
 * nút "Xóa" trong hộp thoại mới gửi form POST thật.
 * ------------------------------------------------------------ */
$idCanXoa     = (int) ($_GET['xac_nhan_xoa'] ?? 0);
$tauBayCanXoa = null;
if ($idCanXoa > 0) {
    foreach ($baoTriController->getDanhSachTauBay() as $tbItem) {
        if ((int) $tbItem['id'] === $idCanXoa) {
            $tauBayCanXoa = $tbItem;
            break;
        }
    }
}

ob_start();
?>
<?php require __DIR__ . '/_flash.php'; ?>
<style>
    .aircraft-stat-card { transition: transform .15s ease, box-shadow .15s ease; }
    .aircraft-stat-card:hover { transform: translateY(-2px); box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .08) !important; }
    .aircraft-stat-icon { width: 46px; height: 46px; flex: 0 0 46px; font-size: 1.3rem; }
    .aircraft-stat-label { font-size: .7rem; letter-spacing: .04em; }
    #themTauBayModal .modal-dialog { max-width: 620px; }
    #themTauBayModal .modal-content { border: 0; border-radius: .75rem; overflow: hidden; }
    #themTauBayModal .modal-header { background: linear-gradient(135deg, rgba(25, 135, 84, .12), rgba(25, 135, 84, .03)); }
    #themTauBayModal .modal-icon { width: 38px; height: 38px; flex: 0 0 38px; }
    #themTauBayModal .form-label { font-size: .8125rem; }
    @media (max-width: 575.98px) {
        .aircraft-stat-icon { width: 38px; height: 38px; flex-basis: 38px; font-size: 1.05rem; }
        .aircraft-stat-label { font-size: .63rem; }
        .aircraft-stat-card .card-body { padding: .75rem; }
        .aircraft-stat-card h3 { font-size: 1.4rem; }
    }
</style>

<div class="row g-3 mb-3">
    <div class="col-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100 aircraft-stat-card">
            <div class="card-body d-flex align-items-center gap-2 gap-sm-3">
                <span class="aircraft-stat-icon rounded-3 bg-primary-subtle text-primary-emphasis d-inline-flex align-items-center justify-content-center">
                    <i class="bi bi-airplane"></i>
                </span>
                <div style="min-width: 0;">
                    <div class="aircraft-stat-label text-secondary text-uppercase fw-semibold">Tổng loại tàu bay</div>
                    <h3 class="mb-0 fw-bold"><?= $tongLoaiTauBay ?></h3>
                    <div class="small text-secondary text-truncate d-none d-sm-block">Đang quản lý</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100 aircraft-stat-card">
            <div class="card-body d-flex align-items-center gap-2 gap-sm-3">
                <span class="aircraft-stat-icon rounded-3 bg-success-subtle text-success-emphasis d-inline-flex align-items-center justify-content-center">
                    <i class="bi bi-people"></i>
                </span>
                <div style="min-width: 0;">
                    <div class="aircraft-stat-label text-secondary text-uppercase fw-semibold">Sức chứa lớn nhất</div>
                    <h3 class="mb-0 fw-bold">
                        <?= number_format($sucChuaLonNhat) ?>
                        <span class="fs-6 fw-normal text-secondary">khách</span>
                    </h3>
                    <div class="small text-secondary text-truncate d-none d-sm-block">Số ghế tối đa</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100 aircraft-stat-card">
            <div class="card-body d-flex align-items-center gap-2 gap-sm-3">
                <span class="aircraft-stat-icon rounded-3 bg-warning-subtle text-warning-emphasis d-inline-flex align-items-center justify-content-center">
                    <i class="bi bi-speedometer2"></i>
                </span>
                <div style="min-width: 0;">
                    <div class="aircraft-stat-label text-secondary text-uppercase fw-semibold">MTOW trung bình</div>
                    <h3 class="mb-0 fw-bold">
                        <?= number_format($mtowTrungBinh, 2) ?>
                        <span class="fs-6 fw-normal text-secondary">tấn</span>
                    </h3>
                    <div class="small text-secondary text-truncate d-none d-sm-block">Trung bình danh mục</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100 aircraft-stat-card">
            <div class="card-body d-flex align-items-center gap-2 gap-sm-3">
                <span class="aircraft-stat-icon rounded-3 bg-info-subtle text-info-emphasis d-inline-flex align-items-center justify-content-center">
                    <i class="bi bi-arrow-up-circle"></i>
                </span>
                <div style="min-width: 0;">
                    <div class="aircraft-stat-label text-secondary text-uppercase fw-semibold">MTOW lớn nhất</div>
                    <h3 class="mb-0 fw-bold">
                        <?= number_format($mtowLonNhat, 2) ?>
                        <span class="fs-6 fw-normal text-secondary">tấn</span>
                    </h3>
                    <div class="small text-secondary text-truncate d-none d-sm-block">Cấu hình lớn nhất</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-body-tertiary border-0 d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div class="flex-grow-1">
            <h3 class="card-title mb-0 float-none">
                <i class="bi bi-airplane me-2 text-primary"></i>Danh sách loại tàu bay
                <span class="badge bg-body-tertiary text-body border ms-1 align-middle fw-normal" title="Tổng số loại tàu bay"><?= $tongLoaiTauBayGoc ?></span>
            </h3>
            <div class="small text-secondary mt-1">Danh mục dòng máy bay và thông số khai thác</div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="#themTauBayModal" class="btn btn-sm btn-success">
                <i class="bi bi-plus-lg me-1"></i>Thêm loại tàu bay
            </a>
        </div>
    </div>

    <?php if ($tongLoaiTauBayGoc > 0): ?>
        <div class="card-body pb-2">
            <form method="get" action="<?= htmlspecialchars(baoTriUrl('tau_bay.php'), ENT_QUOTES, 'UTF-8') ?>" class="row g-2 align-items-end">
                <div class="col-12 col-lg-8">
                    <label for="aircraftSearch" class="form-label small text-secondary mb-1">Tìm kiếm</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-body-tertiary"><i class="bi bi-search" aria-hidden="true"></i></span>
                        <input type="search" id="aircraftSearch" name="q" value="<?= htmlspecialchars($tuKhoa, ENT_QUOTES, 'UTF-8') ?>" class="form-control" placeholder="Tên dòng máy bay hoặc mã LTB...">
                    </div>
                </div>
                <div class="col-12 col-lg-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 small text-secondary">
                        <span>Hiển thị <strong><?= $soLuongHienThi ?></strong> / <strong><?= $tongLoaiTauBayGoc ?></strong> loại</span>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-sm btn-primary">Lọc</button>
                            <a href="<?= htmlspecialchars(baoTriUrl('tau_bay.php'), ENT_QUOTES, 'UTF-8') ?>" class="btn btn-sm btn-outline-secondary">Xóa lọc</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-hover table-sm align-middle mb-0" id="aircraftTable">
            <thead>
                <tr class="text-uppercase small text-secondary">
                    <th class="ps-3">Loại tàu bay</th>
                    <th>MTOW</th>
                    <th>Sức chứa</th>
                    <th class="text-end pe-3">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($tau_bay)): ?>
                    <?php foreach ($tau_bay as $tb): ?>
                        <?php
                        $maLoaiTauBay = 'LTB-' . sprintf('%03d', (int) $tb['id']);
                        $mtow         = (float) $tb['tai_trong_cat_canh_mtow'];
                        $sucChua      = (int) $tb['suc_chua_hanh_khach'];
                        ?>
                        <tr>
                            <td class="ps-3" style="min-width: 240px; white-space: normal;">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="d-inline-flex align-items-center justify-content-center bg-body-secondary border" style="width: 36px; height: 36px; flex: 0 0 36px;">
                                        <i class="bi bi-airplane" aria-hidden="true"></i>
                                    </span>
                                    <div>
                                        <div class="fw-semibold"><?= htmlspecialchars($tb['ma_loai'], ENT_QUOTES, 'UTF-8') ?></div>
                                        <div class="small text-secondary"><?= $maLoaiTauBay ?></div>
                                    </div>
                                </div>
                            </td>
                            <td style="min-width: 130px; white-space: nowrap;">
                                <span class="badge border text-primary-emphasis bg-primary-subtle border-primary-subtle">
                                    <i class="bi bi-speedometer2 me-1"></i><?= number_format($mtow, 2) ?> tấn
                                </span>
                            </td>
                            <td style="min-width: 130px; white-space: nowrap;">
                                <span class="badge border text-success-emphasis bg-success-subtle border-success-subtle">
                                    <i class="bi bi-people me-1"></i><?= number_format($sucChua) ?> khách
                                </span>
                            </td>
                            <td class="text-end pe-3 text-nowrap">
                                <?php
                                $xoaUrl = baoTriUrl('tau_bay.php', array_filter([
                                    'q'            => $tuKhoa,
                                    'xac_nhan_xoa' => (int) $tb['id'],
                                ], static fn($v) => $v !== ''));
                                ?>
                                <a href="<?= htmlspecialchars($xoaUrl, ENT_QUOTES, 'UTF-8') ?>"
                                   class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash3 me-1"></i>Xóa
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php elseif ($tuKhoa !== ''): ?>
                    <tr>
                        <td colspan="4" class="text-center py-4 text-secondary">
                            Không tìm thấy loại tàu bay phù hợp với “<?= htmlspecialchars($tuKhoa, ENT_QUOTES, 'UTF-8') ?>”.
                        </td>
                    </tr>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center py-5">
                            <div class="text-secondary mb-2"><i class="bi bi-airplane fs-1"></i></div>
                            <div class="fw-semibold">Chưa có loại tàu bay nào</div>
                            <div class="small text-secondary mb-3">Thêm loại tàu bay đầu tiên để bắt đầu quản lý.</div>
                            <a href="#themTauBayModal" class="btn btn-sm btn-success"><i class="bi bi-plus-lg me-1"></i>Thêm loại tàu bay</a>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!--begin::Modal thêm loại tàu bay-->
<div class="modal fade<?= $modalError !== null ? ' is-open' : '' ?>" id="themTauBayModal" tabindex="-1" aria-labelledby="themTauBayModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content shadow-lg">
            <form action="<?= htmlspecialchars(baoTriUrl('tau_bay.php'), ENT_QUOTES, 'UTF-8') ?>" method="POST">
                <div class="modal-header border-0 px-3 py-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="modal-icon d-inline-flex align-items-center justify-content-center rounded-circle bg-success text-white shadow-sm">
                            <i class="bi bi-airplane"></i>
                        </span>
                        <div>
                            <h5 class="modal-title mb-0 fs-5" id="themTauBayModalLabel">Thêm loại tàu bay</h5>
                            <div class="small text-secondary">Khai báo dòng máy bay và thông số khai thác</div>
                        </div>
                    </div>
                    <a href="<?= htmlspecialchars($urlDongModal, ENT_QUOTES, 'UTF-8') ?>" class="acv-close" aria-label="Đóng">&times;</a>
                </div>

                <div class="modal-body px-3 py-3">
                    <?php if ($modalError !== null): ?>
                        <div class="alert alert-danger d-flex align-items-start gap-2 py-2 mb-3" role="alert">
                            <i class="bi bi-exclamation-circle-fill mt-1"></i>
                            <div class="small"><?= htmlspecialchars($modalError, ENT_QUOTES, 'UTF-8') ?></div>
                        </div>
                    <?php endif; ?>

                    <div class="mb-2">
                        <label for="ma_loai" class="form-label fw-semibold">
                            Mã loại / tên dòng máy bay <span class="text-danger">*</span>
                        </label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-body-tertiary"><i class="bi bi-airplane"></i></span>
                            <input type="text" name="ma_loai" id="ma_loai" class="form-control"
                                   maxlength="50" placeholder="Ví dụ: Boeing 787" required>
                        </div>
                        <div class="form-text">Tên này dùng để nhận diện loại tàu bay trong danh mục.</div>
                    </div>

                    <div class="row g-2">
                        <div class="col-sm-6">
                            <label for="tai_trong_cat_canh_mtow" class="form-label fw-semibold">
                                MTOW (tấn) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-body-tertiary"><i class="bi bi-speedometer2"></i></span>
                                <input type="number" step="0.01" min="0.01" name="tai_trong_cat_canh_mtow"
                                       id="tai_trong_cat_canh_mtow" class="form-control" placeholder="Tấn" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label for="suc_chua_hanh_khach" class="form-label fw-semibold">
                                Sức chứa (khách) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-body-tertiary"><i class="bi bi-people"></i></span>
                                <input type="number" min="1" name="suc_chua_hanh_khach"
                                       id="suc_chua_hanh_khach" class="form-control" placeholder="Số khách" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 bg-body-tertiary d-flex flex-wrap justify-content-between gap-2 px-3 py-2">
                    <div class="small text-secondary d-none d-sm-block">
                        <i class="bi bi-info-circle me-1"></i> Thông số dùng để tính toán khai thác.
                    </div>
                    <div class="d-flex gap-2">
                        <a href="<?= htmlspecialchars($urlDongModal, ENT_QUOTES, 'UTF-8') ?>" class="btn btn-sm btn-light border px-3">Hủy bỏ</a>
                        <button type="submit" class="btn btn-sm btn-success px-3">
                            <i class="bi bi-check2 me-1"></i> Lưu loại tàu bay
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!--end::Modal thêm loại tàu bay-->

<?php if ($tauBayCanXoa !== null): ?>
<!-- Modal xác nhận xóa (thuần PHP + CSS, không dùng JS) -->
<div class="modal fade is-open" id="xoaTauBayModal" tabindex="-1" aria-labelledby="xoaTauBayModalLabel" aria-hidden="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-body text-center p-4">
                <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-danger-subtle text-danger mb-3" style="width: 58px; height: 58px;">
                    <i class="bi bi-trash3 fs-4"></i>
                </span>
                <h5 class="mb-2" id="xoaTauBayModalLabel">Xóa loại tàu bay?</h5>
                <p class="text-secondary small mb-0">
                    Bạn có chắc muốn xóa <strong><?= htmlspecialchars($tauBayCanXoa['ma_loai'], ENT_QUOTES, 'UTF-8') ?></strong>?
                    Hành động này không thể hoàn tác.
                </p>
            </div>
            <div class="modal-footer border-0 justify-content-center pt-0 pb-4">
                <a href="<?= htmlspecialchars($urlDongModal, ENT_QUOTES, 'UTF-8') ?>" class="btn btn-light border px-3">Hủy</a>
                <form method="post" action="<?= htmlspecialchars(baoTriUrl('tau_bay.php', ['id' => $idCanXoa]), ENT_QUOTES, 'UTF-8') ?>" class="d-inline">
                    <input type="hidden" name="action" value="xoa_tau_bay" />
                    <button type="submit" class="btn btn-danger px-3">
                        <i class="bi bi-trash3 me-1"></i>Xóa loại tàu bay
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<!--end::Modal xác nhận xóa-->
<?php endif; ?>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/main.php';
