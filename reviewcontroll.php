<?php
session_start();
require 'db-connect.php';

// 論理削除処理（POSTリクエストのとき）
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];
    
    // トランザクション処理を開始（より安全な更新のため）
    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare("UPDATE review SET status = 0 WHERE review_id = ?");
        $stmt->execute([$delete_id]);

        $pdo->commit();
        // リダイレクトで二重送信防止（出力前に実行）
        header('Location: '.$_SERVER['PHP_SELF'] . '?status=deleted');
        exit;
    } catch (PDOException $e) {
        $pdo->rollBack();
        // エラーハンドリング (今回はリダイレクトしないが、エラーログを推奨)
    }
}

// ページタイトルとヘッダーは出力前に読み込む
$page_title = 'レビュー管理';
require 'controllheader.php';
require 'admin-menu.php';
?>

<div class="container is-max-desktop p-5 mt-5">

    <h2 class="title is-3 has-text-centered mb-5">レビュー管理画面</h2>

    <?php 
    // 削除成功メッセージの表示（リダイレクト後にクエリパラメータで判定）
    if (isset($_GET['status']) && $_GET['status'] === 'deleted'): ?>
        <div class="notification is-success has-text-centered mb-4">
            レビューを論理削除しました。
        </div>
    <?php endif; ?>

    <?php
    // レビュー一覧取得（status = 1 のみ＝未削除）
    $stmt = $pdo->query("SELECT review_id, comment FROM review WHERE status = 1 ORDER BY review_id DESC");
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <?php if (!empty($reviews)): ?>
        <h3 class="subtitle is-5">アクティブなレビュー (<?= count($reviews) ?>件)</h3>
        
        <?php foreach ($reviews as $r): ?>
            <div class="box mb-3 p-4 is-flex is-justify-content-space-between is-align-items-center">
                
                <p class="content is-medium m-0 mr-5 has-text-grey-dark">
                    <?= htmlspecialchars($r['comment'], ENT_QUOTES, 'UTF-8') ?>
                    <span class="has-text-grey is-size-7 ml-3">(ID: <?= $r['review_id'] ?>)</span>
                </p>

                <form method="POST" class="is-flex-shrink-0">
                    <input type="hidden" name="delete_id" value="<?= $r['review_id'] ?>">
                    <button type="submit" 
                            class="button is-danger is-small is-outlined" 
                            onclick="return confirm('本当にこのレビューを削除（非表示）にしますか？');">
                        <span class="icon"><i class="fas fa-trash-alt"></i></span>
                        <span>削除</span>
                    </button>
                </form>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="notification is-info has-text-centered">
            <p>現在、削除対象のアクティブなレビューはありません。</p>
        </div>
    <?php endif; ?>

    <hr class="my-5">

    <div class="has-text-centered">
        <form action="controlltop.php" method="get" style="display:inline;">
            <button type="submit" class="button is-link is-medium is-rounded">
                <span class="icon"><i class="fas fa-arrow-left"></i></span>
                <span>トップへ戻る</span>
            </button>
        </form>
    </div>
</div>

<?php require 'footer.php'; ?>