<?php
// admin/views/cannang/edit.php
include __DIR__ . '/../layouts/header.php';
include __DIR__ . '/../layouts/sidebar.php';
?>

<div class="ml-64 p-8 min-h-screen bg-gray-50">
    <div class="max-w-xl mx-auto bg-white p-8 rounded-2xl shadow-xl">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Chỉnh Sửa Cân Nặng</h1>

        <!-- Thông báo lỗi -->
        <?php if (!empty($error_message)): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
                <p class="font-bold">Lỗi!</p>
                <p><?= htmlspecialchars($error_message) ?></p>
            </div>
        <?php endif; ?>

        <form method="POST" action="index.php?url=cannang/edit/<?= htmlspecialchars($cannang_data['id'] ?? '') ?>">
            
            <!-- Mã Heo (không cho sửa) -->
            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Mã Heo <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <select disabled class="w-full px-4 py-3 border rounded-xl bg-gray-100 text-gray-600">
                        <?php foreach ($danhSachHeo as $heo): ?>
                            <option <?= ($heo['MaHeo'] == $cannang_data['MaHeo']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($heo['MaHeo']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <input type="hidden" name="MaHeo" value="<?= htmlspecialchars($cannang_data['MaHeo'] ?? '') ?>">
                    <div class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 pointer-events-none">
                        <i class="fas fa-lock"></i>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-1">Không thể thay đổi Mã Heo sau khi tạo.</p>
            </div>

            <div class="flex space-x-4 mb-5">
                <!-- Ngày cân -->
                <div class="w-1/2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Ngày Cân <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="NgayCan" required
                           value="<?= htmlspecialchars($cannang_data['NgayCan'] ?? '') ?>"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500">
                </div>

                <!-- Cân nặng -->
                <div class="w-1/2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Cân Nặng (kg) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="CanNang" step="0.01" required
                           value="<?= htmlspecialchars($cannang_data['CanNang'] ?? '') ?>"
                           placeholder="Ví dụ: 85.5"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>

            <!-- Ghi chú -->
            <div class="mb-8">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Ghi chú thêm</label>
                <textarea name="GhiChu" rows="4" placeholder="Tình trạng sức khỏe, lý do cân..."
                          class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500"><?= htmlspecialchars($cannang_data['GhiChu'] ?? '') ?></textarea>
            </div>

            <!-- Nút hành động -->
            <div class="flex justify-between items-center">
                <a href="index.php?url=cannang" 
                   class="px-6 py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-100 transition">
                    <i class="fas fa-arrow-left mr-2"></i> Hủy
                </a>
                <button type="submit" 
                        class="px-6 py-3 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition shadow-lg">
                    <i class="fas fa-save mr-2"></i> Cập Nhật
                </button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>