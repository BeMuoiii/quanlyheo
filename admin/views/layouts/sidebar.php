<aside class="w-64 bg-gradient-to-b from-emerald-800 to-emerald-900 text-white min-h-screen fixed top-0 left-0 overflow-y-auto shadow-2xl">
    <div class="p-6 text-center border-b border-emerald-700">
        <h2 class="text-2xl font-bold tracking-wider">Heo Rừng Lai</h2>
        <p class="text-emerald-200 text-sm mt-1">Quản Lý Trang Trại</p>
    </div>

    <?php
    // Lấy url hiện tại để highlight menu đúng
    $current_url = $_GET['url'] ?? 'dashboard';
    // Chuẩn hóa: nếu có / thì chỉ lấy phần trước dấu /
    $current_page = explode('/', $current_url)[0];
    ?>

    <nav class="mt-6">
        <!-- Dashboard -->
        <a href="index.php?url=dashboard"
            class="flex items-center px-6 py-4 hover:bg-emerald-700 transition <?= $current_page === 'dashboard' ? 'bg-emerald-700 border-l-4 border-white' : '' ?>">
            <i class="fas fa-home mr-3 text-lg"></i>
            <span class="font-medium">Dashboard</span>
        </a>

        <!-- Quản lý heo -->
        <a href="index.php?url=heo"
            class="flex items-center px-6 py-4 hover:bg-emerald-700 transition <?= in_array($current_page, ['heo']) ? 'bg-emerald-700 border-l-4 border-white' : '' ?>">
            <i class="fas fa-piggy-bank mr-3 text-lg"></i>
            <span class="font-medium">Quản Lý Heo</span>
        </a>

        <!-- Cân nặng -->
        <a href="index.php?url=cannang"
            class="flex items-center px-6 py-4 hover:bg-emerald-700 transition <?= in_array($current_page, ['cannang']) ? 'bg-emerald-700 border-l-4 border-white' : '' ?>">
            <i class="fas fa-weight-hanging mr-3 text-lg"></i>
            <span class="font-medium">Cân Nặng</span>
        </a>

        <!-- Sinh sản -->
        <a href="index.php?url=sinhsan"
            class="flex items-center px-6 py-4 hover:bg-emerald-700 transition <?= in_array($current_page, ['sinhsan', 'sinhsan']) ? 'bg-emerald-700 border-l-4 border-white' : '' ?>">
            <i class="fas fa-baby mr-3 text-lg"></i>
            <span class="font-medium">Sinh Sản</span>
        </a>

        <!-- Khách hàng - ĐÃ SỬA ĐÚNG LINK + ICON -->
        <a href="index.php?url=khachhang"
            class="flex items-center px-6 py-4 hover:bg-emerald-700 transition <?= in_array($current_page, ['khachhang']) ? 'bg-emerald-700 border-l-4 border-white' : '' ?>">
            <i class="fas fa-users mr-3 text-lg"></i>
            <span class="font-medium">Quản Lý Khách Hàng</span>
        </a>
        <a href="index.php?url=nhanvien"
            class="flex items-center px-6 py-4 hover:bg-emerald-700 transition <?= in_array($current_page, ['nhanvien', 'nhanvien/add', 'nhanvien/edit']) ? 'bg-emerald-700 border-l-4 border-white' : '' ?>">
            <i class="fas fa-user-tie mr-3 text-lg"></i>
            <span class="font-medium">Quản Lý Nhân Viên</span>
        </a>

        <!-- Quản lý Xuất chuồng heo -->
        <a href="index.php?url=xuatchuong"
            class="flex items-center px-6 py-4 hover:bg-emerald-700 transition <?= in_array($current_page, ['xuatchuong', 'xuatchuong/add', 'xuatchuong/edit', 'xuatchuong/delete']) ? 'bg-emerald-700 border-l-4 border-white' : '' ?>">
            <i class="fas fa-truck-loading mr-3 text-lg"></i>
            <span class="font-medium">Xuất Chuồng Heo</span>
        </a>

        <!-- Báo Cáo Tài Chính -->
        <a href="index.php?url=baocaotaichinh"
            class="flex items-center px-6 py-4 hover:bg-emerald-700 transition <?= in_array($current_page, ['baocaotaichinh', 'baocaotaichinh/doanhthu', 'baocaotaichinh/loinhuan', 'baocaotaichinh/index']) ? 'bg-emerald-700 border-l-4 border-white' : ''?>">
            <i class="fas fa-chart-line mr-3 text-lg"></i>
            <span class="font-medium">Báo Cáo Tài Chính</span>
        </a>
        <!-- Thêm menu khác nếu cần sau này -->
        <!-- 
        <a href="index.php?url=nhanvien" class="flex items-center px-6 py-4 hover:bg-emerald-700 transition">
            <i class="fas fa-user-tie mr-3 text-lg"></i> Nhân Viên
        </a>
        -->
    </nav>

    <!-- Phần footer nhỏ xinh -->

</aside>