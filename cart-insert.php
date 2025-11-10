<?php
session_start();
require_once 'db-connect.php';

// ログイン状態の確認（例：未ログインの場合はログインページへ）
if (!isset($_SESSION['user_id'])) {
    echo 'カートに追加するにはログインが必要です。<br>';
    echo '<a href="login.php">ログインページへ</a>';
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = $_POST['product_id'];
$buy_quantity = $_POST['buy_quantity'];

// 商品情報の取得
$sql = $pdo->prepare('SELECT * FROM product WHERE product_id = ?');
$sql->execute([$product_id]);
$product = $sql->fetch();

if (!$product) {
    echo '指定された商品が見つかりません。';
    exit;
}

// 既にカートにあるか確認
$sql = $pdo->prepare('SELECT * FROM cart WHERE user_id = ? AND product_id = ?');
$sql->execute([$user_id, $product_id]);
$cart_item = $sql->fetch();

if ($cart_item) {
    // 既存の数量を更新
    $new_quantity = $cart_item['quantity'] + $buy_quantity;
    $update = $pdo->prepare('UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?');
    $update->execute([$new_quantity, $user_id, $product_id]);
} else {
    // 新規追加
    $insert = $pdo->prepare('INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)');
    $insert->execute([$user_id, $product_id, $buy_quantity]);
}

// カート投入完了メッセージ
echo '<h2>カートに商品を追加しました</h2>';
echo '<p>商品名：' . htmlspecialchars($product['product_name'], ENT_QUOTES, 'UTF-8') . '</p>';
echo '<p>数量：' . htmlspecialchars($buy_quantity, ENT_QUOTES, 'UTF-8') . '個</p>';
echo '<a href="cart.php">カートを見る</a><br>';
echo '<a href="top.php">トップページへ戻る</a>';
?>
