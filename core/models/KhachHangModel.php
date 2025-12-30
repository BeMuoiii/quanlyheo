<?php
class KhachHangModel
{
    private $pdo;
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAll($keyword = '', $sort = 'MaKH', $order = 'DESC', $limit = null, $offset = null)
    {
        $sql = "
        SELECT kh.*, nv.HoTen AS TenNhanVien
        FROM khachhang kh
        LEFT JOIN nhanvien nv ON kh.MaNVPhuTrach = nv.MaNV
        WHERE 1=1
    ";
        $params = [];

        // Xử lý tìm kiếm
        if (!empty($keyword)) {
            $sql .= " AND (kh.TenKH LIKE ? OR kh.SDT LIKE ? OR kh.MaKH LIKE ? OR kh.DiaChi LIKE ?)";
            $params = array_merge($params, ["%$keyword%", "%$keyword%", "%$keyword%", "%$keyword%"]);
        }

        // Xử lý sắp xếp (Sử dụng trực tiếp $sort và $order vì đã được lọc ở Controller)
        $sql .= " ORDER BY kh.$sort $order";

        // Xử lý phân trang
        if ($limit !== null && $offset !== null) {
            $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Bạn nên thêm hàm này để đếm tổng số khách hàng phục vụ phân trang
    public function getTotal($keyword = '')
    {
        $sql = "SELECT COUNT(*) FROM khachhang WHERE 1=1";
        $params = [];

        if (!empty($keyword)) {
            $sql .= " AND (TenKH LIKE ? OR SDT LIKE ? OR MaKH LIKE ? OR DiaChi LIKE ?)";
            $params = ["%$keyword%", "%$keyword%", "%$keyword%", "%$keyword%"];
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }




    public function getById($id)
    {
        $sql = "
            SELECT kh.*, nv.HoTen AS TenNhanVien 
            FROM khachhang kh 
            LEFT JOIN nhanvien nv ON kh.MaNVPhuTrach = nv.MaNV 
            WHERE kh.MaKH = ?
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        try {
            $sql = "INSERT INTO khachhang 
                (TenKH, SDT, Email, NgaySinh, GioiTinh, DiaChi, GhiChu, MaNVPhuTrach, ChuongNhap) 
                VALUES 
                (:TenKH, :SDT, :Email, :NgaySinh, :GioiTinh, :DiaChi, :GhiChu, :MaNVPhuTrach, :ChuongNhap)";

            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                ':TenKH'        => $data['TenKH'] ?? null,
                ':SDT'          => $data['SDT'] ?? null,
                ':Email'        => $data['Email'] ?? null,
                ':NgaySinh'     => $data['NgaySinh'] ?? null,
                ':GioiTinh'     => $data['GioiTinh'] ?? 'D',
                ':DiaChi'       => $data['DiaChi'] ?? null,
                ':GhiChu'       => $data['GhiChu'] ?? null,
                ':MaNVPhuTrach' => $data['MaNVPhuTrach'] ?? null,
                ':ChuongNhap'   => $data['ChuongNhap'] ?? 'Thường'
            ]);

            return true;
        } catch (PDOException $e) {
            error_log('Lỗi INSERT khách hàng: ' . $e->getMessage());
            error_log('Data: ' . print_r($data, true));
            return 'Lỗi cơ sở dữ liệu: ' . $e->getMessage(); // tạm thời để debug, sau xóa đi
        }
    }

    public function update($data)
    {
        try {
            $sql = "UPDATE khachhang SET 
                        TenKH = :ten,
                        SDT = :sdt,
                        Email = :email,
                        NgaySinh = :ngaysinh,
                        GioiTinh = :gioitinh,
                        DiaChi = :diachi,
                        GhiChu = :ghichu,
                        MaNVPhuTrach = :manv,
                        ChuongNhap = :chuong
                    WHERE MaKH = :id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':ten'      => $data['TenKH'] ?? '',
                ':sdt'      => $data['SDT'] ?? null,
                ':email'    => $data['Email'] ?? null,
                ':ngaysinh' => $data['NgaySinh'] ?? null,
                ':gioitinh' => $data['GioiTinh'] ?? 'D',
                ':diachi'   => $data['DiaChi'] ?? null,
                ':ghichu'   => $data['GhiChu'] ?? null,
                ':manv'     => $data['MaNVPhuTrach'] ?? null,
                ':chuong'   => $data['ChuongNhap'] ?? null,
                ':id'       => $data['MaKH']
            ]);
            return true;
        } catch (Exception $e) {
            return "Lỗi cập nhật: " . $e->getMessage();
        }
    }

    public function delete($maKH)
    {
        try {
            $check = $this->pdo->prepare("SELECT COUNT(*) FROM hoadon WHERE MaKH = ?");
            $check->execute([$maKH, $maKH]); // ← LỖI: chỉ cần 1 tham số, mà truyền 2!

            if ($check->fetchColumn() > 0) {
                return "Không thể xóa vì khách hàng này đã có lịch sử giao dịch (Hóa đơn). Bạn nên giữ lại để quản lý.";
            }
            // Thực hiện xóa
            $stmt = $this->pdo->prepare("DELETE FROM heo WHERE maKH = ?"); // ← LỖI TO: bảng heo KHÔNG có cột maKH !!!
            $stmt->execute([$maKH]);

            if ($stmt->rowCount() === 0) {
                return "Không tìm thấy con heo với mã <strong>$maKH</strong> để xóa.";
            }

            // Xóa thành công
            return true;
        } catch (Exception $e) {
            return "Lỗi hệ thống khi xóa heo: " . $e->getMessage();
        }
    }


    // === XEM CHI TIẾT KHÁCH HÀNG - HÀM NÀY KHỚP VỚI CONTROLLER XEMCHITIET ===
    public function getXemChiTietById($id)
    {
        // Lấy thông tin cơ bản + tên nhân viên phụ trách
        $sql = "
            SELECT 
                kh.*, 
                nv.HoTen AS TenNhanVienPhuTrach
            FROM khachhang kh
            LEFT JOIN nhanvien nv ON kh.MaNVPhuTrach = nv.MaNV
            WHERE kh.MaKH = ?
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $khachhang = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$khachhang) {
            return false;
        }

        // Khởi tạo thống kê mặc định
        $khachhang['thong_ke'] = [
            'tong_giao_dich' => 0,
            'tong_tien'      => 0,
            'lan_cuoi_mua'   => null
        ];

        // Thống kê giao dịch từ bảng hoadon (nếu tồn tại)
        try {
            $statSql = "
                SELECT 
                    COUNT(*) AS so_lan,
                    COALESCE(SUM(TongTien), 0) AS tong_tien,
                    MAX(NgayLap) AS lan_cuoi
                FROM hoadon 
                WHERE MaKH = ?
            ";
            $statStmt = $this->pdo->prepare($statSql);
            $statStmt->execute([$id]);
            $stats = $statStmt->fetch(PDO::FETCH_ASSOC);

            if ($stats) {
                $khachhang['thong_ke'] = [
                    'tong_giao_dich' => (int)$stats['so_lan'],
                    'tong_tien'      => (float)$stats['tong_tien'],
                    'lan_cuoi_mua'   => $stats['lan_cuoi']
                ];
            }
        } catch (PDOException $e) {
            // Nếu chưa có bảng hoadon → bỏ qua, không lỗi
            error_log("Lỗi lấy thống kê khách hàng ID $id: " . $e->getMessage());
        }

        return $khachhang;
    }
}
