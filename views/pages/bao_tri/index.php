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
    if (modalElement && window.bootstrap && new URLSearchParams(window.location.search).get('open') === 'create') {
        bootstrap.Modal.getOrCreateInstance(modalElement).show();
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
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#taoPhieuModal">
                        <i class="bi bi-plus-lg me-1"></i> Tạo phiếu bảo trì
                    </button>
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
