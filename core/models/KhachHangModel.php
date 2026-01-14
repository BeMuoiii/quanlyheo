<?php
class KhachHangModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Lấy danh sách khách hàng với tìm kiếm, sắp xếp và phân trang
     */
    public function getAll($keyword = '', $sort = 'MaKH', $order = 'DESC', $limit = null, $offset = null)
    {
        $sql = "
            SELECT kh.*, nv.HoTen AS TenNhanVien
            FROM khachhang kh
            LEFT JOIN nhanvien nv ON kh.MaNVPhuTrach = nv.MaNV
            WHERE 1=1
        ";
        $params = [];

        if (!empty($keyword)) {
            $sql .= " AND (kh.TenKH LIKE ? OR kh.SDT LIKE ? OR kh.MaKH LIKE ? OR kh.DiaChi LIKE ?)";
            $likeKeyword = "%$keyword%";
            $params = [$likeKeyword, $likeKeyword, $likeKeyword, $likeKeyword];
        }

        // Bảo vệ chống SQL injection
        $allowedSorts = ['MaKH', 'TenKH', 'SDT', 'DiaChi', 'NgaySinh'];
        $sort = in_array($sort, $allowedSorts) ? $sort : 'MaKH';
        $order = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC';

        $sql .= " ORDER BY kh.$sort $order";

        if ($limit !== null && $offset !== null) {
            $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Đếm tổng số khách hàng (cho phân trang)
     */
    public function getTotal($keyword = '')
    {
        $sql = "SELECT COUNT(*) FROM khachhang WHERE 1=1";
        $params = [];

        if (!empty($keyword)) {
            $sql .= " AND (TenKH LIKE ? OR SDT LIKE ? OR MaKH LIKE ? OR DiaChi LIKE ?)";
            $likeKeyword = "%$keyword%";
            $params = [$likeKeyword, $likeKeyword, $likeKeyword, $likeKeyword];
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Lấy thông tin khách hàng theo MaKH (STRING) - DÙNG CHÍNH CHO EDIT, DELETE, CHI TIẾT
     */
    public function getByMaKH($maKH)
    {
        $sql = "
            SELECT kh.*, nv.HoTen AS TenNhanVien 
            FROM khachhang kh 
            LEFT JOIN nhanvien nv ON kh.MaNVPhuTrach = nv.MaNV 
            WHERE kh.MaKH = ?
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$maKH]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Giữ lại getById để tương thích cũ (nếu có chỗ nào còn dùng)
     * Nhưng chuyển hướng về getByMaKH để hỗ trợ string
     */
    public function getById($id)
    {
        return $this->getByMaKH($id);
    }

    /**
     * Thêm khách hàng mới
     */
    public function create($data)
    {
        try {
            // Điều chỉnh theo cột thực tế của bảng khachhang
            // Tôi đã loại bỏ ChuongNhap vì khách hàng thường không cần
            // Nếu bảng có thêm GhiChu hoặc cột khác → thêm lại
            $sql = "INSERT INTO khachhang 
                (TenKH, SDT, GioiTinh, Email, NgaySinh, DiaChi, MaNVPhuTrach) 
                VALUES 
                (:TenKH, :SDT, :GioiTinh, :Email, :NgaySinh, :DiaChi, :MaNVPhuTrach)";

            $stmt = $this->pdo->prepare($sql);

            $stmt->bindValue(':TenKH', $data['TenKH'] ?? null, PDO::PARAM_STR);
            $stmt->bindValue(':SDT', $data['SDT'] ?? null, PDO::PARAM_STR);
            $stmt->bindValue(':GioiTinh', $data['GioiTinh'] ?? 'Nam', PDO::PARAM_STR);
            $stmt->bindValue(':Email', $data['Email'] ?? null, PDO::PARAM_STR);
            $stmt->bindValue(':NgaySinh', $data['NgaySinh'] ?? null, PDO::PARAM_STR);
            $stmt->bindValue(':DiaChi', $data['DiaChi'] ?? null, PDO::PARAM_STR);
            $stmt->bindValue(':MaNVPhuTrach', $data['MaNVPhuTrach'] ?? null, PDO::PARAM_INT);

            $stmt->execute();

            return true;
        } catch (PDOException $e) {
            error_log('Lỗi thêm khách hàng: ' . $e->getMessage());
            return "Lỗi khi thêm khách hàng: " . $e->getMessage();
        }
    }

    /**
     * Cập nhật khách hàng - dùng MaKH string
     */
    public function update($data)
    {
        try {
            // Loại bỏ GhiChu và ChuongNhap vì không liên quan đến khách hàng
            // Nếu bảng thực sự có → thêm lại
            $sql = "UPDATE khachhang SET 
                        TenKH = :TenKH,
                        SDT = :SDT,
                        Email = :Email,
                        NgaySinh = :NgaySinh,
                        GioiTinh = :GioiTinh,
                        DiaChi = :DiaChi,
                        MaNVPhuTrach = :MaNVPhuTrach
                    WHERE MaKH = :MaKH";

            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                ':TenKH'        => $data['TenKH'] ?? null,
                ':SDT'          => $data['SDT'] ?? null,
                ':Email'        => $data['Email'] ?? null,
                ':NgaySinh'     => $data['NgaySinh'] ?? null,
                ':GioiTinh'     => $data['GioiTinh'] ?? 'Nam',
                ':DiaChi'       => $data['DiaChi'] ?? null,
                ':MaNVPhuTrach' => $data['MaNVPhuTrach'] ?? null,
                ':MaKH'         => $data['MaKH']
            ]);

            return true;
        } catch (PDOException $e) {
            error_log('Lỗi cập nhật khách hàng: ' . $e->getMessage());
            return "Lỗi cập nhật: " . $e->getMessage();
        }
    }

    /**
     * Xóa khách hàng - dùng MaKH string, không cast int
     */
    public function delete($maKH)
    {
        try {
            // Tắt kiểm tra khóa ngoại để có thể xóa
            $this->pdo->exec("SET FOREIGN_KEY_CHECKS = 0");

            // Xóa dữ liệu ở các bảng liên quan trước
            $stmt1 = $this->pdo->prepare("DELETE FROM xuatchuong WHERE MaKH = ?");
            $stmt1->execute([$maKH]);

            $stmt2 = $this->pdo->prepare("DELETE FROM sinhsan WHERE MaKH = ?");
            $stmt2->execute([$maKH]);

            // Xóa khách hàng chính
            $stmt3 = $this->pdo->prepare("DELETE FROM khachhang WHERE MaKH = ?");
            $stmt3->execute([$maKH]);

            // Bật lại kiểm tra khóa ngoại
            $this->pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

            return true;
        } catch (PDOException $e) {
            error_log("Lỗi xóa: " . $e->getMessage());
            return false;
        }
    }
    /**
     * Lấy thông tin chi tiết khách hàng (dùng cho modal xem nhanh) - đổi sang string MaKH
     */
    public function getXemChiTietById($maKH)
    {
        try {
            $sql = "
                SELECT 
                    kh.*, 
                    nv.HoTen AS TenNhanVienPhuTrach
                FROM khachhang kh
                LEFT JOIN nhanvien nv ON kh.MaNVPhuTrach = nv.MaNV
                WHERE kh.MaKH = ?
            ";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$maKH]);
            $khachhang = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$khachhang) {
                return false;
            }

            // Format ngày sinh
            if (!empty($khachhang['NgaySinh']) && $khachhang['NgaySinh'] !== '0000-00-00') {
                $khachhang['NgaySinhFormat'] = date('d/m/Y', strtotime($khachhang['NgaySinh']));
            } else {
                $khachhang['NgaySinhFormat'] = 'Chưa cập nhật';
            }

            $khachhang['GioiTinhHienThi'] = ($khachhang['GioiTinh'] === 'Nam') ? 'Nam' : 'Nữ';

            // Thống kê mặc định
            $khachhang['thong_ke'] = [
                'tong_con_heo_da_mua' => 0,
                'tong_tien_da_chi'    => '0 đ',
                'lan_cuoi_mua'        => 'Chưa mua'
            ];

            // Thống kê từ xuatchuong
            $statSql = "
                SELECT 
                    COUNT(*) AS so_lan_xuat,
                    COALESCE(SUM(TongTien), 0) AS tong_tien,
                    COALESCE(SUM(SoHeoXuat), 0) AS tong_so_con,
                    MAX(NgayXuat) AS lan_cuoi
                FROM xuatchuong 
                WHERE MaKH = ?
            ";
            $statStmt = $this->pdo->prepare($statSql);
            $statStmt->execute([$maKH]);
            $stats = $statStmt->fetch(PDO::FETCH_ASSOC);

            if ($stats && $stats['so_lan_xuat'] > 0) {
                $lanCuoiFormat = $stats['lan_cuoi'] ? date('d/m/Y', strtotime($stats['lan_cuoi'])) : 'Chưa mua';

                $khachhang['thong_ke'] = [
                    'tong_con_heo_da_mua' => (int)$stats['tong_so_con'],
                    'tong_tien_da_chi'    => number_format((float)$stats['tong_tien'], 0, ',', '.') . ' đ',
                    'lan_cuoi_mua'        => $lanCuoiFormat
                ];
            }

            // Số lần phối giống
            $phoiSql = "SELECT COUNT(*) AS so_lan_phoi FROM sinhsan WHERE MaKH = ?";
            $phoiStmt = $this->pdo->prepare($phoiSql);
            $phoiStmt->execute([$maKH]);
            $khachhang['so_lan_phoi_giong'] = (int)$phoiStmt->fetchColumn();

            return $khachhang;
        } catch (PDOException $e) {
            error_log("Lỗi lấy chi tiết khách hàng {$maKH}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Đếm tổng số con heo khách đã mua
     */
    public function getTongHeoDaMua($maKH)
    {
        $sql = "
            SELECT COALESCE(SUM(xc.SoHeoXuat), 0) 
            FROM xuatchuong xc
            WHERE xc.MaKH = ?
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$maKH]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Lấy danh sách heo đã xuất cho khách
     */
    public function getHeoDaXuat($maKH)
    {
        $sql = "
            SELECT 
                h.MaHeo,
                h.GioiTinh,
                h.NgaySinh,
                h.MaBo AS Cha,
                h.MaMe AS Me,
                h.GhiChu,
                xc.NgayXuat,
                xc.CanNangXuat AS CanNang,
                xc.SoHeoXuat,
                xc.SoLuong
            FROM heo h
            INNER JOIN xuatchuong xc ON h.MaHeo = xc.MaHeo
            WHERE xc.MaKH = ?
            ORDER BY xc.NgayXuat DESC, h.MaHeo
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$maKH]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy lịch phối giống của khách
     */
    public function getLichPhoiGiong($maKH)
    {
        $sql = "
            SELECT 
                sp.SinhSan,
                sp.NgayPhoi,
                DATE_ADD(sp.NgayPhoi, INTERVAL 114 DAY) AS NgayDuKienDe,
                sp.SoConSong,
                sp.SoConChet,
                sp.GhiChu AS GhiChu,
                sp.GhiChuDe,
                hd.MaHeo AS TenHeoDuc,
                sp.MaHeoNai AS MaHeoCai
            FROM sinhsan sp
            LEFT JOIN heo hd ON sp.MaHeoDuc = hd.MaHeo
            WHERE sp.MaKH = ?
            ORDER BY sp.NgayPhoi DESC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$maKH]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
