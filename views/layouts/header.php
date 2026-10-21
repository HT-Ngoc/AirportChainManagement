<?php
/**
 * ============================================================
 * LAYOUT: HEADER (AdminLTE 4)
 * ------------------------------------------------------------
 * File này mở đầu trang: <head>, thanh navbar trên cùng và
 * sidebar. Nó KHÔNG đóng thẻ, phần thân trang nằm ở main.php
 * và phần kết thúc nằm ở footer.php
 *
 * Biến có thể truyền vào (đều có giá trị mặc định):
 *   $pageTitle   : Tiêu đề tab trình duyệt
 *   $navActive   : Key menu đang mở (auth|ha_tang|chuyen_bay|bao_tri|ke_toan)
 *   $adminLteUrl : Đường dẫn tới thư mục dist của AdminLTE
 * ============================================================
 */

$pageTitle   = $pageTitle ?? 'Hệ thống quản lý chuỗi cảng hàng không';
$navActive   = $navActive ?? '';

// Nạp cấu hình dùng chung (BASE_URL, ADMINLTE_URL)
require_once __DIR__ . '/../../config/config.php';
$baseUrl     = $baseUrl ?? BASE_URL;
$adminLteUrl = $adminLteUrl ?? ADMINLTE_URL;

/* Thông tin người đang đăng nhập (do AuthController lưu vào session) */
$daDangNhap   = !empty($_SESSION['user_id']);
$tenHienThi   = $daDangNhap ? (string) $_SESSION['ho_ten'] : 'Chưa đăng nhập';
$nhanVaiTro   = [
    'Admin_Tong_Cong_Ty'   => 'Quản trị tổng công ty',
    'Quan_Ly_San_Bay'      => 'Quản lý sân bay',
    'Nhan_Vien_Dieu_Hanh'  => 'Nhân viên điều hành',
    'Ke_Toan'              => 'Kế toán',
];
$vaiTroHienThi = $daDangNhap
    ? ($nhanVaiTro[$_SESSION['vai_tro'] ?? ''] ?? (string) ($_SESSION['vai_tro'] ?? ''))
    : 'Khách';
