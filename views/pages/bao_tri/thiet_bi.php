<?php
require_once __DIR__ . '/_bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'store_thiet_bi') {
    $baoTriController->storeThietBi();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'xoa') {
    $baoTriController->xoaThietBi();
}

if (!isset($thiet_bi)) {
    $thiet_bi = $baoTriController->getDanhSachThietBi();
}

if (!isset($san_bay_list)) {
    $san_bay_list = $baoTriController->getSanBayList();
}

// Tự mở lại modal khi vừa có lỗi nhập liệu
$modalError = null;
if (isset($_GET['open']) && $_GET['open'] === 'create' && !empty($_SESSION['flash_error'])) {
    $modalError = (string) $_SESSION['flash_error'];
    unset($_SESSION['flash_error']);
}

$pageTitle  = 'Quản lý Thiết bị Sân bay';
$pageHeader = 'Danh mục Trang thiết bị';
$navActive    = 'bao_tri';
$navSubActive = 'equipment';
$breadcrumb = [
    ['label' => 'Tổng quan', 'url' => BASE_URL . '/index.php'],
    ['label' => 'Bảo trì', 'url' => baoTriUrl('index.php')],
    ['label' => 'Trang thiết bị', 'url' => null],
];

$statusLabels = [
    'Tot'     => 'Tốt',
    'Bao_Tri' => 'Đang bảo trì',
    'Hong'    => 'Hỏng',
];

$statusBadgeClasses = [
    'Tot'     => 'text-success-emphasis bg-success-subtle border-success-subtle',
    'Bao_Tri' => 'text-warning-emphasis bg-warning-subtle border-warning-subtle',
    'Hong'    => 'text-danger-emphasis bg-danger-subtle border-danger-subtle',
];

$statusDotClasses = [
    'Tot'     => 'bg-success',
    'Bao_Tri' => 'bg-warning',
    'Hong'    => 'bg-danger',
];

$loaiThietBiLabels = [
    'Den_Tin_Hieu'       => 'Đèn tín hiệu',
    'Thiet_Bi_Dan_Duong' => 'Thiết bị dẫn đường',
    'Xe_Thang'           => 'Xe thang',
    'Xe_Keo'             => 'Xe kéo',
    'Khac'               => 'Khác',
];

$loaiThietBiIcons = [
    'Den_Tin_Hieu'       => 'bi-lightbulb',
    'Thiet_Bi_Dan_Duong' => 'bi-broadcast-pin',
    'Xe_Thang'           => 'bi-truck',
    'Xe_Keo'             => 'bi-truck',
    'Khac'               => 'bi-box-seam',
];

$tongThietBi     = count($thiet_bi);
$soThietBiTot    = 0;
$soThietBiBaoTri = 0;
$soThietBiHong   = 0;

foreach ($thiet_bi as $tb) {
    if ($tb['trang_thai'] === 'Tot') {
        $soThietBiTot++;
    } elseif ($tb['trang_thai'] === 'Bao_Tri') {
        $soThietBiBaoTri++;
    } elseif ($tb['trang_thai'] === 'Hong') {
        $soThietBiHong++;
    }
}

$dsSanBay = [];
$dsLoai   = [];
foreach ($thiet_bi as $tb) {
    $dsSanBay[$tb['ten_san_bay']] = true;
    $dsLoai[$tb['loai_thiet_bi']] = true;
}
$dsSanBay = array_keys($dsSanBay);
sort($dsSanBay);

$dsLoai = array_keys($dsLoai);
usort($dsLoai, function ($a, $b) use ($loaiThietBiLabels) {
    return strcmp($loaiThietBiLabels[$a] ?? $a, $loaiThietBiLabels[$b] ?? $b);
});

