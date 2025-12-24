<?php $title = "Báo Cáo Tài Chính"; ?>
<?php include __DIR__ . '/../layouts/header.php'; ?>
<?php include __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="ml-64 p-8 min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto">
        <!-- HEADER -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Báo cáo tài chính</h1>
                <p class="text-gray-500">Cập nhật đến ngày <strong><?= date('d/m/Y') ?></strong></p>
            </div>

            <div class="flex flex-wrap gap-3">
                <select id="namSelect" class="px-5 py-3 rounded-xl border focus:ring-4 focus:ring-emerald-200 focus:outline-none font-medium">
                    <?php for ($i = date('Y') + 1; $i >= 2020; $i--): ?>
                        <option value="<?= $i ?>" <?= ($i == ($namHienTai ?? date('Y'))) ? 'selected' : '' ?>><?= $i ?></option>
                    <?php endfor; ?>
                </select>
                <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-medium shadow-lg flex items-center gap-2">
                    <i class="fas fa-print"></i> In báo cáo
                </button>
                <button onclick="xuatExcel()" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-xl font-medium shadow-lg flex items-center gap-2">
                    <i class="fas fa-file-excel"></i> Xuất Excel
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-10">
            <a href="../.../../xuatchuong/index.php" class="block bg-gradient-to-br from-emerald-500 to-emerald-600 text-white p-6 rounded-2xl shadow-2xl transition duration-300 hover:scale-[1.02] hover:shadow-emerald-400/50 cursor-pointer">
                <p class="text-emerald-100">Doanh thu thuần</p>
                <p class="text-4xl font-bold mt-2"><?= number_format($tongDoanhThu ?? 0, 4, '.', ',') ?> triệu</p>
                <p class="text-emerald-100 text-sm mt-2">+28% so với 2024</p>
            </a>
            <div class="bg-gradient-to-br from-red-500 to-red-600 text-white p-6 rounded-2xl shadow-2xl transition duration-300 hover:scale-[1.02]">
                <p class="text-red-100">Tổng chi phí</p>
                <p class="text-4xl font-bold mt-2">11.280 triệu</p>
                <p class="text-red-100 text-sm mt-2">+15% so với 2024</p>
            </div>
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white p-6 rounded-2xl shadow-2xl transition duration-300 hover:scale-[1.02]">
                <p class="text-blue-100">Lợi nhuận gộp</p>
                <p class="text-4xl font-bold mt-2">7.140 triệu</p>
                <div class="mt-2 flex justify-between items-end">
                    <span class="text-blue-100 text-sm">Tỷ suất gộp:</span>
                    <span class="text-green-200 font-bold text-lg">38.8%</span>
                </div>
            </div>
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white p-6 rounded-2xl shadow-2xl transition duration-300 hover:scale-[1.02]">
                <p class="text-purple-100">Lợi nhuận ròng</p>
                <p class="text-4xl font-bold mt-2">4.280 triệu</p>
                <div class="mt-2 flex justify-between items-end">
                    <span class="text-purple-100 text-sm">Tỷ suất ròng:</span>
                    <span class="text-green-200 font-bold text-lg">23.2%</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl p-8 mb-10">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-4">Doanh thu - Chi phí - Lợi nhuận (triệu đồng)</h2>
            <canvas id="chartDoanhThu" height="100"></canvas>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-10">
            <div class="bg-white rounded-2xl shadow-xl p-6">
                <h2 class="text-2xl font-extrabold text-green-700 mb-6 text-center border-b pb-3">DOANH THU CHI TIẾT</h2>
                <div class="space-y-3">
                    <div class="flex justify-between items-center py-4 px-2 border-b border-green-100 hover:bg-green-50 rounded-lg">
                        <span>Bán heo thương phẩm</span>
                        <span class="font-bold text-green-600 text-lg">17.920 triệu</span>
                    </div>
                    <div class="flex justify-between items-center py-4 px-2 border-b border-green-100 hover:bg-green-50 rounded-lg">
                        <span>Bán heo giống</span>
                        <span class="font-bold text-green-600 text-lg">380 triệu</span>
                    </div>
                    <div class="flex justify-between items-center py-4 px-2 border-b border-green-100 hover:bg-green-50 rounded-lg">
                        <span>Khác (phế phẩm, phân...)</span>
                        <span class="font-bold text-green-600 text-lg">120 triệu</span>
                    </div>
                    <div class="flex justify-between items-center py-5 bg-green-100 rounded-xl px-6 mt-6 shadow-md">
                        <span class="text-xl font-bold text-green-800">TỔNG DOANH THU</span>
                        <span class="text-3xl font-extrabold text-green-700">18.420 triệu</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-xl p-6">
                <h2 class="text-2xl font-extrabold text-red-700 mb-6 text-center border-b pb-3">CHI PHÍ CHI TIẾT</h2>
                <div class="space-y-3">
                    <div class="flex justify-between items-center py-4 px-2 border-b border-red-100 hover:bg-red-50 rounded-lg">
                        <span>Chi phí thức ăn chăn nuôi</span>
                        <span class="font-bold text-red-600 text-lg">8.500 triệu</span>
                    </div>
                    <div class="flex justify-between items-center py-4 px-2 border-b border-red-100 hover:bg-red-50 rounded-lg">
                        <span>Chi phí thuốc thú y & vaccine</span>
                        <span class="font-bold text-red-600 text-lg">950 triệu</span>
                    </div>
                    <div class="flex justify-between items-center py-4 px-2 border-b border-red-100 hover:bg-red-50 rounded-lg">
                        <span>Chi phí nhân công & điện nước</span>
                        <span class="font-bold text-red-600 text-lg">1.830 triệu</span>
                    </div>
                    <div class="flex justify-between items-center py-5 bg-red-100 rounded-xl px-6 mt-6 shadow-md">
                        <span class="text-xl font-bold text-red-800">TỔNG CHI PHÍ</span>
                        <span class="text-3xl font-extrabold text-red-700">11.280 triệu</span>
                    </div>
                </div>
            </div>
        </div>


        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="bg-white rounded-2xl shadow-xl p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-5 border-b pb-3">Top 5 khách hàng mua nhiều nhất</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="py-3 px-4 text-sm font-semibold text-gray-600 rounded-tl-lg">Khách hàng</th>
                                <th class="py-3 px-4 text-center text-sm font-semibold text-gray-600">SĐT</th>
                                <th class="py-3 px-4 text-right text-sm font-semibold text-gray-600">Số con</th>
                                <th class="py-3 px-4 text-right text-sm font-semibold text-gray-600 rounded-tr-lg">Doanh thu</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($topKhachHang ?? [] as $kh): ?>
                                <tr class="hover:bg-blue-50 transition duration-150">
                                    <td class="py-3 px-4 font-medium text-gray-800"><?= htmlspecialchars($kh['TenKH']) ?></td>
                                    <td class="py-3 px-4 text-center text-gray-600"><?= $kh['SDT'] ?></td>
                                    <td class="py-3 px-4 text-right font-extrabold text-emerald-600"><?= number_format($kh['tong_mua']) ?> con</td>
                                    <td class="py-3 px-4 text-right font-extrabold text-blue-600"><?= number_format($kh['tong_tien'] / 1000000, 1) ?> tr</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-xl p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-5 border-b pb-3">Doanh thu theo nhân viên (<?= $namHienTai ?? date('Y') ?>)</h3>
                <div class="space-y-4">
                    <?php foreach ($doanhThuNV ?? [] as $nv): ?>
                        <div class="flex justify-between items-center bg-gray-50 p-4 rounded-xl shadow-sm border border-gray-100 hover:bg-gray-100 transition duration-150">
                            <span class="font-semibold text-gray-800 text-lg"><?= htmlspecialchars($nv['HoTen']) ?></span>
                            <span class="font-extrabold text-emerald-600 text-xl"><?= number_format($nv['doanh_thu'] / 1000000, 1) ?> triệu</span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>



    <div class="mb-10">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-3">Phân tích Hiệu suất (Theo đơn vị con)</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="bg-white rounded-3xl shadow-2xl p-8 text-center border-b-4 border-red-500 transition duration-300 hover:scale-[1.02]">
                <p class="text-gray-600 text-lg font-semibold">Giá vốn trung bình/con</p>
                <p class="text-5xl font-extrabold text-red-600 mt-3">6.820.000 đ</p>
                <p class="text-red-400 text-sm mt-3">Chi phí sản xuất</p>
            </div>

            <div class="bg-white rounded-3xl shadow-2xl p-8 text-center border-b-4 border-green-500 transition duration-300 hover:scale-[1.02]">
                <p class="text-gray-600 text-lg font-semibold">Giá bán trung bình/con</p>
                <p class="text-5xl font-extrabold text-green-600 mt-3">11.120.000 đ</p>
                <p class="text-green-400 text-sm mt-3">Doanh thu đạt được</p>
            </div>

            <div class="bg-white rounded-3xl shadow-2xl p-8 text-center border-b-4 border-blue-500 transition duration-300 hover:scale-[1.02]">
                <p class="text-gray-600 text-lg font-semibold">Lãi ròng trung bình/con</p>
                <p class="text-5xl font-extrabold text-blue-600 mt-3">4.280.000 đ</p>
                <p class="text-green-600 font-bold text-xl mt-3">↑ 18% so với 2024</p>
            </div>
        </div>
    </div>

    <!-- CHI TIẾT THEO THÁNG (có thể mở rộng thành bảng động sau) -->
    <div class="bg-white rounded-2xl shadow-xl p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Chi tiết theo tháng năm <?= $namHienTai ?? date('Y') ?></h2>
        <div class="overflow-x-auto">
            <table class="w-full text-center">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="py-4">Tháng</th>
                        <th class="py-4 text-green-600">Doanh thu</th>
                        <th class="py-4 text-red-600">Chi phí</th>
                        <th class="py-4 text-blue-600">Lợi nhuận</th>
                        <th class="py-4">Tỷ suất LN</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <tr class="bg-green-50">
                        <td class="py-4 font-medium">Tháng 12/2025 (đang chạy)</td>
                        <td class="py-4 font-bold text-green-600">1.840 triệu</td>
                        <td class="py-4 font-bold text-red-600">1.080 triệu</td>
                        <td class="py-4 font-bold text-blue-600">760 triệu</td>
                        <td class="py-4 font-bold text-purple-600">41.3%</td>
                    </tr>
                    <!-- Có thể thêm các tháng khác ở đây -->
                </tbody>
            </table>
        </div>
    </div>

