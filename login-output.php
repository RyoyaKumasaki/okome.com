<?php
session_start();
require 'db-connect.php';
require 'header.php';
require 'menu.php';

unset($_SESSION['customer']);

$sql = $pdo->prepare('SELECT * FROM customer_user WHERE login_name = ?');
$sql->execute([$_POST['login_name']]);
$customer = $sql->fetch(PDO::FETCH_ASSOC);

if ($customer && password_verify($_POST['password'], $customer['password'])) {
    $_SESSION['customer'] = [
        // 'id' => $customer['id'],
        // 'name' => $customer['name'],
        // 'address' => $customer['address'],
        // 'login' => $customer['login']
        'user_id'=>$customer['user_id'],
        'mail'=>$customer['mail'],
        'password'=>$customer['password'],
        'name'=>$customer['name'],
        'address'=>$customer['address'],
        'login_name'=>$customer['login_name'],
        'telephone_number'=>$customer['telephone_number']
    ];
    echo 'ログイン成功!ようこそ ' . htmlspecialchars($customer['name'], ENT_QUOTES, 'UTF-8');
} else {
    echo 'ログイン名またはパスワードが違います。';
}

require 'footer.php';
?>