$pageScripts = <<<'HTML'
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Modal thêm thiết bị
    var addModalElement = document.getElementById('themThietBiModal');
    if (addModalElement) {
        if (window.bootstrap && new URLSearchParams(window.location.search).get('open') === 'create') {
            bootstrap.Modal.getOrCreateInstance(addModalElement).show();
        }

        addModalElement.addEventListener('shown.bs.modal', function () {
            var nameInput = document.getElementById('them_ten_thiet_bi');
            if (nameInput) nameInput.focus();
        });

        addModalElement.addEventListener('hidden.bs.modal', function () {
            var url = new URL(window.location.href);
            if (url.searchParams.get('open') === 'create') {
                url.searchParams.delete('open');
                window.history.replaceState({}, '', url.toString());
            }
        });
    }

    // Bộ lọc thiết bị
    var searchInput = document.getElementById('filterSearch');
    var sanBaySelect = document.getElementById('filterSanBay');
    var loaiSelect = document.getElementById('filterLoai');
    var trangThaiSelect = document.getElementById('filterTrangThai');
    var resetButton = document.getElementById('resetFilters');
    var table = document.getElementById('thietBiTable');
    var visibleCount = document.getElementById('visibleCount');
    var noResultRow = document.getElementById('noResultRow');

    function normalizeText(value) {
        return (value || '').toString().toLocaleLowerCase('vi')
            .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
            .replace(/đ/g, 'd');
    }

    if (table && searchInput && sanBaySelect && loaiSelect && trangThaiSelect) {
        var rows = Array.prototype.slice.call(table.querySelectorAll('tbody tr[data-search]'));

        function applyFilters() {
            var keyword = normalizeText(searchInput.value.trim());
            var sanBay = sanBaySelect.value;
            var loai = loaiSelect.value;
            var trangThai = trangThaiSelect.value;
            var visible = 0;

            rows.forEach(function (row) {
                var matchKeyword = !keyword || normalizeText(row.getAttribute('data-search')).indexOf(keyword) !== -1;
                var matchSanBay = !sanBay || row.getAttribute('data-san-bay') === sanBay;
                var matchLoai = !loai || row.getAttribute('data-loai') === loai;
                var matchTrangThai = !trangThai || row.getAttribute('data-trang-thai') === trangThai;
                var show = matchKeyword && matchSanBay && matchLoai && matchTrangThai;

                row.classList.toggle('d-none', !show);
                if (show) visible++;
            });

            if (visibleCount) visibleCount.textContent = visible;
            if (noResultRow) noResultRow.classList.toggle('d-none', visible !== 0 || rows.length === 0);
        }

        [searchInput, sanBaySelect, loaiSelect, trangThaiSelect].forEach(function (element) {
            element.addEventListener('input', applyFilters);
            element.addEventListener('change', applyFilters);
        });

        if (resetButton) {
            resetButton.addEventListener('click', function () {
                searchInput.value = '';
                sanBaySelect.value = '';
                loaiSelect.value = '';
                trangThaiSelect.value = '';
                applyFilters();
                searchInput.focus();
            });
        }

        applyFilters();
    }

    // Modal xác nhận xóa
    var deleteModal = document.getElementById('xoaThietBiModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function (event) {
            var trigger = event.relatedTarget;
            if (!trigger) return;

            var deleteLink = document.getElementById('xoaThietBiLink');
            var deviceName = document.getElementById('xoaTenThietBi');
            var url = trigger.getAttribute('data-delete-url');
            var name = trigger.getAttribute('data-device-name');

            if (deleteLink && url) deleteLink.setAttribute('href', url);
            if (deviceName) deviceName.textContent = name || 'thiết bị này';
        });
    }
});
</script>
HTML;

ob_start();
?>
<?php require __DIR__ . '/_flash.php'; ?>
<style>
    .equip-stat-card { transition: transform .15s ease, box-shadow .15s ease; }
    .equip-stat-card:hover { transform: translateY(-2px); box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .08) !important; }
    .equip-stat-icon { width: 46px; height: 46px; flex: 0 0 46px; font-size: 1.3rem; }
    .equip-stat-label { font-size: .7rem; letter-spacing: .04em; }
    @media (max-width: 575.98px) {
        .equip-stat-icon { width: 38px; height: 38px; flex-basis: 38px; font-size: 1.05rem; }
        .equip-stat-label { font-size: .63rem; }
        .equip-stat-card .card-body { padding: .75rem; }
        .equip-stat-card h3 { font-size: 1.4rem; }
    }
