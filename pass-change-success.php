<?php
$page_title = 'パスワード変更完了';
require 'header.php';
require 'menu.php';
?>
    <p>パスワードの変更が完了しました。<br>
    下記からログイン画面に戻りログインしてください。</p>
    <form action="login-input.php">
        <button type="submit">ログイン画面に戻る</button>
    </form>
<?php require 'footer.php'; ?>