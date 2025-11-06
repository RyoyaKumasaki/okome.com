<?php 
session_start();
require 'db-connect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'] ?? '';
    $sql = $pdo->prepare('SELECT * FROM customer_user WHERE mail=?');
    $sql->execute([$email]);
    unset($_SESSION['user_id']);
    foreach($sql as $row){
        $_SESSION['user_id'] = [
        'id' => $row['user_id'], 'mail' => $email
        ];
    }

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $token = bin2hex(random_bytes(16));
        $reset_link = "https://aso2401383.peewee.jp/2025/php2/git_okome.com/pass-change.php?token=" . $token;


        $subject = "【お米.com】パスワード再設定のご案内";
        $message = <<<EOT
{$email} 様

パスワード再設定のご依頼を受け付けました。
下記のリンクをクリックして、パスワード再設定ページへお進みください。

{$reset_link}

※このリンクの有効期限は1時間です。
※このメールに心当たりがない場合は破棄してください。

---------------------------------
サイト名運営事務局
EOT;

       $headers = "From: okome.com\r\n";
       $headers .= "Return-Path: info@peewee.jp\r\n"; 
       $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        if (mail($email, $subject, $message, $headers)) {
            header("Location: mail-sent.php");
            exit;
        } else {
            $error = "メール送信に失敗しました。サーバー管理者にお問い合わせください。";
        }

    } else {
        $error = "有効なメールアドレスを入力してください。";
    }
}
?>

<?php 
require 'header.php';
require 'menu.php';
?>

<title>パスワード再設定</title>
<style>
body {
    font-family: sans-serif;
    text-align: center;
    margin-top: 0px;
}
.error {
    color: red;
}
button {
    margin-top: 10px;
    padding: 6px 12px;
}
</style>

<h2>パスワードをお忘れですか？</h2>
<p>パスワードを再設定するためのメールを送信します。メールアドレスを入力して、「送信する」ボタンをクリックしてください。</p>

<?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>

<form method="post">
    <input type="email" name="email" placeholder="メールアドレスの入力" required>
    <br>
    <button type="submit" style="background-color:orange">送信する</button>
</form>

<form action="login-input.php" method="get">
    <br><button type="submit" style="color:blue">キャンセル</button>
</form>

<?php require 'footer.php'; ?>