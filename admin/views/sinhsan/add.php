<?php $title = "Ghi Nhận Phối Giống Mới"; ?>
<?php include __DIR__ . '/../layouts/header.php'; ?>
<?php include __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="ml-64 p-8 min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Ghi Nhận Phối Giống Mới</h1>
            <a href="index.php?url=sinhsan" class="text-gray-600 hover:text-gray-800 transition">
                <i class="fas fa-arrow-left text-2xl"></i>
            </a>
        </div>

        <!-- Thông báo thành công -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-6 py-4 rounded-xl mb-6 flex items-center">
                <i class="fas fa-check-circle text-2xl mr-3"></i>
                <?= $_SESSION['success'];
                unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <!-- Thông báo lỗi -->
        <?php if (!empty($errors ?? [])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-xl mb-6">
                <i class="fas fa-exclamation-triangle text-2xl mr-3 align-middle"></i>
                <ul class="list-disc list-inside">
                    <?php foreach ($errors as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="" method="post" class="bg-white rounded-2xl shadow-xl p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                <!-- Chọn Heo Nái -->
                <div>
                    <label class="block text-gray-700 font-medium mb-2">
                        Heo Nái <span class="text-red-500">*</span>
                    </label>
                    <select name="MaHeoNai" required class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">-- Chọn heo nái --</option>
                        <?php foreach ($dsHeoNai as $nai): ?>
                            <option value="<?= $nai['MaHeo'] ?>"
                                <?= (($_POST['MaHeoNai'] ?? '') == $nai['MaHeo']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($nai['MaHeo']) ?>
                                <?= $nai['CanNangHienTai'] ? ' - ' . $nai['CanNangHienTai'] . 'kg' : '' ?>
                                <?= $nai['ViTriChuong'] ? ' - ' . $nai['ViTriChuong'] : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Chọn Heo Đực -->
                <div>
                    <label class="block text-gray-700 font-medium mb-2">
                        Heo Đực <span class="text-red-500">*</span>
                    </label>
                    <select name="MaHeoDuc" required class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">-- Chọn heo đực --</option>
                        <?php foreach ($dsHeoDuc as $duc): ?>
                            <option value="<?= $duc['MaHeo'] ?>"
                                <?= (($_POST['MaHeoDuc'] ?? '') == $duc['MaHeo']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($duc['MaHeo']) ?>
                                <?= $duc['CanNangHienTai'] ? ' - ' . $duc['CanNangHienTai'] . 'kg' : '' ?>
                                <?= $duc['ViTriChuong'] ? ' - ' . $duc['ViTriChuong'] : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Ngày phối giống -->
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Ngày Phối Giống <span class="text-red-500">*</span></label>
                    <input type="date" name="NgayPhoi" id="inputNgayPhoi"
                        value="<?= date('Y-m-d') ?>" required
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none">
                </div>

                <!-- Nhân viên thực hiện -->
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Nhân Viên Thực Hiện</label>
                    <select name="MaNVThucHien" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-emerald-500">
                        <option value="">-- Không ghi nhận --</option>
                        <?php foreach ($dsNhanVien as $nv): ?>
                            <option value="<?= $nv['MaNV'] ?>"
                                <?= (($_POST['MaNVThucHien'] ?? '') == $nv['MaNV']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($nv['HoTen']) ?> (<?= $nv['MaNV'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Trạng thái phối giống (mặc định đang theo dõi) -->
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Trạng Thái</label>
                    <select name="TrangThai" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-emerald-500">
                        <option value="DangTheoDoi" selected>Đang theo dõi (mặc định)</option>
                        <option value="ThatBai">Thất bại</option>
                    </select>
                </div>

                <!-- Dự sinh (tự động +114 ngày) -->
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Ngày Dự Sinh (tự động)</label>
                    <input type="text" id="displayNgayDuSinh" readonly
                        placeholder="Chọn ngày phối để xem"
                        class="w-full px-4 py-3 bg-gray-100 border rounded-lg text-emerald-700 font-bold outline-none">
                </div>

                <!-- Ghi chú -->
                <div class="md:col-span-2">
                    <label class="block text-gray-700 font-medium mb-2">Ghi Chú</label>
                    <textarea name="GhiChu" rows="4" placeholder="Phối tự nhiên hay thụ tinh nhân tạo, sức khỏe nái, tinh dịch..."
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-emerald-500 resize-none"><?= $_POST['GhiChu'] ?? '' ?></textarea>
                </div>
            </div>

            <!-- Nút hành động -->
            <div class="mt-10 flex justify-end gap-4">
                <a href="index.php?url=sinhsan"
                    class="px-8 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-xl font-semibold transition">
                    Hủy bỏ
                </a>
                <button type="submit"
                    class="px-8 py-3 bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 
                               text-white rounded-xl font-semibold transition transform hover:scale-105 flex items-center shadow-lg">
                    <i class="fas fa-heart mr-3"></i>
                    Ghi Nhận Phối Giống
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Tự động tính ngày dự sinh khi chọn ngày phối -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputNgayPhoi = document.getElementById('inputNgayPhoi');
        const displayNgayDuSinh = document.getElementById('displayNgayDuSinh');

        function tinhNgayDuSinh() {
            const val = inputNgayPhoi.value;
            if (val) {
                const ngayPhoi = new Date(val);
                // Cộng thêm 114 ngày (chu kỳ mang thai chuẩn của heo)
                ngayPhoi.setDate(ngayPhoi.getDate() + 114);

                // Định dạng ngày theo kiểu Việt Nam: DD/MM/YYYY
                const d = String(ngayPhoi.getDate()).padStart(2, '0');
                const m = String(ngayPhoi.getMonth() + 1).padStart(2, '0');
                const y = ngayPhoi.getFullYear();

                displayNgayDuSinh.value = `${d}/${m}/${y}`;
            } else {
                displayNgayDuSinh.value = "Chọn ngày phối để xem";
            }
        }

        // Chạy tính toán ngay khi trang vừa load xong (nếu đã có ngày sẵn)
        tinhNgayDuSinh();

        // Lắng nghe sự kiện thay đổi ngày phối
        inputNgayPhoi.addEventListener('change', tinhNgayDuSinh);
    });
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>