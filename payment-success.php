<?php
session_start();
require 'db-connect.php'; 
$page_title = '注文処理結果'; 
require 'header.php';


$payment_choice = $_POST['choice'] ?? null;

function get_payment_method_name($choice) {
    $map = [
        'kure' => 'クレジットカード',
        'pei' => 'PayPay',
        'app' => 'Apple Pay',
        'gen' => 'コンビニ払い'
    ];
    return $map[$choice] ?? '不明な支払い方法';
}
$payment_method_name = get_payment_method_name($payment_choice);


$user_id = $_SESSION['customer']['user_id'] ?? null;
$shipping_info = $_SESSION['shipping_info'] ?? '未設定の配送情報（要修正）'; 

if (!$user_id) {
    require 'menu.php';
    ?>
    <section class="section">
        <div class="container is-max-desktop has-text-centered">
            <div class="notification is-danger">
                <h2 class="title is-4">エラー</h2>
                <p>ユーザーが認証されていません。ログインし直してください。</p>
            </div>
        </div>
    </section>
    <?php
    require 'footer.php';
    exit;
}

$order_id = null; // 最終的に確定した注文IDを保持

try {
    $sql_cart = '
        SELECT 
            cd.product_id, 
            cd.amount AS amount,
            p.price AS unit_price, 
            p.quantity AS stock_quantity 
        FROM cart_detail cd
        JOIN product p ON cd.product_id = p.product_id
        JOIN cart c ON cd.cart_id = c.cart_id
        WHERE c.user_id = ?
    ';
    $stmt_cart = $pdo->prepare($sql_cart);
    $stmt_cart->execute([$user_id]);
    $cart_details = $stmt_cart->fetchAll(PDO::FETCH_ASSOC);

    if (empty($cart_details)) {
        throw new Exception("カートに商品が入っていません。");
    }

    $total_price = 0;
    foreach ($cart_details as $item) {
        $total_price += $item['unit_price'] * $item['amount']; 
    }

    // ----------------------------------------------------
    // 1. トランザクション開始 
    // ----------------------------------------------------
    $pdo->beginTransaction();

    // ----------------------------------------------------
    // 2. Orderテーブルに登録 (注文ヘッダーの作成) 
    // ----------------------------------------------------
    $sql_order = '
        INSERT INTO `order` (user_id, price) 
        VALUES (?, ?)
    ';
    $stmt_order = $pdo->prepare($sql_order);
    $stmt_order->execute([
        $user_id,
        $total_price,
    ]);
    
    // 挿入された注文IDを取得 
    $order_id = $pdo->lastInsertId();

    // ----------------------------------------------------
    // 3. Order_Itemに登録（注文明細の作成）＆ Productの在庫更新 
    // ----------------------------------------------------
    $sql_item = '
        INSERT INTO order_detail (order_id, product_id, count, order_price)
        VALUES (?, ?, ?, ?)
    ';
    // 在庫チェックと更新を同時に行うSQL 
    $sql_stock_update = '
        UPDATE product
        SET quantity = quantity - ? 
        WHERE product_id = ? AND quantity >= ?
    ';

    foreach ($cart_details as $item) {
        $product_id = $item['product_id'];
        $amount = $item['amount']; // カート内の注文数量
        $order_price = $item['unit_price'];

        // Order_Itemテーブルに登録 
        $stmt_item = $pdo->prepare($sql_item);
        $stmt_item->execute([$order_id, $product_id, $amount, $order_price]); 

        // Productの在庫更新 
        $stmt_stock = $pdo->prepare($sql_stock_update);
        $stmt_stock->execute([$amount, $product_id, $amount]); 

        // 在庫更新の行数が0であれば、在庫不足としてロールバック 
        if ($stmt_stock->rowCount() === 0) {
            throw new Exception("商品ID: {$product_id} の在庫が不足しているか、既に売り切れました。");
        }
    }


    // 5. Cartデータを削除 (変更なし)
    
    $stmt_cart_id = $pdo->prepare('SELECT cart_id FROM cart WHERE user_id = ?');
    $stmt_cart_id->execute([$user_id]);
    $customer_cart = $stmt_cart_id->fetch(PDO::FETCH_ASSOC);

    if ($customer_cart) {
        $user_cart_id = $customer_cart['cart_id'];
        
        $sql_delete_item = 'DELETE FROM cart_detail WHERE cart_id = ?';
        $pdo->prepare($sql_delete_item)->execute([$user_cart_id]); 

        $sql_delete_cart = 'DELETE FROM cart WHERE cart_id = ?';
        $pdo->prepare($sql_delete_cart)->execute([$user_cart_id]); 
    }
    
    // 6. すべて成功した場合、コミット (変更なし)
    $pdo->commit();

    // 成功メッセージ 
    require 'menu.php'; // メニューは成功時に表示
    ?>
    <section class="section">
        <div class="container is-max-desktop has-text-centered">
            <div class="notification is-success p-6">
                <h2 class="title is-3">
                    <span class="icon-text">
                        <span class="icon has-text-warning">
                            <i class="fas fa-star"></i>
                        </span>
        
                        <span class="mx-3">
                            注文完了
                        </span>
    
                        <span class="icon has-text-warning">
                            <i class="fas fa-star"></i>
                        </span>
                    </span>
                </h2>
                <p class="subtitle is-5">ご注文ありがとうございます。以下の内容で注文が正常に完了しました。</p>
                <div class="box has-text-left mt-5">
                    <p class="mb-2"><strong>注文ID:</strong> <span class="has-text-weight-bold has-text-primary"><?= htmlspecialchars($order_id) ?></span></p>
                    <p class="mb-2"><strong>お支払い方法:</strong> <?= htmlspecialchars($payment_method_name) ?></p>
                    <p class="mb-2"><strong>合計金額:</strong> <span class="has-text-weight-bold has-text-danger">¥<?= number_format($total_price) ?></span></p>
                </div>
                <div class="mt-5">
                    <a href="top.php" class="button is-primary is-light">トップ画面に戻る</a>
                    <a href="history.php" class="button is-info is-light ml-3">注文履歴を確認する</a>
                </div>
            </div>
        </div>
    </section>
    <?php

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    require 'menu.php'; // メニューはエラー時にも表示
    ?>
    <section class="section">
        <div class="container is-max-desktop has-text-centered">
            <div class="notification is-danger p-6">
                <h2 class="title is-4">
                    <span class="icon-text">
                        <span class="icon has-text-danger">
                            <i class="fas fa-exclamation-circle"></i>
                        </span>
        
                        <span class="mx-3">
                            注文処理エラー
                        </span>
    
                        <span class="icon has-text-danger">
                            <i class="fas fa-exclamation-circle"></i>
                        </span>
                    </span>
                </h2>
                <p class="mb-3">注文処理中にデータベースエラーが発生しました。時間を置いて再度お試しください。</p>
                <div class="box has-text-left is-size-7 has-background-white-ter p-3">
                    <p class="has-text-danger">（詳細: <?= htmlspecialchars($e->getMessage()) ?>）</p>
                </div>
                <div class="mt-4">
                    <a href="cart-show.php" class="button is-danger is-light">カートに戻る</a>
                </div>
            </div>
        </div>
    </section>
    <?php
    
} catch (Exception $e) {
    // その他のビジネスロジックエラー（在庫不足、カートが空など）が発生した場合 
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    require 'menu.php'; // メニューはエラー時にも表示
    ?>
    <section class="section">
        <div class="container is-max-desktop has-text-centered">
            <div class="notification is-warning p-6">
                <h2 class="title is-4">
                    <span class="icon-text">
                        <span class="icon has-text-danger">
                            <i class="fas fa-exclamation-triangle"></i>
                        </span>
        
                        <span class="mx-3">
                            注文処理エラー
                        </span>
    
                        <span class="icon has-text-danger">
                            <i class="fas fa-exclamation-triangle"></i>
                        </span>
                    </span>
                </h2>
                <p class="mb-3">注文処理エラーが発生しました。</p>
                <p class="subtitle is-5 has-text-danger"><?= htmlspecialchars($e->getMessage()) ?></p>
                <div class="mt-4">
                    <a href="cart-show.php" class="button is-warning is-light">カートに戻る</a>
                </div>
            </div>
        </div>
    </section>
    <?php
}

require 'footer.php';
?>