<?php

require_once __DIR__ . '/../config/config.php';

class HaTangController
{
    /** @var mysqli */
    private $conn;

    private $flash = null;

    private $allowedTabs = ['san_bay', 'nha_ga', 'cua_ra', 'quay', 'bang_chuyen'];

    public function __construct($db_conn)
    {
        $this->conn = $db_conn;
    }

    public function handle()
    {
        $this->ensureCsrfToken();
        $this->processPost();
        $this->flash = $this->pullFlash();

        $quanTri = isset($_GET['quan_tri']) && (string) $_GET['quan_tri'] === '1';
        $tab = isset($_GET['tab']) ? (string) $_GET['tab'] : 'san_bay';
        if (!in_array($tab, $this->allowedTabs, true)) {
            $tab = 'san_bay';
        }

        $airports = $this->getAirports();
        $airportByIata = [];
        foreach ($airports as $airport) {
            $airportByIata[$airport['ma_iata']] = $airport;
        }

        $selectedIata = isset($_GET['san_bay']) ? strtoupper(trim((string) $_GET['san_bay'])) : '';
        $selectedAirport = ($selectedIata !== '' && isset($airportByIata[$selectedIata]))
            ? $airportByIata[$selectedIata]
            : null;

        $terminals = $selectedAirport ? $this->getTerminals((int) $selectedAirport['id']) : [];
        $selectedTerminal = $this->pickTerminal($terminals, isset($_GET['nha_ga']) ? (int) $_GET['nha_ga'] : 0);

        $gates = $selectedTerminal ? $this->getGates((int) $selectedTerminal['id']) : [];
        $checkins = $selectedTerminal ? $this->getCheckins((int) $selectedTerminal['id']) : [];
        $belts = $selectedTerminal ? $this->getBelts((int) $selectedTerminal['id']) : [];

        $khu = isset($_GET['khu']) ? (string) $_GET['khu'] : 'chuyen_bay';
        if (!in_array($khu, ['chuyen_bay', 'nha_ga'], true)) {
            $khu = 'chuyen_bay';
        }

        $filters = $this->readFilters();
        $airlines = $this->getAirlines();
        $flights = null;
        if ($selectedAirport && !$quanTri) {
            $flights = $this->getFlights((int) $selectedAirport['id'], $filters);
        }

        $management = null;
        if ($quanTri) {
            $management = $this->getManagement($tab, $airports, $selectedAirport, $terminals, $selectedTerminal, $gates, $checkins, $belts);
        }

        $domestic = [];
        $international = [];
        foreach ($airports as $airport) {
            if ($airport['phan_cap'] === 'Quoc_Te') {
                $international[] = $airport;
            } else {
                $domestic[] = $airport;
            }
        }

        return [
            'airports' => $airports,
            'airportByIata' => $airportByIata,
            'domesticRows' => array_chunk($domestic, 4),
            'internationalRows' => array_chunk($international, 7),
            'selectedAirport' => $selectedAirport,
            'terminals' => $terminals,
            'selectedTerminal' => $selectedTerminal,
            'gates' => $gates,
            'checkins' => $checkins,
            'belts' => $belts,
            'flights' => $flights,
            'airlines' => $airlines,
            'filters' => $filters,
            'khu' => $khu,
            'tab' => $tab,
            'quanTri' => $quanTri,
            'management' => $management,
            'csrf' => $_SESSION['ha_tang_csrf'],
            'flash' => $this->flash,
        ];
    }

    /* ------------------------------------------------------------
     * DỮ LIỆU HIỂN THỊ
     * ------------------------------------------------------------ */

    private function getAirports()
    {
        $sql = "SELECT sb.*,
                    (SELECT COUNT(*) FROM nha_ga ng WHERE ng.id_san_bay = sb.id) AS so_nha_ga,
                    (SELECT COUNT(*) FROM cua_ra_may_bay c
                        JOIN nha_ga ng2 ON ng2.id = c.id_nha_ga
                        WHERE ng2.id_san_bay = sb.id) AS so_cua_ra,
                    (SELECT COUNT(*) FROM quay_checkin q
                        JOIN nha_ga ng3 ON ng3.id = q.id_nha_ga
                        WHERE ng3.id_san_bay = sb.id) AS so_quay_checkin,
                    (SELECT COUNT(*) FROM bang_chuyen_hanh_ly b
                        JOIN nha_ga ng4 ON ng4.id = b.id_nha_ga
                        WHERE ng4.id_san_bay = sb.id) AS so_bang_chuyen
                FROM san_bay sb
                ORDER BY sb.phan_cap, sb.thu_tu_hien_thi ASC, sb.ten_san_bay ASC";

        return $this->fetchAll($this->query($sql));
    }

