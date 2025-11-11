<?php
session_start();
require_once 'db-connect.php';

// ログインチェック（省略している場合は必ず実装してください）
if (!isset($_SESSION['customer']['user_id'])) {
    echo 'カートに追加するにはログインが必要です。<br>';
    echo '<a href="login-input.php">ログインページへ</a>';
    exit;
}

$user_id = $_SESSION['customer']['user_id'];
$product_id = $_POST['product_id'];
$buy_quantity = (int)$_POST['buy_quantity'];

// カートIDを取得または作成
$sql = $pdo->prepare('SELECT cart_id FROM cart WHERE user_id = ?');
$sql->execute([$user_id]);
$cart = $sql->fetch();

if ($cart) {
    $cart_id = $cart['cart_id'];
} else {
    // カートがなければ新規作成
    $insertCart = $pdo->prepare('INSERT INTO cart (user_id, created_at) VALUES (?, NOW())');
    $insertCart->execute([$user_id]);
    $cart_id = $pdo->lastInsertId();
}

// cart-detailテーブルで同じproduct_idがあるか確認
$sql = $pdo->prepare('SELECT * FROM cart_detail WHERE cart_id = ? AND product_id = ?');
$sql->execute([$cart_id, $product_id]);
$cart_detail = $sql->fetch();

if ($cart_detail) {
    // 既存の商品数量を加算して更新
    $new_quantity = $cart_detail['quantity'] + $buy_quantity;
    $update = $pdo->prepare('UPDATE cart_detail SET quantity = ? WHERE cart_detail_id = ?');
    $update->execute([$new_quantity, $cart_detail['cart_detail_id']]);
} else {
    // 新規商品をカート詳細に追加
    $insertDetail = $pdo->prepare('INSERT INTO cart_detail (cart_id, product_id, quantity) VALUES (?, ?, ?)');
    $insertDetail->execute([$cart_id, $product_id, $buy_quantity]);
}

// 商品情報取得（表示用）
$sql = $pdo->prepare('SELECT * FROM product WHERE product_id = ?');
$sql->execute([$product_id]);
$product = $sql->fetch();

echo '<h2>カートに商品を追加しました</h2>';
echo '<p>商品名：' . htmlspecialchars($product['product_name'], ENT_QUOTES, 'UTF-8') . '</p>';
echo '<p>数量：' . htmlspecialchars($buy_quantity, ENT_QUOTES, 'UTF-8') . '個</p>';
echo '<a href="cart.php">カートを見る</a><br>';
echo '<a href="top.php">トップページへ戻る</a>';
?>