</style>

<div class="row g-3 mb-3">
    <div class="col-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100 equip-stat-card">
            <div class="card-body d-flex align-items-center gap-2 gap-sm-3">
                <span class="equip-stat-icon rounded-3 bg-primary-subtle text-primary-emphasis d-inline-flex align-items-center justify-content-center">
                    <i class="bi bi-hdd-stack"></i>
                </span>
                <div style="min-width: 0;">
                    <div class="equip-stat-label text-secondary text-uppercase fw-semibold">Tổng thiết bị</div>
                    <h3 class="mb-0 fw-bold"><?= $tongThietBi ?></h3>
                    <div class="small text-secondary text-truncate d-none d-sm-block">Toàn bộ danh mục</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100 equip-stat-card">
            <div class="card-body d-flex align-items-center gap-2 gap-sm-3">
                <span class="equip-stat-icon rounded-3 bg-success-subtle text-success-emphasis d-inline-flex align-items-center justify-content-center">
                    <i class="bi bi-check2-circle"></i>
                </span>
                <div style="min-width: 0;">
                    <div class="equip-stat-label text-secondary text-uppercase fw-semibold">Đang hoạt động</div>
                    <h3 class="mb-0 fw-bold"><?= $soThietBiTot ?></h3>
                    <div class="small text-secondary text-truncate d-none d-sm-block">Sẵn sàng khai thác</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100 equip-stat-card">
            <div class="card-body d-flex align-items-center gap-2 gap-sm-3">
                <span class="equip-stat-icon rounded-3 bg-warning-subtle text-warning-emphasis d-inline-flex align-items-center justify-content-center">
                    <i class="bi bi-tools"></i>
                </span>
                <div style="min-width: 0;">
                    <div class="equip-stat-label text-secondary text-uppercase fw-semibold">Đang bảo trì</div>
                    <h3 class="mb-0 fw-bold"><?= $soThietBiBaoTri ?></h3>
                    <div class="small text-secondary text-truncate d-none d-sm-block">Cần theo dõi</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100 equip-stat-card">
            <div class="card-body d-flex align-items-center gap-2 gap-sm-3">
                <span class="equip-stat-icon rounded-3 bg-danger-subtle text-danger-emphasis d-inline-flex align-items-center justify-content-center">
                    <i class="bi bi-exclamation-triangle"></i>
                </span>
                <div style="min-width: 0;">
                    <div class="equip-stat-label text-secondary text-uppercase fw-semibold">Đang hỏng</div>
                    <h3 class="mb-0 fw-bold"><?= $soThietBiHong ?></h3>
                    <div class="small text-secondary text-truncate d-none d-sm-block">Cần xử lý</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-body-tertiary border-0 d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div class="flex-grow-1">
                    <h3 class="card-title mb-0 float-none"><i class="bi bi-hdd-stack me-2 text-primary"></i>Danh sách thiết bị</h3>
                    <div class="small text-secondary mt-1">Quản lý trang thiết bị mặt đất trên toàn mạng lưới</div>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-sm btn-success"
                            data-bs-toggle="modal" data-bs-target="#themThietBiModal">
                        <i class="bi bi-plus-lg me-1"></i>Thêm thiết bị
                    </button>
                </div>
            </div>

            <?php if (!empty($thiet_bi)): ?>
                <div class="card-body pb-2">
                    <div class="row g-2">
                        <div class="col-12 col-lg-4">
                            <label for="filterSearch" class="form-label small text-secondary mb-1">Tìm kiếm</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-body-tertiary"><i class="bi bi-search"></i></span>
                                <input type="search" id="filterSearch" class="form-control" placeholder="Tên thiết bị hoặc mã TB...">
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <label for="filterSanBay" class="form-label small text-secondary mb-1">Sân bay</label>
                            <select id="filterSanBay" class="form-select form-select-sm">
                                <option value="">Tất cả sân bay</option>
                                <?php foreach ($dsSanBay as $tenSanBay): ?>
                                    <option value="<?= htmlspecialchars($tenSanBay, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($tenSanBay, ENT_QUOTES, 'UTF-8') ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6 col-lg-2">
                            <label for="filterLoai" class="form-label small text-secondary mb-1">Phân loại</label>
                            <select id="filterLoai" class="form-select form-select-sm">
                                <option value="">Tất cả loại</option>
                                <?php foreach ($dsLoai as $loai): ?>
                                    <option value="<?= htmlspecialchars($loai, ENT_QUOTES, 'UTF-8') ?>"><?= $loaiThietBiLabels[$loai] ?? htmlspecialchars(str_replace('_', ' ', $loai), ENT_QUOTES, 'UTF-8') ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6 col-lg-3">
                            <label for="filterTrangThai" class="form-label small text-secondary mb-1">Trạng thái</label>
                            <select id="filterTrangThai" class="form-select form-select-sm">
                                <option value="">Tất cả trạng thái</option>
                                <?php foreach ($statusLabels as $key => $label): ?>
                                    <option value="<?= $key ?>"><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 small text-secondary mt-2">
                        <span>Hiển thị <strong id="visibleCount"><?= $tongThietBi ?></strong> / <strong><?= $tongThietBi ?></strong> thiết bị</span>
                        <button type="button" id="resetFilters" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>Xóa lọc
                        </button>
                    </div>
                </div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-hover table-sm align-middle mb-0" id="thietBiTable">
                    <thead>
                        <tr class="text-uppercase small text-secondary">
                            <th class="ps-3">Thiết bị</th>
                            <th>Phân loại</th>
                            <th>Sân bay</th>
                            <th>Trạng thái</th>
                            <th class="text-center">Số phiếu</th>
                            <th class="text-end pe-3 text-nowrap">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($thiet_bi)): ?>
                            <?php foreach ($thiet_bi as $item): ?>
                                <?php
                                $maThietBi  = 'TB-' . sprintf('%03d', (int) $item['id']);
                                $soPhieu    = (int) $item['so_phieu'];
                                $loaiLabel  = $loaiThietBiLabels[$item['loai_thiet_bi']] ?? htmlspecialchars(str_replace('_', ' ', $item['loai_thiet_bi']), ENT_QUOTES, 'UTF-8');
                                $loaiIcon   = $loaiThietBiIcons[$item['loai_thiet_bi']] ?? 'bi-hdd-stack';
                                $statusLabel = $statusLabels[$item['trang_thai']] ?? htmlspecialchars($item['trang_thai'], ENT_QUOTES, 'UTF-8');
                                $statusClass = $statusBadgeClasses[$item['trang_thai']] ?? 'text-secondary bg-body-tertiary border-secondary-subtle';
                                $statusDot   = $statusDotClasses[$item['trang_thai']] ?? 'bg-secondary';
                                ?>
                                <tr data-search="<?= htmlspecialchars($item['ten_thiet_bi'] . ' ' . $maThietBi, ENT_QUOTES, 'UTF-8') ?>"
                                    data-san-bay="<?= htmlspecialchars($item['ten_san_bay'], ENT_QUOTES, 'UTF-8') ?>"
                                    data-loai="<?= htmlspecialchars($item['loai_thiet_bi'], ENT_QUOTES, 'UTF-8') ?>"
                                    data-trang-thai="<?= htmlspecialchars($item['trang_thai'], ENT_QUOTES, 'UTF-8') ?>">
                                    <td class="ps-3" style="min-width: 190px; white-space: normal;">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary-subtle text-primary" style="width: 36px; height: 36px; flex: 0 0 36px;">
                                                <i class="bi <?= $loaiIcon ?>"></i>
                                            </span>
                                            <div>
                                                <div class="fw-semibold"><?= htmlspecialchars($item['ten_thiet_bi'], ENT_QUOTES, 'UTF-8') ?></div>
                                                <div class="small text-secondary"><?= $maThietBi ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill bg-body-tertiary text-body border">
                                            <i class="bi <?= $loaiIcon ?> me-1"></i><?= $loaiLabel ?>
                                        </span>
                                    </td>
                                    <td style="white-space: normal;">
                                        <i class="bi bi-geo-alt text-secondary me-1"></i><?= htmlspecialchars($item['ten_san_bay'], ENT_QUOTES, 'UTF-8') ?>
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill border <?= $statusClass ?>">
                                            <span class="d-inline-block rounded-circle <?= $statusDot ?> me-1" style="width: 6px; height: 6px;"></span>
                                            <?= $statusLabel ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($soPhieu > 0): ?>
                                            <span class="badge bg-body-tertiary text-body border" title="<?= $soPhieu ?> phiếu bảo trì">
                                                <i class="bi bi-clipboard-check me-1"></i><?= $soPhieu ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-secondary small">Chưa có</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end pe-3 text-nowrap">
                                        <?php if ($soPhieu > 0): ?>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" disabled title="Không thể xóa vì thiết bị đã có phiếu bảo trì">
                                                <i class="bi bi-lock me-1"></i>Đã có phiếu
                                            </button>
                                        <?php else: ?>
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                    data-bs-toggle="modal" data-bs-target="#xoaThietBiModal"
                                                    data-delete-url="<?= htmlspecialchars(baoTriUrl('thiet_bi.php', ['action' => 'xoa', 'id' => (int) $item['id']]), ENT_QUOTES, 'UTF-8') ?>"
                                                    data-device-name="<?= htmlspecialchars($item['ten_thiet_bi'], ENT_QUOTES, 'UTF-8') ?>">
                                                <i class="bi bi-trash3 me-1"></i>Xóa
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-secondary mb-2"><i class="bi bi-inbox fs-1"></i></div>
                                    <div class="fw-semibold">Chưa có thiết bị nào</div>
                                    <div class="small text-secondary mb-3">Thêm thiết bị đầu tiên để bắt đầu quản lý.</div>
                                    <button type="button" class="btn btn-sm btn-success"
                                            data-bs-toggle="modal" data-bs-target="#themThietBiModal">
                                        <i class="bi bi-plus-lg me-1"></i>Thêm thiết bị
                                    </button>
                                </td>
                            </tr>
                        <?php endif; ?>
                        <tr id="noResultRow" class="d-none">
                            <td colspan="6" class="text-center py-4 text-secondary">
                                <i class="bi bi-search me-1"></i> Không tìm thấy thiết bị phù hợp với bộ lọc.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!--begin::Modal thêm thiết bị-->
