<?php
session_start();
require 'db-connect.php';
require 'header.php';
require 'menu.php';
?>

<?php
$user_id = $_POST['user_id'];

$sql = "SELECT name, user_name, address, telephone_number
        FROM customer_user
        WHERE user_id = :user_id";

$stmt = $pdo->prepare($sql);
$stmt->execute([':user_id' => $user_id]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<h2>氏名：<?= htmlspecialchars($user['name']) ?></h2>
<p>ユーザー名：<?= htmlspecialchars($user['user_name']) ?></p>
<p>住所：<?= htmlspecialchars($user['address']) ?></p>
<p>電話番号：<?= htmlspecialchars($user['telephone_number']) ?></p>

<form action="top.php" method="get">  
<button type="submit">トップへ戻る</button>
</form>

<?php require 'footer.php'; ?>