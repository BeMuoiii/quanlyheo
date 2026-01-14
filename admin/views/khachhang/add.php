<?php $title = "Thêm khách hàng mới"; ?>
<?php include __DIR__ . '/../layouts/header.php'; ?>
<?php include __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="ml-64 p-8 min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-800 mb-8 text-center">Thêm khách hàng mới</h1>
        <a href="index.php?url=khachhang" class="text-gray-600 hover:text-gray-800 transition">
            <i class="fas fa-arrow-left text-2xl"></i>
        </a>

        <!-- Thông báo thành công (từ session) -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-6 py-4 rounded-xl mb-6 flex items-center">
                <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <span><?= htmlspecialchars($_SESSION['success']) ?></span>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <!-- Hiển thị lỗi validation -->
        <?php if (!empty($errors)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-xl mb-6">
                <div class="flex items-start">
                    <svg class="w-5 h-5 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                    <ul class="list-disc list-inside space-y-1">
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>

        <form action="" method="post" class="bg-white rounded-2xl shadow-xl p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Mã Khách Hàng tự động (chỉ số thuần: 1, 2, 3...) -->
                <div>
                    <label class="block text-gray-700 font-medium mb-2">
                        Mã Khách Hàng <span class="text-sm text-gray-500">(Hệ thống tự cấp)</span>
                    </label>
                    <input type="text"
                        value="<?= htmlspecialchars($autoMaKH ?? '1') ?>"
                        readonly
                        class="w-full px-4 py-3 border rounded-lg bg-gray-100 text-emerald-700 font-bold text-2xl cursor-not-allowed text-center">
                </div>

                <!-- Họ tên -->
                <div>
                    <label class="block text-gray-700 font-medium mb-2">
                        Họ tên khách hàng <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="TenKH" required
                        value="<?= htmlspecialchars($data['TenKH'] ?? '') ?>"
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-emerald-500">
                </div>

                <!-- SĐT -->
                <div>
                    <label class="block text-gray-700 font-medium mb-2">
                        Số điện thoại <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="SDT" required maxlength="10"
                        pattern="0[3-9][0-9]{8}"
                        title="Số di động Việt Nam 10 số"
                        value="<?= htmlspecialchars($data['SDT'] ?? '') ?>"
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-emerald-500">
                </div>

                <!-- Giới tính -->
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Giới tính</label>
                    <div class="flex items-center space-x-6">
                        <?php $gt = $data['GioiTinh'] ?? 'Nam'; ?>
                        <label class="inline-flex items-center">
                            <input type="radio" name="GioiTinh" value="Nam" <?= $gt === 'Nam' ? 'checked' : '' ?> class="form-radio text-emerald-600">
                            <span class="ml-2">Nam</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="GioiTinh" value="Nữ" <?= $gt === 'Nữ' ? 'checked' : '' ?> class="form-radio text-emerald-600">
                            <span class="ml-2">Nữ</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="GioiTinh" value="Khác" <?= $gt === 'Khác' ? 'checked' : '' ?> class="form-radio text-emerald-600">
                            <span class="ml-2">Khác</span>
                        </label>
                    </div>
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Email</label>
                    <input type="email" name="Email"
                        value="<?= htmlspecialchars($data['Email'] ?? '') ?>"
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-emerald-500">
                </div>

                <!-- Ngày sinh -->
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Ngày sinh</label>
                    <input type="date" name="NgaySinh"
                        value="<?= htmlspecialchars($data['NgaySinh'] ?? '') ?>"
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-emerald-500">
                </div>

                <!-- Địa chỉ -->
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Địa chỉ</label>
                    <input type="text" name="DiaChi"
                        value="<?= htmlspecialchars($data['DiaChi'] ?? '') ?>"
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-emerald-500">
                </div>

                <!-- Nhân viên phụ trách -->
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Nhân viên phụ trách</label>
                    <select name="MaNVPhuTrach" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-emerald-500">
                        <option value="">-- Chưa phân công --</option>
                        <?php foreach ($nhanvien as $nv): ?>
                            <option value="<?= $nv['MaNV'] ?>"
                                <?= ($data['MaNVPhuTrach'] ?? '') == $nv['MaNV'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($nv['HoTen']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="mt-10 flex justify-center gap-6">
                <button type="submit"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white px-8 py-4 rounded-xl font-semibold transition transform hover:scale-105 flex items-center">
                    <i class="fas fa-user-plus mr-3"></i> Thêm khách hàng
                </button>
                <a href="index.php?url=khachhang"
                    class="bg-gray-500 hover:bg-gray-600 text-white px-8 py-4 rounded-xl font-semibold transition flex items-center">
                    <i class="fas fa-arrow-left mr-3"></i> Quay lại danh sách
                </a>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>