<?php session_start(); ?>
<?php require 'db-connect.php'; ?>
<?php require 'header.php'; ?>
<?php require 'menu.php'; ?>
<?php
if (isset($_SESSION['customer'])) {
    $id = $_SESSION['customer']['id'];
    $sql = $pdo->prepare('SELECT * FROM customer WHERE id!=? AND login=?');
    $sql->execute([$id, $_POST['login']]);
} else {
    $sql = $pdo->prepare('SELECT * FROM customer WHERE login=?');
    $sql->execute([$_POST['login']]);
}
if (empty($sql->fetchAll())) {
    if (isset($_SESSION['customer'])) {
        $sql = $pdo->prepare('UPDATE customer SET name=?, address=?, 
                             login=?, password=? WHERE id=?');
        $sql->execute([
            $_POST['name'], $_POST['address'],
            $_POST['login'], password_hash($_POST['password'], PASSWORD_DEFAULT), $id]);
        $_SESSION['customer'] = [
            'id'=>$id, 'name'=>$_POST['name'],
            'address'=>$_POST['address'], 'login'=>$_POST['login'],
            'password'=>$_POST['password']];
        echo 'お客様情報を更新しました。';
    } else {
        $sql = $pdo->prepare('INSERT INTO customer VALUES(null,?,?,?,?)');
        $sql->execute([
            $_POST['name'], $_POST['address'],
            $_POST['login'], password_hash($_POST['password'], PASSWORD_DEFAULT)]);
        echo 'お客様情報を登録しました。';
    }
} else {
    echo 'ログイン名がすでに使用されていますので、変更してください。';
}
?>
<?php require 'footer.php'; ?>