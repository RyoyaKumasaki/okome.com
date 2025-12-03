<?php
// ヘッダーやDB接続ファイルを読み込む
require 'controllheader.php';
require 'admin-menu.php';
require_once 'db-connect.php';
?>

<div class="container is-max-desktop p-5 mt-5">

<?php
// ----------------------------------------------------
// 1. データの取得と検証
// ----------------------------------------------------
$product_id = $_POST['product_id'] ?? null;
$message_type = 'danger'; // 成功時の通知タイプを初期設定

if (!$product_id) {
    echo '<div class="notification is-danger">';
    echo '<h2 class="title is-4">エラー</h2><p>削除対象の商品IDが指定されていません。</p>';
    echo '</div>';
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
        // 成功
        $pdo->commit();
        $message_type = 'success';
        $message_title = '論理削除完了';
        $message_body = '商品ID: ' . htmlspecialchars($product_id) . ' の商品情報を更新しました。<br>'
                      . 'この商品はサイト上から非表示になりましたが、注文履歴などのデータは保持されます。';
    } else {
        // レコードが見つからなかった場合
        $pdo->rollBack();
        $message_type = 'warning'; // 警告色で表示
        $message_title = '情報が見つかりません';
        $message_body = '商品ID: ' . htmlspecialchars($product_id) . ' は見つかりませんでした。削除処理は実行されませんでした。';
    }

} catch (PDOException $e) {
    // DBエラー
    $pdo->rollBack();
    $message_type = 'danger';
    $message_title = 'データベースエラー';
    $message_body = '削除処理中に予期せぬエラーが発生しました。時間を置いて再度お試しください。';
    // error_log($e->getMessage()); // 開発環境ではログに出力することを推奨
}

// ----------------------------------------------------
// 3. 結果の表示
// ----------------------------------------------------
?>

<div class="box">
    <div class="notification is-<?= $message_type ?>">
        <h2 class="title is-4"><?= $message_title ?></h2>
        <div class="content">
            <p><?= $message_body ?></p>
        </div>
    </div>
    
    <div class="has-text-centered mt-5">
        <a href="stock-show.php" class="button is-link is-medium is-rounded">
            <span class="icon"><i class="fas fa-list-ul"></i></span>
            <span>商品一覧に戻る</span>
        </a>
    </div>
</div>

</div>

<?php require 'footer.php'; ?>