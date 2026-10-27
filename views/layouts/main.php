<?php
/**
 * ============================================================
 * LAYOUT CHÍNH (AdminLTE 4)
 * ------------------------------------------------------------
 * Khung trang dùng chung cho tất cả các trang nội dung có
 * sidebar (mọi trang trong views/pages, trừ auth/).
 *
 * LƯU Ý: giao diện KHÔNG dùng JavaScript. Không còn biến
 * $pageScripts — mọi tương tác dùng CSS (:target, <details>,
 * :hover/:focus-within) hoặc xử lý phía PHP.
 *
 * CÁCH SỬ DỤNG trong 1 file trang:
 *
 *   $pageTitle    = 'Danh sách chuyến bay';
 *   $pageHeader   = 'Danh sách chuyến bay';
 *   $navActive    = 'chuyen_bay';
 *   $breadcrumb   = [
 *       ['label' => 'Tổng quan', 'url' => '/AirportChainManagement/index.php'],
 *       ['label' => 'Chuyến bay', 'url' => null],
 *   ];
 *
 *   ob_start();
 *   ?>
 *   ... HTML nội dung ...
 *   <?php
 *   $content = ob_get_clean();
 *   require __DIR__ . '/../../layouts/main.php';
 * ============================================================
 */

$pageTitle  = $pageTitle ?? 'Hệ thống quản lý chuỗi cảng hàng không';
$pageHeader = $pageHeader ?? $pageTitle;
$breadcrumb = $breadcrumb ?? [];
$content    = $content ?? '';

require __DIR__ . '/header.php';
?>
      <!--begin::App Main-->
      <main class="app-main">
        <!--begin::App Content Header-->
        <div class="app-content-header">
          <div class="container-fluid">
            <div class="row">
              <div class="col-sm-6">
                <h1 class="mb-0 fs-3"><?= htmlspecialchars($pageHeader, ENT_QUOTES, 'UTF-8') ?></h1>
              </div>
              <div class="col-sm-6">
                <?php if ($breadcrumb): ?>
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb float-sm-end">
                      <?php foreach ($breadcrumb as $item): ?>
                        <?php $label = htmlspecialchars($item['label'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                        <?php if (!empty($item['url'])): ?>
                          <li class="breadcrumb-item"><a href="<?= $item['url'] ?>"><?= $label ?></a></li>
                        <?php else: ?>
                          <li class="breadcrumb-item active" aria-current="page"><?= $label ?></li>
                        <?php endif; ?>
                      <?php endforeach; ?>
                    </ol>
                  </nav>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
        <!--end::App Content Header-->

        <!--begin::App Content-->
        <div class="app-content">
          <div class="container-fluid">
            <?= $content ?>
          </div>
        </div>
        <!--end::App Content-->
      </main>
      <!--end::App Main-->
<?php require __DIR__ . '/footer.php'; ?>
