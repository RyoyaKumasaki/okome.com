<?php
session_start();
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send'])) {
    $email = trim($_POST['email']);

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $userExists = true; 

        if ($userExists) {
            $message = "パスワード再設定メールを送信しました。";
        } else {
            $message = "登録されていないメールアドレスです。";
        }
    } else {
        $message = "正しいメールアドレスを入力してください。";
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>パスワードを忘れた場合</title>
<style>
.container { width: 300px; margin: 50px auto; padding: 20px; border: 1px solid #ccc; background:#fef9e7;}
h2 { text-align: center; }
input[type="text"] { width: 100%; padding: 8px; margin: 5px 0; }
input[type="submit"], .cancel { width: 100%; padding: 8px; margin: 5px 0; cursor: pointer; }
input[type="submit"] { background: #f0c000; border: none; }
.cancel { background: #ccc; border: none; text-align: center; text-decoration: none; display: block; }
.message { color: red; text-align: center; }
</style>
</head>
<body>

<div class="container">
    <h2>パスワードをお忘れですか？</h2>
    <p>パスワードを再設定するためのメールを送信します。メールアドレスを入力して「送信する」ボタンをクリックしてください。</p>

    <?php if($message) echo '<p class="message">'.$message.'</p>'; ?>

    <form method="post">
        <label>メールアドレスの入力</label>
        <input type="text" name="email" required>

        <input type="submit" name="send" value="メールを送信する">
    </form>

    <a href="login.php" class="cancel">キャンセル</a>
</div>

</body>
</html>
