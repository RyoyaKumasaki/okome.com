<?php
session_start();
require 'db-connect.php';
$page_title = 'アカウント管理';
require 'controllheader.php';
require 'admin-menu.php';
?>
<h2>レビュー管理画面</h2>

<?php

// 論理削除
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $stmt = $pdo->prepare("UPDATE review SET status = 1 WHERE cart_detail_id = ?");
    $stmt->execute([$_POST['delete_id']]);
}

// レビュー一覧取得（status = 0 のみ）
$stmt = $pdo->query("SELECT review_id, comment FROM review WHERE status = 0 ORDER BY review_id DESC");
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<?php foreach ($reviews as $r): ?>
    <div style="border:1px solid #ccc; padding:10px; margin:10px 0;">
        <p><?= htmlspecialchars($r['comment'], ENT_QUOTES, 'UTF-8') ?></p>

        <form method="POST" style="display:inline;">
            <input type="hidden" name="delete_id" value="<?= $r['cart_detail_id'] ?>">
            <button type="submit">削除</button>
        </form>
    </div>
<?php endforeach; ?>

<form action="controlltop.php" method="get">
        <button type="submit">トップへ戻る</button>
</form>
<?php require 'footer.php'; ?>