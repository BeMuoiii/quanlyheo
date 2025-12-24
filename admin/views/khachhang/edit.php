<?php $title = "Sửa khách hàng"; ?>
<?php include __DIR__ . '/../layouts/header.php'; ?>
<?php include __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="ml-64 p-8 min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto">
        <div class="mb-8">
            <nav class="flex text-gray-500 text-sm mb-2">
                <a href="index.php?url=khachhang" class="hover:text-emerald-600">Khách hàng</a>
                <span class="mx-2">/</span>
                <span class="text-gray-800 font-medium">Chỉnh sửa</span>
            </nav>
            <h1 class="text-3xl font-bold text-gray-800">Chỉnh sửa thông tin khách hàng</h1>
            <p class="text-gray-500 mt-1">Cập nhật thông tin chi tiết của khách hàng: <span class="text-emerald-600 font-semibold"><?= htmlspecialchars($khachhang['TenKH'] ?? '') ?></span></p>
        </div>

        <?php if (!empty($error_message)): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 px-6 py-4 rounded-xl mb-6 shadow-sm">
                <div class="flex items-center">
                    <i class="fa-solid fa-circle-exclamation mr-3"></i>
                    <span><?= htmlspecialchars($error_message) ?></span>
                </div>
            </div>
        <?php endif; ?>

        <form action="" method="post" class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
            <input type="hidden" name="MaKH" value="<?= $khachhang['MaKH'] ?? '' ?>">

            <div class="p-8">
                <div class="mb-10">
                    <h2 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                        <i class="fa-solid fa-user-tag mr-2 text-emerald-500"></i>
                        Thông tin cá nhân
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-600 text-sm font-medium mb-2">Họ tên khách hàng <span class="text-red-500">*</span></label>
                            <input type="text" name="TenKH" value="<?= htmlspecialchars($khachhang['TenKH'] ?? '') ?>" required
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-gray-600 text-sm font-medium mb-2">Số điện thoại <span class="text-red-500">*</span></label>
                            <input type="text" name="SDT" value="<?= htmlspecialchars($khachhang['SDT'] ?? '') ?>" required
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-gray-600 text-sm font-medium mb-2">Giới tính</label>
                            <div class="flex items-center gap-6 p-3 bg-gray-50 rounded-xl border border-dashed border-gray-300">
                                <label class="inline-flex items-center cursor-pointer group">
                                    <input type="radio" name="GioiTinh" value="1" <?= (($khachhang['GioiTinh'] ?? '1') == '1') ? 'checked' : '' ?> 
                                        class="w-4 h-4 text-emerald-600 focus:ring-emerald-500 border-gray-300">
                                    <span class="ml-2 text-gray-700 group-hover:text-emerald-600">Nam</span>
                                </label>
                                <label class="inline-flex items-center cursor-pointer group">
                                    <input type="radio" name="GioiTinh" value="0" <?= (($khachhang['GioiTinh'] ?? '1') == '0') ? 'checked' : '' ?> 
                                        class="w-4 h-4 text-emerald-600 focus:ring-emerald-500 border-gray-300">
                                    <span class="ml-2 text-gray-700 group-hover:text-emerald-600">Nữ</span>
                                </label>
                            </div>
                        </div>
                        <div>
                            <label class="block text-gray-600 text-sm font-medium mb-2">Ngày sinh</label>
                            <input type="date" name="NgaySinh" value="<?= $khachhang['NgaySinh'] ?? '' ?>"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none">
                        </div>
                    </div>
                </div>

                <hr class="mb-10 border-gray-100">

                <div>
                    <h2 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                        <i class="fa-solid fa-truck-ramp-box mr-2 text-blue-500"></i>
                        Thông tin giao dịch & Liên hệ
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-gray-600 text-sm font-medium mb-2">Email</label>
                            <input type="email" name="Email" value="<?= htmlspecialchars($khachhang['Email'] ?? '') ?>"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none" placeholder="example@gmail.com">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-gray-600 text-sm font-medium mb-2">Địa chỉ hiện tại</label>
                            <input type="text" name="DiaChi" value="<?= htmlspecialchars($khachhang['DiaChi'] ?? '') ?>"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none" placeholder="Số nhà, đường, tỉnh/thành phố...">
                        </div>
                        <div>
                            <label class="block text-gray-600 text-sm font-medium mb-2">Nhân viên phụ trách</label>
                            <select name="MaNV" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none appearance-none bg-no-repeat bg-[right_1rem_center] bg-[length:1em]">
                                <option value="">-- Chọn nhân viên --</option>
                                <?php foreach ($danhSachNhanVien as $nv): ?>
                                    <option value="<?= $nv['MaNV'] ?>" <?= (($khachhang['MaNV'] ?? '') == $nv['MaNV']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($nv['HoTen']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-600 text-sm font-medium mb-2">Chuồng nhập ưu tiên</label>
                            <select name="ChuongNhap" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none">
                                <option value="">-- Không cố định --</option>
                                <?php foreach ($danhSachChuong as $chuong): ?>
                                    <option value="<?= htmlspecialchars($chuong) ?>" <?= (($khachhang['ChuongNhap'] ?? '') === $chuong) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($chuong) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <p class="text-[11px] text-gray-400 mt-1 italic">* Tự động gợi ý chuồng này khi tạo phiếu xuất cho khách</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 px-8 py-6 flex items-center justify-between border-t border-gray-100">
                <p class="text-xs text-gray-400 font-italic">Lưu ý: Các trường có dấu (*) là bắt buộc</p>
                <div class="flex gap-4">
                    <a href="index.php?url=khachhang" class="px-6 py-3 bg-white border border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-100 transition shadow-sm">
                        Hủy bỏ
                    </a>
                    <button type="submit" 
                        class="bg-emerald-600 hover:bg-emerald-700 text-white px-10 py-3 rounded-xl font-bold shadow-lg shadow-emerald-200 transition-all transform hover:scale-105 active:scale-95 flex items-center">
                        <i class="fa-solid fa-floppy-disk mr-2"></i>
                        Lưu thay đổi
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>