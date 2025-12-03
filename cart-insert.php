<?php
session_start();
require_once 'db-connect.php';
?>
<?php require 'header.php'; ?>
<?php require 'menu.php'; ?>

<div class="container is-max-desktop p-5 mt-5">

<?php
if (!isset($_SESSION['customer']['user_id'])) {
    echo '<div class="notification is-warning has-text-centered">';
    echo 'カートに追加するには**ログイン**が必要です。<br>';
    echo '<a href="login-input.php" class="button is-link is-light mt-3">ログインページへ</a>';
    echo '</div>';
    require 'footer.php';
    exit;
}

$user_id = $_SESSION['customer']['user_id'];

// POSTデータの存在と型を安全にチェック
$product_id = $_POST['product_id'] ?? null;
$buy_amount = (int)($_POST['buy_quantity'] ?? 0);

if (!$product_id || $buy_amount <= 0) {
    echo '<div class="notification is-danger">商品IDまたは数量が不正です。</div>';
    require 'footer.php';
    exit;
}

// 商品の価格と名前をproductテーブルから取得（名前も表示のために取得）
$sql = $pdo->prepare('SELECT price, product_name FROM product WHERE product_id = ?');
$sql->execute([$product_id]);
$product = $sql->fetch(PDO::FETCH_ASSOC); // 連想配列で取得

if (!$product) {
    echo '<div class="notification is-danger">指定された商品が見つかりません。</div>';
    require 'footer.php';
    exit;
}
$product_price = $product['price'];
$product_name = $product['product_name'];


// --- データベース処理 ---

// カートIDを取得または作成
$sql = $pdo->prepare('SELECT cart_id FROM cart WHERE user_id = ?');
$sql->execute([$user_id]);
$cart = $sql->fetch(PDO::FETCH_ASSOC);

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
$cart_detail = $sql->fetch(PDO::FETCH_ASSOC);

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


// --- 結果の表示 ---

echo '<div class="box has-text-centered">';
echo '<h2 class="title is-4 has-text-success">';
echo '<span class="icon"><i class="fas fa-check-circle"></i></span>';
echo '<span> カートに商品を追加しました</span>';
echo '</h2>';

echo '<div class="content mb-5">';
echo '<p><strong>商品名：</strong>' . htmlspecialchars($product_name ?? '不明') . '</p>';
echo '<p><strong>数量：</strong>' . htmlspecialchars($buy_amount) . '個</p>';
echo '<p><strong>価格：</strong>' . number_format($product_price) . '円</p>';
echo '</div>';

echo '<div class="buttons is-centered">';
echo '<a href="cart-show.php" class="button is-warning is-large mr-4">';
echo '<span class="icon"><i class="fas fa-shopping-cart"></i></span>';
echo '<span>カートを見る</span>';
echo '</a>';
echo '<a href="top.php" class="button is-link is-light is-large">';
echo '<span class="icon"><i class="fas fa-home"></i></span>';
echo '<span>トップページへ戻る</span>';
echo '</a>';
echo '</div>';

echo '</div>'; // .box 終了
?>
</div>

<?php require 'footer.php'; ?>