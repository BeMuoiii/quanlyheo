<?php $title = "Thêm Heo Mới Vào Trại"; ?>
<?php include __DIR__ . '/../layouts/header.php'; ?>
<?php include __DIR__ . '/../layouts/sidebar.php'; ?>

<div id="toast-container" class="fixed bottom-5 right-5 z-[9999] flex flex-col gap-3"></div>

<div class="ml-64 p-8 min-h-screen bg-gray-50">
    <div class="max-w-5xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-xl font-bold text-gray-800">Thêm Heo Mới Vào Trại</h1>
            <a href="index.php?url=heo" class="text-gray-600 hover:text-gray-800 transition">
                <i class="fas fa-arrow-left text-2xl"></i>
            </a>
        </div>

        <form action="" method="post" class="bg-white rounded-2xl shadow-xl p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

               <div class="mb-4">
    <label class="block text-gray-700 font-medium mb-2">Mã Heo (Hệ thống cấp)</label>
    <input type="text" name="MaHeo" 
           value="<?= $autoMaHeo ?>" 
           readonly 
           class="w-full px-4 py-3 border rounded-lg bg-gray-100 text-gray-600 cursor-not-allowed outline-none font-bold"
           title="Mã này được hệ thống tự động đánh số">
</div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Giống Heo</label>
                    <select name="GiongHeo" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none">
                        <option value="Heo rừng lai" <?= (($_POST['GiongHeo'] ?? '') == 'Heo rừng lai') ? 'selected' : '' ?>>Heo rừng lai (F1, F2...)</option>
                        <option value="Heo rừng thuần" <?= (($_POST['GiongHeo'] ?? '') == 'Heo rừng thuần') ? 'selected' : '' ?>>Heo rừng thuần chủng</option>
                        <option value="Heo rừng thương phẩm" <?= (($_POST['GiongHeo'] ?? '') == 'Heo rừng thương phẩm') ? 'selected' : '' ?>>Heo rừng thương phẩm</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-gray-700 font-medium mb-3">Giới Tính <span class="text-red-500 font-bold">+</span></label>
                    <div class="flex gap-10">
                        <label class="flex items-center cursor-pointer group">
                            <input type="radio" name="GioiTinh" value="D" required <?= (!isset($_POST['GioiTinh']) || $_POST['GioiTinh'] == 'D') ? 'checked' : '' ?>
                                class="w-5 h-5 text-emerald-600 focus:ring-emerald-500">
                            <span class="ml-3 text-blue-600 font-medium text-lg group-hover:text-blue-700">Đực</span>
                        </label>
                        <label class="flex items-center cursor-pointer group">
                            <input type="radio" name="GioiTinh" value="C" <?= (isset($_POST['GioiTinh']) && $_POST['GioiTinh'] == 'C') ? 'checked' : '' ?>
                                class="w-5 h-5 text-emerald-600 focus:ring-emerald-500">
                            <span class="ml-3 text-pink-600 font-medium text-lg group-hover:text-pink-700">Cái</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Ngày Sinh / Nhập Chuồng <span class="text-red-500 font-bold">+</span></label>
                    <input type="date" name="NgaySinh" value="<?= htmlspecialchars($_POST['NgaySinh'] ?? date('Y-m-d')) ?>" required
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none">
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Cân Nặng Hiện Tại (kg) <span class="text-red-500 font-bold">+</span></label>
                    <input type="number" step="0.1" name="CanNangHienTai" value="<?= htmlspecialchars($_POST['CanNangHienTai'] ?? '') ?>" required
                        placeholder="VD: 15.5" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none">
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Vị Trí Chuồng</label>
                    <input type="text" name="ViTriChuong" value="<?= htmlspecialchars($_POST['ViTriChuong'] ?? '') ?>"
                        placeholder="VD: Khu A - Chuồng 01" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none">
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Trạng Thái</label>
                    <select name="TrangThaiHeo" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none">
                        <option value="Bình thường" <?= (($_POST['TrangThaiHeo'] ?? '') == 'Bình thường') ? 'selected' : '' ?>>Bình thường</option>
                        <option value="Theo dõi" <?= (($_POST['TrangThaiHeo'] ?? '') == 'Theo dõi') ? 'selected' : '' ?>>Theo dõi</option>
                        <option value="Cách ly" <?= (($_POST['TrangThaiHeo'] ?? '') == 'Cách ly') ? 'selected' : '' ?>>Cách ly</option>
                    </select>
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Heo Bố</label>
                    <select name="MaBo" class="w-full px-4 py-3 border rounded-lg outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="">-- Không có --</option>
                        <?php foreach ($heoDuc as $bo): ?>
                            <option value="<?= $bo['MaHeo'] ?>" <?= (($_POST['MaBo'] ?? '') == $bo['MaHeo']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($bo['MaHeo']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Heo Mẹ</label>
                    <select name="MaMe" class="w-full px-4 py-3 border rounded-lg outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="">-- Không có --</option>
                        <?php foreach ($heoCai as $me): ?>
                            <option value="<?= $me['MaHeo'] ?>" <?= (($_POST['MaMe'] ?? '') == $me['MaHeo']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($me['MaHeo']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-gray-700 font-medium mb-2">Nguồn Gốc</label>
                    <input type="text" name="NguonGoc" value="<?= htmlspecialchars($_POST['NguonGoc'] ?? '') ?>"
                        placeholder="VD: Nhập từ trại giống ABC hoặc tự nhân giống" class="w-full px-4 py-3 border rounded-lg outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-gray-700 font-medium mb-2">Ghi Chú</label>
                    <textarea name="GhiChu" rows="4" placeholder="Thông tin bổ sung..."
                        class="w-full px-4 py-3 border rounded-lg outline-none focus:ring-2 focus:ring-emerald-500"><?= htmlspecialchars($_POST['GhiChu'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="mt-10 flex justify-end gap-4">
                <a href="index.php?url=heo" class="px-8 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-xl font-semibold transition">
                    Hủy bỏ
                </a>
                <button type="submit" class="px-8 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-semibold transition transform hover:scale-105 flex items-center shadow-lg">
                    <i class="fas fa-piggy-bank mr-2"></i>
                    Thêm Heo Vào Hệ Thống
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.querySelector('select[name="GioiTinh"]').addEventListener('change', function() {
        const gioiTinh = this.value;
        const inputMaHeo = document.getElementById('MaHeo');

        // Gọi API lấy mã mới dựa trên giới tính vừa chọn
        fetch('index.php?url=heo/get_new_code&gioitinh=' + gioiTinh)
            .then(response => response.text())
            .then(data => {
                if (data) inputMaHeo.value = data.trim();
            });
    });

    function showToast(message, type = 'success') {
        const container = document.getElementById('toast-container');
        if (!container) return;

        const toast = document.createElement('div');
        const bgColor = type === 'success' ? 'bg-emerald-600' : 'bg-red-600';
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';

        toast.className = `transform transition-all duration-500 ease-in-out translate-x-full opacity-0 ${bgColor} text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center min-w-[320px]`;

        toast.innerHTML = `
            <div class="mr-3 bg-white/20 p-2 rounded-full">
                <i class="fas ${icon} text-xl"></i>
            </div>
            <div class="flex-1">
                <p class="font-bold text-sm">${type === 'success' ? 'Thành công' : 'Lỗi dữ liệu'}</p>
                <p class="text-xs opacity-90">${message}</p>
            </div>
            <button class="ml-4 hover:opacity-70 transition-opacity">
                <i class="fas fa-times"></i>
            </button>
        `;

        container.appendChild(toast);

        setTimeout(() => {
            toast.classList.remove('translate-x-full', 'opacity-0');
        }, 100);

        const close = () => {
            toast.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => toast.remove(), 500);
        };

        setTimeout(close, 4000);
        toast.querySelector('button').onclick = close;
    }

    // Hiển thị thông báo lỗi từ Controller (biến $error_message)
    <?php if (isset($error_message)): ?>
        window.addEventListener('DOMContentLoaded', () => {
            showToast("<?= addslashes($error_message) ?>", 'error');
        });
    <?php endif; ?>
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>