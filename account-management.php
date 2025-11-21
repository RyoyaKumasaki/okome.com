<?php
session_start();
require 'db-connect.php';
$page_title = 'アカウント管理';
require 'controllheader.php';
require 'admin-menu.php';
?>

<?php
$user_name = $_POST['user_name'];

$sql = "SELECT * FROM customer_user
        WHERE login_name = :user_name"; //name, user_name, address, telephone_number, mail

$stmt = $pdo->prepare($sql);
$stmt->execute([':user_name' => $user_name]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<h2>氏名：<?= htmlspecialchars($user['name']); ?></h2>
<p>ユーザー名：<?= htmlspecialchars($user['login_name']); ?></p>
<p>メールアドレス：<?= htmlspecialchars($user['mail']); ?></p>
<p>住所：<?= htmlspecialchars($user['address']); ?></p>
<p>電話番号：<?= htmlspecialchars($user['telephone_number']); ?></p>

<form action="controlltop.php" method="get">  
<button type="submit">トップへ戻る</button>
</form>

<?php require 'footer.php'; ?>