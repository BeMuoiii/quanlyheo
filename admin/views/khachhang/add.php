<?php $title = "Thêm khách hàng mới"; ?>
<?php include __DIR__ . '/../layouts/header.php'; ?>
<?php include __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="ml-64 p-8 min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Thêm khách hàng mới</h1>
        <a href="index.php?url=heo" class="text-gray-600 hover:text-gray-800 transition">
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
            <!-- CSRF Token (bắt buộc - bạn cần tạo ở header hoặc middleware) -->
            <!-- Ví dụ tạo token: if(empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); -->
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Họ tên -->
                <div>
                    <label class="block text-gray-700 font-medium mb-2">
                        Họ tên khách hàng <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="TenKH" required
                        value="<?= htmlspecialchars($data['TenKH'] ?? '') ?>"
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>

                <!-- Số điện thoại -->
                <div>
                    <label class="block text-gray-700 font-medium mb-2">
                        Số điện thoại <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="SDT" required maxlength="10"
                        pattern="0[3|5|7|8|9][0-9]{8}"
                        title="Số điện thoại Việt Nam: bắt đầu bằng 03,05,07,08,09 và có đúng 10 số"
                        value="<?= htmlspecialchars($data['SDT'] ?? '') ?>"
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-emerald-500">
                </div>

                <!-- Giới tính -->
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Giới tính</label>
                    <div class="flex items-center space-x-8">
                        <?php $gioiTinh = $data['GioiTinh'] ?? 'D'; ?>
                        <label class="inline-flex items-center">
                            <input type="radio" name="GioiTinh" value="Nam" <?= $gioiTinh === 'Nam' ? 'checked' : '' ?>
                                class="form-radio text-emerald-600">
                            <span class="ml-2">Nam</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="GioiTinh" value="Nữ" <?= $gioiTinh === 'Nữ' ? 'checked' : '' ?>
                                class="form-radio text-emerald-600">
                            <span class="ml-2">Nữ</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="GioiTinh" value="D" <?= $gioiTinh === 'D' ? 'checked' : '' ?>
                                class="form-radio text-emerald-600">
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
                            <option value="<?= htmlspecialchars($nv['MaNV']) ?>"
                                <?= ($data['MaNVPhuTrach'] ?? '') == $nv['MaNV'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($nv['HoTen']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Chuồng nhập thường xuyên -->
                <div>
                    <label class="block text-gray-700 font-medium mb-2">
                        Nhập heo từ chuồng <span class="text-sm text-gray-500">(thường xuyên)</span>
                    </label>
                    <select name="ChuongNhap" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-emerald-500">
                        <option value="Thường">Thường (mặc định)</option>
                        <!-- Nếu bạn có danh sách chuồng động thì thay bằng foreach -->
                        <!-- Ví dụ: foreach ($danhSachChuong as $chuong) ... -->
                        <option value="Chuồng A" <?= ($data['ChuongNhap'] ?? 'Thường') === 'Chuồng A' ? 'selected' : '' ?>>Chuồng A</option>
                        <option value="Chuồng B" <?= ($data['ChuongNhap'] ?? 'Thường') === 'Chuồng B' ? 'selected' : '' ?>>Chuồng B</option>
                        <!-- Thêm các chuồng khác nếu cần -->
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Dùng để lọc nhanh khi xuất chuồng</p>
                </div>
            </div>

            <div class="mt-10 flex gap-4">
                <button type="submit"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white px-8 py-3 rounded-xl font-semibold transition transform hover:scale-105">
                    Thêm khách hàng
                </button>
                <a href="index.php?url=khachhang"
                    class="bg-gray-500 hover:bg-gray-600 text-white px-8 py-3 rounded-xl font-semibold transition">
                    Quay lại danh sách
                </a>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>