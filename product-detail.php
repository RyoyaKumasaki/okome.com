<<<<<<< HEAD
<?php
session_start();
=======
<?php 
// ----------------------------------------
// セッション開始（必ず最上部）
// ----------------------------------------
session_start();
?>
<?php session_start(); ?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>商品画面</title>
    <style>
        /* ---------------------------------- */
        /* 全体的なリセットと基本スタイル */
        /* ---------------------------------- */
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            background-color: #f8f8f8;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
        }
>>>>>>> ff099d0fc36a89c09d6c8920a50506a53d58a5db
>>>>>>> 600cb0e6ffd661499573b79238cb33605333d476

$page_title = '商品画面';
require 'header.php';
require 'menu.php';
require_once 'db-connect.php';

// POSTから商品ID取得
$product_id = $_POST['product_id'] ?? '';

if ($product_id === '' || !isset($pdo)) {
    echo '<p class="error">商品IDが指定されていないか、データベース接続に失敗しています。</p>';
    exit;
}

// 商品データ取得
$sql = $pdo->prepare('SELECT * FROM product WHERE product_id = ?');
$sql->execute([$product_id]);
$product_data = $sql->fetch(PDO::FETCH_ASSOC);

if (!$product_data) {
    echo '<p class="error">お探しの商品が見つかりませんでした。</p>';
    exit;
}

// null 回避
$product_name        = $product_data['product_name']        ?? '';
$quantity            = $product_data['quantity']            ?? 0;
$price               = $product_data['price']               ?? 0;
$product_explanation = $product_data['product_explanation'] ?? '';
$product_picture     = $product_data['product_picture']     ?? '';
$producer_picture    = $product_data['producer_picture']    ?? '';
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>商品画面</title>

<style>
/* ここに CSS（あなたの書いたやつ全部） */
</style>

</head>
<body>

<div class="product-page-container">

<a href="top.php">トップ画面へ戻る</a>

<h2><?= htmlspecialchars($product_name) ?></h2>

<div class="product-main-info">

    <img src="img/products/<?= htmlspecialchars($product_picture) ?>" 
         class="product-image" alt="商品画像">

    <div class="product-details">
        <p class="price-tag">価格：<?= number_format($price) ?>円</p>
        <p class="stock-info">在庫数：<?= htmlspecialchars($quantity) ?>個</p>

        <div class="cart-form-section">
            <p>購入個数</p>

            <form action="cart-insert.php" method="post">
                <select name="buy_quantity">
                    <?php for ($i = 1; $i <= $quantity; $i++): ?>
                        <option value="<?= $i ?>"><?= $i ?>個</option>
                    <?php endfor; ?>
                </select>

                <input type="hidden" name="product_id" value="<?= htmlspecialchars($product_id) ?>">
                <input type="submit" value="カートに入れる">
            </form>
        </div>

        <div class="producer-section">
            <img src="img/products/<?= htmlspecialchars($producer_picture) ?>" 
                 class="producer-picture" alt="生産者画像">
        </div>

    </div>
</div>

<div class="product-description">
    <h3>商品について</h3>
    <p><?= nl2br(htmlspecialchars((string)$product_explanation)) ?></p>
</div>

<?php
// レビュー一覧
$sql = $pdo->prepare('SELECT * FROM review WHERE product_id = ?');
$sql->execute([$product_id]);

echo '<h3>レビュー一覧</h3>';

foreach ($sql as $row):
    $user_id = $row['user_id'] ?? '名無し';
    $rating  = $row['rating']  ?? 0;
    $comment = $row['comment'] ?? '';
?>
    <div class="review-item">
        <p class="review-user">投稿者：<?= htmlspecialchars($user_id) ?></p>
        <p class="review-rating">
            評価：<?= str_repeat('★', $rating) . str_repeat('☆', 5 - $rating) ?>
        </p>
        <p class="review-comment">
            <?= nl2br(htmlspecialchars($comment)) ?>
        </p>
    </div>
<?php endforeach; ?>

<hr>

</div>

</body>
</html>
