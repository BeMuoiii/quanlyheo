<?php if (!empty($error_message)): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
        <strong class="font-bold">Có lỗi xảy ra!</strong>
        <span class="block sm:inline"><?php echo $error_message; ?></span>
    </div>
<?php endif; ?>
<?php

// Tệp: admin/views/cannang/add.php
// CHỈ GIỮ LẠI CÁC DÒNG NÀY ĐỂ BẮT ĐẦU FILE
include __DIR__ . '/../layouts/header.php';
include __DIR__ . '/../layouts/sidebar.php';
?>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">


<div class="ml-64 p-8 min-h-screen">
    <div class="max-w-xl mx-auto">
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <div class="flex items-center mb-8">
                <a href="index.php?url=cannang" class="mr-4 text-gray-600 hover:text-gray-800">
                    <i class="fas fa-arrow-left text-2xl"></i>
                </a>
                <h1 class="text-3xl font-bold text-gray-800">Thêm Cân Nặng Mới</h1>
            </div>

            <form action="index.php?url=cannang/add" method="POST" class="space-y-6">

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Chọn Heo <span class="text-red-500">*</span>
                    </label>
                    <select name="MaHeo" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500">
                        <option value="">-- Chọn mã heo --</option>
                        <?php foreach ($danhSachHeo as $heo): ?>
                            <option value="<?= htmlspecialchars($heo['MaHeo']) ?>">
                                <?= htmlspecialchars($heo['MaHeo']) ?>
                                <?php if (!empty($heo['TenHeo'])) echo ' - ' . htmlspecialchars($heo['TenHeo']); ?>
                                <?php if (!empty($heo['Giong'])) echo ' (' . htmlspecialchars($heo['Giong']) . ')'; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (empty($danhSachHeo)): ?>
                        <p class="text-red-500 text-sm mt-2">
                            Chưa có heo nào! <a href="index.php?url=heo/add" class="underline">Thêm heo trước</a>
                        </p>
                    <?php endif; ?>
                </div>
                <?php if (empty($danhSachHeo)): ?>
                    <p class="text-sm text-red-500 mt-1">
                        Hệ thống chưa có con heo nào.
                        <a href="index.php?url=heo/add" class="underline font-bold">Thêm heo mới tại đây</a> trước khi nhập cân nặng.
                    </p>
                <?php endif; ?>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="NgayCan" class="block text-sm font-semibold text-gray-700 mb-2">Ngày Cân <span class="text-red-500">*</span></label>
                <input type="date" id="NgayCan" name="NgayCan" required class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label for="CanNang" class="block text-sm font-semibold text-gray-700 mb-2">Cân nặng (kg) <span class="text-red-500">*</span></label>
                <input type="number" id="CanNang" name="CanNang" min="0" step="0.1" placeholder="Ví dụ: 80.5" required class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-emerald-500">
            </div>
        </div>

        <div>
            <label for="GhiChu" class="block text-sm font-semibold text-gray-700 mb-2">Ghi chú thêm</label>
            <textarea id="GhiChu" name="GhiChu" rows="3" class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-emerald-500" placeholder="Thông tin về tình trạng sức khỏe hoặc lý do cân..."></textarea>
        </div>

        <div class="flex justify-end space-x-4 pt-6">
            <a href="index.php?url=cannang" class="px-8 py-3 border border-gray-300 rounded-xl hover:bg-gray-50 transition">Hủy</a>
            <button type="submit" class="px-8 py-3 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition shadow-lg">
                <i class="fas fa-save mr-2"></i> Lưu Cân Nặng
            </button>
        </div>
        </form>
    </div>
</div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>