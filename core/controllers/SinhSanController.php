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
        $sqlList = "
            SELECT 
                ss.*,
                ss.NgayDe, -- Đảm bảo lấy cột này ra
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

    // === THÊM MỚI ===
    public function add()
    {
        $errors = [];

        $dsHeoDuc = $this->pdo->query("SELECT MaHeo, CanNangHienTai, ViTriChuong FROM heo WHERE GioiTinh = 'D' AND TrangThaiHeo = 'Bình thường' ORDER BY MaHeo DESC")->fetchAll(PDO::FETCH_ASSOC);

        $dsHeoNai = $this->pdo->query("
            SELECT h.MaHeo, h.CanNangHienTai, h.ViTriChuong
            FROM heo h
            LEFT JOIN sinhsan ss ON h.MaHeo = ss.MaHeoNai AND ss.TrangThai = 'DangTheoDoi'
            WHERE h.GioiTinh = 'C' AND h.TrangThaiHeo = 'Bình thường' AND ss.MaHeoNai IS NULL
            ORDER BY h.MaHeo DESC
        ")->fetchAll(PDO::FETCH_ASSOC);

        $dsNhanVien = $this->pdo->query("SELECT MaNV, HoTen FROM nhanvien ORDER BY HoTen ASC")->fetchAll(PDO::FETCH_ASSOC);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $maNai    = trim($_POST['MaHeoNai'] ?? '');
            $maDuc    = trim($_POST['MaHeoDuc'] ?? '');
            $ngayPhoi = $_POST['NgayPhoi'] ?? '';

            $ghiChu   = trim($_POST['GhiChu'] ?? '');
            $maNV     = $_POST['MaNVThucHien'] ?? null;

            if (empty($maNai)) $errors[] = "Chọn heo nái!";
            if (empty($maDuc)) $errors[] = "Chọn heo đực!";
            if (empty($ngayPhoi)) $errors[] = "Chọn ngày phối!";
            if ($maNai === $maDuc) $errors[] = "Nái và đực không được trùng!";
            if (strtotime($ngayPhoi) > time()) $errors[] = "Ngày phối không được lớn hơn hôm nay!";

            if (empty($errors)) {
                $check = $this->pdo->prepare("SELECT 1 FROM sinhsan WHERE MaHeoNai = ? AND TrangThai = 'DangTheoDoi'");
                $check->execute([$maNai]);
                if ($check->rowCount() > 0) $errors[] = "Heo nái <strong>$maNai</strong> đang mang thai!";
            }

            if (empty($errors)) {
                try {
                    $stmt = $this->pdo->prepare("INSERT INTO sinhsan (MaHeoNai, MaHeoDuc, NgayPhoi, MaNVThucHien, GhiChu, TrangThai) VALUES (?, ?, ?, ?, ?, 'DangTheoDoi')");
                    $stmt->execute([$maNai, $maDuc, $ngayPhoi, $maNV, $ghiChu]);

                    $ngayDuSinh = date('d/m/Y', strtotime($ngayPhoi . ' +114 days'));
                    $_SESSION['success'] = "Ghi nhận phối giống thành công! Nái <strong>#$maNai</strong> dự sinh: <strong>$ngayDuSinh</strong>";

                    header('Location: index.php?url=sinhsan/index');
                    exit;
                } catch (PDOException $e) {
                    $errors[] = "Lỗi: " . $e->getMessage();
                }
            }
            if (!empty($ngayPhoi)) {
                // Chuyển đổi dd-mm-yyyy hoặc dd/mm/yyyy → yyyy-mm-dd
                $ngayPhoi = str_replace('/', '-', $ngayPhoi);
                $ngayPhoi = date('Y-m-d', strtotime($ngayPhoi));
            }
        }

        $this->view('add', compact('errors', 'dsHeoNai', 'dsHeoDuc', 'dsNhanVien'));
    }

    // === SỬA – ĐÃ FIX 404 HOÀN TOÀN (hỗ trợ cả &id= và /id) ===
    public function edit($id = null)
    {
        if ($id === null) $id = $_GET['id'] ?? null;

        if (!$id) {
            $_SESSION['error'] = "Không tìm thấy mã phiếu!";
            header('Location: index.php?url=sinhsan/index');
            exit;
        }

        // Lấy dữ liệu cũ
        $stmt = $this->pdo->prepare("SELECT * FROM sinhsan WHERE SinhSan = ?");
        $stmt->execute([$id]);
        $sinhSan = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$sinhSan) {
            $_SESSION['error'] = "Phiếu sinh sản không tồn tại!";
            header('Location: index.php?url=sinhsan/index');
            exit;
        }

        // Lấy danh sách Dropdown (Giữ nguyên logic lọc đực/nái bình thường)
        $dsHeoDuc = $this->pdo->query("SELECT MaHeo, CanNangHienTai, ViTriChuong FROM heo WHERE GioiTinh = 'D' AND TrangThaiHeo = 'Bình thường' ORDER BY MaHeo DESC")->fetchAll(PDO::FETCH_ASSOC);

        // Nái ở trang EDIT cần hiện cả con nái hiện tại của phiếu đó + các con nái đang rảnh
        $dsHeoNai = $this->pdo->query("
        SELECT h.MaHeo, h.CanNangHienTai, h.ViTriChuong
        FROM heo h
        LEFT JOIN sinhsan ss ON h.MaHeo = ss.MaHeoNai AND ss.TrangThai = 'DangTheoDoi'
        WHERE h.GioiTinh = 'C' AND h.TrangThaiHeo = 'Bình thường' 
        AND (ss.MaHeoNai IS NULL OR h.MaHeo = '{$sinhSan['MaHeoNai']}')
        ORDER BY h.MaHeo DESC
    ")->fetchAll(PDO::FETCH_ASSOC);

        $dsNhanVien = $this->pdo->query("SELECT MaNV, HoTen FROM nhanvien ORDER BY HoTen ASC")->fetchAll(PDO::FETCH_ASSOC);
        $errors = [];

        // Xử lý POST khi bấm lưu
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $maNai       = trim($_POST['MaHeoNai'] ?? '');
            $maDuc       = trim($_POST['MaHeoDuc'] ?? '');
            $rawNgayPhoi = trim($_POST['NgayPhoi'] ?? '');
            $ghiChu      = trim($_POST['GhiChu'] ?? '');
            $maNV        = $_POST['MaNVThucHien'] ?? null;
            $trangThai   = $_POST['TrangThai'] ?? 'DangTheoDoi';

            // Validate ngày phối
            $NgayPhoi = null;
            if ($rawNgayPhoi !== '') {
                $NgayPhoi = date('Y-m-d', strtotime(str_replace('/', '-', $rawNgayPhoi)));
            }

            if (empty($maNai)) $errors[] = "Chọn heo nái!";
            if (empty($maDuc)) $errors[] = "Chọn heo đực!";
            if (!$NgayPhoi) $errors[] = "Ngày phối không hợp lệ!";

            if (empty($errors)) {
                try {
                    $sql = "UPDATE sinhsan SET 
                        MaHeoNai = ?, MaHeoDuc = ?, NgayPhoi = ?, 
                        MaNVThucHien = ?, GhiChu = ?, TrangThai = ? 
                        WHERE SinhSan = ?";
                    $stmt = $this->pdo->prepare($sql);
                    $stmt->execute([$maNai, $maDuc, $NgayPhoi, $maNV, $ghiChu, $trangThai, $id]);

                    $_SESSION['success'] = "Cập nhật thành công!";
                    header('Location: index.php?url=sinhsan/index');
                    exit;
                } catch (PDOException $e) {
                    $errors[] = "Lỗi database: " . $e->getMessage();
                }
            }
        }

        // GỌI VIEW: Quan trọng là truyền đúng tên 'data'
        $this->view('edit', [
            'data'       => $sinhSan, // Đổi từ 'sinhSan' thành 'data' để khớp với View
            'dsHeoNai'   => $dsHeoNai,
            'dsHeoDuc'   => $dsHeoDuc,
            'dsNhanVien' => $dsNhanVien,
            'errors'     => $errors
        ]);
    }


    // === GHI NHẬN ĐẺ ===
    public function ghiNhanDe($id = null)
    {
        $id = (int)($id ?? $_GET['id'] ?? 0);

        // Nếu không có ID → quay về trang chính
        if (!$id) {
            header('Location: index.php?url=sinhsan');
            exit;
        }

        // LẤY THÔNG TIN PHIẾU SINH SẢN
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

        // Nếu đã ghi nhận đẻ rồi → không cho làm lại
        if ($sinhSan['SoConSong'] !== null) {
            $_SESSION['error'] = "Phiếu này đã được ghi nhận đẻ rồi!";
            header('Location: index.php?url=sinhsan');
            exit;
        }

        // XỬ LÝ KHI NHẤN NÚT "GHI NHẬN" (POST)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ngayDe   = trim($_POST['NgayDe'] ?? '');
            $soChet   = (int)($_POST['SoConChet'] ?? 0);
            $maNVDe   = trim($_POST['MaNVDe'] ?? '');
            $ghiChuDe = trim($_POST['GhiChuDe'] ?? '');

            if (empty($ngayDe)) {
                $_SESSION['error'] = "Vui lòng chọn ngày đẻ thực tế!";
            } else {
                $soSong = 10; // Cố định 10 con sống theo yêu cầu

                $dataUpdate = [
                    'NgayDe'    => $ngayDe,
                    'SoConSong' => $soSong,
                    'SoConChet' => $soChet,
                    'MaNVDe'    => $maNVDe ?: null,
                    'GhiChuDe'  => $ghiChuDe ?: null,
                ];

                $result = $this->model->updateGhiNhanDe($id, $dataUpdate);

                if (isset($result['status']) && $result['status'] === true) {
                    $_SESSION['success'] = "Ghi nhận đẻ thành công! Phiếu #{$id} - 10 con sống.";
                } else {
                    $_SESSION['error'] = "Lỗi ghi nhận đẻ: " . ($result['message'] ?? 'Không xác định');
                }
            }

            // === LUÔN REDIRECT VỀ TRANG CHÍNH SINH SẢN SAU KHI XỬ LÝ ===
            header('Location: index.php?url=sinhsan');
            exit;
        }

        // ================== HIỂN THỊ FORM (CHỈ KHI GET) ==================
        $ngayDuSinh = date('Y-m-d', strtotime($sinhSan['NgayPhoi'] . ' +114 days'));

        $dsNhanVien = $this->pdo->query("
        SELECT MaNV, HoTen 
        FROM nhanvien 
        ORDER BY HoTen
    ")->fetchAll(PDO::FETCH_ASSOC);

        $this->view('ghiNhanDe', compact('sinhSan', 'ngayDuSinh', 'dsNhanVien'));
    }
}
