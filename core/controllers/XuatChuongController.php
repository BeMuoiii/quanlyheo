
<?php
// core/controllers/XuatChuongController.php
require_once __DIR__ . '/../models/XuatChuongModel.php';
class XuatChuongController
{
    protected $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    private function view($viewName, $data = [])
    {
        extract($data);
        $viewPath = __DIR__ . '/../../admin/views/xuatchuong/' . $viewName . '.php';
        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            die("Không tìm thấy view: $viewPath");
        }
    }

    // DANH SÁCH + CÁC HÀM BẠN ĐÃ VIẾT

    public function index()
    {
        $xuats = [];
        $error = null;
        $tenKH = 'Danh sách xuất chuồng heo';

        // Lấy các tham số lọc và tìm kiếm
        $kh_id = (int)($_GET['kh_id'] ?? 0);
        $keyword = trim($_GET['timkiem'] ?? ''); // THÊM DÒNG NÀY

        // Cấu hình phân trang
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 10;
        $offset = ($page - 1) * $limit;

        try {
            // 1. Xử lý điều kiện lọc (WHERE)
            $whereClauses = ["1=1"];
            $params = [];

            // Lọc theo khách hàng cụ thể (từ link "Lịch sử xuất")
            if ($kh_id > 0) {
                $whereClauses[] = "xc.MaKH = ?";
                $params[] = $kh_id;

                $stmtTen = $this->pdo->prepare("SELECT TenKH FROM khachhang WHERE MaKH = ?");
                $stmtTen->execute([$kh_id]);
                $ten = $stmtTen->fetchColumn();
                $tenKH = $ten ? "Heo đã xuất cho: " . htmlspecialchars($ten) : "Khách hàng không tồn tại";
            }

            // Lọc theo từ khóa tìm kiếm (THÊM DÒNG NÀY)
            if ($keyword !== '') {
                $whereClauses[] = "(kh.TenKH LIKE ? OR kh.SDT LIKE ? OR xc.MaHeo LIKE ? OR xc.GhiChu LIKE ?)";
                $searchParam = "%$keyword%";
                $params = array_merge($params, [$searchParam, $searchParam, $searchParam, $searchParam]);
            }

            $where = " WHERE " . implode(" AND ", $whereClauses);

            // 2. Tính tổng số dòng để phân trang (Dùng $where đã có tìm kiếm)
            $sqlCount = "SELECT COUNT(*) FROM xuatchuong xc 
                     LEFT JOIN khachhang kh ON xc.MaKH = kh.MaKH 
                     $where";
            $stmtCount = $this->pdo->prepare($sqlCount);
            $stmtCount->execute($params);
            $totalRecords = (int)$stmtCount->fetchColumn();
            $totalPages = ceil($totalRecords / $limit);

            // 3. Tính số lượng xuất trong tuần cho Dashboard
            $sqlXuatTuan = "SELECT SUM(SoLuong) FROM xuatchuong 
                        WHERE YEARWEEK(NgayXuat, 1) = YEARWEEK(CURDATE(), 1)";
            $heoXuatTuanNay = (int)$this->pdo->query($sqlXuatTuan)->fetchColumn();

            // 4. Truy vấn danh sách chính có JOIN để tìm kiếm được tên KH
            $sql = "
            SELECT 
                xc.*,
                h.MaHeo,
                CONCAT('Heo ', h.MaHeo, ' - ', COALESCE(h.GiongHeo, '')) AS TenHeoHienThi,
                COALESCE(kh.TenKH, 'Khách lẻ') AS TenKH,
                COALESCE(kh.SDT, '-') AS SDT_KH,
                COALESCE(nv.HoTen, 'Hệ thống') AS NhanVienThucHien
            FROM xuatchuong xc
            LEFT JOIN heo h ON xc.MaHeo = h.MaHeo
            LEFT JOIN khachhang kh ON xc.MaKH = kh.MaKH
            LEFT JOIN nhanvien nv ON xc.MaNVThucHien = nv.MaNV
            $where
            ORDER BY xc.NgayXuat DESC, xc.MaXuat DESC
            LIMIT $limit OFFSET $offset
        ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $xuats = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $error = "Lỗi hệ thống: " . $e->getMessage();
        }

        $this->view('index', [
            'xuats'          => $xuats,
            'heoXuatTuanNay' => $heoXuatTuanNay,
            'tenKH'          => $tenKH,
            'kh_id'          => $kh_id,
            'keyword'        => $keyword, // Truyền từ khóa ra View để hiển thị lại trong ô input
            'page'           => $page,
            'totalPages'     => $totalPages,
            'totalRecords'   => $totalRecords,
            'limit'          => $limit,
            'error'          => $error
        ]);
    }


    // FORM THÊM PHIẾU XUẤT
    // ========================================
    public function add()
    {
        $errors = [];

        // Lấy dữ liệu cho form
        $dsHeoChuaXuat = $this->pdo->query("
        SELECT MaHeo, GiongHeo, CanNangHienTai, ViTriChuong, GioiTinh
        FROM heo 
        WHERE TrangThaiHeo != 'Đã xuất' AND TrangThaiHeo != 'Chết'
        ORDER BY MaHeo DESC
    ")->fetchAll(PDO::FETCH_ASSOC);

        $dsKhachHang = $this->pdo->query("SELECT MaKH, TenKH, SDT FROM khachhang ORDER BY TenKH")->fetchAll(PDO::FETCH_ASSOC);
        $dsNhanVien = $this->pdo->query("SELECT MaNV, HoTen FROM nhanvien ORDER BY HoTen")->fetchAll(PDO::FETCH_ASSOC);

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $MaHeo        = trim($_POST['MaHeo'] ?? '');
            $MaKH  = !empty($_POST['MaKH']) ? (int)$_POST['MaKH'] : null;
            $NgayXuat     = $_POST['NgayXuat'] ?? date('Y-m-d H:i:s');
            $CanNangXuat  = (float)str_replace(',', '.', $_POST['CanNangXuat'] ?? 0);
            $SoLuong      = (int)($_POST['SoLuong'] ?? 1);
            $DonGia       = (float)str_replace(['.', ' '], '', $_POST['DonGia'] ?? 0);
            $LyDoXuat     = trim($_POST['LyDoXuat'] ?? 'Bán thịt');
            $GhiChu       = trim($_POST['GhiChu'] ?? '');
            $MaNVThucHien = $_SESSION['MaNV'] ?? null;

            // Validate
            if (empty($MaHeo)) $errors[] = "Chọn heo để xuất!";
            if ($CanNangXuat <= 0) $errors[] = "Cân nặng phải > 0!";
            if ($DonGia <= 0) $errors[] = "Đơn giá phải > 0!";

            if (empty($errors)) {
                $ThanhTien = $CanNangXuat * $DonGia * $SoLuong;

                try {
                    $this->pdo->beginTransaction();

                    // ĐÃ SỬA ĐÚNG TÊN CỘT: NgayXuat, ThanhTien, MaNVThucHien
                    $sql = "INSERT INTO xuatchuong 
                        (MaHeo, MaKH, NgayXuat, CanNangXuat, SoLuong, DonGia, ThanhTien, LyDoXuat, GhiChu, MaNVThucHien)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $this->pdo->prepare($sql)->execute([
                        $MaHeo,
                        $MaKH,
                        $NgayXuat,
                        $CanNangXuat,
                        $SoLuong,
                        $DonGia,
                        $ThanhTien,
                        $LyDoXuat,
                        $GhiChu,
                        $MaNVThucHien
                    ]);

                    // Cập nhật trạng thái heo
                    $this->pdo->prepare("UPDATE heo SET TrangThaiHeo = 'Đã xuất' WHERE MaHeo = ?")
                        ->execute([$MaHeo]);

                    $this->pdo->commit();

                    $_SESSION['success'] = "Xuất thành công heo <strong>$MaHeo</strong> – Tổng tiền: <strong>" . number_format($ThanhTien) . "đ</strong>";
                    header('Location: index.php?url=xuatchuong');
                    exit;
                } catch (Exception $e) {
                    $this->pdo->rollBack();
                    $errors[] = "Lỗi hệ thống: " . $e->getMessage();
                }
            }
        }

        $this->view('add', compact('errors', 'dsHeoChuaXuat', 'dsKhachHang', 'dsNhanVien'));
    }


    public function edit($id)
    {
        $id = (int)$id;

        // 1. Lấy thông tin phiếu xuất hiện tại
        $stmt = $this->pdo->prepare("SELECT * FROM xuatchuong WHERE MaXuat = ?");
        $stmt->execute([$id]);
        $xuatchuong = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$xuatchuong) {
            $_SESSION['error'] = "Không tìm thấy phiếu xuất chuồng!";
            header('Location: index.php?url=xuatchuong');
            exit;
        }

        // 2. Lấy danh sách heo (Dùng prepare đúng như ảnh 1 bạn đã sửa)
        $stmtHeo = $this->pdo->prepare("
        SELECT MaHeo, GiongHeo, CanNangHienTai, 
               CONCAT('Heo ', MaHeo, ' - ', IFNULL(GiongHeo,''), ' - ', CanNangHienTai, 'kg') AS TenHeo
        FROM heo 
        WHERE TrangThaiHeo != 'Đã xuất' OR MaHeo = ?
        ORDER BY MaHeo DESC
    ");
        $stmtHeo->execute([$xuatchuong['MaHeo']]);
        $dsHeoChuaXuat = $stmtHeo->fetchAll(PDO::FETCH_ASSOC);

        // 3. Danh sách khách hàng & nhân viên
        $dsKhachHang = $this->pdo->query("SELECT MaKH, TenKH FROM khachhang ORDER BY TenKH")->fetchAll();
        $dsNhanVien  = $this->pdo->query("SELECT MaNV, HoTen FROM nhanvien ORDER BY HoTen")->fetchAll();

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $postData = [
                'MaXuat'       => $id,
                'MaHeo'        => $_POST['MaHeo'] ?? '',
                'MaKH'         => !empty($_POST['MaKH']) ? (int)$_POST['MaKH'] : null,
                'NgayXuat'     => $_POST['NgayXuat'] ?? $xuatchuong['NgayXuat'],
                'CanNangXuat'  => (float)str_replace(',', '.', $_POST['CanNangXuat'] ?? 0),
                'SoLuong'      => (int)($_POST['SoLuong'] ?? 1),
                'DonGia'       => (float)str_replace(['.', ' '], '', $_POST['DonGia'] ?? 0),
                'LyDoXuat'     => trim($_POST['LyDoXuat'] ?? 'Bán thịt'),
                'GhiChu'       => trim($_POST['GhiChu'] ?? ''),
                'MaNVThucHien' => $_SESSION['MaNV'] ?? $xuatchuong['MaNVThucHien']
            ];

            if (empty($postData['MaHeo'])) $errors[] = "Vui lòng chọn heo!";
            if ($postData['CanNangXuat'] <= 0) $errors[] = "Cân nặng phải lớn hơn 0!";
            if ($postData['DonGia'] <= 0) $errors[] = "Đơn giá phải lớn hơn 0!";

            if (empty($errors)) {
                try {
                    // Khởi tạo Model ngay tại đây
                    $model = new XuatChuongModel($this->pdo);

                    if ($model->update($postData)) {
                        $_SESSION['success'] = "Cập nhật phiếu xuất thành công!";
                        header('Location: index.php?url=xuatchuong');
                        exit;
                    }
                } catch (Exception $e) {
                    $errors[] = "Lỗi hệ thống: " . $e->getMessage();
                }
            }
        }

        $this->view('edit', compact('xuatchuong', 'dsHeoChuaXuat', 'dsKhachHang', 'dsNhanVien', 'errors'));
    }



    public function delete($id = null)
    {
        $id = $id ?? $_GET['id'] ?? null;
        if (!$id) {
            $_SESSION['error'] = "Thiếu ID!";
            header('Location: index.php?url=xuatchuong');
            exit;
        }

        try {
            $this->pdo->beginTransaction();

            // 1. Lấy mã heo từ phiếu xuất trước khi xóa
            $stmt = $this->pdo->prepare("SELECT MaHeo FROM xuatchuong WHERE MaXuat = ?");
            $stmt->execute([$id]);
            $MaHeo = $stmt->fetchColumn();

            // 2. Thực hiện xóa phiếu xuất
            $this->pdo->prepare("DELETE FROM xuatchuong WHERE MaXuat = ?")->execute([$id]);

            // 3. Nếu có mã heo, hoàn lại trạng thái và thông báo cụ thể
            if ($MaHeo) {
                $this->pdo->prepare("UPDATE heo SET TrangThaiHeo = 'Bình thường' WHERE MaHeo = ?")
                    ->execute([$MaHeo]);

                // Hiện mã heo ở đây
                $_SESSION['success'] = "Đã xóa phiếu xuất #" . htmlspecialchars($id) . " và hoàn trạng thái cho Heo mã: " . htmlspecialchars($MaHeo) . " về 'Bình thường'!";
            } else {
                $_SESSION['success'] = "Đã xóa phiếu xuất thành công!";
            }

            $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            $_SESSION['error'] = "Lỗi xóa: " . $e->getMessage();
        }

        header('Location: index.php?url=xuatchuong');
        exit;
    }
}
