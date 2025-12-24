<?php
// File: core/models/SinhSanModel.php
class SinhSanModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    // === LẤY DANH SÁCH PHỐI GIỐNG (CHO TRANG INDEX) ===
    public function getAll()
    {
        $sql = "
        SELECT 
            ss.*,
            CASE 
                WHEN ss.NgayPhoi IS NOT NULL
                THEN DATE_ADD(ss.NgayPhoi, INTERVAL 114 DAY)
                ELSE NULL
            END AS NgayDuSinh,
            hn.MaHeo AS MaHeoNai, hn.GiongHeo AS GiongNai,
            hd.MaHeo AS MaHeoDuc, hd.GiongHeo AS GiongDuc,
            nv.HoTen AS TenNhanVien
        FROM sinhsan ss
        LEFT JOIN heo hn ON ss.MaHeoNai = hn.MaHeo
        LEFT JOIN heo hd ON ss.MaHeoDuc = hd.MaHeo
        LEFT JOIN nhanvien nv ON ss.MaNVThucHien = nv.MaNV
        ORDER BY ss.NgayPhoi DESC
    ";

        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }


    // === LẤY CHI TIẾT 1 PHIẾU PHỐI GIỐNG ===
    public function getById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM sinhsan WHERE SinhSan = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // === THÊM MỚI PHỐI GIỐNG ===

    public function create($data)
    {
        try {
            $maNV = $this->validateMaNV($data['MaNVThucHien'] ?? null);

            // ✅ Xử lý ngày phối
            $ngayPhoi = null;
            if (!empty($data['NgayPhoi'])) {
                $dt = DateTime::createFromFormat('d/m/Y', $data['NgayPhoi']);
                $ngayPhoi = $dt ? $dt->format('Y-m-d') : null;
            }

            $sql = "INSERT INTO sinhsan 
            (MaHeoNai, MaHeoDuc, NgayPhoi, SoConSong, MaNVThucHien, GhiChu, TrangThai) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $data['MaHeoNai'],
                $data['MaHeoDuc'] ?: null,
                $ngayPhoi,                    // ✅ ĐÚNG
                $data['SoConSong'] ?: null,
                $maNV,
                $data['GhiChu'] ?? null,
                $data['TrangThai'] ?? 'DangTheoDoi'
            ]);

            return true;
        } catch (PDOException $e) {
            return "Lỗi lưu phối giống: " . $e->getMessage();
        }
    }

    // === CẬP NHẬT ===
    public function update($data)
    {
        try {
            $maNV = $this->validateMaNV($data['MaNVThucHien'] ?? null);

            $sql = "UPDATE sinhsan SET 
                MaHeoNai = ?, 
                MaHeoDuc = ?, 
                NgayPhoi = ?, 
                NgayDe = ?, 
                SoConSong = ?, 
                SoConChet = ?, 
                MaNVThucHien = ?, 
                MaNVDe = ?, 
                GhiChu = ?, 
                GhiChuDe = ?, 
                TrangThai = ?
            WHERE SinhSan = ?";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $data['MaHeoNai'],
                $data['MaHeoDuc'] ?: null,
                $data['NgayPhoi'] ?: null,
                $data['NgayDe'] ?: null,
                $data['SoConSong'] ?: null,
                $data['SoConChet'] ?: null,
                $maNV,                     // NV phối giống
                $data['MaNVDe'] ?? null,  // NV đỡ đẻ
                $data['GhiChu'] ?? null,
                $data['GhiChuDe'] ?? null,
                $data['TrangThai'] ?? 'DangTheoDoi',
                $data['SinhSan']
            ]);

            return true;
        } catch (PDOException $e) {
            return "Lỗi cập nhật: " . $e->getMessage();
        }
    }

    // === HÀM GHI NHẬN ĐẺ (CẬP NHẬT THÔNG TIN SAU KHI SINH) ===
    public function updateGhiNhanDe($id, $data)
    {
        try {
            $this->pdo->beginTransaction();

            $sql = "UPDATE sinhsan SET 
                    NgayDe = ?, 
                    SoConSong = ?, 
                    SoConChet = ?, 
                    MaNVDe = ?, 
                    GhiChuDe = ?, 
                    TrangThai = 'DaSinh'
                WHERE SinhSan = ?";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $data['NgayDe'],
                $data['SoConSong'],
                $data['SoConChet'],
                $data['MaNVDe'] ?: null,
                $data['GhiChuDe'] ?? null,
                $id
            ]);

            $stmtNai = $this->pdo->prepare("SELECT MaHeoNai FROM sinhsan WHERE SinhSan = ?");
            $stmtNai->execute([$id]);
            $maHeoNai = $stmtNai->fetchColumn();

            if ($maHeoNai) {
                $stmtHeo = $this->pdo->prepare(
                    "UPDATE heo SET TrangThaiHeo = 'Chăm sóc con' WHERE MaHeo = ?"
                );
                $stmtHeo->execute([$maHeoNai]);
            }

            $this->pdo->commit();
            return [
                'status'  => true,
                'message' => 'OK'
            ];
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            return [
                'status'  => false,
                'message' => $e->getMessage()
            ];
        }
    }


    // === HÀM KIỂM TRA MÃ NHÂN VIÊN HỢP LỆ ===
    private function validateMaNV($maNV)
    {
        // Nếu rỗng, null, chuỗi trắng → trả về NULL (an toàn)
        if (empty($maNV) || trim($maNV) === '' || $maNV === '0') {
            return null;
        }

        $maNV = trim($maNV);

        // Kiểm tra xem mã nhân viên có thực sự tồn tại không
        $stmt = $this->pdo->prepare("SELECT MaNV FROM nhanvien WHERE MaNV = ?");
        $stmt->execute([$maNV]);

        return $stmt->fetch() ? $maNV : null; // Nếu không tồn tại → trả về NULL
    }




    // === XÓA PHIẾU PHỐI GIỐNG ===
    public function delete($id)
    {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM sinhsan WHERE SinhSan = ?");
            $stmt->execute([$id]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
}