</div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Dữ liệu từ PHP
    const doanhThuThang = <?= json_encode(array_values($doanhThuThang ?? array_fill(0, 12, 0))) ?>;

    const ctx = document.getElementById('chartDoanhThu').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Th1', 'Th2', 'Th3', 'Th4', 'Th5', 'Th6', 'Th7', 'Th8', 'Th9', 'Th10', 'Th11', 'Th12'],
            datasets: [{
                    label: 'Doanh thu',
                    data: doanhThuThang.map(x => x / 1000000),
                    backgroundColor: 'rgba(34, 197, 94, 0.8)',
                    borderColor: 'rgb(34, 197, 94)',
                    borderWidth: 1
                },
                {
                    label: 'Chi phí (ước tính)',
                    data: [850, 920, 1100, 1050, 1200, 1300, 1250, 1400, 1350, 1500, 1450, 1600],
                    backgroundColor: 'rgba(239, 68, 68, 0.8)',
                    borderColor: 'rgb(239, 68, 68)',
                    borderWidth: 1
                },
                {
                    type: 'line',
                    label: 'Lợi nhuận',
                    data: doanhThuThang.map((dt, i) => (dt - [850, 920, 1100, 1050, 1200, 1300, 1250, 1400, 1350, 1500, 1450, 1600][i]) / 1000000),
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.2)',
                    tension: 0.4,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Xuất Excel đơn giản
    function xuatExcel() {
        alert("Chức năng xuất Excel đang phát triển – liên hệ admin để có file mẫu nhé!");
    }
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>