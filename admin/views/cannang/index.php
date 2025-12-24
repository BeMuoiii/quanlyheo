<?php
// admin/views/cannang/index.php
include __DIR__ . '/../layouts/header.php';
include __DIR__ . '/../layouts/sidebar.php';

// ĐƯỜNG DẪN CHUẨN CHO PROJECT CÓ THƯ MỤC public/
$base_url = './index.php';
?>
<script src="https://cdn.tailwindcss.com"></script>

<div class="ml-64 p-8 min-h-screen bg-gray-50">
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Lịch Sử Cân Nặng</h1>
            <a href="<?= $base_url ?>?url=cannang/add" class="px-6 py-3 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition shadow-lg flex items-center gap-2">
                <i class="fas fa-plus"></i> Thêm Mới
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead class="bg-emerald-600 text-white">
                    <tr>
                        <th class="p-4 font-semibold">Mã Heo</th>
                        <th class="p-4 font-semibold">Ngày Cân</th>
                        <th class="p-4 font-semibold">Cân Nặng (kg)</th>
                        <th class="p-4 font-semibold">Ghi Chú</th>
                        <th class="p-4 font-semibold text-center">Hành Động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (!empty($danhSachCanNang)): ?>
                        <?php foreach ($danhSachCanNang as $row): ?>
                            <?php
                            $maCan   = $row['MaCan'];
                            $maHeo   = htmlspecialchars($row['MaHeo']);
                            $confirm = "Bạn có chắc muốn xóa bản ghi cân nặng của Mã Heo: {$maHeo} không?";
                            ?>
                            <tr class="hover:bg-emerald-100 transition cursor-pointer group"
                                onclick="window.location='<?= $base_url ?>?url=cannang/edit/<?= $maCan ?>'"
                                title="Click để chỉnh sửa">
                                <td class="p-4 font-bold text-emerald-700 group-hover:text-emerald-900"><?= $maHeo ?></td>
                                <td class="p-4"><?= date("d/m/Y", strtotime($row['NgayCan'])) ?></td>
                                <td class="p-4 font-bold text-lg text-emerald-600"><?= number_format($row['CanNang'], 1) ?> kg</td>
                                <td class="p-4 text-gray-600 italic"><?= htmlspecialchars($row['GhiChu'] ?? '') ?></td>

                                <td class="p-4 text-center" onclick="event.stopPropagation();">
                                    <div class="flex justify-center gap-6">
                                        <a href="<?= $base_url ?>?url=cannang/edit/<?= $maCan ?>" ...>
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= $base_url ?>?url=cannang/delete/<?= $maCan ?>"
                                            class="text-red-600 hover:text-red-900 text-xl"
                                            onclick="event.stopPropagation(); return confirm(<?= json_encode($confirm) ?>);">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="p-8 text-center text-gray-500">Chưa có dữ liệu nào.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>