    private function getTerminals($airportId)
    {
        $sql = "SELECT ng.*,
                    (SELECT COUNT(*) FROM cua_ra_may_bay c WHERE c.id_nha_ga = ng.id) AS so_cua_ra,
                    (SELECT COUNT(*) FROM quay_checkin q WHERE q.id_nha_ga = ng.id) AS so_quay_checkin,
                    (SELECT COUNT(*) FROM bang_chuyen_hanh_ly b WHERE b.id_nha_ga = ng.id) AS so_bang_chuyen
                FROM nha_ga ng
                WHERE ng.id_san_bay = ?
                ORDER BY ng.ma_nha_ga ASC, ng.ten_nha_ga ASC";

        $stmt = $this->prepare($sql);
        $this->bind($stmt, 'i', [$airportId]);
        $stmt->execute();

        return $this->fetchAll($stmt);
    }

    private function getAirlines()
    {
        return $this->fetchAll($this->query("SELECT * FROM hang_hang_khong ORDER BY ten_hang ASC"));
    }

    private function pickTerminal($terminals, $requestedId)
    {
        if (!$terminals) {
            return null;
        }

        foreach ($terminals as $terminal) {
            if ((int) $terminal['id'] === (int) $requestedId) {
                return $terminal;
            }
        }

        return $terminals[0];
    }

    private function readFilters()
    {
        $ngay = isset($_GET['ngay']) ? trim((string) $_GET['ngay']) : '';
        if ($ngay !== '' && !$this->isValidDate($ngay)) {
            $ngay = '';
        }

        $hang = isset($_GET['hang']) ? (int) $_GET['hang'] : 0;
        $soHieu = isset($_GET['so_hieu']) ? strtoupper(trim((string) $_GET['so_hieu'])) : '';
        $huong = isset($_GET['huong']) ? (string) $_GET['huong'] : 'tat_ca';
        if (!in_array($huong, ['tat_ca', 'di', 'den'], true)) {
            $huong = 'tat_ca';
        }

        return [
            'ngay' => $ngay,
            'hang' => $hang,
            'so_hieu' => $soHieu,
            'huong' => $huong,
            'trang' => isset($_GET['trang']) ? max(1, (int) $_GET['trang']) : 1,
        ];
    }

    private function getFlights($airportId, $filters)
    {
        $perPage = 10;
        $departures = $this->countFlights($airportId, $filters, 'di');
        $arrivals = $this->countFlights($airportId, $filters, 'den');

        if ($filters['huong'] === 'di') {
            $total = $departures;
        } elseif ($filters['huong'] === 'den') {
            $total = $arrivals;
        } else {
            $total = $departures + $arrivals;
        }

        $pages = max(1, (int) ceil($total / $perPage));
        $page = min(max(1, (int) $filters['trang']), $pages);
        $offset = ($page - 1) * $perPage;

        $items = $this->fetchFlights($airportId, $filters, $filters['huong'], $perPage, $offset);
        foreach ($items as &$item) {
            $item['huong'] = ((int) $item['id_san_bay_di'] === (int) $airportId) ? 'di' : 'den';
        }
        unset($item);

        return [
            'items' => $items,
            'total' => $total,
            'page' => $page,
            'pages' => $pages,
            'per_page' => $perPage,
            'departures_count' => $departures,
            'arrivals_count' => $arrivals,
        ];
    }

    private function countFlights($airportId, $filters, $direction)
    {
        $sql = "SELECT COUNT(*) AS total FROM chuyen_bay_ngay cb WHERE 1 = 1";
        $types = '';
        $params = [];
        $this->appendFlightWhere($sql, $types, $params, $airportId, $filters, $direction);

        $stmt = $this->prepare($sql);
        $this->bind($stmt, $types, $params);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();

        return $row ? (int) $row['total'] : 0;
    }

