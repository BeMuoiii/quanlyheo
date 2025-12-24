<div id="toast-final" class="fixed bottom-8 right-8 z-[9999] transform translate-y-20 opacity-0 transition-all duration-500 ease-in-out pointer-events-none">
    <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 text-white px-8 py-5 rounded-2xl shadow-2xl flex items-center gap-4 min-w-[320px] pointer-events-auto">
        <i class="fa-solid fa-check-circle text-3xl animate-pulse"></i>
        <div>
            <div class="font-bold text-lg"> Thành công! </div>
            <div id="toast-message-final" class="text-emerald-50 text-sm"></div>
        </div>
        <button onclick="closeToastFinal()" class="ml-auto text-white hover:text-emerald-200 p-2">
            <i class="fa-solid fa-xmark text-xl"></i>
        </button>
    </div>
</div>

<style>
    /* Dùng class riêng biệt để không bị cache trình duyệt đè */
    .show-my-toast {
        transform: translateY(0) !important;
        opacity: 1 !important;
    }
</style>

<script>
    function showToast(message) {
        const toast = document.getElementById('toast-final');
        const msgContainer = document.getElementById('toast-message-final');
        
        if (toast && msgContainer) {
            msgContainer.textContent = message;
            toast.classList.add('show-my-toast');
            
            // Tự động ẩn sau 4 giây
            setTimeout(closeToastFinal, 4000);
        }
    }

    function closeToastFinal() {
        const toast = document.getElementById('toast-final');
        if (toast) {
            toast.classList.remove('show-my-toast');
        }
    }

    // Xử lý Session PHP
    <?php if (isset($_SESSION['success'])): ?>
        // Đợi trang load xong hoàn toàn mới hiện để tránh lỗi tọa độ
        window.addEventListener('load', () => {
            showToast("<?= addslashes($_SESSION['success']) ?>");
            <?php unset($_SESSION['success']); ?>
        });
    <?php endif; ?>

    // Hàm preview ảnh của bạn (giữ nguyên)
    function previewImage(event) {
        const input = event.target;
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('previewAnh');
                if(preview) preview.src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>