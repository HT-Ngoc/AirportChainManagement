<?php
/**
 * ============================================================
 * TRANG: ĐĂNG KÝ  —  views/pages/auth/register.php
 * Phân hệ: Xác thực & tài khoản (Thành viên 1)
 * ------------------------------------------------------------
 * Ghi chú cho Thành viên 1:
 *   - Bổ sung kiểm tra dữ liệu (validate) và CSRF token khi ghép
 *     với AuthController.
 *   - Đổ lỗi vào biến $error để hiển thị thông báo.
 * ============================================================
 */

$pageTitle = 'Đăng ký tài khoản';
$authTitle = 'Đăng ký tài khoản mới';
$authMsg   = 'Tài khoản sau khi tạo sẽ được gán vai trò bởi quản trị viên';

$error = $error ?? '';

ob_start();
?>
          <?php if ($error !== ''): ?>
            <div class="alert alert-danger py-2" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
          <?php endif; ?>

          <form action="#" method="post" novalidate>
            <label class="visually-hidden" for="registerFullname">Họ và tên</label>
            <div class="input-group mb-3">
              <input
                id="registerFullname"
                name="fullname"
                type="text"
                class="form-control"
                placeholder="Họ và tên"
                required
              />
              <div class="input-group-text"><span class="bi bi-person-badge"></span></div>
            </div>

            <label class="visually-hidden" for="registerEmail">Email</label>
            <div class="input-group mb-3">
              <input
                id="registerEmail"
                name="email"
                type="email"
                class="form-control"
                placeholder="Email"
                autocomplete="email"
                required
              />
              <div class="input-group-text"><span class="bi bi-envelope"></span></div>
            </div>

            <label class="visually-hidden" for="registerPassword">Mật khẩu</label>
            <div class="input-group mb-3">
              <input
                id="registerPassword"
                name="password"
                type="password"
                class="form-control"
                placeholder="Mật khẩu"
                autocomplete="new-password"
                required
              />
              <div class="input-group-text"><span class="bi bi-lock-fill"></span></div>
            </div>

            <label class="visually-hidden" for="registerConfirmPassword">Nhập lại mật khẩu</label>
            <div class="input-group mb-3">
              <input
                id="registerConfirmPassword"
                name="password_confirm"
                type="password"
                class="form-control"
                placeholder="Nhập lại mật khẩu"
                autocomplete="new-password"
                required
              />
              <div class="input-group-text"><span class="bi bi-shield-lock"></span></div>
            </div>

            <div class="form-check mb-3">
              <input class="form-check-input" type="checkbox" name="agree" value="1" id="agreeTerms" required />
              <label class="form-check-label" for="agreeTerms">Tôi đồng ý với quy định sử dụng hệ thống</label>
            </div>

            <div class="d-grid gap-2">
              <button type="submit" class="btn btn-primary">Đăng ký</button>
            </div>
          </form>

          <p class="mb-0 mt-3">
            <a href="/AirportChainManagement/index.php?page=login">Tôi đã có tài khoản</a>
          </p>
<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/auth.php';
