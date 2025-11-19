<h2>カート一覧</h2>
<?php
if (!isset($_SESSION['customer']['user_id'])) {
    echo 'カートを見るにはログインが必要です。<br>';
    echo '<a href="login-input.php">ログインページへ</a>';
    require 'footer.php';
    exit;
}
?> 

<?php
$sql = $pdo->prepare('SELECT cd.cart_detail_id, p.product_name, p.product_picture, cd.price, cd.amount 
                      FROM cart_detail cd 
                      JOIN product p ON cd.product_id = p.product_id 
                      JOIN cart c ON cd.cart_id = c.cart_id 
                      WHERE c.user_id = ?');
$sql->execute([$_SESSION['customer']['user_id']]);
$has_items = false;
$total_price = 0;
if ($sql->rowCount() == 0) {
    echo 'カートに商品が入っていません。';
    require 'footer.php';
    exit;
}
else{
echo '<table>';
echo '<tr><th>商品画像</th><th>商品名</th><th>価格</th><th>個数</th><th>小計</th><th></th></tr>';
foreach($sql as $row){
    $has_items = true;
    $subtotal = $row['price'] * $row['amount'];
    $total_price += $subtotal;
    echo '<tr>';
    echo '<td><img src="img/' . htmlspecialchars($row['product_picture'], ENT_QUOTES, 'UTF-8') . '" width="100px"></td>';
    echo '<td>' . htmlspecialchars($row['product_name'], ENT_QUOTES, 'UTF-8') . '</td>';
    echo '<td>';
        // 個数を変更するためのフォーム
        // 個数変更処理を行うファイルを cart-update.php と仮定
        echo '<form action="cart-update.php" method="post" style="display:inline-flex; align-items:center;">';
        
        // 個数変更に必要な情報（cart_detail_id）を隠しフィールドで渡す
        echo '<input type="hidden" name="cart_detail_id" value="' . htmlspecialchars($row['cart_detail_id'], ENT_QUOTES, 'UTF-8') . '">';
        
        // 減らすボタン
        // amountを -1 に設定し、サーバー側で現在のamountから減算処理を行う
        echo '<input type="submit" name="change_amount" value="-" ' . ($row['amount'] <= 1 ? 'disabled' : '') . '>'; 
        
        // 現在の個数表示（更新時は name="amount" で新しい個数を渡すことも考えられるが、ここではボタンで増減の指示を出す）
        echo '<span style="margin: 0 10px;">' . htmlspecialchars($row['amount'], ENT_QUOTES, 'UTF-8') . '</span>';
        
        // 増やすボタン
        // amountを +1 に設定し、サーバー側で現在のamountに加算処理を行う
        echo '<input type="submit" name="change_amount" value="+">'; 
        
        echo '</form>';
        echo '</td>';
        // ★★★ 個数変更フォームの追加 終了 ★★★
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
    echo '</table>';
    echo '<form action="payment.php" method="post">';
    echo '<input type="hidden" name="total_price" value="' . htmlspecialchars($total_price, ENT_QUOTES, 'UTF-8') . '">';
    echo '<input type="hidden" name="user_id" value="' . htmlspecialchars($_SESSION['customer']['user_id'], ENT_QUOTES, 'UTF-8') . '">';
    echo '<input type="hidden" name="cart_id" value="' . htmlspecialchars($cart_id, ENT_QUOTES, 'UTF-8') . '">';
    echo '<input type="submit" value="購入手続きへ進む">';
    echo '</form>';
}
}
?>

<?php require 'footer.php'?>