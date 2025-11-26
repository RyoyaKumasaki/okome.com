
<?php require 'header.php'?>
<?PHP require 'menu.php'?>
<?php require 'db-connect.php' ?>
<?php
try{
$user_id = $_SESSION['customer']['user_id'];
$product_id = $_POST['product_id'] ?? '';
$rating = (int)($_POST['rating'] ?? 0);
$comment = $_POST['comment'] ?? '';
$sql = $pdo->prepare('insert into review (product_id, rating, comment) values (?, ?, ?)');
$sql->execute([$product_id, $rating, $comment]);
} catch (PDOException $e) {
    echo 'データベースエラー: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    exit;
}
?>
<p>レビューを送信しました。ありがとうございました。</p>