<?php
if (!$formMode) {
    return;
}

$formData = [
    'id' => 0,
    'ma_iata' => '',
    'ma_icao' => '',
    'ten_san_bay' => '',
    'ten_ngan' => '',
    'phan_cap' => 'Quoc_Noi',
    'vi_tri' => '',
    'hinh_anh' => '',
    'mo_ta' => '',
    'thu_tu_hien_thi' => 0,
    'trang_thai' => 'Hoat_Dong',
];
if ($formRecord) {
    $formData = array_merge($formData, $formRecord);
}
$isEdit = $formMode === 'sua';
$actionName = $isEdit ? 'sua_san_bay' : 'them_san_bay';
?>
<div class="ht-form-card">
  <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
    <h4 class="mb-0">
      <i class="bi <?= $isEdit ? 'bi-pencil-square' : 'bi-plus-circle' ?> me-1"></i>
      <?= $isEdit ? 'Cập nhật sân bay' : 'Thêm sân bay mới' ?>
    </h4>
    <span class="badge text-bg-light border"><?= $isEdit ? 'Mã: ' . $e($formData['ma_iata']) : 'Danh mục sân bay' ?></span>
  </div>

  <form method="post" action="<?= $e(BASE_URL . '/index.php?page=ha_tang') ?>">
    <input type="hidden" name="csrf" value="<?= $e($data['csrf']) ?>" />
    <input type="hidden" name="action" value="<?= $e($actionName) ?>" />
    <input type="hidden" name="id" value="<?= (int) $formData['id'] ?>" />
    <input type="hidden" name="return_tab" value="<?= $e($returnTab) ?>" />
    <input type="hidden" name="return_san_bay" value="<?= $e($returnSanBay) ?>" />
    <input type="hidden" name="return_nha_ga" value="<?= (int) $returnNhaGa ?>" />

    <div class="row g-3">
      <div class="col-md-3">
        <label class="form-label" for="sb-iata">Mã IATA <span class="text-danger">*</span></label>
        <input type="text" class="form-control form-control-sm text-uppercase" id="sb-iata" name="ma_iata" value="<?= $e($formData['ma_iata']) ?>" maxlength="3" pattern="[A-Za-z]{3}" placeholder="HAN" required />
        <div class="form-text">3 chữ cái, ví dụ HAN.</div>
      </div>
      <div class="col-md-3">
        <label class="form-label" for="sb-icao">Mã ICAO</label>
        <input type="text" class="form-control form-control-sm text-uppercase" id="sb-icao" name="ma_icao" value="<?= $e($formData['ma_icao']) ?>" maxlength="4" pattern="[A-Za-z]{4}" placeholder="VVNB" />
        <div class="form-text">Không bắt buộc, 4 chữ cái.</div>
      </div>
      <div class="col-md-6">
        <label class="form-label" for="sb-ten">Tên sân bay <span class="text-danger">*</span></label>
        <input type="text" class="form-control form-control-sm" id="sb-ten" name="ten_san_bay" value="<?= $e($formData['ten_san_bay']) ?>" maxlength="120" placeholder="Sân bay quốc tế Nội Bài" required />
      </div>

      <div class="col-md-4">
        <label class="form-label" for="sb-ten-ngan">Tên ngắn <span class="text-danger">*</span></label>
        <input type="text" class="form-control form-control-sm" id="sb-ten-ngan" name="ten_ngan" value="<?= $e($formData['ten_ngan']) ?>" maxlength="50" placeholder="Nội Bài" required />
      </div>
      <div class="col-md-4">
        <label class="form-label" for="sb-phan-cap">Phân cấp <span class="text-danger">*</span></label>
        <select class="form-select form-select-sm" id="sb-phan-cap" name="phan_cap" required>
          <option value="Quoc_Noi" <?= $formData['phan_cap'] === 'Quoc_Noi' ? 'selected' : '' ?>>Quốc nội</option>
          <option value="Quoc_Te" <?= $formData['phan_cap'] === 'Quoc_Te' ? 'selected' : '' ?>>Quốc tế</option>
        </select>
      </div>
      <div class="col-md-4">
        <label class="form-label" for="sb-vi-tri">Vị trí <span class="text-danger">*</span></label>
        <input type="text" class="form-control form-control-sm" id="sb-vi-tri" name="vi_tri" value="<?= $e($formData['vi_tri']) ?>" maxlength="255" placeholder="Huyện Sóc Sơn, Hà Nội" required />
      </div>

      <div class="col-md-6">
        <label class="form-label" for="sb-hinh">Ảnh sân bay</label>
        <input type="text" class="form-control form-control-sm" id="sb-hinh" name="hinh_anh" value="<?= $e($formData['hinh_anh']) ?>" maxlength="255" placeholder="assets/img/san_bay/HAN.jpg" />
        <div class="form-text">Để trống sẽ dùng ảnh mặc định theo mã IATA.</div>
      </div>
      <div class="col-md-3">
        <label class="form-label" for="sb-thu-tu">Thứ tự hiển thị</label>
        <input type="number" class="form-control form-control-sm" id="sb-thu-tu" name="thu_tu_hien_thi" value="<?= (int) $formData['thu_tu_hien_thi'] ?>" min="0" step="1" />
      </div>
      <div class="col-md-3">
        <label class="form-label" for="sb-trang-thai">Trạng thái</label>
        <select class="form-select form-select-sm" id="sb-trang-thai" name="trang_thai">
          <option value="Hoat_Dong" <?= $formData['trang_thai'] === 'Hoat_Dong' ? 'selected' : '' ?>>Hoạt động</option>
          <option value="Bao_Tri" <?= $formData['trang_thai'] === 'Bao_Tri' ? 'selected' : '' ?>>Bảo trì</option>
          <option value="Dong_Cua_Khan_Cap" <?= $formData['trang_thai'] === 'Dong_Cua_Khan_Cap' ? 'selected' : '' ?>>Đóng cửa khẩn cấp</option>
        </select>
      </div>

      <div class="col-12">
        <label class="form-label" for="sb-mo-ta">Mô tả</label>
        <textarea class="form-control form-control-sm" id="sb-mo-ta" name="mo_ta" rows="2" maxlength="500" placeholder="Thông tin ngắn về sân bay..."><?= $e($formData['mo_ta']) ?></textarea>
      </div>
    </div>

    <div class="d-flex flex-wrap gap-2 mt-3">
      <button type="submit" class="btn btn-primary btn-sm">
        <i class="bi bi-check2 me-1"></i> <?= $isEdit ? 'Lưu thay đổi' : 'Thêm sân bay' ?>
      </button>
      <a href="<?= $e($listUrl) ?>" class="btn btn-outline-secondary btn-sm">Hủy</a>
    </div>
  </form>
</div>
