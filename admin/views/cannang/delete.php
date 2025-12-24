<?php
// Tùy chọn: View này chỉ hiện ra khi bạn muốn xác nhận trước khi xóa
include __DIR__ . '/../layouts/header.php';
include __DIR__ . '/../layouts/sidebar.php';
// Biến $cannang_data chứa dữ liệu của bản ghi cần xóa (được truyền từ Controller)
?>

<div class="ml-64 p-8 min-h-screen bg-gray-50">
    <div class="max-w-md mx-auto bg-white p-8 rounded-2xl shadow-xl border-t-4 border-red-500">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">🗑️ Xác Nhận Xóa Bản Ghi</h1>

        <div class="mb-6 bg-red-50 p-4 rounded-xl">
            <p class="font-bold text-red-700 mb-2">Bạn có chắc chắn muốn xóa bản ghi cân nặng này không?</p>
            <p class="text-sm text-red-600">
                Hành động này không thể hoàn tác. Bản ghi cân nặng của **Mã Heo: <?php echo htmlspecialchars($cannang_data['MaHeo'] ?? 'N/A'); ?>** vào ngày **<?php echo htmlspecialchars($cannang_data['NgayCan'] ?? 'N/A'); ?>** sẽ bị loại bỏ vĩnh viễn.
            </p>
        </div>

        <div class="flex justify-between items-center">
            <a href="index.php?url=cannang" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-100 transition shadow">
                <i class="fas fa-arrow-left mr-2"></i> Hủy
            </a>
            
            <form method="POST" action="index.php?url=cannang/delete/<?php echo htmlspecialchars($cannang_data['id'] ?? ''); ?>">
                <input type="hidden" name="confirm_delete" value="1">
                <button type="submit" class="px-6 py-3 bg-red-600 text-white rounded-xl hover:bg-red-700 transition shadow-lg flex items-center">
                    <i class="fas fa-trash-alt mr-2"></i> Xóa Vĩnh Viễn
                </button>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>