<style>
    #themThietBiModal .modal-dialog { max-width: 620px; }
    #themThietBiModal .modal-content { border: 0; border-radius: .75rem; overflow: hidden; }
    #themThietBiModal .modal-header { background: linear-gradient(135deg, rgba(25, 135, 84, .12), rgba(25, 135, 84, .03)); }
    #themThietBiModal .modal-icon { width: 38px; height: 38px; flex: 0 0 38px; }
    #themThietBiModal .form-label { font-size: .8125rem; }
</style>

<div class="modal fade" id="themThietBiModal" tabindex="-1" aria-labelledby="themThietBiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content shadow-lg">
            <form action="<?= baoTriUrl('thiet_bi.php') ?>" method="POST">
                <input type="hidden" name="action" value="store_thiet_bi">
                <div class="modal-header border-0 px-3 py-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="modal-icon d-inline-flex align-items-center justify-content-center rounded-circle bg-success text-white shadow-sm">
                            <i class="bi bi-plus-lg"></i>
                        </span>
                        <div>
                            <h5 class="modal-title mb-0 fs-5" id="themThietBiModalLabel">Thêm thiết bị mới</h5>
                            <div class="small text-secondary">Khai báo trang thiết bị mặt đất</div>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>

                <div class="modal-body px-3 py-3">
                    <?php if ($modalError !== null): ?>
                        <div class="alert alert-danger d-flex align-items-start gap-2 py-2 mb-3" role="alert">
                            <i class="bi bi-exclamation-circle-fill mt-1"></i>
                            <div class="small"><?= htmlspecialchars($modalError, ENT_QUOTES, 'UTF-8') ?></div>
                        </div>
                    <?php endif; ?>

                    <div class="mb-2">
                        <label for="them_ten_thiet_bi" class="form-label fw-semibold">
                            Tên thiết bị <span class="text-danger">*</span>
                        </label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-body-tertiary"><i class="bi bi-hdd-stack"></i></span>
                            <input type="text" name="ten_thiet_bi" id="them_ten_thiet_bi" class="form-control"
                                   maxlength="100" placeholder="Ví dụ: Xe kéo A1 - Sân bay Nội Bài" required>
                        </div>
                        <div class="form-text">Tên này sẽ hiển thị trong danh mục và phiếu bảo trì.</div>
                    </div>

                    <div class="mb-2">
                        <label for="them_loai_thiet_bi" class="form-label fw-semibold">
                            Phân loại <span class="text-danger">*</span>
                        </label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-body-tertiary"><i class="bi bi-tags"></i></span>
                            <select name="loai_thiet_bi" id="them_loai_thiet_bi" class="form-select form-select-sm" required>
                                <option value="">-- Chọn phân loại --</option>
                                <?php foreach ($loaiThietBiLabels as $value => $label): ?>
                                    <option value="<?= htmlspecialchars($value, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label for="them_id_san_bay" class="form-label fw-semibold">
                            Sân bay quản lý <span class="text-danger">*</span>
                        </label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-body-tertiary"><i class="bi bi-geo-alt"></i></span>
                            <select name="id_san_bay" id="them_id_san_bay" class="form-select form-select-sm" required>
                                <option value="">-- Chọn sân bay --</option>
                                <?php foreach ($san_bay_list as $sb): ?>
                                    <option value="<?= (int) $sb['id'] ?>"><?= htmlspecialchars($sb['ten_san_bay'], ENT_QUOTES, 'UTF-8') ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php if (empty($san_bay_list)): ?>
                            <div class="form-text text-danger">Chưa có dữ liệu sân bay.</div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="modal-footer border-0 bg-body-tertiary d-flex flex-wrap justify-content-between gap-2 px-3 py-2">
                    <div class="small text-secondary d-none d-sm-block">
                        <i class="bi bi-info-circle me-1"></i> Thiết bị mới mặc định ở trạng thái <strong>Tốt</strong>.
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-light border px-3" data-bs-dismiss="modal">Hủy bỏ</button>
                        <button type="submit" class="btn btn-sm btn-success px-3" <?= empty($san_bay_list) ? 'disabled' : '' ?>>
                            <i class="bi bi-check2 me-1"></i> Lưu thiết bị
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!--end::Modal thêm thiết bị-->

<!-- Modal xác nhận xóa -->
<div class="modal fade" id="xoaThietBiModal" tabindex="-1" aria-labelledby="xoaThietBiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-body text-center p-4">
                <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-danger-subtle text-danger mb-3" style="width: 58px; height: 58px;">
                    <i class="bi bi-trash3 fs-4"></i>
                </span>
                <h5 class="mb-2" id="xoaThietBiModalLabel">Xóa thiết bị?</h5>
                <p class="text-secondary small mb-0">
                    Bạn có chắc muốn xóa <strong id="xoaTenThietBi">thiết bị này</strong>?
                    Hành động này không thể hoàn tác.
                </p>
            </div>
            <div class="modal-footer border-0 justify-content-center pt-0 pb-4">
                <button type="button" class="btn btn-light border px-3" data-bs-dismiss="modal">Hủy</button>
                <a href="#" id="xoaThietBiLink" class="btn btn-danger px-3">
                    <i class="bi bi-trash3 me-1"></i>Xóa thiết bị
                </a>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/main.php';
