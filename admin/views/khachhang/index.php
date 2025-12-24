<?php $title = "Quản Lý Khách Hàng"; ?>
<?php include __DIR__ . '/../layouts/header.php'; ?>
<?php include __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="ml-64 p-8 min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Quản Lý Khách Hàng</h1>
            <a href="index.php?url=khachhang/add" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-xl font-semibold shadow-lg transition transform hover:scale-105">
                + Thêm khách hàng mới
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden mb-8">
            <div class="p-4 bg-gradient-to-br from-emerald-50 to-teal-50">
                <form method="get" class="grid grid-cols-1 lg:grid-cols-11 gap-4 items-end">
                    <input type="hidden" name="url" value="khachhang">

                    <div class="lg:col-span-4">
                        <div class="relative">
                            <i class="fa-solid fa-magnifying-glass absolute left-5 top-1/2 -translate-y-1/2 text-emerald-600 text-lg"></i>
                            <input
                                type="text"
                                name="timkiem"
                                value="<?= htmlspecialchars($keyword ?? '') ?>"
                                placeholder="Tìm tên, SĐT, mã khách hàng, địa chỉ..."
                                class="w-full pl-14 pr-4 py-2.5 rounded-2xl border-2 border-gray-200 
                                   focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-100 
                                   text-base font-medium transition-all duration-300 shadow-sm"
                                autocomplete="off">
                        </div>
                    </div>

                    <div class="lg:col-span-7 flex flex-wrap items-center gap-3 justify-end">
                        <span class="text-gray-700 font-semibold hidden sm:block">Sắp xếp:</span>

                        <select name="sort"
                            class="px-3 py-2.5 rounded-xl border border-gray-300 font-medium 
                            focus:outline-none focus:ring-4 focus:ring-emerald-100 focus:border-emerald-500 transition bg-white">
                            <option value="MaKH" <?= ($sort ?? 'MaKH') == 'MaKH' ? 'selected' : '' ?>>Mã khách hàng</option>
                            <option value="TenKH" <?= ($sort ?? '') == 'TenKH' ? 'selected' : '' ?>>Tên khách hàng</option>
                            <option value="DiaChi" <?= ($sort ?? '') == 'DiaChi' ? 'selected' : '' ?>>Địa chỉ</option>
                        </select>

                        <select name="order"
                            class="px-3 py-2.5 rounded-xl border border-gray-300 font-medium 
                            focus:outline-none focus:ring-4 focus:ring-emerald-100 focus:border-emerald-500 transition bg-white">
                            <option value="DESC" <?= ($order ?? 'DESC') == 'DESC' ? 'selected' : '' ?>>Mới nhất trước</option>
                            <option value="ASC" <?= ($order ?? '') == 'ASC' ? 'selected' : '' ?>>Cũ nhất trước</option>
                        </select>

                        <button type="submit"
                            class="bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 
                            text-white font-bold px-6 py-2.5 rounded-xl shadow-lg transform hover:scale-105 transition duration-300">
                            Áp dụng
                        </button>

                        <?php if (!empty($keyword) || ($sort ?? '') != 'MaKH' || ($order ?? '') != 'DESC'): ?>
                            <a href="index.php?url=khachhang"
                                class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold px-4 py-2.5 rounded-xl transition duration-200 text-center">
                                Xóa lọc
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            <div class="overflow-x-auto">
                <table class="w-full" id="tableKhachHang">
                    <thead class="bg-gradient-to-r from-emerald-600 to-emerald-700 text-white">
                        <tr>
                            <th class="px-6 py-4 text-center">STT</th>
                            <th class="px-6 py-4 text-left">Mã KH</th>
                            <th class="px-6 py-4 text-left">Họ tên</th>
                            <th class="px-6 py-4 text-left">Số điện thoại</th>
                            <th class="px-6 py-4 text-center">Địa chỉ</th>
                            <th class="px-6 py-4 text-center text-yellow-300 font-bold">ĐÃ MUA (CON)</th>
                            <th class="px-6 py-4 text-center">Chi tiết</th>
                            <th class="px-6 py-4 text-center text-blue-300 font-bold uppercase">Phối giống</th>
                            
                            <th class="px-6 py-4 text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php
                        $stt = 1;
                        foreach ($dsKhachHang as $kh):
                            global $pdo;

                            // 1. Lấy tổng số heo khách đã mua từ bảng xuatchuong
                            $stmtXuat = $pdo->prepare("SELECT COALESCE(SUM(SoLuong), 0) FROM xuatchuong WHERE MaKH = ?");
                            $stmtXuat->execute([$kh['MaKH']]);
                            $tongDaMua = (int)$stmtXuat->fetchColumn();

                            // 2. Lấy tổng số lượt phối giống từ bảng sinhsan (Dùng bảng có sẵn của bạn)
                            // Mình giả định trong bảng sinhsan bạn có cột MaKH để đánh dấu heo của khách
                            $stmtPhoi = $pdo->prepare("SELECT COUNT(*) FROM sinhsan WHERE MaKH = ?");
                            $stmtPhoi->execute([$kh['MaKH']]);
                            $tongPhoiGiong = (int)$stmtPhoi->fetchColumn();
                        ?>
                            <tr class="hover:bg-emerald-50 transition">
                                <td class="px-6 py-4 text-center font-bold text-gray-400"><?= $stt++ ?></td>
                                <td class="px-6 py-4 font-medium text-emerald-700"><?= $kh['MaKH'] ?></td>
                                <td class="px-6 py-4 font-semibold text-gray-800"><?= htmlspecialchars($kh['TenKH']) ?></td>
                                <td class="px-6 py-4 text-gray-600"><?= $kh['SDT'] ?></td>

                                <td class="px-6 py-4 text-center">
                                    <?php if (!empty($kh['DiaChi'])): ?>
                                        <span class="px-4 py-1.5 bg-blue-50 text-blue-700 rounded-full font-medium text-sm border border-blue-100">
                                            <?= htmlspecialchars($kh['DiaChi']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-gray-400 italic">Chưa có địa chỉ</span>
                                    <?php endif; ?>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <span class="text-2xl font-bold <?= $tongDaMua >= 100 ? 'text-red-600' : 'text-emerald-600' ?>">
                                        <?= number_format($tongDaMua) ?>
                                    </span>
                                    <span class="text-sm font-medium text-gray-500 ml-1">con</span>
                                    <?php if ($tongDaMua >= 100): ?>
                                        <i class="fas fa-crown text-yellow-500 ml-1" title="Khách VIP"></i>
                                    <?php endif; ?>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <a href="index.php?url=xuatchuong&kh_id=<?= $kh['MaKH'] ?>"
                                        class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition shadow-md hover:shadow-lg">
                                        <i class="fa-solid fa-file-invoice"></i>
                                        Lịch sử xuất
                                    </a>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="index.php?url=sinhsan&kh_id=<?= $kh['MaKH'] ?>"
                                        class="inline-flex items-center gap-2 bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-medium transition shadow-md hover:shadow-lg">
                                        <i class="fa-solid fa-venus-mars"></i>
                                        Phối giống (<?= $tongPhoiGiong ?>)
                                    </a>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex justify-center items-center gap-4">
                                        <a href="index.php?url=khachhang/edit&id=<?= $kh['MaKH'] ?>"
                                            class="text-indigo-600 hover:text-indigo-900 font-bold transition">Sửa</a>
                                        <a href="index.php?url=khachhang/delete&id=<?= $kh['MaKH'] ?>"
                                            onclick="return confirm('Xóa khách hàng này?')"
                                            class="text-red-600 hover:text-red-900 font-bold transition">Xóa</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if (isset($totalPages) && $totalPages > 1): ?>
                <div class="p-6 bg-white border-t border-gray-100 flex flex-col items-center">

                    <?php if (isset($totalPages) && $totalPages > 1): ?>
                        <nav class="flex items-center gap-2 mb-4">
                            <a href="index.php?url=khachhang&page=<?= max(1, $page - 1) ?>&timkiem=<?= urlencode($keyword ?? '') ?>&sort=<?= $sort ?? '' ?>&order=<?= $order ?? '' ?>"
                                class="p-2.5 rounded-xl bg-white border border-gray-200 text-gray-600 hover:bg-emerald-50 transition <?= $page <= 1 ? 'opacity-40 pointer-events-none' : '' ?>">
                                <i class="fa-solid fa-chevron-left"></i>
                            </a>

                            <?php
                            $start = max(1, $page - 2);
                            $end = min($totalPages, $page + 2);
                            for ($i = $start; $i <= $end; $i++):
                            ?>
                                <a href="index.php?url=khachhang&page=<?= $i ?>&timkiem=<?= urlencode($keyword ?? '') ?>&sort=<?= $sort ?? '' ?>&order=<?= $order ?? '' ?>"
                                    class="w-11 h-11 flex items-center justify-center rounded-xl font-bold transition <?= $i == $page ? 'bg-emerald-600 text-white shadow-lg' : 'bg-white border border-gray-200 text-gray-600 hover:border-emerald-500' ?>">
                                    <?= $i ?>
                                </a>
                            <?php endfor; ?>

                            <a href="index.php?url=khachhang&page=<?= min($totalPages, $page + 1) ?>&timkiem=<?= urlencode($keyword ?? '') ?>&sort=<?= $sort ?? '' ?>&order=<?= $order ?? '' ?>"
                                class="p-2.5 rounded-xl bg-white border border-gray-200 text-gray-600 hover:bg-emerald-50 transition <?= $page >= $totalPages ? 'opacity-40 pointer-events-none' : '' ?>">
                                <i class="fa-solid fa-chevron-right"></i>
                            </a>
                        </nav>
                    <?php endif; ?>

                    <div class="text-gray-500 text-sm font-medium">
                        Hiển thị <?= count($dsKhachHang) ?> / <?= $totalRecords ?> khách hàng
                        (Trang <?= $page ?> / <?= $totalPages ?>)
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div id="chiTietModal" class="fixed inset-0 bg-black bg-opacity-60 z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full mx-4 max-h-screen overflow-y-auto">
        <div class="bg-gradient-to-r from-emerald-600 to-teal-700 text-white p-6 rounded-t-2xl">
            <h2 class="text-2xl font-bold" id="modalTenKH">Khách hàng</h2>
            <p class="opacity-90">Lịch sử mua heo chi tiết</p>
        </div>
        <div class="p-8" id="modalNoiDung">
            <p class="text-center py-10"><i class="fas fa-spinner fa-spin text-4xl text-emerald-600"></i></p>
        </div>
        <div class="p-6 border-t text-right">
            <button onclick="document.getElementById('chiTietModal').classList.add('hidden')"
                class="bg-gray-600 hover:bg-gray-700 text-white px-8 py-3 rounded-xl font-medium">
                Đóng
            </button>
        </div>
    </div>
</div>

<script>
    async function xemChiTiet(maKH, tenKH) {
        document.getElementById('modalTenKH').textContent = tenKH;
        const noidung = document.getElementById('modalNoiDung');
        noidung.innerHTML = '<p class="text-center py-10"><i class="fas fa-spinner fa-spin text-4xl text-emerald-600"></i></p>';

        try {
            const response = await fetch(`index.php?url=khachhang/xemchitiet&makh=${maKH}`);
            const data = await response.json();

            let html = `
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-blue-50 p-6 rounded-xl text-center">
                        <p class="text-gray-600">Tổng cộng đã mua</p>
                        <p class="text-4xl font-bold text-blue-600">${data.tong} con</p>
                    </div>
                    <div class="bg-pink-50 p-6 rounded-xl text-center">
                        <p class="text-gray-600">Con đực</p>
                        <p class="text-4xl font-bold text-pink-600">${data.duc || 0} con</p>
                    </div>
                    <div class="bg-teal-50 p-6 rounded-xl text-center">
                        <p class="text-gray-600">Con cái</p>
                        <p class="text-4xl font-bold text-teal-600">${data.cai || 0} con</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <h3 class="font-bold text-lg mb-3 text-emerald-700">Từ các chuồng</h3>
                        <div class="space-y-2">
                            ${Object.entries(data.chuong || {}).length > 0 
                                ? Object.entries(data.chuong || {}).map(([c, sl]) => ` <
                div class = "flex justify-between bg-gray-50 p-3 rounded-lg" >
                <
                span class = "font-medium" > $ {
                    c || 'Không ghi'
                } < /span> <
            span class = "text-emerald-600 font-bold" > $ {
                sl
            }
            con < /span> < /
            div >
                `).join('')
                                : '<p class="text-gray-500">Chưa có dữ liệu</p>'
                            }
                        </div>
                    </div>

                    <div>
                        <h3 class="font-bold text-lg mb-3 text-indigo-700">Nhân viên bán</h3>
                        <div class="space-y-2">
                            ${Object.entries(data.nhanvien || {}).length > 0 
                                ? Object.entries(data.nhanvien || {}).map(([nv, sl]) => ` <
                div class = "flex justify-between bg-indigo-50 p-3 rounded-lg" >
                <
                span class = "font-medium" > $ {
                    nv
                } < /span> <
            span class = "text-indigo-600 font-bold" > $ {
                sl
            }
            lần < /span> < /
            div >
                `).join('')
                                : '<p class="text-gray-500">Chưa có dữ liệu</p>'
                            }
                        </div>
                    </div>
                </div>
            `;

            noidung.innerHTML = html;
            document.getElementById('chiTietModal').classList.remove('hidden');
        } catch (e) {
            console.error(e);
            noidung.innerHTML = '<p class="text-red-600 text-center">Lỗi tải dữ liệu!</p>';
        }
    }
</script>
<!-- 
// TÌM KIẾM REALTIME
document.getElementById('searchInput')?.addEventListener('keyup', function () {
const filter = this.value.toLowerCase();
const rows = document.querySelectorAll('#tableKhachHang tbody tr');
rows.forEach(row => {
const text = row.textContent.toLowerCase();
row.style.display = text.includes(filter) ? '' : 'none';
});
}); -->
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>