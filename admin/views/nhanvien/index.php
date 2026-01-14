<?php $title = "Quản Lý Nhân Viên"; ?>
<?php include __DIR__ . '/../layouts/header.php'; ?>
<?php include __DIR__ . '/../layouts/sidebar.php'; ?>

<!-- Style ẩn scrollbar ngang nhưng vẫn cuộn được -->
<style>
    .overflow-x-auto {
        -ms-overflow-style: none;
        /* IE và Edge */
        scrollbar-width: none;
        /* Firefox */
    }

    .overflow-x-auto::-webkit-scrollbar {
        display: none;
        /* Chrome, Safari, Opera */
    }
</style>

<div class="ml-64 p-4 sm:p-6 lg:p-8 min-h-screen bg-gray-50 transition-all duration-300">
    <!-- Phần tiêu đề + nút thêm -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 lg:mb-8">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Quản Lý Nhân Viên</h1>
            <p class="text-gray-600 text-sm sm:text-base">Heo Rừng Lai An Nông</p>
        </div>
        <a href="index.php?url=nhanvien/add"
            class="bg-gradient-to-r from-green-600 to-emerald-700 hover:from-green-700 hover:to-emerald-800 
                  text-white font-semibold px-5 py-3 sm:px-6 sm:py-4 rounded-xl shadow-lg 
                  flex items-center gap-2 sm:gap-3 transform hover:scale-105 transition text-sm sm:text-base whitespace-nowrap">
            <i class="fa-solid fa-plus"></i>
            Tuyển dụng mới
        </a>
    </div>

    <!-- Cards thống kê -->
    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6 lg:mb-8">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-xl">
            <div class="text-2xl sm:text-4xl font-bold"><?= $tongNhanVien ?? 0 ?></div>
            <div class="text-blue-100 text-xs sm:text-sm mt-1">Tổng nhân viên</div>
        </div>
        <div class="bg-gradient-to-br from-green-500 to-emerald-600 text-white rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-xl">
            <div class="text-2xl sm:text-4xl font-bold"><?= $nhanVienChinhThuc ?? 0 ?></div>
            <div class="text-green-100 text-xs sm:text-sm mt-1">Nhân viên chính thức</div>
        </div>
        <div class="bg-gradient-to-br from-orange-500 to-amber-600 text-white rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-xl">
            <div class="text-2xl sm:text-4xl font-bold"><?= $nhanVienThuViec ?? 0 ?></div>
            <div class="text-orange-100 text-xs sm:text-sm mt-1">Nhân viên thử việc</div>
        </div>
        <div class="bg-gradient-to-br from-purple-500 to-pink-600 text-white rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-xl <?= ($nghiViecHomNay ?? 0) > 0 ? 'animate-pulse' : '' ?>">
            <div class="text-2xl sm:text-4xl font-bold"><?= $nghiViecHomNay ?? 0 ?></div>
            <div class="text-purple-100 text-xs sm:text-sm mt-1">Nghỉ việc hôm nay</div>
        </div>
    </div>

    <!-- Cảnh báo nghỉ việc hôm nay -->
    <?php if (!empty($listNghiViecHomNay)): ?>
        <div class="bg-white rounded-2xl lg:rounded-3xl shadow-xl border border-red-200 overflow-hidden mb-6 lg:mb-8">
            <div class="bg-gradient-to-r from-red-600 to-rose-700 text-white p-4 sm:p-5">
                <h3 class="text-lg sm:text-xl font-bold flex items-center gap-3">
                    <i class="fa-solid fa-bell animate-pulse"></i>
                    Nhân viên nghỉ việc hôm nay – Cần bàn giao
                </h3>
            </div>
            <div class="p-4 sm:p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
                    <?php foreach ($listNghiViecHomNay as $nv): ?>
                        <div class="bg-red-50 border-2 border-red-300 rounded-xl p-4 sm:p-6 text-center shadow hover:scale-105 transition">
                            <div class="text-xl sm:text-2xl font-bold text-red-700"><?= htmlspecialchars($nv['HoTen'] ?? '') ?></div>
                            <div class="text-sm text-gray-600 mt-2">
                                <?= htmlspecialchars($nv['ViTri'] ?? '—') ?> • <?= htmlspecialchars($nv['TenBoPhan'] ?? 'Chưa có') ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Bảng danh sách nhân viên -->
    <div class="bg-white rounded-2xl lg:rounded-3xl shadow-xl overflow-hidden border border-gray-200">
        <div class="p-4 sm:p-6 border-b border-gray-200">
            <h3 class="text-xl sm:text-2xl font-bold text-gray-800">Danh sách nhân viên</h3>
        </div>

        <div class="overflow-x-auto scrollbar-hide">
            <table class="w-full min-w-[1000px] text-sm sm:text-base divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-emerald-600 to-teal-700 text-white">
                    <tr>
                        <th class="px-4 py-3 sm:px-6 sm:py-4 text-center whitespace-nowrap">STT</th>
                        <th class="px-4 py-3 sm:px-6 sm:py-4 text-center whitespace-nowrap">Mã NV</th>
                        <th class="px-4 py-3 sm:px-6 sm:py-4 text-left whitespace-nowrap">Họ tên</th>
                        <th class="px-4 py-3 sm:px-6 sm:py-4 text-left whitespace-nowrap">SĐT</th>
                        <th class="px-4 py-3 sm:px-6 sm:py-4 text-left whitespace-nowrap">Chức vụ</th>
                        <th class="px-4 py-3 sm:px-6 sm:py-4 text-left whitespace-nowrap">Bộ phận</th>
                        <th class="px-4 py-3 sm:px-6 sm:py-4 text-center whitespace-nowrap">Ngày vào</th>
                        <th class="px-4 py-3 sm:px-6 sm:py-4 text-center whitespace-nowrap">Lương CB</th>
                        <th class="px-4 py-3 sm:px-6 sm:py-4 text-center whitespace-nowrap">Trạng thái</th>
                        <th class="px-4 py-3 sm:px-6 sm:py-4 text-center whitespace-nowrap">Hành động</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    <?php if (empty($dsNhanVien)): ?>
                        <tr>
                            <td colspan="10" class="py-10 sm:py-16 text-center text-gray-500">
                                <i class="fa-solid fa-users-slash text-5xl sm:text-6xl mb-4 block text-gray-300"></i>
                                Chưa có nhân viên nào được thêm vào hệ thống.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $stt = 1;
                        foreach ($dsNhanVien as $nv): ?>
                            <?php
                            $statusClass = match (trim($nv['TrangThai'] ?? 'Thử việc')) {
                                'Chính thức' => 'emerald',
                                'Thử việc'   => 'yellow',
                                default      => 'red'
                            };
                            $luongCB = !empty($nv['LuongCoBan']) ? number_format($nv['LuongCoBan'], 0, ',', '.') . ' VNĐ' : '—';
                            ?>
                            <tr class="hover:bg-emerald-50 transition-colors">
                                <td class="px-4 py-4 sm:px-6 sm:py-5 text-center text-gray-700 font-medium"><?= $stt++ ?></td>
                                <td class="px-4 py-4 sm:px-6 sm:py-5 text-center">
                                    <span class="inline-block px-3 py-1.5 bg-emerald-100 text-emerald-800 font-bold rounded-full text-xs sm:text-sm">
                                        <?= (int)$nv['MaNV'] ?> <!-- Chỉ số thuần, không #, không 0 padding -->
                                    </span>
                                </td>
                                <td class="px-4 py-4 sm:px-6 sm:py-5 font-medium text-emerald-700">
                                    <?= htmlspecialchars($nv['HoTen'] ?? '') ?>
                                </td>
                                <!-- Các cột còn lại giữ nguyên -->
                                <td class="px-4 py-4 sm:px-6 sm:py-5"><?= htmlspecialchars($nv['SDT'] ?? '—') ?></td>
                                <td class="px-4 py-4 sm:px-6 sm:py-5"><?= htmlspecialchars($nv['ViTri'] ?? '—') ?></td>
                                <td class="px-4 py-4 sm:px-6 sm:py-5 text-gray-700">
                                    <?= htmlspecialchars($nv['TenBoPhan'] ?? 'Chưa có') ?>
                                </td>
                                <td class="px-4 py-4 sm:px-6 sm:py-5 text-center">
                                    <?= $nv['NgayVaoLam'] ? date('d/m/Y', strtotime($nv['NgayVaoLam'])) : '—' ?>
                                </td>
                                <td class="px-4 py-4 sm:px-6 sm:py-5 text-center font-medium text-gray-800">
                                    <?= $luongCB ?>
                                </td>
                                <td class="px-4 py-4 sm:px-6 sm:py-5 text-center">
                                    <span class="px-3 py-1.5 sm:px-4 sm:py-2 rounded-full text-xs font-bold
                                <?= $statusClass === 'emerald' ? 'bg-emerald-100 text-emerald-700' : '' ?>
                                <?= $statusClass === 'yellow' ? 'bg-yellow-100 text-yellow-700' : '' ?>
                                <?= $statusClass === 'red' ? 'bg-red-100 text-red-700' : '' ?>">
                                        <?= htmlspecialchars($nv['TrangThai'] ?? 'Thử việc') ?>
                                    </span>
                                </td>
                                <td class="px-4 py-4 sm:px-6 sm:py-5 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-2 sm:gap-3">
                                        <a href="index.php?url=nhanvien/edit&id=<?= $nv['MaNV'] ?>"
                                            title="Sửa thông tin"
                                            class="text-blue-600 hover:text-blue-800 transition-transform hover:scale-110 p-1 rounded-full hover:bg-blue-50">
                                            <i class="fa-solid fa-pen-to-square text-base sm:text-lg"></i>
                                        </a>

                                        <?php if (trim($nv['TrangThai'] ?? '') !== 'Nghỉ việc'): ?>
                                            <a href="index.php?url=nhanvien/banGiao&id=<?= $nv['MaNV'] ?>"
                                                title="Bàn giao / Nghỉ việc"
                                                class="text-orange-600 hover:text-orange-800 transition-transform hover:scale-110 p-1 rounded-full hover:bg-orange-50">
                                                <i class="fa-solid fa-hand-paper text-base sm:text-lg"></i>
                                            </a>
                                        <?php endif; ?>

                                        <a href="index.php?url=nhanvien/delete&id=<?= $nv['MaNV'] ?>"
                                            title="Xóa nhân viên"
                                            onclick="return confirm('XÓA VĨNH VIỄN nhân viên này?\nHành động KHÔNG THỂ HOÀN TÁC!')"
                                            class="text-red-600 hover:text-red-800 transition-transform hover:scale-110 p-1 rounded-full hover:bg-red-50">
                                            <i class="fa-solid fa-trash-can text-base sm:text-lg"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <!-- PHÂN TRANG (giữ nguyên) -->
        <?php if (isset($totalPages) && $totalPages > 1): ?>
            <div class="p-4 sm:p-6 border-t border-gray-200 flex justify-center">
                <nav class="flex gap-1 sm:gap-2" aria-label="Pagination">
                    <a href="index.php?url=nhanvien&page=<?= max(1, $page - 1) ?>"
                        class="px-4 py-3 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium transition <?= $page <= 1 ? 'opacity-50 cursor-not-allowed pointer-events-none' : '' ?>">
                        Trước
                    </a>

                    <?php
                    $start = max(1, $page - 2);
                    $end = min($totalPages, $page + 2);
                    for ($i = $start; $i <= $end; $i++):
                    ?>
                        <a href="index.php?url=nhanvien&page=<?= $i ?>"
                            class="px-4 py-3 rounded-lg font-medium transition <?= $i == $page ? 'bg-emerald-600 text-white shadow-lg' : 'bg-gray-100 hover:bg-gray-200 text-gray-700' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>

                    <a href="index.php?url=nhanvien&page=<?= min($totalPages, $page + 1) ?>"
                        class="px-4 py-3 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium transition <?= $page >= $totalPages ? 'opacity-50 cursor-not-allowed pointer-events-none' : '' ?>">
                        Sau
                    </a>
                </nav>
            </div>
            <div class="text-center mt-4 text-gray-600 text-sm pb-4">
                Hiển thị <?= count($dsNhanVien) ?> / <?= $total ?? 0 ?> nhân viên (Trang <?= $page ?> / <?= $totalPages ?>)
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>