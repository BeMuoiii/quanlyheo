<?php
// core/models/DashboardModel.php

class DashboardModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getStats()
    {
        $stats = [];

        try {
            // 1. Tổng đàn heo
            $stats['tongDanHeo'] = $this->db->query("SELECT COUNT(*) FROM heo")->fetchColumn();

            // 2. Heo nái (C hoặc Cái)
            $stats['heoDuc'] = $this->db->query("
                SELECT COUNT(*) FROM heo
                WHERE LOWER(TRIM(GioiTinh)) IN ('đực','duc','d','1')
            ")->fetchColumn();

            $stats['heoNai'] = $this->db->query("
                SELECT COUNT(*) FROM heo
                WHERE LOWER(TRIM(GioiTinh)) IN ('cái','cai','c','0')
            ")->fetchColumn();

            // 4. Heo con (Chỉ lấy những con có lần cân mới nhất <= 8kg)
            $stats['heoCon'] = $this->db->query("
                SELECT COUNT(*) FROM (
                    SELECT MaHeo FROM cannang 
                    WHERE (MaHeo, NgayCan) IN (SELECT MaHeo, MAX(NgayCan) FROM cannang GROUP BY MaHeo)
                    AND CanNang <= 8
                ) as temp
            ")->fetchColumn();

            // 4. Heo con (Cân nặng <= 8kg) - Lấy từ bảng cannang
            $stats['heoCon'] = $this->db->query("SELECT COUNT(DISTINCT MaHeo) FROM cannang WHERE CanNang <= 8")->fetchColumn();

            // 5. Heo già / Heo thịt (Cân nặng >= 35kg) - Lấy từ bảng cannang
            $stats['heoGia'] = $this->db->query("SELECT COUNT(DISTINCT MaHeo) FROM cannang WHERE CanNang >= 35")->fetchColumn();

            // 6. Nái chờ sinh
            try {
                $stats['heoNaiChoSinh'] = $this->db->query("SELECT COUNT(*) FROM sinhsan")->fetchColumn();
            } catch (Exception $e) {
                $stats['heoNaiChoSinh'] = 0;
            }

            // 7. Trạng thái khác
            $stats['heoDangBenh'] = $this->db->query("SELECT COUNT(*) FROM heo WHERE TrangThai LIKE '%Bệnh%'")->fetchColumn();
            $stats['heoCanTiemChung'] = $this->db->query("SELECT COUNT(*) FROM heo WHERE TrangThai LIKE '%tiêm%'")->fetchColumn();
        } catch (PDOException $e) {
            error_log("Lỗi SQL Dashboard: " . $e->getMessage());
        }

        return $stats;
    }


    public function getHeoXuatTuanNay()
    {
        try {
            $sql = "
                SELECT COUNT(*) 
                FROM xuatchuong 
                WHERE YEARWEEK(NgayXuat, 1) = YEARWEEK(CURDATE(), 1)
            ";
            $stmt = $this->db->query($sql);
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Lỗi lấy heo xuất tuần này: " . $e->getMessage());
            return 0;
        }
    }
    public function getTongKhachHang()
    {
        try {
            $sql = "SELECT COUNT(*) FROM khachhang";
            $stmt = $this->db->query($sql);
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Lỗi lấy tổng khách hàng: " . $e->getMessage());
            return 0;
        }
    }
}
