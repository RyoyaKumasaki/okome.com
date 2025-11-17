<?php
// ヘッダーやDB接続ファイルを読み込む
require 'header.php';
require_once 'db-connect.php';

// ----------------------------------------------------
// 1. データの取得と検証
// ----------------------------------------------------
$product_id = $_POST['product_id'] ?? null;

if (!$product_id) {
    echo '<h2>エラー</h2><p>削除対象の商品IDが指定されていません。</p>';
    require 'footer.php';
    exit;
}

// ----------------------------------------------------
// 2. トランザクション処理（データの整合性維持）
// ----------------------------------------------------
$pdo->beginTransaction();

try {
    // 依存関係のあるテーブルの確認 (オプション)
    // 例えば、この商品を参照している cart_detail や order_detail のレコードがある場合、
    // 外部キー制約により削除が失敗します。
    // その場合、まず依存するレコードを削除/更新する必要がありますが、
    // ここでは外部キーに CASCADE が設定されていない前提で、商品削除のみ試みます。
    
    // 商品テーブルからレコードを削除
    $sql = 'DELETE FROM product WHERE product_id = ?';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$product_id]);

    if ($stmt->rowCount() > 0) {
        $pdo->commit();
        echo '<h2>削除完了</h2><p>商品ID: ' . htmlspecialchars($product_id) . ' の商品を削除しました。</p>';
    } else {
        // レコードが見つからなかった場合
        $pdo->rollBack();
        echo '<h2>エラー</h2><p>商品ID: ' . htmlspecialchars($product_id) . ' は見つかりませんでした。</p>';
    }

} catch (PDOException $e) {
    $pdo->rollBack();
    // 外部キー制約違反などで削除できなかった場合のメッセージ
    if ($e->getCode() == '23000') { 
        echo '<h2>削除エラー</h2><p>この商品は、注文履歴やカート情報に登録されているため、削除できません。</p>';
        echo '<p>先に依存するレコードを処理してください。</p>';
    } else {
        echo '<h2>データベースエラー</h2><p>削除処理中に予期せぬエラーが発生しました。</p>';
    }
    // error_log($e->getMessage());
}

echo '<p><a href="product-list.php">商品一覧に戻る</a></p>'; // 戻り先ファイル名は適切に修正してください
require 'footer.php';
?>