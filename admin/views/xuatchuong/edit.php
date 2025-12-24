<?php $title = "Sửa Giao Dịch Xuất Chuồng: " . ($xuatchuong['MaXuat'] ?? ''); ?>
<?php include __DIR__ . '/../layouts/header.php'; ?>
<?php include __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="ml-64 p-8 min-h-screen bg-gray-50">
    <div class="max-w-5xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">
                <i class="fas fa-edit text-orange-600 mr-2"></i> Sửa Giao Dịch:
                <span class="text-orange-600">#<?= htmlspecialchars($xuatchuong['MaXuat'] ?? 'N/A') ?></span>
            </h1>
            <a href="index.php?url=xuatchuong" class="text-gray-600 hover:text-gray-800">
                <i class="fas fa-arrow-left text-2xl"></i>
            </a>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-xl mb-6">
                <ul class="list-disc pl-5">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="" method="post" class="bg-white rounded-2xl shadow-xl p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Mã Giao Dịch</label>
                    <input type="text" value="<?= htmlspecialchars($xuatchuong['MaXuat']) ?>" disabled
                        class="w-full px-4 py-3 bg-gray-100 border rounded-lg shadow-sm">
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Ngày Xuất</label>
                    <input type="date" name="NgayXuat"
                        value="<?= htmlspecialchars($xuatchuong['NgayXuat'] ?? date('Y-m-d')) ?>" required
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-orange-500">
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Khách Hàng</label>
                    <select name="MaKH" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-orange-500">
                        <option value="">-- Chọn khách hàng --</option>
                        <?php foreach ($dsKhachHang as $kh): ?>
                            <option value="<?= $kh['MaKH'] ?>" <?= ($xuatchuong['MaKH'] == $kh['MaKH']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($kh['TenKH']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Chọn Heo Xuất</label>
                    <select name="MaHeo" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-orange-500">
                        <?php foreach ($dsHeoChuaXuat as $heo): ?>
                            <option value="<?= $heo['MaHeo'] ?>" <?= ($xuatchuong['MaHeo'] == $heo['MaHeo']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($heo['TenHeo']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Cân Nặng Tổng (kg)</label>
                    <input type="number" step="0.1" name="CanNangXuat" id="CanNangXuat"
                        value="<?= htmlspecialchars($xuatchuong['CanNangXuat']) ?>" required
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-orange-500">
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Đơn Giá (VNĐ/kg)</label>
                    <input type="number" name="DonGia" id="DonGia"
                        value="<?= (int)$xuatchuong['DonGia'] ?>" required
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-orange-500">
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Số Lượng (Con)</label>
                    <input type="number" name="SoLuong" id="SoLuong" value="<?= htmlspecialchars($xuatchuong['SoLuong']) ?>" required
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-orange-500">
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Nhân Viên Phụ Trách</label>
                    <select name="MaNVThucHien" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-orange-500">
                        <?php foreach ($dsNhanVien as $nv): ?>
                            <option value="<?= $nv['MaNV'] ?>" <?= ($xuatchuong['MaNVThucHien'] == $nv['MaNV']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($nv['HoTen']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Lý Do Xuất</label>
                    <input type="text" name="LyDoXuat" value="<?= htmlspecialchars($xuatchuong['LyDoXuat'] ?? 'Bán thịt') ?>"
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-orange-500">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-gray-700 font-medium mb-2">Ghi Chú</label>
                    <textarea name="GhiChu" rows="2" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-orange-500"><?= htmlspecialchars($xuatchuong['GhiChu'] ?? '') ?></textarea>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-gray-700 font-medium mb-2">Thành Tiền Dự Kiến</label>
                    <input type="text" id="ThanhTienHienThi"
                        value="<?= number_format($xuatchuong['ThanhTien']) ?> VNĐ" disabled
                        class="w-full px-4 py-3 bg-orange-50 border-orange-200 border rounded-lg font-bold text-orange-700 text-xl text-center">
                </div>
            </div>

            <div class="mt-10 flex justify-end gap-4">
                <a href="index.php?url=xuatchuong" class="px-8 py-3 bg-gray-400 text-white rounded-xl hover:bg-gray-500 transition">Hủy</a>
                <button type="submit" class="px-8 py-3 bg-orange-600 text-white rounded-xl shadow-lg hover:bg-orange-700 transition">
                    <i class="fas fa-save mr-2"></i> Lưu Cập Nhật
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const canNangInput = document.getElementById('CanNangXuat');
    const donGiaInput = document.getElementById('DonGia');
    const soLuongInput = document.getElementById('SoLuong');
    const thanhTienHienThi = document.getElementById('ThanhTienHienThi');

    function updateThanhTien() {
        const canNang = parseFloat(canNangInput.value) || 0;
        const donGia = parseFloat(donGiaInput.value) || 0;
        const soLuong = parseInt(soLuongInput.value) || 0;

        // Controller của bạn tính: CanNangXuat * DonGia * SoLuong
        const tong = canNang * donGia * soLuong;
        thanhTienHienThi.value = tong.toLocaleString('vi-VN') + " VNĐ";
    }

    canNangInput.addEventListener('input', updateThanhTien);
    donGiaInput.addEventListener('input', updateThanhTien);
    soLuongInput.addEventListener('input', updateThanhTien);
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>