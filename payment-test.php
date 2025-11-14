<?php
session_start();
// db-connect.php がPDOインスタンス ($pdo) を提供するものと仮定します
require 'db-connect.php'; 
require 'header.php';

// ----------------------------------------------------
// 0-1. 支払い方法の取得と検証
// ----------------------------------------------------

$payment_choice = $_POST['choice'] ?? null;

// if (!$payment_choice) {
//     echo "<h2>エラー</h2><p>支払い方法が選択されていません。</p>";
//     require 'footer.php';
//     exit;
// }

// 支払い方法コードを日本語名にマッピングする関数 (仮)
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


// 顧客IDと配送情報を取得 (実際のECサイトでは、これらの情報はセッションやフォームの hidden フィールドから取得します)
$user_id = $_SESSION['customer']['user_id'] ?? null;
// 配送情報は仮の値。実際はフォーム/セッションから取得した確定情報を使用。
$shipping_info = $_SESSION['shipping_info'] ?? '未設定の配送情報（要修正）'; 

if (!$user_id) {
    echo "<h2>エラー</h2><p>ユーザーが認証されていません。ログインし直してください。</p>";
    require 'footer.php';
    exit;
}

$order_id = null; // 最終的に確定した注文IDを保持

// try {
    // ----------------------------------------------------
    // 0-2. 登録する内容を取得（カート明細の取得）
    // ----------------------------------------------------
    $sql_cart = '
        SELECT 
            cd.product_id, 
            cd.amount AS quantity, 
            p.price AS unit_price,
            p.quantity
        FROM Cart_detail cd
        JOIN Product p ON cd.product_id = p.product_id
        JOIN cart c ON cd.cart_id = c.cart_id
        WHERE c.user_id = ?
    ';
    $stmt_cart = $pdo->prepare($sql_cart);
    $stmt_cart->execute([$user_id]);
    $cart_details = $stmt_cart->fetchAll(PDO::FETCH_ASSOC);

    if (empty($cart_details)) {
        throw new Exception("カートに商品が入っていません。");
    }

    // 合計金額を計算
    $total_price = 0;
    foreach ($cart_details as $item) {
        $total_price += $item['unit_price'] * $item['quantity'];
    }

    // ----------------------------------------------------
    // 1. トランザクション開始
    // ----------------------------------------------------
    $pdo->beginTransaction();

    // ----------------------------------------------------
    // 2. Orderテーブルに登録 (注文ヘッダーの作成)
    // ----------------------------------------------------
    $sql_order = '
        INSERT INTO [order] (user_id,price)
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
        $quantity = $item['quantity'];
        $order_price = $item['order_price'];

        // Order_Itemテーブルに登録
        $stmt_item = $pdo->prepare($sql_item);
        $stmt_item->execute([$order_id, $product_id, $quantity, $order_price]);

        // Productの在庫更新
        $stmt_stock = $pdo->prepare($sql_stock_update);
        $stmt_stock->execute([$quantity, $product_id, $quantity]);

        // 在庫更新の行数が0であれば、在庫不足としてロールバック
        if ($stmt_stock->rowCount() === 0) {
            // 在庫が足りなかったか、競合により在庫がなくなった
            throw new Exception("商品ID: {$product_id} の在庫が不足しているか、既に売り切れました。");
        }
    }

    // ----------------------------------------------------
    // 4. Paymentテーブルに登録 (未使用)
    // ----------------------------------------------------
    // $transaction_id = uniqid('txn_'); // 仮の取引ID (実際は決済システムからの返却値)
    
    // $sql_payment = '
    //     INSERT INTO Payment (order_id, payment_method, transaction_id, payment_date, amount)
    //     VALUES (?, ?, ?, NOW(), ?)
    // ';
    // $stmt_payment = $pdo->prepare($sql_payment);
    $stmt_payment->execute([$order_id, $payment_method_name, $transaction_id, $total_price]);


    // ----------------------------------------------------
    // 5. Cartデータを削除
    // ----------------------------------------------------
    // 該当顧客のCart_ItemとCartを削除
    
    // Cart_Itemテーブルを削除するためのCart_idを取得 (複数のCartが存在しないことを前提)
    $stmt_cart_id = $pdo->prepare('SELECT cart_id FROM Cart WHERE user_id = ?');
    $stmt_cart_id->execute([$user_id]);
    $customer_cart = $stmt_cart_id->fetch(PDO::FETCH_ASSOC);

    if ($customer_cart) {
        $customer_cart_id = $customer_cart['cart_id'];
        
        $sql_delete_item = 'DELETE FROM Cart_Item WHERE cart_id = ?';
        $pdo->prepare($sql_delete_item)->execute([$user_cart_id]);

        $sql_delete_cart = 'DELETE FROM Cart WHERE cart_id = ?';
        $pdo->prepare($sql_delete_cart)->execute([$user_cart_id]);
    }
    
    // ----------------------------------------------------
    // 6. すべて成功した場合、コミット
    // ----------------------------------------------------
    $pdo->commit();

    // 成功メッセージ
    echo "<h2>注文完了</h2>";
    echo "<p>ご注文ありがとうございます。注文ID: {$order_id} で注文が正常に完了しました。</p>";
    echo "<p>お支払い方法: {$payment_method_name}</p>";
    echo "<p>合計金額: ¥" . number_format($total_price) . "</p>";


// } catch (PDOException $e) {
//     // データベースエラーが発生した場合
//     if ($pdo->inTransaction()) {
//         $pdo->rollBack();
//     }
//     echo "<h2>エラー</h2><p>注文処理中にデータベースエラーが発生しました。時間を置いて再度お試しください。</p>";
//     // ログ記録を推奨: error_log("PDO Error: " . $e->getMessage());
    
// } catch (Exception $e) {
//     // その他のビジネスロジックエラー（在庫不足、カートが空など）が発生した場合
//     if ($pdo->inTransaction()) {
//         $pdo->rollBack();
//     }
//     echo "<h2>エラー</h2><p>注文処理エラーが発生しました: " . htmlspecialchars($e->getMessage()) . "</p>";
//     // ログ記録を推奨
// }

require 'footer.php';
?>