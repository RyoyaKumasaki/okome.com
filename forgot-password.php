<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'] ?? '';

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
         $message = "リセット用のメールを送信しました。";
    } else {
        $error = "有効なメールアドレスを入力してください。";
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>パスワード再設定</title>
</head>
<body>
<div class="container">
    <h2>パスワードをお忘れですか？</h2>
    <p>パスワードを再設定するためのメールを送信します。メールアドレスを入力して「送信する」ボタンをクリックしてください。</p>

    <?php if (!empty($message)) echo "<p class='message'>$message</p>"; ?>
    <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>

    <form method="post">
        <input type="email" name="email" placeholder="メールアドレスの入力" required>
        <button type="submit" class="send-btn">メールを送信する</button>
    </form>
    <form action="login.php" method="get">
        <button type="submit" class="cancel-btn">キャンセル</button>
    </form>
</div>
</body>
</html>
