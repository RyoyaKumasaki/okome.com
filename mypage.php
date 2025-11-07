<?php
session_start();
require 'db-connect.php';
require 'header.php';
require 'menu.php';
?>
<h1>ユーザー情報</h1>
<?php
$sql = $pdo->prepare("SELECT user_id, password, address FROM customer_user WHERE user_id = ?");
        $sql->execute([$_SESSION['customer']['user_id']]);
?>

<table border="0" cellpadding="8">
  <tr>
    <th>ユーザーID</th>
    <td><?= htmlspecialchars($user['login_name']) ?></td>
  </tr>
  <tr>
    <th>メールアドレス</th>
    <td><?= htmlspecialchars($user['mail']) ?></td>
  </tr>
  <tr>
    <th>住所</th>
    <td><?= htmlspecialchars($user['address']) ?></td>
  </tr>
</table>
<form action="user-change.php" method="get"> <!--パスワード入力画面のリンク-->
    <button type="submit">ユーザー情報を変更</button>
</form>

<a href="buy-history">購入履歴を見る</a>

<h2>レビュー投稿履歴</h2>
<?php
$sql = $pdo->prepare('SELECT * FROM review WHERE user_id = ?');
$sql->execute([$_SESSION['customer']['user_id']]);

foreach ($sql as $row) {
    echo '<p>';
    echo htmlspecialchars($row['comment']) . '<br>';
    echo '</p><hr>';
}
?>

<form action="logout-input.php" method="get">  
<button type="submit">ログアウト</button>
</form>
<?php require 'footer.php'; ?>