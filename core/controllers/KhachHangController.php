<?php
require_once __DIR__ . '/../models/KhachHangModel.php';

class KhachHangController
{
    private $pdo;
    private $model;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->model = new KhachHangModel($pdo);
    }

    private function view($name, $data = [])
    {
        extract($data);
        require __DIR__ . '/../../admin/views/khachhang/' . $name . '.php';
    }

    // ==================== DANH SÁCH ====================
    public function index()
    {
        $keyword = trim($_GET['timkiem'] ?? '');
        $sort    = $_GET['sort'] ?? 'MaKH';
        $order   = strtoupper($_GET['order'] ?? 'DESC');

        // Khớp với $allowedSorts trong Model
        $allowed = ['MaKH', 'TenKH', 'SDT', 'DiaChi', 'NgaySinh'];
        if (!in_array($sort, $allowed)) {
            $sort = 'MaKH';
        }
        $order = ($order === 'ASC') ? 'ASC' : 'DESC';

        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 10;
        $offset = ($page - 1) * $limit;

        // Gọi hàm từ Model
        $totalRecords = $this->model->getTotal($keyword);
        $totalPages = ceil($totalRecords / $limit);
        $ds = $this->model->getAll($keyword, $sort, $order, $limit, $offset);

        $this->view('index', [
            'dsKhachHang'  => $ds,
            'keyword'      => $keyword,
            'sort'         => $sort,
            'order'        => $order,
            'page'         => $page,
            'totalPages'   => $totalPages,
            'totalRecords' => $totalRecords,
            'limit'        => $limit
        ]);
    }

    // ==================== THÊM MỚI ====================
    public function add()
    {
        $errors = [];
        $data = [];

        // Load danh sách nhân viên
        $nhanvien = $this->pdo->query("SELECT MaNV, HoTen FROM nhanvien WHERE TrangThai IN ('Chính thức','Thử việc') ORDER BY HoTen")->fetchAll();

        // === TỰ ĐỘNG SINH MÃ KHÁCH HÀNG CHỈ LÀ SỐ THUẦN (1, 2, 3...) ===
        // Không padding zero (không dùng 001, 002...)
        // Sử dụng ORDER BY MaKH + 0 để lấy mã lớn nhất đúng thứ tự số học (tránh lỗi sort string)
        $stmt = $this->pdo->query("SELECT MaKH FROM khachhang ORDER BY MaKH + 0 DESC LIMIT 1");
        $last = $stmt->fetchColumn();

        if ($last !== false && $last !== null) {
            $nextNumber = (int)$last + 1;
        } else {
            $nextNumber = 1;
        }

        // Giá trị mặc định để hiển thị trong form
        $autoMaKH = $nextNumber;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'TenKH'        => trim($_POST['TenKH'] ?? ''),
                'SDT'          => trim($_POST['SDT'] ?? ''),
                'Email'        => trim($_POST['Email'] ?? ''),
                'NgaySinh'     => $_POST['NgaySinh'] ?? null,
                'GioiTinh'     => $_POST['GioiTinh'] ?? 'Nam',
                'DiaChi'       => trim($_POST['DiaChi'] ?? ''),
                'MaNVPhuTrach' => $_POST['MaNVPhuTrach'] ?: null,
            ];

            // Validation cơ bản
            if (empty($data['TenKH'])) $errors[] = 'Vui lòng nhập tên khách hàng';
            if (empty($data['SDT']))   $errors[] = 'Vui lòng nhập số điện thoại';

            // Kiểm tra trùng SDT
            if (!$errors) {
                $check = $this->pdo->prepare("SELECT MaKH FROM khachhang WHERE SDT = ?");
                $check->execute([$data['SDT']]);
                if ($check->fetch()) {
                    $errors[] = 'Số điện thoại này đã được sử dụng.';
                }
            }

            // Nếu không có lỗi → sinh MaKH chắc chắn chưa tồn tại (xử lý concurrency)
            if (!$errors) {
                $currentNumber = $nextNumber;
                do {
                    $candidateMaKH = (string)$currentNumber; // chỉ số thuần, ví dụ: '1', '2', '10'
                    $checkMaKH = $this->pdo->prepare("SELECT 1 FROM khachhang WHERE MaKH = ?");
                    $checkMaKH->execute([$candidateMaKH]);
                    if ($checkMaKH->fetch()) {
                        $currentNumber++; // đã tồn tại → tăng lên
                    } else {
                        break;
                    }
                } while (true);

                $data['MaKH'] = $candidateMaKH;
                $autoMaKH = $candidateMaKH; // cập nhật để hiển thị và thông báo

                $result = $this->model->create($data);

                if ($result === true) {
                    $_SESSION['success'] = "Thêm khách hàng '{$data['TenKH']}' (Mã: {$autoMaKH}) thành công!";
                    header('Location: index.php?url=khachhang');
                    exit;
                } else {
                    $errors[] = $result ?: 'Không thể thêm khách hàng, vui lòng thử lại.';
                }
            }
        }

        // Truyền $autoMaKH sang view (hiển thị số thuần: 1, 2, 3...)
        $this->view('add', compact('errors', 'data', 'nhanvien', 'autoMaKH'));
    }
    // ==================== CHỈNH SỬA ====================
    public function edit($maKH)
    {
        // Lấy thông tin khách hàng hiện tại từ DB
        $khachHang = $this->model->getByMaKH($maKH);

        if (!$khachHang) {
            $_SESSION['error'] = 'Không tìm thấy khách hàng';
            header('Location: index.php?url=khachhang');
            exit;
        }

        // Load danh sách nhân viên
        $nhanvien = $this->pdo->query("SELECT MaNV, HoTen FROM nhanvien WHERE TrangThai IN ('Chính thức','Thử việc') ORDER BY HoTen")->fetchAll();

        $errors = [];

        // Mặc định dùng dữ liệu cũ từ DB để hiển thị trong form
        $data = [
            'MaKH'         => $khachHang['MaKH'],
            'TenKH'        => $khachHang['TenKH'],
            'SDT'          => $khachHang['SDT'],
            'Email'        => $khachHang['Email'],
            'NgaySinh'     => $khachHang['NgaySinh'],
            'GioiTinh'     => $khachHang['GioiTinh'] ?? 'Nam',
            'DiaChi'       => $khachHang['DiaChi'],
            'MaNVPhuTrach' => $khachHang['MaNVPhuTrach'],
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Lấy dữ liệu từ POST (trim và xử lý null tương tự add)
            $data = [
                'MaKH'         => $maKH,
                'TenKH'        => trim($_POST['TenKH'] ?? ''),
                'SDT'          => trim($_POST['SDT'] ?? ''),
                'Email'        => trim($_POST['Email'] ?? '') ?: null,
                'NgaySinh'     => $_POST['NgaySinh'] ?? null,
                'GioiTinh'     => $_POST['GioiTinh'] ?? 'Nam',
                'DiaChi'       => trim($_POST['DiaChi'] ?? '') ?: null,
                'MaNVPhuTrach' => $_POST['MaNVPhuTrach'] ?: null,
            ];

            // Validation cơ bản
            if (empty($data['TenKH'])) $errors[] = 'Vui lòng nhập tên khách hàng';
            if (empty($data['SDT']))   $errors[] = 'Vui lòng nhập số điện thoại';

            // Kiểm tra trùng SDT (loại trừ chính khách hàng đang sửa)
            if (!$errors) {
                $check = $this->pdo->prepare("SELECT MaKH FROM khachhang WHERE SDT = ? AND MaKH != ?");
                $check->execute([$data['SDT'], $maKH]);
                if ($check->fetch()) {
                    $errors[] = 'Số điện thoại này đã được sử dụng bởi khách hàng khác.';
                }
            }

            // Nếu không có lỗi → cập nhật
            if (!$errors) {
                $result = $this->model->update($data);

                if ($result === true) {
                    $_SESSION['success'] = "Cập nhật thành công khách hàng '{$data['TenKH']}' (Mã: {$maKH})!";
                    header('Location: index.php?url=khachhang');
                    exit;
                } else {
                    $errors[] = $result ?: 'Không thể cập nhật khách hàng, vui lòng thử lại.';
                }
            }

            // Nếu có lỗi → $data vẫn là dữ liệu từ POST (đã được gán ở trên), form sẽ hiển thị lại dữ liệu người dùng vừa nhập
        }

        // Truyền dữ liệu sang view
        // $data luôn chứa giá trị sẽ hiển thị trong form (dữ liệu cũ nếu chưa submit, hoặc dữ liệu POST nếu có lỗi)
        $this->view('edit', compact('data', 'nhanvien', 'errors'));
    }
    // ==================== XÓA ====================
    public function delete()
    {
        // Lấy mã KH từ URL (ví dụ: ?url=khachhang/delete&maKH=1)
        $maKH = $_GET['maKH'] ?? null;

        if ($maKH) {
            // Gọi hàm delete từ Model đã sửa ở trên
            $result = $this->model->delete($maKH);

            if ($result === true) {
                $_SESSION['success'] = "Đã xóa sạch dữ liệu khách hàng {$maKH}!";
            } else {
                $_SESSION['error'] = "Không thể xóa khách hàng này.";
            }
        }

        // Sau khi xóa xong PHẢI có dòng này để không bị trắng trang
        header('Location: index.php?url=khachhang');
        exit;
    }
    // ==================== PHẢ HỆ / LỊCH SỬ MUA BÁN ====================
    public function phahe($maKH)
    {
        $khach = $this->model->getByMaKH($maKH);
        if (!$khach) {
            $_SESSION['error'] = 'Không tìm thấy khách hàng';
            header('Location: index.php?url=khachhang');
            exit;
        }

        $dsHeo = $this->model->getHeoDaXuat($maKH);
        $lichPhoi = $this->model->getLichPhoiGiong($maKH);

        $this->view('phahe', [
            'khach'    => $khach,
            'dsHeo'    => $dsHeo,
            'lichPhoi' => $lichPhoi
        ]);
    }

    // ==================== XEM CHI TIẾT ====================
    public function xemchitiet($maKH = null)
    {
        $maKH = $maKH ?? $_GET['maKH'] ?? $_GET['id'] ?? '';

        if (empty($maKH)) {
            $_SESSION['error'] = 'Mã khách hàng không hợp lệ!';
            header('Location: index.php?url=khachhang');
            exit;
        }

        // Sử dụng hàm getXemChiTietById (đã bao gồm stats) từ Model
        $khachhang = $this->model->getXemChiTietById($maKH);

        if (!$khachhang) {
            $_SESSION['error'] = 'Không tìm thấy dữ liệu khách hàng!';
            header('Location: index.php?url=khachhang');
            exit;
        }

        $this->view('xemchitiet', [
            'khachhang' => $khachhang,
            'thong_ke'  => $khachhang['thong_ke']
        ]);
    }
}
