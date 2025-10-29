<?php session_start(); require 'header.php'; require 'menu.php'; ?>
<h2>ログイン</h2>
<form action="login-output.php" method="post">
<table>
<tr>
<td>ログインID：</td>
<td><input type="text" name="login" required></td>
</tr>
<tr>
<td>パスワード：</td>
<td><input type="password" name="password" required></td>
</tr>
</table>
<p>
<input type="submit" value="ログイン">
</p>
</form>
<p>
<a href="forgot-password.php">パスワードを忘れた方はこちら</a><br>
<a href="customer-input.php">アカウント作成はこちら</a>
</p>
<?php require 'footer.php'; ?>