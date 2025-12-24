<?php
// core/controllers/DashboardController.php
require_once __DIR__ . '/../models/DashboardModel.php';

class DashboardController
{
    private $db;
    private $model;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        // Khởi tạo Model
        $this->model = new DashboardModel($this->db);
    }

    public function index()
    {
        $stats = $this->model->getStats();

        // Giải nén biến ra để View dùng
        $tongDanHeo = $stats['tongDanHeo'] ?? 0;
        $heoCon = $stats['heoCon'] ?? 0;
        $heoNai = $stats['heoNai'] ?? 0;
        $heoDuc = $stats['heoDuc'] ?? 0;
        $heoGia = $stats['heoGia'] ?? 0;
        $heoNaiChoSinh = $stats['heoNaiChoSinh'] ?? 0;
        $heoDangBenh = $stats['heoDangBenh'] ?? 0;
        $heoCanTiemChung = $stats['heoCanTiemChung'] ?? 0;

        // THÊM DÒNG NÀY ĐỂ LẤY XUẤT TUẦN NAY
        $heoXuatTuanNay = $this->model->getHeoXuatTuanNay();

        // Các biến phụ khác (có thể tính sau)
        $tongKhachHang = $this->model->getTongKhachHang();
        $doanhThuTrieu = 0;
        $labels = [];
        $chartCanNang = [];
        $chartDoanhThu = [];

        require __DIR__ . '/../../admin/views/dashboard/index.php';
    }
}
