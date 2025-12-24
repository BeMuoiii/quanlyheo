<?php $title = "Quản Lý Heo Rừng"; ?>
<?php include __DIR__ . '/../layouts/header.php'; ?>
<?php include __DIR__ . '/../layouts/sidebar.php'; ?>
<script src="../../../public/js/./index.js"></script>


<div id="toast-container" class="fixed bottom-5 right-5 z-[9999] flex flex-col gap-3"></div>
<div class="ml-64 p-4 min-h-screen bg-gray-50">
    <!-- TÌM KIẾM + BỘ LỌC SIÊU ĐẸP – ĐÃ ĐỔI THÀNH "NHỎ NHẤT" / "LỚN NHẤT" -->
    <div class="bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden mb-8">
        <div class="p-3 bg-gradient-to-br from-emerald-50 to-teal-50">
            <form method="get" class="grid grid-cols-1 lg:grid-cols-11 gap-5 items-end">
                <input type="hidden" name="url" value="heo">

                <!-- Ô TÌM KIẾM ĐẸP -->
                <div class="lg:col-span-3">
                    <div class="relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-5 top-1/2 -translate-y-1/2 text-emerald-600 text-lg"></i>
                        <input
                            type="text"
                            name="timkiem"
                            value="<?= htmlspecialchars($keyword ?? '') ?>"
                            placeholder="Nhập mã heo, giống, chuồng, ghi chú..."
                            class="w-full pl-14 pr-4 py-2 rounded-2xl border-2 border-gray-200 
                               focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-100 
                               text-base font-medium transition-all duration-300 shadow-sm"
                            autocomplete="off">
                    </div>
                </div>

                <!-- BỘ LỌC SẮP XẾP -->

                <div class="lg:col-span-7 flex flex-wrap items-center gap-3 justify-end">
                    <span class="text-gray-700 font-semibold hidden sm:block">Sắp xếp theo:</span>

                    <select name="sort"
                        class="px-1 py-2 rounded-xl border border-gray-300 font-medium 
               focus:outline-none focus:ring-4 focus:ring-emerald-100 focus:border-emerald-500 transition">
                        <option value="MaHeo" <?= ($sort ?? 'MaHeo') == 'MaHeo' ? 'selected' : '' ?>>Mã heo</option>
                        <option value="NgaySinh" <?= ($sort ?? '') == 'NgaySinh' ? 'selected' : '' ?>>Ngày sinh</option>
                        <option value="CanNangHienTai" <?= ($sort ?? '') == 'CanNangHienTai' ? 'selected' : '' ?>>Cân nặng</option>
                        <option value="ViTriChuong" <?= ($sort ?? '') == 'ViTriChuong' ? 'selected' : '' ?>>Chuồng</option>
                    </select>

                    <select name="order"
                        class="px-1 py-2 rounded-xl border border-gray-300 font-medium 
               focus:outline-none focus:ring-4 focus:ring-emerald-100 focus:border-emerald-500 transition">
                        <option value="DESC" <?= ($order ?? 'DESC') == 'DESC' ? 'selected' : '' ?>>Lớn nhất trước</option>
                        <option value="ASC" <?= ($order ?? '') == 'ASC' ? 'selected' : '' ?>>Nhỏ nhất trước</option>
                    </select>

                    <button type="submit"
                        class="bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 
               text-white font-bold px-3 py-2 rounded-xl shadow-lg transform hover:scale-105 transition duration-300">
                        Áp dụng
                    </button>

                    <?php if (!empty($keyword) || ($sort ?? '') != 'MaHeo' || ($order ?? '') != 'DESC'): ?>
                        <a href="index.php?url=heo"
                            class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold px-3 py-2 rounded-xl transition duration-200">
                            Xóa lọc
                        </a>
                    <?php endif; ?>

                    <a href="index.php?url=heo/add"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 py-2 rounded-xl shadow-lg transform hover:scale-105 transition duration-300 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Thêm mới</span>
                    </a>
                </div>
            </form>

        </div>
    </div>
    <!-- Bảng danh sách -->
    <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table id="myTable" class="w-full">
                <thead class="bg-gradient-to-r from-emerald-600 to-emerald-700 text-white">
                    <tr>
                        <th class="px-5 py-4 text-center w-16 text-sm font-bold">STT</th>
                        <th class="px-5 py-4 text-left font-bold text-sm">Mã Heo</th>
                        <th class="px-5 py-4 text-left text-sm">Giống</th>
                        <th id="genderHeader" class="px-5 py-4 text-center text-xs relative">
                            <span class="flex items-center justify-center"> Giới Tính
                                <i
                                    class="fa-solid fa-filter text-gray-500 hover:text-blue-600 ml-1 cursor-pointer"
                                    id="genderFilterIcon"
                                    style="font-size: 0.75rem;">
                                </i>
                            </span>

                            <div
                                id="genderFilterMenu"
                                class="absolute top-full left-0 mt-2 w-40 bg-white border border-gray-200 rounded-lg shadow-xl p-3 z-20 hidden">

                                <p class="text-xs font-semibold text-gray-700 mb-2 border-b pb-1">Lọc theo Giới Tính</p>

                                <div class="space-y-2">
                                    <label class="flex items-center text-sm text-gray-800">
                                        <input type="radio" name="genderFilter" value="all" checked class="form-radio text-blue-600">
                                        <span class="ml-2">Tất cả</span>
                                    </label>

                                    <label class="flex items-center text-sm text-gray-800">
                                        <input type="radio" name="genderFilter" value="Đực" class="form-radio text-blue-600">
                                        <span class="ml-2">Đực</span>
                                    </label>

                                    <label class="flex items-center text-sm text-gray-800">
                                        <input type="radio" name="genderFilter" value="Cái" class="form-radio text-blue-600">
                                        <span class="ml-2">Cái</span>
                                    </label>
                                </div>

                                <button
                                    id="applyFilter"
                                    class="mt-3 w-full bg-blue-500 hover:bg-blue-600 text-white text-sm py-1.5 rounded transition duration-150">
                                    Áp dụng
                                </button>
                            </div>
                        </th>
                        <th class="px-5 py-4 text-right text-sm">Giá vốn</th>
                        <th class="px-5 py-4 text-left text-sm">Ngày Sinh</th>
                        <th class="px-5 py-4 text-right text-sm">Cân Nặng</th>
                        <th class="px-5 py-4 text-center text-sm">Chuồng</th>
                        <th class="px-5 py-4 text-center text-sm">Trạng Thái</th>
                        <th class="px-5 py-4 text-left text-sm">Bố / Mẹ</th>
                        <th class="px-5 py-4 text-center text-sm">Thao Tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (empty($dsHeo)): ?>
                        <tr>
                            <td colspan="11" class="text-center py-20">
                                <i class="fas fa-piggy-bank text-8xl text-gray-200 mb-5 block"></i>
                                <p class="text-xl text-gray-500">Chưa có heo nào trong trại</p>
                                <a href="index.php?url=heo/add" class="mt-4 inline-block bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-7 py-3 rounded-xl shadow transition">
                                    Thêm heo đầu tiên
                                </a>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $stt = 1; ?>
                        <?php foreach ($dsHeo as $h): ?>
                            <tr class="<?= $stt % 2 == 0 ? 'bg-gray-50/70' : 'bg-white' ?> hover:bg-emerald-50 transition">
                                <!-- 1. STT -->
                                <td class="px-5 py-5 text-center">
                                    <div class="w-9 h-9 mx-auto rounded-full bg-emerald-100 text-emerald-700 font-bold flex items-center justify-center text-sm shadow">
                                        <?= $stt++ ?>
                                    </div>
                                </td>

                                <!-- 2. Mã Heo -->
                                <td class="px-5 py-5 font-bold text-emerald-700 text-lg">
                                    <?= htmlspecialchars($h['MaHeo']) ?>
                                </td>

                                <!-- 3. Giống -->
                                <td class="px-5 py-5 text-gray-700 text-sm">
                                    <?= htmlspecialchars($h['GiongHeo'] ?? 'Heo rừng lai') ?>
                                </td>

                                <!-- 4. Giới Tính -->
                                <td class="px-5 py-5 text-center">
                                    <?php if ($h['GioiTinh'] == 'D'): ?>
                                        <span class="px-3 py-1.5 rounded-full bg-blue-600 text-white text-xs font-bold">Đực</span>
                                    <?php elseif ($h['GioiTinh'] == 'C'): ?>
                                        <span class="px-3 py-1.5 rounded-full bg-pink-600 text-white text-xs font-bold">Cái</span>
                                    <?php else: ?>
                                        <span class="text-gray-400 text-xs">-</span>
                                    <?php endif; ?>
                                </td>

                                <!-- 5. Giá vốn -->
                                <td class="px-5 py-5 text-right">
                                    <span class="text-md font-bold text-gray-700">
                                        <?= number_format($h['GiaVon'] ?? 0) ?> đ
                                    </span>
                                </td>

                                <!-- 6. Ngày Sinh -->
                                <td class="px-5 py-5 text-gray-700 text-sm">
                                    <?= $h['NgaySinh'] ? date('d/m/Y', strtotime($h['NgaySinh'])) : '-' ?>
                                </td>

                                <!-- 7. Cân Nặng -->
                                <td class="px-5 py-5 text-right">
                                    <span class="text-xl font-bold text-emerald-600">
                                        <?= number_format($h['CanNangHienTai'] ?? 0, 1) ?> kg
                                    </span>
                                </td>

                                <!-- 8. Chuồng -->
                                <td class="px-5 py-5 text-center">
                                    <span class="px-4 py-1.5 bg-gray-100 rounded-xl text-gray-700 font-medium text-sm">
                                        <?= htmlspecialchars($h['ViTriChuong'] ?? '-') ?>
                                    </span>
                                </td>

                                <!-- 9. Trạng Thái -->
                                <td class="px-5 py-5 text-center">
                                    <?php
                                    $status = $h['TrangThaiHeo'] ?? 'Bình thường';
                                    $bg = $status == 'Bình thường' ? 'emerald' : ($status == 'Theo dõi' ? 'yellow' : 'red');
                                    ?>
                                    <span class="px-4 py-1.5 rounded-full bg-<?= $bg ?>-600 text-white text-xs font-bold">
                                        <?= $status ?>
                                    </span>
                                </td>

                                <!-- 10. Bố / Mẹ -->
                                <td class="px-5 py-5 text-xs leading-relaxed text-center">
                                    <?php if ($h['MaHeoBo']): ?>
                                        <div class="text-blue-700 font-medium">Bố: <?= htmlspecialchars($h['MaHeoBo']) ?></div>
                                    <?php endif; ?>
                                    <?php if ($h['MaHeoMe']): ?>
                                        <div class="text-pink-700 font-medium">Mẹ: <?= htmlspecialchars($h['MaHeoMe']) ?></div>
                                    <?php endif; ?>
                                    <?php if (!$h['MaHeoBo'] && !$h['MaHeoMe']): ?>
                                        <span class="text-gray-400">Chưa rõ</span>
                                    <?php endif; ?>
                                </td>

                                <!-- 11. Thao Tác -->
                                <td class="px-5 py-5 text-center">
                                    <div class="flex gap-2 justify-center">
                                        <a href="index.php?url=heo/edit&id=<?= $h['MaHeo'] ?>"
                                            class="bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold px-5 py-2.5 rounded-lg shadow transition">
                                            Sửa
                                        </a>
                                        <a href="index.php?url=heo/delete&id=<?= $h['MaHeo'] ?>"
                                            onclick="return confirm('Xóa heo <?= htmlspecialchars($h['MaHeo']) ?> thật không?')"
                                            class="bg-red-500 hover:bg-red-600 text-white text-xs font-bold px-5 py-2.5 rounded-lg shadow transition">
                                            Xóa
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>

            </table>
        </div>
    </div>
    <!-- PHÂN TRANG SIÊU ĐẸP -->
    <?php if ($totalPages > 1): ?>
        <div class="mt-8 flex justify-center">
            <nav class="flex gap-1" aria-label="Pagination">
                <!-- Nút Previous -->
                <a href="?url=heo<?= $keyword ? '&timkiem=' . urlencode($keyword) : '' ?>&sort=<?= $sort ?>&order=<?= $order ?>&page=<?= max(1, $page - 1) ?>"
                    class="px-4 py-3 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium transition <?= $page <= 1 ? 'opacity-50 cursor-not-allowed' : '' ?>">
                    Trước
                </a>

                <!-- Các trang gần đó -->
                <?php
                $start = max(1, $page - 2);
                $end = min($totalPages, $page + 2);
                for ($i = $start; $i <= $end; $i++):
                ?>
                    <a href="?url=heo<?= $keyword ? '&timkiem=' . urlencode($keyword) : '' ?>&sort=<?= $sort ?>&order=<?= $order ?>&page=<?= $i ?>"
                        class="px-4 py-3 rounded-lg font-medium transition <?= $i == $page ? 'bg-emerald-600 text-white shadow-lg' : 'bg-gray-100 hover:bg-gray-200 text-gray-700' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <!-- Nút Next -->
                <a href="?url=heo<?= $keyword ? '&timkiem=' . urlencode($keyword) : '' ?>&sort=<?= $sort ?>&order=<?= $order ?>&page=<?= min($totalPages, $page + 1) ?>"
                    class="px-4 py-3 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium transition <?= $page >= $totalPages ? 'opacity-50 cursor-not-allowed' : '' ?>">
                    Sau
                </a>
            </nav>
        </div>
        <div class="text-center mt-4 text-gray-600 text-sm">
            Hiển thị <?= count($dsHeo) ?> / <?= $total ?> con heo (Trang <?= $page ?> / <?= $totalPages ?>)
        </div>
    <?php endif; ?>
    </tbody>
    </table>
