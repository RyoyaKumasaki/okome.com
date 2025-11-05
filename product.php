<?php require 'header.php'?>
<?php require 'menu.php'?>
<?php require_once 'db-connect.php'?>
<a href="top.html">トップ画面へ戻る</a>
<?php
//トップ画面で選択した商品のIDを取得し、商品情報をDBに照合
$product_id = $_POST['product_id'];
$sql = $pdo->prepare('select * from product where product_id = ?');
$sql->execute([$product_id]);
foreach($pdo as $row){
    $product_name = $row['product_name'];
    $quantity = $row['quantity'];
    $price = $row['price'];
    $product_explanation = $row['product_explanation'];
    $product_picture = $row['product_picture'];
    $producer_picture = $row['producer_picture'];
}
//商品情報を表示
echo '<h2>' . $product_name . '</h2>';
echo '<img src="images/' . $product_picture . '" width="300px"><br>';
echo '<p> 価格：' . $price . '円</p>';
echo '<p> 在庫数：' . $quantity . '個</p>';
echo '<p>購入個数</p>';
echo '<form action="cart-insert.php" method="post">';
echo '<select name="buy_quantity">';
for ($i = 1; $i <= $quantity; $i++) {
    echo '<option value="' . $i . '">' . $i . '個</option>';
}
echo '</select>';echo '<input type="hidden" name="product_id" value="' . $product_id . '">';
echo '<input type="submit" value="カートに入れる">';
echo '<img src="images/' . $producer_picture . '" width="100px"><br>';
echo '<p> 商品説明：' . $product_explanation . '</p>';
echo '</form>';
$sql = $pdo->prepare('select * from review where product_id = ?');
$sql->execute([$product_id]);
echo '<h3>レビュー一覧</h3>';
//商品IDに合致するレビューを取得し、一覧表示
foreach($pdo as $row){
    $user_id = $row['user_id'];
    $rating = $row['rating'];
    $comment = $row['comment'];
    echo '<p>投稿者名：' . $user_id . '</p>';
    echo '<p>評価：' . str_repeat('★', $rating) . str_repeat('☆', 5 - $rating) . '</p>';
    echo '<p>レビュー内容：' . $comment . '</p>';
    echo '<hr>';
}
?>
<hr>
