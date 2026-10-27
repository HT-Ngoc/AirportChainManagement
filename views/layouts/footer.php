<?php
/**
 * ============================================================
 * LAYOUT: FOOTER (AdminLTE 4)
 * ------------------------------------------------------------
 * Đóng phần nội dung và in chân trang kiểu AdminLTE mặc định.
 *
 * KHÔNG nạp bất kỳ thư viện JavaScript nào
 * (OverlayScrollbars, Popper, Bootstrap JS, AdminLTE JS đều
 * đã được gỡ bỏ khỏi dự án).
 * ============================================================
 */

// Nạp cấu hình dùng chung (phòng trường hợp footer được nhúng độc lập)
require_once __DIR__ . '/../../config/config.php';
?>
      <!--begin::Footer-->
      <footer class="app-footer">
        <div class="float-end d-none d-sm-inline">Hệ thống quản lý chuỗi cảng hàng không</div>
        <strong>
          Copyright &copy; <?= date('Y') ?> <a href="<?= BASE_URL ?>/index.php" class="text-decoration-none">Nhóm đồ án</a>.
        </strong>
        Bảo lưu mọi quyền.
      </footer>
      <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->
  </body>
</html>
