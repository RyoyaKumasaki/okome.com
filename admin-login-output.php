<?php
session_start();
require 'db-connect.php';
$page_title = 'ログイン';

unset($_SESSION['admin']);

$sql = $pdo->prepare('SELECT * FROM `admin` WHERE name = ?');
$sql->execute([$_POST['login_name']]);
$admin = $sql->fetch(PDO::FETCH_ASSOC);

if ($admin && password_verify($_POST['password'], $admin['password'])) {
    $_SESSION['admin'] = [
        'admin_id'=>$admin['admin_id'],
        'mail'=>$admin['mail'],
        'name'=>$admin['name'],
    ];
    header("Location: controlltop.php");
    exit;
} else {
    header("Location: admin-login-input.php");
    exit;
}
require 'controllheader.php';
require 'admin-menu.php';
require 'footer.php';
?>