    private function fetchFlights($airportId, $filters, $direction, $limit, $offset)
    {
        $sql = "SELECT cb.*,
                    hhk.ten_hang,
                    hhk.ma_iata AS ma_hang,
                    sbd.ma_iata AS ma_di,
                    sbd.ten_san_bay AS ten_di,
                    sbden.ma_iata AS ma_den,
                    sbden.ten_san_bay AS ten_den,
                    g.so_cua,
                    q.so_quay,
                    b.so_bang_chuyen
                FROM chuyen_bay_ngay cb
                JOIN hang_hang_khong hhk ON hhk.id = cb.id_hang_hang_khong
                JOIN san_bay sbd ON sbd.id = cb.id_san_bay_di
                JOIN san_bay sbden ON sbden.id = cb.id_san_bay_den
                LEFT JOIN cua_ra_may_bay g ON g.id = cb.id_cua_ra_may_bay
                LEFT JOIN quay_checkin q ON q.id = cb.id_quay_checkin
                LEFT JOIN bang_chuyen_hanh_ly b ON b.id = cb.id_bang_chuyen
                WHERE 1 = 1";

        $types = '';
        $params = [];
        $this->appendFlightWhere($sql, $types, $params, $airportId, $filters, $direction);
        $sql .= " ORDER BY cb.gio_du_kien ASC, cb.id ASC LIMIT ? OFFSET ?";
        $types .= 'ii';
        $params[] = $limit;
        $params[] = $offset;

        $stmt = $this->prepare($sql);
        $this->bind($stmt, $types, $params);
        $stmt->execute();

        return $this->fetchAll($stmt);
    }

    private function appendFlightWhere(&$sql, &$types, &$params, $airportId, $filters, $direction)
    {
        if ($direction === 'di') {
            $sql .= " AND cb.id_san_bay_di = ?";
            $types .= 'i';
            $params[] = $airportId;
        } elseif ($direction === 'den') {
            $sql .= " AND cb.id_san_bay_den = ?";
            $types .= 'i';
            $params[] = $airportId;
        } else {
            $sql .= " AND (cb.id_san_bay_di = ? OR cb.id_san_bay_den = ?)";
            $types .= 'ii';
            $params[] = $airportId;
            $params[] = $airportId;
        }

        if ($filters['ngay'] !== '') {
            $sql .= " AND DATE(cb.gio_du_kien) = ?";
            $types .= 's';
            $params[] = $filters['ngay'];
        }

        if ($filters['hang'] > 0) {
            $sql .= " AND cb.id_hang_hang_khong = ?";
            $types .= 'i';
            $params[] = $filters['hang'];
        }

        if ($filters['so_hieu'] !== '') {
            $sql .= " AND cb.so_hieu LIKE ?";
            $types .= 's';
            $params[] = '%' . $filters['so_hieu'] . '%';
        }
    }

    private function getManagement($tab, $airports, $selectedAirport, $terminals, $selectedTerminal, $gates, $checkins, $belts)
    {
        $management = [
            'tab' => $tab,
            'editRecord' => null,
            'formMode' => null,
            'formType' => null,
            'gates' => $gates,
            'checkins' => $checkins,
            'belts' => $belts,
        ];

        if (isset($_GET['them']) && (string) $_GET['them'] === '1') {
            $management['formMode'] = 'them';
            $management['formType'] = $tab;
        }

        $editId = isset($_GET['sua']) ? (int) $_GET['sua'] : 0;
        if ($editId > 0) {
            $management['formMode'] = 'sua';
            $management['formType'] = $tab;
            $management['editRecord'] = $this->findRecord($tab, $editId);
        }

        return $management;
    }

    private function findRecord($tab, $id)
    {
        $map = [
            'san_bay' => 'san_bay',
            'nha_ga' => 'nha_ga',
            'cua_ra' => 'cua_ra_may_bay',
            'quay' => 'quay_checkin',
            'bang_chuyen' => 'bang_chuyen_hanh_ly',
        ];

        if (!isset($map[$tab])) {
            return null;
        }

        $stmt = $this->prepare("SELECT * FROM `{$map[$tab]}` WHERE id = ? LIMIT 1");
        $this->bind($stmt, 'i', [$id]);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();

        return $row ?: null;
    }

