<?php
// File: admin/controllers/Auth.php

// Nếu session chưa bật thì bật lên
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Xử lý Logout (Đăng xuất)
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_destroy();
    header("Location: index.php?module=auth&action=login");
    exit;
}

// 2. Nạp file Database & Model
// LƯU Ý: Phải lùi 2 cấp (../../) mới ra được thư mục gốc để vào core
$dbPath = __DIR__ . '/../../core/config/database.php';
$modelPath = __DIR__ . '/../../core/models/UserModel.php';

if (!file_exists($dbPath)) die("Lỗi: Không tìm thấy database.php tại $dbPath");
if (!file_exists($modelPath)) die("Lỗi: Không tìm thấy UserModel.php tại $modelPath");

require_once $dbPath;
require_once $modelPath;

// 3. Nhận dữ liệu
$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// 4. Nếu chưa nhập gì -> Hiển thị form Login
if (empty($email) && empty($password)) {
    // Lùi 2 cấp để tìm về thư mục views
    require_once __DIR__ . '/../../admin/views/auth/login.php'; 
    // Nếu thư mục views của bạn nằm trực tiếp trong admin thì dùng:
    // require_once __DIR__ . '/../views/auth/login.php';
    exit;
}

// 5. Xử lý Đăng Nhập khi có dữ liệu gửi lên
$userModel = new UserModel();
$user = $userModel->findUserByEmail($email);

$loginSuccess = false;

if ($user) {
    // Check pass mã hóa hoặc pass thường
    if (password_verify($password, $user['PasswordHash'])) {
        $loginSuccess = true;
    } elseif ($password === $user['PasswordHash']) {
        $loginSuccess = true;
    }
}

if (!$loginSuccess) {
    $error_message = "Email hoặc mật khẩu không đúng.";
    // Load lại form với thông báo lỗi
    require_once __DIR__ . '/../../admin/views/auth/login.php';
    exit;
}

// 6. Đăng nhập thành công -> Lưu Session
$_SESSION['user_id']   = $user['UserID'];
$_SESSION['email']     = $user['Email'];
$_SESSION['role_id']   = $user['RoleID'];
$_SESSION['hoten']     = $user['HoTen'] ?? 'Admin';

// Chống tấn công session fixation
session_regenerate_id(true);

// 7. Chuyển hướng về Dashboard
$scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
$targetPath = ($scriptDir === '' || $scriptDir === '.')
    ? '/index.php?module=dashboard'
    : $scriptDir . '/index.php?module=dashboard';
header("Location: $targetPath");
exit;
?>