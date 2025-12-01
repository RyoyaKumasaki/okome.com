<div class="container is-max-desktop p-4">
    <h1 class="title is-3 has-text-centered">カート一覧</h1>
    <hr>

    <?php
    // ログインチェック
    if (!isset($_SESSION['customer']['user_id'])) {
        echo '<div class="notification is-warning has-text-centered">';
        echo 'カートを見るにはログインが必要です。<br>';
        echo '<a href="login-input.php" class="button is-link is-light mt-3">ログインページへ</a>';
        echo '</div>';
        require 'footer.php';
        exit;
    }
    ?> 

    <?php
    // SQLクエリ
    $sql = $pdo->prepare('SELECT cd.cart_detail_id, p.product_name, p.product_picture, p.product_id, cd.price, cd.amount 
                         FROM cart_detail cd 
                         JOIN product p ON cd.product_id = p.product_id 
                         JOIN cart c ON cd.cart_id = c.cart_id 
                         WHERE c.user_id = ?');
    $sql->execute([$_SESSION['customer']['user_id']]);
    $total_price = 0;

    // カートが空の場合の表示
    if ($sql->rowCount() == 0) {
        echo '<div class="notification is-info has-text-centered">';
        echo 'カートに商品が入っていません。<br>';
        echo '<a href="top.php" class="button is-link is-light mt-3">トップ画面に戻る</a>';
        echo '</div>';
        require 'footer.php';
        exit;
    }
    
    // カートの内容表示
    echo '<div class="box">';
    echo '<table class="table is-striped is-fullwidth is-hoverable">';
    echo '<thead><tr>';
    echo '<th style="width: 100px;">商品画像</th>';
    echo '<th>商品名</th>';
    echo '<th class="has-text-right">単価</th>';
    echo '<th class="has-text-centered" style="width: 150px;">個数</th>';
    echo '<th class="has-text-right">小計</th>';
    echo '<th style="width: 80px;"></th>';
    echo '</tr></thead>';
    echo '<tbody>';

    foreach($sql as $row){
        $subtotal = $row['price'] * $row['amount'];
        $total_price += $subtotal;
        
        // Null合体演算子を適用して非推奨警告を回避
        $product_picture = $row['product_picture'] ?? '';
        $product_name = $row['product_name'] ?? '';
        $price = $row['price'] ?? 0;
        $amount = $row['amount'] ?? 0;
        $cart_detail_id = $row['cart_detail_id'] ?? '';
        
        echo '<tr>';
        
        // 1. 商品画像
        echo '<td><img src="img/products/' . htmlspecialchars($product_picture) . '" alt="' . htmlspecialchars($product_name) . '" width="100px"></td>';
        
        // 2. 商品名
        echo '<td>' . htmlspecialchars($product_name) . '</td>';
        
        // 3. 単価
        echo '<td class="has-text-right">' . number_format($price) . '円</td>';
        
        // 4. 個数変更フォーム（テーブルセルを統合）
        echo '<td class="has-text-centered">';
        
        // 個数変更フォーム
        echo '<form action="cart-update.php" method="post" class="is-flex is-justify-content-center">';
        
        echo '<input type="hidden" name="cart_detail_id" value="' . htmlspecialchars($cart_detail_id) . '">';
        
        // 減らすボタン (is-small, is-danger, is-lightを適用)
        echo '<button type="submit" name="change_amount" value="-" class="button is-small is-danger is-light" ' . ($amount <= 1 ? 'disabled' : '') . '>'; 
        echo '-';
        echo '</button>';
        
        // 現在の個数表示 (Bulma Typographyと余白を適用)
        echo '<span class="px-3 has-text-weight-semibold">' . htmlspecialchars($amount) . '</span>';
        
        // 増やすボタン (is-small, is-info, is-lightを適用)
        echo '<button type="submit" name="change_amount" value="+" class="button is-small is-info is-light">'; 
        echo '+';
        echo '</button>';
        
        echo '</form>';
        echo '</td>';
        
        // 5. 小計
        echo '<td class="has-text-right has-text-weight-bold">' . number_format($subtotal) . '円</td>';
        
        // 6. 削除ボタン
        echo '<td>';
        echo '<form action="cart-delete.php" method="post">';
        echo '<input type="hidden" name="cart_detail_id" value="' . htmlspecialchars($cart_detail_id) . '">';
        // 削除ボタン (is-small, is-dangerを適用)
        echo '<input type="submit" value="削除" class="button is-small is-danger is-outlined is-fullwidth">';
        echo '</form>';
        echo '</td>';
        
        echo '</tr>';
    }

    echo '</tbody>';
    echo '<tfoot>';
    echo '<tr>';
    echo '<td colspan="4" class="has-text-right has-text-weight-bold is-size-5">合計金額</td>';
    echo '<td class="has-text-right has-text-weight-bold has-text-danger is-size-5">' . number_format($total_price) . '円</td>';
    echo '<td></td>';
    echo '</tr>';
    echo '</tfoot>';
    echo '</table>';
    echo '</div>'; // .box 終了

    // 購入手続きボタン
    echo '<div class="has-text-right mt-5">';
    echo '<form action="payment.php" method="post" style="display:inline;">';
    echo '<input type="hidden" name="total_price" value="' . htmlspecialchars($total_price) . '">';
    echo '<input type="hidden" name="user_id" value="' . htmlspecialchars($_SESSION['customer']['user_id']) . '">';
    // $cart_id が null の場合は空文字列（''）に置き換えてから htmlspecialchars に渡す
    // ※元のコードにあった echo htmlspecialchars($cart_id ?? ''); は表示が不要なため削除。
    //   $cart_id がフォームで必要なら <input type="hidden"> で渡すべきです。
    echo '<input type="submit" value="購入手続きへ進む" class="button is-primary is-large">';
    echo '</form>';
    echo '</div>';
    ?>
</div>

<?php require 'footer.php'?>