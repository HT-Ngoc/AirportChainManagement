<?php
require_once __DIR__ . '/_bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'cap_nhat_ticket') {
        $baoTriController->capNhatTrangThaiTicket();
    } elseif ($action === 'store_ticket') {
        $baoTriController->storeTicket();
    }
}

if (!isset($tickets, $thong_ke)) {
    $data     = $baoTriController->getIndexData();
    $tickets  = $data['tickets'];
    $thong_ke = $data['thong_ke'];
}

if (!isset($thiet_bi_list)) {
    $thiet_bi_list = $baoTriController->getThietBiList();
}

// Nhóm thiết bị theo sân bay để hiển thị trong modal
$thietBiTheoSanBay = [];
foreach ($thiet_bi_list as $tb) {
    $tenSanBay = !empty($tb['ten_san_bay']) ? $tb['ten_san_bay'] : 'Khác';
    $thietBiTheoSanBay[$tenSanBay][] = $tb;
}

$pageTitle  = 'Bảo trì & khắc phục';
$pageHeader = 'Bảo trì & khắc phục';
$navActive    = 'bao_tri';
$navSubActive = 'tickets';
$breadcrumb = [
    ['label' => 'Tổng quan', 'url' => BASE_URL . '/index.php'],
    ['label' => 'Bảo trì', 'url' => null],
];

$uploadBase = BASE_URL . '/public/uploads/bao_tri/';

$statusLabels = [
    'Moi'        => 'Mới',
    'Dang_Xu_Ly' => 'Đang xử lý',
    'Hoan_Thanh' => 'Hoàn thành',
];

$statusBadgeClasses = [
    'Moi'        => 'text-info-emphasis bg-info-subtle border-info-subtle',
    'Dang_Xu_Ly' => 'text-primary-emphasis bg-primary-subtle border-primary-subtle',
    'Hoan_Thanh' => 'text-success-emphasis bg-success-subtle border-success-subtle',
];

$statusDotClasses = [
    'Moi'        => 'bg-info',
    'Dang_Xu_Ly' => 'bg-primary',
    'Hoan_Thanh' => 'bg-success',
];

$priorityLabels = [
    'Thap'       => 'Thấp',
    'Trung_Binh' => 'Trung bình',
    'Khan_Cap'   => 'Khẩn cấp',
];

$priorityBadgeClasses = [
    'Thap'       => 'text-success-emphasis bg-success-subtle border-success-subtle',
    'Trung_Binh' => 'text-warning-emphasis bg-warning-subtle border-warning-subtle',
    'Khan_Cap'   => 'text-danger-emphasis bg-danger-subtle border-danger-subtle',
];

$priorityIcons = [
    'Thap'       => 'bi-check-circle',
    'Trung_Binh' => 'bi-exclamation-circle',
    'Khan_Cap'   => 'bi-exclamation-octagon',
];

$loaiThietBiLabels = [
    'Den_Tin_Hieu'       => 'Đèn tín hiệu',
    'Thiet_Bi_Dan_Duong' => 'Thiết bị dẫn đường',
    'Xe_Thang'           => 'Xe thang',
    'Xe_Keo'             => 'Xe kéo',
    'Khac'               => 'Khác',
];

$trangThaiThietBiLabels = [
    'Tot'     => 'Tốt',
    'Bao_Tri' => 'Đang bảo trì',
    'Hong'    => 'Hỏng',
];

// Tự mở lại modal khi vừa có lỗi nhập liệu
$modalError = null;
if (isset($_GET['open']) && $_GET['open'] === 'create' && !empty($_SESSION['flash_error'])) {
    $modalError = (string) $_SESSION['flash_error'];
    unset($_SESSION['flash_error']);
}

