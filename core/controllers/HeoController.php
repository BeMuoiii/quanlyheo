<?php
require_once __DIR__ . '/../models/HeoModel.php';  // <<< THÊM DÒNG NÀY!!!

class HeoController
{
    private $model;

    public function __construct($pdo)
    {
        $this->model = new HeoModel($pdo);
    }
    /** Load view an toàn */
    private function view($viewName, $data = [])
    {
        extract($data);
        $viewPath = __DIR__ . '/../../admin/views/heo/' . $viewName . '.php';

        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            die("Lỗi: Không tìm thấy file view tại <br><code>$viewPath</code>");
        }
    }

    /** DANH SÁCH + TÌM KIẾM */

    public function index()
    {
        $keyword = trim($_GET['timkiem'] ?? '');
        $sort    = $_GET['sort'] ?? 'MaHeo';
        $order   = strtoupper($_GET['order'] ?? 'DESC');
        $gioitinh = $_GET['gioitinh'] ?? '';

        $allowed = ['MaHeo', 'GiongHeo', 'GioiTinh', 'NgaySinh', 'CanNangHienTai', 'ViTriChuong', 'TrangThaiHeo'];
        if (!in_array($sort, $allowed)) $sort = 'MaHeo';
        $order = ($order === 'ASC') ? 'ASC' : 'DESC';

        // === PHÂN TRANG ===
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 10;
        $offset = ($page - 1) * $limit;

        // Lấy tổng số lượng tổng để tính số trang
        $total = $this->model->getTotal($keyword, $gioitinh);
        $totalPages = ceil($total / $limit);

        // Lấy danh sách heo theo trang
        $dsHeo = $this->model->getAll($keyword, $gioitinh, $sort, $order, $limit, $offset);

        $this->view('index', [
            'dsHeo'       => $dsHeo,
            'keyword'     => $keyword,
            'sort'        => $sort,
            'order'       => $order,
            'page'        => $page,
            'totalPages'  => $totalPages,
            'total'       => $total
        ]);
    }
    public function get_new_code() {
    $gioiTinh = $_GET['gioitinh'] ?? 'D';
    echo $this->model->generateAutoMaHeo($gioiTinh);
    exit;
}
    /** THÊM HEO MỚI */
    public function add()
    {
        $error_message = '';

        // Bước 1: Tạo mã mặc định ngay khi load trang (mặc định cho Heo Đực 'D')
        // Nếu bạn muốn mặc định Heo Cái thì đổi thành 'C'
        $defaultGioiTinh = 'D';
        $autoMaHeo = $this->model->generateAutoMaHeo($defaultGioiTinh);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $gioiTinh = $_POST['GioiTinh'] ?? 'D';
            $maHeo = trim($_POST['MaHeo'] ?? '');

            // Nếu lúc POST người dùng xóa trống mã, hệ thống lại tự tạo lần nữa cho an toàn
            if (empty($maHeo)) {
                $maHeo = $this->model->generateAutoMaHeo($gioiTinh);
            }

            $data = [
                'MaHeo'          => $maHeo,
                'GiongHeo'       => $_POST['GiongHeo'] ?? 'Heo rừng lai',
                'GioiTinh'       => $gioiTinh,
                'NgaySinh'       => $_POST['NgaySinh'] ?? '',
                'GiaVon'         => max(0, floatval($_POST['GiaVon'] ?? 0)),
                'CanNangHienTai' => max(0.1, floatval($_POST['CanNangHienTai'] ?? 0)),
                'ViTriChuong'    => trim($_POST['ViTriChuong'] ?? ''),
                'TrangThaiHeo'   => $_POST['TrangThaiHeo'] ?? 'Bình thường',
                'NguonGoc'       => trim($_POST['NguonGoc'] ?? ''),
                'GhiChu'         => trim($_POST['GhiChu'] ?? ''),
                'MaBo'           => !empty($_POST['MaBo']) ? $_POST['MaBo'] : null,
                'MaMe'           => !empty($_POST['MaMe']) ? $_POST['MaMe'] : null,
            ];

            if (empty($data['NgaySinh'])) {
                $error_message = 'Vui lòng chọn Ngày sinh!';
                // Nếu lỗi, ta giữ lại mã heo người dùng đang nhập hoặc mã tự động cũ
                $autoMaHeo = $maHeo;
            } else {
                $result = $this->model->create($data);
                if ($result === true) {
                    $_SESSION['success'] = "Thêm thành công mã: {$data['MaHeo']}";
                    header('Location: index.php?url=heo');
                    exit;
                } else {
                    $error_message = $result;
                    $autoMaHeo = $maHeo;
                }
            }
        }

        // Bước 2: Truyền biến $autoMaHeo sang View để hiện lên ô nhập liệu
        $this->view('add', [
            'error_message' => $error_message,
            'autoMaHeo'     => $autoMaHeo,
            'heoDuc'        => $this->model->getByGioiTinh('D'),
            'heoCai'        => $this->model->getByGioiTinh('C')
        ]);
    }

    /** SỬA HEO */
    public function edit($id)
    {
        $heo = $this->model->getById($id);
        if (!$heo) {
            $_SESSION['error'] = "Không tìm thấy heo có mã: " . htmlspecialchars($id);
            header('Location: index.php?url=heo');
            exit;
        }

        $error_message = '';
        $heoDuc = $this->model->getByGioiTinh('D');
        $heoCai = $this->model->getByGioiTinh('C');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'MaHeo'         => $id,
                'GiongHeo'      => $_POST['GiongHeo'] ?? 'Heo rừng lai',
                'GioiTinh'      => $_POST['GioiTinh'] ?? $heo['GioiTinh'],
                'NgaySinh'      => $_POST['NgaySinh'] ?? $heo['NgaySinh'],
                'GiaVon'        => $_POST['GiaVon'] ?? $heo['GiaVon'] ?? 0,
                'CanNangHienTai' => max(0.1, floatval($_POST['CanNangHienTai'] ?? $heo['CanNangHienTai'])),
                'ViTriChuong'   => trim($_POST['ViTriChuong'] ?? ''),
                'TrangThaiHeo'  => $_POST['TrangThaiHeo'] ?? $heo['TrangThaiHeo'],
                'NguonGoc'      => trim($_POST['NguonGoc'] ?? ''),
                'GhiChu'        => trim($_POST['GhiChu'] ?? ''),
                'MaBo'          => !empty($_POST['MaBo']) ? $_POST['MaBo'] : null,
                'MaMe'          => !empty($_POST['MaMe']) ? $_POST['MaMe'] : null,
            ];

            if ($data['CanNangHienTai'] < 0.1) {
                $error_message = 'Cân nặng phải lớn hơn 0kg!';
            } else {
                $result = $this->model->update($data);
                if ($result === true) {
                    $_SESSION['success'] = "Cập nhật heo $id thành công!";
                    header('Location: index.php?url=heo');
                    exit;
                } else {
                    $error_message = $result;
                }
            }
        }

        $this->view('edit', [
            'heo'           => $heo,
            'error_message' => $error_message,
            'heoDuc'        => $heoDuc,
            'heoCai'        => $heoCai
        ]);
    }

    /** XÓA HEO */
    public function delete($id)
    {
        $result = $this->model->delete($id);

        if ($result === true) {
            $_SESSION['success'] = "Xóa heo $id thành công!";
        } else {
            // $result là chuỗi lỗi từ Model
            $_SESSION['error'] = "Không thể xóa heo $id !<br><span class='text-sm'>Lý do: $result</span>";
        }

        header('Location: index.php?url=heo');
        exit;
    }
}
