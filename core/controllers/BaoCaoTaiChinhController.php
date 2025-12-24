<?php
require_once __DIR__ . '/../models/BaoCaoTaiChinhModel.php'; 

class BaoCaoTaiChinhController
{
    private $pdo;
    private $model;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->model = new BaoCaoTaiChinhModel($pdo);
    }

    /** Load view an toàn – giống hệt HeoController */
    private function view($viewName, $data = [])
    {
        extract($data);
        $viewPath = __DIR__ . '/../../admin/views/baocaotaichinh/' . $viewName . '.php';

        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            die("Lỗi: Không tìm thấy file view tại <br><code>$viewPath</code>");
        }
    }

    /** TRANG CHỦ BÁO CÁO */
    public function index()
    {
        $namHienTai = date('Y');
        
        // =========================================================
        // 1. TÍNH TOÁN DOANH THU THUẦN THỰC TẾ TỪ XUẤT CHUỒNG HEƠ
        // =========================================================
        try {
            // Truy vấn CSDL để tính tổng cột ThanhTien trong bảng 'xuatchuong'
            $sqlDoanhThu = "
                SELECT SUM(ThanhTien) AS TongDoanhThu
                FROM xuatchuong
                WHERE YEAR(NgayXuat) = :namHienTai
            ";
            
            $stmt = $this->pdo->prepare($sqlDoanhThu);
            $stmt->execute([':namHienTai' => $namHienTai]);
            
            // Lấy kết quả (Đơn vị: Đồng)
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $tongDoanhThuThucTe = $result['TongDoanhThu'] ?? 0; // Giá trị là: 1827500 (đồng)
            
        } catch (Exception $e) {
            $tongDoanhThuThucTe = 0;
            // Ghi log lỗi: $e->getMessage();
        }
        
        // Lấy báo cáo tổng hợp từ bảng baocaotaichinh
        $baoCao = $this->model->getBaoCaoHienTai($namHienTai);

        // =========================================================
        // 2. CẬP NHẬT DOANH THU VÀ CHỈ SỐ LỢI NHUẬN
        // =========================================================
        
        // Nếu chưa có dữ liệu năm nay → dùng dữ liệu mẫu đẹp như hình
        if (!$baoCao) {
            $baoCao = [
                'Nam'             => $namHienTai,
                // **THAY THẾ:** Gán Doanh Thu thực tế vào đây
                'DoanhThu'        => $tongDoanhThuThucTe, 
                'ChiPhi'          => 11280000000, // Chi phí mẫu (bạn có thể thay bằng tổng chi phí thực tế)
                'DonGiaBan'       => 13644444,
                'TongSoHeoBan'    => 1350,
                // Tính Lợi nhuận gộp mẫu dựa trên Doanh thu thực tế và Chi phí mẫu
                'LoiNhuan'        => $tongDoanhThuThucTe - 11280000000, 
                'TyLeLoiNhuan'    => 0, // Tính lại tỷ lệ sau
                'SoKhachHang'     => 48,
                'GhiChu'          => 'Dữ liệu mẫu'
            ];
            
            // Tính lại tỷ lệ lợi nhuận gộp
            if ($baoCao['DoanhThu'] > 0) {
                $baoCao['TyLeLoiNhuan'] = ($baoCao['LoiNhuan'] / $baoCao['DoanhThu']) * 100;
            }
        }
        
        // **QUAN TRỌNG NHẤT:** Cần tính toán các chỉ số đã được format/chuyển đổi để View có thể sử dụng
        $dataView = [
            'tongDoanhThu' => $baoCao['DoanhThu'] / 1000000, // Đơn vị: Triệu
            'tongChiPhi'   => $baoCao['ChiPhi'] / 1000000,   // Đơn vị: Triệu
            'loiNhuanGop'  => $baoCao['LoiNhuan'] / 1000000,  // Đơn vị: Triệu
            'tySuatGop'    => number_format($baoCao['TyLeLoiNhuan'], 1),
            // Giả định Lãi ròng = 60% Lãi gộp (Hoặc bạn cần tính Lãi ròng thực tế)
            'loiNhuanRong' => ($baoCao['LoiNhuan'] * 0.6) / 1000000, 
            'tySuatRong'   => number_format($baoCao['TyLeLoiNhuan'] * 0.6, 1), 
            'namHienTai'   => $namHienTai
        ];
        
        // Doanh thu từng tháng + top khách
        $doanhThuThang = $this->model->getDoanhThuTheoThang($namHienTai);
        $topKhachHang  = $this->model->getTopKhachHang(5);

        // Truyền các biến đã được tính toán ra View
        $this->view('index', array_merge($dataView, [
            'baoCao'         => $baoCao,
            'doanhThuThang'  => $doanhThuThang,
            'topKhachHang'   => $topKhachHang,
        ]));
    }
}