<?php include __DIR__ . '/../layouts/header.php'; ?>
<?php include __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="ml-64 p-8 min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Quản Lý Phả Hệ & Phối Giống</h1>
                <p class="text-lg text-gray-600 mt-2">
                    Khách hàng: <strong><?= htmlspecialchars($khach['TenKH']) ?></strong>
                    <?= !empty($khach['SDT']) ? ' - ' . $khach['SDT'] : '' ?>
                    <?= !empty($khach['DiaChi']) ? ' - ' . htmlspecialchars($khach['DiaChi']) : '' ?>
                </p>
            </div>
            <a href="index.php?url=sinhsan/khach_add&kh_id=<?= $khach['MaKH'] ?>"
                class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-xl font-semibold shadow-lg transition transform hover:scale-105">
                <i class="fa-solid fa-plus mr-2"></i> Ghi nhận phối giống cho khách
            </a>
        </div>

        <!-- 1. Heo đã bán + phả hệ gốc từ trại -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-10">
            <div class="p-6 bg-gradient-to-r from-orange-600 to-orange-700 text-white">
                <h2 class="text-2xl font-bold">Heo đã cung cấp (<?= count($dsHeo) ?> con)</h2>
                <p class="opacity-90">Phả hệ gốc do trại Heo Rừng Lái cung cấp</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-orange-100 text-orange-800">
                        <tr>
                            <th class="px-6 py-4 text-left">Mã heo</th>
                            <th class="px-6 py-4 text-center">Giới tính</th>
                            <th class="px-6 py-4 text-center">Ngày xuất</th>
                            <th class="px-6 py-4 text-center">Cân nặng xuất</th>
                            <th class="px-6 py-4 text-center font-bold text-blue-700">Ba (Đực)</th>
                            <th class="px-6 py-4 text-center font-bold text-pink-700">Mẹ (Cái)</th>
                            <th class="px-6 py-4 text-center">Ngày sinh</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if (empty($dsHeo)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-10 text-gray-500">Chưa xuất heo cho khách này</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($dsHeo as $heo): ?>
                                <tr class="hover:bg-orange-50 transition">
                                    <td class="px-6 py-4 font-bold text-orange-700"><?= htmlspecialchars($heo['MaHeo']) ?></td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-4 py-1 rounded-full text-sm font-bold <?= $heo['GioiTinh'] == 'D' ? 'bg-blue-100 text-blue-700' : 'bg-pink-100 text-pink-700' ?>">
                                            <?= $heo['GioiTinh'] == 'D' ? 'Đực' : 'Cái' ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center"><?= date('d/m/Y', strtotime($heo['NgayXuat'])) ?></td>
                                    <td class="px-6 py-4 text-center font-semibold"><?= number_format($heo['CanNang'], 1) ?> kg</td>
                                    <td class="px-6 py-4 text-center font-medium text-blue-700">
                                        <?= !empty($heo['Cha']) ? htmlspecialchars($heo['Cha']) : '<i class="text-gray-400">Chưa ghi</i>' ?>
                                    </td>
                                    <td class="px-6 py-4 text-center font-medium text-pink-700">
                                        <?= !empty($heo['Me']) ? htmlspecialchars($heo['Me']) : '<i class="text-gray-400">Chưa ghi</i>' ?>
                                    </td>
                                    <td class="px-6 py-4 text-center"><?= $heo['NgaySinh'] ? date('d/m/Y', strtotime($heo['NgaySinh'])) : '-' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 2. Lịch phối giống đã ghi nhận cho khách (phối hộ / tư vấn) -->
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100">
            <div class="p-6 bg-gradient-to-r from-emerald-600 to-teal-700 text-white">
                <h2 class="text-2xl font-bold">Lịch phối giống đã ghi nhận (<?= count($lichPhoi) ?> lần)</h2>
                <p class="opacity-90 mt-1">Heo đực từ trại bạn, heo cái của khách (nhập mã tự do)</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-emerald-600 to-teal-700 text-white text-xs uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-4 text-center">STT</th>
                            <th class="px-6 py-4 text-center">Ngày phối</th>
                            <th class="px-6 py-4 text-center">Heo đực (trại)</th>
                            <th class="px-6 py-4 text-center">Heo cái (khách)</th>
                            <th class="px-6 py-4 text-center">Dự kiến sinh</th>
                            <th class="px-6 py-4 text-center text-blue-200">Ngày đẻ thực tế</th>
                            <th class="px-6 py-4 text-center">Kết quả đẻ</th>
                            <th class="px-6 py-4 text-center">Ghi chú</th>
                            <th class="px-6 py-4 text-center">Thao tác</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200">
                        <?php if (empty($lichPhoi)): ?>
                            <tr>
                                <td colspan="9" class="text-center py-12 text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <i class="fa-solid fa-calendar-times text-4xl text-gray-300 mb-4"></i>
                                        <p class="text-lg">Chưa ghi nhận lần phối giống nào cho khách này.</p>
                                        <p class="text-sm mt-2">Khi khách gọi phối heo, bấm nút <strong>"Ghi nhận phối giống mới"</strong> phía trên.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $stt = 1; ?>
                            <?php foreach ($lichPhoi as $phoi): ?>
                                <tr class="hover:bg-emerald-50 transition text-sm">
                                    <td class="px-6 py-4 text-center font-medium text-gray-600">
                                        <?= $stt++ ?>
                                    </td>

                                    <td class="px-6 py-4 text-center font-semibold">
                                        <?= $phoi['NgayPhoi'] ? date('d/m/Y', strtotime($phoi['NgayPhoi'])) : '-' ?>
                                    </td>

                                    <td class="px-6 py-4 text-center text-blue-700 font-medium">
                                        <?= htmlspecialchars($phoi['TenHeoDuc'] ?? $phoi['MaHeoDuc'] ?? '-') ?>
                                    </td>

                                    <td class="px-6 py-4 text-center text-pink-700 font-medium">
                                        <?= htmlspecialchars($phoi['MaHeoCai'] ?? $phoi['MaHeoNai'] ?? '-') ?>
                                    </td>

                                    <td class="px-6 py-4 text-center text-orange-600 font-medium">
                                        <?= $phoi['NgayDuKienDe'] ? date('d/m/Y', strtotime($phoi['NgayDuKienDe'])) : '-' ?>
                                    </td>

                                    <td class="px-6 py-4 text-center font-bold text-blue-700 bg-blue-50/30">
                                        <?= !empty($phoi['NgayDe']) ? date('d/m/Y', strtotime($phoi['NgayDe'])) : '<span class="text-gray-400 italic">Chưa đẻ</span>' ?>
                                    </td>

                                    <td class="px-6 py-4 text-center">
                                        <?php if (!empty($phoi['NgayDe'])): ?>
                                            <?php if ($phoi['SoConSong'] > 0): ?>
                                                <span class="text-emerald-600 font-bold"><?= $phoi['SoConSong'] ?> sống / <?= $phoi['SoConChet'] ?? 0 ?> chết</span>
                                            <?php elseif ($phoi['SoConSong'] === 0): ?>
                                                <span class="text-red-600 font-bold">Thất bại</span>
                                            <?php else: ?>
                                                <span class="text-orange-600 font-bold">Chưa ghi số con</span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-gray-500">Chưa đẻ</span>
                                        <?php endif; ?>
                                    </td>

                                    <td class="px-6 py-4 text-center text-gray-700 text-xs">
                                        <?= htmlspecialchars($phoi['GhiChu'] ?? $phoi['GhiChuDe'] ?? '-') ?>
                                    </td>

                                    <td class="px-6 py-4 text-center">
                                        <div class="flex justify-center gap-2">
                                            <a href="index.php?url=sinhsan/edit&id=<?= $phoi['SinhSan'] ?>&kh_id=<?= $khach['MaKH'] ?>"
                                                class="bg-amber-500 text-white p-2 rounded-lg hover:bg-amber-600 transition" title="Sửa">
                                                <i class="fa-solid fa-pen text-xs"></i>
                                            </a>

                                            <?php if (empty($phoi['NgayDe'])): ?>
                                                <a href="index.php?url=sinhsan/edit&id=<?= $phoi['SinhSan'] ?>&kh_id=<?= $khach['MaKH'] ?>#ghi-nhan-de"
                                                    class="bg-blue-600 text-white p-2 rounded-lg hover:bg-blue-700 transition" title="Ghi nhận đẻ">
                                                    <i class="fa-solid fa-baby-carriage text-xs"></i>
                                                </a>
                                            <?php endif; ?>

                                            <a href="index.php?url=sinhsan/delete&id=<?= $phoi['SinhSan'] ?>"
                                                onclick="return confirm('Xóa lần phối này? Dữ liệu sẽ mất vĩnh viễn.');"
                                                class="bg-red-600 text-white p-2 rounded-lg hover:bg-red-700 transition" title="Xóa">
                                                <i class="fa-solid fa-trash text-xs"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>