?>
<!doctype html>
<html lang="vi">
  <head>
    <meta charset="utf-8" />
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>

    <!--begin::Theme Init (tránh nháy sáng/tối khi tải trang)-->
    <script>
      (() => {
        'use strict';
        const root = document.documentElement;
        if (root.getAttribute('data-lte-color-mode') === 'off') return;
        const STORAGE_KEY = 'lte-theme';
        let stored = null;
        try {
          stored = localStorage.getItem(STORAGE_KEY);
        } catch {
          /* localStorage có thể bị chặn */
        }
        const authored = root.getAttribute('data-bs-theme');
        let resolved = 'light';
        if (stored === 'dark' || stored === 'light') {
          resolved = stored;
        } else if (authored === 'dark' || authored === 'light') {
          resolved = authored;
        } else if (globalThis.matchMedia('(prefers-color-scheme: dark)').matches) {
          resolved = 'dark';
        }
        root.setAttribute('data-bs-theme', resolved);
        root.style.colorScheme = resolved;
        if (resolved !== authored) root.setAttribute('data-lte-theme-resolved', '');
      })();
    </script>
    <!--end::Theme Init-->

    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="color-scheme" content="light dark" />
    <meta name="description" content="Hệ thống quản lý điều hành khai thác chuỗi cảng hàng không (ACV)" />

    <!--begin::Fonts-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.3.0/index.css"
      crossorigin="anonymous"
      media="print"
      onload="this.media = 'all'"
    />
    <!--end::Fonts-->

    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.16.0/styles/overlayscrollbars.min.css"
      crossorigin="anonymous"
    />
    <!--end::Third Party Plugin-->

    <!--begin::Third Party Plugin(Bootstrap Icons)-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
      crossorigin="anonymous"
    />
    <!--end::Third Party Plugin-->

    <!--begin::CSS dùng chung của dự án-->
    <link rel="stylesheet" href="<?= $baseUrl ?>/assets/css/style.css" />
    <!--end::CSS dùng chung-->

    <!--begin::Required Plugin(AdminLTE)-->
    <link rel="stylesheet" href="<?= $adminLteUrl ?>/css/adminlte.css" />
    <!--end::Required Plugin(AdminLTE)-->
  </head>

  <body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <div class="app-wrapper">
      <!--begin::Header-->
      <nav class="app-header navbar navbar-expand bg-body">
        <div class="container-fluid">
          <ul class="navbar-nav">
            <li class="nav-item">
              <a
                class="nav-link"
                data-lte-toggle="sidebar"
                href="#"
                role="button"
                aria-label="Mở/đóng menu"
              >
                <i class="bi bi-list"></i>
              </a>
            </li>
            <li class="nav-item d-none d-md-block">
              <a href="<?= $baseUrl ?>/index.php" class="nav-link">
                <i class="bi bi-grid-1x2 me-1" aria-hidden="true"></i>
                Tổng quan
              </a>
            </li>
          </ul>

          <ul class="navbar-nav ms-auto">
            <!--begin::Fullscreen Toggle-->
            <li class="nav-item">
              <a class="nav-link" href="#" data-lte-toggle="fullscreen" aria-label="Toàn màn hình">
                <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i>
                <i data-lte-icon="minimize" class="bi bi-fullscreen-exit d-none"></i>
              </a>
            </li>
            <!--end::Fullscreen Toggle-->

            <!--begin::Color Mode Toggle-->
            <li class="nav-item dropdown">
              <a
                class="nav-link"
                href="#"
                id="bd-theme"
                aria-label="Chọn chế độ màu"
                data-bs-toggle="dropdown"
                aria-expanded="false"
              >
                <i class="bi bi-sun-fill" data-lte-theme-icon="light"></i>
                <i class="bi bi-moon-fill d-none" data-lte-theme-icon="dark"></i>
                <i class="bi bi-circle-half d-none" data-lte-theme-icon="auto"></i>
              </a>
              <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="bd-theme">
                <li>
                  <button type="button" class="dropdown-item" data-bs-theme-value="light">
                    <i class="bi bi-sun-fill me-2"></i> Sáng
                  </button>
                </li>
                <li>
                  <button type="button" class="dropdown-item" data-bs-theme-value="dark">
                    <i class="bi bi-moon-fill me-2"></i> Tối
                  </button>
                </li>
                <li>
                  <button type="button" class="dropdown-item active" data-bs-theme-value="auto">
                    <i class="bi bi-circle-half me-2"></i> Tự động
                  </button>
                </li>
              </ul>
            </li>
            <!--end::Color Mode Toggle-->

            <!--begin::User Menu Dropdown (dữ liệu mẫu, Thành viên 1 thay bằng session thật)-->
            <li class="nav-item dropdown user-menu">
              <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                <img
                  src="<?= $adminLteUrl ?>/assets/img/user2-160x160.jpg"
                  class="user-image rounded-circle shadow"
                  alt="Ảnh người dùng"
                />
                <span class="d-none d-xl-inline"><?= htmlspecialchars($tenHienThi, ENT_QUOTES, 'UTF-8') ?></span>
              </a>
              <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                <li class="user-header text-bg-primary">
                  <img
                    src="<?= $adminLteUrl ?>/assets/img/user2-160x160.jpg"
                    class="rounded-circle shadow"
                    alt="Ảnh người dùng"
                  />
                  <p>
                    <?= htmlspecialchars($tenHienThi, ENT_QUOTES, 'UTF-8') ?>
                    <small><?= htmlspecialchars($vaiTroHienThi, ENT_QUOTES, 'UTF-8') ?></small>
                  </p>
                </li>
                <li class="user-footer">
                  <a href="#" class="btn btn-outline-secondary">Hồ sơ</a>
                  <?php if ($daDangNhap): ?>
                    <a href="<?= $baseUrl ?>/controllers/AuthController.php?action=logout" class="btn btn-outline-danger float-end">
                      Đăng xuất
                    </a>
                  <?php else: ?>
                    <a href="<?= $baseUrl ?>/index.php?page=login" class="btn btn-outline-primary float-end">
                      Đăng nhập
                    </a>
                  <?php endif; ?>
                </li>
              </ul>
            </li>
            <!--end::User Menu Dropdown-->
          </ul>
        </div>
      </nav>
      <!--end::Header-->

      <?php require __DIR__ . '/sidebar.php'; ?>
