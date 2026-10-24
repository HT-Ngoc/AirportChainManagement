<?php
$airport = $selectedAirport;
$items = $data['terminals'];
?>
<table class="table ht-table align-middle">
  <thead>
    <tr>
      <th>Mã</th>
      <th>Nhà ga</th>
      <th>Loại</th>
      <th>Khai thác</th>
      <th>Diện tích / Công suất</th>
      <th>Tài nguyên</th>
      <th>Trạng thái</th>
      <th class="text-end">Thao tác</th>
    </tr>
  </thead>
  <tbody>
    <?php if (!$items): ?>
      <tr>
        <td colspan="8">
          <div class="ht-empty border-0">
            <i class="bi bi-door-closed"></i>
            <h3>Sân bay chưa có nhà ga</h3>
            <p>Thêm nhà ga đầu tiên cho <?= $e($airport['ten_san_bay']) ?> để cấu hình tài nguyên khai thác.</p>
            <a href="<?= $e($addUrl) ?>" class="btn btn-primary btn-sm mt-2">Thêm nhà ga</a>
          </div>
        </td>
      </tr>
    <?php else: ?>
      <?php foreach ($items as $item): ?>
        <?php $isSelected = $selectedTerminal && (int) $selectedTerminal['id'] === (int) $item['id']; ?>
        <tr class="<?= $isSelected ? 'table-active' : '' ?>">
          <td><span class="badge text-bg-secondary"><?= $e($item['ma_nha_ga']) ?></span></td>
          <td>
            <div class="fw-semibold"><?= $e($item['ten_nha_ga']) ?></div>
            <?php if (!empty($item['mo_ta'])): ?>
              <span class="ht-soft-label"><?= $e($item['mo_ta']) ?></span>
            <?php endif; ?>
          </td>
          <td><?= $terminalTypeBadge($item['loai_nha_ga']) ?></td>
          <td>
            <span class="ht-soft-label"><?= $item['nam_khai_thac'] ? (int) $item['nam_khai_thac'] : '—' ?></span>
          </td>
          <td>
            <span class="ht-soft-label">DT: <strong><?= !empty($item['dien_tich']) ? $e($item['dien_tich']) : '—' ?></strong></span>
            <span class="ht-soft-label">CS: <strong><?= !empty($item['cong_suat']) ? $e($item['cong_suat']) : '—' ?></strong></span>
          </td>
          <td>
            <span class="ht-soft-label"><?= (int) $item['so_cua_ra'] ?> cửa · <?= (int) $item['so_quay_checkin'] ?> quầy</span>
            <span class="ht-soft-label"><?= (int) $item['so_bang_chuyen'] ?> băng chuyền</span>
          </td>
          <td><?= $statusBadge($item['trang_thai']) ?></td>
          <td>
            <div class="ht-actions">
              <a
                href="<?= $e($adminUrl(['tab' => 'nha_ga', 'san_bay' => $airport['ma_iata'], 'nha_ga' => (int) $item['id'], 'sua' => (int) $item['id']])) ?>"
                class="btn btn-sm btn-outline-secondary"
              >
                <i class="bi bi-pencil"></i> Sửa
              </a>
              <form method="post" action="<?= $e(BASE_URL . '/index.php?page=ha_tang') ?>" class="d-inline" onsubmit="return confirm('Xóa nhà ga này? Các cửa ra, quầy check-in và băng chuyền trực thuộc cũng sẽ bị xóa.');">
                <input type="hidden" name="csrf" value="<?= $e($data['csrf']) ?>" />
                <input type="hidden" name="action" value="xoa_nha_ga" />
                <input type="hidden" name="id" value="<?= (int) $item['id'] ?>" />
                <input type="hidden" name="return_tab" value="nha_ga" />
                <input type="hidden" name="return_san_bay" value="<?= $e($airport['ma_iata']) ?>" />
                <input type="hidden" name="return_nha_ga" value="<?= (int) $returnNhaGa ?>" />
                <button type="submit" class="btn btn-sm btn-outline-danger">
                  <i class="bi bi-trash"></i> Xóa
                </button>
              </form>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php endif; ?>
  </tbody>
</table>
