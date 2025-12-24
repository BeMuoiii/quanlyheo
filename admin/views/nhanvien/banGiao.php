<?php $title = "Bàn giao nhân viên nghỉ việc"; ?>
<?php include __DIR__ . '/../layouts/header.php'; ?>
<?php include __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="ml-64 p-8 min-h-screen bg-gray-50">
    <div class="max-w-3xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Bàn giao nhân viên nghỉ việc</h1>

        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden">
            <div class="bg-gradient-to-r from-red-600 to-rose-700 text-white p-8 text-center">
                <i class="fa-solid fa-user-times text-6xl mb-4 block"></i>
                <h2 class="text-2xl font-bold">Xác nhận cho nhân viên nghỉ việc</h2>
            </div>

            <div class="p-8 border-b-4 border-red-200 bg-gradient-to-br from-red-50 to-rose-50">
                <div class="flex flex-col sm:flex-row items-center gap-8">
                    <div class="relative group">
                        <img src="<?= base_url() . htmlspecialchars($nv['Anh'] ?: '/assets/img/avatar-default.png') ?>"
                             class="w-40 h-40 rounded-full object-cover border-8 border-white shadow-2xl ring-4 ring-red-300">
                        <div class="absolute bottom-0 right-0 bg-red-600 text-white rounded-full p-3">
                            <i class="fa-solid fa-user-slash text-2xl"></i>
                        </div>
                    </div>
                    <div class="text-center sm:text-left">
                        <h3 class="text-4xl font-bold text-red-700 mb-4"><?= htmlspecialchars($nv['HoTen']) ?></h3>
                        <div class="space-y-3 text-lg">
                            <p><i class="fa-solid fa-id-badge text-red-600"></i> Mã NV: <strong>#<?= str_pad($nv['MaNV'], 4, '0', STR_PAD_LEFT) ?></strong></p>
                            <p><i class="fa-solid fa-briefcase text-blue-600"></i> Chức vụ: <strong><?= htmlspecialchars($nv['ViTri'] ?? '—') ?></strong></p>
                            <p><i class="fa-solid fa-building text-purple-600"></i> Bộ phận: <strong><?= htmlspecialchars($nv['TenBoPhan'] ?? '—') ?></strong></p>
                            <p><i class="fa-solid fa-phone text-green-600"></i> SĐT: <strong><?= htmlspecialchars($nv['SDT'] ?? '—') ?></strong></p>
                        </div>
                    </div>
                </div>
            </div>

            <form action="" method="post" class="p-8">
                <div class="space-y-6">
                    <div>
                        <label class="block text-gray-700 font-medium mb-3">Ngày nghỉ việc <span class="text-red-500">*</span></label>
                        <input type="date" name="NgayNghi" value="<?= date('Y-m-d') ?>" required class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-red-500">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-3">Lý do nghỉ việc</label>
                        <textarea name="LyDo" rows="4" placeholder="Ghi rõ lý do..." class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-red-500"></textarea>
                    </div>
                </div>

                <div class="mt-10 flex gap-4 justify-center">
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-10 py-5 rounded-xl font-bold text-xl flex items-center gap-3">
                        <i class="fa-solid fa-check"></i> Xác nhận bàn giao & cho nghỉ
                    </button>
                    <a href="index.php?url=nhanvien" class="bg-gray-600 hover:bg-gray-700 text-white px-8 py-5 rounded-xl font-bold">Hủy bỏ</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>