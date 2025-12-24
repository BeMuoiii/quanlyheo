<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Heo Rừng Lai' ?> - Quản Lý Trang Trại</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen flex">

    <!-- Sidebar -->
    <?php include __DIR__ . '/sidebar.php'; ?> ?>

    <!-- Nội dung chính -->
    <div class="flex-1 ml-64">
        <!-- Header -->
        <?php include __DIR__ . '/header.php'; ?>

        <!-- Nội dung trang con -->
        <main class="p-8">
            <?= $content ?>
        </main>

        <!-- Footer -->
        <?php include __DIR__ . '/footer.php'; ?>
    </div>
</body>
</html>