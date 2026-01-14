<?php $title = "Xóa khách hàng vĩnh viễn"; ?>

<?php
// === XỬ LÝ XÁC NHẬN XÓA - PHẢI ĐẶT TRƯỚC MỌI THỨ ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
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

    header('Location: index.php?url=khachhang');
    exit;
}
?>

<?php include __DIR__ . '/../layouts/header.php'; ?>
<?php include __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="ml-64 p-8 min-h-screen bg-gray-50">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <div class="text-center mb-8">
                <i class="fas fa-exclamation-triangle text-6xl text-red-500 mb-6"></i>
                <h1 class="text-3xl font-bold text-gray-800 mb-4">Bạn có chắc chắn muốn xóa nhân viên này?</h1>
                <p class="text-lg text-gray-600">Dữ liệu liên quan sẽ bị xóa và <strong class="text-red-600">không thể hoàn tác</strong>!</p>
            </div>

            <?php if ($nhanVien): ?>
                <div class="bg-gray-50 rounded-xl p-6 mb-8 border border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-lg">
                        <div>
                            <span class="font-semibold text-gray-700">Mã Nhân Viên:</span>
                            <span class="ml-3 text-xl font-bold text-emerald-700"><?= htmlspecialchars($nhanVien['MaNV']) ?></span>
                        </div>
                        <div>
                            <span class="font-semibold text-gray-700">Họ và Tên:</span>
                            <span class="ml-3"><?= htmlspecialchars($nhanVien['HoTen'] ?? 'Chưa rõ') ?></span>
                        </div>
                        <div>
                            <span class="font-semibold text-gray-700">Số Điện Thoại:</span>
                            <span class="ml-3"><?= htmlspecialchars($nhanVien['SoDienThoai'] ?? 'Chưa có') ?></span>
                        </div>
                        <div>
                            <span class="font-semibold text-gray-700">Chức Vụ:</span>
                            <span class="ml-3 text-blue-600 font-bold"><?= htmlspecialchars($nhanVien['ChucVu'] ?? 'Nhân viên') ?></span>
                        </div>
                    </div>
                </div>

                <form action="" method="post" class="text-center">
                    <div class="flex justify-center gap-6">
                        <a href="index.php?url=nhanvien" class="px-8 py-3 bg-gray-500 text-white rounded-xl">Hủy bỏ</a>

                        <button type="submit" name="confirm_delete" value="1" class="px-8 py-3 bg-red-600 text-white rounded-xl">
                            Xóa vĩnh viễn nhân viên này
                        </button>
                    </div>
                </form>
            <?php else: ?>
                <div class="text-center py-12">
                    <p class="text-xl text-red-600 font-medium">Không tìm thấy nhân viên để xóa!</p>
                    <a href="index.php?url=nhanvien" class="mt-6 inline-block px-8 py-3 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition">
                        Quay lại danh sách
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>