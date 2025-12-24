<?php $title = "Sửa Thông Tin Heo: " . ($heo['MaHeo'] ?? ''); ?>
<?php include __DIR__ . '/../layouts/header.php'; ?>
<?php include __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="ml-64 p-8 min-h-screen bg-gray-50">
    <div class="max-w-5xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Sửa Thông Tin Heo: <span class="text-emerald-600"><?= htmlspecialchars($heo['MaHeo']) ?></span></h1>
            <a href="index.php?url=heo" class="text-gray-600 hover:text-gray-800"><i class="fas fa-arrow-left text-2xl"></i></a>
        </div>

        <?php if (!empty($error_message)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-xl mb-6">
                <?= htmlspecialchars($error_message) ?>
            </div>
        <?php endif; ?>

        <form action="" method="post" class="bg-white rounded-2xl shadow-xl p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Các field giống add.php, chỉ thêm value -->
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Mã Heo</label>
                    <input type="text" value="<?= htmlspecialchars($heo['MaHeo']) ?>" disabled class="w-full px-4 py-3 bg-gray-100 border rounded-lg">
                    <input type="hidden" name="MaHeo" value="<?= $heo['MaHeo'] ?>">
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Giống Heo</label>
                    <select name="GiongHeo" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-emerald-500">
                        <option value="Heo rừng lai" <?= (($_POST['GiongHeo'] ?? '') == 'Heo rừng lai') ? 'selected' : '' ?>>
                            Heo rừng lai (F1, F2...)
                        </option>
                        <option value="Heo rừng thuần" <?= (($_POST['GiongHeo'] ?? '') == 'Heo rừng thuần') ? 'selected' : '' ?>>
                            Heo rừng thuần chủng
                        </option>
                        <option value="Heo rừng thương phẩm" <?= (($_POST['GiongHeo'] ?? '') == 'Heo rừng thương phẩm') ? 'selected' : '' ?>>
                            Heo rừng thương phẩm
                        </option>
                    </select>
                </div>
                <!-- Giới tính -->
                <div class="md:col-span-2">
                    <label class="block text-gray-700 font-medium mb-3">Giới Tính</label>
                    <div class="flex gap-10">
                        <label class="flex items-center"><input type="radio" name="GioiTinh" value="D" <?= ($heo['GioiTinh'] == 'D') ? 'checked' : '' ?> class="text-emerald-600"> <span class="ml-3 text-blue-600 font-medium">Đực</span></label>
                        <label class="flex items-center"><input type="radio" name="GioiTinh" value="C" <?= ($heo['GioiTinh'] == 'C') ? 'checked' : '' ?> class="text-emerald-600"> <span class="ml-3 text-pink-600 font-medium">Cái</span></label>
                    </div>
                </div>
                <!-- Các field khác giống add.php, chỉ thay value -->
                <div><label class="block text-gray-700 font-medium mb-2">Ngày Sinh</label>
                    <input type="date" name="NgaySinh" value="<?= $heo['NgaySinh'] ?? '' ?>" required class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-emerald-500">
                </div>
                <div><label class="block text-gray-700 font-medium mb-2">Cân Nặng Hiện Tại (kg)</label>
                    <input type="number" step="0.1" name="CanNangHienTai" value="<?= $heo['CanNangHienTai'] ?? '' ?>" required class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-emerald-500">
                </div>
                <div><label class="block text-gray-700 font-medium mb-2">Vị Trí Chuồng</label>
                    <input type="text" name="ViTriChuong" value="<?= htmlspecialchars($heo['ViTriChuong'] ?? '') ?>" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-emerald-500">
                </div>
                <div><label class="block text-gray-700 font-medium mb-2">Trạng Thái</label>
                    <select name="TrangThaiHeo" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-emerald-500">
                        <option value="Bình thường" <?= ($heo['TrangThaiHeo'] ?? '') == 'Bình thường' ? 'selected' : '' ?>>Bình thường</option>
                        <option value="Theo dõi" <?= ($heo['TrangThaiHeo'] ?? '') == 'Theo dõi' ? 'selected' : '' ?>>Theo dõi</option>
                        <option value="Cách ly" <?= ($heo['TrangThaiHeo'] ?? '') == 'Cách ly' ? 'selected' : '' ?>>Cách ly</option>
                    </select>
                </div>
                <!-- Bố / Mẹ -->
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Heo Bố</label>
                    <select name="MaBo" class="w-full px-4 py-3 border rounded-lg">
                        <option value="">-- Không có --</option>
                        <?php foreach ($heoDuc as $bo): ?>
                            <option value="<?= $bo['MaHeo'] ?>" <?= ($heo['MaBo'] ?? '') == $bo['MaHeo'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($bo['MaHeo']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Heo Mẹ</label>
                    <select name="MaMe" class="w-full px-4 py-3 border rounded-lg">
                        <option value="">-- Không có --</option>
                        <?php foreach ($heoCai as $me): ?>
                            <option value="<?= $me['MaHeo'] ?>" <?= ($heo['MaMe'] ?? '') == $me['MaHeo'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($me['MaHeo']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-gray-700 font-medium mb-2">Nguồn Gốc</label>
                    <input type="text" name="NguonGoc" value="<?= htmlspecialchars($heo['NguonGoc'] ?? '') ?>" class="w-full px-4 py-3 border rounded-lg">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-gray-700 font-medium mb-2">Ghi Chú</label>
                    <textarea name="GhiChu" rows="4" class="w-full px-4 py-3 border rounded-lg"><?= htmlspecialchars($heo['GhiChu'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="mt-10 flex justify-end gap-4">
                <a href="index.php?url=heo" class="px-8 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-xl font-semibold">Hủy</a>
                <button type="submit" class="px-8 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-semibold flex items-center">
                    <i class="fas fa-save mr-2"></i> Cập Nhật Thông Tin
                </button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>