    private function getGates($terminalId)
    {
        $stmt = $this->prepare("SELECT * FROM cua_ra_may_bay WHERE id_nha_ga = ? ORDER BY LENGTH(so_cua), so_cua");
        $this->bind($stmt, 'i', [$terminalId]);
        $stmt->execute();

        return $this->fetchAll($stmt);
    }

    private function getCheckins($terminalId)
    {
        $stmt = $this->prepare("SELECT * FROM quay_checkin WHERE id_nha_ga = ? ORDER BY LENGTH(so_quay), so_quay");
        $this->bind($stmt, 'i', [$terminalId]);
        $stmt->execute();

        return $this->fetchAll($stmt);
    }

    private function getBelts($terminalId)
    {
        $stmt = $this->prepare("SELECT * FROM bang_chuyen_hanh_ly WHERE id_nha_ga = ? ORDER BY LENGTH(so_bang_chuyen), so_bang_chuyen");
        $this->bind($stmt, 'i', [$terminalId]);
        $stmt->execute();

        return $this->fetchAll($stmt);
    }

    /* ------------------------------------------------------------
     * XỬ LÝ POST (THÊM / SỬA / XÓA)
     * ------------------------------------------------------------ */

    private function processPost()
    {
        if (!isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $action = isset($_POST['action']) ? (string) $_POST['action'] : '';
        if ($action === '') {
            return;
        }

        $csrf = isset($_POST['csrf']) ? (string) $_POST['csrf'] : '';
        if (!hash_equals($_SESSION['ha_tang_csrf'] ?? '', $csrf)) {
            $this->setFlash('danger', 'Phiên làm việc không hợp lệ. Vui lòng thử lại.');
            $this->redirectAfterPost();
        }

        switch ($action) {
            case 'them_san_bay':
                $this->saveSanBay(false);
                break;
            case 'sua_san_bay':
                $this->saveSanBay(true);
                break;
            case 'xoa_san_bay':
                $this->deleteSanBay();
                break;

            case 'them_nha_ga':
                $this->saveNhaGa(false);
                break;
            case 'sua_nha_ga':
                $this->saveNhaGa(true);
                break;
            case 'xoa_nha_ga':
                $this->deleteNhaGa();
                break;

            case 'them_cua_ra':
                $this->saveResource('cua_ra', false);
                break;
            case 'sua_cua_ra':
                $this->saveResource('cua_ra', true);
                break;
            case 'xoa_cua_ra':
                $this->deleteResource('cua_ra');
                break;

            case 'them_quay':
                $this->saveResource('quay', false);
                break;
            case 'sua_quay':
                $this->saveResource('quay', true);
                break;
            case 'xoa_quay':
                $this->deleteResource('quay');
                break;

            case 'them_bang_chuyen':
                $this->saveResource('bang_chuyen', false);
                break;
            case 'sua_bang_chuyen':
                $this->saveResource('bang_chuyen', true);
                break;
            case 'xoa_bang_chuyen':
                $this->deleteResource('bang_chuyen');
                break;

            default:
                $this->setFlash('warning', 'Hành động không được hỗ trợ.');
                break;
        }

        $this->redirectAfterPost();
    }

    private function saveSanBay($isEdit)
    {
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        $iata = strtoupper(trim((string) ($_POST['ma_iata'] ?? '')));
        $icao = strtoupper(trim((string) ($_POST['ma_icao'] ?? '')));
        $ten = trim((string) ($_POST['ten_san_bay'] ?? ''));
        $tenNgan = trim((string) ($_POST['ten_ngan'] ?? ''));
        $phanCap = (string) ($_POST['phan_cap'] ?? 'Quoc_Noi');
        $viTri = trim((string) ($_POST['vi_tri'] ?? ''));
        $hinhAnh = trim((string) ($_POST['hinh_anh'] ?? ''));
        $moTa = trim((string) ($_POST['mo_ta'] ?? ''));
        $thuTu = (int) ($_POST['thu_tu_hien_thi'] ?? 0);
        $trangThai = (string) ($_POST['trang_thai'] ?? 'Hoat_Dong');

        $errors = [];
        if (!preg_match('/^[A-Z]{3}$/', $iata)) {
            $errors[] = 'Mã IATA phải gồm đúng 3 chữ cái (ví dụ: HAN).';
        }
        if ($icao !== '' && !preg_match('/^[A-Z]{4}$/', $icao)) {
            $errors[] = 'Mã ICAO phải gồm đúng 4 chữ cái (ví dụ: VVNB).';
        }
        if ($ten === '') {
            $errors[] = 'Tên sân bay không được để trống.';
        }
        if ($tenNgan === '') {
            $errors[] = 'Tên ngắn không được để trống.';
        }
        if (!in_array($phanCap, ['Quoc_Noi', 'Quoc_Te'], true)) {
            $errors[] = 'Phân cấp sân bay không hợp lệ.';
        }
        if ($viTri === '') {
            $errors[] = 'Vị trí không được để trống.';
        }
        if (!in_array($trangThai, ['Hoat_Dong', 'Bao_Tri', 'Dong_Cua_Khan_Cap'], true)) {
            $errors[] = 'Trạng thái sân bay không hợp lệ.';
        }
        if ($isEdit && $id <= 0) {
            $errors[] = 'Không xác định được sân bay cần sửa.';
        }

        if ($errors) {
            $this->setFlash('danger', implode(' ', $errors));
            return;
        }

        if ($hinhAnh === '') {
            $hinhAnh = 'assets/img/san_bay/' . $iata . '.jpg';
        }
        if ($icao === '') {
            $icao = null;
        }
        if ($moTa === '') {
            $moTa = null;
        }

        if ($isEdit) {
            $sql = "UPDATE san_bay
                    SET ma_iata = ?, ma_icao = ?, ten_san_bay = ?, ten_ngan = ?, phan_cap = ?,
                        vi_tri = ?, hinh_anh = ?, mo_ta = ?, thu_tu_hien_thi = ?, trang_thai = ?
                    WHERE id = ?";
            $types = 'ssssssssisi';
            $params = [$iata, $icao, $ten, $tenNgan, $phanCap, $viTri, $hinhAnh, $moTa, $thuTu, $trangThai, $id];
            $label = 'sân bay';
        } else {
            $sql = "INSERT INTO san_bay
                    (ma_iata, ma_icao, ten_san_bay, ten_ngan, phan_cap, vi_tri, hinh_anh, mo_ta, thu_tu_hien_thi, trang_thai)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $types = 'ssssssssis';
            $params = [$iata, $icao, $ten, $tenNgan, $phanCap, $viTri, $hinhAnh, $moTa, $thuTu, $trangThai];
            $label = 'sân bay';
        }

        if ($this->executeWrite($sql, $types, $params, $label)) {
            $this->setReturnContext($iata);
        }
    }

    private function deleteSanBay()
    {
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        if ($id <= 0) {
            $this->setFlash('danger', 'Không xác định được sân bay cần xóa.');
            return;
        }

        $this->executeDelete(
            "DELETE FROM san_bay WHERE id = ?",
            'i',
            [$id],
            'sân bay',
            'Không thể xóa sân bay vì đang có lịch bay hoặc chuyến bay tham chiếu.'
        );
    }

    private function saveNhaGa($isEdit)
    {
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        $airportId = (int) ($_POST['id_san_bay'] ?? 0);
        $ma = strtoupper(trim((string) ($_POST['ma_nha_ga'] ?? '')));
        $ten = trim((string) ($_POST['ten_nha_ga'] ?? ''));
        $loai = (string) ($_POST['loai_nha_ga'] ?? 'Hon_Hop');
        $nam = trim((string) ($_POST['nam_khai_thac'] ?? ''));
        $dienTich = trim((string) ($_POST['dien_tich'] ?? ''));
        $congSuat = trim((string) ($_POST['cong_suat'] ?? ''));
        $soDo = trim((string) ($_POST['so_do'] ?? ''));
        $moTa = trim((string) ($_POST['mo_ta'] ?? ''));
        $trangThai = (string) ($_POST['trang_thai'] ?? 'Hoat_Dong');

        $errors = [];
        if ($airportId <= 0) {
            $errors[] = 'Vui lòng chọn sân bay.';
        }
        if ($ma === '') {
            $errors[] = 'Mã nhà ga không được để trống.';
        }
        if ($ten === '') {
            $errors[] = 'Tên nhà ga không được để trống.';
        }
        if (!in_array($loai, ['Quoc_Noi', 'Quoc_Te', 'Hon_Hop'], true)) {
            $errors[] = 'Loại nhà ga không hợp lệ.';
        }
        if ($nam !== '' && (!ctype_digit($nam) || (int) $nam < 1900 || (int) $nam > 2100)) {
            $errors[] = 'Năm khai thác không hợp lệ.';
        }
        if (!in_array($trangThai, ['Hoat_Dong', 'Bao_Tri'], true)) {
            $errors[] = 'Trạng thái nhà ga không hợp lệ.';
        }
        if ($isEdit && $id <= 0) {
            $errors[] = 'Không xác định được nhà ga cần sửa.';
        }

        if ($errors) {
            $this->setFlash('danger', implode(' ', $errors));
            return;
        }

        $namValue = $nam === '' ? null : (int) $nam;
        $dienTich = $dienTich === '' ? null : $dienTich;
        $congSuat = $congSuat === '' ? null : $congSuat;
        $soDo = $soDo === '' ? null : $soDo;
        $moTa = $moTa === '' ? null : $moTa;

        if ($isEdit) {
            $sql = "UPDATE nha_ga
                    SET id_san_bay = ?, ma_nha_ga = ?, ten_nha_ga = ?, loai_nha_ga = ?,
                        nam_khai_thac = ?, dien_tich = ?, cong_suat = ?, so_do = ?, mo_ta = ?, trang_thai = ?
                    WHERE id = ?";
            $types = 'isssisssssi';
            $params = [$airportId, $ma, $ten, $loai, $namValue, $dienTich, $congSuat, $soDo, $moTa, $trangThai, $id];
        } else {
            $sql = "INSERT INTO nha_ga
                    (id_san_bay, ma_nha_ga, ten_nha_ga, loai_nha_ga, nam_khai_thac, dien_tich, cong_suat, so_do, mo_ta, trang_thai)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $types = 'isssisssss';
            $params = [$airportId, $ma, $ten, $loai, $namValue, $dienTich, $congSuat, $soDo, $moTa, $trangThai];
        }

        if ($this->executeWrite($sql, $types, $params, 'nhà ga')) {
            $savedId = $isEdit ? $id : (int) $this->conn->insert_id;
            $context = $this->getTerminalContext($savedId);
            if ($context) {
                $this->setReturnContext($context['ma_iata'], $context['id']);
            } else {
                $this->setReturnContext($this->getAirportIata($airportId), 0);
            }
        }
    }

    private function deleteNhaGa()
    {
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        if ($id <= 0) {
            $this->setFlash('danger', 'Không xác định được nhà ga cần xóa.');
            return;
        }

        $this->executeDelete(
            "DELETE FROM nha_ga WHERE id = ?",
            'i',
            [$id],
            'nhà ga',
            'Không thể xóa nhà ga này.'
        );
    }

    private function saveResource($type, $isEdit)
    {
        $map = [
            'cua_ra' => ['table' => 'cua_ra_may_bay', 'field' => 'so_cua', 'label' => 'cửa ra máy bay'],
            'quay' => ['table' => 'quay_checkin', 'field' => 'so_quay', 'label' => 'quầy check-in'],
            'bang_chuyen' => ['table' => 'bang_chuyen_hanh_ly', 'field' => 'so_bang_chuyen', 'label' => 'băng chuyền hành lý'],
        ];

        if (!isset($map[$type])) {
            $this->setFlash('danger', 'Loại tài nguyên không hợp lệ.');
            return;
        }

        $config = $map[$type];
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        $terminalId = (int) ($_POST['id_nha_ga'] ?? 0);
        $so = strtoupper(trim((string) ($_POST['so'] ?? '')));
        $khuVuc = trim((string) ($_POST['khu_vuc'] ?? ''));
        $trangThai = (string) ($_POST['trang_thai'] ?? 'Hoat_Dong');

        $errors = [];
        if ($terminalId <= 0) {
            $errors[] = 'Vui lòng chọn nhà ga.';
        }
        if ($so === '') {
            $errors[] = 'Số hiệu không được để trống.';
        }
        if (!in_array($trangThai, ['Hoat_Dong', 'Bao_Tri'], true)) {
            $errors[] = 'Trạng thái không hợp lệ.';
        }
        if ($isEdit && $id <= 0) {
            $errors[] = 'Không xác định được bản ghi cần sửa.';
        }

        if ($errors) {
            $this->setFlash('danger', implode(' ', $errors));
            return;
        }

        $khuVuc = $khuVuc === '' ? null : $khuVuc;
        $table = $config['table'];
        $field = $config['field'];

        if ($isEdit) {
            $sql = "UPDATE `{$table}`
                    SET id_nha_ga = ?, `{$field}` = ?, khu_vuc = ?, trang_thai = ?
                    WHERE id = ?";
            $types = 'isssi';
            $params = [$terminalId, $so, $khuVuc, $trangThai, $id];
        } else {
            $sql = "INSERT INTO `{$table}` (id_nha_ga, `{$field}`, khu_vuc, trang_thai)
                    VALUES (?, ?, ?, ?)";
            $types = 'isss';
            $params = [$terminalId, $so, $khuVuc, $trangThai];
        }

        if ($this->executeWrite($sql, $types, $params, $config['label'])) {
            $context = $this->getTerminalContext($terminalId);
            if ($context) {
                $this->setReturnContext($context['ma_iata'], $context['id']);
            }
        }
    }

    private function deleteResource($type)
    {
        $map = [
            'cua_ra' => ['table' => 'cua_ra_may_bay', 'label' => 'cửa ra máy bay'],
            'quay' => ['table' => 'quay_checkin', 'label' => 'quầy check-in'],
            'bang_chuyen' => ['table' => 'bang_chuyen_hanh_ly', 'label' => 'băng chuyền hành lý'],
        ];

        if (!isset($map[$type])) {
            $this->setFlash('danger', 'Loại tài nguyên không hợp lệ.');
            return;
        }

        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        if ($id <= 0) {
            $this->setFlash('danger', 'Không xác định được bản ghi cần xóa.');
            return;
        }

        $this->executeDelete(
            "DELETE FROM `{$map[$type]['table']}` WHERE id = ?",
            'i',
            [$id],
            $map[$type]['label'],
            'Không thể xóa bản ghi này.'
        );
    }

    /* ------------------------------------------------------------
     * TIỆN ÍCH CSDL
     * ------------------------------------------------------------ */

    private function query($sql)
    {
        return $this->conn->query($sql);
    }

    private function prepare($sql)
    {
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new RuntimeException('Không thể chuẩn bị truy vấn: ' . $this->conn->error);
        }

        return $stmt;
    }

