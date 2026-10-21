<?php
/**
 * ============================================================
 * TRANG: BẢO TRÌ & KHẮC PHỤC  —  views/pages/bao_tri/index.php
 * Phân hệ: Bảo trì trang thiết bị (Thành viên 4)
 * ------------------------------------------------------------
 * Phạm vi gợi ý: phiếu bảo trì, kế hoạch bảo dưỡng định kỳ,
 * xử lý sự cố trang thiết bị.
 * Ghi chú cho Thành viên 4:
 *   - Dữ liệu trong bảng là MẪU, thay bằng dữ liệu do
 *     BaoTriController truyền vào.
 * ============================================================
 */

$pageTitle  = 'Bảo trì & khắc phục';
$pageHeader = 'Bảo trì & khắc phục';
$navActive  = 'bao_tri';
$breadcrumb = [
    ['label' => 'Tổng quan', 'url' => '/AirportChainManagement/index.php'],
    ['label' => 'Bảo trì', 'url' => null],
];

ob_start();
?>
            <!--begin::Thống kê nhanh-->
            <div class="row">
              <div class="col-lg-3 col-6">
                <div class="small-box text-bg-primary">
                  <div class="inner">
                    <h3>12</h3>
                    <p>Phiếu đang mở</p>
                  </div>
                  <i class="small-box-icon bi bi-clipboard-check"></i>
                </div>
              </div>
              <div class="col-lg-3 col-6">
                <div class="small-box text-bg-danger">
                  <div class="inner">
                    <h3>2</h3>
                    <p>Sự cố khẩn cấp</p>
                  </div>
                  <i class="small-box-icon bi bi-exclamation-octagon"></i>
                </div>
              </div>
              <div class="col-lg-3 col-6">
                <div class="small-box text-bg-success">
                  <div class="inner">
                    <h3>35</h3>
                    <p>Hoàn thành trong tháng</p>
                  </div>
                  <i class="small-box-icon bi bi-check2-circle"></i>
                </div>
              </div>
              <div class="col-lg-3 col-6">
                <div class="small-box text-bg-info">
                  <div class="inner">
                    <h3>8</h3>
                    <p>Bảo dưỡng định kỳ</p>
                  </div>
                  <i class="small-box-icon bi bi-calendar-check"></i>
                </div>
              </div>
            </div>
            <!--end::Thống kê nhanh-->

            <div class="row">
              <!--begin::Danh sách phiếu bảo trì-->
              <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">Danh sách phiếu bảo trì</h3>
                    <div class="card-tools">
                      <button type="button" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-lg me-1"></i> Tạo phiếu bảo trì
                      </button>
                    </div>
                  </div>
                  <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap align-middle">
                      <thead>
                        <tr>
                          <th>Mã phiếu</th>
                          <th>Hạng mục / thiết bị</th>
                          <th>Loại</th>
                          <th>Ngày tạo</th>
                          <th>Phụ trách</th>
                          <th>Trạng thái</th>
                          <th class="text-end">Thao tác</th>
                        </tr>
                      </thead>
                      <tbody>
                        <!-- Dữ liệu mẫu: Thành viên 4 thay bằng vòng lặp PHP -->
                        <tr>
                          <td>BT-1001</td>
                          <td>Thang cuốn nhà ga T1</td>
                          <td>Khắc phục sự cố</td>
                          <td>12/10/2026</td>
                          <td>Nguyễn Văn A</td>
                          <td><span class="badge text-bg-danger">Đang xử lý</span></td>
                          <td class="text-end">
                            <a href="#" class="btn btn-sm btn-outline-secondary">Sửa</a>
                            <a href="#" class="btn btn-sm btn-outline-danger">Xóa</a>
                          </td>
                        </tr>
                        <tr>
                          <td>BT-1002</td>
                          <td>Xe chở hành lý XH-07</td>
                          <td>Bảo dưỡng định kỳ</td>
                          <td>10/10/2026</td>
                          <td>Trần Thị B</td>
                          <td><span class="badge text-bg-success">Hoàn thành</span></td>
                          <td class="text-end">
                            <a href="#" class="btn btn-sm btn-outline-secondary">Sửa</a>
                            <a href="#" class="btn btn-sm btn-outline-danger">Xóa</a>
                          </td>
                        </tr>
                        <tr>
                          <td>BT-1003</td>
                          <td>Hệ thống SOFA nhà ga</td>
                          <td>Kiểm tra kỹ thuật</td>
                          <td>09/10/2026</td>
                          <td>Lê Văn C</td>
                          <td><span class="badge text-bg-warning">Chờ vật tư</span></td>
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
              <!--end::Danh sách phiếu bảo trì-->
            </div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/main.php';
