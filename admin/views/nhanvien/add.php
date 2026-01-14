<?php
$isEdit = isset($nv) && $nv; // true nếu sửa, false nếu thêm mới
$title = $isEdit ? "Sửa thông tin nhân viên" : "Tuyển dụng nhân viên mới";
?>
<?php $title = $title; ?>
<?php include __DIR__ . '/../layouts/header.php'; ?>
<?php include __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="ml-64 p-6 md:p-8 min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-6 md:mb-8"><?= htmlspecialchars($title) ?></h1>

        <?php if (!empty($errors)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-xl mb-6">
                <i class="fa-solid fa-exclamation-triangle mr-2"></i>
                <ul class="list-disc list-inside">
                    <?php foreach ($errors as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="" method="post" class="bg-white rounded-2xl shadow-xl p-6 md:p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Mã NV tự động (read-only, hiển thị luôn) -->
                <div>
                   

                    <?php if (!$isEdit): ?>
                        <div class="mb-6">
                            <label class="block text-gray-700 font-medium mb-2">Mã NV (hệ thống tự cấp)</label>
                            <div class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-100 text-emerald-700 font-bold text-lg cursor-not-allowed">
                                <?= $nextMaNV ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($isEdit): ?>
                        <div class="mb-6">
                            <label class="block text-gray-700 font-medium mb-2">Mã NV hiện tại</label>
                            <div class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-100 text-gray-700 font-bold text-lg cursor-not-allowed">
                                <?= (int)$nv['MaNV'] ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Họ và tên <span class="text-red-500">*</span></label>
                    <input type="text" name="HoTen" value="<?= htmlspecialchars($isEdit ? ($nv['HoTen'] ?? '') : ($_POST['HoTen'] ?? '')) ?>" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition">
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Số điện thoại <span class="text-red-500">*</span></label>
                    <input type="tel" name="SDT" value="<?= htmlspecialchars($isEdit ? ($nv['SDT'] ?? '') : ($_POST['SDT'] ?? '')) ?>" required pattern="^0[3|5|7|8|9]\d{8}$" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition">
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">CMND/CCCD <span class="text-red-500">*</span></label>
                    <input type="text" name="CMND" value="<?= htmlspecialchars($isEdit ? ($nv['CMND'] ?? '') : ($_POST['CMND'] ?? '')) ?>" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition">
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Ngày sinh</label>
                    <input type="date" name="NgaySinh" value="<?= htmlspecialchars($isEdit ? ($nv['NgaySinh'] ?? '') : ($_POST['NgaySinh'] ?? '')) ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition">
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Giới tính</label>
                    <div class="flex items-center gap-8 mt-3">
                        <label class="inline-flex items-center">
                            <input type="radio" name="GioiTinh" value="Nam" <?= ($isEdit ? ($nv['GioiTinh'] ?? 'Nam') === 'Nam' : (!isset($_POST['GioiTinh']) || $_POST['GioiTinh'] === 'Nam')) ? 'checked' : '' ?> class="w-5 h-5 text-emerald-600 border-gray-300 focus:ring-emerald-500">
                            <span class="ml-2 text-gray-700">Nam</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="GioiTinh" value="Nữ" <?= ($isEdit ? ($nv['GioiTinh'] ?? '') === 'Nữ' : (isset($_POST['GioiTinh']) && $_POST['GioiTinh'] === 'Nữ')) ? 'checked' : '' ?> class="w-5 h-5 text-emerald-600 border-gray-300 focus:ring-emerald-500">
                            <span class="ml-2 text-gray-700">Nữ</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Địa chỉ</label>
                    <input type="text" name="DiaChi" value="<?= htmlspecialchars($isEdit ? ($nv['DiaChi'] ?? '') : ($_POST['DiaChi'] ?? '')) ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition">
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Bộ phận <span class="text-red-500">*</span></label>
                    <select name="MaBoPhan" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition">
                        <option value="">-- Chọn bộ phận --</option>
                        <?php foreach ($dsBoPhan as $bp): ?>
                            <option value="<?= $bp['MaBoPhan'] ?>"
                                <?= ($isEdit ? ($nv['MaBoPhan'] ?? '') : ($_POST['MaBoPhan'] ?? '')) == $bp['MaBoPhan'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($bp['TenBoPhan']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Chức vụ</label>
                    <input type="text" name="ViTri" value="<?= htmlspecialchars($isEdit ? ($nv['ViTri'] ?? '') : ($_POST['ViTri'] ?? '')) ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition">
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Ngày vào làm <span class="text-red-500">*</span></label>
                    <input type="date" name="NgayVaoLam" value="<?= $isEdit ? ($nv['NgayVaoLam'] ?? '') : ($_POST['NgayVaoLam'] ?? date('Y-m-d')) ?>" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition">
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Lương cơ bản (VNĐ)</label>
                    <input type="text" name="LuongCoBan" value="<?= $isEdit ? number_format($nv['LuongCoBan'] ?? 0, 0, ',', '.') : ($_POST['LuongCoBan'] ?? '') ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition">
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Trạng thái</label>
                    <select name="TrangThai" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition">
                        <option value="Thử việc" <?= ($isEdit ? ($nv['TrangThai'] ?? 'Thử việc') : ($_POST['TrangThai'] ?? 'Thử việc')) == 'Thử việc' ? 'selected' : '' ?>>Thử việc</option>
                        <option value="Chính thức" <?= ($isEdit ? ($nv['TrangThai'] ?? '') : ($_POST['TrangThai'] ?? '')) == 'Chính thức' ? 'selected' : '' ?>>Chính thức</option>
                    </select>
                </div>
            </div>

            <div class="mt-8 md:mt-10 flex flex-col sm:flex-row gap-4 justify-center">
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-8 md:px-10 py-3 md:py-4 rounded-xl font-bold flex items-center justify-center gap-3 transition">
                    <i class="fa-solid fa-save"></i>
                    <?= $isEdit ? "Cập nhật thông tin" : "Tuyển dụng ngay" ?>
                </button>
                <a href="index.php?url=nhanvien" class="bg-gray-500 hover:bg-gray-600 text-white px-8 md:px-10 py-3 md:py-4 rounded-xl font-bold text-center transition">← Quay lại</a>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>