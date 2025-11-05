<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'] ?? '';

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        
        header("Location: mail-sent.php");
        exit;
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
<style>
body {
    font-family: sans-serif;
    text-align: center;
    margin-top: 100px;
}
.error {
    color: red;
}
button {
    margin-top: 10px;
    padding: 6px 12px;
}
</style>
</head>
<body>
<h2>パスワードをお忘れですか？</h2>
<p>パスワードを再設定するためのメールを送信します。メールアドレスを入力して、「送信する」ボタンをクリックしてください。</p>

<?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>

<form method="post">
    <input type="email" name="email" placeholder="メールアドレスの入力" required>
    <br>
    <button type="submit" style="background-color:orange">送信する</button>
</form>

<form action="login.php" method="get">
    <br><button type="submit" style="color:blue">キャンセル</button>
</form>
</body>
</html>
