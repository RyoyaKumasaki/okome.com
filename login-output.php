<?php session_start(); ?>
<?php require 'db-connect.php'; ?>
<?php require 'header.php'; ?>
<?php require 'menu.php'; ?>
<?php
unset($_SESSION['customer']);
$sql=$pdo->prepare('SELECT * FROM customer_user WHERE user_name=?');
$sql->execute([$_POST['login']]);
foreach ($sql as $row) {
    if (password_verify($_POST['password'], $row['password'])) {
        $_SESSION['customer']=[
            'id'=>$row['id'],'name'=>$row['name'],
            'address'=>$row['address'],'login'=>$row['login'],
            'password'=>$_POST['password']];
    }
}
if (isset($_SESSION['customer'])) {
    echo 'いらっしゃいませ、',$_SESSION['customer']['name'],'さん。';
} else {
    echo 'ログイン名またはパスワードが違います。';
}
?>
<?php require 'footer.php'; ?>
