<?php $title = "Tạo Phiếu Xuất Chuồng Mới"; ?>
<?php include __DIR__ . '/../layouts/header.php'; ?>
<?php include __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="ml-64 p-8 min-h-screen bg-gray-50">
    <div class="max-w-5xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-4xl font-bold text-gray-800">Tạo Phiếu Xuất Chuồng Mới</h1>
            <a href="index.php?url=xuatchuong" class="text-gray-600 hover:text-gray-800 transition">
                <i class="fas fa-arrow-left text-3xl"></i>
            </a>
        </div>

        <!-- Thông báo thành công -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-6 py-4 rounded-xl mb-6 flex items-center shadow-md">
                <i class="fas fa-check-circle text-2xl mr-3"></i>
                <span class="font-bold"><?= $_SESSION['success'];
                                        unset($_SESSION['success']); ?></span>
            </div>
        <?php endif; ?>

        <!-- Thông báo lỗi -->
        <?php if (!empty($errors ?? [])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-xl mb-6 shadow-md">
                <i class="fas fa-exclamation-triangle text-2xl mr-3"></i>
                <ul class="font-bold">Có lỗi xảy ra:</span>
                    <ul class="list-disc list-inside mt-2">
                        <?php foreach ($errors as $e): ?>
                            <li><?= htmlspecialchars($e) ?></li>
                        <?php endforeach; ?>
                    </ul>
            </div>
        <?php endif; ?>

        <form action="" method="post" class="bg-white rounded-2xl shadow-2xl p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

                <!-- Chọn Heo -->
                <div class="lg:col-span-1">
                    <label class="block text-gray-700 font-bold mb-2">
                        Mã Heo <span class="text-red-500">*</span>
                    </label>
                    <select name="MaHeo" id="MaHeo" required
                        class="w-full px-5 py-4 border-2 rounded-xl focus:ring-4 focus:ring-emerald-200 focus:border-emerald-600 transition text-lg">
                        <option value="">-- Chọn heo để xuất --</option>
                        <?php foreach ($dsHeoChuaXuat ?? [] as $heo): ?>
                            <option value="<?= $heo['MaHeo'] ?>"
                                <?= ($_POST['MaHeo'] ?? '') == $heo['MaHeo'] ? 'selected' : '' ?>
                                data-cannang="<?= $heo['CanNangHienTai'] ?? 0 ?>">
                                <?= htmlspecialchars($heo['MaHeo']) ?>
                                <?= $heo['CanNangHienTai'] ? " - {$heo['CanNangHienTai']}kg" : '' ?>
                                <?= $heo['ViTriChuong'] ? " - {$heo['ViTriChuong']}" : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Cân nặng lúc xuất -->
                <div>
                    <label class="block text-gray-700 font-bold mb-2">
                        Cân Nặng Xuất (kg) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" step="0.1" name="CanNangXuat" id="CanNangXuat" required
                        value="<?= $_POST['CanNangXuat'] ?? '' ?>"
                        placeholder="VD: 108.5"
                        class="w-full px-5 py-4 border-2 rounded-xl focus:ring-4 focus:ring-emerald-200 focus:border-emerald-600 text-lg">
                </div>

                <!-- Số lượng (thường là 1 con) -->
                <div>
                    <label class="block text-gray-700 font-bold mb-2">
                        Số Lượng (con) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="SoLuong" min="1" required
                        value="<?= $_POST['SoLuong'] ?? '1' ?>"
                        class="w-full px-5 py-4 border-2 rounded-xl focus:ring-4 focus:ring-emerald-200 focus:border-emerald-600 text-lg">
                </div>

                <!-- Đơn giá -->
                <div>
                    <label class="block text-gray-700 font-bold mb-2">
                        Đơn Giá (VNĐ/kg) <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="DonGia" id="DonGia" required
                        value="<?= $_POST['DonGia'] ?? '' ?>"
                        placeholder="75.000"
                        class="w-full px-5 py-4 border-2 rounded-xl focus:ring-4 focus:ring-emerald-200 focus:border-emerald-600 text-lg money-format">
                </div>

                <!-- Ngày xuất -->
                <div>
                    <label class="block text-gray-700 font-bold mb-2">
                        Ngày giờ xuất <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local"
                        name="NgayXuat"
                        required
                        value="<?= $_POST['NgayXuat'] ?? date('Y-m-d\TH:i') ?>"
                        class="w-full px-5 py-4 border-2 rounded-xl focus:ring-4 focus:ring-emerald-200 focus:border-emerald-600 text-lg">
                </div>

                <!-- Tổng tiền (tự động) -->
                <div class="lg:col-span-1">
                    <label class="block text-gray-700 font-bold mb-2">Tổng Tiền Thu Về</label>
                    <div class="bg-gradient-to-r from-emerald-600 to-green-700 text-white text-3xl font-bold 
                                px-8 py-6 rounded-2xl text-right shadow-xl">
                        <span id="tongtien">0</span> <sup>đ</sup>
                    </div>
                </div>

                <!-- Khách hàng -->
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Khách Hàng</label>
                    <select name="MaKH" class="w-full px-5 py-4 border-2 rounded-xl focus:ring-4 focus:ring-emerald-200">
                        <option value="">-- Khách lẻ / Không ghi nhận --</option>
                        <?php foreach ($dsKhachHang ?? [] as $kh): ?>
                            <option value="<?= $kh['MaKH'] ?>"
                                <?= ($_POST['MaKH'] ?? '') == $kh['MaKH'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($kh['TenKH']) ?> (<?= $kh['DienThoai'] ?? '' ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Lý do xuất -->
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Lý Do Xuất</label>
                    <select name="LyDoXuat" class="w-full px-5 py-4 border-2 rounded-xl focus:ring-4 focus:ring-emerald-200">
                        <option value="Bán thịt" <?= ($_POST['LyDoXuat'] ?? '') == 'Bán thịt' ? 'selected' : '' ?>>Bán thịt</option>
                        <option value="Bán giống" <?= ($_POST['LyDoXuat'] ?? '') == 'Bán giống' ? 'selected' : '' ?>>Bán giống</option>
                        <option value="Loại thải" <?= ($_POST['LyDoXuat'] ?? '') == 'Loại thải' ? 'selected' : '' ?>>Loại thải</option>
                        <option value="Chuyển trại" <?= ($_POST['LyDoXuat'] ?? '') == 'Chuyển trại' ? 'selected' : '' ?>>Chuyển trại</option>
                    </select>
                </div>

                <!-- Nhân viên thực hiện -->
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Người Thực Hiện</label>
                    <select name="MaNVThucHien" class="w-full px-5 py-4 border-2 rounded-xl focus:ring-4 focus:ring-emerald-200">
                        <option value="">-- Không ghi nhận --</option>
                        <?php foreach ($dsNhanVien ?? [] as $nv): ?>
                            <option value="<?= $nv['MaNV'] ?>"
                                <?= ($_POST['MaNVThucHien'] ?? '') == $nv['MaNV'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($nv['HoTen']) ?> (<?= $nv['MaNV'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Ghi chú -->
                <div class="lg:col-span-3">
                    <label class="block text-gray-700 font-bold mb-2">Ghi Chú</label>
                    <textarea name="GhiChu" rows="4" placeholder="VD: Bán cho anh Hùng Lai Châu, heo đẹp, không bệnh..."
                        class="w-full px-5 py-4 border-2 rounded-xl focus:ring-4 focus:ring-emerald-200 resize-none text-lg"><?= $_POST['GhiChu'] ?? '' ?></textarea>
                </div>
            </div>

            <!-- Nút -->
            <div class="mt-12 flex justify-end gap-6">
                <a href="index.php?url=xuatchuong"
                    class="px-10 py-4 bg-gray-600 hover:bg-gray-700 text-white font-bold rounded-xl transition transform hover:scale-105">
                    Hủy Bỏ
                </a>
                <button type="submit"
                    class="px-12 py-4 bg-gradient-to-r from-emerald-600 to-emerald-800 hover:from-emerald-700 hover:to-emerald-900 
                               text-white font-bold rounded-xl shadow-2xl transition transform hover:scale-105 flex items-center gap-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Xuất Chuồng Ngay
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Tính tiền realtime + format tiền + điền cân nặng tự động -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const maHeoSelect = document.getElementById('MaHeo');
        const canNangInput = document.getElementById('CanNangXuat');
        const donGiaInput = document.getElementById('DonGia');
        const soLuongInput = document.querySelector('[name="SoLuong"]');
        const tongTienSpan = document.getElementById('tongtien');

        function formatMoney(input) {
            let value = input.value.replace(/\D/g, '');
            if (value) {
                value = parseInt(value).toLocaleString('vi-VN');
            }
            input.value = value;
        }

        function tinhTongTien() {
            const canNang = parseFloat(canNangInput.value.replace(',', '.')) || 0;
            const donGia = parseInt(donGiaInput.value.replace(/\D/g, '')) || 0;
            const soLuong = parseInt(soLuongInput.value) || 1;
            const tong = canNang * donGia * soLuong;
            tongTienSpan.textContent = tong.toLocaleString('vi-VN');
        }

        // Khi chọn heo → tự điền cân nặng
        maHeoSelect?.addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            const canNang = selected.dataset.cannang;
            if (canNang > 0) {
                canNangInput.value = canNang;
            }
            tinhTongTien();
        });

        // Format tiền + tính realtime
        donGiaInput?.addEventListener('input', function() {
            formatMoney(this);
            tinhTongTien();
        });

        [canNangInput, soLuongInput].forEach(el => {
            el?.addEventListener('input', tinhTongTien);
        });

        // Tính lần đầu khi load (nếu có dữ liệu)
        tinhTongTien();
    });
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>