<?php
/**
 * ============================================================
 * TRANG: QUẢN LÝ HẠ TẦNG  —  views/pages/ha_tang/index.php
 * Phân hệ: Hạ tầng cảng hàng không (Thành viên 2)
 * ------------------------------------------------------------
 * Phạm vi gợi ý: nhà ga, đường băng, sân đỗ, trang thiết bị cố định.
 * Ghi chú cho Thành viên 2:
 *   - Số liệu trong các thẻ (small-box) và bảng dưới đây là dữ liệu
 *     MẪU, thay bằng dữ liệu thật do HaTangController truyền vào.
 * ============================================================
 */

$pageTitle  = 'Quản lý hạ tầng';
$pageHeader = 'Quản lý hạ tầng';
$navActive  = 'ha_tang';
$breadcrumb = [
    ['label' => 'Tổng quan', 'url' => '/AirportChainManagement/index.php'],
    ['label' => 'Hạ tầng', 'url' => null],
];

ob_start();
?>
            <!--begin::Thống kê nhanh-->
            <div class="row">
              <div class="col-lg-3 col-6">
                <div class="small-box text-bg-primary">
                  <div class="inner">
                    <h3>21</h3>
                    <p>Sân bay trong chuỗi</p>
                  </div>
                  <i class="small-box-icon bi bi-building"></i>
                </div>
              </div>
              <div class="col-lg-3 col-6">
                <div class="small-box text-bg-success">
                  <div class="inner">
                    <h3>48</h3>
                    <p>Nhà ga đang khai thác</p>
                  </div>
                  <i class="small-box-icon bi bi-door-open"></i>
                </div>
              </div>
              <div class="col-lg-3 col-6">
                <div class="small-box text-bg-warning">
                  <div class="inner">
                    <h3>7</h3>
                    <p>Đường băng cần theo dõi</p>
                  </div>
                  <i class="small-box-icon bi bi-signpost-2"></i>
                </div>
              </div>
              <div class="col-lg-3 col-6">
                <div class="small-box text-bg-danger">
                  <div class="inner">
                    <h3>3</h3>
                    <p>Hạng mục đang sửa chữa</p>
                  </div>
                  <i class="small-box-icon bi bi-cone-striped"></i>
                </div>
              </div>
            </div>
            <!--end::Thống kê nhanh-->

            <div class="row">
              <!--begin::Danh sách hạ tầng-->
              <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">Danh sách hạ tầng</h3>
                    <div class="card-tools">
                      <button type="button" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-lg me-1"></i> Thêm hạ tầng
                      </button>
                    </div>
                  </div>
                  <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap align-middle">
                      <thead>
                        <tr>
                          <th>Mã</th>
                          <th>Tên hạng mục</th>
                          <th>Loại</th>
                          <th>Sân bay</th>
                          <th>Trạng thái</th>
                          <th class="text-end">Thao tác</th>
                        </tr>
                      </thead>
                      <tbody>
                        <!-- Dữ liệu mẫu: Thành viên 2 thay bằng vòng lặp PHP -->
                        <tr>
                          <td>HT-001</td>
                          <td>Nhà ga T1</td>
                          <td>Nhà ga</td>
                          <td>Nội Bài</td>
                          <td><span class="badge text-bg-success">Hoạt động</span></td>
                          <td class="text-end">
                            <a href="#" class="btn btn-sm btn-outline-secondary">Sửa</a>
                            <a href="#" class="btn btn-sm btn-outline-danger">Xóa</a>
                          </td>
                        </tr>
                        <tr>
                          <td>HT-002</td>
                          <td>Đường băng 11L/29R</td>
                          <td>Đường băng</td>
                          <td>Đà Nẵng</td>
                          <td><span class="badge text-bg-warning">Bảo trì</span></td>
                          <td class="text-end">
                            <a href="#" class="btn btn-sm btn-outline-secondary">Sửa</a>
                            <a href="#" class="btn btn-sm btn-outline-danger">Xóa</a>
                          </td>
                        </tr>
                        <tr>
                          <td>HT-003</td>
                          <td>Sân đỗ số 5</td>
                          <td>Sân đỗ</td>
                          <td>Tân Sơn Nhất</td>
                          <td><span class="badge text-bg-secondary">Ngừng khai thác</span></td>
                          <td class="text-end">
                            <a href="#" class="btn btn-sm btn-outline-secondary">Sửa</a>
                            <a href="#" class="btn btn-sm btn-outline-danger">Xóa</a>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                  <div class="card-footer clearfix">
                    <ul class="pagination pagination-sm m-0 float-end">
                      <li class="page-item disabled"><a class="page-link" href="#">&laquo;</a></li>
                      <li class="page-item active"><a class="page-link" href="#">1</a></li>
                      <li class="page-item"><a class="page-link" href="#">2</a></li>
                      <li class="page-item"><a class="page-link" href="#">&raquo;</a></li>
                    </ul>
                  </div>
                </div>
              </div>
              <!--end::Danh sách hạ tầng-->
            </div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/main.php';
