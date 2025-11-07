<?php require 'header.php'; ?>
<?php require 'menu.php'; ?>
<form action="product.php" method="post">
<input type="num" name="product_id">
<input type="submit" value="商品詳細へ">
</form>
<a href="mypage.php">
    <img src="" alt="">
</a>
<h2>
    ランキング
</h2>
<?php
require_once 'db-connect.php';
$sql = $pdo->prepare('select * from reviw order by rating desc limit 3');
<?php require 'footer.php'; ?>
