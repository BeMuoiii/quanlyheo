<?php
require_once __DIR__ . '/../models/NhanVienModel.php';

class NhanVienController
{
    private $pdo;
    private $model;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->model = new NhanVienModel($pdo);
    }

    private function view($viewName, $data = [])
    {
        extract($data);
        $viewPath = __DIR__ . '/../../admin/views/nhanvien/' . $viewName . '.php';
        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            die("Không tìm thấy view: $viewPath");
        }
    }

    // ==================== TRANG CHỦ ====================
    public function index()
    {
        // Phân trang: mỗi trang 10 nhân viên
        $perPage = 10;
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $offset = ($page - 1) * $perPage;

        // Tổng số nhân viên để tính tổng trang
        $totalStmt = $this->pdo->query("SELECT COUNT(*) FROM nhanvien");
        $total = $totalStmt->fetchColumn();
        $totalPages = ceil($total / $perPage);

        // Thống kê
        $tongNhanVien = $total;
        $nhanVienChinhThuc = $this->pdo->query("SELECT COUNT(*) FROM nhanvien WHERE TrangThai = 'Chính thức'")->fetchColumn();
        $nhanVienThuViec   = $this->pdo->query("SELECT COUNT(*) FROM nhanvien WHERE TrangThai = 'Thử việc'")->fetchColumn();

        $nghiViecHomNay = $this->pdo->query("SELECT COUNT(*) FROM nhanvien WHERE TrangThai = 'Nghỉ việc' AND DATE(NgayNghi) = CURDATE()")->fetchColumn();

        $listNghiViecHomNay = $this->pdo->query("
            SELECT nv.*, bp.TenBoPhan 
            FROM nhanvien nv
            LEFT JOIN bophan bp ON nv.MaBoPhan = bp.MaBoPhan
            WHERE nv.TrangThai = 'Nghỉ việc' AND DATE(nv.NgayNghi) = CURDATE()
            ORDER BY nv.HoTen
        ")->fetchAll();

        // Danh sách nhân viên có phân trang
        $dsNhanVien = $this->pdo->query("
            SELECT nv.*, bp.TenBoPhan 
            FROM nhanvien nv
            LEFT JOIN bophan bp ON nv.MaBoPhan = bp.MaBoPhan
            ORDER BY FIELD(nv.TrangThai, 'Thử việc', 'Chính thức', 'Nghỉ việc'), nv.NgayVaoLam DESC
            LIMIT $offset, $perPage
        ")->fetchAll();

        // Truyền thêm biến phân trang vào view
        $this->view('index', compact(
            'tongNhanVien',
            'nhanVienChinhThuc',
            'nhanVienThuViec',
            'nghiViecHomNay',
            'listNghiViecHomNay',
            'dsNhanVien',
            'page',
            'totalPages',
            'perPage'
        ));
    }

    // ==================== THÊM NHÂN VIÊN ====================
   public function add()
{
    $errors = [];
    $dsBoPhan = $this->pdo->query("SELECT MaBoPhan, TenBoPhan FROM bophan ORDER BY TenBoPhan")->fetchAll();

    // Tính Mã NV tiếp theo để hiển thị trong form (dự đoán)
    $stmt = $this->pdo->query("SELECT MAX(MaNV) AS max_id FROM nhanvien");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $nextMaNV = ($row['max_id'] ?? 0) + 1;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $hoTen      = trim($_POST['HoTen'] ?? '');
        $sdt        = trim($_POST['SDT'] ?? '');
        $cmnd       = trim($_POST['CMND'] ?? '');
        $ngaySinh   = $_POST['NgaySinh'] ?? null;
        $gioiTinh   = $_POST['GioiTinh'] ?? 'Nam';
        $diaChi     = trim($_POST['DiaChi'] ?? '');
        $maBoPhan   = $_POST['MaBoPhan'] ?? null;
        $viTri      = trim($_POST['ViTri'] ?? '');
        $ngayVaoLam = $_POST['NgayVaoLam'] ?? date('Y-m-d');
        $luong      = !empty($_POST['LuongCoBan']) ? str_replace(['.', ' '], '', $_POST['LuongCoBan']) : 0;
        $trangThai  = $_POST['TrangThai'] ?? 'Thử việc';

        // Validate
        if ($hoTen === '')      $errors[] = "Vui lòng nhập họ tên!";
        if ($sdt === '')        $errors[] = "Vui lòng nhập số điện thoại!";
        if ($cmnd === '')       $errors[] = "Vui lòng nhập CMND/CCCD!";
        if (!$maBoPhan)         $errors[] = "Vui lòng chọn bộ phận!";

        if (empty($errors)) {
            $data = [
                'HoTen' => $hoTen,
                'SDT' => $sdt,
                'ViTri' => $viTri,
                'CMND' => $cmnd,
                'NgaySinh' => $ngaySinh,
                'GioiTinh' => $gioiTinh,
                'DiaChi' => $diaChi,
                'MaBoPhan' => $maBoPhan,
                'NgayVaoLam' => $ngayVaoLam,
                'LuongCoBan' => $luong,
                'TrangThai' => $trangThai,
            ];

            $result = $this->model->create($data);
            if (is_numeric($result)) {
                $_SESSION['success'] = "Thêm nhân viên <strong>$hoTen</strong> thành công!";
                header('Location: index.php?url=nhanvien');
                exit;
            } else {
                $errors[] = "Lỗi hệ thống: " . $result;
            }
        }
    }

    // Truyền thêm $nextMaNV vào view
    $this->view('add', compact('errors', 'dsBoPhan', 'nextMaNV'));
}

    // ==================== SỬA NHÂN VIÊN ====================
    public function edit($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM nhanvien WHERE MaNV = ?");
        $stmt->execute([$id]);
        $nv = $stmt->fetch();
        if (!$nv) {
            $_SESSION['error'] = "Không tìm thấy nhân viên!";
            header('Location: index.php?url=nhanvien');
            exit;
        }

        $dsBoPhan = $this->pdo->query("SELECT MaBoPhan, TenBoPhan FROM bophan ORDER BY TenBoPhan")->fetchAll();
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $hoTen      = trim($_POST['HoTen'] ?? '');
            $sdt        = trim($_POST['SDT'] ?? '');
            $cmnd       = trim($_POST['CMND'] ?? '');
            $ngaySinh   = $_POST['NgaySinh'] ?? null;
            $gioiTinh   = $_POST['GioiTinh'] ?? 'Nam';
            $diaChi     = trim($_POST['DiaChi'] ?? '');
            $maBoPhan   = $_POST['MaBoPhan'] ?? null;
            $viTri      = trim($_POST['ViTri'] ?? '');
            $ngayVaoLam = $_POST['NgayVaoLam'] ?? $nv['NgayVaoLam'];
            $luong      = !empty($_POST['LuongCoBan']) ? str_replace(['.', ' '], '', $_POST['LuongCoBan']) : $nv['LuongCoBan'];
            $trangThai  = $_POST['TrangThai'] ?? $nv['TrangThai'];

            if ($hoTen === '')      $errors[] = "Vui lòng nhập họ tên!";
            if ($sdt === '')        $errors[] = "Vui lòng nhập số điện thoại!";
            if ($cmnd === '')       $errors[] = "Vui lòng nhập CMND/CCCD!";
            if (!$maBoPhan)         $errors[] = "Vui lòng chọn bộ phận!";

            if (empty($errors)) {
                $data = [
                    'HoTen'      => $hoTen,
                    'SDT'        => $sdt,
                    'ViTri'      => $viTri,
                    'CMND'       => $cmnd,
                    'NgaySinh'   => $ngaySinh,
                    'GioiTinh'   => $gioiTinh,
                    'DiaChi'     => $diaChi,
                    'MaBoPhan'   => $maBoPhan,
                    'NgayVaoLam' => $ngayVaoLam,
                    'LuongCoBan' => $luong,
                    'TrangThai'  => $trangThai,
                    'MaNV'       => $id
                ];

                if ($this->model->update($data) === true) {
                    $_SESSION['success'] = "Cập nhật nhân viên thành công!";
                    header('Location: index.php?url=nhanvien');
                    exit;
                } else {
                    $errors[] = "Lỗi cập nhật cơ sở dữ liệu!";
                }
            }
        }

        $this->view('edit', compact('nv', 'dsBoPhan', 'errors'));
    }

    // ==================== BÀN GIAO & XÓA (giữ nguyên) ====================
    public function delete($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM nhanvien WHERE MaNV = ?");
        $stmt->execute([$id]);
        $nhanVien = $stmt->fetch();

        if (!$nhanVien) {
            $_SESSION['error'] = "Nhân viên không tồn tại!";
            header('Location: index.php?url=nhanvien');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
            try {
                $this->pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
                $this->pdo->prepare("DELETE FROM nhanvien WHERE MaNV = ?")->execute([$id]);
                $this->pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

                $_SESSION['success'] = "Đã xóa vĩnh viễn nhân viên <strong>{$nhanVien['HoTen']}</strong>!";
                header('Location: index.php?url=nhanvien');
                exit;
            } catch (PDOException $e) {
                $_SESSION['error'] = "Lỗi hệ thống: " . $e->getMessage();
            }
        }

        $this->view('delete', ['nhanVien' => $nhanVien]);
    }


    public function banGiao($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM nhanvien WHERE MaNV = ?");
        $stmt->execute([$id]);
        $nv = $stmt->fetch();

        if (!$nv || $nv['TrangThai'] === 'Nghỉ việc') {
            $_SESSION['error'] = "Nhân viên không tồn tại hoặc đã nghỉ!";
            header('Location: index.php?url=nhanvien');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ngayNghi = $_POST['NgayNghi'] ?? date('Y-m-d');
            $lyDo     = trim($_POST['LyDo'] ?? '');

            $sql = "UPDATE nhanvien SET TrangThai = 'Nghỉ việc', NgayNghi = ?, LyDoNghi = ? WHERE MaNV = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$ngayNghi, $lyDo, $id]);

            $_SESSION['success'] = "Đã cho nhân viên {$nv['HoTen']} nghỉ việc!";
            header('Location: index.php?url=nhanvien');
            exit;
        }

        $this->view('banGiao', ['nv' => $nv]);
    }
}
