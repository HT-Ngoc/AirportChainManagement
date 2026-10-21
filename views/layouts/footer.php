<?php
/**
 * ============================================================
 * LAYOUT: FOOTER (AdminLTE 4)
 * ------------------------------------------------------------
 * Đóng phần nội dung, in chân trang và nạp toàn bộ thư viện JS
 * (OverlayScrollbars, Popper, Bootstrap, AdminLTE).
 * Mọi trang nội dung nên nhúng thêm <script> riêng bằng biến
 * $pageScripts trước khi gọi layout này trong main.php.
 * ============================================================
 */

// Nạp cấu hình dùng chung (phòng trường hợp footer được nhúng độc lập)
require_once __DIR__ . '/../../config/config.php';

$adminLteUrl  = $adminLteUrl ?? ADMINLTE_URL;
$pageScripts  = $pageScripts ?? '';
?>
      <!--begin::Footer-->
      <footer class="app-footer">
        <div class="float-end d-none d-sm-inline">Hệ thống quản lý chuỗi cảng hàng không</div>
        <strong>
          Copyright &copy; <?= date('Y') ?> <a href="#" class="text-decoration-none">Nhóm đồ án</a>.
        </strong>
        Bảo lưu mọi quyền.
      </footer>
      <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->

    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <script
      src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.16.0/browser/overlayscrollbars.browser.es6.min.js"
      crossorigin="anonymous"
    ></script>
    <!--end::Third Party Plugin-->
    <!--begin::Required Plugin(popperjs for Bootstrap 5)-->
    <script
      src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
      crossorigin="anonymous"
    ></script>
    <!--end::Required Plugin-->
    <!--begin::Required Plugin(Bootstrap 5)-->
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js"
      crossorigin="anonymous"
    ></script>
    <!--end::Required Plugin-->
    <!--begin::Required Plugin(AdminLTE)-->
    <script src="<?= $adminLteUrl ?>/js/adminlte.js"></script>
    <!--end::Required Plugin-->

    <!--begin::OverlayScrollbars Configure-->
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        const sidebarWrapper = document.querySelector('.sidebar-wrapper');
        const isMobile = window.innerWidth <= 992;
        if (
          sidebarWrapper &&
          globalThis.OverlayScrollbarsGlobal?.OverlayScrollbars !== undefined &&
          !isMobile
        ) {
          OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
            scrollbars: {
              theme: 'os-theme-light',
              autoHide: 'leave',
              clickScroll: true,
            },
          });
        }
      });
    </script>
    <!--end::OverlayScrollbars Configure-->

    <?= $pageScripts ?>
  </body>
</html>
