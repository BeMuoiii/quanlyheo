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
        // Lấy dữ liệu từ bộ lọc (giống bên Quản lý Heo)
        $keyword = trim($_GET['timkiem'] ?? '');
        $sort    = $_GET['sort'] ?? 'MaKH';
        $order   = strtoupper($_GET['order'] ?? 'DESC');

        // Các cột cho phép sắp xếp của Khách hàng
        $allowed = ['MaKH', 'TenKH', 'SDT', 'DiaChi'];
        if (!in_array($sort, $allowed)) {
            $sort = 'MaKH';
        }
        $order = ($order === 'ASC') ? 'ASC' : 'DESC';

        // === PHÂN TRANG ===
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 10;
        $offset = ($page - 1) * $limit;

        // 1. Lấy tổng số lượng khách hàng sau khi lọc để tính số trang
        // Model của bạn cần được cập nhật hàm getTotal($keyword)
        $totalRecords = $this->model->getTotal($keyword);
        $totalPages = ceil($totalRecords / $limit);

        // 2. Lấy danh sách khách hàng theo các tiêu chí lọc và phân trang
        // Model của bạn cần được cập nhật hàm getAll($keyword, $sort, $order, $limit, $offset)
        $ds = $this->model->getAll($keyword, $sort, $order, $limit, $offset);

        // 3. Truyền dữ liệu ra View
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
    // ==================== THÊM ====================
    public function add()
    {
        $errors = [];
        $data = []; // Để giữ dữ liệu cũ khi có lỗi

        // Load danh sách nhân viên (luôn cần cho form)
        $nhanvien = $this->pdo->query("SELECT MaNV, HoTen FROM nhanvien WHERE TrangThai IN ('Chính thức','Thử việc') ORDER BY HoTen")->fetchAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // === CSRF PROTECTION (bắt buộc thêm) ===
            // Bạn cần tạo token ở đầu trang hoặc middleware:
            // $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            // if (!hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'] ?? '')) {
            //     $errors[] = 'Lỗi bảo mật, vui lòng thử lại.';
            // }

            // Lấy và làm sạch dữ liệu
            $data = [
                'TenKH'        => trim($_POST['TenKH'] ?? ''),
                'SDT'          => trim($_POST['SDT'] ?? ''),
                'Email'        => trim($_POST['Email'] ?? ''),
                'NgaySinh'     => $_POST['NgaySinh'] ?? null,
                'GioiTinh'     => $_POST['GioiTinh'] ?? 'D',
                'DiaChi'       => trim($_POST['DiaChi'] ?? ''),
                'GhiChu'       => trim($_POST['GhiChu'] ?? ''),
                'MaNVPhuTrach' => $_POST['MaNVPhuTrach'] ?? null,
                'ChuongNhap'   => $_POST['ChuongNhap'] ?? 'Thường',
            ];

            // === VALIDATION ===
            if (empty($data['TenKH'])) {
                $errors[] = 'Vui lòng nhập tên khách hàng';
            } elseif (strlen($data['TenKH']) < 2) {
                $errors[] = 'Tên khách hàng phải ít nhất 2 ký tự';
            }

            if (empty($data['SDT'])) {
                $errors[] = 'Vui lòng nhập số điện thoại';
            } else {
                // Kiểm tra định dạng số di động Việt Nam (10 số, bắt đầu đúng đầu số)
                if (!preg_match('/^(0)(3[2-9]|5[689]|7[06-9]|8[1-689]|9[0-46-9])[0-9]{7}$/', $data['SDT'])) {
                    $errors[] = 'Số điện thoại không hợp lệ (phải là số di động Việt Nam 10 chữ số)';
                }
            }

            if (!empty($data['Email']) && !filter_var($data['Email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Định dạng email không hợp lệ';
            }

            if (!empty($data['NgaySinh'])) {
                $date = DateTime::createFromFormat('Y-m-d', $data['NgaySinh']);
                $now = new DateTime();
                if (!$date || $date->format('Y-m-d') !== $data['NgaySinh']) {
                    $errors[] = 'Ngày sinh không đúng định dạng';
                } elseif ($date > $now) {
                    $errors[] = 'Ngày sinh không được trong tương lai';
                }
            }

            // === KIỂM TRA TRÙNG SDT (chỉ khi không có lỗi trước đó) ===
            if (!$errors && !empty($data['SDT'])) {
                $check = $this->pdo->prepare("SELECT MaKH FROM khachhang WHERE SDT = ?");
                $check->execute([$data['SDT']]);
                if ($check->fetch()) {
                    $errors[] = 'Số điện thoại này đã được sử dụng bởi khách hàng khác';
                }
            }

            // === THỰC HIỆN THÊM MỚI ===
            if (!$errors) {
                $ok = $this->model->create($data);

                if ($ok === true) {
                    $_SESSION['success'] = "Thêm khách hàng '{$data['TenKH']}' thành công!";
                    header('Location: index.php?url=khachhang');
                    exit;
                } else {
                    // Không hiển thị lỗi DB trực tiếp cho người dùng
                    $errors[] = 'Không thể thêm khách hàng. Vui lòng thử lại sau.';
                    error_log('Lỗi thêm khách hàng: ' . (is_string($ok) ? $ok : print_r($ok, true)));
                }
            }
        }

        // Render view: truyền errors, data (để repopulate), nhanvien
        $this->view('add', compact('errors', 'data', 'nhanvien'));
    }

    // ==================== SỬA ====================
    public function edit($id)
    {
        // Đổi $kh thành $khachhang để khớp với View
        $khachhang = $this->model->getById($id);
        if (!$khachhang) {
            $_SESSION['error'] = 'Không tìm thấy khách hàng';
            header('Location: index.php?url=khachhang');
            exit;
        }

        $danhSachNhanVien = $this->pdo->query("SELECT MaNV, HoTen FROM nhanvien WHERE TrangThai IN ('Chính thức','Thử việc')")->fetchAll();

        // Giả lập danh sách chuồng nếu bạn chưa có bảng chuồng riêng
        $danhSachChuong = ['Chuồng 1', 'Chuồng 2', 'Chuồng 3', 'Chuồng 4'];
        $errors = [];

        if ($_POST) {
            $data = [
                'MaKH'         => $id,
                'TenKH'        => trim($_POST['TenKH'] ?? ''),
                'SDT'          => trim($_POST['SDT'] ?? ''),
                'Email'        => $_POST['Email'] ?? null,
                'NgaySinh'     => $_POST['NgaySinh'] ?? null,
                'GioiTinh'     => $_POST['GioiTinh'] ?? '1',
                'DiaChi'       => $_POST['DiaChi'] ?? null,
                'MaNV'         => $_POST['MaNV'] ?? null, // Khớp với tên cột MaNV trong form
                'ChuongNhap'   => $_POST['ChuongNhap'] ?? null,
            ];

            if (empty($data['TenKH'])) $errors[] = 'Nhập tên';
            if (empty($data['SDT']))   $errors[] = 'Nhập SĐT';

            if (!$errors) {
                $check = $this->pdo->prepare("SELECT MaKH FROM khachhang WHERE SDT = ? AND MaKH != ?");
                $check->execute([$data['SDT'], $id]);
                if ($check->fetch()) $errors[] = 'SĐT đã được dùng';
            }

            if (!$errors) {
                $ok = $this->model->update($data);
                if ($ok === true) {
                    $_SESSION['success'] = "Cập nhật thành công Mã #{$id}";
                    header('Location: index.php?url=khachhang');
                    exit;
                } else {
                    $errors[] = $ok;
                }
            }
        }

        // Truyền đúng tên biến khachhang sang view
        $this->view('edit', [
            'khachhang' => $khachhang,
            'danhSachNhanVien' => $danhSachNhanVien,
            'danhSachChuong' => $danhSachChuong,
            'error_message' => implode('<br>', $errors)
        ]);
    }
    // ==================== XÓA ====================
    public function delete($id)
    {
        $kh = $this->model->getById($id);
        if (!$kh) {
            $_SESSION['error'] = 'Không tìm thấy';
            header('Location: index.php?url=khachhang');
            exit;
        }

        if ($_POST && isset($_POST['confirm'])) {
            if (strtoupper($_POST['confirm']) === 'XÓA') {
                $this->model->delete($id);
                $_SESSION['success'] = "Đã xóa {$kh['TenKH']}";
            } else {
                $_SESSION['error'] = 'Nhập sai xác nhận';
            }
            header('Location: index.php?url=khachhang');
            exit;
        }

        $this->view('delete', compact('kh'));
    }
}
