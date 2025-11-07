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

    if ($user && $input_password === $user['password']) { 
        header('Location: customer-input.php');
        exit;
    } else {
        $error = 'パスワードが正しくありません。';
    }
}
require 'header.php';
require 'menu.php';
?>

<h2>ユーザー情報を変更するにはパスワード入力が必要です。</h2>

<?php if ($error): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form action="" method="post">
    <p>パスワード</p>
    <input type="password" name="password" required>
    <br><br>
    <button type="submit">アカウント情報更新</button>
</form>

<?php require 'footer.php'; ?>
