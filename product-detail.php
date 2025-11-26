<?php
// 1. session_start() の修正: 必ずファイルの出力より前、<?php タグの直後に配置
session_start();
?>
<?php $page_title='商品画面'; ?>
<?php require 'header.php'?>
<?php require 'menu.php'?>
<?php require_once 'db-connect.php'?>

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

/* ---------------------------------- */
/* 商品画面全体 */
/* ---------------------------------- */
.product-page-container {
    max-width: 1000px;
    margin: 0 auto;
    background-color: #fff;
    padding: 30px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
}

h2 {
    font-size: 2.2em;
    color: #0066cc;
    border-bottom: 3px solid #0066cc;
    padding-bottom: 10px;
    margin-top: 0;
    margin-bottom: 20px;
}

/* ---------------------------------- */
/* 商品情報セクション */
/* ---------------------------------- */
.product-main-info {
    display: flex;
    gap: 30px;
    margin-bottom: 40px;
    padding-bottom: 20px;
    border-bottom: 1px solid #eee;
}

.product-image {
    flex-shrink: 0;
    width: 350px; /* 画像サイズに合わせて調整 */
    height: auto;
    border: 1px solid #ddd;
    border-radius: 6px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.product-details {
    flex-grow: 1;
}

.price-tag {
    font-size: 1.8em;
    color: #cc0000;
    font-weight: bold;
    margin: 10px 0;
}

.stock-info {
    font-size: 1.1em;
    color: #555;
    margin-bottom: 20px;
}

/* ---------------------------------- */
/* カートフォーム */
/* ---------------------------------- */
.cart-form-section {
    padding: 15px;
    border: 1px dashed #ccc;
    border-radius: 6px;
    background-color: #fafafa;
    margin-bottom: 20px;
}

select[name="buy_quantity"] {
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 4px;
    margin-right: 15px;
    font-size: 1em;
}

input[type="submit"] {
    background-color: #ff6600;
    color: white;
    border: none;
    padding: 10px 20px;
    font-size: 1.1em;
    font-weight: bold;
    border-radius: 20px;
    cursor: pointer;
    transition: background-color 0.3s;
}

input[type="submit"]:hover {
    background-color: #e65c00;
}

/* ---------------------------------- */
/* 生産者情報と説明 */
/* ---------------------------------- */
.producer-section {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-top: 30px;
    padding: 15px;
    background-color: #f0f0ff; /* 淡い青の背景 */
    border: 1px solid #ccd;
    border-radius: 6px;
}

.producer-picture {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 50%; /* 丸い画像に */
    border: 3px solid #fff;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
}

.product-description {
    margin-top: 20px;
    padding: 15px;
    background-color: #fff;
    border-left: 5px solid #0066cc;
}

/* ---------------------------------- */
/* レビューセクション */
/* ---------------------------------- */
h3 {
    font-size: 1.5em;
    color: #333;
    margin-top: 40px;
    margin-bottom: 15px;
    border-bottom: 1px solid #ddd;
    padding-bottom: 5px;
}

.review-item {
    padding: 15px 0;
    border-bottom: 1px dotted #ccc;
}

.review-item:last-child {
    border-bottom: none;
}

.review-rating {
    font-size: 1.2em;
    color: gold; /* 評価の星の色 */
}

.review-user {
    font-weight: bold;
    color: #555;
    margin-bottom: 5px;
}

.review-comment {
    background-color: #fff;
    padding: 10px;
    border-radius: 4px;
    border: 1px solid #eee;
    margin-top: 5px;
}

/* ---------------------------------- */
/* その他の要素 */
/* ---------------------------------- */
a {
    color: #0066cc;
    text-decoration: none;
    padding: 5px 10px;
    display: inline-block;
    border: 1px solid #0066cc;
    border-radius: 4px;
    margin-bottom: 15px;
    transition: background-color 0.3s, color 0.3s;
}

a:hover {
    background-color: #0066cc;
    color: white;
}

.error {
    color: red;
    font-weight: bold;
    padding: 20px;
    background-color: #ffeaea;
    border: 1px solid red;
    border-radius: 4px;
}

hr {
    border: none;
    height: 1px;
    background-color: #ddd;
    margin: 20px 0;
}
</style>

<div class="product-page-container">
<a href="top.php">トップ画面へ戻る</a>

<?php
// トップ画面で選択した商品のIDを取得（POSTデータがない場合に備えて null 合体演算子を使用）
$product_id = $_POST['product_id'] ?? null;

// 商品IDがない、またはデータベース接続がない場合は処理を中断
if (empty($product_id) || !isset($pdo)) {
    echo '<p class="error">商品IDが指定されていないか、データベース接続に失敗しています。</p>';
    echo '</div>'; 
    exit;
}

// データベース照合
$sql = $pdo->prepare('SELECT * FROM product WHERE product_id = ?');
$sql->execute([$product_id]);
$product_data = $sql->fetch(PDO::FETCH_ASSOC);

if (!$product_data) {
    // データが見つからなかった場合の処理（Deprecatedエラー防止の根本対策）
    echo '<p class="error">お探しの商品が見つかりませんでした。</p>';
    echo '</div>'; 
    exit;
}

// データが見つかったため、変数に格納
$product_name = $product_data['product_name'];
$quantity = $product_data['quantity'];
$price = $product_data['price'];
$product_explanation = $product_data['product_explanation'];
$product_picture = $product_data['product_picture'];
$producer_picture = $product_data['producer_picture'];


// 商品情報を表示
echo '<h2>' . htmlspecialchars($product_name) . '</h2>';

echo '<div class="product-main-info">';
// 商品画像
echo '<img src="img/products/' . htmlspecialchars($product_picture) . '" class="product-image" alt="商品画像">';

echo '<div class="product-details">';
// 価格情報
echo '<p class="price-tag"> 価格：' . number_format($price) . '円</p>';
// 在庫情報
echo '<p class="stock-info"> 在庫数：' . htmlspecialchars($quantity) . '個</p>';

// 購入フォームセクション
echo '<div class="cart-form-section">';
echo '<p>購入個数</p>';
echo '<form action="cart-insert.php" method="post">';
echo '<select name="buy_quantity">';
for ($i = 1; $i <= $quantity; $i++) {
    echo '<option value="' . $i . '">' . $i . '個</option>';
}
echo '</select>';
echo '<input type="hidden" name="product_id" value="' . htmlspecialchars($product_id) . '">';
echo '<input type="submit" value="カートに入れる">';
echo '</form>';
echo '</div>'; // .cart-form-section 終了

// 生産者情報
echo '<div class="producer-section">';
echo '<img src="img/products/' . htmlspecialchars($producer_picture) . '" class="producer-picture" alt="生産者画像">';
echo '</div>'; // .producer-section 終了

echo '</div>'; // .product-details 終了
echo '</div>'; // .product-main-info 終了

// 商品説明
echo '<div class="product-description">';
echo '<h3>商品について</h3>';
// XSS対策と改行処理
echo '<p>' . nl2br(htmlspecialchars($product_explanation)) . '</p>';
echo '</div>';


// レビューの取得と表示
$sql = $pdo->prepare('SELECT * FROM review WHERE product_id = ?');
$sql->execute([$product_id]);
echo '<h3>レビュー一覧</h3>';

foreach($sql as $row){
    // レビューデータも念のため null合体演算子と XSS 対策を適用
    $user_id = $row['user_id'] ?? '名無し';
    $rating = $row['rating'] ?? 0;
    $comment = $row['comment'] ?? '';
    
    echo '<div class="review-item">';
    echo '<p class="review-user">投稿者：' . htmlspecialchars($user_id) . '</p>';
    echo '<p class="review-rating">評価：' . str_repeat('★', $rating) . str_repeat('☆', 5 - $rating) . '</p>';
    echo '<p class="review-comment">レビュー内容：' . nl2br(htmlspecialchars($comment)) . '</p>';
    echo '</div>'; // .review-item 終了
}
?>
<hr>
</div>