<?php
session_start();
require 'db-connect.php';
$page_title = 'アカウント管理';
require 'controllheader.php';
require 'admin-menu.php';
?>
<h2>レビュー管理画面</h2>

<?php
// 論理削除処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $stmt = $pdo->prepare("UPDATE review SET status = 1 WHERE review_id = ?");
    $stmt->execute([$_POST['delete_id']]);

    // POST後リロード防止
    header('Location: '.$_SERVER['PHP_SELF']);
    exit;
}

// レビュー一覧取得（status = 1 のみ＝未削除）
$stmt = $pdo->query("SELECT review_id, comment FROM review WHERE status = 1 ORDER BY review_id DESC");
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php if (!empty($reviews)): ?>
    <?php foreach ($reviews as $r): ?>
        <div style="border:1px solid #ccc; padding:10px; margin:10px 0;">
            <p><?= htmlspecialchars($r['comment'], ENT_QUOTES, 'UTF-8') ?></p>

            <form method="POST" style="display:inline;">
                <input type="hidden" name="delete_id" value="<?= $r['review_id'] ?>">
                <button type="submit" onclick="return confirm('本当に削除しますか？');">削除</button>
            </form>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>現在、削除対象のレビューはありません。</p>
<?php endif; ?>

<form action="controlltop.php" method="get">
    <button type="submit">トップへ戻る</button>
</form>

<?php require 'footer.php'; ?>
