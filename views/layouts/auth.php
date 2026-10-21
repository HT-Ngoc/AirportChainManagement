<?php
/**
 * ============================================================
 * LAYOUT XÁC THỰC (dùng cho các trang trong views/pages/auth/)
 * ------------------------------------------------------------
 * Khung trang riêng cho màn hình đăng nhập / đăng ký / quên
 * mật khẩu: không có navbar và sidebar, chỉ có form ở giữa.
 *
 * CÁCH SỬ DỤNG:
 *   $pageTitle = 'Đăng nhập';
 *   $authTitle = 'Đăng nhập hệ thống';
 *   $authMsg   = 'Nhập tài khoản để bắt đầu phiên làm việc';
 *   ob_start();
 *   ?>
 *   ... HTML của form ...
 *   <?php
 *   $content = ob_get_clean();
 *   require __DIR__ . '/../../layouts/auth.php';
 * ============================================================
 */

$pageTitle   = $pageTitle ?? 'Xác thực';
$authTitle   = $authTitle ?? 'ACV Airports';
$authMsg     = $authMsg ?? '';
$content     = $content ?? '';
$pageScripts = $pageScripts ?? '';

// Nạp cấu hình dùng chung (BASE_URL, ADMINLTE_URL)
require_once __DIR__ . '/../../config/config.php';
$baseUrl     = $baseUrl ?? BASE_URL;
$adminLteUrl = $adminLteUrl ?? ADMINLTE_URL;
?>
<!doctype html>
<html lang="vi">
  <head>
    <meta charset="utf-8" />
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="color-scheme" content="light dark" />

    <!--begin::Fonts-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.3.0/index.css"
      crossorigin="anonymous"
      media="print"
      onload="this.media = 'all'"
    />
    <!--end::Fonts-->

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
    <!--end::Required Plugin-->
  </head>

  <body class="login-page bg-body-secondary">
    <main class="login-box">
      <!--begin::Logo-->
      <h1 class="login-logo">
        <a href="<?= $baseUrl ?>/index.php"><b>ACV</b> Airports</a>
      </h1>
      <!--end::Logo-->

      <div class="card">
        <div class="card-body login-card-body">
          <p class="login-box-msg fw-semibold"><?= htmlspecialchars($authTitle, ENT_QUOTES, 'UTF-8') ?></p>
          <?php if ($authMsg !== ''): ?>
            <p class="text-secondary small text-center"><?= htmlspecialchars($authMsg, ENT_QUOTES, 'UTF-8') ?></p>
          <?php endif; ?>

          <?= $content ?>
        </div>
      </div>
    </main>

    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js"
      crossorigin="anonymous"
    ></script>
    <script src="<?= $adminLteUrl ?>/js/adminlte.js"></script>
    <?= $pageScripts ?>
  </body>
</html>
