<?php
$title = "Xóa Khách Hàng";

// Giả định bạn đã có kết nối DB và các model
// require_once ... 

// === XỬ LÝ XÓA KHI NHẤN NÚT XÁC NHẬN ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
    $maKH = $_POST['maKH'] ?? '';

    if (empty($maKH)) {
        $_SESSION['error'] = "Không xác định được mã khách hàng để xóa.";
    } else {
        // Gọi model để xóa (bạn cần có hàm delete trong KhachHangModel)
        $result = $khachHangModel->delete($maKH);

        if ($result === true) {
            $_SESSION['success'] = "Xóa khách hàng <strong>$maKH</strong> thành công!";
        } else {
            // Nếu có lỗi (ví dụ ràng buộc FK), model trả về thông báo lỗi
            $_SESSION['error'] = "Không thể xóa khách hàng <strong>$maKH</strong>!<br>Lý do: $result";
        }
    }

    header('Location: index.php?url=khachhang');
    exit;
}

// === LẤY THÔNG TIN KHÁCH HÀNG ĐỂ HIỂN THỊ TRANG XÁC NHẬN ===
$maKH = $_GET['maKH'] ?? '';

if (empty($maKH)) {
    $_SESSION['error'] = "Không có mã khách hàng để xóa.";
    header('Location: index.php?url=khachhang');
    exit;
}

// Lấy thông tin khách hàng
$kh = $khachHangModel->getOne($maKH); // hoặc getById, tùy tên hàm của bạn

if (!$kh) {
    $_SESSION['error'] = "Không tìm thấy khách hàng với mã $maKH.";
    header('Location: index.php?url=khachhang');
    exit;
}

// === LẤY DANH SÁCH CON HEO MÀ KHÁCH HÀNG NÀY LÀ BỐ HOẶC MẸ ===
$danhSachCon = [];
try {
    $stmt = $db->prepare("
        SELECT MaHeo, GioiTinh, MaBo, MaMe 
        FROM heo 
        WHERE MaBo = :maKH OR MaMe = :maKH
        ORDER BY MaHeo
    ");
    $stmt->execute([':maKH' => $maKH]);
    $danhSachCon = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Nếu lỗi query thì bỏ qua, không hiển thị danh sách con
}

?>

<?php include __DIR__ . '/../layouts/header.php'; ?>
<?php include __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="ml-64 p-8 min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <div class="text-center mb-8">
                <i class="fas fa-exclamation-triangle text-6xl text-red-500 mb-6"></i>
                <h1 class="text-3xl font-bold text-gray-800 mb-4">Bạn có chắc chắn muốn xóa khách hàng này?</h1>
                <p class="text-lg text-gray-600">Hành động này <strong class="text-red-600">không thể hoàn tác</strong>!</p>
            </div>

            <!-- Thông tin khách hàng -->
            <div class="bg-gray-50 rounded-xl p-6 mb-8 border border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Thông tin khách hàng</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-lg">
                    <div>
                        <span class="font-semibold text-gray-700">Mã Khách Hàng:</span>
                        <span class="ml-3 text-xl font-bold text-emerald-700"><?= htmlspecialchars($kh['MaKH']) ?></span>
                    </div>
                    <div>
                        <span class="font-semibold text-gray-700">Họ Tên:</span>
                        <span class="ml-3 font-bold"><?= htmlspecialchars($kh['TenKH'] ?? $kh['HoTen'] ?? 'Chưa có tên') ?></span>
                    </div>
                    <div>
                        <span class="font-semibold text-gray-700">Số Điện Thoại:</span>
                        <span class="ml-3"><?= htmlspecialchars($kh['SDT'] ?? $kh['SoDienThoai'] ?? $kh['DienThoai'] ?? 'Chưa cập nhật') ?></span>
                    </div>
                    <div>
                        <span class="font-semibold text-gray-700">Địa Chỉ:</span>
                        <span class="ml-3"><?= htmlspecialchars($kh['DiaChi'] ?? 'Chưa cập nhật') ?></span>
                    </div>
                </div>
            </div>

            <!-- Cảnh báo nếu là bố/mẹ của heo -->
            <?php if (!empty($danhSachCon)): ?>
                <div class="bg-yellow-50 border-l-4 border-yellow-500 rounded-xl p-6 mb-8">
                    <h3 class="text-xl font-semibold text-yellow-800 mb-3 flex items-center">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Cảnh báo: Khách hàng này là bố/mẹ của <?= count($danhSachCon) ?> con heo
                    </h3>
                    <p class="text-gray-700 mb-4">
                        Nếu xóa, thông tin bố/mẹ trong các con heo dưới đây sẽ bị mất (trở thành trống).
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <?php foreach ($danhSachCon as $con): ?>
                            <div class="bg-white rounded-lg p-4 shadow-sm border">
                                <div class="font-semibold text-gray-800">
                                    Mã heo: <span class="text-blue-600"><?= htmlspecialchars($con['MaHeo']) ?></span>
                                </div>
                                <div class="text-sm text-gray-600">
                                    Giới tính: <?= $con['GioiTinh'] == 'D' ? 'Đực' : 'Cái' ?>
                                </div>
                                <div class="mt-2 text-sm">
                                    <?php if ($con['MaBo'] == $maKH): ?>
                                        <span class="inline-block bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-xs font-medium">Là Bố</span>
                                    <?php endif; ?>
                                    <?php if ($con['MaMe'] == $maKH): ?>
                                        <span class="inline-block bg-pink-100 text-pink-800 px-3 py-1 rounded-full text-xs font-medium">Là Mẹ</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Form xác nhận xóa -->
            <form action="" method="post" class="text-center">
                <input type="hidden" name="maKH" value="<?= htmlspecialchars($kh['MaKH']) ?>">
                
                <div class="flex justify-center gap-8">
                    <a href="index.php?url=khachhang"
                       class="px-8 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-xl font-semibold transition transform hover:scale-105">
                        <i class="fas fa-arrow-left mr-2"></i> Hủy bỏ
                    </a>
                    <button type="submit" name="confirm_delete" value="1"
                            class="px-8 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl font-semibold transition transform hover:scale-105 flex items-center">
                        <i class="fas fa-trash-alt mr-2"></i> Xóa vĩnh viễn
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>