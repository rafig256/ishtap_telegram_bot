<?php
// فایل connect.php برای اتصال به پایگاه داده
$host = 'localhost';
$dbname = 'varavi_bot';
$username = 'varavi_rafig';
$password = 'Rafig@256#256'; // رمز عبور را جایگزین کنید

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("خطا در اتصال به پایگاه داده: " . $e->getMessage());
}
?>
