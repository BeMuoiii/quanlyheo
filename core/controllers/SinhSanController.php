<?php
require_once __DIR__ . '/../models/SinhSanModel.php';
require_once __DIR__ . '/../models/HeoModel.php';

class SinhSanController
{
    private $pdo;
    private $model;
    private $heoModel;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->model = new SinhSanModel($pdo);
        $this->heoModel = new HeoModel($pdo);
    }

    private function view($viewName, $data = [])
    {
        extract($data);
        $viewPath = __DIR__ . '/../../admin/views/sinhsan/' . $viewName . '.php';
        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            die("Không tìm thấy view: $viewPath");
        }
    }

    // === TRANG CHỦ QUẢN LÝ SINH SẢN ===
    public function index()
    {
        // ================== XỬ LÝ GHI NHẬN ĐẺ KHI SUBMIT FORM VỀ TRANG CHÍNH ==================
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ghiNhanDe_id'])) {
            $id = (int)$_POST['ghiNhanDe_id'];

            // Lấy dữ liệu từ form
            $ngayDe   = trim($_POST['NgayDe'] ?? '');
            $soSong   = (int)($_POST['SoConSong'] ?? 10);  // Mặc định 10 nếu không gửi
            $soChet   = (int)($_POST['SoConChet'] ?? 0);
            $maNVDe   = trim($_POST['MaNVDe'] ?? '');
            $ghiChuDe = trim($_POST['GhiChuDe'] ?? '');

            // Validation
            if ($id <= 0) {
                $_SESSION['error'] = "Phiếu sinh sản không hợp lệ!";
            } elseif (empty($ngayDe)) {
                $_SESSION['error'] = "Vui lòng chọn ngày đẻ thực tế!";
            } else {
                // Kiểm tra phiếu tồn tại và chưa đẻ
                $stmtCheck = $this->pdo->prepare("SELECT SoConSong FROM sinhsan WHERE SinhSan = ?");
                $stmtCheck->execute([$id]);
                $daDe = $stmtCheck->fetchColumn();

                if ($daDe !== false && $daDe !== null) {
                    $_SESSION['error'] = "Phiếu này đã được ghi nhận đẻ rồi!";
                } else {
                    $dataUpdate = [
                        'NgayDe'    => $ngayDe,
                        'SoConSong' => $soSong,
                        'SoConChet' => $soChet,
                        'MaNVDe'    => $maNVDe ?: null,
                        'GhiChuDe'  => $ghiChuDe ?: null,
                    ];

                    $result = $this->model->updateGhiNhanDe($id, $dataUpdate);

                    if (isset($result['status']) && $result['status'] === true) {
                        $_SESSION['success'] = "Ghi nhận đẻ thành công! Phiếu #{$id} - {$soSong} con sống.";
                    } else {
                        $_SESSION['error'] = "Lỗi khi ghi nhận đẻ: " . ($result['message'] ?? 'Không xác định');
                    }
                }
            }

            // Luôn redirect về trang chính để reload dữ liệu mới + tránh submit lại
            header('Location: index.php?url=sinhsan');
            exit;
        }

        // ================== PHÂN TRANG ==================
        // ================== PHÂN TRANG - CHỈ 10 PHIẾU MỖI TRANG ==================
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 10;  // Đúng 10 sản phẩm (phiếu) mỗi trang
        $offset = ($page - 1) * $limit;

        // Tổng số phiếu
        $total = $this->pdo->query("SELECT COUNT(*) FROM sinhsan")->fetchColumn();
        $totalPages = ceil($total / $limit);

        // ================== THỐNG KÊ (GIỮ NGUYÊN) ==================
        $tongNai = $this->pdo->query("SELECT COUNT(*) FROM heo WHERE GioiTinh = 'C'")->fetchColumn();

        $tongPhoi = $this->pdo->query("SELECT COUNT(*) FROM sinhsan WHERE NgayPhoi IS NOT NULL")->fetchColumn();
        $thanhCong = $this->pdo->query("SELECT COUNT(*) FROM sinhsan WHERE SoConSong IS NOT NULL AND SoConSong > 0")->fetchColumn();
        $tyLe = $tongPhoi > 0 ? round(($thanhCong / $tongPhoi) * 100) : 0;

        $avgCon = $this->pdo->query("SELECT ROUND(AVG(SoConSong),1) FROM sinhsan WHERE SoConSong > 0")->fetchColumn() ?: 0;

        $sapDe = $this->pdo->query("
        SELECT COUNT(*) FROM sinhsan 
        WHERE SoConSong IS NULL 
          AND NgayPhoi IS NOT NULL
          AND DATEDIFF(DATE_ADD(NgayPhoi, INTERVAL 114 DAY), CURDATE()) BETWEEN 0 AND 7
    ")->fetchColumn();

        $listSapDe = $this->pdo->query("
        SELECT 
            ss.SinhSan, ss.MaHeoNai, ss.NgayPhoi, h.ViTriChuong,
            DATEDIFF(DATE_ADD(ss.NgayPhoi, INTERVAL 114 DAY), CURDATE()) AS ConLaiNgay
        FROM sinhsan ss
        JOIN heo h ON ss.MaHeoNai = h.MaHeo
        WHERE ss.SoConSong IS NULL 
          AND ss.NgayPhoi IS NOT NULL
          AND DATEDIFF(DATE_ADD(ss.NgayPhoi, INTERVAL 114 DAY), CURDATE()) BETWEEN 0 AND 7
        ORDER BY ConLaiNgay ASC
    ")->fetchAll(PDO::FETCH_ASSOC);

        // ================== DANH SÁCH SINH SẢN CÓ PHÂN TRANG ==================
        // ================== DANH SÁCH CHÍNH CÓ PHÂN TRANG (SỬA LẠI ĐÚNG CÁCH) ==================
        // ================== DANH SÁCH CHÍNH CÓ PHÂN TRANG ==================
        // Trong method index(), phần query danh sách chính
        $sqlList = "
    SELECT 
        ss.*,
        ss.NgayDe, 
        DATE_ADD(ss.NgayPhoi, INTERVAL 114 DAY) AS NgayDuSinh,
        hn.MaHeo AS MaHeoNai,
        COALESCE(hn.GiongHeo, 'Không rõ') AS GiongNai,
        hd.MaHeo AS MaHeoDuc,
        COALESCE(hd.GiongHeo, 'Không rõ') AS GiongDuc,
        COALESCE(nv.HoTen, 'Chưa ghi nhận') AS HoTen,
        h_nai.ViTriChuong AS ChuongNai
    FROM sinhsan ss
    LEFT JOIN heo hn ON ss.MaHeoNai = hn.MaHeo
    LEFT JOIN heo hd ON ss.MaHeoDuc = hd.MaHeo
    LEFT JOIN nhanvien nv ON ss.MaNVThucHien = nv.MaNV
    LEFT JOIN heo h_nai ON ss.MaHeoNai = h_nai.MaHeo
    WHERE ss.MaKH IS NULL  -- CHỈ HIỆN PHIẾU PHỐI TRONG TRẠI, LOẠI BỎ CỦA KHÁCH
    ORDER BY ss.NgayPhoi DESC, ss.SinhSan DESC
    LIMIT ? OFFSET ?
";
        $stmtList = $this->pdo->prepare($sqlList);
        $stmtList->bindValue(1, $limit, PDO::PARAM_INT);
        $stmtList->bindValue(2, $offset, PDO::PARAM_INT);
        $stmtList->execute();

        $sinhSanList = $stmtList->fetchAll(PDO::FETCH_ASSOC);
        // Render view
        $this->view('index', [
            'tongNai'       => $tongNai,
            'tyLe'          => $tyLe,
            'avgCon'        => $avgCon,
            'sapDe'         => $sapDe,
            'listSapDe'     => $listSapDe,
            'sinhSanList'   => $sinhSanList,
            'tongPhoi'      => $tongPhoi,

            // Biến phân trang
            'page'          => $page,
            'totalPages'    => $totalPages,
            'total'         => $total,
        ]);
    }

    // === THÊM MỚI ==

    public function add()
    {
        $kh_id = (int)($_GET['kh_id'] ?? 0);
        $errors = [];

        // --- 1. TẠO MÃ PHIẾU TỰ ĐỘNG (Dùng để hiển thị và lưu) ---
        // Giả sử tên cột trong bảng là MaSinhSan
        try {
            // Đổi MaSinhSan thành SinhSan trong câu lệnh SELECT
            $stmtMax = $this->pdo->query("SELECT SinhSan FROM sinhsan ORDER BY CAST(SinhSan AS UNSIGNED) DESC LIMIT 1");
            $lastRecord = $stmtMax->fetch(PDO::FETCH_ASSOC);

            if ($lastRecord && is_numeric($lastRecord['SinhSan'])) {
                $autoSinhSan = (int)$lastRecord['SinhSan'] + 1;
            } else {
                $autoSinhSan = 1;
            }
        } catch (PDOException $e) {
            $autoSinhSan = 1;
        }

        // --- 2. LẤY DANH SÁCH HEO ĐỰC & HEO CÁI (Giữ nguyên logic của bạn) ---
        if ($kh_id > 0) {
            $dsHeoDucStmt = $this->pdo->prepare("
            SELECT DISTINCT h.MaHeo, h.CanNangHienTai, h.ViTriChuong
            FROM heo h
            INNER JOIN xuatchuong xc ON h.MaHeo = xc.MaHeo
            WHERE xc.MaKH = ? AND h.GioiTinh = 'D'
            ORDER BY h.MaHeo DESC
        ");
            $dsHeoDucStmt->execute([$kh_id]);
            $dsHeoDuc = $dsHeoDucStmt->fetchAll(PDO::FETCH_ASSOC);

            $dsHeoNaiStmt = $this->pdo->prepare("
            SELECT DISTINCT h.MaHeo, h.CanNangHienTai, h.ViTriChuong
            FROM heo h
            INNER JOIN xuatchuong xc ON h.MaHeo = xc.MaHeo
            WHERE xc.MaKH = ? AND h.GioiTinh = 'C'
            ORDER BY h.MaHeo DESC
        ");
            $dsHeoNaiStmt->execute([$kh_id]);
            $dsHeoNai = $dsHeoNaiStmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $dsHeoDuc = $this->pdo->query("
            SELECT MaHeo, CanNangHienTai, ViTriChuong 
            FROM heo 
            WHERE GioiTinh = 'D' AND TrangThaiHeo = 'Bình thường' 
            ORDER BY MaHeo DESC
        ")->fetchAll(PDO::FETCH_ASSOC);

            $dsHeoNai = $this->pdo->query("
            SELECT h.MaHeo, h.CanNangHienTai, h.ViTriChuong
            FROM heo h
            LEFT JOIN sinhsan ss ON h.MaHeo = ss.MaHeoNai AND ss.TrangThai = 'DangTheoDoi'
            WHERE h.GioiTinh = 'C' AND h.TrangThaiHeo = 'Bình thường' AND ss.MaHeoNai IS NULL
            ORDER BY h.MaHeo DESC
        ")->fetchAll(PDO::FETCH_ASSOC);
        }

        $dsNhanVien = $this->pdo->query("SELECT MaNV, HoTen FROM nhanvien ORDER BY HoTen ASC")->fetchAll(PDO::FETCH_ASSOC);

        // --- 3. XỬ LÝ KHI NGƯỜI DÙNG NHẤN LƯU (POST) ---
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $maNai       = trim($_POST['MaHeoNai'] ?? '');
            $maDuc       = trim($_POST['MaHeoDuc'] ?? '');
            $rawNgayPhoi = trim($_POST['NgayPhoi'] ?? '');
            $ghiChu      = trim($_POST['GhiChu'] ?? '');
            $maNV        = $_POST['MaNVThucHien'] ?? null;
            // Lấy lại mã auto từ lúc load trang hoặc tạo mới để đảm bảo tính duy nhất
            $finalMaSS = $autoSinhSan;

            $NgayPhoi = null;
            if ($rawNgayPhoi !== '') {
                $NgayPhoi = date('Y-m-d', strtotime(str_replace('/', '-', $rawNgayPhoi)));
            }

            if (empty($maNai)) $errors[] = "Chọn mã heo cái!";
            if (empty($maDuc)) $errors[] = "Chọn mã heo đực!";
            if (!$NgayPhoi) $errors[] = "Ngày phối không hợp lệ!";
            if ($maNai === $maDuc) $errors[] = "Heo cái và đực không được cùng một con!";
            if (strtotime($NgayPhoi) > time()) $errors[] = "Ngày phối không được ở tương lai!";

            if (empty($errors)) {
                try {
                    // Thêm MaSinhSan vào danh sách cột INSERT
                    $sql = "INSERT INTO sinhsan 
                    (SinhSan, MaHeoNai, MaHeoDuc, NgayPhoi, MaNVThucHien, GhiChu, TrangThai";
                    $params = [$finalMaSS, $maNai, $maDuc, $NgayPhoi, $maNV, $ghiChu, 'DangTheoDoi'];

                    if ($kh_id > 0) {
                        $sql .= ", MaKH";
                        $params[] = $kh_id;
                    }

                    $sql .= ") VALUES (?" . str_repeat(',?', count($params) - 1) . ")";
                    $stmt = $this->pdo->prepare($sql);
                    $stmt->execute($params);

                    $ngayDuSinh = date('d/m/Y', strtotime($NgayPhoi . ' +114 days'));
                    $_SESSION['success'] = "Ghi nhận thành công phiếu $finalMaSS! Dự sinh: $ngayDuSinh";

                    $redirect = $kh_id > 0
                        ? "index.php?url=khachhang/phahe&kh_id=$kh_id"
                        : "index.php?url=sinhsan";

                    header("Location: $redirect");
                    exit;
                } catch (PDOException $e) {
                    $errors[] = "Lỗi database: " . $e->getMessage();
                }
            }
        }

        // --- 4. GỌI VIEW ---
        $this->view('add', [
            'errors'          => $errors,
            'dsHeoNai'        => $dsHeoNai,
            'dsHeoDuc'        => $dsHeoDuc,
            'dsNhanVien'      => $dsNhanVien,
            'kh_id'           => $kh_id,
            'autoMaSinhSan'   => $autoSinhSan // Truyền biến này sang View
        ]);
    }
    public function edit($id = null)
    {
        if ($id === null) $id = $_GET['id'] ?? null;
        $kh_id = (int)($_GET['kh_id'] ?? 0);

        if (!$id) {
            $_SESSION['error'] = "Không tìm thấy mã phiếu!";
            header('Location: index.php?url=sinhsan');
            exit;
        }

        // 1. Lấy dữ liệu phiếu hiện tại
        $stmt = $this->pdo->prepare("SELECT * FROM sinhsan WHERE SinhSan = ?");
        $stmt->execute([$id]);
        $sinhSan = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$sinhSan) {
            $_SESSION['error'] = "Phiếu sinh sản không tồn tại!";
            header('Location: index.php?url=sinhsan');
            exit;
        }

        // 2. Lấy danh sách Heo Đực & Heo Cái (Tối ưu để không bị trống)
        if ($kh_id > 0) {
            // Trường hợp heo của khách hàng
            $dsHeoDucStmt = $this->pdo->prepare("
            SELECT DISTINCT h.MaHeo, h.CanNangHienTai 
            FROM heo h INNER JOIN xuatchuong xc ON h.MaHeo = xc.MaHeo 
            WHERE xc.MaKH = ? AND h.GioiTinh = 'D'
            UNION 
            SELECT MaHeo, CanNangHienTai FROM heo WHERE MaHeo = ?
        ");
            $dsHeoDucStmt->execute([$kh_id, $sinhSan['MaHeoDuc']]);
            $dsHeoDuc = $dsHeoDucStmt->fetchAll(PDO::FETCH_ASSOC);

            $dsHeoNaiStmt = $this->pdo->prepare("
            SELECT DISTINCT h.MaHeo, h.CanNangHienTai 
            FROM heo h INNER JOIN xuatchuong xc ON h.MaHeo = xc.MaHeo 
            WHERE xc.MaKH = ? AND h.GioiTinh = 'C'
            UNION 
            SELECT MaHeo, CanNangHienTai FROM heo WHERE MaHeo = ?
        ");
            $dsHeoNaiStmt->execute([$kh_id, $sinhSan['MaHeoNai']]);
            $dsHeoNai = $dsHeoNaiStmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            // Trường hợp heo tại trại: Lấy heo bình thường HOẶC con heo đang nằm trong phiếu này
            $dsHeoDucStmt = $this->pdo->prepare("
            SELECT MaHeo, CanNangHienTai FROM heo 
            WHERE (GioiTinh = 'D' AND TrangThaiHeo = 'Bình thường') OR MaHeo = ?
        ");
            $dsHeoDucStmt->execute([$sinhSan['MaHeoDuc']]);
            $dsHeoDuc = $dsHeoDucStmt->fetchAll(PDO::FETCH_ASSOC);

            $dsHeoNaiStmt = $this->pdo->prepare("
            SELECT MaHeo, CanNangHienTai FROM heo 
            WHERE (GioiTinh = 'C' AND TrangThaiHeo = 'Bình thường') OR MaHeo = ?
        ");
            $dsHeoNaiStmt->execute([$sinhSan['MaHeoNai']]);
            $dsHeoNai = $dsHeoNaiStmt->fetchAll(PDO::FETCH_ASSOC);
        }

        $dsNhanVien = $this->pdo->query("SELECT MaNV, HoTen FROM nhanvien ORDER BY HoTen ASC")->fetchAll(PDO::FETCH_ASSOC);
        $errors = [];

        // 3. Xử lý lưu dữ liệu (POST)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $maNai       = trim($_POST['MaHeoNai'] ?? '');
            $maDuc       = trim($_POST['MaHeoDuc'] ?? '');
            $rawNgayPhoi = trim($_POST['NgayPhoi'] ?? '');
            $ghiChu      = trim($_POST['GhiChu'] ?? '');
            $maNV        = !empty($_POST['MaNVThucHien']) ? $_POST['MaNVThucHien'] : null;
            $trangThai   = $_POST['TrangThai'] ?? 'DangTheoDoi';

            // Dữ liệu đẻ thực tế
            $ngayDe      = !empty($_POST['NgayDeThucTe']) ? $_POST['NgayDeThucTe'] : null;
            $soConSong   = isset($_POST['SoConSong']) ? (int)$_POST['SoConSong'] : null;
            $soConChet   = isset($_POST['SoConChet']) ? (int)$_POST['SoConChet'] : 0;

            $NgayPhoi = ($rawNgayPhoi !== '') ? date('Y-m-d', strtotime(str_replace('/', '-', $rawNgayPhoi))) : null;

            if (empty($maNai)) $errors[] = "Vui lòng chọn heo cái!";
            if (empty($maDuc)) $errors[] = "Vui lòng chọn heo đực!";
            if (!$NgayPhoi) $errors[] = "Ngày phối không hợp lệ!";

            if (empty($errors)) {
                try {
                    // Sửa lỗi: Sử dụng cột 'SinhSan' làm khóa chính, không dùng 'MaSinhSan'
                    $sql = "UPDATE sinhsan SET 
                        MaHeoNai = ?, MaHeoDuc = ?, NgayPhoi = ?, 
                        MaNVThucHien = ?, GhiChu = ?, TrangThai = ?, 
                        NgayDe = ?, SoConSong = ?, SoConChet = ? 
                        WHERE SinhSan = ?";

                    $params = [$maNai, $maDuc, $NgayPhoi, $maNV, $ghiChu, $trangThai, $ngayDe, $soConSong, $soConChet, $id];

                    $this->pdo->prepare($sql)->execute($params);

                    $_SESSION['success'] = "Cập nhật thành công!";
                    header("Location: " . ($kh_id > 0 ? "index.php?url=khachhang/phahe&kh_id=$kh_id" : "index.php?url=sinhsan"));
                    exit;
                } catch (PDOException $e) {
                    $errors[] = "Lỗi database: " . $e->getMessage();
                }
            }
        }

        $this->view('edit', [
            'data'       => $sinhSan,
            'dsHeoNai'   => $dsHeoNai,
            'dsHeoDuc'   => $dsHeoDuc,
            'dsNhanVien' => $dsNhanVien,
            'errors'     => $errors,
            'kh_id'      => $kh_id
        ]);
    }

    // === XÓA PHIẾU SINH SẢN ===
    public function delete($id = null)
    {
        // Lấy ID từ parameter hoặc GET (hỗ trợ cả ?url=sinhsan/delete&id=5 và /delete/5)
        $id = (int)($id ?? $_GET['id'] ?? 0);

        if ($id <= 0) {
            $_SESSION['error'] = "Mã phiếu sinh sản không hợp lệ!";
            header('Location: index.php?url=sinhsan');
            exit;
        }

        // === KIỂM TRA QUYỀN (CHỈ ADMIN ĐƯỢC XÓA) ===
        // Nếu bạn có session role, ví dụ: $_SESSION['user']['role'] === 'admin'
        // Nếu chưa có hệ thống phân quyền thì comment phần này tạm
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] ?? '' !== 'admin') {
            $_SESSION['error'] = "Bạn không có quyền xóa phiếu sinh sản!";
            header('Location: index.php?url=sinhsan');
            exit;
        }

        // === KIỂM TRA PHIẾU CÓ TỒN TẠI KHÔNG ===
        $stmt = $this->pdo->prepare("SELECT SinhSan, MaHeoNai FROM sinhsan WHERE SinhSan = ?");
        $stmt->execute([$id]);
        $phieu = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$phieu) {
            $_SESSION['error'] = "Phiếu sinh sản không tồn tại!";
            header('Location: index.php?url=sinhsan');
            exit;
        }

        // === THỰC HIỆN XÓA ===
        try {
            $stmtDelete = $this->pdo->prepare("DELETE FROM sinhsan WHERE SinhSan = ?");
            $stmtDelete->execute([$id]);

            // Kiểm tra có xóa thành công không (rowCount > 0)
            if ($stmtDelete->rowCount() > 0) {
                $_SESSION['success'] = "Xóa phiếu sinh sản #{$id} (Heo nái: {$phieu['MaHeoNai']}) thành công!";
            } else {
                $_SESSION['error'] = "Không thể xóa phiếu này (có thể đã bị xóa trước đó)!";
            }
        } catch (PDOException $e) {
            // Log lỗi nếu cần (không hiển thị cho user)
            error_log("Lỗi xóa phiếu sinh sản ID $id: " . $e->getMessage());
            $_SESSION['error'] = "Lỗi hệ thống khi xóa phiếu. Vui lòng thử lại!";
        }

        // === CHUYỂN HƯỚNG VỀ TRANG CHÍNH ===
        header('Location: index.php?url=sinhsan');
        exit;
    }


    public function khachAdd($kh_id = null)
    {
        $kh_id = (int)($kh_id ?? $_GET['kh_id'] ?? 0);

        if ($kh_id <= 0) {
            $_SESSION['error'] = "Không xác định khách hàng!";
            header('Location: index.php?url=sinhsan');
            exit;
        }

        // Lấy thông tin khách để hiển thị tiêu đề (tùy chọn)
        // Giả sử bạn có KhachHangModel, nếu không thì bỏ qua
        // $khach = $this->khachModel->getById($kh_id); // Nếu có inject model

        $errors = [];

        // Heo đực đã xuất cho khách này
        $dsHeoDucStmt = $this->pdo->prepare("
        SELECT DISTINCT h.MaHeo, h.CanNangHienTai, h.GioiTinh, h.NgaySinh
        FROM heo h
        INNER JOIN xuatchuong xc ON h.MaHeo = xc.MaHeo
        WHERE xc.MaKH = ? AND h.GioiTinh = 'D'
        ORDER BY h.MaHeo DESC
    ");
        $dsHeoDucStmt->execute([$kh_id]);
        $dsHeoDuc = $dsHeoDucStmt->fetchAll(PDO::FETCH_ASSOC);

        // Heo cái đã xuất cho khách này
        $dsHeoNaiStmt = $this->pdo->prepare("
        SELECT DISTINCT h.MaHeo, h.CanNangHienTai, h.GioiTinh, h.NgaySinh
        FROM heo h
        INNER JOIN xuatchuong xc ON h.MaHeo = xc.MaHeo
        WHERE xc.MaKH = ? AND h.GioiTinh = 'C'
        ORDER BY h.MaHeo DESC
    ");
        $dsHeoNaiStmt->execute([$kh_id]);
        $dsHeoNai = $dsHeoNaiStmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($dsHeoDuc) && empty($dsHeoNai)) {
            $_SESSION['error'] = "Khách này chưa có heo nào được xuất (không thể ghi phối giống).";
            header("Location: index.php?url=khachhang/phahe&kh_id=$kh_id");
            exit;
        }

        $dsNhanVien = $this->pdo->query("SELECT MaNV, HoTen FROM nhanvien ORDER BY HoTen ASC")->fetchAll(PDO::FETCH_ASSOC);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $maNai       = trim($_POST['MaHeoNai'] ?? '');
            $maDuc       = trim($_POST['MaHeoDuc'] ?? '');
            $rawNgayPhoi = trim($_POST['NgayPhoi'] ?? '');
            $ghiChu      = trim($_POST['GhiChu'] ?? '');
            $maNV        = $_POST['MaNVThucHien'] ?? null;

            $NgayPhoi = null;
            if ($rawNgayPhoi !== '') {
                $NgayPhoi = date('Y-m-d', strtotime(str_replace('/', '-', $rawNgayPhoi)));
            }

            if (empty($maNai)) $errors[] = "Chọn heo cái!";
            if (empty($maDuc)) $errors[] = "Chọn heo đực!";
            if (!$NgayPhoi) $errors[] = "Ngày phối không hợp lệ!";
            if ($maNai === $maDuc) $errors[] = "Heo cái và đực không được trùng!";
            if (strtotime($NgayPhoi) > time()) $errors[] = "Ngày phối không được lớn hơn hôm nay!";

            if (empty($errors)) {
                try {
                    $stmt = $this->pdo->prepare("
                    INSERT INTO sinhsan 
                    (MaHeoNai, MaHeoDuc, NgayPhoi, MaNVThucHien, GhiChu, TrangThai, MaKH) 
                    VALUES (?, ?, ?, ?, ?, 'DangTheoDoi', ?)
                ");
                    $stmt->execute([$maNai, $maDuc, $NgayPhoi, $maNV, $ghiChu, $kh_id]);

                    $ngayDuSinh = date('d/m/Y', strtotime($NgayPhoi . ' +114 days'));
                    $_SESSION['success'] = "Ghi nhận phối giống cho khách thành công! Đực #$maDuc phối với Cái #$maNai - Dự sinh: $ngayDuSinh";

                    header("Location: index.php?url=khachhang/phahe&kh_id=$kh_id");
                    exit;
                } catch (PDOException $e) {
                    $errors[] = "Lỗi database: " . $e->getMessage();
                }
            }
        }

        // Gọi view riêng cho phối khách (tạo file khach_add.php)
        $this->view('khach_add', [
            'errors'      => $errors,
            'dsHeoNai'    => $dsHeoNai,
            'dsHeoDuc'    => $dsHeoDuc,
            'dsNhanVien'  => $dsNhanVien,
            'kh_id'       => $kh_id
        ]);
    }


    // === GHI NHẬN ĐẺ ===
    public function ghiNhanDe($id = null)
    {
        // Lấy ID từ GET (khi load form) hoặc POST (khi nhấn Lưu)
        $id = (int)($id ?? $_GET['id'] ?? $_POST['ghiNhanDe_id'] ?? 0);

        if (!$id) {
            header('Location: index.php?url=sinhsan');
            exit;
        }

        // 1. LẤY THÔNG TIN ĐỂ HIỂN THỊ (GET)
        $stmt = $this->pdo->prepare("
        SELECT ss.*, h.ViTriChuong
        FROM sinhsan ss
        JOIN heo h ON ss.MaHeoNai = h.MaHeo
        WHERE ss.SinhSan = ?
    ");
        $stmt->execute([$id]);
        $sinhSan = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$sinhSan) {
            $_SESSION['error'] = "Không tìm thấy phiếu sinh sản!";
            header('Location: index.php?url=sinhsan');
            exit;
        }

        // 2. XỬ LÝ KHI NHẤN NÚT "GHI NHẬN" (POST)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ngayDe   = trim($_POST['NgayDe'] ?? '');
            $soSong   = (int)($_POST['SoConSong'] ?? 0); // Lấy từ form thay vì cố định 10
            $soChet   = (int)($_POST['SoConChet'] ?? 0);
            $maNVDe   = trim($_POST['MaNVDe'] ?? '');
            $ghiChuDe = trim($_POST['GhiChuDe'] ?? '');

            if (empty($ngayDe)) {
                $_SESSION['error'] = "Vui lòng chọn ngày đẻ thực tế!";
            } else {
                // Chuẩn bị dữ liệu cập nhật, thêm TrangThai = 'ThanhCong'
                $dataUpdate = [
                    'NgayDe'    => $ngayDe,
                    'SoConSong' => $soSong,
                    'SoConChet' => $soChet,
                    'MaNVDe'    => $maNVDe ?: null,
                    'GhiChuDe'  => $ghiChuDe ?: null,
                    'TrangThai' => 'ThanhCong'
                ];

                $result = $this->model->updateGhiNhanDe($id, $dataUpdate);

                if (isset($result['status']) && $result['status'] === true) {
                    $_SESSION['success'] = "Ghi nhận đẻ thành công cho heo nái #" . $sinhSan['MaHeoNai'];
                } else {
                    $_SESSION['error'] = "Lỗi database: " . ($result['message'] ?? 'Không thể cập nhật');
                }
            }

            header('Location: index.php?url=sinhsan');
            exit;
        }

        // 3. HIỂN THỊ FORM
        $ngayDuSinh = date('Y-m-d', strtotime($sinhSan['NgayPhoi'] . ' +114 days'));
        $dsNhanVien = $this->pdo->query("SELECT MaNV, HoTen FROM nhanvien ORDER BY HoTen")->fetchAll(PDO::FETCH_ASSOC);

        $this->view('ghiNhanDe', compact('sinhSan', 'ngayDuSinh', 'dsNhanVien'));
    }
}
