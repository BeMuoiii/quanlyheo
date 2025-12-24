<?php $title = "Quản Lý Xuất Chuồng Heo"; ?>
<?php include __DIR__ . '/../layouts/header.php'; ?>
<?php include __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="ml-64 p-8 min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto">

        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Xuất Chuồng Heo</h1>
            <a href="index.php?url=xuatchuong/add" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-xl font-semibold shadow-lg transition transform hover:scale-105">
                + Thêm phiếu xuất mới
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden mb-8">
            <div class="p-4 bg-gradient-to-br from-emerald-50 to-teal-50">
                <form method="get" class="grid grid-cols-1 lg:grid-cols-11 gap-4 items-end">
                    <input type="hidden" name="url" value="xuatchuong">
                    <?php if (isset($kh_id) && $kh_id > 0): ?>
                        <input type="hidden" name="kh_id" value="<?= $kh_id ?>">
                    <?php endif; ?>

                    <div class="lg:col-span-4">
                        <div class="relative">
                            <i class="fa-solid fa-magnifying-glass absolute left-5 top-1/2 -translate-y-1/2 text-emerald-600 text-lg"></i>
                            <input
                                type="text"
                                name="timkiem"
                                value="<?= htmlspecialchars($keyword ?? '') ?>"
                                placeholder="Tìm mã heo, khách hàng..."
                                class="w-full pl-14 pr-4 py-2.5 rounded-2xl border-2 border-gray-200 focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-100 text-base font-medium transition-all shadow-sm"
                                autocomplete="off">
                        </div>
                    </div>

                    <div class="lg:col-span-7 flex flex-wrap items-center gap-3 justify-end">
                        <span class="text-gray-700 font-semibold hidden sm:block">Sắp xếp:</span>
                        <select name="sort" class="px-3 py-2.5 rounded-xl border border-gray-300 font-medium focus:outline-none focus:ring-4 focus:ring-emerald-100 bg-white">
                            <option value="NgayXuat" <?= ($sort ?? 'NgayXuat') == 'NgayXuat' ? 'selected' : '' ?>>Ngày xuất</option>
                            <option value="ThanhTien" <?= ($sort ?? '') == 'ThanhTien' ? 'selected' : '' ?>>Tổng tiền</option>
                        </select>
                        <select name="order" class="px-3 py-2.5 rounded-xl border border-gray-300 font-medium focus:outline-none focus:ring-4 focus:ring-emerald-100 bg-white">
                            <option value="DESC" <?= ($order ?? 'DESC') == 'DESC' ? 'selected' : '' ?>>Mới nhất trước</option>
                            <option value="ASC" <?= ($order ?? '') == 'ASC' ? 'selected' : '' ?>>Cũ nhất trước</option>
                        </select>
                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-6 py-2.5 rounded-xl shadow-lg transition transform hover:scale-105">
                            Áp dụng
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden mb-8">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-emerald-600 text-white text-sm">
                        <tr class="uppercase tracking-wider">
                            <th class="px-4 py-4 text-center font-bold">STT</th>
                            <th class="px-4 py-4 text-left font-bold">Ngày Xuất</th>
                            <th class="px-4 py-4 text-left font-bold">Mã Heo</th>
                            <th class="px-4 py-4 text-center font-bold">Cân Nặng</th>
                            <th class="px-4 py-4 text-center font-bold">SL</th>
                            <th class="px-4 py-4 text-right font-bold">Đơn Giá</th>
                            <th class="px-4 py-4 text-right font-bold">Tổng Tiền</th>
                            <th class="px-4 py-4 text-left font-bold">Khách Hàng</th>
                            <th class="px-4 py-4 text-center font-bold">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if (empty($xuats)): ?>
                            <tr>
                                <td colspan="9" class="text-center py-10 text-gray-500">Chưa có dữ liệu</td>
                            </tr>
                        <?php else: ?>
                            <?php $stt = ($page - 1) * $limit + 1; ?>
                            <?php foreach ($xuats as $x): ?>
                                <tr class="hover:bg-emerald-50 transition duration-150">
                                    <td class="px-4 py-4 text-center font-semibold text-gray-600"><?= $stt++ ?></td>
                                    <td class="px-4 py-4">
                                        <div class="font-bold text-emerald-700"><?= date('d/m/y', strtotime($x['NgayXuat'])) ?></div>
                                        <div class="text-[10px] text-gray-400"><?= date('H:i', strtotime($x['NgayXuat'])) ?></div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="font-bold text-emerald-700 italic">#<?= htmlspecialchars($x['MaHeo']) ?></div>
                                        <div class="text-[11px] text-gray-500"><?= $x['TenHeoHienThi'] ?></div>
                                    </td>
                                    <td class="px-4 py-4 text-center font-bold text-orange-600"><?= number_format($x['CanNangXuat'], 1) ?> kg</td>
                                    <td class="px-4 py-4 text-center font-bold text-blue-600"><?= $x['SoLuong'] ?></td>
                                    <td class="px-4 py-4 text-right text-gray-500"><?= number_format($x['DonGia']) ?></td>
                                    <td class="px-4 py-4 text-right font-bold text-emerald-600 text-base"><?= number_format($x['ThanhTien']) ?></td>
                                    <td class="px-4 py-4">
                                        <div class="font-bold text-gray-700 text-xs"><?= htmlspecialchars($x['TenKH'] ?? 'Khách lẻ') ?></div>
                                        <div class="text-[10px] text-gray-400"><?= htmlspecialchars($x['SDT_KH'] ?? '-') ?></div>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <div class="flex justify-center gap-2">
                                            <a href="index.php?url=xuatchuong/edit&id=<?= $x['MaXuat'] ?>" class="p-1.5 bg-indigo-50 text-indigo-600 rounded-lg hover:bg-indigo-100 transition shadow-sm"><i class="fa-solid fa-pen-to-square"></i></a>
                                            <a href="index.php?url=xuatchuong/delete&id=<?= $x['MaXuat'] ?>" onclick="return confirm('Xóa phiếu này?')" class="p-1.5 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition shadow-sm"><i class="fa-solid fa-trash"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($totalPages > 1): ?>
                <div class="p-6 bg-white border-t border-gray-100 flex flex-col items-center">
                    <?php if (isset($totalPages) && $totalPages > 1): ?>
                        <nav class="flex gap-2 mb-4">
                            <a href="?url=xuatchuong&page=<?= max(1, $page - 1) ?>&timkiem=<?= urlencode($keyword ?? '') ?>&sort=<?= $sort ?? '' ?>&order=<?= $order ?? '' ?>"
                                class="px-4 py-2 rounded-lg bg-gray-100 text-gray-700 font-bold hover:bg-emerald-600 hover:text-white transition <?= $page <= 1 ? 'opacity-50 pointer-events-none' : '' ?>">
                                Trước
                            </a>

                            <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                                <a href="?url=xuatchuong&page=<?= $i ?>&timkiem=<?= urlencode($keyword ?? '') ?>&sort=<?= $sort ?? '' ?>&order=<?= $order ?? '' ?>"
                                    class="px-4 py-2 rounded-lg font-bold transition <?= $i == $page ? 'bg-emerald-600 text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-emerald-200' ?>">
                                    <?= $i ?>
                                </a>
                            <?php endfor; ?>

                            <a href="?url=xuatchuong&page=<?= min($totalPages, $page + 1) ?>&timkiem=<?= urlencode($keyword ?? '') ?>&sort=<?= $sort ?? '' ?>&order=<?= $order ?? '' ?>"
                                class="px-4 py-2 rounded-lg bg-gray-100 text-gray-700 font-bold hover:bg-emerald-600 hover:text-white transition <?= $page >= $totalPages ? 'opacity-50 pointer-events-none' : '' ?>">
                                Sau
                            </a>
                        </nav>
                    <?php endif; ?>

                    <div class="text-gray-500 text-sm font-medium">
                        Hiển thị <?= count($xuats ?? []) ?> / <?= $totalRecords ?? 0 ?> phiếu xuất
                        (Trang <?= $page ?? 1 ?> / <?= $totalPages ?? 1 ?>)
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>