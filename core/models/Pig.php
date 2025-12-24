<?php
// File: core/models/Pig.php

// Tên Class phải là Pig (loại bỏ Model)
require_once __DIR__ . '/../config/database.php';

class Pig {
    private $conn;

    public function __construct() {
        // Hàm kết nối CSDL
        $this->conn = connectDB(); 
    }

    // Hàm lấy tất cả heo (giữ nguyên logic của bạn)
    public function getAllPigs() {
        $sql = "SELECT h.*, nv.HoTen AS TenNVTao 
                FROM heo h
                LEFT JOIN nhanvien nv ON h.MaNVTao = nv.MaNV
                ORDER BY h.NgaySinh DESC";
        
        $result = $this->conn->query($sql);
        
        $data = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }

    // THÊM HÀM getAllPigsForDropdown() để phục vụ Form Tạo Mới
    public function getAllPigsForDropdown() {
        // Lấy tất cả heo Đực (D) và Cái (C) đang còn sống
        $sql = "SELECT MaHeo, GioiTinh, GiongHeo FROM heo WHERE TrangThaiHeo = 'ConSong'";
        
        $result = $this->conn->query($sql);
        
        $data = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }

    // Hàm thêm heo mới (CRUD: Create) (Giữ nguyên logic của bạn)
    public function addPig($data) {
        $stmt = $this->conn->prepare("INSERT INTO heo (MaHeo, GiongHeo, NgaySinh, GioiTinh, MaBo, MaMe, ViTriChuong, MaNVTao) 
                                      VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        
        // s: string, i: integer
        $stmt->bind_param("sssssssi", 
            $data['MaHeo'], 
            $data['GiongHeo'], 
            $data['NgaySinh'], 
            $data['GioiTinh'],
            $data['MaBo'],
            $data['MaMe'],
            $data['ViTriChuong'],
            $data['MaNVTao']
        );
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function __destruct() {
        $this->conn->close();
    }
}
?>