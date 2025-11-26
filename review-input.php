<?php session_start(); ?>
<?php require 'header.php'?>
<?PHP require 'menu.php'?>
<?php require 'db-connect.php' ?>
<?php

$sql = $pdo->prepare('select * from product where product_id = ?');
$sql->execute([$_POST['product_id']]);
$product = $sql->fetch();
echo '<h2>商品レビュー</h2>';
echo '<p><img src="img/products/' . htmlspecialchars($product['product_picture'], ENT_QUOTES, 'UTF-8') . '" alt="' . htmlspecialchars($product['product_name'], ENT_QUOTES, 'UTF-8') . '" style="max-width:40%;height:40px;"></p>';
echo '<p>商品名: ' . htmlspecialchars($product['product_name'], ENT_QUOTES, 'UTF-8') . '</p>';
echo '<p>評価点</p>';   
echo '<form action="review-output.php" method="post">';
echo '<input type="hidden" name="product_id" value="' . htmlspecialchars($product['product_id'], ENT_QUOTES, 'UTF-8') . '">';
for ($i = 1; $i <= 5; $i++) {
    echo '<input type="radio" name="rating" value="' . $i . '">' . $i . ' ';
}
echo '<p>コメント</p>';
echo '<textarea name="comment" rows="4" cols="40"></textarea></p>';
echo '<input type="submit" value="投稿">';
echo '</form>';
?>