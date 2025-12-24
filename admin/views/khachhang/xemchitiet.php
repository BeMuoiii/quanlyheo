<?php $title = "Chi tiết khách hàng"; ?>
<?php include __DIR__ . '/../layouts/header.php'; ?>
<?php include __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="ml-64 p-8 min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto">
        
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">
                <i class="fas fa-user-tag text-emerald-600 mr-2"></i> Chi Tiết Khách Hàng
            </h1>
            <a href="index.php?url=khachhang/index" class="bg-gray-500 hover:bg-gray-600 text-white px-5 py-2 rounded-xl font-semibold transition duration-150">
                <i class="fas fa-arrow-left mr-2"></i> Quay lại danh sách
            </a>
        </div>

        <?php if (!empty($error_message)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-xl mb-6">
                <?= htmlspecialchars($error_message) ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-1 bg-white rounded-2xl shadow-xl p-8 h-fit">
                <h2 class="text-xl font-bold text-gray-800 border-b pb-3 mb-4">Thông tin Khách hàng</h2>
                <div class="space-y-4 text-gray-700">
                    <p><strong>Mã KH:</strong> <span class="text-blue-600 font-semibold"><?= htmlspecialchars($khachhang['MaKH'] ?? 'N/A') ?></span></p>
                    <p><strong>Họ tên:</strong> <?= htmlspecialchars($khachhang['TenKH'] ?? 'N/A') ?></p>
                    <p><strong>SĐT:</strong> <?= htmlspecialchars($khachhang['SDT'] ?? 'N/A') ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($khachhang['Email'] ?? 'Chưa cung cấp') ?></p>
                    <p><strong>Giới tính:</strong> <?= ($khachhang['GioiTinh'] ?? '') == '1' ? 'Nam' : 'Nữ' ?></p>
                    <p><strong>Ngày sinh:</strong> <?= !empty($khachhang['NgaySinh']) ? date('d/m/Y', strtotime($khachhang['NgaySinh'])) : 'N/A' ?></p>
                    <p><strong>Địa chỉ:</strong> <?= htmlspecialchars($khachhang['DiaChi'] ?? 'N/A') ?></p>
                    <p><strong>Nhân viên PT:</strong> <span class="font-medium"><?= htmlspecialchars($khachhang['TenNV'] ?? 'N/A') ?></span></p>
                </div>
            </div>

            <div class="lg:col-span-2 bg-white rounded-2xl shadow-xl p-8">
                <h2 class="text-xl font-bold text-gray-800 border-b pb-3 mb-4">
                    <i class="fas fa-clipboard-list mr-1"></i> Danh sách Heo đã xuất chuồng
                </h2>

                <?php if (empty($danhSachGiaoDich)): ?>
                    <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 px-6 py-4 rounded-lg text-center font-medium">
                        Khách hàng này chưa có giao dịch xuất chuồng nào.
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mã Heo</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Giống Heo</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày Xuất</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NV Bán</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($danhSachGiaoDich as $giaoDich): ?>
                                    <tr>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-blue-600">
                                            <?= htmlspecialchars($giaoDich['MaHeo'] ?? 'N/A') ?>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
                                            <?= htmlspecialchars($giaoDich['GiongHeo'] ?? 'N/A') ?>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
                                            <?= !empty($giaoDich['NgayXuat']) ? date('d/m/Y', strtotime($giaoDich['NgayXuat'])) : 'N/A' ?>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
                                            <?= htmlspecialchars($giaoDich['TenNV'] ?? 'N/A') ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
            
        </div>
        
        </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>