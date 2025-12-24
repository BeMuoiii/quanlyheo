<?php
$title = "Dashboard Tổng Quan";
ob_start();

// Biến an toàn
$tongDanHeo = $tongDanHeo ?? 0;
$heoNai = $heoNai ?? 0;
$heoDuc = $heoDuc ?? 0;
$heoNaiChoSinh = $heoNaiChoSinh ?? 0;
$heoXuatTuanNay = $heoXuatTuanNay ?? 0;
$heoDangBenh = $heoDangBenh ?? 0;
$heoCanTiemChung = $heoCanTiemChung ?? 0;
$tongKhachHang = $tongKhachHang ?? 0;
$doanhThuTrieu = $doanhThuTrieu ?? 0;
$labels = $labels ?? [];
$chartCanNang = $chartCanNang ?? [];
$chartDoanhThu = $chartDoanhThu ?? [];
?>

<div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
    <!-- 1. Tổng đàn heo -->
    <a href="index.php?url=heo" class="block bg-white rounded-xl shadow-lg p-5 border border-gray-100 hover:shadow-xl transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-semibold">Tổng đàn heo</p>
                <p class="text-3xl font-bold text-emerald-600 mt-1"><?= number_format($tongDanHeo) ?></p>
            </div>
            <div class="bg-emerald-100 p-3 rounded-full">
                <i class="fa-solid fa-piggy-bank text-2xl text-emerald-600"></i>
            </div>
        </div>
    </a>
    <!-- cai -->
    <a href="index.php?url=heo&gioitinh=cai" data-card="heo cái" class="block bg-white rounded-xl shadow-lg p-5 border border-gray-100 hover:shadow-xl transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-semibold">Heo Cái</p>
                <p class="text-3xl font-bold text-pink-600 mt-1"><?= number_format($heoNai) ?></p>
            </div>
            <div class="bg-pink-100 p-3 rounded-full">
                <i class="fa-solid fa-venus text-2xl text-pink-600"></i>
            </div>
        </div>
    </a>
    <!-- duc -->
    <a href="index.php?url=heo&gioitinh=duc"
        data-card="heo-duc"
        class="block bg-white rounded-xl shadow-lg p-5 border border-gray-100 hover:shadow-xl transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-semibold">Heo đực</p>
                <p class="text-3xl font-bold text-sky-600 mt-1">
                    <?= number_format($heoDuc) ?>
                </p>
            </div>
            <div class="bg-sky-100 p-3 rounded-full">
                <i class="fa-solid fa-mars text-2xl text-sky-600"></i>
            </div>
        </div>
    </a>


    <a href="index.php?url=sinhsan" data-card="nai-cho-sinh" class="block bg-white rounded-xl shadow-lg p-5 border border-gray-100 hover:shadow-xl transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-semibold">Nái chờ sinh</p>
                <p class="text-3xl font-bold text-purple-600 mt-1"><?= number_format($heoNaiChoSinh) ?></p>
            </div>
            <div class="bg-purple-100 p-3 rounded-full">
                <i class="fa-solid fa-baby-carriage text-2xl text-purple-600"></i>
            </div>
        </div>
    </a>
</div>

<div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-10">
    <a href="index.php?url=xuatchuong" class="block bg-white rounded-xl shadow-lg p-5 border border-gray-100 hover:shadow-xl transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-semibold">Xuất tuần này</p>
                <p class="text-3xl font-bold text-orange-600 mt-1"><?= number_format($heoXuatTuanNay) ?></p>
            </div>
            <div class="bg-orange-100 p-3 rounded-full">
                <i class="fa-solid fa-truck text-2xl text-orange-600"></i>
            </div>
        </div>
    </a>

    <a href="index.php?url=heo&trangthai=Bệnh" class="block bg-white rounded-xl shadow-lg p-5 border border-gray-100 hover:shadow-xl transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-semibold">Heo đang bệnh</p>
                <p class="text-3xl font-bold text-red-600 mt-1"><?= number_format($heoDangBenh) ?></p>
            </div>
            <div class="bg-red-100 p-3 rounded-full"><i class="fa-solid fa-skull-crossbones text-2xl text-red-600"></i></div>
        </div>
    </a>

    <a href="index.php?url=heo&trangthai=Cần tiêm chủng" class="block bg-white rounded-xl shadow-lg p-5 border border-gray-100 hover:shadow-xl transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-semibold">Cần tiêm chủng</p>
                <p class="text-3xl font-bold text-yellow-600 mt-1"><?= number_format($heoCanTiemChung) ?></p>
            </div>
            <div class="bg-yellow-100 p-3 rounded-full"><i class="fa-solid fa-syringe text-2xl text-yellow-600"></i></div>
        </div>
    </a>

    <a href="index.php?url=khachhang" class="block bg-white rounded-xl shadow-lg p-5 border border-gray-100 hover:shadow-xl transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-semibold">Tổng khách hàng</p>
                <p class="text-3xl font-bold text-indigo-600 mt-1"><?= number_format($tongKhachHang) ?></p>
            </div>
            <div class="bg-indigo-100 p-3 rounded-full"><i class="fa-solid fa-users text-2xl text-indigo-600"></i></div>
        </div>
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-10">
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Tăng trưởng cân nặng (kg)</h3>
        <canvas id="chartCanNang"></canvas>
    </div>
    <div class="bg-white rounded-xl shadow-lg p-6">
        <div class="text-right mb-3">
            <span class="text-3xl font-bold text-emerald-600"><?= $doanhThuTrieu ?> triệu</span>
            <p class="text-sm text-gray-500">Doanh thu năm <?= date('Y') ?></p>
        </div>
        <h3 class="text-lg font-bold text-gray-800 mb-4">Doanh thu (triệu đồng)</h3>
        <canvas id="chartDoanhThu"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Giữ nguyên logic Chart.js của bạn
    const labels = <?= json_encode($labels) ?>;
    const dataCanNang = <?= json_encode($chartCanNang) ?>;
    const dataDoanhThu = <?= json_encode($chartDoanhThu) ?>;

    new Chart(document.getElementById('chartCanNang'), {
        type: 'line',
        data: {
            labels,
            datasets: [{
                data: dataCanNang,
                borderColor: '#10b981',
                backgroundColor: 'rgba(16,185,129,0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    new Chart(document.getElementById('chartDoanhThu'), {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                data: dataDoanhThu,
                backgroundColor: '#10b981',
                borderRadius: 6
            }]
        },
        options: {
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>



<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>