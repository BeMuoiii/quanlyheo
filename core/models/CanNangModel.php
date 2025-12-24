<?php
class CanNangModel
{
    private $conn;
    private $table = 'cannang';

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // ⭐ HÀM HỖ TRỢ: Làm sạch dữ liệu đầu vào
    private function cleanInput($data)
    {
        $cleaned = [];
        foreach ($data as $key => $value) {
            // Áp dụng trim, strip_tags, và htmlspecialchars cho mọi trường
            $cleaned[$key] = trim(htmlspecialchars(strip_tags($value)));
        }
        return $cleaned;
    }

    public function create($data)
    {
        // ⭐ SỬA GỌN: Chỉ cần gọi hàm cleanInput()
        $cleanedData = $this->cleanInput($data);

        $query = 'INSERT INTO ' . $this->table . ' (MaHeo, NgayCan, CanNang, GhiChu) 
                  VALUES (:MaHeo, :NgayCan, :CanNang, :GhiChu)';

        $stmt = $this->conn->prepare($query);

        // Gán dữ liệu đã được làm sạch
        $stmt->bindParam(':MaHeo', $cleanedData['MaHeo']);
        $stmt->bindParam(':NgayCan', $cleanedData['NgayCan']);
        $stmt->bindParam(':CanNang', $cleanedData['CanNang']);
        $stmt->bindParam(':GhiChu', $cleanedData['GhiChu']);

        try {
            if ($stmt->execute()) {
                return true;
            }
        } catch (PDOException $e) {
            if ($e->getCode() == '23000' && strpos($e->getMessage(), '1452') !== false) {
                return "Mã Heo '{$cleanedData['MaHeo']}' không tồn tại trong hệ thống!";
            }
            error_log("Lỗi: " . $e->getMessage());
        }
        return false;
    }

    public function getAll()
    {
        $query = "SELECT * FROM " . $this->table . " ORDER BY NgayCan DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Thay hàm getById cũ bằng cái này:
    public function getByMaCan($maCan)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE MaCan = :maCan";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':maCan', $maCan);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Sửa hàm update:
    public function update($data)
    {
        $query = "UPDATE cannang SET 
                MaHeo = :MaHeo,
                NgayCan = :NgayCan,
                CanNang = :CanNang,
                GhiChu = :GhiChu
              WHERE MaCan = :MaCan";

        $stmt = $this->conn->prepare($query);

        // Chuyển ngày
        $ngayCan = DateTime::createFromFormat('d/m/Y', $data['NgayCan']);
        $ngayCanDB = $ngayCan ? $ngayCan->format('Y-m-d') : null;

        $stmt->bindParam(':MaHeo', $data['MaHeo']);
        $stmt->bindParam(':NgayCan', $ngayCanDB);
        $stmt->bindParam(':CanNang', $data['CanNang']);
        $stmt->bindParam(':GhiChu', $data['GhiChu']);
        $stmt->bindParam(':MaCan', $data['MaCan']);

        return $stmt->execute();
    }

    // Sửa hàm delete:
    public function delete($maCan)
    {
        $query = "DELETE FROM cannang WHERE MaCan = :maCan";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':maCan', $maCan);
        return $stmt->execute();
    }
}
