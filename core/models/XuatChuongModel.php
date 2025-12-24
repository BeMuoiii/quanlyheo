<?php
class XuatChuongModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getTongXuatTuanNay()
    {
        $sql = "SELECT COUNT(*) FROM xuatchuong WHERE YEARWEEK(NgayXuat, 1) = YEARWEEK(CURDATE(), 1)";
        return (int)$this->pdo->query($sql)->fetchColumn();
    }

    public function getDsHeoChuaXuat($maHeoHienTai = null)
    {
        // Chỉnh sửa để lấy thêm tên hiển thị giống như Controller cần
        $sql = "SELECT MaHeo, GiongHeo, CanNangHienTai, ViTriChuong, GioiTinh, TrangThaiHeo,
                CONCAT('Heo ', MaHeo, ' - ', IFNULL(GiongHeo,''), ' - ', CanNangHienTai, 'kg') AS TenHeo
                FROM heo 
                WHERE TrangThaiHeo != 'Chết' AND TrangThaiHeo != 'Đã xuất'
                OR MaHeo = ?
                ORDER BY MaHeo DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$maHeoHienTai]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($maXuat)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM xuatchuong WHERE MaXuat = ?");
        $stmt->execute([$maXuat]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($data)
    {
        $maXuat       = (int)$data['MaXuat'];
        $maHeoMoi     = $data['MaHeo'];
        $maKH         = !empty($data['MaKH']) ? (int)$data['MaKH'] : null;
        $ngayXuat     = $data['NgayXuat'];
        $canNangXuat  = (float)$data['CanNangXuat'];
        $soLuong      = (int)$data['SoLuong'];
        $donGia       = (float)$data['DonGia'];
        $lyDo         = $data['LyDoXuat'] ?? 'Bán thịt';
        $ghiChu       = $data['GhiChu'] ?? '';
        $maNV         = $data['MaNVThucHien'];

        $thanhTien = $canNangXuat * $donGia * $soLuong;

        // 1. Lấy thông tin cũ để so sánh
        $stmtOld = $this->pdo->prepare("SELECT MaHeo FROM xuatchuong WHERE MaXuat = ?");
        $stmtOld->execute([$maXuat]);
        $old = $stmtOld->fetch(PDO::FETCH_ASSOC);

        if (!$old) throw new Exception("Không tìm thấy phiếu xuất cần sửa!");
        $maHeoCu = $old['MaHeo'];

        try {
            $this->pdo->beginTransaction();

            // 2. Cập nhật bảng xuatchuong
            $sql = "UPDATE xuatchuong SET 
                    MaHeo = ?, MaKH = ?, NgayXuat = ?, CanNangXuat = ?,
                    SoLuong = ?, DonGia = ?, ThanhTien = ?, LyDoXuat = ?, 
                    GhiChu = ?, MaNVThucHien = ?
                    WHERE MaXuat = ?";

            $this->pdo->prepare($sql)->execute([
                $maHeoMoi,
                $maKH,
                $ngayXuat,
                $canNangXuat,
                $soLuong,
                $donGia,
                $thanhTien,
                $lyDo,
                $ghiChu,
                $maNV,
                $maXuat
            ]);

            // 3. Xử lý logic bảng Heo (Quan trọng để giữ đúng dữ liệu)
            if ($maHeoMoi != $maHeoCu) {
                // Nếu đổi sang con heo khác:
                // Trả heo cũ về trạng thái bình thường
                $this->pdo->prepare("UPDATE heo SET TrangThaiHeo = 'Bình thường' WHERE MaHeo = ?")
                    ->execute([$maHeoCu]);

                // Chuyển heo mới sang trạng thái Đã xuất và cập nhật cân nặng
                $this->pdo->prepare("UPDATE heo SET TrangThaiHeo = 'Đã xuất', CanNangHienTai = ? WHERE MaHeo = ?")
                    ->execute([$canNangXuat, $maHeoMoi]);
            } else {
                // Nếu vẫn là con heo đó, chỉ cập nhật lại cân nặng hiện tại của nó
                $this->pdo->prepare("UPDATE heo SET CanNangHienTai = ?, TrangThaiHeo = 'Đã xuất' WHERE MaHeo = ?")
                    ->execute([$canNangXuat, $maHeoMoi]);
            }

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
    public function delete($maXuat)
    {
        $old = $this->find($maXuat);
        if (!$old) return false;

        try {
            $this->pdo->beginTransaction();
            $this->pdo->prepare("DELETE FROM xuatchuong WHERE MaXuat = ?")->execute([$maXuat]);
            $this->pdo->prepare("UPDATE heo SET TrangThaiHeo = 'Bình thường' 
                                 WHERE MaHeo = ? AND TrangThaiHeo = 'Đã xuất'")
                ->execute([$old['MaHeo']]);
            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }
}
