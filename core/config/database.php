<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');
if (!function_exists('base_url')) {
    function base_url($path = '')
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
        $host = $_SERVER['HTTP_HOST'];
        $dir = dirname($_SERVER['SCRIPT_NAME']);
        $dir = $dir === '/' ? '' : rtrim($dir, '/');
        return $protocol . $host . $dir . '/' . ltrim($path, '/');
    }
}

// core/config/Database.php
class Database
{
    private $host = '127.0.0.1';       // ĐỔI DÒNG NÀY TỪ 'localhost' THÀNH '127.0.0.1'
    private $db_name = 'heorunglai1';   // tên DB của bạn
    private $username = 'root';
    private $password = '';
    public $conn;

    public function getConnection()
    {
        $this->conn = null;
        try {
            // Có thể thêm port cho chắc chắn (mặc định MariaDB trong XAMPP là 3306)
            $this->conn = new PDO("mysql:host=" . $this->host . ";port=3306;dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8mb4"); // utf8mb4 tốt hơn utf8
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            echo "Lỗi kết nối CSDL: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
