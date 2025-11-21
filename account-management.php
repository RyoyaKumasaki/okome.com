<?php
session_start();
require 'db-connect.php';
$page_title = 'アカウント管理';
require 'controllheader.php';
require 'admin-menu.php';

// 初回アクセス（検索フォームから来た時）か？
// 2回目のPOST（削除/復元）か？を判定
$user_name = $_POST['user_name'] ?? ($_POST['login_name'] ?? null);

// user_name が無い場合はエラー回避
if (!$user_name) {
    echo "ユーザーが指定されていません。<br>";
    require 'footer.php';
    exit;
}

$sql = "SELECT * FROM customer_user WHERE login_name = :user_name";
$stmt = $pdo->prepare($sql);
$stmt->execute([':user_name' => $user_name]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// 1. 復元 / 削除ボタンが押された場合
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    if ($_POST['action'] === 'delete') {
        $sql = "UPDATE customer_user SET status = 0 WHERE login_name = :id";
        $pdo->prepare($sql)->execute([':id' => $user_name]);
        $message = "アカウントを削除しました。";

    } elseif ($_POST['action'] === 'restore') {
        $sql = "UPDATE customer_user SET status = 1 WHERE login_name = :id";
        $pdo->prepare($sql)->execute([':id' => $user_name]);
        $message = "アカウントを復元しました。";
    }

    // 更新したので再取得
    $stmt = $pdo->prepare($sql = "SELECT * FROM customer_user WHERE login_name = :user_name");
    $stmt->execute([':user_name' => $user_name]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<?php if (isset($message)) echo "<p>$message</p>"; ?>

<form method="post">
    <!-- 次のPOSTでも user_name を渡す -->
    <input type="hidden" name="login_name" value="<?= htmlspecialchars($user_name) ?>">

    <?php if ($user['status'] == 0): ?>
        <button type="submit" name="action" value="restore">復元</button>
    <?php else: ?>
        <button type="submit" name="action" value="delete">削除</button>
    <?php endif; ?>
</form>

<h2>氏名：<?= htmlspecialchars($user['name']); ?></h2>
<p>ユーザー名：<?= htmlspecialchars($user['login_name']); ?></p>
<p>メールアドレス：<?= htmlspecialchars($user['mail']); ?></p>
<p>住所：<?= htmlspecialchars($user['address']); ?></p>
<p>電話番号：<?= htmlspecialchars($user['telephone_number']); ?></p>

<form action="controlltop.php" method="get">  
<button type="submit">トップへ戻る</button>
</form>

<?php require 'footer.php'; ?>
