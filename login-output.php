<?php
session_start();
require 'db-connect.php';
require 'header.php';
require 'menu.php';

$pdo = new PDO($connect, USER, PASS);

$login = $_POST['login'] ?? '';
$password = $_POST['password'] ?? '';

$sql = $pdo->prepare('SELECT * FROM customer WHERE login = ?');
$sql->execute([$login]);
$customer = $sql->fetch(PDO::FETCH_ASSOC);

if ($customer && password_verify($password, $customer['password'])) {
    $_SESSION['customer'] = [
        'id' => $customer['id'],
        'name' => $customer['name'],
        'address' => $customer['address'],
        'login' => $customer['login']
    ];
    echo 'ログイン成功!ようこそ ' . htmlspecialchars($customer['name'], ENT_QUOTES, 'UTF-8');
} else {
    echo 'ログイン名またはパスワードが違います。';
}

require 'footer.php';
?>
