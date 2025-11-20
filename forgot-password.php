<?php 
session_start();
require 'db-connect.php';

// エラーメッセージの初期化
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'] ?? '';

    // ... (PHPロジックは省略)
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "有効なメールアドレスを入力してください。";
    } else {
        $sql_user = $pdo->prepare('SELECT user_id FROM customer_user WHERE mail=?');
        $sql_user->execute([$email]);
        $user_row = $sql_user->fetch(PDO::FETCH_ASSOC);

        if ($user_row) {
            $user_id = $user_row['user_id'];
            $token = bin2hex(random_bytes(32)); 
            $expires = date('Y-m-d H:i:s', time() + 3600);
            
            try {
                $pdo->prepare("DELETE FROM password_reset WHERE user_id=?")->execute([$user_id]);
                $sql_insert = $pdo->prepare("INSERT INTO password_reset (user_id, token, expires_at) VALUES (?, ?, ?)");
                $sql_insert->execute([$user_id, $token, $expires]);
            } catch (PDOException $e) {
                $error = "リセット情報登録時にエラーが発生しました。";
            }

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

<section class="section">
    <div class="container is-max-desktop">
        <div class="box has-text-centered">

            <h2 class="title is-4">パスワードをお忘れですか？</h2>
            <p class="mb-5">パスワードを再設定するためのメールを送信します。メールアドレスを入力して、「送信する」ボタンをクリックしてください。</p>

            <?php if (!empty($error)): ?>
                <div class="notification is-danger is-light">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="post" class="mb-4">
                <div class="field is-grouped is-grouped-centered">
                    <div class="control is-expanded">
                        <input class="input is-medium" type="email" name="email" placeholder="メールアドレスの入力" required>
                    </div>
                </div>
                
                <div class="field is-grouped is-grouped-centered">
                    <div class="control">
                        <button type="submit" class="button is-warning is-medium mt-2">送信する</button>
                    </div>
                </div>
            </form>

            <form action="login-input.php" method="get">
                <div class="field is-grouped is-grouped-centered">
                    <div class="control">
                        <button type="submit" class="button is-link is-light is-small">キャンセルしてログイン画面に戻る</button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</section>

<?php require 'footer.php'; ?>