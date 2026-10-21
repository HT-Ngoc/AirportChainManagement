<?php
/**
 * ============================================================
 * TRANG: KẾ TOÁN & DOANH THU  —  views/pages/ke_toan/index.php
 * Phân hệ: Kế toán & doanh thu dịch vụ hàng không (Thành viên 5)
 * ------------------------------------------------------------
 * Phạm vi gợi ý: hóa đơn dịch vụ, doanh thu theo hãng, công nợ.
 * Ghi chú cho Thành viên 5:
 *   - Dữ liệu trong bảng là MẪU, thay bằng dữ liệu do
 *     KeToanController truyền vào.
 * ============================================================
 */

$pageTitle  = 'Kế toán & doanh thu';
$pageHeader = 'Kế toán & doanh thu';
$navActive  = 'ke_toan';
$breadcrumb = [
    ['label' => 'Tổng quan', 'url' => '/AirportChainManagement/index.php'],
    ['label' => 'Kế toán', 'url' => null],
];

ob_start();
?>
            <!--begin::Thống kê nhanh-->
            <div class="row">
              <div class="col-lg-3 col-6">
                <div class="small-box text-bg-primary">
                  <div class="inner">
                    <h3>4,2 tỷ</h3>
                    <p>Doanh thu tháng</p>
                  </div>
                  <i class="small-box-icon bi bi-graph-up-arrow"></i>
                </div>
              </div>
              <div class="col-lg-3 col-6">
                <div class="small-box text-bg-success">
                  <div class="inner">
                    <h3>186</h3>
                    <p>Hóa đơn đã thu</p>
                  </div>
                  <i class="small-box-icon bi bi-receipt"></i>
                </div>
              </div>
              <div class="col-lg-3 col-6">
                <div class="small-box text-bg-warning">
                  <div class="inner">
                    <h3>24</h3>
                    <p>Hóa đơn chờ thu</p>
                  </div>
                  <i class="small-box-icon bi bi-hourglass-split"></i>
                </div>
              </div>
              <div class="col-lg-3 col-6">
                <div class="small-box text-bg-danger">
                  <div class="inner">
                    <h3>310 tr</h3>
                    <p>Công nợ quá hạn</p>
                  </div>
                  <i class="small-box-icon bi bi-exclamation-triangle"></i>
                </div>
              </div>
            </div>
            <!--end::Thống kê nhanh-->

            <div class="row">
              <!--begin::Danh sách hóa đơn-->
              <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">Danh sách hóa đơn dịch vụ</h3>
                    <div class="card-tools">
                      <button type="button" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-lg me-1"></i> Lập hóa đơn
                      </button>
                    </div>
                  </div>
                  <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap align-middle">
                      <thead>
                        <tr>
                          <th>Mã hóa đơn</th>
                          <th>Đối tác</th>
                          <th>Nội dung</th>
                          <th class="text-end">Số tiền (VND)</th>
                          <th>Ngày lập</th>
                          <th>Trạng thái</th>
                          <th class="text-end">Thao tác</th>
                        </tr>
                      </thead>
                      <tbody>
                        <!-- Dữ liệu mẫu: Thành viên 5 thay bằng vòng lặp PHP -->
                        <tr>
                          <td>HD-2026-001</td>
                          <td>Vietnam Airlines</td>
                          <td>Phí cất hạ cánh</td>
                          <td class="text-end">128,500,000</td>
                          <td>05/10/2026</td>
                          <td><span class="badge text-bg-success">Đã thu</span></td>
                          <td class="text-end">
                            <a href="#" class="btn btn-sm btn-outline-secondary">Chi tiết</a>
                            <a href="#" class="btn btn-sm btn-outline-primary">In</a>
                          </td>
                        </tr>
                        <tr>
                          <td>HD-2026-002</td>
                          <td>Vietjet Air</td>
                          <td>Phí sử dụng nhà ga</td>
                          <td class="text-end">96,200,000</td>
                          <td>08/10/2026</td>
                          <td><span class="badge text-bg-warning">Chờ thu</span></td>
                          <td class="text-end">
                            <a href="#" class="btn btn-sm btn-outline-secondary">Chi tiết</a>
                            <a href="#" class="btn btn-sm btn-outline-primary">In</a>
                          </td>
                        </tr>
                        <tr>
                          <td>HD-2026-003</td>
                          <td>Bamboo Airways</td>
                          <td>Dịch vụ mặt đất</td>
                          <td class="text-end">54,700,000</td>
                          <td>11/10/2026</td>
                          <td><span class="badge text-bg-danger">Quá hạn</span></td>
                          <td class="text-end">
                            <a href="#" class="btn btn-sm btn-outline-secondary">Chi tiết</a>
                            <a href="#" class="btn btn-sm btn-outline-primary">In</a>
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
              <!--end::Danh sách hóa đơn-->
            </div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/main.php';
