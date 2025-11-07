<?php 
session_start();
require 'db-connect.php';

// エラーメッセージの初期化
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'] ?? '';

    // 1. メールアドレスのバリデーション
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "有効なメールアドレスを入力してください。";
    } else {
        // 2. ユーザーIDを取得
        $sql_user = $pdo->prepare('SELECT user_id FROM customer_user WHERE mail=?');
        $sql_user->execute([$email]);
        $user_row = $sql_user->fetch(PDO::FETCH_ASSOC);

        // ユーザーが存在する場合のみ処理を続行
        if ($user_row) {
            $user_id = $user_row['user_id'];
            
            // 3. トークンと有効期限を生成
            // セキュリティのため、より長いトークン(64文字)を推奨
            $token = bin2hex(random_bytes(32)); 
            $expires = date('Y-m-d H:i:s', time() + 3600); // 1時間後の有効期限
            
            // 4. トークンをDB（password_resetsテーブル）に保存
            // 既存のトークンがあれば削除し、新しいものを挿入するロジックを推奨（ここではシンプルに挿入）
            try {
                // 既存のトークンを削除 (複数リセット要求への対応)
                $pdo->prepare("DELETE FROM password_reset WHERE user_id=?")->execute([$user_id]);
                
                $sql_insert = $pdo->prepare("INSERT INTO password_reset (user_id, token, expires_at) VALUES (?, ?, ?)");
                $sql_insert->execute([$user_id, $token, $expires]);
            } catch (PDOException $e) {
                // DBエラーの処理
                $error = "リセット情報登録時にエラーが発生しました。";
            }

            // 5. リセットリンクを作成
            $reset_link = "https://aso2401383.peewee.jp/2025/php2/git_okome.com/pass-change.php?token=" . $token; //php2/git_okome.com/pass-change.php

            // 6. メール送信処理 (メッセージ内容は変更なし)
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
                // 成功時は送信完了ページへリダイレクト
                header("Location: mail-sent.php");
                exit;
            } else {
                $error = "メール送信に失敗しました。サーバー管理者にお問い合わせください。";
            }
        } else {
            // ユーザーが存在しない場合でも、セキュリティのためエラーメッセージは表示しない方が望ましい
            // 例: $error = "指定されたメールアドレスは見つかりませんでした。" 
            // 処理を続行し、ユーザーに混乱を与えないようにするのが一般的。ここではシンプルにリダイレクト。
             header("Location: mail-sent.php");
             exit;
        }
    }
}
?>

<?php 
$page_title = 'パスワード再設定';
require 'header.php';
require 'menu.php';
?>

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