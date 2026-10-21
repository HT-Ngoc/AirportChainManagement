<?php
/**
 * ============================================================
 * LAYOUT: SIDEBAR (AdminLTE 4)
 * ------------------------------------------------------------
 * Menu chính của hệ thống, chia theo 5 phân hệ tương ứng 5
 * thành viên thực hiện. Biến $navActive dùng để tô đậm mục
 * đang mở (nhận 1 trong các giá trị: ha_tang, chuyen_bay,
 * bao_tri, ke_toan, auth).
 * ============================================================
 */

$navActive   = $navActive ?? '';

// Nạp cấu hình dùng chung (BASE_URL, ADMINLTE_URL)
require_once __DIR__ . '/../../config/config.php';
$baseUrl     = $baseUrl ?? BASE_URL;
$adminLteUrl = $adminLteUrl ?? ADMINLTE_URL;

/** Tiện ích: trả về ' active' nếu menu đang được chọn */
$isActive = static function (string $key) use ($navActive): string {
    return $navActive === $key ? ' active' : '';
};
?>
<!--begin::Sidebar-->
<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
  <!--begin::Sidebar Brand-->
  <div class="sidebar-brand">
    <a href="<?= $baseUrl ?>/index.php" class="brand-link">
      <img
        src="<?= $adminLteUrl ?>/assets/img/AdminLTELogo.png"
        alt="Logo"
        class="brand-image opacity-75 shadow"
      />
      <span class="brand-text fw-light">ACV Airports</span>
    </a>
  </div>
  <!--end::Sidebar Brand-->

  <!--begin::Sidebar Search-->
  <div class="sidebar-search" role="search">
    <label for="sidebar-search-input" class="visually-hidden">Lọc menu</label>
    <input
      type="search"
      id="sidebar-search-input"
      class="form-control form-control-sm"
      placeholder="Lọc menu…"
      autocomplete="off"
      data-lte-toggle="sidebar-search"
      data-lte-target="#navigation"
    />
  </div>
  <!--end::Sidebar Search-->

  <!--begin::Sidebar Wrapper-->
  <div class="sidebar-wrapper">
    <nav class="mt-2" aria-label="Menu chính">
      <ul
        class="nav sidebar-menu flex-column"
        data-lte-toggle="treeview"
        data-accordion="false"
        id="navigation"
      >
        <li class="nav-item">
          <a href="<?= $baseUrl ?>/index.php" class="nav-link">
            <i class="nav-icon bi bi-speedometer2"></i>
            <p>Tổng quan</p>
          </a>
        </li>

        <li class="nav-header">PHÂN HỆ CHỨC NĂNG</li>

        <!--Thành viên 1 - Xác thực & tài khoản-->
        <li class="nav-item">
          <a href="#" class="nav-link<?= $isActive('auth') ?>">
            <i class="nav-icon bi bi-person-badge"></i>
            <p>
              Tài khoản &amp; phân quyền
              <i class="nav-arrow bi bi-chevron-right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="<?= $baseUrl ?>/index.php?page=login" class="nav-link">
                <i class="nav-icon bi bi-circle"></i>
                <p>Đăng nhập</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?= $baseUrl ?>/index.php?page=register" class="nav-link">
                <i class="nav-icon bi bi-circle"></i>
                <p>Đăng ký</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?= $baseUrl ?>/index.php?page=forgot" class="nav-link">
                <i class="nav-icon bi bi-circle"></i>
                <p>Quên mật khẩu</p>
              </a>
            </li>
          </ul>
        </li>

        <!--Thành viên 2 - Hạ tầng-->
        <li class="nav-item">
          <a href="<?= $baseUrl ?>/index.php?page=ha_tang" class="nav-link<?= $isActive('ha_tang') ?>">
            <i class="nav-icon bi bi-buildings"></i>
            <p>Quản lý hạ tầng</p>
          </a>
        </li>

        <!--Thành viên 3 - Chuyến bay-->
        <li class="nav-item">
          <a href="<?= $baseUrl ?>/index.php?page=chuyen_bay" class="nav-link<?= $isActive('chuyen_bay') ?>">
            <i class="nav-icon bi bi-airplane-engines"></i>
            <p>Quản lý chuyến bay</p>
          </a>
        </li>

        <!--Thành viên 4 - Bảo trì-->
        <li class="nav-item">
          <a href="<?= $baseUrl ?>/index.php?page=bao_tri" class="nav-link<?= $isActive('bao_tri') ?>">
            <i class="nav-icon bi bi-tools"></i>
            <p>Bảo trì &amp; khắc phục</p>
          </a>
        </li>

        <!--Thành viên 5 - Kế toán-->
        <li class="nav-item">
          <a href="<?= $baseUrl ?>/index.php?page=ke_toan" class="nav-link<?= $isActive('ke_toan') ?>">
            <i class="nav-icon bi bi-cash-coin"></i>
            <p>Kế toán &amp; doanh thu</p>
          </a>
        </li>
      </ul>
    </nav>
  </div>
  <!--end::Sidebar Wrapper-->
</aside>
<!--end::Sidebar-->