    private function bind($stmt, $types, $params)
    {
        if ($types !== '') {
            $stmt->bind_param($types, ...$params);
        }
    }

    private function fetchAll($resultOrStmt)
    {
        $rows = [];
        $result = $resultOrStmt instanceof mysqli_stmt
            ? $resultOrStmt->get_result()
            : $resultOrStmt;

        if (!$result) {
            return $rows;
        }

        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }

        return $rows;
    }

    private function executeWrite($sql, $types, $params, $label)
    {
        try {
            $stmt = $this->prepare($sql);
            $this->bind($stmt, $types, $params);
            $stmt->execute();
            $this->setFlash('success', 'Đã lưu ' . $label . ' thành công.');

            return true;
        } catch (mysqli_sql_exception $e) {
            $this->setFlash('danger', $this->databaseErrorMessage($e, $label));

            return false;
        }
    }

    private function executeDelete($sql, $types, $params, $label, $referenceMessage)
    {
        try {
            $stmt = $this->prepare($sql);
            $this->bind($stmt, $types, $params);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                $this->setFlash('success', 'Đã xóa ' . $label . '.');
            } else {
                $this->setFlash('warning', 'Không tìm thấy ' . $label . ' cần xóa.');
            }
        } catch (mysqli_sql_exception $e) {
            if ((int) $e->getCode() === 1451) {
                $this->setFlash('danger', $referenceMessage);
            } else {
                $this->setFlash('danger', $this->databaseErrorMessage($e, $label));
            }
        }
    }

    private function getAirportIata($airportId)
    {
        try {
            $stmt = $this->prepare("SELECT ma_iata FROM san_bay WHERE id = ? LIMIT 1");
            $this->bind($stmt, 'i', [$airportId]);
            $stmt->execute();
            $row = $stmt->get_result()->fetch_assoc();

            return $row ? (string) $row['ma_iata'] : '';
        } catch (Throwable $e) {
            return '';
        }
    }

    private function getTerminalContext($terminalId)
    {
        try {
            $stmt = $this->prepare(
                "SELECT ng.id, sb.ma_iata
                 FROM nha_ga ng
                 JOIN san_bay sb ON sb.id = ng.id_san_bay
                 WHERE ng.id = ? LIMIT 1"
            );
            $this->bind($stmt, 'i', [$terminalId]);
            $stmt->execute();
            $row = $stmt->get_result()->fetch_assoc();

            return $row ?: null;
        } catch (Throwable $e) {
            return null;
        }
    }

    private function setReturnContext($airportIata, $terminalId = 0)
    {
        $airportIata = trim((string) $airportIata);
        if ($airportIata !== '') {
            $_POST['return_san_bay'] = $airportIata;
        }
        $_POST['return_nha_ga'] = (int) $terminalId;
    }

    private function databaseErrorMessage($e, $label)
    {
        if ((int) $e->getCode() === 1062) {
            return 'Dữ liệu bị trùng. Vui lòng kiểm tra mã/số hiệu đã tồn tại.';
        }
        if ((int) $e->getCode() === 1452) {
            return 'Không tìm thấy sân bay hoặc nhà ga được chọn.';
        }
        if ((int) $e->getCode() === 1451) {
            return 'Không thể xóa ' . $label . ' vì đang được dữ liệu khác tham chiếu.';
        }

        return 'Lỗi cơ sở dữ liệu. Vui lòng kiểm tra dữ liệu và thử lại.';
    }

    /* ------------------------------------------------------------
     * TIỆN ÍCH KHÁC
     * ------------------------------------------------------------ */

    private function ensureCsrfToken()
    {
        if (empty($_SESSION['ha_tang_csrf'])) {
            $_SESSION['ha_tang_csrf'] = bin2hex(random_bytes(32));
        }
    }

    private function setFlash($type, $message)
    {
        $_SESSION['ha_tang_flash'] = ['type' => $type, 'message' => $message];
    }

    private function pullFlash()
    {
        if (empty($_SESSION['ha_tang_flash'])) {
            return null;
        }

        $flash = $_SESSION['ha_tang_flash'];
        unset($_SESSION['ha_tang_flash']);

        return $flash;
    }

    private function isValidDate($value)
    {
        if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $value, $matches)) {
            return false;
        }

        return checkdate((int) $matches[2], (int) $matches[3], (int) $matches[1]);
    }

    private function redirectAfterPost()
    {
        $tab = isset($_POST['return_tab']) ? (string) $_POST['return_tab'] : 'san_bay';
        if (!in_array($tab, $this->allowedTabs, true)) {
            $tab = 'san_bay';
        }

        $params = [
            'page' => 'ha_tang',
            'quan_tri' => '1',
            'tab' => $tab,
        ];

        $sanBay = isset($_POST['return_san_bay']) ? trim((string) $_POST['return_san_bay']) : '';
        if ($sanBay !== '') {
            $params['san_bay'] = $sanBay;
        }

        $nhaGa = isset($_POST['return_nha_ga']) ? (int) $_POST['return_nha_ga'] : 0;
        if ($nhaGa > 0) {
            $params['nha_ga'] = $nhaGa;
        }

        $this->redirect($params);
    }

    private function redirect($params)
    {
        header('Location: ' . BASE_URL . '/index.php?' . http_build_query($params));
        exit();
    }
}
