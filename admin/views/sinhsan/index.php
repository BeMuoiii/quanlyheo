<?php $title = "Quản Lý Sinh Sản"; ?>
<?php include __DIR__ . '/../layouts/header.php'; ?>
<?php include __DIR__ . '/../layouts/sidebar.php'; ?>

<?php
// Bảo vệ biến (nếu Controller quên truyền)
$sinhSanList   = $sinhSanList   ?? [];
$listSapDe     = $listSapDe     ?? [];
$tongNai       = $tongNai       ?? 0;
$tyLe          = $tyLe          ?? 0;
$avgCon        = $avgCon        ?? 0;
$sapDe         = $sapDe         ?? 0;
$tongPhoi      = $tongPhoi      ?? 0;
$tyLe = $tyLe ?? 0;
?>

<div class="ml-64 p-8 min-h-screen bg-gray-50">
    <!-- Phần còn lại của code giữ nguyên như mình đã sửa lần trước -->

    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Quản Lý Sinh Sản</h1>
            <p class="text-gray-600">Heo Rừng Lai An Nông</p>
        </div>
        <a href="index.php?url=sinhsan/add"
            class="bg-gradient-to-r from-green-600 to-emerald-700 hover:from-green-700 hover:to-emerald-800 
                  text-white font-bold px-6 py-4 rounded-xl shadow-lg flex items-center gap-3 transform hover:scale-105 transition">
            <i class="fa-solid fa-plus text-xl"></i>
            Ghi nhận phối giống mới
        </a>
    </div>

    <!-- 4 ô thống kê -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-gradient-to-br from-pink-500 to-rose-600 text-white rounded-2xl p-6 shadow-xl">
            <div class="text-4xl font-bold"><?= $tongNai ?? 0 ?></div>
            <div class="text-pink-100 text-sm mt-1">Tổng heo nái</div>
        </div>
        <div class="bg-gradient-to-br from-purple-500 to-indigo-600 text-white rounded-2xl p-6 shadow-xl">
            <div class="text-4xl font-bold"><?= $tyLe ?? 0 ?>%</div>
            <div class="text-purple-100 text-sm mt-1">Tỷ lệ phối thành công</div>
        </div>
        <div class="bg-gradient-to-br from-blue-500 to-cyan-600 text-white rounded-2xl p-6 shadow-xl">
            <div class="text-4xl font-bold"><?= $tongPhoi ?? 0 ?></div>
            <div class="text-blue-100 text-sm mt-1">Tổng heo đang phối </div>
        </div>
        <div class="bg-gradient-to-br from-orange-500 to-red-600 text-white rounded-2xl p-6 shadow-xl <?= ($sapDe ?? 0) > 0 ? 'animate-pulse' : '' ?>">
            <div class="text-4xl font-bold"><?= $sapDe ?? 0 ?></div>
            <div class="text-orange-100 text-sm mt-1">Nái sắp đẻ (7 ngày tới)</div>
        </div>
    </div>

    <!-- Danh sách nái sắp đẻ -->
    <div class="bg-white rounded-3xl shadow-2xl border <?= ($sapDe ?? 0) > 0 ? 'border-red-200' : 'border-gray-100' ?> overflow-hidden mb-8">
        <div class="bg-gradient-to-r from-red-600 to-rose-700 text-white p-5">
            <h3 class="text-xl font-bold flex items-center gap-3">
                <i class="fa-solid fa-bell <?= ($sapDe ?? 0) > 0 ? 'animate-pulse' : '' ?>"></i>
                Nái sắp đẻ (7 ngày tới) – Cần chuẩn bị ô đẻ
            </h3>
        </div>
        <div class="p-6">
            <?php if (empty($listSapDe)): ?>
                <p class="text-center py-12 text-gray-500 text-lg">
                    <i class="fa-solid fa-check-circle text-6xl text-green-500 block mb-4"></i>
                    Không có nái nào sắp đẻ trong 7 ngày tới
                </p>
            <?php else: ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <?php foreach ($listSapDe as $n):
                        $ngay = $n['ConLaiNgay'] ?? 0;
                        $color = $ngay <= 2 ? 'red' : ($ngay <= 5 ? 'orange' : 'yellow');
                        $bg = $ngay <= 2 ? 'bg-red-50 border-red-300' : ($ngay <= 5 ? 'bg-orange-50 border-orange-300' : 'bg-yellow-50 border-yellow-300');
                    ?>
                        <div class="<?= $bg ?> border-2 rounded-2xl p-6 text-center shadow hover:scale-105 transition">
                            <div class="text-3xl font-bold text-<?= $color ?>-700">#<?= htmlspecialchars($n['MaHeoNai'] ?? '') ?></div>
                            <div class="text-sm text-gray-600 mt-1">
                                Phối: <?= $n['NgayPhoi'] ? date('d/m/Y', strtotime($n['NgayPhoi'])) : '—' ?>
                            </div>
                            <div class="text-xs text-gray-500">Chuồng: <?= htmlspecialchars($n['ViTriChuong'] ?? '—') ?></div>
                            <div class="mt-4">
                                <span class="px-8 py-3 bg-<?= $color ?>-600 text-white font-bold rounded-full text-lg shadow">
                                    Còn <?= $ngay ?> ngày
                                </span>
                            </div>
                            <a href="index.php?url=sinhsan/ghiNhanDe&id=<?= $n['SinhSan'] ?>"
                                class="block mt-4 text-<?= $color ?>-700 font-bold hover:underline">
                                → Ghi nhận đẻ
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Bảng lịch sử phối & đẻ -->
    <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-2xl font-bold text-gray-800">Lịch sử phối giống & sinh đẻ</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gradient-to-r from-emerald-600 to-teal-700 text-white text-xs uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3 text-center">STT</th>
                        <th class="px-4 py-3 text-left">Mã SS / Nái</th>
                        <th class="px-4 py-3 text-left">Đực</th>
                        <th class="px-4 py-3 text-center">Ngày phối</th>
                        <th class="px-4 py-3 text-center">Dự sinh</th>
                        <th class="px-4 py-3 text-center text-blue-200">Ngày Đẻ Thực</th>
                        <th class="px-4 py-3 text-center">Con sống</th>
                        <th class="px-4 py-3 text-center">Trạng thái</th>
                        <th class="px-4 py-3 text-center">Hành động</th>
                    </tr>
                </thead>

                <tbody>
                    <?php $stt = 1; ?>
                    <?php foreach ($sinhSanList as $r): ?>
                        <tr class="hover:bg-emerald-50 transition border-b border-gray-100 text-sm">
                            <td class="px-4 py-3 text-center font-medium"><?= $stt++ ?></td>

                            <td class="px-4 py-3">
                                <div class="font-bold text-emerald-700 text-xs">#<?= htmlspecialchars($r['SinhSan'] ?? '') ?></div>
                                <div class="text-xs">
                                    <span class="font-semibold text-pink-600">Nái:</span>
                                    #<?= htmlspecialchars($r['MaHeoNai'] ?? '') ?>
                                </div>
                                <?php if (!empty($r['HoTen'] ?? null)): ?>
                                    <div class="text-[10px] text-gray-400 mt-0.5">
                                        <i class="fa-solid fa-user"></i> <?= htmlspecialchars($r['HoTen']) ?>
                                    </div>
                                <?php endif; ?>
                            </td>

                            <td class="px-4 py-3 text-xs text-center">
                                <?= !empty($r['MaHeoDuc'] ?? null) ? '#' . htmlspecialchars($r['MaHeoDuc']) : '<span class="text-gray-300">—</span>' ?>
                            </td>

                            <td class="px-4 py-3 text-center text-xs">
                                <?= !empty($r['NgayPhoi'] ?? null) ? date('d/m/Y', strtotime($r['NgayPhoi'])) : '—' ?>
                            </td>

                            <td class="px-4 py-3 text-center text-xs font-medium text-orange-600">
                                <?= !empty($r['NgayDuSinh']) ? date('d/m/Y', strtotime($r['NgayDuSinh'])) : '—' ?>
                            </td>

                            <td class="px-4 py-3 text-center text-xs font-bold text-blue-700 bg-blue-50/30">
    <?php if (!empty($r['NgayDe'])): ?>
        <?= date('d/m/Y', strtotime($r['NgayDe'])) ?>
    <?php else: ?>
        <span class="text-gray-300 italic">Chưa đẻ</span>
    <?php endif; ?>
