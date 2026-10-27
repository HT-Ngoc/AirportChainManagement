<?php
/**
 * Bootstrap riêng cho phân hệ Bảo trì.
 * Cho phép các view trong thư mục này tự chạy mà không cần sửa index.php tổng.
 */
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../controllers/BaoTriController.php';

if (!function_exists('baoTriUrl')) {
    /**
     * Tạo URL tuyệt đối tới một file trong views/pages/bao_tri/.
     */
    function baoTriUrl($file = 'index.php', array $query = []) {
        $base = defined('BASE_URL') ? rtrim(BASE_URL, '/') : '';
        $url  = $base . '/views/pages/bao_tri/' . ltrim($file, '/');

        if (!empty($query)) {
            $url .= '?' . http_build_query($query);
        }

        return $url;
    }
}

if (!isset($baoTriController) || !($baoTriController instanceof BaoTriController)) {
    $dbConn = $dbConnection ?? $db ?? $conn ?? null;

    if (!$dbConn) {
        die('Không thể kết nối cơ sở dữ liệu.');
    }

    $baoTriController = new BaoTriController($dbConn);
}
