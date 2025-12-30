<?php
// core/controllers/AuthController.php

require_once __DIR__ . '/../models/UserModel.php';

class AuthController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    // DÒNG DUY NHẤT CẦN SỬA – CHẠY 100% LUÔN!
    public function showLogin()
    {
       require_once dirname(__DIR__, 2) . '/admin/views/auth/login.php';
    }

    public function processLogin()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?url=login');
            exit;
        }

        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $user = $this->userModel->login($email, $password);

        if ($user) {
            $_SESSION['user'] = [                    // XÓA DẤU {} THỪA Ở ĐÂY
                'id'     => $user['UserID'],
                'HoTen'  => $user['HoTen'],
                'email'  => $user['Email']
            ];
            header('Location: index.php?url=dashboard');
            exit;
        } else {
            header('Location: index.php?url=login&error=1');
            exit;
        }
    }
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->showLogin();
            return;
        }

        $hoTen    = trim($_POST['HoTen'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['password_confirm'] ?? '';

        if ($password !== $confirm) {
            header('Location: index.php?url=login&reg_error=Mật khẩu không khớp');
            exit;
        }
        if (strlen($password) < 6) {
            header('Location: index.php?url=login&reg_error=Mật khẩu ít nhất 6 ký tự');
            exit;
        }

        $result = $this->userModel->register($hoTen, $email, $password);

        if ($result === true) {
            header('Location: index.php?url=login&reg_success=1');
        } else {
            header('Location: index.php?url=login&reg_error=' . urlencode($result));
        }
        exit;
    }

    public function logout()
    {
        session_destroy();
        header('Location: index.php?url=login');
        exit;
    }
}