</td>

                            <td class="px-4 py-3 text-center font-bold text-lg <?= $r['SoConSong'] !== null ? 'text-emerald-700' : 'text-gray-300' ?>">
                                <?= $r['SoConSong'] !== null ? $r['SoConSong'] : '—' ?>
                            </td>

                            <td class="px-4 py-3 text-center">
                                <?php
                                $status = $r['TrangThai'] ?? 'DangTheoDoi';
                                $badgeClass = $status == 'ThanhCong' ? 'bg-emerald-100 text-emerald-700' : ($status == 'ThatBai' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700');
                                $statusText = $status == 'ThanhCong' ? 'Thành công' : ($status == 'ThatBai' ? 'Thất bại' : 'Đang theo dõi');
                                ?>
                                <span class="px-2 py-1 rounded-lg text-[10px] font-bold uppercase <?= $badgeClass ?>">
                                    <?= $statusText ?>
                                </span>
                            </td>

                            <td class="px-4 py-3 text-center space-x-1">
                                <div class="flex justify-center gap-1">
                                    <?php if ($r['SoConSong'] === null): ?>
                                        <a href="index.php?url=sinhsan/ghiNhanDe&id=<?= $r['SinhSan'] ?>"
                                            class="bg-blue-600 text-white p-1.5 rounded-lg text-[10px] hover:bg-blue-700 transition" title="Ghi nhận đẻ">
                                            <i class="fa-solid fa-baby-carriage"></i>
                                        </a>
                                    <?php endif; ?>

                                    <a href="index.php?url=sinhsan/edit&id=<?= $r['SinhSan'] ?>"
                                        class="bg-amber-500 text-white p-1.5 rounded-lg text-[10px] hover:bg-amber-600 transition">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>

                                    <a href="index.php?url=sinhsan/delete&id=<?= $r['SinhSan'] ?>"
                                        onclick="return confirm('Xóa phiếu #<?= $r['SinhSan'] ?>?');"
                                        class="bg-red-600 text-white p-1.5 rounded-lg text-[10px] hover:bg-red-700 transition">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <?php if ($totalPages > 1): ?>
                    <div class="mt-8 flex justify-center">
                        <nav class="flex gap-1" aria-label="Pagination">
                            <!-- Nút Trước -->
                            <a href="?url=sinhsan&page=<?= max(1, $page - 1) ?>"
                                class="px-4 py-3 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium transition <?= $page <= 1 ? 'opacity-50 cursor-not-allowed' : '' ?>">
                                Trước
                            </a>

                            <!-- Các số trang -->
                            <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                                <a href="?url=sinhsan&page=<?= $i ?>"
                                    class="px-4 py-3 rounded-lg font-medium transition <?= $i == $page ? 'bg-emerald-600 text-white shadow-lg' : 'bg-gray-100 hover:bg-gray-200 text-gray-700' ?>">
                                    <?= $i ?>
                                </a>
                            <?php endfor; ?>

                            <!-- Nút Sau -->
                            <a href="?url=sinhsan&page=<?= min($totalPages, $page + 1) ?>"
                                class="px-4 py-3 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium transition <?= $page >= $totalPages ? 'opacity-50 cursor-not-allowed' : '' ?>">
                                Sau
                            </a>
                        </nav>
                    </div>

                    <div class="text-center mt-4 text-gray-600 text-sm">
                        Hiển thị <?= count($sinhSanList) ?> / <?= $total ?> phiếu sinh sản (Trang <?= $page ?> / <?= $totalPages ?>)
                    </div>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputNgayPhoi = document.getElementById('inputNgayPhoi');
        const displayNgayDuSinh = document.getElementById('displayNgayDuSinh');

        function tinhNgayDuSinh() {
            const val = inputNgayPhoi.value;
            if (val) {
                const ngayPhoi = new Date(val);
                // Cộng thêm 114 ngày (chu kỳ mang thai chuẩn của heo)
                ngayPhoi.setDate(ngayPhoi.getDate() + 114);

                // Định dạng ngày theo kiểu Việt Nam: DD/MM/YYYY
                const d = String(ngayPhoi.getDate()).padStart(2, '0');
                const m = String(ngayPhoi.getMonth() + 1).padStart(2, '0');
                const y = ngayPhoi.getFullYear();

                displayNgayDuSinh.value = `${d}/${m}/${y}`;
            } else {
                displayNgayDuSinh.value = "Chọn ngày phối để xem";
            }
        }

        // Chạy tính toán ngay khi trang vừa load xong (nếu đã có ngày sẵn)
        tinhNgayDuSinh();

        // Lắng nghe sự kiện thay đổi ngày phối
        inputNgayPhoi.addEventListener('change', tinhNgayDuSinh);
    });
</script>