</div>
</div>
</div>


<script>
    function capNhatDashboardChuan() {
        // 1. Lấy dữ liệu từ localStorage (nếu có)
        const dataH = localStorage.getItem('heoData');
        const heoData = JSON.parse(dataH || '[]');

        // 2. LẤY SỐ TỔNG TỪ PHP (Đây là con số chuẩn từ Database của bạn)
        // Chúng ta lấy biến $total từ PHP đổ vào đây
        const tongTuDatabase = <?= isset($total) ? $total : 0 ?>;
        const tongHienTai = heoData.length > 0 ? heoData.length : tongTuDatabase;

        // 3. Tính toán các thành phần khác
        let heoCon = 0, heoGia = 0;
        let heoNai = 0, heoDuc = 0;

        if (heoData.length > 0) {
            heoData.forEach(h => {
                const cn = parseFloat(h.canNang) || 0;
                if (cn <= 8) heoCon++;
                if (cn >= 35) heoGia++;
                
                const gt = (h.gioiTinh || '').toLowerCase();
                if (gt === 'cái') heoNai++;
                if (gt === 'đực') heoDuc++;
            });
        }

        // 4. Hàm cập nhật giao diện cực mạnh
        const updateText = (cardId, value) => {
            const card = document.querySelector(`[data-card="${cardId}"]`);
            if (card) {
                const p = card.querySelector('p.text-3xl');
                if (p) p.textContent = value.toLocaleString('vi-VN');
            }
        };

        // 5. Ghi đè toàn bộ chỉ số
        updateText('tong-dan', tongHienTai);
        updateText('heo-con', heoCon);
        updateText('heo-nai', heoNai);
        updateText('heo-duc', heoDuc);
        updateText('heo-gia', heoGia);
        
        console.log("Đã cập nhật tổng đàn: " + tongHienTai);
    }

    // Thực thi ngay
    capNhatDashboardChuan();

    // Thực thi lại khi trang tải xong
    window.addEventListener('load', capNhatDashboardChuan);

    // Lắng nghe các sự kiện thay đổi
    window.addEventListener('storage', capNhatDashboardChuan);
    window.addEventListener('heoDataChanged', capNhatDashboardChuan);

    // Kiểm tra liên tục mỗi 1 giây để ép số
    setInterval(capNhatDashboardChuan, 1000);
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>