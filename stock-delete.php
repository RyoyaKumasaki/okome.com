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
// 2. トランザクション処理
// ----------------------------------------------------
$pdo->beginTransaction();

try {
    // status カラムを 0 (削除済み) に更新する
    
    $sql = 'UPDATE product SET status = 0 WHERE product_id = ?'; 
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$product_id]);

    if ($stmt->rowCount() > 0) {
        $pdo->commit();
        echo '<h2>論理削除完了</h2><p>商品ID: ' . htmlspecialchars($product_id) . ' の商品情報を更新しました。</p>';
        echo '<p>この商品はサイト上から非表示になりましたが、注文履歴などのデータは保持されます。</p>'; // メッセージ変更
    } else {
        // レコードが見つからなかった場合
        $pdo->rollBack();
        echo '<h2>エラー</h2><p>商品ID: ' . htmlspecialchars($product_id) . ' は見つかりませんでした。</p>';
    }

} catch (PDOException $e) {
    $pdo->rollBack();
    // 予期せぬDBエラーの場合
    echo '<h2>データベースエラー</h2><p>削除処理中に予期せぬエラーが発生しました。</p>';
    // error_log($e->getMessage());
}

echo '<p><a href="stock-show.php">商品一覧に戻る</a></p>'; // 戻り先ファイル名は適切に修正してください
require 'footer.php';
?>