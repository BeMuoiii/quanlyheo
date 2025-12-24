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
        $tongNhanVien = $this->pdo->query("SELECT COUNT(*) FROM nhanvien")->fetchColumn();
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

        $dsNhanVien = $this->pdo->query("
            SELECT nv.*, bp.TenBoPhan 
            FROM nhanvien nv
            LEFT JOIN bophan bp ON nv.MaBoPhan = bp.MaBoPhan
            ORDER BY FIELD(nv.TrangThai, 'Thử việc', 'Chính thức', 'Nghỉ việc'), nv.NgayVaoLam DESC
        ")->fetchAll();

        $this->view('index', compact('tongNhanVien', 'nhanVienChinhThuc', 'nhanVienThuViec', 'nghiViecHomNay', 'listNghiViecHomNay', 'dsNhanVien'));
    }

    // ==================== UPLOAD ẢNH (DÙNG CHUNG) ====================
    private function handleUpload($current = null)
    {
        $anhPath = $current ?? '/assets/img/avatar-default.png';

        if (!empty($_FILES['Anh']['name'])) {
            // ĐƯỜNG DẪN CHUẨN CHO DỰ ÁN ANH
            $uploadDir = __DIR__ . '/../../../public/assets/uploads/nhanvien/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $ext = strtolower(pathinfo($_FILES['Anh']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                return "Chỉ chấp nhận JPG, PNG, GIF!";
            }
            if ($_FILES['Anh']['size'] > 2 * 1024 * 1024) {
                return "Ảnh không quá 2MB!";
            }

            $fileName = time() . '_' . uniqid() . '.' . $ext;
            $target = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['Anh']['tmp_name'], $target)) {
                return '/assets/uploads/nhanvien/' . $fileName;
            }
            return "Lỗi lưu ảnh! Kiểm tra thư mục public/assets/uploads/nhanvien/";
        }
        return $anhPath;
    }

    // ==================== THÊM NHÂN VIÊN ====================
   public function add()
{
    $errors = [];
    $dsBoPhan = $this->pdo->query("SELECT MaBoPhan, TenBoPhan FROM bophan ORDER BY TenBoPhan")->fetchAll();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // TOÀN BỘ DỮ LIỆU CHỈ LẤY TỪ $_POST
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
            $anhResult = $this->handleUpload();
            if (is_string($anhResult) && str_starts_with($anhResult, "Lỗi")) {
                $errors[] = $anhResult;
            } else {
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
                    'Anh' => $anhResult
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
    }

    $this->view('add', compact('errors', 'dsBoPhan'));
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
            $ngayVaoLam = $_POST['NgayVaoLam'] ?? date('Y-m-d');
            $luong      = str_replace(['.', ' '], '', $_POST['LuongCoBan'] ?? '0');
            $trangThai  = $_POST['TrangThai'] ?? 'Thử việc';

            if (empty($errors)) {
                $anhResult = $this->handleUpload($nv['Anh']);
                if (is_string($anhResult) && str_starts_with($anhResult, "Lỗi")) {
                    $errors[] = $anhResult;
                } else {
                    $data = compact('hoTen', 'sdt', 'viTri', 'cmnd', 'ngaySinh', 'gioiTinh', 'diaChi', 'maBoPhan', 'ngayVaoLam', 'luong', 'trangThai');
                    $data['Anh'] = $anhResult;
                    if ($this->model->update($data) === true) {
                        $_SESSION['success'] = "Cập nhật thành công!";
                        header('Location: index.php?url=nhanvien');
                        exit;
                    } else $errors[] = "Lỗi cập nhật!";
                }
            }
        }
        $this->view('edit', compact('nv', 'dsBoPhan', 'errors'));
    }

    // ==================== BÀN GIAO & XÓA (giữ nguyên anh đã có) ====================
    public function delete($id)
    {
        $stmt = $this->pdo->prepare("SELECT HoTen FROM nhanvien WHERE MaNV = ?");
        $stmt->execute([$id]);
        $nv = $stmt->fetch();

        if (!$nv) {
            $_SESSION['error'] = "Nhân viên không tồn tại!";
            header('Location: index.php?url=nhanvien');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_confirm'])) {
            if (strtoupper(trim($_POST['confirm_delete'] ?? '')) !== 'XÓA VĨNH VIỄN') {
                $errors[] = "Bạn phải gõ đúng cụm từ xác nhận!";
            } else {
                $this->pdo->prepare("DELETE FROM nhanvien WHERE MaNV = ?")->execute([$id]);
                $_SESSION['success'] = "Đã xóa vĩnh viễn nhân viên <strong>{$nv['HoTen']}</strong>!";
                header('Location: index.php?url=nhanvien');
                exit;
            }
        }

        $this->view('delete', ['nv' => $nv, 'errors' => $errors ?? []]);
    }

    // ==================== BÀN GIAO / NGHỈ VIỆC ====================
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
