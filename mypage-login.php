<?php
session_start();
require 'db-connect.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_password = $_POST['password'] ?? '';

    if (!isset($_SESSION['customer']['user_id'])) {
        header('Location: login-input.php');
        exit;
    }

    $user_id = $_SESSION['customer']['user_id'];

    $sql = $pdo->prepare('SELECT password FROM customer_user WHERE user_id = ?');
    $sql->execute([$user_id]);
    $user = $sql->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($input_password, $user['password'])) { 
        header('Location: customer-input.php');
        exit;
    } else {
        $error = 'パスワードが正しくありません。';
    }
}
$page_title = '情報変更認証';
require 'header.php';
require 'menu.php';
?>

<section class="section">
    <div class="container is-max-desktop">
        <div class="box has-text-centered">
            
            <h2 class="title is-4 has-text-centered">ユーザー情報を変更するにはパスワード入力が必要です。</h2>
            <p class="subtitle is-6 has-text-grey">セキュリティのため、現在のパスワードを再入力してください。</p>

            <?php if ($error): ?>
                <div class="notification is-danger is-light mt-4">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form action="" method="post" class="mt-5">
                
                <div class="field is-grouped is-grouped-centered">
                    <div class="control is-expanded">
                        <label class="label has-text-left" for="password">パスワード</label>
                        <input class="input is-medium" type="password" id="password" name="password" required>
                    </div>
                </div>
                
                <div class="field mt-5">
                    <div class="control">
                        <button type="submit" class="button is-primary is-medium is-fullwidth">アカウント情報更新</button>
                    </div>
                </div>
            </form>
            
        </div>
    </div>
</section>

<?php require 'footer.php'; ?>