<?php
/**
 * Hiển thị thông báo flash riêng cho phân hệ Bảo trì.
 * Controller ghi $_SESSION['flash_success'] / flash_error / flash_info,
 * sau khi hiển thị sẽ xóa khỏi session để không lặp lại.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$flashMessages = [
    'flash_success' => 'success',
    'flash_error'   => 'danger',
    'flash_info'    => 'info',
];

foreach ($flashMessages as $flashKey => $flashType):
    if (empty($_SESSION[$flashKey])) {
        continue;
    }
    $flashText = (string) $_SESSION[$flashKey];
    unset($_SESSION[$flashKey]);
?>
<div class="alert alert-<?= $flashType ?> alert-dismissible fade show" role="alert">
    <?= htmlspecialchars($flashText, ENT_QUOTES, 'UTF-8') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
</div>
<?php endforeach; ?>
