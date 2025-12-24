<?php
// Mật khẩu gốc bạn muốn dùng
$pass_thanhngan = 'ngan1'; // Cho email thanhnganle36...
$pass_admin     = '1';     // Cho email admin...
$pass_ngan      = '1234';  // Cho email ngan...

echo "<h1>Copy các dòng in đậm bên dưới:</h1>";

echo "1. Hash cho <b>ngan1</b> (thanhnganle36@gmail.com):<br>";
echo "<input type='text' value='" . password_hash($pass_thanhngan, PASSWORD_DEFAULT) . "' style='width:100%; background: yellow'><br><br>";

echo "2. Hash cho <b>1</b> (admin@gmail.com):<br>";
echo "<input type='text' value='" . password_hash($pass_admin, PASSWORD_DEFAULT) . "' style='width:100%; background: yellow'><br><br>";

echo "3. Hash cho <b>1234</b> (ngan@gmail.com):<br>";
echo "<input type='text' value='" . password_hash($pass_ngan, PASSWORD_DEFAULT) . "' style='width:100%; background: yellow'><br><br>";
?>