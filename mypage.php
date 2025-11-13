<?php
session_start();
require 'db-connect.php';
?>
<?php
$name = $address = $login_name = $password = $mail = $telephone_number = '';
if (isset($_SESSION['customer'])) {
    $user_id = $_SESSION['customer']['user_id'];
    $mail = $_SESSION['customer']['mail'];
    $name = $_SESSION['customer']['name'];
    $address = $_SESSION['customer']['address'];
    $login_name = $_SESSION['customer']['login_name'];
} else {
  header("Location: login-input.php");
  exit;
}
?>
<?php
require 'header.php';
require 'menu.php';
?>
<div class="card">
  <header class="card-header">
    <h1 class="card-header-title has-text-left is-size-3">ユーザー情報</h1>
  </header>
  <div class="card-content">
    <div class="content">
      <table border="0" cellpadding="8">
        <tr>
          <th>ユーザーID：</th>
          <td><?= htmlspecialchars($login_name) ?></td>
        </tr>
        <tr>
          <th>メールアドレス：</th>
          <td><?= htmlspecialchars($mail) ?></td>
        </tr>
        <tr>
          <th>住所：</th>
          <td><?= htmlspecialchars($address) ?></td>
        </tr>
      </table>
      <div class="card-footer">
      <form action="mypage-login.php" method="get"> <!--パスワード入力画面のリンク-->
        <button type="submit" class="card-footer-item">ユーザー情報を変更</button>
      </form>
      <a href="history.php" class="card-footer-item">購入履歴を見る</a>
      </div>
    </div>
  </div>
</div>
<div class="card">
  <header class="card-header">
    <h2 class="has-text-left is-size-4">レビュー投稿履歴</h2>
  </header>
  <div class="card-content">
    <div class="content">
  <?php
    if(isset($_SESSION['customer'])) :
      $sql = $pdo->prepare('SELECT * FROM review WHERE user_id = ?');
      $sql->execute([$_SESSION['customer']['user_id']]);

      $reviews = $sql->fetchAll(PDO::FETCH_ASSOC); // ← 配列に変換！

      foreach ($reviews as $row) : ?>
        <?php if($row['comment'] == '') : ?>
          <p class="card-content">
            <?= htmlspecialchars($row['comment']) ?><br>
          </p><hr>
        <?php else : ?>
          <p class="card-content">投稿したレビューはありません</p>
        <?php endif;?>
      <?php endforeach; ?>
    <?php endif;?>
    </div>
  </div>
</div>


<form action="logout-input.php" method="get">  
<button type="submit">ログアウト</button>
</form>
<?php require 'footer.php'; ?>