
<!DOCTYPE html>
<html lang="vi" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Heo Rừng Lai</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .hidden { display: none; }
    </style>
</head>
<body class="h-full bg-gradient-to-br from-emerald-50 via-green-50 to-teal-100 flex items-center justify-center min-h-screen py-8 px-4">

<div class="w-full max-w-sm">
    <div class="bg-white rounded-3xl shadow-2xl overflow-hidden">

        <!-- Header xanh -->
        <div class="bg-gradient-to-r from-emerald-600 to-teal-700 p-8 text-white text-center">
            <div class="w-20 h-20 bg-white rounded-full mx-auto mb-4 flex items-center justify-center shadow-lg">
                <svg class="w-12 h-12 text-emerald-600" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                </svg>
            </div>
            <h1 class="text-3xl font-extrabold">Heo Rừng Lai</h1>
            <p class="mt-1 text-emerald-100 text-sm">Hệ thống quản lý trang trại</p>
        </div>

        <div class="p-8 pb-10">

            <!-- ==================== FORM ĐĂNG NHẬP ==================== -->
            <div id="loginForm">
                <form action="index.php?url=auth/process" method="POST" class="space-y-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email</label>
                        <input type="email" name="email" required autofocus
                               class="w-full px-4 py-3.5 border border-gray-300 rounded-xl focus:ring-4 focus:ring-emerald-200 focus:border-emerald-500 outline-none"
                               placeholder="admin@gmail.com">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Mật khẩu</label>
                        <input type="password" name="password" required
                               class="w-full px-4 py-3.5 border border-gray-300 rounded-xl focus:ring-4 focus:ring-emerald-200 focus:border-emerald-500 outline-none"
                               placeholder="••••••••">
                    </div>

                    <?php if (isset($_GET['error'])): ?>
                        <div class="bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded-xl text-center text-sm">Sai email hoặc mật khẩu!</div>
                    <?php endif; ?>

                    <button type="submit" class="w-full bg-gradient-to-r from-emerald-600 to-teal-700 hover:from-emerald-700 hover:to-teal-800 text-white font-bold py-4 rounded-xl shadow-lg transition">
                        Đăng nhập ngay
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-gray-600 text-sm">
                        Chưa có tài khoản? 
                        <a href="#" id="showRegister" class="font-bold text-emerald-600 hover:underline">Đăng ký ngay</a>
                    </p>
                </div>
            </div>

            <!-- ==================== FORM ĐĂNG KÝ (ẩn mặc định) ==================== -->
            <div id="registerForm" class="hidden">
                <form action="index.php?url=auth/register" method="POST" class="space-y-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Họ và tên</label>
                        <input type="text" name="HoTen" required
                               class="w-full px-4 py-3.5 border border-gray-300 rounded-xl focus:ring-4 focus:ring-emerald-200 focus:border-emerald-500 outline-none"
                               placeholder="Nguyễn Văn A">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email</label>
                        <input type="email" name="email" required
                               class="w-full px-4 py-3.5 border border-gray-300 rounded-xl focus:ring-4 focus:ring-emerald-200 focus:border-emerald-500 outline-none"
                               placeholder="abc@gmail.com">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Mật khẩu</label>
                        <input type="password" name="password" required minlength="6"
                               class="w-full px-4 py-3.5 border border-gray-300 rounded-xl focus:ring-4 focus:ring-emerald-200 focus:border-emerald-500 outline-none"
                               placeholder="Tối thiểu 6 ký tự">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nhập lại mật khẩu</label>
                        <input type="password" name="password_confirm" required
                               class="w-full px-4 py-3.5 border border-gray-300 rounded-xl focus:ring-4 focus:ring-emerald-200 focus:border-emerald-500 outline-none"
                               placeholder="Nhập lại mật khẩu">
                    </div>

                    <?php if (isset($_GET['reg_success'])): ?>
                        <div class="bg-green-50 border border-green-300 text-green-700 px-4 py-3 rounded-xl text-center text-sm font-medium">
                            Đăng ký thành công! Vui lòng đăng nhập
                        </div>
                    <?php elseif (isset($_GET['reg_error'])): ?>
                        <div class="bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded-xl text-center text-sm">
                            <?= htmlspecialchars($_GET['reg_error']) ?>
                        </div>
                    <?php endif; ?>

                    <button type="submit" class="w-full bg-gradient-to-r from-emerald-600 to-teal-700 hover:from-emerald-700 hover:to-teal-800 text-white font-bold py-4 rounded-xl shadow-lg transition">
                        Tạo tài khoản
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-gray-600 text-sm">
                        Đã có tài khoản? 
                        <a href="#" id="showLogin" class="font-bold text-emerald-600 hover:underline">Đăng nhập</a>
                    </p>
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-8 pt-6 border-t border-gray-200 text-center">
                <p class="text-gray-500 text-xs">© 2025 Trang trại Heo Rừng Lai</p>
            </div>
        </div>
    </div>

    <!-- JS chuyển form (không cần jQuery) -->
    <script>
        document.getElementById('showRegister').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('loginForm').classList.add('hidden');
            document.getElementById('registerForm').classList.remove('hidden');
        });
        document.getElementById('showLogin').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('registerForm').classList.add('hidden');
            document.getElementById('loginForm').classList.remove('hidden');
        });

        // Nếu đăng ký thành công → tự động hiện form đăng nhập
        <?php if (isset($_GET['reg_success'])): ?>
            document.getElementById('loginForm').classList.add('hidden');
            document.getElementById('registerForm').classList.remove('hidden');
            setTimeout(() => {
                document.getElementById('registerForm').classList.add('hidden');
                document.getElementById('loginForm').classList.remove('hidden');
            }, 3000);
        <?php endif; ?>
    </script>
</body>
</html>