$pageScripts = <<<'HTML'
<script>
document.addEventListener('DOMContentLoaded', function () {
    var modalElement = document.getElementById('taoPhieuModal');

    // Tự mở lại modal khi quay về sau lỗi nhập liệu
    if (modalElement) {
        if (window.bootstrap && new URLSearchParams(window.location.search).get('open') === 'create') {
            bootstrap.Modal.getOrCreateInstance(modalElement).show();
        }

        modalElement.addEventListener('shown.bs.modal', function () {
            var deviceSelect = document.getElementById('modal_id_thiet_bi');
            if (deviceSelect) deviceSelect.focus();
        });

        modalElement.addEventListener('hidden.bs.modal', function () {
            var url = new URL(window.location.href);
            if (url.searchParams.get('open') === 'create') {
                url.searchParams.delete('open');
                window.history.replaceState({}, '', url.toString());
            }
        });
    }

    var imageInput = document.getElementById('modal_hinh_anh_minh_hoa');
    var imagePreview = document.getElementById('imagePreview');
    var imagePreviewImg = document.getElementById('imagePreviewImg');
    var imageError = document.getElementById('imageError');
    var dropZone = document.getElementById('imageDropZone');

    function setImageError(message) {
        if (!imageError) return;
        imageError.textContent = message || '';
        imageError.classList.toggle('d-none', !message);
    }

    function resetImagePreview() {
        if (imagePreviewImg) imagePreviewImg.src = '';
        if (imagePreview) imagePreview.classList.add('d-none');
    }

    function showImagePreview() {
        resetImagePreview();
        setImageError('');

        if (!imageInput || !imageInput.files || !imageInput.files[0]) return;

        var file = imageInput.files[0];
        if (file.size > 5 * 1024 * 1024) {
            imageInput.value = '';
            setImageError('Ảnh vượt quá 5 MB. Vui lòng chọn ảnh nhỏ hơn.');
            return;
        }
        if (!file.type.match(/^image\/(jpeg|jpg|png|gif)$/)) {
            imageInput.value = '';
            setImageError('Chỉ chấp nhận ảnh JPG, PNG hoặc GIF.');
            return;
        }

        if (imagePreviewImg) imagePreviewImg.src = URL.createObjectURL(file);
        if (imagePreview) imagePreview.classList.remove('d-none');
    }

    if (imageInput) imageInput.addEventListener('change', showImagePreview);

    if (dropZone && imageInput) {
        ['dragenter', 'dragover'].forEach(function (evt) {
            dropZone.addEventListener(evt, function (e) {
                e.preventDefault();
                e.stopPropagation();
                dropZone.classList.add('border-danger');
            });
        });
        ['dragleave', 'drop'].forEach(function (evt) {
            dropZone.addEventListener(evt, function (e) {
                e.preventDefault();
                e.stopPropagation();
                dropZone.classList.remove('border-danger');
            });
        });
        dropZone.addEventListener('drop', function (e) {
            if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length) {
                imageInput.files = e.dataTransfer.files;
                showImagePreview();
            }
        });
    }

    var description = document.getElementById('modal_mo_ta_su_co');
    var descriptionCount = document.getElementById('moTaCount');
    function updateDescriptionCount() {
        if (description && descriptionCount) descriptionCount.textContent = description.value.length;
    }
    if (description) {
        description.addEventListener('input', updateDescriptionCount);
        updateDescriptionCount();
    }

    // Bộ lọc phiếu bảo trì
    var ticketTable = document.getElementById('ticketTable');
    var ticketSearch = document.getElementById('ticketSearch');
    var ticketStatusFilter = document.getElementById('ticketStatusFilter');
    var ticketPriorityFilter = document.getElementById('ticketPriorityFilter');
    var ticketResetFilters = document.getElementById('ticketResetFilters');
    var ticketVisibleCount = document.getElementById('ticketVisibleCount');
    var ticketNoResultRow = document.getElementById('ticketNoResultRow');

    function normalizeText(value) {
        return (value || '').toString().toLocaleLowerCase('vi')
            .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
            .replace(/đ/g, 'd');
    }

    if (ticketTable && ticketSearch && ticketStatusFilter && ticketPriorityFilter) {
        var ticketRows = Array.prototype.slice.call(ticketTable.querySelectorAll('tbody tr[data-ticket-search]'));

        function applyTicketFilters() {
            var keyword = normalizeText(ticketSearch.value.trim());
            var status = ticketStatusFilter.value;
            var priority = ticketPriorityFilter.value;
            var visible = 0;

            ticketRows.forEach(function (row) {
                var matchKeyword = !keyword || normalizeText(row.getAttribute('data-ticket-search')).indexOf(keyword) !== -1;
                var matchStatus = !status || row.getAttribute('data-ticket-status') === status;
                var matchPriority = !priority || row.getAttribute('data-ticket-priority') === priority;
                var show = matchKeyword && matchStatus && matchPriority;

                row.classList.toggle('d-none', !show);
                if (show) visible++;
            });

            if (ticketVisibleCount) ticketVisibleCount.textContent = visible;
            if (ticketNoResultRow) ticketNoResultRow.classList.toggle('d-none', visible !== 0 || ticketRows.length === 0);
        }

        [ticketSearch, ticketStatusFilter, ticketPriorityFilter].forEach(function (element) {
            element.addEventListener('input', applyTicketFilters);
            element.addEventListener('change', applyTicketFilters);
        });

        if (ticketResetFilters) {
            ticketResetFilters.addEventListener('click', function () {
                ticketSearch.value = '';
                ticketStatusFilter.value = '';
                ticketPriorityFilter.value = '';
                applyTicketFilters();
                ticketSearch.focus();
            });
        }

        applyTicketFilters();
    }
});
</script>
HTML;

