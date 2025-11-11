<h2>カート一覧</h2>
<?php
if (!isset($_SESSION['customer']['user_id'])) {
    echo 'カートを見るにはログインが必要です。<br>';
    echo '<a href="login-input.php">ログインページへ</a>';
    exit;
}

<table>
<tr><th>商品画像</th><th>商品名</th>';
<th>価格</th><th>個数</th><th>小計</th><th></th></tr>';
<?php
$sql = $pdo->prepare('SELECT cd.cart_detail_id, p.product_name, p.product_picture, cd.price, cd.amount 
                      FROM cart_detail cd 
                      JOIN product p ON cd.product_id = p.product_id 
                      JOIN cart c ON cd.cart_id = c.cart_id 
                      WHERE c.user_id = ?');
$sql->execute([$_SESSION['customer']['user_id']]);
$has_items = false;
$total_price = 0;
foreach($sql as $row){
    $has_items = true;
    $subtotal = $row['price'] * $row['amount'];
    $total_price += $subtotal;
    echo '<tr>';
    echo '<td><img src="img/' . htmlspecialchars($row['product_picture'], ENT_QUOTES, 'UTF-8') . '" width="100px"></td>';
    echo '<td>' . htmlspecialchars($row['product_name'], ENT_QUOTES, 'UTF-8') . '</td>';
    echo '<td>' . htmlspecialchars($row['price'], ENT_QUOTES, 'UTF-8') . '円</td>';
    echo '<td>' . htmlspecialchars($row['amount'], ENT_QUOTES, 'UTF-8') . '個</td>';
    echo '<td>' . htmlspecialchars($subtotal, ENT_QUOTES, 'UTF-8') . '円</td>';
    echo '<td><form action="cart-delete.php" method="post" style="display:inline;">';
    echo '<input type="hidden" name="cart_detail_id" value="' . htmlspecialchars($row['cart_detail_id'], ENT_QUOTES, 'UTF-8') . '">';
    echo '<input type="submit" value="削除">';
    echo '</form></td>';
    echo '</tr>';
}
if (!$has_items) {
    echo '<tr><td colspan="6">カートに商品が入っていません。</td></tr>';
} else {
    echo '<tr><td colspan="4">合計金額</td><td>' . htmlspecialchars($total_price, ENT_QUOTES, 'UTF-8') . '円</td><td></td></tr>';
}
?>

<?php require 'footer.php'?>