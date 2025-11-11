<?php session_start(); ?>
<?php require 'header.php'; ?>
<?php require 'menu.php'; ?>
<form action="product-detail.php" method="post">
<input type="text" name="product_id" placeholder="商品検索">
<input type="submit" value="検索">
</form>

<a href="mypage.php"><img src="img/guest.png" width="150px"></a>
<?php require 'ranking.php'; ?>
<?php require 'product.php'; ?>
<?php require 'AI-concierge.php'; ?>
<?php require 'footer.php'; ?>