// Giá trị mặc định phòng khi view được nạp trực tiếp
$thong_ke = array_merge([
    'dang_mo'          => 0,
    'khan_cap'         => 0,
    'hoan_thanh_thang' => 0,
    'thiet_bi_bao_tri' => 0,
], $thong_ke ?? []);

$totalTickets = count($tickets);

ob_start();
?>
<?php require __DIR__ . '/_flash.php'; ?>
<style>
    .maint-stat-card { transition: transform .15s ease, box-shadow .15s ease; }
    .maint-stat-card:hover { transform: translateY(-2px); box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .08) !important; }
    .maint-stat-icon { width: 46px; height: 46px; flex: 0 0 46px; font-size: 1.3rem; }
    .maint-stat-label { font-size: .7rem; letter-spacing: .04em; }
    .maint-description { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .maint-ticket-thumb { width: 52px; height: 52px; object-fit: cover; flex: 0 0 52px; }
    @media (max-width: 575.98px) {
        .maint-stat-icon { width: 38px; height: 38px; flex-basis: 38px; font-size: 1.05rem; }
        .maint-stat-label { font-size: .63rem; }
        .maint-stat-card .card-body { padding: .75rem; }
        .maint-stat-card h3 { font-size: 1.4rem; }
    }
</style>

<div class="row g-3 mb-3">
    <div class="col-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100 maint-stat-card">
            <div class="card-body d-flex align-items-center gap-2 gap-sm-3">
                <span class="maint-stat-icon rounded-3 bg-primary-subtle text-primary-emphasis d-inline-flex align-items-center justify-content-center">
                    <i class="bi bi-clipboard-check"></i>
                </span>
                <div style="min-width: 0;">
                    <div class="maint-stat-label text-secondary text-uppercase fw-semibold">Phiếu đang mở</div>
                    <div class="d-flex align-items-baseline gap-1">
                        <h3 class="mb-0 fw-bold"><?= (int) $thong_ke['dang_mo'] ?></h3>
                        <?php if ($totalTickets > 0): ?>
                            <span class="small text-secondary d-none d-sm-inline">/ <?= $totalTickets ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="small text-secondary text-truncate d-none d-sm-block">Cần theo dõi</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100 maint-stat-card">
            <div class="card-body d-flex align-items-center gap-2 gap-sm-3">
                <span class="maint-stat-icon rounded-3 bg-danger-subtle text-danger-emphasis d-inline-flex align-items-center justify-content-center">
                    <i class="bi bi-exclamation-octagon"></i>
                </span>
                <div style="min-width: 0;">
                    <div class="maint-stat-label text-secondary text-uppercase fw-semibold">Sự cố khẩn cấp</div>
                    <h3 class="mb-0 fw-bold"><?= (int) $thong_ke['khan_cap'] ?></h3>
                    <div class="small text-secondary text-truncate d-none d-sm-block">Ưu tiên xử lý</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100 maint-stat-card">
            <div class="card-body d-flex align-items-center gap-2 gap-sm-3">
                <span class="maint-stat-icon rounded-3 bg-success-subtle text-success-emphasis d-inline-flex align-items-center justify-content-center">
                    <i class="bi bi-check2-circle"></i>
                </span>
                <div style="min-width: 0;">
                    <div class="maint-stat-label text-secondary text-uppercase fw-semibold">Hoàn thành trong tháng</div>
                    <h3 class="mb-0 fw-bold"><?= (int) $thong_ke['hoan_thanh_thang'] ?></h3>
                    <div class="small text-secondary text-truncate d-none d-sm-block">Trong tháng này</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100 maint-stat-card">
            <div class="card-body d-flex align-items-center gap-2 gap-sm-3">
                <span class="maint-stat-icon rounded-3 bg-info-subtle text-info-emphasis d-inline-flex align-items-center justify-content-center">
                    <i class="bi bi-tools"></i>
                </span>
                <div style="min-width: 0;">
                    <div class="maint-stat-label text-secondary text-uppercase fw-semibold">Thiết bị đang bảo trì</div>
                    <h3 class="mb-0 fw-bold"><?= (int) $thong_ke['thiet_bi_bao_tri'] ?></h3>
                    <div class="small text-secondary text-truncate d-none d-sm-block">Đang theo dõi</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-body-tertiary border-0 d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div class="flex-grow-1">
            <h3 class="card-title mb-0 float-none">
                <i class="bi bi-clipboard-check me-2 text-primary"></i>Danh sách phiếu bảo trì
                <span class="badge bg-body-tertiary text-body border ms-1 align-middle fw-normal" title="Tổng số phiếu"><?= $totalTickets ?></span>
            </h3>
            <div class="small text-secondary mt-1">Theo dõi và cập nhật tiến độ xử lý</div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#taoPhieuModal">
                <i class="bi bi-plus-lg me-1"></i>Tạo phiếu bảo trì
            </button>
        </div>
    </div>

    <?php if (!empty($tickets)): ?>
        <div class="card-body pb-2">
            <div class="row g-2">
                <div class="col-12 col-lg-5">
                    <label for="ticketSearch" class="form-label small text-secondary mb-1">Tìm kiếm</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-body-tertiary"><i class="bi bi-search"></i></span>
                        <input type="search" id="ticketSearch" class="form-control" placeholder="Mã phiếu, thiết bị hoặc mô tả...">
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <label for="ticketStatusFilter" class="form-label small text-secondary mb-1">Trạng thái</label>
                    <select id="ticketStatusFilter" class="form-select form-select-sm">
                        <option value="">Tất cả trạng thái</option>
                        <?php foreach ($statusLabels as $key => $label): ?>
                            <option value="<?= $key ?>"><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-6 col-lg-4">
                    <label for="ticketPriorityFilter" class="form-label small text-secondary mb-1">Mức độ</label>
                    <select id="ticketPriorityFilter" class="form-select form-select-sm">
                        <option value="">Tất cả mức độ</option>
                        <?php foreach ($priorityLabels as $key => $label): ?>
                            <option value="<?= $key ?>"><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 small text-secondary mt-2">
                <span>Hiển thị <strong id="ticketVisibleCount"><?= $totalTickets ?></strong> / <strong><?= $totalTickets ?></strong> phiếu</span>
                <button type="button" id="ticketResetFilters" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-counterclockwise me-1"></i>Xóa lọc
                </button>
            </div>
        </div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-hover table-sm align-middle mb-0" id="ticketTable">
            <thead>
                <tr class="text-uppercase small text-secondary">
                    <th class="ps-3">Phiếu</th>
                    <th>Thiết bị &amp; sự cố</th>
                    <th>Mức độ / Trạng thái</th>
                    <th class="text-end pe-3">Cập nhật</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($tickets)): ?>
                    <?php foreach ($tickets as $ticket): ?>
                        <?php
                        $maPhieu = '#BT-' . sprintf('%03d', (int) $ticket['id']);
                        $ngayTao = date('d/m/Y H:i', strtotime($ticket['ngay_tao']));
                        $mucDo = $ticket['muc_do'];
                        $trangThai = $ticket['trang_thai_xu_ly'];
                        $priorityLabel = $priorityLabels[$mucDo] ?? htmlspecialchars($mucDo, ENT_QUOTES, 'UTF-8');
                        $priorityClass = $priorityBadgeClasses[$mucDo] ?? 'text-secondary bg-body-tertiary border-secondary-subtle';
                        $priorityIcon = $priorityIcons[$mucDo] ?? 'bi-circle';
                        $statusLabel = $statusLabels[$trangThai] ?? htmlspecialchars($trangThai, ENT_QUOTES, 'UTF-8');
                        $statusClass = $statusBadgeClasses[$trangThai] ?? 'text-secondary bg-body-tertiary border-secondary-subtle';
                        $statusDot = $statusDotClasses[$trangThai] ?? 'bg-secondary';
                        $anhUrl = !empty($ticket['hinh_anh_minh_hoa'])
                            ? $uploadBase . rawurlencode($ticket['hinh_anh_minh_hoa'])
                            : '';
                        ?>
                        <tr data-ticket-search="<?= htmlspecialchars($maPhieu . ' ' . $ticket['ten_thiet_bi'] . ' ' . $ticket['mo_ta_su_co'], ENT_QUOTES, 'UTF-8') ?>"
                            data-ticket-status="<?= htmlspecialchars($trangThai, ENT_QUOTES, 'UTF-8') ?>"
                            data-ticket-priority="<?= htmlspecialchars($mucDo, ENT_QUOTES, 'UTF-8') ?>">
                            <td class="ps-3" style="min-width: 105px; white-space: nowrap;">
                                <div class="fw-semibold text-primary"><?= $maPhieu ?></div>
                                <div class="small text-secondary"><i class="bi bi-clock me-1"></i><?= $ngayTao ?></div>
                            </td>
                            <td style="min-width: 230px; white-space: normal;">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="flex-grow-1" style="min-width: 0;">
                                        <div class="fw-semibold"><?= htmlspecialchars($ticket['ten_thiet_bi'], ENT_QUOTES, 'UTF-8') ?></div>
                                        <div class="small text-secondary maint-description" title="<?= htmlspecialchars($ticket['mo_ta_su_co'], ENT_QUOTES, 'UTF-8') ?>">
                                            <?= htmlspecialchars($ticket['mo_ta_su_co'], ENT_QUOTES, 'UTF-8') ?>
                                        </div>
                                    </div>
                                    <?php if ($anhUrl !== ''): ?>
                                        <a href="<?= $anhUrl ?>" target="_blank" rel="noopener" class="flex-shrink-0" title="Xem ảnh minh họa">
                                            <img src="<?= $anhUrl ?>" alt="Ảnh sự cố" class="rounded border maint-ticket-thumb">
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td style="min-width: 145px; white-space: nowrap;">
                                <div class="d-flex flex-column align-items-start gap-1">
                                    <span class="badge rounded-pill border <?= $priorityClass ?>">
                                        <i class="bi <?= $priorityIcon ?> me-1"></i><?= $priorityLabel ?>
                                    </span>
                                    <span class="badge rounded-pill border <?= $statusClass ?>">
                                        <span class="d-inline-block rounded-circle <?= $statusDot ?> me-1" style="width: 6px; height: 6px;"></span>
                                        <?= $statusLabel ?>
                                    </span>
                                </div>
                            </td>
                            <td class="text-end pe-3" style="min-width: 165px;">
                                <?php if ($trangThai !== 'Hoan_Thanh'): ?>
                                    <form action="<?= baoTriUrl('index.php') ?>" method="POST" class="d-flex justify-content-end align-items-center gap-1">
                                        <input type="hidden" name="action" value="cap_nhat_ticket">
                                        <input type="hidden" name="id_ticket" value="<?= (int) $ticket['id'] ?>">
                                        <select name="trang_thai_xu_ly" class="form-select form-select-sm" style="max-width: 115px;" aria-label="Cập nhật trạng thái phiếu <?= $maPhieu ?>">
                                            <option value="Moi" <?= $trangThai === 'Moi' ? 'selected' : '' ?>>Mới</option>
                                            <option value="Dang_Xu_Ly" <?= $trangThai === 'Dang_Xu_Ly' ? 'selected' : '' ?>>Đang xử lý</option>
                                            <option value="Hoan_Thanh" <?= $trangThai === 'Hoan_Thanh' ? 'selected' : '' ?>>Hoàn thành</option>
                                        </select>
                                        <button type="submit" class="btn btn-sm btn-success" title="Lưu trạng thái" aria-label="Lưu trạng thái phiếu <?= $maPhieu ?>">
                                            <i class="bi bi-check2"></i>
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span class="badge text-success-emphasis bg-success-subtle border border-success-subtle">
                                        <i class="bi bi-check2-circle me-1"></i>Đã hoàn thành
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center py-5">
                            <div class="text-secondary mb-2"><i class="bi bi-clipboard-x fs-1"></i></div>
                            <div class="fw-semibold">Chưa có phiếu bảo trì nào</div>
                            <div class="small text-secondary mb-3">Tạo phiếu đầu tiên để ghi nhận sự cố và theo dõi xử lý.</div>
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#taoPhieuModal">
                                <i class="bi bi-plus-lg me-1"></i>Tạo phiếu bảo trì
                            </button>
                        </td>
                    </tr>
                <?php endif; ?>
                <tr id="ticketNoResultRow" class="d-none">
                    <td colspan="4" class="text-center py-4 text-secondary">
                        <i class="bi bi-search me-1"></i> Không tìm thấy phiếu phù hợp với bộ lọc.
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!--begin::Modal tạo phiếu bảo trì-->
<style>
    #taoPhieuModal .modal-dialog { max-width: 680px; }
    #taoPhieuModal .modal-content { border: 0; border-radius: .75rem; overflow: hidden; }
    #taoPhieuModal .modal-header { background: linear-gradient(135deg, rgba(220, 53, 69, .12), rgba(220, 53, 69, .03)); }
    #taoPhieuModal .modal-icon { width: 38px; height: 38px; flex: 0 0 38px; }
    #taoPhieuModal .form-label { font-size: .8125rem; }
    #taoPhieuModal .severity-group .btn { white-space: nowrap; font-size: .8125rem; }
    #taoPhieuModal .image-dropzone {
        border: 2px dashed var(--bs-border-color);
        transition: border-color .15s ease, background-color .15s ease;
    }
    #taoPhieuModal .image-dropzone:hover { background-color: var(--bs-tertiary-bg); }
    #taoPhieuModal #imagePreviewImg {
        max-height: 100px;
        width: 100%;
        object-fit: contain;
        background: var(--bs-body-bg);
    }
