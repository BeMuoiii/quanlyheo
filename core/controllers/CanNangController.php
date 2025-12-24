<?php
class CanNangController
{

    public function index()
    {
        global $pdo;
        $canNangModel = new CanNangModel($pdo);

        $danhSachCanNang = $canNangModel->getAll();

        $rootDir = dirname(dirname(__DIR__));
        $viewPath = $rootDir . '/admin/views/cannang/index.php';

        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            echo "Lỗi: Không tìm thấy file view index.";
        }
    }

    public function add() // ⭐ BẮT ĐẦU HÀM ADD
    {
        global $pdo;
        $canNangModel = new CanNangModel($pdo);
        $rootDir = dirname(dirname(__DIR__));
        $error_message = "";

        // Lấy danh sách heo
        $stmt_heo = $pdo->prepare("SELECT MaHeo FROM heo ORDER BY MaHeo ASC");
        $stmt_heo->execute();
        $danhSachHeo = $stmt_heo->fetchAll(PDO::FETCH_ASSOC);

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $result = $canNangModel->create($_POST);
            if ($result === true) {
                header('Location: index.php?url=cannang');
                exit;
            } elseif (is_string($result)) {
                $error_message = $result;
            } else {
                $error_message = "Lỗi hệ thống. Vui lòng thử lại.";
            }
        }

        $viewPath = $rootDir . '/admin/views/cannang/add.php';
        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            echo "Lỗi: Không tìm thấy file view add.";
        }
    } // ⭐ KẾT THÚC HÀM ADD (Dấu '}' đã được thêm vào đây)

    public function edit($maCan)  // ← ĐỔI TÊN BIẾN TỪ $id → $maCan
    {
        global $pdo;
        $model = new CanNangModel($pdo);

        // Lấy danh sách heo
        $stmt = $pdo->query("SELECT MaHeo FROM heo ORDER BY MaHeo");
        $danhSachHeo = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $model->update(array_merge($_POST, ['MaCan' => $maCan])); // ← Dùng MaCan
            if ($result === true) {
                header('Location: index.php?url=cannang');
                exit;
            } else {
                $error_message = $result;
            }
        }

        // Lấy dữ liệu theo MaCan
        $cannang_data = $model->getByMaCan($maCan); // ← Dùng hàm mới
        if (!$cannang_data) {
            header('Location: index.php?url=cannang');
            exit;
        }

        require __DIR__ . '/views/cannang/edit.php';
    }

    public function delete($maCan)  // ← Đổi $id → $maCan
    {
        global $pdo;
        $model = new CanNangModel($pdo);
        $model->delete($maCan); // ← Dùng MaCan
        header('Location: index.php?url=cannang');
        exit;
    }
} // ⭐ KẾT THÚC CLASS CanNangController