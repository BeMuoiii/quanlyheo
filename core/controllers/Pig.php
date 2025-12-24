<?php
// File: core/controllers/PigController.php

require_once __DIR__ . '/../models/Pig.php';

class PigController {

    private $pig;

    public function __construct() {
        $this->pig = new Pig();
        session_start();
    }

    // Hiển thị form tạo mới
    public function create() {
        $parentsList = $this->pig->getAllPigsForDropdown();

        include __DIR__ . '/../../../admin/views/pig/create.php';
    }

    // Lưu dữ liệu
     public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Gom dữ liệu gửi lên từ form
        $data = [
            'MaHeo'       => trim($_POST['ma_heo']),
            'GiongHeo'    => $_POST['giong_heo'] ?? 'Lai',
            'NgaySinh'    => $_POST['ngay_sinh'],
            'GioiTinh'    => $_POST['gioi_tinh'],
            'MaBo'        => !empty($_POST['ma_bo']) ? $_POST['ma_bo'] : NULL,
            'MaMe'        => !empty($_POST['ma_me']) ? $_POST['ma_me'] : NULL,
            'ViTriChuong' => $_POST['vi_tri_chuong'],
            'MaNVTao'     => $_SESSION['MaNV1'] ?? 1
        ];

        // --- SỬA ĐOẠN NÀY ---
        // Gọi Model để thêm
        if ($this->pig->addPig($data)) {
            header('Location: index.php?module=pig&action=index&status=success');
            exit();
        } else {
            $error_message = "Lỗi khi thêm heo vào CSDL. Có thể Mã Heo bị trùng.";

            // Lấy lại danh sách heo để fill vào dropdown
            $parentsList = $this->pig->getAllPigsForDropdown();

            include __DIR__ . '/../../admin/views/pig/create.php';
        }
    }
}

}
