<?php $title = "Ghi Nhận Đẻ"; ?>
<?php include __DIR__ . '/../layouts/header.php'; ?>
<?php include __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="ml-64 p-8 min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Ghi Nhận Sinh Đẻ</h1>
            <a href="index.php?url=sinhsan" class="text-gray-600 hover:text-gray-800 transition">
                <i class="fas fa-arrow-left text-2xl"></i>
            </a>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-6 py-4 rounded-xl mb-6 flex items-center">
                <i class="fas fa-check-circle text-2xl mr-3"></i>
                <?= $_SESSION['success'];
                unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['errors'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-xl mb-6">
                <ul class="list-disc list-inside">
                    <?php foreach ($_SESSION['errors'] as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
                    <?php unset($_SESSION['errors']); ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-2xl shadow-xl p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <div class="bg-gradient-to-br from-pink-100 to-rose-100 rounded-2xl p-6">
                    <h3 class="font-bold text-pink-800 text-lg mb-3">Thông tin heo nái</h3>
                    <p><span class="font-semibold">Mã nái:</span> <span class="text-pink-700 text-xl">#<?= $sinhSan['MaHeoNai'] ?></span></p>
                    <p><span class="font-semibold">Chuồng hiện tại:</span> <?= $sinhSan['ViTriChuong'] ?: 'Chưa rõ' ?></p>
                </div>
                <div class="bg-gradient-to-br from-emerald-100 to-teal-100 rounded-2xl p-6">
                    <h3 class="font-bold text-emerald-800 text-lg mb-3">Thông tin phối giống</h3>
                    <p><span class="font-semibold">Mã heo đực:</span> <?= $sinhSan['MaHeoDuc'] ?: '—' ?></p>
                    <p><span class="font-semibold">Ngày phối:</span> <?= date('d/m/Y', strtotime($sinhSan['NgayPhoi'])) ?></p>
                    <p><span class="font-semibold">Dự sinh:</span> <span class="font-bold text-emerald-700"><?= date('d/m/Y', strtotime($ngayDuSinh)) ?></span></p>
                </div>
            </div>

            <form method="POST" action="index.php?url=sinhsan">
            <input type="hidden" name="ghiNhanDe_id" value="<?= $sinhSan['SinhSan'] ?>">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-gray-700 font-medium mb-2">
                        Ngày Đẻ Thực Tế <span class="text-red-500 text-sm">*</span>
                    </label>

                    <input type="date" name="NgayDe"
                        value="<?= date('Y-m-d') ?>"
                        min="<?= $minNgayDe ?>"
                        max="<?= $maxNgayDe ?>"
                        required
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none">

                </div>

                <div>
    <label class="block text-gray-700 font-medium mb-2">
        Số Con Sống <span class="text-red-500 text-sm">*</span>
    </label>
    <input type="number" 
           name="SoConSong" 
           min="0" 
           value="10" 
           required 
           placeholder="Số lượng con sống"
           class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none 
                  font-bold text-emerald-700 text-lg">
</div>
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Số Con Chết</label>
                    <input type="number" name="SoConChet" min="0" value="0"
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none">
                </div>
            </div>

            <div>
                <label class="block text-gray-700 font-medium mb-2">Nhân viên hỗ trợ</label>
                <select name="MaNVDe" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none">
                    <option value="">-- Chọn nhân viên --</option>
                    <?php foreach ($dsNhanVien as $nv): ?>
                        <option value="<?= $nv['MaNV'] ?>"><?= htmlspecialchars($nv['HoTen']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-gray-700 font-medium mb-2">Ghi Chú Đẻ</label>
                <textarea name="GhiChuDe" rows="3" placeholder="Ví dụ: Đẻ tự nhiên, heo con khỏe mạnh..."
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none resize-none"></textarea>
            </div>

            <div class="flex justify-end gap-4 pt-6">
               <a href="index.php?url=sinhsan" class="px-8 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-xl font-semibold transition">
    Hủy bỏ
</a>

                <button type="submit"
                    class="px-8 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-semibold">
                    Ghi nhận
                </button>

            </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>