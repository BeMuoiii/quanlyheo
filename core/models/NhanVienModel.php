<?php
// File: core/models/NhanVienModel.php
class NhanVienModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    // === LẤY DANH SÁCH NHÂN VIÊN CHO TRANG INDEX (JOIN bộ phận + user) ===
    public function getAll()
    {
        $sql = "SELECT nv.*, 
                       bp.TenBoPhan,
                       u.username AS TenDangNhap
                FROM nhanvien nv
                LEFT JOIN bophan bp ON nv.MaBoPhan = bp.MaBoPhan
                LEFT JOIN users u ON nv.UserID = u.UserID
                ORDER BY 
                    FIELD(nv.TrangThai, 'Thử việc', 'Chính thức', 'Nghỉ việc'),
                    nv.NgayVaoLam DESC,
                    nv.HoTen ASC";

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // === LẤY CHI TIẾT 1 NHÂN VIÊN ===
    public function getById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM nhanvien WHERE MaNV = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // === THÊM MỚI NHÂN VIÊN ===
    public function create($data)
    {
        try {
            $sql = "INSERT INTO nhanvien 
                    (HoTen, SDT, ViTri, CMND, NgaySinh, GioiTinh, DiaChi, 
                     MaBoPhan, NgayVaoLam, LuongCoBan, TrangThai, UserID, Anh, GhiChu) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $data['HoTen'],
                $data['SDT'] ?? null,
                $data['ViTri'] ?? null,
                $data['CMND'] ?? null,
                $data['NgaySinh'] ?? null,
                $data['GioiTinh'] ?? null,
                $data['DiaChi'] ?? null,
                $data['MaBoPhan'] ?? null,
                $data['NgayVaoLam'] ?? null,
                $data['LuongCoBan'] ?? 0,
                $data['TrangThai'] ?? 'Thử việc',
                $data['UserID'] ?? null,
                $data['Anh'] ?? null,
                $data['GhiChu'] ?? null
            ]);
            return $this->pdo->lastInsertId(); // Trả về MaNV mới tạo
        } catch (PDOException $e) {
            return "Lỗi thêm nhân viên: " . $e->getMessage();
        }
    }

    // === CẬP NHẬT NHÂN VIÊN ===
    public function update($data)
    {
        try {
            $sql = "UPDATE nhanvien SET 
                        HoTen = ?, SDT = ?, ViTri = ?, CMND = ?, 
                        NgaySinh = ?, GioiTinh = ?, DiaChi = ?,
                        MaBoPhan = ?, NgayVaoLam = ?, LuongCoBan = ?, 
                        TrangThai = ?, UserID = ?, Anh = ?, GhiChu = ?
                    WHERE MaNV = ?";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $data['HoTen'],
                $data['SDT'] ?? null,
                $data['ViTri'] ?? null,
                $data['CMND'] ?? null,
                $data['NgaySinh'] ?? null,
                $data['GioiTinh'] ?? null,
                $data['DiaChi'] ?? null,
                $data['MaBoPhan'] ?? null,
                $data['NgayVaoLam'] ?? null,
                $data['LuongCoBan'] ?? 0,
                $data['TrangThai'] ?? 'Thử việc',
                $data['UserID'] ?? null,
                $data['Anh'] ?? null,
                $data['GhiChu'] ?? null,
                $data['MaNV']
            ]);
            return true;
        } catch (PDOException $e) {
            return "Lỗi cập nhật: " . $e->getMessage();
        }
    }

    // === CHO NHÂN VIÊN NGHỈ VIỆC (chỉ cập nhật trạng thái + ngày nghỉ + lý do) ===
    public function nghiViec($maNV, $ngayNghi = null, $lyDo = '')
    {
        try {
            $ngayNghi = $ngayNghi ?: date('Y-m-d');
            $sql = "UPDATE nhanvien 
                    SET TrangThai = 'Nghỉ việc', NgayNghi = ?, LyDoNghi = ? 
                    WHERE MaNV = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$ngayNghi, $lyDo, $maNV]);
            return true;
        } catch (PDOException $e) {
            return "Lỗi cập nhật nghỉ việc: " . $e->getMessage();
        }
    }

    // === XÓA NHÂN VIÊN (cẩn thận chỉ dùng khi thật sự cần) ===
    public function delete($id)
    {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM nhanvien WHERE MaNV = ?");
            $stmt->execute([$id]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
}