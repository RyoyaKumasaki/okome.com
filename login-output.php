<?php
session_start();
require 'db-connect.php';
require 'header.php';
require 'menu.php';

$pdo = new PDO($connect, USER, PASS);

$login = $_POST['login'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($login) || empty($password)) {
    echo '<p>ログインIDとパスワードを入力してください。</p>';
    echo '<p><a href="login-input.php">戻る</a></p>';
    require 'footer.php';
    exit;
}
$sql = $pdo->prepare('SELECT * FROM customer WHERE login = ?');
$sql->execute([$login]);
$customer = $sql->fetch(PDO::FETCH_ASSOC);

if ($customer && password_verify($password, $customer['password'])) {
    $_SESSION['customer'] = [
        'id' => $customer['id'],
        'name' => $customer['name'],
        'login' => $customer['login']
    ];

    echo '<p>' . htmlspecialchars($customer['name'], ENT_QUOTES) . ' さん、ようこそ！</p>';
    echo '<p><a href="index.php">トップページへ</a></p>';

} else {
    echo '<p>ログインIDまたはパスワードが違います。</p>';
    echo '<p><a href="login-input.php">戻る</a></p>';
}

require 'footer.php';
?>
