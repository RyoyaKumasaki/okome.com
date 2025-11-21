<?php
session_start();
require 'db-connect.php';
$page_title = 'アカウント管理';
require 'controllheader.php';
require 'admin-menu.php';
?>

<?php
$user_name = $_POST['user_name'];

$sql = "SELECT * FROM customer_user
        WHERE login_name = :user_name"; //name, user_name, address, telephone_number, mail

$stmt = $pdo->prepare($sql);
$stmt->execute([':user_name' => $user_name]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    if ($_POST['action'] === 'delete') {
        $sql = "UPDATE customer_user SET status = 0 WHERE login_name = :id";
        $pdo->prepare($sql)->execute([':id' => $customer_id]);
        $message = "アカウントを削除しました。";

    } elseif ($_POST['action'] === 'restore') {
        $sql = "UPDATE customer_user SET status = 1 WHERE login_name = :id";
        $pdo->prepare($sql)->execute([':id' => $user_name]);
        $message = "アカウントを復元しました。";
    }
}
?>
<form method="post">
    <input type="hidden" name="customer_id" value="<?= htmlspecialchars($customer_id) ?>">

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