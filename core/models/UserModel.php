<?php
class UserModel
{
    private $pdo;   // đổi từ $db thành $pdo

    public function __construct()
    {
        $database = new Database();
        $this->pdo = $database->getConnection();   // đổi $db → $pdo
    }

    public function findByEmail($email)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE Email = ? LIMIT 1");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function login($email, $password)
    {
        $stmt = $this->pdo->prepare("SELECT UserID, HoTen, Email, password FROM users WHERE Email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    // hàm register bạn thêm vào đây luôn cho đầy đủ
    public function register($hoTen, $email, $password)
    {
        $check = $this->findByEmail($email);
        if ($check) {
            return "Email đã được sử dụng!";
        }

        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("INSERT INTO users (HoTen, Email, password) VALUES (?, ?, ?)");
        return $stmt->execute([$hoTen, $email, $hashed]) ? true : "Đăng ký thất bại!";
    }
}