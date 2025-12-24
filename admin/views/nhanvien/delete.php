<?php $title = "Xóa nhân viên vĩnh viễn"; ?>
<?php include __DIR__ . '/../layouts/header.php'; ?>
<?php include __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="ml-64 p-8 min-h-screen bg-gray-50">
    <div class="max-w-3xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Xóa nhân viên vĩnh viễn</h1>

        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden">
            <div class="bg-gradient-to-r from-red-700 to-rose-800 text-white p-8 text-center">
                <i class="fa-solid fa-skull-crossbones text-6xl mb-4 block"></i>
                <h2 class="text-2xl font-bold">CẢNH BÁO: XÓA VĨNH VIỄN!</h2>
            </div>

            <div class="p-8 bg-red-50 border-b-4 border-red-400">
                <div class="flex flex-col sm:flex-row items-center gap-8">
                    <img src="<?= base_url() . htmlspecialchars($nv['Anh'] ?: '/assets/img/avatar-default.png') ?>"
                         class="w-40 h-40 rounded-full object-cover border-8 border-white shadow-2xl ring-4 ring-red-500">
                    <div class="text-center sm:text-left">
                        <h3 class="text-4xl font-bold text-red-700 mb-4"><?= htmlspecialchars($nv['HoTen']) ?></h3>
                        <p class="text-xl"><strong>#<?= str_pad($nv['MaNV'], 4, '0', STR_PAD_LEFT) ?></strong></p>
                        <p class="text-lg text-gray-700">Chức vụ: <?= htmlspecialchars($nv['ViTri'] ?? '—') ?></p>
                        <p class="text-lg text-gray-700">Bộ phận: <?= htmlspecialchars($nv['TenBoPhan'] ?? '—') ?></p>
                    </div>
                </div>
            </div>

            <form action="" method="post" class="p-8">
                <div class="bg-yellow-50 border-2 border-yellow-400 rounded-xl p-6 mb-8">
                    <p class="text-yellow-800 font-bold text-lg mb-4">
                        <i class="fa-solid fa-triangle-exclamation mr-2"></i>
                        Gõ chính xác: <span class="text-red-600 font-mono">XÓA VĨNH VIỄN</span>
                    </p>
                    <input type="text" name="confirm_delete" placeholder="Nhập XÓA VĨNH VIỄN để xác nhận" required
                           class="w-full px-4 py-3 border-2 border-yellow-500 rounded-lg text-center text-lg font-bold focus:ring-4 focus:ring-red-300">
                </div>

                <div class="flex gap-4 justify-center">
                    <button type="submit" name="delete_confirm" class="bg-red-600 hover:bg-red-800 text-white px-12 py-5 rounded-xl font-bold text-xl flex items-center gap-3 shadow-2xl">
                        <i class="fa-solid fa-trash"></i> XÓA VĨNH VIỄN
                    </button>
                    <a href="index.php?url=nhanvien" class="bg-gray-600 hover:bg-gray-700 text-white px-10 py-5 rounded-xl font-bold text-lg">Hủy bỏ</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>