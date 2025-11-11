<?php session_start(); ?>
<?php require 'header.php'?>
<?php require 'menu.php'?>
<?php
$sql = $pdo->prepare('DELETE FROM cart_detail WHERE cart_detail_id = ?');
$sql->execute([$_POST['cart_detail_id']]);
echo '商品を削除しました。';
echo '<hr>';
require 'cart.php';
?>
<?php require 'footer.php'?>