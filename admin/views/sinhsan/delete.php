<?php $title = "Xóa Heo Trong Trại"; ?>
<?php include __DIR__ . '/../layouts/header.php'; ?>
<?php include __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="ml-64 p-8 min-h-screen bg-gray-50">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <div class="text-center mb-8">
                <i class="fas fa-exclamation-triangle text-6xl text-red-500 mb-6"></i>
                <h1 class="text-3xl font-bold text-gray-800 mb-4">Bạn có chắc chắn muốn xóa heo này?</h1>
                <p class="text-lg text-gray-600">Hành động này <strong class="text-red-600">không thể hoàn tác</strong>!</p>
            </div>

            <?php if ($heo): ?>
                <!-- Hiển thị thông tin heo sẽ bị xóa -->
                <div class="bg-gray-50 rounded-xl p-6 mb-8 border border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-lg">
                        <div>
                            <span class="font-semibold text-gray-700">Mã Heo:</span>
                            <span class="ml-3 text-xl font-bold text-emerald-700"><?= htmlspecialchars($heo['MaHeo']) ?></span>
                        </div>
                        <div>
                            <span class="font-semibold text-gray-700">Giống:</span>
                            <span class="ml-3"><?= htmlspecialchars($heo['GiongHeo'] ?? 'Heo rừng lai') ?></span>
                        </div>
                        <div>
                            <span class="font-semibold text-gray-700">Giới Tính:</span>
                            <span class="ml-3 <?= $heo['GioiTinh'] == 'D' ? 'text-blue-600' : 'text-pink-600' ?> font-bold">
                                <?= $heo['GioiTinh'] == 'D' ? 'Đực' : 'Cái' ?>
                            </span>
                        </div>
                        <div>
                            <span class="font-semibold text-gray-700">Ngày Sinh:</span>
                            <span class="ml-3"><?= $heo['NgaySinh'] ? date('d/m/Y', strtotime($heo['NgaySinh'])) : 'Chưa rõ' ?></span>
                        </div>
                        <div>
                            <span class="font-semibold text-gray-700">Cân Nặng Hiện Tại:</span>
                            <span class="ml-3 font-bold"><?= number_format($heo['CanNangHienTai'] ?? 0, 1) ?> kg</span>
                        </div>
                        <div>
                            <span class="font-semibold text-gray-700">Chuồng:</span>
                            <span class="ml-3"><?= htmlspecialchars($heo['ViTriChuong'] ?? 'Chưa phân') ?></span>
                        </div>
                        <?php if ($heo['MaBo'] || $heo['MaMe']): ?>
                            <div class="md:col-span-2">
                                <span class="font-semibold text-gray-700">Con của:</span>
                                <span class="ml-3">
                                    <?= $heo['MaBo'] ? "Bố: <strong>{$heo['MaBo']}</strong>" : '' ?>
                                    <?= $heo['MaBo'] && $heo['MaMe'] ? ' - ' : '' ?>
                                    <?= $heo['MaMe'] ? "Mẹ: <strong>{$heo['MaMe']}</strong>" : '' ?>
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Form xác nhận xóa -->
                <form action="" method="post" class="text-center">
                    <div class="flex justify-center gap-6">
                        <a href="index.php?url=heo" 
                           class="px-8 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-xl font-semibold transition transform hover:scale-105">
                            <i class="fas fa-times mr-2"></i> Hủy bỏ
                        </a>
                        <button type="submit" name="confirm_delete" value="1"
                                class="px-8 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl font-semibold transition transform hover:scale-105 flex items-center">
                            <i class="fas fa-trash-alt mr-2"></i> Xóa vĩnh viễn heo này
                        </button>
                    </div>
                </form>
            <?php else: ?>
                <div class="text-center py-12">
                    <p class="text-xl text-red-600 font-medium">Không tìm thấy heo để xóa!</p>
                    <a href="index.php?url=heo" class="mt-6 inline-block px-8 py-3 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition">
                        Quay lại danh sách
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>