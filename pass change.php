<?php
require 'db-connect.php';
require 'header.php';
require 'menu.php';

session_start(); // セッションを使う宣言
$user_id = $_SESSION['user_id']['id'];
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $password1 = $_POST["password"];
    $password2 = $_POST["passwordc"];

    if ($password1 !== $password2) {
        $error = "パスワードが一致しません！";
    } else {
        $sql = $pdo->prepare("UPDATE customer_user SET password=? WHERE user_id=?");
        $sql->execute([$password1,$user_id]); //$_SESSION['user_id']をつくれ！！

        header("Location: success.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>パスワード変更</title>
</head>
<body>
  <h2>パスワード変更</h2>

  <?php if ($error): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <form action="" method="post">
    <table>
      <tr>
        <td>新しいパスワード</td>
        <td><input type="text" name="password"></td>
      </tr>
      <tr>
        <td>新しいパスワードの確認</td>
        <td><input type="text" name="passwordc"></td>
      </tr>
    </table>

    <p>新しいパスワードの確認入力は、新しいパスワードの入力と一致しなければなりません。</p>

    <input type="submit" value="パスワードを変更する">
  </form>

  <form action="login-input.php" method="get">
    <button type="submit">キャンセル</button>
  </form>
</body>
</html>

<?php require 'footer.php'; ?>
