<?php 
$isEdit = isset($nv) && $nv; // Xác định là trang Sửa hay Thêm
$title = $isEdit ? "Sửa thông tin nhân viên" : "Tuyển dụng nhân viên mới";
?>
<?php $title = $title; ?>
<?php include __DIR__ . '/../layouts/header.php'; ?>
<?php include __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="ml-64 p-8 min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-800 mb-8"><?= $title ?></h1>

        <?php if (!empty($errors)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-xl mb-6">
                <i class="fa-solid fa-exclamation-triangle mr-2"></i>
                <ul class="list-disc list-inside">
                    <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="" method="post" enctype="multipart/form-data" class="bg-white rounded-2xl shadow-xl p-8">
            <!-- KHUNG ẢNH SIÊU ĐẸP -->
            <div class="col-span-1 md:col-span-2 mb-8">
                <label class="block text-gray-700 font-medium mb-4 text-center md:text-left">Ảnh nhân viên</label>
                <div class="flex flex-col items-center">
                    <div class="relative group">
                        <img id="previewAnh" 
                             src="<?= $isEdit && !empty($nv['Anh']) ? base_url() . htmlspecialchars($nv['Anh']) : base_url() . '/assets/img/avatar-default.png' ?>"
                             class="w-40 h-40 rounded-full object-cover border-8 border-white shadow-xl ring-4 ring-gray-200 transition-all">
                        <label for="inputAnh" class="absolute inset-0 flex items-center justify-center cursor-pointer opacity-0 group-hover:opacity-100 transition-opacity bg-black bg-opacity-40 rounded-full">
                            <div class="text-white text-center">
                                <i class="fa-solid fa-camera text-3xl mb-2 block"></i>
                                <span class="text-sm font-medium">Đổi ảnh</span>
                            </div>
                        </label>
                        <input type="file" id="inputAnh" name="Anh" accept="image/*" onchange="previewImage(event)" class="hidden">
                    </div>
                    <p class="text-sm text-gray-500 mt-4">JPG, PNG, GIF • Tối đa 2MB</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Các field giữ nguyên như cũ, chỉ đổi source từ $_POST sang $nv khi $isEdit -->
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Họ và tên <span class="text-red-500">*</span></label>
                    <input type="text" name="HoTen" value="<?= htmlspecialchars($isEdit ? ($nv['HoTen'] ?? '') : ($_POST['HoTen'] ?? '')) ?>" required class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Số điện thoại <span class="text-red-500">*</span></label>
                    <input type="text" name="SDT" value="<?= htmlspecialchars($isEdit ? ($nv['SDT'] ?? '') : ($_POST['SDT'] ?? '')) ?>" required pattern="^0[3|5|7|8|9]\d{8}$" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-2">CMND/CCCD <span class="text-red-500">*</span></label>
                    <input type="text" name="CMND" value="<?= htmlspecialchars($isEdit ? ($nv['CMND'] ?? '') : ($_POST['CMND'] ?? '')) ?>" required class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Ngày sinh</label>
                    <input type="date" name="NgaySinh" value="<?= $isEdit ? ($nv['NgaySinh'] ?? '') : ($_POST['NgaySinh'] ?? '') ?>" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Giới tính</label>
                    <div class="flex items-center gap-8 mt-3">
                        <label class="inline-flex items-center mr-6">
                            <input type="radio" name="GioiTinh" value="Nam" <?= ($isEdit ? ($nv['GioiTinh'] ?? 'Nam')=='Nam' : (!isset($_POST['GioiTinh']) || $_POST['GioiTinh']=='Nam')) ? 'checked' : '' ?> class="text-emerald-600">
                            <span class="ml-2">Nam</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="GioiTinh" value="Nữ" <?= ($isEdit ? ($nv['GioiTinh'] ?? '')=='Nữ' : ($_POST['GioiTinh']??'')=='Nữ') ? 'checked' : '' ?> class="text-emerald-600">
                            <span class="ml-2">Nữ</span>
                        </label>
                    </div>
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Địa chỉ</label>
                    <input type="text" name="DiaChi" value="<?= htmlspecialchars($isEdit ? ($nv['DiaChi'] ?? '') : ($_POST['DiaChi'] ?? '')) ?>" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Bộ phận <span class="text-red-500">*</span></label>
                    <select name="MaBoPhan" required class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-emerald-500">
                        <option value="">-- Chọn bộ phận --</option>
                        <?php foreach ($dsBoPhan as $bp): ?>
                            <option value="<?= $bp['MaBoPhan'] ?>" <?= ($isEdit ? ($nv['MaBoPhan'] ?? '') : ($_POST['MaBoPhan'] ?? '')) == $bp['MaBoPhan'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($bp['TenBoPhan']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Chức vụ</label>
                    <input type="text" name="ViTri" value="<?= htmlspecialchars($isEdit ? ($nv['ViTri'] ?? '') : ($_POST['ViTri'] ?? '')) ?>" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Ngày vào làm <span class="text-red-500">*</span></label>
                    <input type="date" name="NgayVaoLam" value="<?= $isEdit ? ($nv['NgayVaoLam'] ?? '') : ($_POST['NgayVaoLam'] ?? date('Y-m-d')) ?>" required class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Lương cơ bản (VNĐ)</label>
                    <input type="text" name="LuongCoBan" value="<?= $isEdit ? number_format($nv['LuongCoBan'] ?? 0) : ($_POST['LuongCoBan'] ?? '') ?>" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Trạng thái</label>
                    <select name="TrangThai" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-emerald-500">
                        <option value="Thử việc" <?= ($isEdit ? ($nv['TrangThai'] ?? '') : ($_POST['TrangThai'] ?? '')) == 'Thử việc' ? 'selected' : '' ?>>Thử việc</option>
                        <option value="Chính thức" <?= ($isEdit ? ($nv['TrangThai'] ?? '') : ($_POST['TrangThai'] ?? '')) == 'Chính thức' ? 'selected' : '' ?>>Chính thức</option>
                    </select>
                </div>
            </div>

            <div class="mt-10 flex gap-4 justify-center">
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-10 py-4 rounded-xl font-bold flex items-center gap-3">
                    <i class="fa-solid fa-save"></i> <?= $isEdit ? "Cập nhật thông tin" : "Tuyển dụng ngay" ?>
                </button>
                <a href="index.php?url=nhanvien" class="bg-gray-500 hover:bg-gray-600 text-white px-8 py-4 rounded-xl font-bold">← Quay lại</a>
            </div>
        </form>
    </div>
</div>

<script>
function previewImage(e) {
    if (e.target.files[0]) {
        document.getElementById('previewAnh').src = URL.createObjectURL(e.target.files[0]);
    }
}
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>