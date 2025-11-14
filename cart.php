<h2>カート一覧</h2>
<?php
if (!isset($_SESSION['customer']['user_id'])) {
    echo 'カートを見るにはログインが必要です。<br>';
    echo '<a href="login-input.php">ログインページへ</a>';
    require 'footer.php';
    exit;
}

$user_id = $_SESSION['customer']['user_id'];
$cart_id = null; 

// ★修正ポイント2: Cartテーブルからcart_idを取得する

    $sql_get_cart = $pdo->prepare('SELECT cart_id FROM Cart WHERE user_id = ?'); 
    $sql_get_cart->execute([$user_id]);
    $cart_row = $sql_get_cart->fetch(PDO::FETCH_ASSOC);

    if ($cart_row) {
        $cart_id = $cart_row['cart_id'];
    } else {
        // カート自体が存在しない場合（通常はありえないが、念のため）
        echo 'カートに商品が入っていません。';
        require 'footer.php';
        exit;
    }


// カート明細の取得
$sql = $pdo->prepare('SELECT cd.cart_detail_id, p.product_name, p.product_picture, cd.price, cd.amount 
                     FROM Cart_Item cd 
                     JOIN Product p ON cd.product_id = p.product_id 
                     JOIN Cart c ON cd.cart_id = c.cart_id 
                     WHERE c.user_id = ?'); // ★修正
$sql->execute([$user_id]);

$has_items = false;
$total_price = 0;

if ($sql->rowCount() == 0) {
    echo 'カートに商品が入っていません。';
    require 'footer.php';
    exit;
} else {
    echo '<table>';
    echo '<tr><th>商品画像</th><th>商品名</th><th>価格</th><th>個数</th><th>小計</th><th></th></tr>';
    
    foreach($sql as $row){
        $has_items = true;
        $subtotal = $row['price'] * $row['amount'];
        $total_price += $subtotal;
        echo '<tr>';
        echo '<td><img src="img/' . htmlspecialchars($row['product_picture'], ENT_QUOTES, 'UTF-8') . '" width="100px"></td>';
        echo '<td>' . htmlspecialchars($row['product_name'], ENT_QUOTES, 'UTF-8') . '</td>';
        
        // 個数変更フォーム（ここから）
        echo '<td>';
        echo '<form action="cart-update.php" method="post" style="display:inline-flex; align-items:center;">';
        
        echo '<input type="hidden" name="cart_detail_id" value="' . htmlspecialchars($row['cart_detail_id'], ENT_QUOTES, 'UTF-8') . '">';
        
        // 減らすボタン
        echo '<input type="submit" name="change_amount" value="-" ' . ($row['amount'] <= 1 ? 'disabled' : '') . '>'; 
        
        echo '<span style="margin: 0 10px;">' . htmlspecialchars($row['amount'], ENT_QUOTES, 'UTF-8') . '</span>';
        
        // 増やすボタン
        echo '<input type="submit" name="change_amount" value="+">'; 
        
        echo '</form>';
        echo '</td>';
        // 個数変更フォーム（ここまで）
        
        echo '<td>' . htmlspecialchars(number_format($row['price']), ENT_QUOTES, 'UTF-8') . '円</td>'; // 価格はカンマ区切りに修正
        echo '<td>' . htmlspecialchars($row['amount'], ENT_QUOTES, 'UTF-8') . '個</td>';
        echo '<td>' . htmlspecialchars(number_format($subtotal), ENT_QUOTES, 'UTF-8') . '円</td>'; // 小計もカンマ区切りに修正
        
        echo '<td><form action="cart-delete.php" method="post" style="display:inline;">';
        echo '<input type="hidden" name="cart_detail_id" value="' . htmlspecialchars($row['cart_detail_id'], ENT_QUOTES, 'UTF-8') . '">';
        echo '<input type="submit" value="削除">';
        echo '</form></td>';
        echo '</tr>';
    }

    // ★修正ポイント4: 合計金額の表示と購入ボタン
    echo '<tr><td colspan="4">合計金額</td><td>' . htmlspecialchars(number_format($total_price), ENT_QUOTES, 'UTF-8') . '円</td><td></td></tr>';
    echo '</table>';
    echo '<form action="payment-test.php" method="post">';
    echo '<input type="hidden" name="total_price" value="' . htmlspecialchars($total_price, ENT_QUOTES, 'UTF-8') . '">';
    echo '<input type="hidden" name="user_id" value="' . htmlspecialchars($user_id, ENT_QUOTES, 'UTF-8') . '">';
    echo '<input type="hidden" name="cart_id" value="' . htmlspecialchars($cart_id, ENT_QUOTES, 'UTF-8') . '">'; // ★修正ポイント5: 定義された $cart_id を使用
    echo '<input type="submit" value="購入手続きへ進む">';
    echo '</form>';
}
?>

<?php require 'footer.php'?>