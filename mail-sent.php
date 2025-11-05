<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>メール送信完了</title>
<style>
body {
    font-family: sans-serif;
    text-align: center;
    padding-top: 100px;
}
.message {
    border: 2px dashed red;
    display: inline-block;
    padding: 20px;
    background-color: #fff;
}
.red-text {
    color: red;
    font-weight: bold;
}
button {
    margin-top: 20px;
    padding: 6px 12px;
}
</style>
</head>
<body>
<div class="message">
    <p>パスワード再設定用のメールを送信しました。</p>
    <p class="red-text">パスワードの再設定はまだ完了していません。</p>
</div>

<form action="login-input.php" method="get">
    <button type="submit">ログイン画面に戻る</button>
</form>
</body>
</html>