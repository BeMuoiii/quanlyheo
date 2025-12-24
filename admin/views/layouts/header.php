<?php 
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
// XÓA BỎ TOÀN BỘ ĐOẠN HIỂN THỊ SUCCESS/ERROR Ở ĐÂY
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Heo Rừng Lái - Quản Lý Trang Trại' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <header class="bg-white shadow-lg fixed top-0 left-0 right-0 z-50 h-16">
        <div class="px-6 flex items-center justify-between h-full">
            <div class="flex items-center">
                <h1 class="text-2xl font-bold text-emerald-700">Heo Rừng Lái</h1>
                <p class="ml-3 text-gray-600 text-sm">Quản Lý Trang Trại</p>
            </div>
            <div class="flex items-center gap-4">
                <span class="flex items-center gap-2 text-emerald-600">
                    <i class="fas fa-user-circle"></i>
                    Xin chào <strong>admin</strong>
                </span>
                <a href="index.php?url=logout" class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition">
                    Đăng xuất
                </a>
            </div>
        </div>
    </header>

    <div class="pt-16">