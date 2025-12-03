<?php
session_start();
require 'db-connect.php';
$page_title = 'パスワード変更';

$error = "";
$user_id = null;
// 1. URLからトークンを取得
$token = $_GET['token'] ?? '';

// トークンがない、または空の場合はエラー
if (empty($token)) {
    die("パスワード変更に必要な情報がありません。メールからアクセスしてください。");
}

// 2. トークンを検証し、user_idを取得
$sql_check = $pdo->prepare("SELECT user_id FROM password_reset WHERE token=? AND expires_at > NOW()");
$sql_check->execute([$token]);
$reset_row = $sql_check->fetch(PDO::FETCH_ASSOC);

if (!$reset_row) {
    die("無効なリンク、または有効期限切れです。再度パスワードリセット手続きを行ってください。");
}

$user_id = $reset_row['user_id']; // 認証されたユーザーID

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $password1 = $_POST["password"];
    $password2 = $_POST["passwordc"];

    if (empty($password1) || empty($password2)) {
        $error = "パスワードを入力してください。";
    } elseif ($password1 !== $password2) {
        $error = "パスワードが一致しません！";
    } else {
        // 3. 【重要】新しいパスワードをハッシュ化
        $hashed_password = password_hash($password1, PASSWORD_DEFAULT);
        
        // 4. パスワードを更新
        // customer_user テーブルを想定
        $sql_update = $pdo->prepare("UPDATE customer_user SET password=? WHERE user_id=?"); 
        $sql_update->execute([$hashed_password, $user_id]); 

        // 5. 【重要】使用済みのトークンをDBから削除（無効化）
        $sql_delete = $pdo->prepare("DELETE FROM password_reset WHERE token=?");
        $sql_delete->execute([$token]);

        // 成功ページへリダイレクト
        header("Location: pass-change-success.php");
        exit();
    }
}
?>
<?php
require 'header.php';
require 'menu.php';
// Bulmaのコンテナと余白
?>
<div class="container is-max-desktop p-5 mt-5 has-background-white">
    <h2 class="title is-3 has-text-centered mb-5">パスワード変更</h2>
    
    <?php if ($error): ?>
        <div class="notification is-danger">
            <p><?= htmlspecialchars($error) ?></p>
        </div>
    <?php endif; ?>

    <div class="box">
        <form action="pass-change.php?token=<?= htmlspecialchars($token) ?>" method="post">
            
            <div class="field">
                <label class="label">新しいパスワード</label>
                <div class="control">
                    <input class="input" type="password" name="password" required placeholder="8文字以上の英数字">
                </div>
            </div>
            
            <div class="field">
                <label class="label">新しいパスワードの確認</label>
                <div class="control">
                    <input class="input" type="password" name="passwordc" required>
                </div>
                <p class="help">確認入力は、新しいパスワードの入力と一致しなければなりません。</p>
            </div>
            
            <div class="field is-grouped is-justify-content-center mt-5">
                <div class="control">
                    <input type="submit" value="パスワードを変更する" class="button is-primary is-large is-fullwidth">
                </div>
            </div>
        </form>
    </div> <div class="has-text-centered mt-4">
        <form action="login-input.php" method="get" style="display:inline;">
            <button type="submit" class="button is-light">キャンセル</button>
        </form>
    </div>
</div>

<?php require 'footer.php'; ?>