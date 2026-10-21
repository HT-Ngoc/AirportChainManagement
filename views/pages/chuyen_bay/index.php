<?php
/**
 * ============================================================
 * TRANG: QUẢN LÝ CHUYẾN BAY  —  views/pages/chuyen_bay/index.php
 * Phân hệ: Điều hành chuyến bay (Thành viên 3)
 * ------------------------------------------------------------
 * Phạm vi gợi ý: lịch bay, giờ cất/hạ cánh, hãng hàng không, trạng thái.
 * Ghi chú cho Thành viên 3:
 *   - Dữ liệu trong bảng là MẪU, thay bằng dữ liệu do
 *     ChuyenBayController truyền vào.
 * ============================================================
 */

$pageTitle  = 'Quản lý chuyến bay';
$pageHeader = 'Quản lý chuyến bay';
$navActive  = 'chuyen_bay';
$breadcrumb = [
    ['label' => 'Tổng quan', 'url' => '/AirportChainManagement/index.php'],
    ['label' => 'Chuyến bay', 'url' => null],
];

ob_start();
?>
            <!--begin::Thống kê nhanh-->
            <div class="row">
              <div class="col-lg-3 col-6">
                <div class="small-box text-bg-primary">
                  <div class="inner">
                    <h3>128</h3>
                    <p>Chuyến bay hôm nay</p>
                  </div>
                  <i class="small-box-icon bi bi-airplane-engines"></i>
                </div>
              </div>
              <div class="col-lg-3 col-6">
                <div class="small-box text-bg-info">
                  <div class="inner">
                    <h3>64</h3>
                    <p>Chuyến khởi hành</p>
                  </div>
                  <i class="small-box-icon bi bi-box-arrow-up-right"></i>
                </div>
              </div>
              <div class="col-lg-3 col-6">
                <div class="small-box text-bg-success">
                  <div class="inner">
                    <h3>64</h3>
                    <p>Chuyến đến</p>
                  </div>
                  <i class="small-box-icon bi bi-box-arrow-in-down"></i>
                </div>
              </div>
              <div class="col-lg-3 col-6">
                <div class="small-box text-bg-warning">
                  <div class="inner">
                    <h3>6</h3>
                    <p>Chuyến bị chậm</p>
                  </div>
                  <i class="small-box-icon bi bi-clock-history"></i>
                </div>
              </div>
            </div>
            <!--end::Thống kê nhanh-->

            <div class="row">
              <!--begin::Danh sách chuyến bay-->
              <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">Danh sách chuyến bay</h3>
                    <div class="card-tools">
                      <button type="button" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-lg me-1"></i> Thêm chuyến bay
                      </button>
                    </div>
                  </div>
                  <div class="card-body">
                    <form class="row g-2 mb-3" action="#" method="get">
                      <div class="col-md-4">
                        <input type="text" class="form-control" name="keyword" placeholder="Mã chuyến bay / hãng" />
                      </div>
                      <div class="col-md-3">
                        <input type="date" class="form-control" name="date" />
                      </div>
                      <div class="col-md-3">
                        <select class="form-select" name="status">
                          <option value="">-- Tất cả trạng thái --</option>
                          <option value="scheduled">Theo kế hoạch</option>
                          <option value="delayed">Chậm chuyến</option>
                          <option value="landed">Đã hạ cánh</option>
                          <option value="cancelled">Hủy chuyến</option>
                        </select>
                      </div>
                      <div class="col-md-2 d-grid">
                        <button type="submit" class="btn btn-outline-primary">Tìm kiếm</button>
                      </div>
                    </form>

                    <div class="table-responsive p-0">
                      <table class="table table-hover align-middle">
                        <thead>
                          <tr>
                            <th>Mã CB</th>
                            <th>Hãng</th>
                            <th>Điểm đi</th>
                            <th>Điểm đến</th>
                            <th>Cất cánh</th>
                            <th>Hạ cánh</th>
                            <th>Trạng thái</th>
                            <th class="text-end">Thao tác</th>
                          </tr>
                        </thead>
                        <tbody>
                          <!-- Dữ liệu mẫu: Thành viên 3 thay bằng vòng lặp PHP -->
                          <tr>
                            <td>VN123</td>
                            <td>Vietnam Airlines</td>
                            <td>HAN</td>
                            <td>SGN</td>
                            <td>08:00</td>
                            <td>10:10</td>
                            <td><span class="badge text-bg-success">Theo kế hoạch</span></td>
                            <td class="text-end">
                              <a href="#" class="btn btn-sm btn-outline-secondary">Sửa</a>
                              <a href="#" class="btn btn-sm btn-outline-danger">Hủy</a>
                            </td>
                          </tr>
                          <tr>
                            <td>VJ456</td>
                            <td>Vietjet Air</td>
                            <td>DAD</td>
                            <td>HAN</td>
                            <td>09:15</td>
                            <td>10:40</td>
                            <td><span class="badge text-bg-warning">Chậm 25 phút</span></td>
                            <td class="text-end">
                              <a href="#" class="btn btn-sm btn-outline-secondary">Sửa</a>
                              <a href="#" class="btn btn-sm btn-outline-danger">Hủy</a>
                            </td>
                          </tr>
                          <tr>
                            <td>QH789</td>
                            <td>Bamboo Airways</td>
                            <td>SGN</td>
                            <td>CXR</td>
                            <td>11:30</td>
                            <td>12:35</td>
                            <td><span class="badge text-bg-success">Theo kế hoạch</span></td>
                            <td class="text-end">
                              <a href="#" class="btn btn-sm btn-outline-secondary">Sửa</a>
                              <a href="#" class="btn btn-sm btn-outline-danger">Hủy</a>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
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
              <!--end::Danh sách chuyến bay-->
            </div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/main.php';
