<?php $title = "Ghi Nhận Phối Giống Mới"; ?>
<?php include __DIR__ . '/../layouts/header.php'; ?>
<?php include __DIR__ . '/../layouts/sidebar.php'; ?>

<div id="toast-container" class="fixed bottom-5 right-5 z-[9999] flex flex-col gap-3"></div>

<div class="ml-64 p-8 min-h-screen bg-gray-50">
    <div class="max-w-5xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-xl font-bold text-gray-800">Ghi Nhận Phối Giống Mới</h1>
            <a href="<?= $kh_id > 0 ? 'index.php?url=khachhang/phahe&kh_id=' . $kh_id : 'index.php?url=sinhsan' ?>"
                class="text-gray-600 hover:text-gray-800 transition">
                <i class="fas fa-arrow-left text-2xl"></i>
            </a>
        </div>

        <form action="" method="post" class="bg-white rounded-2xl shadow-xl p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Mã Phiếu Sinh Sản (Hệ thống cấp) - đơn giản giống form thêm heo -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Mã Phiếu Sinh Sản (Hệ thống cấp)</label>
                    <input type="text"
                        name="MaSinhSan"
                        value="<?= htmlspecialchars($autoMaSinhSan ?? '') ?>"
                        readonly
                        class="w-full px-4 py-3 border rounded-lg bg-gray-50 text-emerald-700 cursor-not-allowed outline-none font-bold"
                        title="Mã này được hệ thống tự động đánh số tăng dần">
                </div>

                <!-- Ngày Phối Giống -->
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Ngày Phối Giống <span class="text-red-500 font-bold">*</span></label>
                    <input type="date"
                        name="NgayPhoi"
                        id="inputNgayPhoi"
                        value="<?= htmlspecialchars($_POST['NgayPhoi'] ?? date('Y-m-d')) ?>"
                        max="<?= date('Y-m-d') ?>"
                        required
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none">
                </div>

                <!-- Heo Nái -->
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Heo Nái <span class="text-red-500 font-bold">*</span></label>
                    <select name="MaHeoNai" required class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none">
                        <option value="">-- Chọn heo nái --</option>
                        <?php foreach ($dsHeoNai as $nai): ?>
                            <option value="<?= htmlspecialchars($nai['MaHeo']) ?>"
                                <?= ($_POST['MaHeoNai'] ?? '') === $nai['MaHeo'] ? 'selected' : '' ?>>
                                #<?= htmlspecialchars($nai['MaHeo']) ?>
                                <?= $nai['CanNangHienTai'] ? ' - ' . number_format($nai['CanNangHienTai'], 1) . 'kg' : '' ?>
                                <?= !empty($nai['ViTriChuong']) ? ' - ' . htmlspecialchars($nai['ViTriChuong']) : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Heo Đực -->
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Heo Đực <span class="text-red-500 font-bold">*</span></label>
                    <select name="MaHeoDuc" required class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none">
                        <option value="">-- Chọn heo đực --</option>
                        <?php foreach ($dsHeoDuc as $duc): ?>
                            <option value="<?= htmlspecialchars($duc['MaHeo']) ?>"
                                <?= ($_POST['MaHeoDuc'] ?? '') === $duc['MaHeo'] ? 'selected' : '' ?>>
                                #<?= htmlspecialchars($duc['MaHeo']) ?>
                                <?= $duc['CanNangHienTai'] ? ' - ' . number_format($duc['CanNangHienTai'], 1) . 'kg' : '' ?>
                                <?= !empty($duc['ViTriChuong']) ? ' - ' . htmlspecialchars($duc['ViTriChuong']) : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Ngày Dự Sinh (tự động +114 ngày) -->
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Ngày Dự Sinh (tự động +114 ngày)</label>
                    <input type="text"
                        id="displayNgayDuSinh"
                        readonly
                        placeholder="Chọn ngày phối để xem"
                        class="w-full px-4 py-3 bg-orange-50 border rounded-lg text-orange-700 font-bold outline-none text-center">
                </div>

                <!-- Nhân Viên Thực Hiện -->
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Nhân Viên Thực Hiện</label>
                    <select name="MaNVThucHien" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none">
                        <option value="">-- Không ghi nhận --</option>
                        <?php foreach ($dsNhanVien as $nv): ?>
                            <option value="<?= $nv['MaNV'] ?>"
                                <?= ($_POST['MaNVThucHien'] ?? '') === $nv['MaNV'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($nv['HoTen']) ?> (<?= $nv['MaNV'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Ghi Chú -->
                <div class="md:col-span-2">
                    <label class="block text-gray-700 font-medium mb-2">Ghi Chú</label>
                    <textarea name="GhiChu" rows="4"
                        placeholder="Thông tin bổ sung về lần phối (tự nhiên/thụ tinh nhân tạo, sức khỏe nái...)"
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-emerald-500 resize-none"><?= htmlspecialchars($_POST['GhiChu'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="mt-10 flex justify-end gap-4">
                <a href="<?= $kh_id > 0 ? 'index.php?url=khachhang/phahe&kh_id=' . $kh_id : 'index.php?url=sinhsan' ?>"
                    class="px-8 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-xl font-semibold transition">
                    Hủy bỏ
                </a>
                <button type="submit"
                    class="px-8 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-semibold transition transform hover:scale-105 flex items-center shadow-lg">
                    <i class="fa-solid fa-heart mr-2"></i>
                    Ghi Nhận Phối Giống
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Tính ngày dự sinh (+114 ngày)
    const ngayPhoiInput = document.getElementById('inputNgayPhoi');
    const ngayDuSinhDisplay = document.getElementById('displayNgayDuSinh');

    function tinhNgayDuSinh() {
        const value = ngayPhoiInput.value.trim();
        if (!value) {
            ngayDuSinhDisplay.value = '';
            return;
        }

        const date = new Date(value);
        if (isNaN(date.getTime())) {
            ngayDuSinhDisplay.value = 'Ngày không hợp lệ';
            return;
        }

        date.setDate(date.getDate() + 114);

        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();

        ngayDuSinhDisplay.value = `${day}/${month}/${year}`;
    }

    if (ngayPhoiInput) {
        ngayPhoiInput.addEventListener('change', tinhNgayDuSinh);
    }

    document.addEventListener('DOMContentLoaded', tinhNgayDuSinh);

    // Toast notification (giống form heo)
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

    // Hiển thị thông báo lỗi từ controller (nếu có $error_message hoặc $errors)
    <?php if (isset($error_message) && $error_message): ?>
        window.addEventListener('DOMContentLoaded', () => {
            showToast("<?= addslashes($error_message) ?>", 'error');
        });
    <?php elseif (!empty($errors ?? [])): ?>
        window.addEventListener('DOMContentLoaded', () => {
            <?php foreach ($errors as $e): ?>
                showToast("<?= addslashes($e) ?>", 'error');
            <?php endforeach; ?>
        });
    <?php endif; ?>
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>