<?php
class BaoCaoTaiChinhModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    // Lấy báo cáo năm hiện tại từ bảng baocaotaichinh (anh đã tạo)
    public function getBaoCaoHienTai($nam = null)
    {
        if (!$nam) $nam = date('Y');

        $stmt = $this->pdo->prepare("
            SELECT 
                Nam,
                DoanhThu,
                ChiPhi,
                DonGiaBan,
                LoiNhuan,
                TyLeLoiNhuan,
                TongSoHeoBan,
                SoKhachHang,
                GhiChu
            FROM baocaotaichinh 
            WHERE Nam = ?
        ");
        $stmt->execute([$nam]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Doanh thu theo từng tháng trong năm (từ bảng xuatchuong)
    public function getDoanhThuTheoThang($nam = null)
    {
        if (!$nam) $nam = date('Y');

        $sql = "
            SELECT 
                MONTH(NgayXuat) AS thang,
                COALESCE(SUM(SoLuong * DonGia), 0) AS doanhthu
            FROM xuatchuong 
            WHERE YEAR(NgayXuat) = ?
            GROUP BY MONTH(NgayXuat)
            ORDER BY thang
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$nam]);

        $result = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        $data = array_fill(1, 12, 0);
        foreach ($result as $th => $dt) {
            $data[$th] = (int)$dt;
        }
        return $data;
    }

    // TOP KHÁCH HÀNG MUA NHIỀU NHẤT (ĐÃ SỬA LỖI LIMIT)
    public function getTopKhachHang($limit = 5)
    {
        $limit = (int)$limit; // ép kiểu int để an toàn

        $sql = "
        SELECT 
            kh.TenKH,
            kh.SDT,
            COALESCE(SUM(xc.SoLuong), 0) AS tong_mua,
            COALESCE(SUM(xc.SoLuong * xc.DonGia), 0) AS tong_tien
        FROM khachhang kh
        LEFT JOIN xuatchuong xc ON kh.MaKH = xc.MaKH
        GROUP BY kh.MaKH
        ORDER BY tong_mua DESC, tong_tien DESC
        LIMIT $limit
    ";

        $stmt = $this->pdo->query($sql); // dùng query() thay vì prepare() + execute()
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
