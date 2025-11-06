<?php
session_start();
require 'db-connect.php';
require 'header.php';
require 'menu.php';

?>
<h1>ログイン</h1>
<form action="login-output.php" method="post">
  <p>ログイン名：<input type="text" name="login_name"></p>
  <p>パスワード：<input type="password" name="password"></p>
  <p><input type="submit" value="ログイン"></p>
</form>
<p>
<a href="forgot-password.php">パスワードを忘れた方はこちら</a><br>
<a href="customer-input.php">アカウント作成はこちら</a>
</p>
<?php require 'footer.php'; ?>