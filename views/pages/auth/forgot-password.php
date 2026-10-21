<?php
/**
 * ============================================================
 * TRANG: QUÊN MẬT KHẨU  —  views/pages/auth/forgot-password.php
 * Phân hệ: Xác thực & tài khoản (Thành viên 1)
 * ------------------------------------------------------------
 * Ghi chú cho Thành viên 1:
 *   - Luồng đề xuất: nhập email -> sinh token -> gửi mail/hoặc
 *     hiển thị liên kết đặt lại mật khẩu (bản demo).
 *   - Đổ thông báo vào $message và lỗi vào $error.
 * ============================================================
 */

$pageTitle = 'Quên mật khẩu';
$authTitle = 'Khôi phục mật khẩu';
$authMsg   = 'Nhập email đã đăng ký để nhận hướng dẫn đặt lại mật khẩu';

$error   = $error ?? '';
$message = $message ?? '';

ob_start();
?>
          <?php if ($error !== ''): ?>
            <div class="alert alert-danger py-2" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
          <?php endif; ?>
          <?php if ($message !== ''): ?>
            <div class="alert alert-success py-2" role="alert"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
          <?php endif; ?>

          <form action="#" method="post" novalidate>
            <label class="visually-hidden" for="forgotEmail">Email</label>
            <div class="input-group mb-3">
              <input
                id="forgotEmail"
                name="email"
                type="email"
                class="form-control"
                placeholder="Email"
                autocomplete="email"
                required
              />
              <div class="input-group-text"><span class="bi bi-envelope"></span></div>
            </div>

            <div class="d-grid gap-2">
              <button type="submit" class="btn btn-primary">Gửi yêu cầu</button>
            </div>
          </form>

          <p class="mb-0 mt-3">
            <a href="/AirportChainManagement/index.php?page=login">Quay lại trang đăng nhập</a>
          </p>
<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/auth.php';
