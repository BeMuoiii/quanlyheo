<?php
$title = "Xóa Khách Hàng";

// === XỬ LÝ XÁC NHẬN XÓA - PHẢI ĐẶT TRƯỚC MỌI THỨ ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
    // Lấy mã khách hàng từ URL (ví dụ: index.php?url=khachhang/delete&maKH=KH001)
    $maKH = $_GET['maKH'] ?? '';

    if (empty($maKH)) {
        $_SESSION['error'] = "Không xác định được mã khách hàng để xóa.";
    } else {
        $result = $khachHangModel->delete($maKH);

        if ($result === true) {
            $_SESSION['success'] = "Xóa khách hàng <strong>$maKH</strong> thành công!";
        } else {
            $_SESSION['error'] = "Không thể xóa khách hàng <strong>$maKH</strong>!<br>$result";
        }
    }

    // Chuyển hướng về danh sách khách hàng
    header('Location: index.php?url=khachhang');
    exit;
}

// Nếu không phải POST → load thông tin khách hàng để hiển thị trang xác nhận
// (Giả sử bạn đã có code load $khachHang ở đây hoặc từ controller trước khi include view)
?>

<?php include __DIR__ . '/../layouts/header.php'; ?>
<?php include __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="ml-64 p-8 min-h-screen bg-gray-50">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <div class="text-center mb-8">
                <i class="fas fa-exclamation-triangle text-6xl text-red-500 mb-6"></i>
                <h1 class="text-3xl font-bold text-gray-800 mb-4">Bạn có chắc chắn muốn xóa khách hàng này?</h1>
                <p class="text-lg text-gray-600">Hành động này <strong class="text-red-600">không thể hoàn tác</strong>!</p>
            </div>

            <?php if ($khachHang): ?>
                <!-- Thông tin khách hàng cần xóa -->
                <div class="bg-gray-50 rounded-xl p-6 mb-8 border border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-lg">
                        <div>
                            <span class="font-semibold text-gray-700">Mã Khách Hàng:</span>
                            <span class="ml-3 text-xl font-bold text-emerald-700"><?= htmlspecialchars($khachHang['MaKH']) ?></span>
                        </div>
                        <div>
                            <span class="font-semibold text-gray-700">Họ và Tên:</span>
                            <span class="ml-3"><?= htmlspecialchars($khachHang['HoTen'] ?? 'Chưa rõ') ?></span>
                        </div>
                        <div>
                            <span class="font-semibold text-gray-700">Giới Tính:</span>
                            <span class="ml-3 <?= $khachHang['GioiTinh'] == 'Nam' ? 'text-blue-600' : 'text-pink-600' ?> font-bold">
                                <?= htmlspecialchars($khachHang['GioiTinh'] ?? 'Chưa rõ') ?>
                            </span>
                        </div>
                        <div>
                            <span class="font-semibold text-gray-700">Ngày Sinh:</span>
                            <span class="ml-3"><?= $khachHang['NgaySinh'] ? date('d/m/Y', strtotime($khachHang['NgaySinh'])) : 'Chưa rõ' ?></span>
                        </div>
                        <div>
                            <span class="font-semibold text-gray-700">Địa Chỉ:</span>
                            <span class="ml-3"><?= htmlspecialchars($khachHang['DiaChi'] ?? 'Chưa rõ') ?></span>
                        </div>
                        <div>
                            <span class="font-semibold text-gray-700">Số Điện Thoại:</span>
                            <span class="ml-3"><?= htmlspecialchars($khachHang['SoDienThoai'] ?? 'Chưa có') ?></span>
                        </div>
                        <div>
                            <span class="font-semibold text-gray-700">Email:</span>
                            <span class="ml-3"><?= htmlspecialchars($khachHang['Email'] ?? 'Chưa có') ?></span>
                        </div>
                    </div>
                </div>

                <!-- Form xác nhận -->
                <form action="" method="post" class="text-center">
                    <div class="flex justify-center gap-6">
                        <a href="index.php?url=khachhang"
                            class="px-8 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-xl font-semibold transition transform hover:scale-105">
                            <i class="fas fa-times mr-2"></i> Hủy bỏ
                        </a>
                        <button type="submit" name="confirm_delete" value="1"
                            class="px-8 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl font-semibold transition transform hover:scale-105 flex items-center">
                            <i class="fas fa-trash-alt mr-2"></i> Xóa vĩnh viễn khách hàng này
                        </button>
                    </div>
                </form>

            <?php else: ?>
                <div class="text-center py-12">
                    <p class="text-xl text-red-600 font-medium">Không tìm thấy khách hàng để xóa!</p>
                    <a href="index.php?url=khachhang" class="mt-6 inline-block px-8 py-3 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition">
                        Quay lại danh sách
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>