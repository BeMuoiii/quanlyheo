<?php
// === CHỈ GỌI SESSION_START 1 LẦN DUY NHẤT ===
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
ini_set('display_errors', 1);
error_reporting(E_ALL);
// === KẾT NỐI DATABASE ===
require_once __DIR__ . '/../core/config/database.php';
require_once __DIR__ . '/../core/models/UserModel.php';
require_once __DIR__ . '/../core/controllers/AuthController.php';
require_once __DIR__ . '/../core/controllers/DashboardController.php';
require_once __DIR__ . '/../core/controllers/CanNangController.php';
require_once __DIR__ . '/../core/models/CanNangModel.php';
require_once __DIR__ . '/../core/controllers/SinhSanController.php';
require_once __DIR__ . '/../core/controllers/HeoController.php';
require_once __DIR__ . '/../core/controllers/KhachHangController.php';
require_once __DIR__ . '/../core/models/KhachHangModel.php';
$database = new Database();
$pdo = $database->getConnection();

// === BẢO VỆ TRUY CẬP ===
$publicRoutes = ['login', 'auth/process', 'auth/register', 'logout'];
$currentRoute = $_GET['url'] ?? '';

// Nếu chưa login và không phải trang công khai → đá về login
if (!isset($_SESSION['user']) && !in_array($currentRoute, $publicRoutes)) {
    header('Location: index.php?url=login');
    exit;
}

// Nếu đã login rồi mà vào trang login hoặc rỗng → nhảy vào dashboard
if (isset($_SESSION['user']) && ($currentRoute === '' || $currentRoute === 'login')) {
    header('Location: index.php?url=dashboard');
    exit;
}

// === XỬ LÝ ROUTE ===
$url = $currentRoute;
$url = rtrim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);
$url = $_GET['url'] ?? 'dashboard';

