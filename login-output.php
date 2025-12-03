<?php
session_start();
require 'db-connect.php';
$page_title = 'ログイン';

unset($_SESSION['customer']); 

$sql = $pdo->prepare('SELECT * FROM customer_user WHERE login_name = ?');
$sql->execute([$_POST['login_name']]);
$customer = $sql->fetch(PDO::FETCH_ASSOC);

if ($customer && password_verify($_POST['password'], $customer['password']) && $customer['status'] == 1) {
    // ログイン成功時の処理
    $_SESSION['customer'] = [
        // ... ユーザー情報 ...
        'user_id'=>$customer['user_id'],
        'mail'=>$customer['mail'],
        'name'=>$customer['name'],
        'address'=>$customer['address'],
        'login_name'=>$customer['login_name'],
        'telephone_number'=>$customer['telephone_number']
    ];
    // ★★★ ログイン成功時はエラーセッションをクリア ★★★
    unset($_SESSION['login_error']);
    header("Location: top.php");
    exit;
} else {
    // ログイン失敗時の処理
    $error = "ログインIDまたはパスワードが違います";
    $_SESSION['login_error'] = $error;
    header("Location: login-input.php");
    exit;
}
?>