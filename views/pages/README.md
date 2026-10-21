# Thư mục views/pages — phân công theo thành viên

Mỗi thư mục con tương ứng với một phân hệ do một thành viên phụ trách.
Các file `.php` bên trong đã là **khung trang mẫu dùng AdminLTE 4**, chỉ cần
thay dữ liệu mẫu bằng dữ liệu thật do controller truyền vào.

| Thư mục | Thành viên | Phân hệ | File mẫu |
| --- | --- | --- | --- |
| `auth/` | Thành viên 1 | Xác thực & tài khoản | `login.php`, `register.php`, `forgot-password.php` |
| `ha_tang/` | Thành viên 2 | Quản lý hạ tầng (nhà ga, đường băng, sân đỗ) | `index.php` |
| `chuyen_bay/` | Thành viên 3 | Quản lý chuyến bay | `index.php` |
| `bao_tri/` | Thành viên 4 | Bảo trì & khắc phục | `index.php` |
| `ke_toan/` | Thành viên 5 | Kế toán & doanh thu | `index.php` |

## Cách viết thêm một trang mới

```php
<?php
$pageTitle  = 'Tên trang';
$pageHeader = 'Tiêu đề hiển thị';
$navActive  = 'chuyen_bay';   // key menu cần tô đậm
$breadcrumb = [
    ['label' => 'Tổng quan', 'url' => '/AirportChainManagement/index.php'],
    ['label' => 'Tên trang', 'url' => null],
];

ob_start(); ?>
  ... HTML nội dung (dùng class của Bootstrap 5 / AdminLTE 4) ...
<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/main.php';
```

- Trang **không có sidebar** (đăng nhập, đăng ký, quên mật khẩu): thay
  `layouts/main.php` bằng `layouts/auth.php` và dùng thêm các biến
  `$authTitle`, `$authMsg`.
- Layout dùng chung nằm ở `views/layouts/`: `header.php`, `sidebar.php`,
  `footer.php`, `main.php`, `auth.php`.
- Thư viện giao diện: `admin-lte-v4.10.1/dist` (đã có sẵn trong dự án; nếu thiếu
  thư mục này thì `config/config.php` tự chuyển sang CDN jsDelivr).

## Luồng đăng nhập (đã chạy được)

| Thành phần | File |
| --- | --- |
| Kết nối CSDL (dùng chung) | `config/database.php` → biến `$conn` |
| Model người dùng | `models/NguoiDung.php` → `NguoiDung::kiemTraDangNhap()` |
| Controller xác thực | `controllers/AuthController.php` |
| Giao diện đăng nhập | `views/pages/auth/login.php` |

- Form đăng nhập POST tới `<?= BASE_URL ?>/controllers/AuthController.php` với
  `name="btn_login"`.
- Controller kiểm tra mật khẩu bằng `password_verify()` với cột `mat_khau_hash`
  (bcrypt) trong bảng `nguoi_dung`, rồi lưu `user_id`, `ho_ten`, `vai_tro`,
  `id_san_bay` vào `$_SESSION`.
- Đăng xuất: `<?= BASE_URL ?>/controllers/AuthController.php?action=logout`.
- Header (`views/layouts/header.php`) tự hiển thị tên + vai trò người đăng nhập.
- Tài khoản kiểm thử: `kiem_thu_login` / `123456`
  (xóa khi không cần: `DELETE FROM nguoi_dung WHERE ten_dang_nhap = 'kiem_thu_login';`).