</style>

<div class="modal fade" id="taoPhieuModal" tabindex="-1" aria-labelledby="taoPhieuModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content shadow-lg">
            <form action="<?= baoTriUrl('index.php') ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="store_ticket">
                <div class="modal-header border-0 px-3 py-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="modal-icon d-inline-flex align-items-center justify-content-center rounded-circle bg-danger text-white shadow-sm">
                            <i class="bi bi-tools"></i>
                        </span>
                        <h5 class="modal-title mb-0 fs-5" id="taoPhieuModalLabel">Tạo phiếu bảo trì</h5>
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

                    <div class="row g-3">
                        <div class="col-lg-7">
                            <div class="mb-2">
                                <label for="modal_id_thiet_bi" class="form-label fw-semibold">
                                    Thiết bị gặp sự cố <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-body-tertiary"><i class="bi bi-hdd-stack"></i></span>
                                    <select name="id_thiet_bi" id="modal_id_thiet_bi" class="form-select form-select-sm" required>
                                        <option value="">-- Chọn thiết bị --</option>
                                        <?php foreach ($thietBiTheoSanBay as $tenSanBay => $dsThietBi): ?>
                                            <optgroup label="<?= htmlspecialchars($tenSanBay, ENT_QUOTES, 'UTF-8') ?>">
                                                <?php foreach ($dsThietBi as $tb): ?>
                                                    <option value="<?= (int) $tb['id'] ?>"><?= htmlspecialchars($tb['ten_thiet_bi'], ENT_QUOTES, 'UTF-8') ?> — <?= $loaiThietBiLabels[$tb['loai_thiet_bi']] ?? htmlspecialchars(str_replace('_', ' ', $tb['loai_thiet_bi']), ENT_QUOTES, 'UTF-8') ?> (<?= $trangThaiThietBiLabels[$tb['trang_thai']] ?? htmlspecialchars($tb['trang_thai'], ENT_QUOTES, 'UTF-8') ?>)</option>
                                                <?php endforeach; ?>
                                            </optgroup>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <?php if (empty($thietBiTheoSanBay)): ?>
                                    <div class="form-text text-danger mt-1">
                                        Chưa có thiết bị.
                                        <a href="<?= baoTriUrl('thiet_bi.php', ['open' => 'create']) ?>">Thêm thiết bị</a> trước khi tạo phiếu.
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="modal_mo_ta_su_co" class="form-label fw-semibold mb-0">
                                        Mô tả sự cố <span class="text-danger">*</span>
                                    </label>
                                    <span class="small text-secondary"><span id="moTaCount">0</span>/1000</span>
                                </div>
                                <textarea name="mo_ta_su_co" id="modal_mo_ta_su_co" class="form-control form-control-sm mt-1" rows="3"
                                          maxlength="1000" placeholder="Ví dụ: Đèn tín hiệu tại đường lăn A bị chập, không sáng khi thử nghiệm..."
                                          required></textarea>
                            </div>

                            <div class="mb-0">
                                <label class="form-label fw-semibold">Mức độ nghiêm trọng <span class="text-danger">*</span></label>
                                <div class="btn-group w-100 severity-group" role="group" aria-label="Mức độ nghiêm trọng">
                                    <input type="radio" class="btn-check" name="muc_do" id="mucDoThap" value="Thap" checked>
                                    <label class="btn btn-outline-success flex-fill" for="mucDoThap" title="Không ảnh hưởng ngay đến khai thác">
                                        <i class="bi bi-check-circle me-1"></i>Thấp
                                    </label>

                                    <input type="radio" class="btn-check" name="muc_do" id="mucDoTrungBinh" value="Trung_Binh">
                                    <label class="btn btn-outline-warning flex-fill" for="mucDoTrungBinh" title="Cần xử lý trong thời gian sớm nhất">
                                        <i class="bi bi-exclamation-circle me-1"></i>Trung bình
                                    </label>

                                    <input type="radio" class="btn-check" name="muc_do" id="mucDoKhanCap" value="Khan_Cap">
                                    <label class="btn btn-outline-danger flex-fill" for="mucDoKhanCap" title="Ảnh hưởng trực tiếp đến an toàn khai thác">
                                        <i class="bi bi-exclamation-octagon me-1"></i>Khẩn cấp
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-5">
                            <label class="form-label fw-semibold">
                                Hình ảnh minh họa <span class="text-secondary fw-normal">(không bắt buộc)</span>
                            </label>
                            <label for="modal_hinh_anh_minh_hoa" id="imageDropZone"
                                   class="image-dropzone d-flex flex-column align-items-center justify-content-center text-center rounded-3 bg-body-tertiary p-3 w-100"
                                   style="cursor: pointer; min-height: 150px;">
                                <i class="bi bi-cloud-arrow-up fs-3 text-secondary"></i>
                                <span class="small fw-semibold mt-1">Chọn ảnh hoặc kéo thả</span>
                                <span class="text-secondary" style="font-size: .72rem;">JPG, PNG, GIF · tối đa 5 MB</span>
                            </label>
                            <input type="file" name="hinh_anh_minh_hoa" id="modal_hinh_anh_minh_hoa" class="d-none"
                                   accept="image/png, image/jpeg, image/jpg, image/gif">

                            <div id="imageError" class="small text-danger mt-1 d-none"></div>

                            <div id="imagePreview" class="mt-2 d-none">
                                <img id="imagePreviewImg" src="" alt="Ảnh minh họa sự cố" class="img-fluid rounded border">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 bg-body-tertiary d-flex flex-wrap justify-content-between gap-2 px-3 py-2">
                    <div class="small text-secondary d-none d-sm-block">
                        <i class="bi bi-info-circle me-1"></i> Thiết bị chuyển sang <strong>Hỏng</strong> · Phiếu ở trạng thái <strong>Mới</strong>.
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-light border px-3" data-bs-dismiss="modal">Hủy bỏ</button>
                        <button type="submit" class="btn btn-sm btn-danger px-3">
                            <i class="bi bi-send me-1"></i> Gửi yêu cầu
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!--end::Modal tạo phiếu bảo trì-->

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/main.php';
