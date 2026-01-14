<?php $title = "Chỉnh Sửa Ghi Nhận Phối Giống"; ?>
<?php include __DIR__ . '/../layouts/header.php'; ?>
<?php include __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="ml-64 p-8 min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Chỉnh Sửa Phối Giống</h1>
            <a href="index.php?url=sinhsan" class="text-gray-600 hover:text-gray-800 transition">
                <i class="fas fa-arrow-left text-2xl"></i>
            </a>
        </div>

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
            <input type="hidden" name="SinhSan" value="<?= $data['SinhSan'] ?>">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Heo Nái <span class="text-red-500 font-bold">+</span></label>
                    <select name="MaHeoNai" required class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none">
                        <?php foreach ($dsHeoNai as $nai): ?>
                            <option value="<?= $nai['MaHeo'] ?>" <?= ($data['MaHeoNai'] == $nai['MaHeo']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($nai['MaHeo']) ?>
                                <?= $nai['CanNangHienTai'] ? ' (' . $nai['CanNangHienTai'] . 'kg)' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Heo Đực <span class="text-red-500 font-bold">+</span></label>
                    <select name="MaHeoDuc" required class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none">
                        <?php foreach ($dsHeoDuc as $duc): ?>
                            <option value="<?= $duc['MaHeo'] ?>" <?= ($data['MaHeoDuc'] == $duc['MaHeo']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($duc['MaHeo']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Ngày Phối Giống <span class="text-red-500 font-bold">+</span></label>
                    <input type="date" name="NgayPhoi" id="inputNgayPhoi"
                        value="<?= date('Y-m-d', strtotime($data['NgayPhoi'])) ?>" required
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none">
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Nhân Viên Thực Hiện</label>
                    <select name="MaNVThucHien" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-emerald-500">
                        <option value="">-- Không ghi nhận --</option>
                        <?php foreach ($dsNhanVien as $nv): ?>
                            <option value="<?= $nv['MaNV'] ?>" <?= ($data['MaNVThucHien'] == $nv['MaNV']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($nv['HoTen']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Trạng Thái</label>
                    <select name="TrangThai" id="selectTrangThai" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-emerald-500">
                        <option value="DangTheoDoi" <?= ($data['TrangThai'] == 'DangTheoDoi') ? 'selected' : '' ?>>Đang theo dõi</option>
                        <option value="ThatBai" <?= ($data['TrangThai'] == 'ThatBai') ? 'selected' : '' ?>>Thất bại</option>
                        <option value="ThanhCong" <?= ($data['TrangThai'] == 'ThanhCong') ? 'selected' : '' ?>>Đã đẻ (Thành công)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Ngày Dự Sinh (tự động)</label>
                    <input type="text" id="displayNgayDuSinh" readonly
                        class="w-full px-4 py-3 bg-gray-100 border rounded-lg text-emerald-700 font-bold outline-none">
                </div>


                <div id="groupGhiNhanDe"> <label class="block text-gray-700 font-medium mb-2">Ngày Đẻ Thực Tế <span class="text-red-500 font-bold">*</span></label>
                    <input type="date" name="NgayDeThucTe" id="inputNgayDe"
                        value="<?= !empty($data['NgayDeThucTe']) ? date('Y-m-d', strtotime($data['NgayDeThucTe'])) : '' ?>"
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                </div>


                <div class="md:col-span-2">
                    <label class="block text-gray-700 font-medium mb-2">Ghi Chú</label>
                    <textarea name="GhiChu" rows="4" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-emerald-500 resize-none"><?= htmlspecialchars($data['GhiChu'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="mt-10 flex justify-end gap-4">
                <a href="index.php?url=sinhsan" class="px-8 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-xl font-semibold transition">Hủy bỏ</a>
                <button type="submit" class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold transition transform hover:scale-105 flex items-center shadow-lg">
                    <i class="fas fa-save mr-2"></i> Lưu Cập Nhật
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Khai báo các phần tử
        const selectTrangThai = document.getElementById('selectTrangThai');
        const groupGhiNhanDe = document.getElementById('groupGhiNhanDe');
        const inputNgayDe = document.getElementById('inputNgayDe');
        const inputNgayPhoi = document.getElementById('inputNgayPhoi');
        const displayNgayDuSinh = document.getElementById('displayNgayDuSinh');

        // 2. Hàm xử lý hiển thị phần ghi nhận đẻ
        function xuLyTrangThai() {
            if (selectTrangThai.value === 'ThanhCong') {
                // Hiển thị khung ghi nhận đẻ
                groupGhiNhanDe.classList.remove('hidden');
                inputNgayDe.required = true;
                inputNgayDe.classList.add('border-blue-500', 'bg-blue-50');
            } else {
                // Ẩn khung ghi nhận đẻ
                groupGhiNhanDe.classList.add('hidden');
                inputNgayDe.required = false;
                inputNgayDe.classList.remove('border-blue-500', 'bg-blue-50');
            }
        }

        // 3. Hàm tính ngày dự sinh (114 ngày)
        function tinhNgayDuSinh() {
            const val = inputNgayPhoi.value;
            if (val) {
                const ngayPhoi = new Date(val);
                ngayPhoi.setDate(ngayPhoi.getDate() + 114);

                const d = String(ngayPhoi.getDate()).padStart(2, '0');
                const m = String(ngayPhoi.getMonth() + 1).padStart(2, '0');
                const y = ngayPhoi.getFullYear();

                displayNgayDuSinh.value = d + '/' + m + '/' + y;
            } else {
                displayNgayDuSinh.value = 'Chưa có ngày phối';
            }
        }

        // 4. Kiểm tra logic ngày đẻ (Không cho phép đẻ trước khi phối)
        inputNgayDe.addEventListener('change', function() {
            if (inputNgayPhoi.value && this.value) {
                const ngayPhoi = new Date(inputNgayPhoi.value);
                const ngayDe = new Date(this.value);

                if (ngayDe < ngayPhoi) {
                    alert('Lỗi: Ngày đẻ thực tế không thể trước ngày phối giống!');
                    this.value = '';
                }
            }
        });

        // 5. Đăng ký sự kiện
        selectTrangThai.addEventListener('change', xuLyTrangThai);
        inputNgayPhoi.addEventListener('change', tinhNgayDuSinh);

        // 6. Chạy ngay khi tải trang để khớp dữ liệu cũ
        xuLyTrangThai();
        tinhNgayDuSinh();
    });
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>