switch ($url) {

    // ================== AUTH ==================
    case 'login':
        (new AuthController())->showLogin();
        break;

    case 'auth/process':
        (new AuthController())->processLogin();
        break;

    case 'auth/register':
        (new AuthController())->register();
        break;

    case 'logout':
        (new AuthController())->logout();
        break;

    // ================== DASHBOARD ==================
    case 'dashboard':
    case '':
        (new DashboardController())->index();
        break;

    // ================== QUẢN LÝ HEO ==================
    // ==================== QUẢN LÝ HEO ====================
    case 'heo':
    case 'heo/':
        $heoController = new HeoController($pdo);  // Truyền $pdo vào
        $heoController->index();
        break;

    case 'heo/add':
        $heoController = new HeoController($pdo);
        $heoController->add();
        break;

    case 'heo/edit':
        $heoController = new HeoController($pdo);
        if (isset($_GET['id'])) {
            $heoController->edit($_GET['id']);
        } else {
            $_SESSION['error'] = "Không tìm thấy mã heo để sửa!";
            header('Location: index.php?url=heo');
            exit;
        }
        break;

    case 'heo/delete':
        $heoController = new HeoController($pdo);
        if (isset($_GET['id'])) {
            $heoController->delete($_GET['id']);
        } else {
            $_SESSION['error'] = "Không tìm thấy mã heo để xóa!";
            header('Location: index.php?url=heo');
            exit;
        }
        break;



    // ================== CÂN NẶNG ==================
    case 'cannang':
    case 'cannang/add':
        $canNang = new CanNangController();
        if ($url === 'cannang') {
            $canNang->index();
        } elseif ($url === 'cannang/add') {
            $canNang->add();
        }
        break;

    // ==================== QUẢN LÝ SINH SẢN (PHỐI GIỐNG) ====================
    // ==================== QUẢN LÝ SINH SẢN (PHỐI GIỐNG) ====================
    case 'nhanvien':
    case 'nhanvien/add':
    case 'nhanvien/edit':
    case 'nhanvien/banGiao':
    case 'nhanvien/delete':
        require_once __DIR__ . '/../core/controllers/NhanVienController.php';
        $nhanVienController = new NhanVienController($pdo);

        // Tách phần chính của URL (trước dấu &)
        $mainUrl = explode('&', $url)[0];
        $parts   = explode('/', $mainUrl);

        $action = $parts[1] ?? '';
        $id     = $_GET['id'] ?? null; // Lấy ID từ ?id=...

        if ($url === 'nhanvien' || $mainUrl === 'nhanvien') {
            $nhanVienController->index();
        } elseif ($action === 'add') {
            $nhanVienController->add();
        } elseif ($action === 'edit' && $id) {
            $nhanVienController->edit((int)$id);
        } elseif ($action === 'banGiao' && $id) {
            $nhanVienController->banGiao((int)$id);
        } elseif ($action === 'delete' && $id) {
            $nhanVienController->delete((int)$id);
        } else {
            $nhanVienController->index(); // fallback
        }
        break;


    // Đoạn code bạn cần sửa trong file index.php (tại vị trí của khối switch/case)

    case 'sinhsan':
    case 'sinh-san': // <<< THÊM CASE NÀY VÀO
    case 'sinhsan/index':
    case 'sinh-san/index': // <<< THÊM CASE NÀY VÀO (nếu bạn muốn dùng sinh-san/index)
    case 'sinhsan/add':
    case 'sinh-san/add': // <<< THÊM CASE NÀY VÀO
    case 'sinhsan/edit':
    case 'sinh-san/edit': // <<< THÊM CASE NÀY VÀO
    case 'sinhsan/delete':
    case 'sinh-san/delete': // <<< THÊM CASE NÀY VÀO
    case 'sinhsan/ghiNhanDe':
    case 'sinh-san/ghiNhanDe': // <<< THÊM CASE NÀY VÀO
     // <<< THÊM CASE NÀY VÀO
        // ... (Code khởi tạo Controller và logic điều hướng bên dưới giữ nguyên)

        // ... // <-- Đã thêm case xử lý POST
        // Đảm bảo file Controller đã được yêu cầu tải
        require_once __DIR__ . '/../core/controllers/SinhSanController.php';

        // Khởi tạo Controller
        $sinhSanController = new SinhSanController($pdo);

        // Tách phần chính của URL (trước dấu &)
        $mainUrl = explode('&', $url)[0];
        $parts   = explode('/', $mainUrl);

        $action = $parts[1] ?? '';
        $id     = $_GET['id'] ?? null; // Lấy ID từ ?id=...
        $idInt  = $id ? (int)$id : null; // Chuyển ID sang kiểu số nguyên (nếu có)


        // === LOGIC ĐIỀU HƯỚNG CÁC HÀNH ĐỘNG ===

        // === LOGIC ĐIỀU HƯỚNG CÁC HÀNH ĐỘNG (ĐÃ FIX) ===

        if ($url === 'sinhsan' || $mainUrl === 'sinhsan' || $action === 'index' || $action === '') {
            $sinhSanController->index();
        } elseif ($action === 'add') {
            $sinhSanController->add();
        } elseif ($action === 'edit' && $idInt) {
            $sinhSanController->edit($idInt);
        } elseif ($action === 'ghiNhanDe' && $idInt) {
            // Chỉ chạy duy nhất hàm này
            $sinhSanController->ghiNhanDe($idInt);
        } elseif ($action === 'delete' && $idInt) {
            // Tách riêng hành động xóa ra một elseif khác
            $sinhSanController->delete($idInt);
        } else {
            $sinhSanController->index();
        }
        break;



    // khách hàng
    case 'khachhang':
    case 'khachhang/add':
    case 'khachhang/edit':
    case 'khachhang/delete':
    case 'khachhang/xemchitiet':
    case 'khachhang/view':
        require_once __DIR__ . '/../core/controllers/KhachHangController.php';

        // TÊN CLASS PHẢI ĐÚNG 100%: KhachHangController (chữ H hoa)
        $khachHangController = new KhachHangController($pdo);

        // Lấy action từ URL: khachhang/add → $action = add
        $url_clean = explode('&', $url)[0];                    // bỏ phần sau dấu &
        $parts     = explode('/', rtrim($url_clean, '/'));     // chia theo dấu /
        $action    = $parts[1] ?? 'index';                     // mặc định là index

        if ($action === 'index' || empty($parts[1])) {
            $khachHangController->index();
        } elseif ($action === 'add') {
            $khachHangController->add();
        } elseif ($action === 'edit' && isset($_GET['id'])) {
            $khachHangController->edit((int)$_GET['id']);
        } elseif ($action === 'delete' && isset($_GET['id'])) {
            $khachHangController->delete((int)$_GET['id']);
        } elseif (in_array($action, ['xemchitiet', 'view']) && (isset($_GET['id']) || isset($_GET['makh']))) {
            $id = $_GET['id'] ?? $_GET['makh'];
            $khachHangController->xemchitiet((int)$id);
        } else {
            // Nếu gõ linh tinh → quay về danh sách
            $khachHangController->index();
        }
        break;

    case 'xuatchuong':
    case 'xuatchuong/add':
    case 'xuatchuong/edit':
    case 'xuatchuong/delete':
        require_once __DIR__ . '/../core/controllers/XuatChuongController.php';

        $xuatChuongController = new XuatChuongController($pdo); // tên class đúng rồi

        $url_clean = explode('&', $url)[0];
        $parts = explode('/', rtrim($url_clean, '/'));

        $action = $parts[1] ?? 'index';

        if ($action === 'index' || empty($parts[1])) {
            $xuatChuongController->index();
        } elseif ($action === 'add') {
            $xuatChuongController->add();
        } elseif ($action === 'edit' && isset($_GET['id'])) {
            $xuatChuongController->edit((int)$_GET['id']);
        } elseif ($action === 'delete' && isset($_GET['id'])) {
            $xuatChuongController->delete((int)$_GET['id']); // truyền $id vào luôn cho đẹp
        } else {
            $xuatChuongController->index();
        }
        break;




    // ====================== BÁO CÁO TÀI CHÍNH ======================
    case 'baocaotaichinh':
    case 'baocaotaichinh/add':
    case 'baocaotaichinh/edit':
    case 'baocaotaichinh/delete':
        // Require controller chỉ 1 lần
        require_once __DIR__ . '/../core/controllers/BaoCaoTaiChinhController.php';

        // TÊN CLASS PHẢI ĐÚNG 100% – viết hoa đúng chỗ
        $baoCaoTaiChinhController = new BaoCaoTaiChinhController($pdo);

        // Lấy action từ URL: baocaotaichinh/add → action = add
        $url_clean = explode('&', $url)[0];                    // bỏ phần ?id=...
        $parts     = explode('/', rtrim($url_clean, '/'));     // chia theo dấu /
        $action    = $parts[1] ?? 'index';                     // mặc định là index

        // Điều hướng – chỉ dùng index vì báo cáo không có add/edit/delete
        if ($action === 'index' || empty($parts[1])) {
            $baoCaoTaiChinhController->index();
        } elseif ($action === 'add') {
            // Báo cáo tài chính không có thêm mới → chuyển về index
            $baoCaoTaiChinhController->index();
        } elseif ($action === 'edit' && isset($_GET['id'])) {
            $baoCaoTaiChinhController->index();
        } elseif ($action === 'delete' && isset($_GET['id'])) {
            $baoCaoTaiChinhController->index();
        } else {
            // Mọi trường hợp khác → về trang báo cáo
            $baoCaoTaiChinhController->index();
        }
        break;




    case 'sinhsan/ghiNhanDe':
        require_once 'views/sinhsan/ghiNhanDe.php';
        break;
    // ================== MẶC ĐỊNH (404) ==================
    default:
        http_response_code(404);
        echo "<h1 class='text-center text-4xl mt-20'>404 - Không tìm thấy trang!</h1>";
        break;
}
