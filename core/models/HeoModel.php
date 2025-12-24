<?php
// File: core/models/HeoModel.php
class HeoModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    //=tìm kiếm
    // File: core/models/HeoModel.php

    // File: core/models/HeoModel.php
    // Chỉ sửa hàm getAll thôi nhé

    // Đếm tổng số heo (có tìm kiếm)
    public function getTotal($keyword = '', $gioitinh = '')
    {
        $sql = "SELECT COUNT(*) FROM heo h WHERE 1=1";
        $params = [];

        // 1. Lọc giới tính từ bộ lọc tiêu đề
        if (!empty($gioitinh)) {
            $sql .= " AND h.GioiTinh = ?";
            $params[] = $gioitinh;
        }

        // 2. Tìm kiếm từ khóa (Phải giống hệt getAll)
        if (!empty($keyword)) {
            $search = "%$keyword%";
            $sql .= " AND (
            h.MaHeo LIKE ? OR 
            h.GiongHeo LIKE ? OR 
            h.ViTriChuong LIKE ? OR
            (CASE WHEN h.GioiTinh = 'C' THEN 'Cái' WHEN h.GioiTinh = 'D' THEN 'Đực' ELSE '' END) LIKE ?
        )";
            $params = array_merge($params, [$search, $search, $search, $search]);
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }
    // Sửa lại hàm getAll() để nhận thêm limit & offset
    public function getAll($keyword = '', $gioitinh = '', $sort = 'MaHeo', $order = 'DESC', $limit = 10, $offset = 0)
    {
        $sql = "
        SELECT 
            h.*,
            bo.MaHeo AS MaHeoBo,    
            me.MaHeo AS MaHeoMe     
        FROM heo h 
        LEFT JOIN heo bo ON h.MaBo = bo.MaHeo
        LEFT JOIN heo me ON h.MaMe = me.MaHeo
        WHERE 1=1
    ";

        $params = [];

        // 1. Lọc giới tính (Bộ lọc tại tiêu đề cột)
        if (!empty($gioitinh)) {
            // Sử dụng TRIM để loại bỏ khoảng trắng thừa
            $sql .= " AND TRIM(h.GioiTinh) = ?";
            $params[] = $gioitinh;
        }

        // 2. Lọc từ khóa (Ô tìm kiếm chung) - CHỈ DÙNG 1 KHỐI NÀY THÔI
        if (!empty($keyword)) {
            $search = "%$keyword%";
            $sql .= " AND (
            h.MaHeo LIKE ? OR 
            h.GiongHeo LIKE ? OR 
            h.ViTriChuong LIKE ? OR
            (CASE WHEN h.GioiTinh = 'C' THEN 'Cái' WHEN h.GioiTinh = 'D' THEN 'Đực' ELSE '' END) LIKE ?
        )";
            // Dùng array_merge để thêm vào sau biến giới tính, không được dùng dấu = trực tiếp
            $params = array_merge($params, [$search, $search, $search, $search]);
        }

        // Phần sắp xếp
        if ($sort === 'MaHeo' || $sort === 'ViTriChuong') {
            $sql .= " ORDER BY CAST(REGEXP_SUBSTR(h.$sort, '[0-9]+') AS UNSIGNED) $order, h.$sort $order";
        } elseif ($sort === 'CanNangHienTai') {
            $sql .= " ORDER BY CAST(h.$sort AS DECIMAL(10,2)) $order";
        } else {
            $sql .= " ORDER BY h.$sort $order, h.MaHeo DESC";
        }

        $sql .= " LIMIT ? OFFSET ?";
        $stmt = $this->pdo->prepare($sql);

        $currentIndex = 1;
        foreach ($params as $val) {
            $stmt->bindValue($currentIndex++, $val, PDO::PARAM_STR);
        }

        $stmt->bindValue($currentIndex++, (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue($currentIndex++, (int)$offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



    public function getById($maHeo)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM heo WHERE MaHeo = ?");
        $stmt->execute([$maHeo]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByGioiTinh($gioiTinh = 'D')
    {
        $stmt = $this->pdo->prepare("SELECT MaHeo, GiongHeo FROM heo WHERE GioiTinh = ? ORDER BY MaHeo");
        $stmt->execute([$gioiTinh]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // mã heo bắt đàu từ số 
    public function generateAutoMaHeo($gioiTinh = null)
    {
        try {
            // Lấy mã heo có giá trị số lớn nhất
            // CAST(MaHeo AS UNSIGNED) giúp c2huyển chuỗi '30' thành số 30 để so sánh chính xác
            $sql = "SELECT MaHeo FROM heo ORDER BY CAST(MaHeo AS UNSIGNED) DESC LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $lastMa = $stmt->fetchColumn();

            if ($lastMa && is_numeric($lastMa)) {
                // Nếu tìm thấy mã là số (ví dụ: 30), cộng thêm 1 thành 31
                return (int)$lastMa + 1;
            } else {
                // Nếu chưa có dữ liệu hoặc mã cũ là chữ, bắt đầu từ 1
                return 1;
            }
        } catch (PDOException $e) {
            return 1;
        }
    }


    public function create($data)
    {
        try {
            // === TỰ ĐỘNG TẠO CHUỒNG NẾU CHƯA TỒN TẠI ===
            if (!empty($data['ViTriChuong'])) {
                $maChuong = strtoupper(trim($data['ViTriChuong']));

                $check = $this->pdo->prepare("SELECT MaChuong FROM chuongheo WHERE MaChuong = ?");
                $check->execute([$maChuong]);

                if ($check->rowCount() == 0) {
                    $this->pdo->prepare("INSERT INTO chuongheo (MaChuong, TenChuong, SucChua) VALUES (?, ?, 20)")
                        ->execute([$maChuong, 'Chuồng ' . $maChuong]);
                }
                $data['ViTriChuong'] = $maChuong;
            } else {
                $data['ViTriChuong'] = null; // Cho phép để trống
            }

            // === INSERT HEO – ĐÃ SỬA LỖI CHÍNH TẢ ===
            $sql = "INSERT INTO heo (
                    MaHeo, GiongHeo, GioiTinh, NgaySinh, GiaVon, CanNangHienTai,
                    ViTriChuong, TrangThaiHeo, NguonGoc, GhiChu, MaBo, MaMe, NgayTao
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([
                $data['MaHeo'],
                $data['GiongHeo'],
                $data['GioiTinh'],
                $data['NgaySinh'],
                $data['GiaVon'],
                $data['CanNangHienTai'] ?? null,
                $data['ViTriChuong'],
                $data['TrangThaiHeo'] ?? 'Bình thường',
                $data['NguonGoc'] ?? null,
                $data['GhiChu'] ?? null,
                $data['MaBo'] ?? null,
                $data['MaMe'] ?? null
            ]);

            return $result;
        } catch (PDOException $e) {
            return "Lỗi thêm heo: " . $e->getMessage();
        }
    }

    public function update($data)
    {
        try {
            // === BỔ SUNG: TỰ ĐỘNG TẠO CHUỒNG NẾU CHƯA TỒN TẠI (GIỐNG CREATE) ===
            if (!empty($data['ViTriChuong'])) {
                $maChuong = strtoupper(trim($data['ViTriChuong']));

                $check = $this->pdo->prepare("SELECT MaChuong FROM chuongheo WHERE MaChuong = ?");
                $check->execute([$maChuong]);

                if ($check->rowCount() == 0) {
                    // Tự động INSERT Chuồng mới
                    $this->pdo->prepare("INSERT INTO chuongheo (MaChuong, TenChuong, SucChua) VALUES (?, ?, 20)")
                        ->execute([$maChuong, 'Chuồng ' . $maChuong]);
                }
                $data['ViTriChuong'] = $maChuong; // Chuẩn hóa lại giá trị
            } else {
                $data['ViTriChuong'] = null; // Cho phép để trống
            }
            // ====================================================================

            $sql = "UPDATE heo SET 
                        GiongHeo = ?, GioiTinh = ?, NgaySinh = ?, GiaVon = ?, CanNangHienTai = ?,
                        ViTriChuong = ?, TrangThaiHeo = ?, NguonGoc = ?, GhiChu = ?,
                        MaBo = ?, MaMe = ?
                    WHERE MaHeo = ?";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $data['GiongHeo'],
                $data['GioiTinh'],
                $data['NgaySinh'],
                $data['GiaVon'],
                $data['CanNangHienTai'],
                $data['ViTriChuong'],  // Giá trị đã được kiểm tra/tạo ở trên
                $data['TrangThaiHeo'],
                $data['NguonGoc'],
                $data['GhiChu'],
                $data['MaBo'],
                $data['MaMe'],
                $data['MaHeo']
            ]);
            return true;
        } catch (PDOException $e) {
            // Lỗi sẽ chỉ xảy ra nếu có lỗi DB khác hoặc logic khóa ngoại phức tạp hơn
            return "Lỗi cập nhật: " . $e->getMessage();
        }
    }
    public function delete($maHeo)
    {
        try {
            // Kiểm tra xem heo này có phải là bố/mẹ của heo nào khác không
            $check = $this->pdo->prepare("SELECT COUNT(*) FROM heo WHERE MaBo = ? OR MaMe = ?");
            $check->execute([$maHeo, $maHeo]);

            if ($check->fetchColumn() > 0) {
                return "Không thể xóa vì con heo này đang là bố hoặc mẹ của một (hoặc nhiều) con heo khác.";
            }

            // Thực hiện xóa
            $stmt = $this->pdo->prepare("DELETE FROM heo WHERE MaHeo = ?");
            $stmt->execute([$maHeo]);

            if ($stmt->rowCount() === 0) {
                return "Không tìm thấy con heo với mã <strong>$maHeo</strong> để xóa.";
            }

            // Xóa thành công
            return true;
        } catch (Exception $e) {
            return "Lỗi hệ thống khi xóa heo: " . $e->getMessage();
        }
    }
}
