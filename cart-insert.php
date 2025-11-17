<?php
session_start();
require_once 'db-connect.php';
?>
<?php require 'header.php'; ?>
<?php require 'menu.php'; ?>
<?php
if (!isset($_SESSION['customer']['user_id'])) {
    echo 'カートに追加するにはログインが必要です。<br>';
    echo '<a href="login-input.php">ログインページへ</a>';
    exit;
}

$user_id = $_SESSION['customer']['user_id'];
$product_id = $_POST['product_id'];
$buy_amount = (int)$_POST['buy_quantity'];

// 商品の価格をproductテーブルから取得
$sql = $pdo->prepare('SELECT price FROM product WHERE product_id = ?');
$sql->execute([$product_id]);
$product = $sql->fetch();

if (!$product) {
    echo '指定された商品が見つかりません。';
    exit;
}
$product_price = $product['price'];

// カートIDを取得または作成
$sql = $pdo->prepare('SELECT cart_id FROM cart WHERE user_id = ?');
$sql->execute([$user_id]);
$cart = $sql->fetch();

if ($cart) {
    $cart_id = $cart['cart_id'];
} else {
    $insertCart = $pdo->prepare('INSERT INTO cart (user_id) VALUES (?)');
    $insertCart->execute([$user_id]);
    $cart_id = $pdo->lastInsertId();
}

// cart-detailテーブルで同じproduct_idがあるか確認
$sql = $pdo->prepare('SELECT * FROM cart_detail WHERE cart_id = ? AND product_id = ?');
$sql->execute([$cart_id, $product_id]);
$cart_detail = $sql->fetch();

if ($cart_detail) {
    // 既存のamountを加算し、priceを更新（購入時の価格を保持）
    $new_amount = $cart_detail['amount'] + $buy_amount;
    $update = $pdo->prepare('UPDATE cart_detail SET amount = ?, price = ? WHERE cart_detail_id = ?');
    $update->execute([$new_amount, $product_price, $cart_detail['cart_detail_id']]);
} else {
    // 新規商品をcart_detailに追加（amountとpriceを保存）
    $insertDetail = $pdo->prepare('INSERT INTO cart_detail (cart_id, product_id, amount, price) VALUES (?, ?, ?, ?)');
    $insertDetail->execute([$cart_id, $product_id, $buy_amount, $product_price]);
}

echo '<h2>カートに商品を追加しました</h2>';
echo '<p>商品名：' . htmlspecialchars($product_id, ENT_QUOTES, 'UTF-8') . '</p>';
echo '<p>数量：' . htmlspecialchars($buy_amount, ENT_QUOTES, 'UTF-8') . '個</p>';
echo '<p>価格：' . htmlspecialchars($product_price, ENT_QUOTES, 'UTF-8') . '円</p>';
echo '<a href="cart-show.php">カートを見る</a><br>';
echo '<a href="top.php">トップページへ戻る</a>';
?>
<?php require 'footer.php'; ?>
