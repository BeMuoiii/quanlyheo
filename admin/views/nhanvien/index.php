<?php $title = "Quản Lý Nhân Viên"; ?>
<?php include __DIR__ . '/../layouts/header.php'; ?>
<?php include __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="ml-64 p-8 min-h-screen bg-gray-50">

    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Quản Lý Nhân Viên</h1>
            <p class="text-gray-600">Heo Rừng Lai An Nông</p>
        </div>
        <a href="index.php?url=nhanvien/add"
            class="bg-gradient-to-r from-green-600 to-emerald-700 hover:from-green-700 hover:to-emerald-800 
                  text-white font-bold px-6 py-4 rounded-xl shadow-lg flex items-center gap-3 transform hover:scale-105 transition">
            <i class="fa-solid fa-plus text-xl"></i>
            Tuyển dụng mới
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-2xl p-6 shadow-xl">
            <div class="text-4xl font-bold"><?= $tongNhanVien ?></div>
            <div class="text-blue-100 text-sm mt-1">Tổng nhân viên</div>
        </div>
        <div class="bg-gradient-to-br from-green-500 to-emerald-600 text-white rounded-2xl p-6 shadow-xl">
            <div class="text-4xl font-bold"><?= $nhanVienChinhThuc ?></div>
            <div class="text-green-100 text-sm mt-1">Nhân viên chính thức</div>
        </div>
        <div class="bg-gradient-to-br from-orange-500 to-amber-600 text-white rounded-2xl p-6 shadow-xl">
            <div class="text-4xl font-bold"><?= $nhanVienThuViec ?></div>
            <div class="text-orange-100 text-sm mt-1">Nhân viên thử việc</div>
        </div>
        <div class="bg-gradient-to-br from-purple-500 to-pink-600 text-white rounded-2xl p-6 shadow-xl <?= $nghiViecHomNay > 0 ? 'animate-pulse' : '' ?>">
            <div class="text-4xl font-bold"><?= $nghiViecHomNay ?></div>
            <div class="text-purple-100 text-sm mt-1">Nghỉ việc hôm nay</div>
        </div>
    </div>

    <!-- Cảnh báo nghỉ việc hôm nay -->
    <div class="bg-white rounded-3xl shadow-2xl border <?= $nghiViecHomNay > 0 ? 'border-red-200' : 'border-gray-100' ?> overflow-hidden mb-8">
        <div class="bg-gradient-to-r from-red-600 to-rose-700 text-white p-5">
            <h3 class="text-xl font-bold flex items-center gap-3">
                <i class="fa-solid fa-bell <?= $nghiViecHomNay > 0 ? 'animate-pulse' : '' ?>"></i>
                Nhân viên nghỉ việc hôm nay – Cần làm thủ tục bàn giao
            </h3>
        </div>
        <div class="p-6">
            <?php if (empty($listNghiViecHomNay)): ?>
                <p class="text-center py-12 text-gray-500 text-lg">
                    <i class="fa-solid fa-check-circle text-6xl text-green-500 block mb-4"></i>
                    Không có nhân viên nào nghỉ việc hôm nay
                </p>
            <?php else: ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <?php foreach ($listNghiViecHomNay as $nv): ?>
                        <div class="bg-red-50 border-2 border-red-300 rounded-2xl p-6 text-center shadow hover:scale-105 transition">
                            <img src="<?= htmlspecialchars($nv['Anh'] ?: '/assets/img/avatar-default.png') ?>"
                                class="w-20 h-20 rounded-full mx-auto mb-3 object-cover border-4 border-red-300">
                            <div class="text-2xl font-bold text-red-700"><?= htmlspecialchars($nv['HoTen']) ?></div>
                            <div class="text-sm text-gray-600 mt-1"><?= htmlspecialchars($nv['ViTri'] ?? 'Chưa có chức vụ') ?></div>
                            <div class="text-xs text-gray-500">Bộ phận: <?= htmlspecialchars($nv['TenBoPhan'] ?? '—') ?></div>
                            <div class="mt-4">
                                <span class="px-8 py-3 bg-red-600 text-white font-bold rounded-full text-lg shadow">
                                    Nghỉ hôm nay
                                </span>
                            </div>
                            <a href="index.php?url=nhanvien/banGiao&id=<?= $nv['MaNV'] ?>"
                                class="block mt-4 text-red-700 font-bold hover:underline">
                                → Làm bàn giao
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Danh sách nhân viên -->
    <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-2xl font-bold text-gray-800">Danh sách nhân viên</h3>
        </div>
        <table class="w-full">
            <thead class="bg-gradient-to-r from-emerald-600 to-teal-700 text-white">
                <tr>
                    <th class="px-6 py-4 text-center">STT</th>
                    <th class="px-6 py-4 text-center">Mã NV</th>
                    <th class="px-6 py-4 text-left">Họ tên</th>
                    <th class="px-6 py-4 text-left">SĐT</th>
                    <th class="px-6 py-4 text-left">Chức vụ</th>
                    <th class="px-6 py-4 text-left">Bộ phận</th>
                    <th class="px-6 py-4 text-center">Ngày vào</th>
                    <th class="px-6 py-4 text-center">Trạng thái</th>
                    <th class="px-6 py-4 text-center">Hành động</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php
                $stt = 1; // Bắt đầu đếm STT
                foreach ($dsNhanVien as $nv):
                    $statusClass = match ($nv['TrangThai'] ?? '') {
                        'Chính thức' => 'emerald',
                        'Thử việc'   => 'yellow',
                        default      => 'red'
                    };
                ?>
                    <tr class="hover:bg-emerald-50 transition">
                        <!-- STT -->
                        <td class="px-6 py-5 text-center font-medium text-gray-700">
                            <?= $stt++ ?>
                        </td>

                        <!-- Mã NV -->
                        <td class="px-6 py-5 text-center">
                            <span class="inline-block px-4 py-2 bg-emerald-100 text-emerald-800 font-bold rounded-full text-sm">
                                #<?= str_pad($nv['MaNV'], 4, '0', STR_PAD_LEFT) ?>
                            </span>
                        </td>

                        <!-- Họ tên + Avatar -->
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-3">
                                <img src="<?= htmlspecialchars($nv['Anh'] ?: '/assets/img/avatar-default.png') ?>"
                                    class="w-10 h-10 rounded-full object-cover border-2 border-gray-300">
                                <span class="font-bold text-emerald-700"><?= htmlspecialchars($nv['HoTen']) ?></span>
                            </div>
                        </td>

                        <!-- SĐT -->
                        <td class="px-6 py-5"><?= htmlspecialchars($nv['SDT'] ?? '—') ?></td>

                        <!-- Chức vụ -->
                        <td class="px-6 py-5"><?= htmlspecialchars($nv['ViTri'] ?? '—') ?></td>

                        <!-- Bộ phận -->
                        <td class="px-6 py-5">
                            <span class="text-sm font-medium text-gray-700">
                                <?= htmlspecialchars($nv['TenBoPhan'] ?? 'Chưa có') ?>
                            </span>
                        </td>

                        <!-- Ngày vào -->
                        <td class="px-6 py-5 text-center">
                            <?= $nv['NgayVaoLam'] ? date('d/m/Y', strtotime($nv['NgayVaoLam'])) : '—' ?>
                        </td>

                        <!-- Trạng thái -->
                        <td class="px-6 py-5 text-center">
                            <span class="px-4 py-2 rounded-full text-xs font-bold
                    <?= $statusClass === 'emerald' ? 'bg-emerald-100 text-emerald-700' : '' ?>
                    <?= $statusClass === 'yellow' ? 'bg-yellow-100 text-yellow-700' : '' ?>
                    <?= $statusClass === 'red' ? 'bg-red-100 text-red-700' : '' ?>">
                                <?= $nv['TrangThai'] ?? 'Thử việc' ?>
                            </span>
                        </td>

                        <!-- Hành động -->
                        <td class="px-6 py-5 text-center text-sm">
                            <a href="index.php?url=nhanvien/edit&id=<?= $nv['MaNV'] ?>"
                                class="text-blue-600 hover:underline mr-3 font-medium">Sửa</a>
                            <?php if (($nv['TrangThai'] ?? '') !== 'Nghỉ việc'): ?>
                                <a href="index.php?url=nhanvien/banGiao&id=<?= $nv['MaNV'] ?>"
                                    class="text-orange-600 hover:underline mr-3 font-medium">Bàn giao</a>
                            <?php endif; ?>
                            <a href="index.php?url=nhanvien/delete&id=<?= $nv['MaNV'] ?>"
                                class="text-red-600 hover:underline font-medium"
                                onclick="return confirm('XÓA VĨNH VIỄN nhân viên này?\nHành động KHÔNG THỂ HOÀN TÁC!')">
                                Xóa
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>